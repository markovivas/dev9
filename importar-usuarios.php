<?php
header('Content-Type: text/html; charset=UTF-8');
ini_set('default_charset', 'UTF-8');

require_once __DIR__ . '/wp-load.php';

ini_set('max_execution_time', '0');
ini_set('memory_limit', '1024M');
set_time_limit(0);

wp_suspend_cache_invalidation(true);
remove_all_actions('profile_update');
remove_all_actions('user_register');
add_filter('send_password_change_email', '__return_false');
add_filter('send_email_change_email', '__return_false');

function intranet_import_render_message($type, $title, $details = '') {
	echo '<div class="message ' . esc_attr($type) . '">';
	echo '<div><strong>' . esc_html($title) . '</strong>';

	if ('' !== $details) {
		echo '<br><small>' . wp_kses_post($details) . '</small>';
	}

	echo '</div>';
	echo '</div>';
}

function intranet_import_reference_date() {
	$timezone = wp_timezone();
	$date     = new DateTimeImmutable('first day of this month', $timezone);

	return $date->modify('-3 months');
}

function intranet_import_api_url(DateTimeImmutable $reference_date) {
	return add_query_arg(
		array(
			'ano' => $reference_date->format('Y'),
			'mes' => $reference_date->format('m'),
		),
		'https://trescoracoes-mg.portaltp.com.br/api/transparencia.asmx/json_servidores'
	);
}

function intranet_import_extract_records($payload) {
	if (is_array($payload)) {
		$is_list = array_keys($payload) === range(0, count($payload) - 1);

		if ($is_list) {
			return $payload;
		}

		foreach (array('d', 'data', 'result', 'results', 'servidores', 'items') as $key) {
			if (isset($payload[$key]) && is_array($payload[$key])) {
				return intranet_import_extract_records($payload[$key]);
			}
		}

		foreach ($payload as $value) {
			if (is_array($value)) {
				$candidate = intranet_import_extract_records($value);

				if (! empty($candidate)) {
					return $candidate;
				}
			}
		}
	}

	return array();
}

function intranet_import_normalize_string($value) {
	$value = is_scalar($value) ? (string) $value : '';
	$value = wp_strip_all_tags($value);

	return trim(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
}

function intranet_import_normalize_status($value) {
	$value = intranet_import_normalize_string($value);
	$value = remove_accents($value);

	return strtoupper($value);
}

function intranet_import_normalize_registration($value) {
	$value = preg_replace('/\D+/', '', (string) $value);
	$value = ltrim($value, '0');

	return '' === $value ? '' : $value;
}

function intranet_import_split_name($name) {
	$parts = preg_split('/\s+/', trim($name));
	$parts = array_values(array_filter((array) $parts));

	if (empty($parts)) {
		return array('', '');
	}

	$first_name = array_shift($parts);
	$last_name  = implode(' ', $parts);

	return array($first_name, $last_name);
}

function intranet_import_build_email($registration) {
	return sanitize_email($registration . '@trescoracoes.mg.gov.br');
}

function intranet_import_fetch_records(DateTimeImmutable $reference_date) {
	$url      = intranet_import_api_url($reference_date);
	$response = wp_remote_get(
		$url,
		array(
			'timeout' => 30,
			'headers' => array(
				'Accept' => 'application/json',
			),
		)
	);

	if (is_wp_error($response)) {
		return $response;
	}

	$status_code = (int) wp_remote_retrieve_response_code($response);

	if (200 !== $status_code) {
		return new WP_Error('api_http_error', 'Falha ao consultar a API. Codigo HTTP: ' . $status_code);
	}

	$body         = wp_remote_retrieve_body($response);
	$decoded_body = trim($body);

	if ('' === $decoded_body) {
		return new WP_Error('api_empty_body', 'A API retornou uma resposta vazia.');
	}

	if (0 === strpos($decoded_body, '<')) {
		$xml_payload = null;

		if (preg_match('/<string[^>]*>(.*)<\/string>/is', $decoded_body, $matches)) {
			$xml_payload = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
		}

		if (null !== $xml_payload && '' !== $xml_payload) {
			$decoded_body = $xml_payload;
		}
	}

	$decoded = json_decode($decoded_body, true);

	if (! is_array($decoded)) {
		return new WP_Error('api_invalid_json', 'A API retornou um JSON invalido ou inesperado.');
	}

	$records = intranet_import_extract_records($decoded);

	if (empty($records)) {
		return new WP_Error('api_empty_dataset', 'Nenhum registro de servidor foi encontrado na resposta da API.');
	}

	return $records;
}

function intranet_import_should_keep_active($record) {
	$status = intranet_import_normalize_status($record['situacao'] ?? '');

	return 'ATIVO' === $status || 'ATIVOS' === $status;
}

function intranet_import_sync_user($record, &$stats) {
	$name         = intranet_import_normalize_string($record['nome'] ?? '');
	$registration = intranet_import_normalize_registration($record['matricula'] ?? '');
	$cargo        = intranet_import_normalize_string($record['cargo'] ?? '');
	$secretaria   = intranet_import_normalize_string($record['secretaria'] ?? '');
	$profissao    = intranet_import_normalize_string($record['profissao'] ?? '');
	$local        = intranet_import_normalize_string($record['local'] ?? '');

	if ('' === $name || '' === $registration) {
		$stats['ignored']++;
		intranet_import_render_message('error', 'Registro ignorado', 'Nome ou matricula ausentes na API.');
		return;
	}

	list($first_name, $last_name) = intranet_import_split_name($name);

	$user_data = array(
		'user_login'    => $registration,
		'user_pass'     => $registration,
		'user_nicename' => sanitize_title($name . '-' . $registration),
		'user_email'    => intranet_import_build_email($registration),
		'display_name'  => $name,
		'first_name'    => $first_name,
		'last_name'     => $last_name,
		'role'          => 'subscriber',
	);

	$existing_user = get_user_by('login', $registration);

	if ($existing_user instanceof WP_User) {
		$user_data['ID'] = $existing_user->ID;
		$user_id         = wp_update_user($user_data);
		$action_label    = 'Atualizado';
		$stats['updated']++;
	} else {
		$user_id      = wp_insert_user($user_data);
		$action_label = 'Criado';
		$stats['created']++;
	}

	if (is_wp_error($user_id)) {
		$stats['errors']++;
		intranet_import_render_message('error', 'Erro ao sincronizar ' . $name, $user_id->get_error_message());
		return;
	}

	update_user_meta($user_id, 'job_title', $cargo);
	update_user_meta($user_id, 'department', $secretaria);
	update_user_meta($user_id, 'intranet_import_source', 'portal_tp_servidores');
	update_user_meta($user_id, 'intranet_import_status', 'active');
	update_user_meta($user_id, 'intranet_import_registration', $registration);
	update_user_meta($user_id, 'intranet_import_last_sync', current_time('mysql'));
	update_user_meta($user_id, 'intranet_import_profissao', $profissao);
	update_user_meta($user_id, 'intranet_import_local', $local);
	update_user_meta($user_id, 'intranet_import_secretaria', $secretaria);
	update_user_meta($user_id, 'intranet_import_cargo', $cargo);

	wp_set_password($registration, $user_id);

	$stats['processed']++;
	$stats['active_registrations'][$registration] = true;

	intranet_import_render_message(
		'success',
		$action_label . ': ' . $name,
		'Matricula: ' . esc_html($registration) . ' | Cargo: ' . esc_html($cargo ?: 'Nao informado') . ' | Secretaria: ' . esc_html($secretaria ?: 'Nao informada')
	);
}

function intranet_import_deactivate_missing_users(array $active_registrations, &$stats) {
	$users = get_users(
		array(
			'fields'     => array('ID', 'user_login', 'display_name'),
			'meta_key'   => 'intranet_import_source',
			'meta_value' => 'portal_tp_servidores',
			'number'     => -1,
		)
	);

	foreach ($users as $user) {
		if (isset($active_registrations[$user->user_login])) {
			continue;
		}

		update_user_meta($user->ID, 'intranet_import_status', 'inactive');
		update_user_meta($user->ID, 'intranet_import_last_sync', current_time('mysql'));
		wp_set_password(wp_generate_password(32, true, true), $user->ID);

		$stats['deactivated']++;
		intranet_import_render_message(
			'info',
			'Usuario desativado: ' . $user->display_name,
			'Matricula: ' . esc_html($user->user_login) . ' nao apareceu como ATIVO na consulta atual.'
		);
	}
}

$reference_date = intranet_import_reference_date();
$api_url        = intranet_import_api_url($reference_date);
$records        = intranet_import_fetch_records($reference_date);
$stats          = array(
	'processed'            => 0,
	'created'              => 0,
	'updated'              => 0,
	'ignored'              => 0,
	'errors'               => 0,
	'deactivated'          => 0,
	'active_registrations' => array(),
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Importacao de Usuarios</title>
	<style>
		* {
			box-sizing: border-box;
			margin: 0;
			padding: 0;
			font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
		}

		body {
			background: linear-gradient(135deg, #0f3b61 0%, #1f6b86 100%);
			min-height: 100vh;
			padding: 24px;
		}

		.container {
			width: min(1100px, 100%);
			margin: 0 auto;
			background: #fff;
			border-radius: 18px;
			box-shadow: 0 30px 80px rgba(0, 0, 0, 0.22);
			overflow: hidden;
		}

		.header {
			padding: 28px 32px;
			background: linear-gradient(135deg, #103b61 0%, #2f7f99 100%);
			color: #fff;
		}

		.header h1 {
			font-size: 28px;
			margin-bottom: 8px;
		}

		.header p {
			opacity: 0.92;
			line-height: 1.6;
		}

		.content {
			padding: 28px 32px;
			display: grid;
			gap: 14px;
		}

		.message {
			border-radius: 12px;
			padding: 16px 18px;
			border-left: 4px solid transparent;
			line-height: 1.6;
		}

		.success {
			background: #effcf5;
			border-left-color: #17a34a;
			color: #17603a;
		}

		.error {
			background: #fef2f2;
			border-left-color: #dc2626;
			color: #991b1b;
		}

		.info {
			background: #eff6ff;
			border-left-color: #2563eb;
			color: #1d4ed8;
		}

		.summary {
			margin: 8px 32px 32px;
			padding: 24px;
			border-radius: 16px;
			background: #f8fafc;
			border: 1px solid #dbe5ef;
			display: grid;
			grid-template-columns: repeat(5, minmax(0, 1fr));
			gap: 16px;
		}

		.summary-card {
			padding: 18px;
			border-radius: 14px;
			background: #fff;
			border: 1px solid #e2e8f0;
			text-align: center;
		}

		.summary-card strong {
			display: block;
			font-size: 28px;
			color: #103b61;
		}

		.summary-card span {
			display: block;
			margin-top: 8px;
			color: #64748b;
			font-size: 14px;
		}

		code {
			background: rgba(15, 23, 42, 0.08);
			padding: 2px 6px;
			border-radius: 6px;
		}

		@media (max-width: 900px) {
			.summary {
				grid-template-columns: 1fr 1fr;
			}
		}

		@media (max-width: 560px) {
			body {
				padding: 14px;
			}

			.header,
			.content {
				padding: 20px;
			}

			.summary {
				margin: 8px 20px 20px;
				padding: 20px;
				grid-template-columns: 1fr;
			}
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="header">
			<h1>Importacao de Usuarios via API</h1>
			<p>Fonte: <code><?php echo esc_html($api_url); ?></code></p>
			<p>Referencia automatica: <?php echo esc_html($reference_date->format('m/Y')); ?> (3 meses antes do mes atual)</p>
			<p>Regras: importa apenas servidores com situacao <strong>ATIVO</strong>, usa a matricula sem zeros iniciais como login e senha, atualiza <strong>Cargo</strong> em <code>job_title</code> e <strong>Secretaria</strong> em <code>department</code>.</p>
		</div>

		<div class="content">
			<?php
			if (is_wp_error($records)) {
				intranet_import_render_message('error', 'Falha ao consultar a API', $records->get_error_message());
			} else {
				intranet_import_render_message('info', 'Sincronizacao iniciada', 'Registros brutos recebidos: ' . count($records));

				$seen_registrations = array();

				foreach ($records as $record) {
					if (! is_array($record)) {
						$stats['ignored']++;
						continue;
					}

					$registration = intranet_import_normalize_registration($record['matricula'] ?? '');

					if ('' === $registration) {
						$stats['ignored']++;
						intranet_import_render_message('error', 'Registro ignorado', 'Matricula ausente ou invalida na API.');
						continue;
					}

					if (isset($seen_registrations[$registration])) {
						$stats['ignored']++;
						intranet_import_render_message('info', 'Registro duplicado ignorado', 'Matricula ' . esc_html($registration) . ' apareceu mais de uma vez na API.');
						continue;
					}

					$seen_registrations[$registration] = true;

					if (! intranet_import_should_keep_active($record)) {
						$stats['ignored']++;
						continue;
					}

					intranet_import_sync_user($record, $stats);
					flush();
					ob_flush();
				}

				intranet_import_deactivate_missing_users($stats['active_registrations'], $stats);
				intranet_import_render_message('info', 'Sincronizacao finalizada', 'Processamento concluido sem usar CSV.');
			}
			?>
		</div>

		<div class="summary">
			<div class="summary-card">
				<strong><?php echo esc_html((string) $stats['processed']); ?></strong>
				<span>Ativos sincronizados</span>
			</div>
			<div class="summary-card">
				<strong><?php echo esc_html((string) $stats['created']); ?></strong>
				<span>Usuarios criados</span>
			</div>
			<div class="summary-card">
				<strong><?php echo esc_html((string) $stats['updated']); ?></strong>
				<span>Usuarios atualizados</span>
			</div>
			<div class="summary-card">
				<strong><?php echo esc_html((string) $stats['deactivated']); ?></strong>
				<span>Usuarios desativados</span>
			</div>
			<div class="summary-card">
				<strong><?php echo esc_html((string) ($stats['ignored'] + $stats['errors'])); ?></strong>
				<span>Ignorados ou com erro</span>
			</div>
		</div>
	</div>
</body>
</html>

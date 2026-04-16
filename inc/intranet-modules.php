<?php
/**
 * Native intranet modules for the dashboard theme.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

function intranet_dashboard_base_build_taxonomy_labels($plural, $singular) {
	return array(
		'name'                       => $plural,
		'singular_name'              => $singular,
		'search_items'               => sprintf(__('Buscar %s', 'intranet-dashboard-base'), strtolower($plural)),
		'all_items'                  => sprintf(__('Todos os %s', 'intranet-dashboard-base'), strtolower($plural)),
		'parent_item'                => sprintf(__('Categoria pai de %s', 'intranet-dashboard-base'), strtolower($singular)),
		'parent_item_colon'          => sprintf(__('Categoria pai de %s:', 'intranet-dashboard-base'), strtolower($singular)),
		'edit_item'                  => sprintf(__('Editar %s', 'intranet-dashboard-base'), strtolower($singular)),
		'update_item'                => sprintf(__('Atualizar %s', 'intranet-dashboard-base'), strtolower($singular)),
		'add_new_item'               => sprintf(__('Adicionar %s', 'intranet-dashboard-base'), strtolower($singular)),
		'new_item_name'              => sprintf(__('Novo nome de %s', 'intranet-dashboard-base'), strtolower($singular)),
		'menu_name'                  => $plural,
		'not_found'                  => sprintf(__('Nenhum %s encontrado', 'intranet-dashboard-base'), strtolower($singular)),
		'back_to_items'              => sprintf(__('Voltar para %s', 'intranet-dashboard-base'), strtolower($plural)),
		'choose_from_most_used'      => sprintf(__('Escolher entre os %s mais usados', 'intranet-dashboard-base'), strtolower($plural)),
		'separate_items_with_commas' => sprintf(__('Separe %s com virgulas', 'intranet-dashboard-base'), strtolower($plural)),
		'add_or_remove_items'        => sprintf(__('Adicionar ou remover %s', 'intranet-dashboard-base'), strtolower($plural)),
	);
}

function intranet_dashboard_base_register_taxonomy($taxonomy, $object_type, $plural, $singular, $slug) {
	register_taxonomy(
		$taxonomy,
		$object_type,
		array(
			'labels'             => intranet_dashboard_base_build_taxonomy_labels($plural, $singular),
			'public'             => true,
			'publicly_queryable' => true,
			'hierarchical'       => true,
			'show_admin_column'  => true,
			'show_ui'            => true,
			'show_in_nav_menus'  => true,
			'show_tagcloud'      => false,
			'show_in_quick_edit' => true,
			'show_in_rest'       => true,
			'rewrite'            => array(
				'slug'         => $slug,
				'with_front'   => false,
				'hierarchical' => true,
			),
		)
	);
}

function intranet_dashboard_base_register_modules() {
	register_post_type(
		'comunicado',
		array(
			'labels' => array(
				'name'               => __('Comunicados', 'intranet-dashboard-base'),
				'singular_name'      => __('Comunicado', 'intranet-dashboard-base'),
				'add_new_item'       => __('Adicionar comunicado', 'intranet-dashboard-base'),
				'edit_item'          => __('Editar comunicado', 'intranet-dashboard-base'),
				'new_item'           => __('Novo comunicado', 'intranet-dashboard-base'),
				'view_item'          => __('Ver comunicado', 'intranet-dashboard-base'),
				'search_items'       => __('Buscar comunicados', 'intranet-dashboard-base'),
				'not_found'          => __('Nenhum comunicado encontrado', 'intranet-dashboard-base'),
				'not_found_in_trash' => __('Nenhum comunicado na lixeira', 'intranet-dashboard-base'),
				'menu_name'          => __('Comunicados', 'intranet-dashboard-base'),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-megaphone',
			'supports'     => array('title', 'editor', 'excerpt', 'thumbnail'),
			'has_archive'  => true,
			'rewrite'      => array('slug' => 'comunicados'),
		)
	);

	register_post_type(
		'evento',
		array(
			'labels' => array(
				'name'               => __('Eventos', 'intranet-dashboard-base'),
				'singular_name'      => __('Evento', 'intranet-dashboard-base'),
				'add_new_item'       => __('Adicionar evento', 'intranet-dashboard-base'),
				'edit_item'          => __('Editar evento', 'intranet-dashboard-base'),
				'new_item'           => __('Novo evento', 'intranet-dashboard-base'),
				'view_item'          => __('Ver evento', 'intranet-dashboard-base'),
				'search_items'       => __('Buscar eventos', 'intranet-dashboard-base'),
				'not_found'          => __('Nenhum evento encontrado', 'intranet-dashboard-base'),
				'not_found_in_trash' => __('Nenhum evento na lixeira', 'intranet-dashboard-base'),
				'menu_name'          => __('Eventos', 'intranet-dashboard-base'),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-calendar-alt',
			'supports'     => array('title', 'editor', 'excerpt'),
			'has_archive'  => true,
			'rewrite'      => array('slug' => 'eventos'),
		)
	);

	intranet_dashboard_base_register_taxonomy(
		'tipo_evento',
		'evento',
		__('Tipos de Evento', 'intranet-dashboard-base'),
		__('Tipo de Evento', 'intranet-dashboard-base'),
		'tipo-evento'
	);

	register_post_type(
		'link_util',
		array(
			'labels' => array(
				'name'               => __('Links Uteis', 'intranet-dashboard-base'),
				'singular_name'      => __('Link Util', 'intranet-dashboard-base'),
				'add_new_item'       => __('Adicionar link util', 'intranet-dashboard-base'),
				'edit_item'          => __('Editar link util', 'intranet-dashboard-base'),
				'new_item'           => __('Novo link util', 'intranet-dashboard-base'),
				'view_item'          => __('Ver link util', 'intranet-dashboard-base'),
				'search_items'       => __('Buscar links uteis', 'intranet-dashboard-base'),
				'not_found'          => __('Nenhum link util encontrado', 'intranet-dashboard-base'),
				'not_found_in_trash' => __('Nenhum link util na lixeira', 'intranet-dashboard-base'),
				'menu_name'          => __('Links Uteis', 'intranet-dashboard-base'),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-admin-links',
			'supports'            => array('title', 'editor', 'page-attributes'),
		)
	);

	register_post_type(
		'documento',
		array(
			'labels' => array(
				'name'               => __('Documentos', 'intranet-dashboard-base'),
				'singular_name'      => __('Documento', 'intranet-dashboard-base'),
				'add_new_item'       => __('Adicionar documento', 'intranet-dashboard-base'),
				'edit_item'          => __('Editar documento', 'intranet-dashboard-base'),
				'new_item'           => __('Novo documento', 'intranet-dashboard-base'),
				'view_item'          => __('Ver documento', 'intranet-dashboard-base'),
				'search_items'       => __('Buscar documentos', 'intranet-dashboard-base'),
				'not_found'          => __('Nenhum documento encontrado', 'intranet-dashboard-base'),
				'not_found_in_trash' => __('Nenhum documento na lixeira', 'intranet-dashboard-base'),
				'menu_name'          => __('Documentos', 'intranet-dashboard-base'),
			),
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-media-document',
			'supports'     => array('title', 'editor', 'excerpt', 'page-attributes'),
			'has_archive'  => true,
			'rewrite'      => array('slug' => 'documentos'),
		)
	);

	intranet_dashboard_base_register_taxonomy(
		'documento_categoria',
		'documento',
		__('Categorias de Documento', 'intranet-dashboard-base'),
		__('Categoria de Documento', 'intranet-dashboard-base'),
		'categoria-documento'
	);
}
add_action('init', 'intranet_dashboard_base_register_modules');

function intranet_dashboard_base_add_module_metaboxes() {
	add_meta_box(
		'evento_details',
		__('Detalhes do evento', 'intranet-dashboard-base'),
		'intranet_dashboard_base_render_event_metabox',
		'evento',
		'normal',
		'high'
	);

	add_meta_box(
		'link_util_details',
		__('Detalhes do link', 'intranet-dashboard-base'),
		'intranet_dashboard_base_render_link_metabox',
		'link_util',
		'normal',
		'high'
	);

	add_meta_box(
		'documento_details',
		__('Detalhes do documento', 'intranet-dashboard-base'),
		'intranet_dashboard_base_render_document_metabox',
		'documento',
		'normal',
		'high'
	);
}
add_action('add_meta_boxes', 'intranet_dashboard_base_add_module_metaboxes');

function intranet_dashboard_base_render_event_metabox($post) {
	wp_nonce_field('intranet_dashboard_base_save_event', 'intranet_dashboard_base_event_nonce');

	$start_date = get_post_meta($post->ID, '_event_start_date', true);
	$end_date   = get_post_meta($post->ID, '_event_end_date', true);
	$location   = get_post_meta($post->ID, '_event_location', true);
	?>
	<p>
		<label for="event_start_date"><strong><?php esc_html_e('Data e hora de inicio', 'intranet-dashboard-base'); ?></strong></label><br>
		<input id="event_start_date" name="event_start_date" type="datetime-local" value="<?php echo esc_attr($start_date); ?>" class="widefat">
	</p>
	<p>
		<label for="event_end_date"><strong><?php esc_html_e('Data e hora de termino', 'intranet-dashboard-base'); ?></strong></label><br>
		<input id="event_end_date" name="event_end_date" type="datetime-local" value="<?php echo esc_attr($end_date); ?>" class="widefat">
	</p>
	<p>
		<label for="event_location"><strong><?php esc_html_e('Local', 'intranet-dashboard-base'); ?></strong></label><br>
		<input id="event_location" name="event_location" type="text" value="<?php echo esc_attr($location); ?>" class="widefat" placeholder="<?php esc_attr_e('Ex.: Auditorio, Teams ou Sala 04', 'intranet-dashboard-base'); ?>">
	</p>
	<?php
}

function intranet_dashboard_base_render_link_metabox($post) {
	wp_nonce_field('intranet_dashboard_base_save_link', 'intranet_dashboard_base_link_nonce');

	$url         = get_post_meta($post->ID, '_useful_link_url', true);
	$description = get_post_meta($post->ID, '_useful_link_description', true);
	$is_featured = get_post_meta($post->ID, '_useful_link_featured', true);
	?>
	<p>
		<label for="useful_link_url"><strong><?php esc_html_e('URL', 'intranet-dashboard-base'); ?></strong></label><br>
		<input id="useful_link_url" name="useful_link_url" type="url" value="<?php echo esc_attr($url); ?>" class="widefat" placeholder="https://">
	</p>
	<p>
		<label for="useful_link_description"><strong><?php esc_html_e('Descricao curta', 'intranet-dashboard-base'); ?></strong></label><br>
		<textarea id="useful_link_description" name="useful_link_description" rows="3" class="widefat"><?php echo esc_textarea($description); ?></textarea>
	</p>
	<p>
		<label>
			<input type="checkbox" name="useful_link_featured" value="1" <?php checked($is_featured, '1'); ?>>
			<?php esc_html_e('Destacar no dashboard', 'intranet-dashboard-base'); ?>
		</label>
	</p>
	<?php
}

function intranet_dashboard_base_render_document_metabox($post) {
	wp_nonce_field('intranet_dashboard_base_save_document', 'intranet_dashboard_base_document_nonce');

	$file_url     = get_post_meta($post->ID, '_document_file_url', true);
	$external_url = get_post_meta($post->ID, '_document_external_url', true);
	$file_type    = get_post_meta($post->ID, '_document_file_type', true);
	$is_featured  = get_post_meta($post->ID, '_document_featured', true);
	?>
	<p>
		<label for="document_file_url"><strong><?php esc_html_e('Arquivo do documento', 'intranet-dashboard-base'); ?></strong></label><br>
		<input id="document_file_url" name="document_file_url" type="url" value="<?php echo esc_attr($file_url); ?>" class="widefat" placeholder="<?php esc_attr_e('Cole a URL de um arquivo enviado na biblioteca de midia', 'intranet-dashboard-base'); ?>">
	</p>
	<p>
		<label for="document_external_url"><strong><?php esc_html_e('Link alternativo', 'intranet-dashboard-base'); ?></strong></label><br>
		<input id="document_external_url" name="document_external_url" type="url" value="<?php echo esc_attr($external_url); ?>" class="widefat" placeholder="https://">
	</p>
	<p>
		<label for="document_file_type"><strong><?php esc_html_e('Tipo do arquivo', 'intranet-dashboard-base'); ?></strong></label><br>
		<input id="document_file_type" name="document_file_type" type="text" value="<?php echo esc_attr($file_type); ?>" class="widefat" placeholder="<?php esc_attr_e('Ex.: PDF, DOCX, XLSX', 'intranet-dashboard-base'); ?>">
	</p>
	<p>
		<label>
			<input type="checkbox" name="document_featured" value="1" <?php checked($is_featured, '1'); ?>>
			<?php esc_html_e('Destacar no dashboard', 'intranet-dashboard-base'); ?>
		</label>
	</p>
	<?php
}

function intranet_dashboard_base_save_module_meta($post_id) {
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	$post_type = get_post_type($post_id);

	if ('evento' === $post_type) {
		if (! isset($_POST['intranet_dashboard_base_event_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['intranet_dashboard_base_event_nonce'])), 'intranet_dashboard_base_save_event')) {
			return;
		}

		if (! current_user_can('edit_post', $post_id)) {
			return;
		}

		$start_date = isset($_POST['event_start_date']) ? sanitize_text_field(wp_unslash($_POST['event_start_date'])) : '';
		$end_date   = isset($_POST['event_end_date']) ? sanitize_text_field(wp_unslash($_POST['event_end_date'])) : '';
		$location   = isset($_POST['event_location']) ? sanitize_text_field(wp_unslash($_POST['event_location'])) : '';

		update_post_meta($post_id, '_event_start_date', $start_date);
		update_post_meta($post_id, '_event_end_date', $end_date);
		update_post_meta($post_id, '_event_location', $location);
	}

	if ('link_util' === $post_type) {
		if (! isset($_POST['intranet_dashboard_base_link_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['intranet_dashboard_base_link_nonce'])), 'intranet_dashboard_base_save_link')) {
			return;
		}

		if (! current_user_can('edit_post', $post_id)) {
			return;
		}

		$url         = isset($_POST['useful_link_url']) ? esc_url_raw(wp_unslash($_POST['useful_link_url'])) : '';
		$description = isset($_POST['useful_link_description']) ? sanitize_textarea_field(wp_unslash($_POST['useful_link_description'])) : '';
		$is_featured = isset($_POST['useful_link_featured']) ? '1' : '0';

		update_post_meta($post_id, '_useful_link_url', $url);
		update_post_meta($post_id, '_useful_link_description', $description);
		update_post_meta($post_id, '_useful_link_featured', $is_featured);
	}

	if ('documento' === $post_type) {
		if (! isset($_POST['intranet_dashboard_base_document_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['intranet_dashboard_base_document_nonce'])), 'intranet_dashboard_base_save_document')) {
			return;
		}

		if (! current_user_can('edit_post', $post_id)) {
			return;
		}

		$file_url     = isset($_POST['document_file_url']) ? esc_url_raw(wp_unslash($_POST['document_file_url'])) : '';
		$external_url = isset($_POST['document_external_url']) ? esc_url_raw(wp_unslash($_POST['document_external_url'])) : '';
		$file_type    = isset($_POST['document_file_type']) ? sanitize_text_field(wp_unslash($_POST['document_file_type'])) : '';
		$is_featured  = isset($_POST['document_featured']) ? '1' : '0';

		update_post_meta($post_id, '_document_file_url', $file_url);
		update_post_meta($post_id, '_document_external_url', $external_url);
		update_post_meta($post_id, '_document_file_type', $file_type);
		update_post_meta($post_id, '_document_featured', $is_featured);
	}
}
add_action('save_post', 'intranet_dashboard_base_save_module_meta');

function intranet_dashboard_base_register_user_fields($user) {
	$job_title  = get_user_meta($user->ID, 'job_title', true);
	$department = get_user_meta($user->ID, 'department', true);
	$birthday   = get_user_meta($user->ID, 'birthday', true);
	$extension  = get_user_meta($user->ID, 'extension_number', true);
	$photo_url  = function_exists('intranet_dashboard_base_get_profile_photo_url') ? intranet_dashboard_base_get_profile_photo_url($user->ID, 'thumbnail') : '';
	?>
	<h2><?php esc_html_e('Perfil da intranet', 'intranet-dashboard-base'); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th><?php esc_html_e('Foto do perfil', 'intranet-dashboard-base'); ?></th>
			<td>
				<?php if ($photo_url) : ?>
					<p><img src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($user->display_name); ?>" style="width:96px;height:96px;border-radius:18px;object-fit:cover;"></p>
				<?php else : ?>
					<p><?php esc_html_e('Nenhuma foto enviada.', 'intranet-dashboard-base'); ?></p>
				<?php endif; ?>
				<p class="description"><?php esc_html_e('A foto pode ser atualizada na pagina personalizada Meu Perfil da intranet.', 'intranet-dashboard-base'); ?></p>
			</td>
		</tr>
		<tr>
			<th><label for="job_title"><?php esc_html_e('Cargo', 'intranet-dashboard-base'); ?></label></th>
			<td><input type="text" name="job_title" id="job_title" value="<?php echo esc_attr($job_title); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="department"><?php esc_html_e('Departamento', 'intranet-dashboard-base'); ?></label></th>
			<td><input type="text" name="department" id="department" value="<?php echo esc_attr($department); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="birthday"><?php esc_html_e('Aniversario', 'intranet-dashboard-base'); ?></label></th>
			<td><input type="date" name="birthday" id="birthday" value="<?php echo esc_attr($birthday); ?>" class="regular-text"></td>
		</tr>
		<tr>
			<th><label for="extension_number"><?php esc_html_e('Ramal', 'intranet-dashboard-base'); ?></label></th>
			<td><input type="text" name="extension_number" id="extension_number" value="<?php echo esc_attr($extension); ?>" class="regular-text"></td>
		</tr>
	</table>
	<?php
}
add_action('show_user_profile', 'intranet_dashboard_base_register_user_fields');
add_action('edit_user_profile', 'intranet_dashboard_base_register_user_fields');

function intranet_dashboard_base_save_user_fields($user_id) {
	if (! current_user_can('edit_user', $user_id)) {
		return;
	}

	$fields = array(
		'job_title'        => 'sanitize_text_field',
		'department'       => 'sanitize_text_field',
		'birthday'         => 'sanitize_text_field',
		'extension_number' => 'sanitize_text_field',
	);

	foreach ($fields as $field => $callback) {
		if (isset($_POST[ $field ])) {
			update_user_meta($user_id, $field, call_user_func($callback, wp_unslash($_POST[ $field ])));
		}
	}
}
add_action('personal_options_update', 'intranet_dashboard_base_save_user_fields');
add_action('edit_user_profile_update', 'intranet_dashboard_base_save_user_fields');

function intranet_dashboard_base_get_birthdays_for_current_month($limit = 6) {
	$users      = get_users(array('fields' => array('ID', 'display_name')));
	$birthdays  = array();
	$month_now  = (int) wp_date('m');

	foreach ($users as $user) {
		$birthday = get_user_meta($user->ID, 'birthday', true);

		if (! $birthday) {
			continue;
		}

		$timestamp = strtotime($birthday);

		if (! $timestamp || (int) gmdate('n', $timestamp) !== $month_now) {
			continue;
		}

		$birthdays[] = array(
			'user_id'      => $user->ID,
			'display_name' => $user->display_name,
			'day'          => (int) gmdate('j', $timestamp),
			'department'   => get_user_meta($user->ID, 'department', true),
		);
	}

	usort(
		$birthdays,
		static function ($left, $right) {
			return $left['day'] <=> $right['day'];
		}
	);

	return array_slice($birthdays, 0, absint($limit));
}

function intranet_dashboard_base_get_upcoming_events($limit = 5) {
	$query = new WP_Query(
		array(
			'post_type'      => 'evento',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
		)
	);

	$events = array();

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_id    = get_the_ID();
			$timestamp  = intranet_dashboard_base_get_event_start_timestamp($post_id);

			if (! $timestamp || $timestamp < current_time('timestamp')) {
				continue;
			}

			$events[] = array(
				'post_id'    => $post_id,
				'timestamp'  => $timestamp,
				'title'      => get_the_title(),
				'permalink'  => get_permalink(),
				'location'   => intranet_dashboard_base_get_event_location($post_id),
				'type_name'  => intranet_dashboard_base_get_event_type_name($post_id),
			);
		}

		wp_reset_postdata();
	}

	usort(
		$events,
		static function ($left, $right) {
			return $left['timestamp'] <=> $right['timestamp'];
		}
	);

	return array_slice($events, 0, absint($limit));
}

function intranet_dashboard_base_get_event_start_timestamp($post_id) {
	$start_date = get_post_meta($post_id, '_event_start_date', true);

	return $start_date ? strtotime($start_date) : false;
}

function intranet_dashboard_base_get_event_location($post_id) {
	return (string) get_post_meta($post_id, '_event_location', true);
}

function intranet_dashboard_base_get_event_type_name($post_id) {
	$terms = get_the_terms($post_id, 'tipo_evento');

	if ($terms && ! is_wp_error($terms)) {
		return $terms[0]->name;
	}

	return '';
}

function intranet_dashboard_base_get_calendar_events($year, $month) {
	$year       = absint($year);
	$month      = max(1, min(12, absint($month)));
	$start_date = sprintf('%04d-%02d-01 00:00', $year, $month);
	$end_date   = wp_date('Y-m-t 23:59', strtotime($start_date));

	$query = new WP_Query(
		array(
			'post_type'      => 'evento',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'     => '_event_start_date',
					'value'   => array(str_replace(' ', 'T', $start_date), str_replace(' ', 'T', $end_date)),
					'compare' => 'BETWEEN',
					'type'    => 'CHAR',
				),
			),
		)
	);

	$events = array();

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$post_id   = get_the_ID();
			$timestamp = intranet_dashboard_base_get_event_start_timestamp($post_id);

			if (! $timestamp) {
				continue;
			}

			$events[] = array(
				'id'       => $post_id,
				'title'    => get_the_title(),
				'url'      => get_permalink(),
				'start'    => wp_date('Y-m-d\TH:i', $timestamp),
				'date'     => wp_date('Y-m-d', $timestamp),
				'time'     => wp_date('H:i', $timestamp),
				'type'     => intranet_dashboard_base_get_event_type_name($post_id),
				'location' => intranet_dashboard_base_get_event_location($post_id),
			);
		}

		wp_reset_postdata();
	}

	return $events;
}

function intranet_dashboard_base_get_eventos_ajax() {
	check_ajax_referer('intranet_dashboard_base_nonce', 'security');

	$year  = isset($_POST['year']) ? absint(wp_unslash($_POST['year'])) : (int) wp_date('Y');
	$month = isset($_POST['month']) ? absint(wp_unslash($_POST['month'])) : (int) wp_date('n');

	wp_send_json_success(intranet_dashboard_base_get_calendar_events($year, $month));
}
add_action('wp_ajax_intranet_dashboard_base_get_eventos', 'intranet_dashboard_base_get_eventos_ajax');
add_action('wp_ajax_nopriv_intranet_dashboard_base_get_eventos', 'intranet_dashboard_base_get_eventos_ajax');

function intranet_dashboard_base_get_featured_links($limit = 6) {
	return new WP_Query(
		array(
			'post_type'      => 'link_util',
			'posts_per_page' => absint($limit),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'meta_query'     => array(
				array(
					'key'     => '_useful_link_featured',
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);
}

function intranet_dashboard_base_get_latest_announcements($limit = 4) {
	return new WP_Query(
		array(
			'post_type'      => 'comunicado',
			'posts_per_page' => absint($limit),
			'post_status'    => 'publish',
		)
	);
}

function intranet_dashboard_base_get_featured_documents($limit = 5) {
	return new WP_Query(
		array(
			'post_type'      => 'documento',
			'posts_per_page' => absint($limit),
			'orderby'        => array(
				'menu_order' => 'ASC',
				'date'       => 'DESC',
			),
			'meta_query'     => array(
				array(
					'key'     => '_document_featured',
					'value'   => '1',
					'compare' => '=',
				),
			),
		)
	);
}

function intranet_dashboard_base_get_document_url($post_id) {
	$file_url     = get_post_meta($post_id, '_document_file_url', true);
	$external_url = get_post_meta($post_id, '_document_external_url', true);

	if ($file_url) {
		return $file_url;
	}

	if ($external_url) {
		return $external_url;
	}

	return get_permalink($post_id);
}

<?php
/**
 * Front-end profile edit page.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! is_user_logged_in()) {
	auth_redirect();
}

$current_user = wp_get_current_user();
$status       = isset($_GET['profile-updated']) ? sanitize_text_field(wp_unslash($_GET['profile-updated'])) : '';
$messages     = array(
	'1'                 => array('type' => 'success', 'text' => __('Perfil atualizado com sucesso.', 'intranet-dashboard-base')),
	'password-mismatch' => array('type' => 'error', 'text' => __('As senhas informadas nao coincidem.', 'intranet-dashboard-base')),
	'password-short'    => array('type' => 'error', 'text' => __('A nova senha precisa ter pelo menos 6 caracteres.', 'intranet-dashboard-base')),
	'photo-error'       => array('type' => 'error', 'text' => __('Nao foi possivel enviar a foto do perfil.', 'intranet-dashboard-base')),
	'nonce-error'       => array('type' => 'error', 'text' => __('Sua sessao expirou. Recarregue a pagina e tente novamente.', 'intranet-dashboard-base')),
	'error'             => array('type' => 'error', 'text' => __('Nao foi possivel salvar o perfil.', 'intranet-dashboard-base')),
);

get_header();
?>

<section class="content-shell profile-edit-shell">
	<article class="dashboard-card profile-edit-card">
		<div class="profile-edit-header">
			<div>
				<p class="card-kicker"><?php esc_html_e('Meu Perfil', 'intranet-dashboard-base'); ?></p>
				<h1 class="entry-title"><?php esc_html_e('Editar perfil', 'intranet-dashboard-base'); ?></h1>
				<p class="profile-edit-copy"><?php esc_html_e('Atualize sua foto, senha e os dados exibidos na intranet. As informacoes abaixo alimentam o perfil do WordPress e os cards da HOME.', 'intranet-dashboard-base'); ?></p>
			</div>
			<div class="profile-edit-avatar-panel">
				<?php echo wp_kses_post(intranet_dashboard_base_get_avatar_markup($current_user->ID, 'profile-edit-avatar', 'medium')); ?>
				<p><?php esc_html_e('Foto atual do perfil', 'intranet-dashboard-base'); ?></p>
			</div>
		</div>

		<?php if (isset($messages[ $status ])) : ?>
			<div class="profile-edit-notice is-<?php echo esc_attr($messages[ $status ]['type']); ?>">
				<?php echo esc_html($messages[ $status ]['text']); ?>
			</div>
		<?php endif; ?>

		<form class="profile-edit-form" method="post" enctype="multipart/form-data">
			<?php wp_nonce_field('intranet_profile_edit', 'intranet_profile_edit_nonce'); ?>

			<div class="profile-edit-account-summary">
				<div class="profile-edit-account-chip">
					<span><?php esc_html_e('Login', 'intranet-dashboard-base'); ?></span>
					<strong><?php echo esc_html($current_user->user_login); ?></strong>
				</div>
				<div class="profile-edit-account-chip">
					<span><?php esc_html_e('Email', 'intranet-dashboard-base'); ?></span>
					<strong><?php echo esc_html($current_user->user_email); ?></strong>
				</div>
			</div>

			<div class="profile-edit-grid">
				<div class="profile-edit-field">
					<label for="display_name"><?php esc_html_e('Nome de exibicao', 'intranet-dashboard-base'); ?></label>
					<input id="display_name" name="display_name" type="text" value="<?php echo esc_attr($current_user->display_name); ?>" required>
				</div>

				<div class="profile-edit-field">
					<label for="profile_photo"><?php esc_html_e('Editar foto do perfil', 'intranet-dashboard-base'); ?></label>
					<input id="profile_photo" name="profile_photo" type="file" accept="image/*">
					<p class="field-help"><?php esc_html_e('Escolha uma imagem nítida. O preview aparece abaixo antes de salvar.', 'intranet-dashboard-base'); ?></p>
					<div class="profile-photo-preview" data-empty-label="<?php esc_attr_e('Nenhuma nova foto selecionada.', 'intranet-dashboard-base'); ?>">
						<div class="profile-photo-preview-media">
							<?php echo wp_kses_post(intranet_dashboard_base_get_avatar_markup($current_user->ID, 'profile-photo-preview-avatar', 'medium')); ?>
						</div>
						<p class="profile-photo-preview-text"><?php esc_html_e('Foto atual em uso.', 'intranet-dashboard-base'); ?></p>
					</div>
				</div>

				<div class="profile-edit-field">
					<label for="first_name"><?php esc_html_e('Primeiro nome', 'intranet-dashboard-base'); ?></label>
					<input id="first_name" name="first_name" type="text" value="<?php echo esc_attr($current_user->first_name); ?>">
				</div>

				<div class="profile-edit-field">
					<label for="last_name"><?php esc_html_e('Sobrenome', 'intranet-dashboard-base'); ?></label>
					<input id="last_name" name="last_name" type="text" value="<?php echo esc_attr($current_user->last_name); ?>">
				</div>

				<div class="profile-edit-field">
					<label for="job_title"><?php esc_html_e('Cargo', 'intranet-dashboard-base'); ?></label>
					<input id="job_title" name="job_title" type="text" value="<?php echo esc_attr(get_user_meta($current_user->ID, 'job_title', true)); ?>">
				</div>

				<div class="profile-edit-field">
					<label for="department"><?php esc_html_e('Departamento', 'intranet-dashboard-base'); ?></label>
					<input id="department" name="department" type="text" value="<?php echo esc_attr(get_user_meta($current_user->ID, 'department', true)); ?>">
				</div>

				<div class="profile-edit-field">
					<label for="birthday"><?php esc_html_e('Aniversario', 'intranet-dashboard-base'); ?></label>
					<input id="birthday" name="birthday" type="date" value="<?php echo esc_attr(get_user_meta($current_user->ID, 'birthday', true)); ?>">
				</div>

				<div class="profile-edit-field">
					<label for="extension_number"><?php esc_html_e('Ramal', 'intranet-dashboard-base'); ?></label>
					<input id="extension_number" name="extension_number" type="text" value="<?php echo esc_attr(get_user_meta($current_user->ID, 'extension_number', true)); ?>">
				</div>
			</div>

			<div class="profile-edit-passwords">
				<h2><?php esc_html_e('Alterar senha', 'intranet-dashboard-base'); ?></h2>
				<p class="field-help"><?php esc_html_e('Preencha apenas se quiser trocar a senha atual. Use pelo menos 6 caracteres.', 'intranet-dashboard-base'); ?></p>
				<div class="profile-edit-grid">
					<div class="profile-edit-field">
						<label for="new_password"><?php esc_html_e('Nova senha', 'intranet-dashboard-base'); ?></label>
						<input id="new_password" name="new_password" type="password" autocomplete="new-password">
					</div>

					<div class="profile-edit-field">
						<label for="confirm_password"><?php esc_html_e('Confirmar nova senha', 'intranet-dashboard-base'); ?></label>
						<input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password">
					</div>
				</div>
				<p class="password-match-indicator" aria-live="polite"></p>
			</div>

			<div class="profile-edit-actions">
				<button type="submit" class="profile-edit-submit"><?php esc_html_e('Salvar alteracoes', 'intranet-dashboard-base'); ?></button>
				<a href="<?php echo esc_url(home_url('/')); ?>" class="profile-edit-cancel"><?php esc_html_e('Voltar para a HOME', 'intranet-dashboard-base'); ?></a>
			</div>
		</form>
	</article>
</section>

<?php
get_footer();

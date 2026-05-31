<?php

if (! defined('ABSPATH')) {
	exit;
}

function intranet_dashboard_base_get_profile_photo_id($user_id) {
	return (int) get_user_meta($user_id, 'intranet_profile_photo_id', true);
}

function intranet_dashboard_base_get_profile_photo_url($user_id, $size = 'medium') {
	$photo_id = intranet_dashboard_base_get_profile_photo_id($user_id);

	if (! $photo_id) {
		return '';
	}

	$image = wp_get_attachment_image_url($photo_id, $size);

	return $image ? $image : '';
}

function intranet_dashboard_base_get_avatar_markup($user_id, $class = '', $size = 'thumbnail') {
	$user = get_userdata($user_id);

	if (! $user) {
		return '';
	}

	$photo_url = intranet_dashboard_base_get_profile_photo_url($user_id, $size);
	$classes   = trim('profile-avatar-shell ' . $class);

	if ($photo_url) {
		return sprintf(
			'<span class="%1$s"><img src="%2$s" alt="%3$s"></span>',
			esc_attr($classes),
			esc_url($photo_url),
			esc_attr($user->display_name)
		);
	}

	$initial = strtoupper(substr($user->display_name ?: $user->user_login, 0, 1));

	return sprintf(
		'<span class="%1$s profile-avatar-fallback">%2$s</span>',
		esc_attr($classes),
		esc_html($initial)
	);
}

function intranet_dashboard_base_handle_profile_update() {
	if (! get_query_var('intranet_profile_edit') || 'POST' !== $_SERVER['REQUEST_METHOD']) {
		return;
	}

	if (! is_user_logged_in()) {
		auth_redirect();
	}

	$user_id = get_current_user_id();

	if (! wp_verify_nonce(isset($_POST['intranet_profile_edit_nonce']) ? wp_unslash($_POST['intranet_profile_edit_nonce']) : '', 'intranet_profile_edit')) {
		wp_safe_redirect(add_query_arg('profile-updated', 'nonce-error', home_url('/meu-perfil/')));
		exit;
	}

	$user_data = array(
		'ID'           => $user_id,
		'display_name' => sanitize_text_field(wp_unslash($_POST['display_name'] ?? '')),
		'first_name'   => sanitize_text_field(wp_unslash($_POST['first_name'] ?? '')),
		'last_name'    => sanitize_text_field(wp_unslash($_POST['last_name'] ?? '')),
	);

	if ('' === $user_data['display_name']) {
		$user_data['display_name'] = wp_get_current_user()->display_name;
	}

	$password = (string) wp_unslash($_POST['new_password'] ?? '');
	$confirm  = (string) wp_unslash($_POST['confirm_password'] ?? '');

	if ('' !== $password || '' !== $confirm) {
		if ($password !== $confirm) {
			wp_safe_redirect(add_query_arg('profile-updated', 'password-mismatch', home_url('/meu-perfil/')));
			exit;
		}

		if (strlen($password) < 6) {
			wp_safe_redirect(add_query_arg('profile-updated', 'password-short', home_url('/meu-perfil/')));
			exit;
		}

		$user_data['user_pass'] = $password;
	}

	$result = wp_update_user($user_data);

	if (is_wp_error($result)) {
		wp_safe_redirect(add_query_arg('profile-updated', 'error', home_url('/meu-perfil/')));
		exit;
	}

	$meta_fields = array(
		'job_title'        => 'sanitize_text_field',
		'department'       => 'sanitize_text_field',
		'birthday'         => 'sanitize_text_field',
		'extension_number' => 'sanitize_text_field',
	);

	foreach ($meta_fields as $field => $callback) {
		if (isset($_POST[$field])) {
			update_user_meta($user_id, $field, call_user_func($callback, wp_unslash($_POST[$field])));
		}
	}

	if (! empty($_FILES['profile_photo']['name'])) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = media_handle_upload('profile_photo', 0);

		if (! is_wp_error($attachment_id)) {
			update_user_meta($user_id, 'intranet_profile_photo_id', (int) $attachment_id);
		} else {
			wp_safe_redirect(add_query_arg('profile-updated', 'photo-error', home_url('/meu-perfil/')));
			exit;
		}
	}

	if (! empty($user_data['user_pass'])) {
		wp_set_current_user($user_id);
		wp_set_auth_cookie($user_id, true);
	}

	wp_safe_redirect(add_query_arg('profile-updated', '1', home_url('/meu-perfil/')));
	exit;
}
add_action('template_redirect', 'intranet_dashboard_base_handle_profile_update', 20);

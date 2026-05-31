<?php

if (! defined('ABSPATH')) {
	exit;
}

function intranet_dashboard_base_user_can_access_admin($user = null) {
	if ($user instanceof WP_User) {
		return user_can($user, 'manage_options');
	}

	return current_user_can('manage_options');
}

function intranet_dashboard_base_restrict_admin_panel() {
	if (! is_user_logged_in() || wp_doing_ajax()) {
		return;
	}

	if (in_array($GLOBALS['pagenow'] ?? '', array('admin-post.php', 'async-upload.php'), true)) {
		return;
	}

	if (intranet_dashboard_base_user_can_access_admin()) {
		return;
	}

	wp_safe_redirect(home_url('/'));
	exit;
}
add_action('admin_init', 'intranet_dashboard_base_restrict_admin_panel');

function intranet_dashboard_base_login_redirect($redirect_to, $request, $user) {
	if (! ($user instanceof WP_User)) {
		return $redirect_to;
	}

	if (intranet_dashboard_base_user_can_access_admin($user)) {
		return $redirect_to;
	}

	return home_url('/');
}
add_filter('login_redirect', 'intranet_dashboard_base_login_redirect', 10, 3);

function intranet_dashboard_base_logout_redirect($redirect_to, $requested_redirect_to, $user) {
	return home_url('/');
}
add_filter('logout_redirect', 'intranet_dashboard_base_logout_redirect', 10, 3);

function intranet_dashboard_base_remove_wp_logo_from_admin_bar($wp_admin_bar) {
	if (! is_object($wp_admin_bar)) {
		return;
	}

	$wp_admin_bar->remove_node('wp-logo');
}
add_action('admin_bar_menu', 'intranet_dashboard_base_remove_wp_logo_from_admin_bar', 999);

function intranet_dashboard_base_customize_register($wp_customize) {
	$wp_customize->add_section(
		'intranet_dashboard_base_login_section',
		array(
			'title'       => __('Tela de Login', 'intranet-dashboard-base'),
			'description' => __('Personalize a aparencia da tela de login do WordPress.', 'intranet-dashboard-base'),
			'priority'    => 160,
		)
	);

	$wp_customize->add_setting(
		'intranet_dashboard_base_login_disable_scroll',
		array(
			'default'           => false,
			'sanitize_callback' => 'intranet_dashboard_base_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'intranet_dashboard_base_login_disable_scroll',
		array(
			'type'        => 'checkbox',
			'section'     => 'intranet_dashboard_base_login_section',
			'label'       => __('Desativar rolagem da tela de login', 'intranet-dashboard-base'),
			'description' => __('Mantem a tela fixa, sem barra de rolagem, quando houver espaco suficiente.', 'intranet-dashboard-base'),
		)
	);

	$wp_customize->add_setting(
		'intranet_dashboard_base_login_logo',
		array(
			'default'           => '',
			'sanitize_callback' => 'absint',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'intranet_dashboard_base_login_logo',
			array(
				'section'     => 'intranet_dashboard_base_login_section',
				'label'       => __('Icone/logo do login', 'intranet-dashboard-base'),
				'description' => __('Selecione uma imagem personalizada para substituir a logo padrao da tela de login.', 'intranet-dashboard-base'),
				'mime_type'   => 'image',
			)
		)
	);

	$wp_customize->add_setting(
		'intranet_dashboard_base_login_show_language_switcher',
		array(
			'default'           => true,
			'sanitize_callback' => 'intranet_dashboard_base_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'intranet_dashboard_base_login_show_language_switcher',
		array(
			'type'        => 'checkbox',
			'section'     => 'intranet_dashboard_base_login_section',
			'label'       => __('Mostrar seletor de idioma', 'intranet-dashboard-base'),
			'description' => __('Exibe ou oculta o bloco "language-switcher" na tela de login.', 'intranet-dashboard-base'),
		)
	);

}
add_action('customize_register', 'intranet_dashboard_base_customize_register');

function intranet_dashboard_base_get_login_logo_url() {
	$custom_logo = get_theme_mod('intranet_dashboard_base_login_logo');

	if ($custom_logo) {
		$image = wp_get_attachment_image_url((int) $custom_logo, 'full');

		if ($image) {
			return $image;
		}
	}

	return get_template_directory_uri() . '/logo/logo_login.png';
}

function intranet_dashboard_base_login_logo_url() {
	return home_url('/');
}
add_filter('login_headerurl', 'intranet_dashboard_base_login_logo_url');

function intranet_dashboard_base_login_logo_title() {
	return get_bloginfo('name');
}
add_filter('login_headertext', 'intranet_dashboard_base_login_logo_title');

function intranet_dashboard_base_login_show_language_switcher($display) {
	return (bool) get_theme_mod('intranet_dashboard_base_login_show_language_switcher', true);
}
add_filter('login_display_language_dropdown', 'intranet_dashboard_base_login_show_language_switcher');

function intranet_dashboard_base_login_enqueue_assets() {
	$version = wp_get_theme()->get('Version');

	wp_enqueue_style(
		'intranet-dashboard-base-login',
		get_template_directory_uri() . '/assets/css/login.css',
		array('login'),
		$version
	);

	$logo_url       = intranet_dashboard_base_get_login_logo_url();
	$disable_scroll = (bool) get_theme_mod('intranet_dashboard_base_login_disable_scroll', false);

	$custom_css = '';

	if ($logo_url) {
		$custom_css .= sprintf(
			'.login h1 a { background-image: url(%s); }',
			esc_url($logo_url)
		);
	}

	if ($custom_css) {
		wp_add_inline_style('intranet-dashboard-base-login', $custom_css);
	}
}
add_action('login_enqueue_scripts', 'intranet_dashboard_base_login_enqueue_assets');

function intranet_dashboard_base_login_body_classes($classes) {
	$disable_scroll = (bool) get_theme_mod('intranet_dashboard_base_login_disable_scroll', false);

	if ($disable_scroll) {
		$classes[] = 'intranet-login-no-scroll';
	}

	return $classes;
}
add_filter('login_body_class', 'intranet_dashboard_base_login_body_classes');

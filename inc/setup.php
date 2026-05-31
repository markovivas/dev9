<?php

if (! defined('ABSPATH')) {
	exit;
}

function intranet_dashboard_base_setup() {
	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
	add_theme_support('custom-logo', array(
		'height'      => 80,
		'width'       => 240,
		'flex-height' => true,
		'flex-width'  => true,
	));
	add_theme_support('custom-background', array(
		'default-color' => 'f3f6f8',
	));
	add_theme_support('align-wide');
	add_theme_support('editor-styles');

	register_nav_menus(
		array(
			'primary' => __('Menu Principal', 'intranet-dashboard-base'),
			'utility' => __('Menu Utilitario', 'intranet-dashboard-base'),
		)
	);
}
add_action('after_setup_theme', 'intranet_dashboard_base_setup');

function intranet_dashboard_base_assets() {
	$version = wp_get_theme()->get('Version');

	wp_enqueue_style(
		'intranet-dashboard-base-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array(),
		$version
	);

	if (is_front_page()) {
		wp_enqueue_style(
			'intranet-dashboard-base-dashboard',
			get_template_directory_uri() . '/assets/css/dashboard.css',
			array('intranet-dashboard-base-main'),
			$version
		);
	}

	if (is_singular('post') || is_post_type_archive('post')) {
		wp_enqueue_style(
			'intranet-dashboard-base-single',
			get_template_directory_uri() . '/assets/css/single.css',
			array('intranet-dashboard-base-main'),
			$version
		);
	}

	if (is_singular('evento') || is_post_type_archive('evento')) {
		wp_enqueue_style(
			'intranet-dashboard-base-calendar',
			get_template_directory_uri() . '/assets/css/calendar.css',
			array('intranet-dashboard-base-main'),
			$version
		);
	}

	if (get_query_var('intranet_profile_edit')) {
		wp_enqueue_style(
			'intranet-dashboard-base-profile',
			get_template_directory_uri() . '/assets/css/profile.css',
			array('intranet-dashboard-base-main'),
			$version
		);
	}

	wp_enqueue_script(
		'intranet-dashboard-base-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		$version,
		true
	);

	wp_localize_script(
		'intranet-dashboard-base-main',
		'intranetDashboardBase',
		array(
			'ajaxurl'      => admin_url('admin-ajax.php'),
			'nonce'        => wp_create_nonce('intranet_dashboard_base_nonce'),
			'monthNames'   => array('Janeiro', 'Fevereiro', 'Marco', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'),
			'weekDayNames' => array('Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'),
		)
	);
}
add_action('wp_enqueue_scripts', 'intranet_dashboard_base_assets');

function intranet_dashboard_base_widgets_init() {
	$sidebars = array(
		'home-profile'   => __('Home - Perfil', 'intranet-dashboard-base'),
		'home-highlight' => __('Home - Destaque Central', 'intranet-dashboard-base'),
		'home-actions'   => __('Home - Acoes Rapidas', 'intranet-dashboard-base'),
		'home-left'      => __('Home - Coluna Esquerda', 'intranet-dashboard-base'),
		'home-middle'    => __('Home - Coluna Central', 'intranet-dashboard-base'),
		'home-right'     => __('Home - Coluna Direita', 'intranet-dashboard-base'),
		'footer-1'       => __('Rodape - Coluna 1', 'intranet-dashboard-base'),
		'footer-2'       => __('Rodape - Coluna 2', 'intranet-dashboard-base'),
		'footer-3'       => __('Rodape - Coluna 3', 'intranet-dashboard-base'),
	);

	foreach ($sidebars as $id => $name) {
		register_sidebar(
			array(
				'name'          => $name,
				'id'            => $id,
				'before_widget' => '<section class="widget dashboard-widget">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action('widgets_init', 'intranet_dashboard_base_widgets_init');

function intranet_dashboard_base_unregister_default_widgets() {
	unregister_widget('WP_Widget_Search');
	unregister_widget('WP_Widget_Recent_Posts');
	unregister_widget('WP_Widget_Recent_Comments');
	unregister_widget('WP_Widget_Archives');
	unregister_widget('WP_Widget_Categories');
}
add_action('widgets_init', 'intranet_dashboard_base_unregister_default_widgets', 20);

function intranet_dashboard_base_reset_default_sidebars() {
	$sidebars_widgets = get_option('sidebars_widgets', array());

	if (! is_array($sidebars_widgets)) {
		$sidebars_widgets = array();
	}

	$sidebars_widgets['home-right'] = array();
	$sidebars_widgets['footer-1']   = array();

	update_option('sidebars_widgets', $sidebars_widgets);
}
add_action('after_switch_theme', 'intranet_dashboard_base_reset_default_sidebars');

function intranet_dashboard_base_register_search_route() {
	add_rewrite_rule('^busca-interna/?$', 'index.php?intranet_search=1', 'top');
	add_rewrite_rule('^meu-perfil/?$', 'index.php?intranet_profile_edit=1', 'top');
	add_rewrite_rule('^noticias/?$', 'index.php?intranet_news_archive=1', 'top');
	add_rewrite_rule('^noticias/page/([0-9]+)/?$', 'index.php?intranet_news_archive=1&paged=$matches[1]', 'top');
}
add_action('init', 'intranet_dashboard_base_register_search_route');

function intranet_dashboard_base_register_query_vars($vars) {
	$vars[] = 'intranet_search';
	$vars[] = 'term';
	$vars[] = 'intranet_profile_edit';
	$vars[] = 'intranet_news_archive';

	return $vars;
}
add_filter('query_vars', 'intranet_dashboard_base_register_query_vars');

function intranet_dashboard_base_template_include($template) {
	if (get_query_var('intranet_search')) {
		$custom_template = get_template_directory() . '/search-intranet.php';

		if (file_exists($custom_template)) {
			return $custom_template;
		}
	}

	if (get_query_var('intranet_profile_edit')) {
		$custom_template = get_template_directory() . '/page-editar-perfil.php';

		if (file_exists($custom_template)) {
			return $custom_template;
		}
	}

	if (get_query_var('intranet_news_archive')) {
		$custom_template = get_template_directory() . '/archive.php';

		if (file_exists($custom_template)) {
			return $custom_template;
		}
	}

	return $template;
}
add_filter('template_include', 'intranet_dashboard_base_template_include');

function intranet_dashboard_base_prepare_news_archive_query($query) {
	if (! ($query instanceof WP_Query) || ! $query->is_main_query() || is_admin()) {
		return;
	}

	if (! $query->get('intranet_news_archive')) {
		return;
	}

	$query->set('post_type', 'post');
	$query->set('post_status', 'publish');
	$query->set('ignore_sticky_posts', true);
	$query->set('posts_per_page', (int) get_option('posts_per_page', 10));
}
add_action('pre_get_posts', 'intranet_dashboard_base_prepare_news_archive_query');

function intranet_dashboard_base_disable_native_search() {
	if (is_admin()) {
		return;
	}

	if (is_search()) {
		$search_term = get_query_var('s');
		$redirect    = home_url('/busca-interna/');

		if ($search_term) {
			$redirect = add_query_arg('term', rawurlencode((string) $search_term), $redirect);
		}

		wp_safe_redirect($redirect, 301);
		exit;
	}
}
add_action('template_redirect', 'intranet_dashboard_base_disable_native_search');

function intranet_dashboard_base_require_login() {
	if (is_user_logged_in() || is_admin()) {
		return;
	}

	if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
		return;
	}

	auth_redirect();
}
add_action('template_redirect', 'intranet_dashboard_base_require_login', 1);

function intranet_dashboard_base_flush_rewrite_rules() {
	intranet_dashboard_base_register_search_route();
	flush_rewrite_rules();
}
add_action('after_switch_theme', 'intranet_dashboard_base_flush_rewrite_rules');

function intranet_dashboard_base_maybe_flush_rewrite_rules() {
	$rewrite_version = '3';

	if (get_option('intranet_dashboard_base_rewrite_version') === $rewrite_version) {
		return;
	}

	intranet_dashboard_base_register_search_route();
	flush_rewrite_rules(false);
	update_option('intranet_dashboard_base_rewrite_version', $rewrite_version);
}
add_action('admin_init', 'intranet_dashboard_base_maybe_flush_rewrite_rules');

function intranet_dashboard_base_menu_fallback() {
	echo '<ul class="menu dashboard-menu">';
	echo '<li><a href="' . esc_url(home_url('/')) . '">' . esc_html__('Para Voce', 'intranet-dashboard-base') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/institucional')) . '">' . esc_html__('Institucional', 'intranet-dashboard-base') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/comunicados')) . '">' . esc_html__('Comunicados', 'intranet-dashboard-base') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/eventos')) . '">' . esc_html__('Eventos', 'intranet-dashboard-base') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/links')) . '">' . esc_html__('Links', 'intranet-dashboard-base') . '</a></li>';
	echo '<li><a href="' . esc_url(home_url('/meu-perfil/')) . '">' . esc_html__('Meu Perfil', 'intranet-dashboard-base') . '</a></li>';
	echo '</ul>';
}

function intranet_dashboard_base_body_classes($classes) {
	$classes[] = 'intranet-dashboard-base';

	if (is_front_page()) {
		$classes[] = 'is-dashboard';
	}

	if (get_query_var('intranet_profile_edit')) {
		$classes[] = 'is-profile-edit';
	}

	return $classes;
}
add_filter('body_class', 'intranet_dashboard_base_body_classes');

<?php
/**
 * Theme bootstrap.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

require get_template_directory() . '/inc/intranet-modules.php';

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

	register_nav_menus(
		array(
			'primary' => __('Menu Principal', 'intranet-dashboard-base'),
			'utility' => __('Menu Utilitario', 'intranet-dashboard-base'),
		)
	);
}
add_action('after_setup_theme', 'intranet_dashboard_base_setup');

function intranet_dashboard_base_assets() {
	wp_enqueue_style(
		'intranet-dashboard-base-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array(),
		wp_get_theme()->get('Version')
	);

	wp_enqueue_script(
		'intranet-dashboard-base-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		wp_get_theme()->get('Version'),
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

function intranet_dashboard_base_get_reading_time_label($post_id = 0) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	if (! $post_id) {
		return __('1 min de leitura', 'intranet-dashboard-base');
	}

	$content     = get_post_field('post_content', $post_id);
	$word_count  = str_word_count(wp_strip_all_tags((string) $content));
	$minutes     = max(1, (int) ceil($word_count / 200));
	$minutes_txt = sprintf(_n('%s min de leitura', '%s min de leitura', $minutes, 'intranet-dashboard-base'), number_format_i18n($minutes));

	return $minutes_txt;
}

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

function intranet_dashboard_base_search_url() {
	return home_url('/busca-interna/');
}

function intranet_dashboard_base_profile_edit_url() {
	return add_query_arg('intranet_profile_edit', '1', home_url('/'));
}

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
		$redirect    = intranet_dashboard_base_search_url();

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

function intranet_dashboard_base_remove_wp_logo_from_admin_bar($wp_admin_bar) {
	if (! is_object($wp_admin_bar)) {
		return;
	}

	$wp_admin_bar->remove_node('wp-logo');
}
add_action('admin_bar_menu', 'intranet_dashboard_base_remove_wp_logo_from_admin_bar', 999);

function intranet_dashboard_base_flush_rewrite_rules() {
	intranet_dashboard_base_register_search_route();
	flush_rewrite_rules();
}
add_action('after_switch_theme', 'intranet_dashboard_base_flush_rewrite_rules');

function intranet_dashboard_base_maybe_flush_rewrite_rules() {
	$rewrite_version = '2';

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
	echo '<li><a href="' . esc_url(intranet_dashboard_base_profile_edit_url()) . '">' . esc_html__('Meu Perfil', 'intranet-dashboard-base') . '</a></li>';
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
		wp_safe_redirect(add_query_arg('profile-updated', 'nonce-error', intranet_dashboard_base_profile_edit_url()));
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
			wp_safe_redirect(add_query_arg('profile-updated', 'password-mismatch', intranet_dashboard_base_profile_edit_url()));
			exit;
		}

		if (strlen($password) < 6) {
			wp_safe_redirect(add_query_arg('profile-updated', 'password-short', intranet_dashboard_base_profile_edit_url()));
			exit;
		}

		$user_data['user_pass'] = $password;
	}

	$result = wp_update_user($user_data);

	if (is_wp_error($result)) {
		wp_safe_redirect(add_query_arg('profile-updated', 'error', intranet_dashboard_base_profile_edit_url()));
		exit;
	}

	$meta_fields = array(
		'job_title'        => 'sanitize_text_field',
		'department'       => 'sanitize_text_field',
		'birthday'         => 'sanitize_text_field',
		'extension_number' => 'sanitize_text_field',
	);

	foreach ($meta_fields as $field => $callback) {
		update_user_meta($user_id, $field, call_user_func($callback, wp_unslash($_POST[ $field ] ?? '')));
	}

	if (! empty($_FILES['profile_photo']['name'])) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$attachment_id = media_handle_upload('profile_photo', 0);

		if (! is_wp_error($attachment_id)) {
			update_user_meta($user_id, 'intranet_profile_photo_id', (int) $attachment_id);
		} else {
			wp_safe_redirect(add_query_arg('profile-updated', 'photo-error', intranet_dashboard_base_profile_edit_url()));
			exit;
		}
	}

	if (! empty($user_data['user_pass'])) {
		wp_set_current_user($user_id);
		wp_set_auth_cookie($user_id, true);
	}

	wp_safe_redirect(add_query_arg('profile-updated', '1', intranet_dashboard_base_profile_edit_url()));
	exit;
}
add_action('template_redirect', 'intranet_dashboard_base_handle_profile_update', 20);

function intranet_dashboard_base_get_weather_code_label($code) {
	$labels = array(
		0  => __('Ceu limpo', 'intranet-dashboard-base'),
		1  => __('Quase limpo', 'intranet-dashboard-base'),
		2  => __('Parcialmente nublado', 'intranet-dashboard-base'),
		3  => __('Nublado', 'intranet-dashboard-base'),
		45 => __('Nevoeiro', 'intranet-dashboard-base'),
		48 => __('Nevoeiro com geada', 'intranet-dashboard-base'),
		51 => __('Garoa leve', 'intranet-dashboard-base'),
		53 => __('Garoa moderada', 'intranet-dashboard-base'),
		55 => __('Garoa intensa', 'intranet-dashboard-base'),
		56 => __('Garoa congelante leve', 'intranet-dashboard-base'),
		57 => __('Garoa congelante intensa', 'intranet-dashboard-base'),
		61 => __('Chuva fraca', 'intranet-dashboard-base'),
		63 => __('Chuva moderada', 'intranet-dashboard-base'),
		65 => __('Chuva forte', 'intranet-dashboard-base'),
		66 => __('Chuva congelante leve', 'intranet-dashboard-base'),
		67 => __('Chuva congelante forte', 'intranet-dashboard-base'),
		71 => __('Neve fraca', 'intranet-dashboard-base'),
		73 => __('Neve moderada', 'intranet-dashboard-base'),
		75 => __('Neve forte', 'intranet-dashboard-base'),
		77 => __('Graos de neve', 'intranet-dashboard-base'),
		80 => __('Pancadas de chuva', 'intranet-dashboard-base'),
		81 => __('Pancadas moderadas', 'intranet-dashboard-base'),
		82 => __('Pancadas fortes', 'intranet-dashboard-base'),
		85 => __('Pancadas de neve leves', 'intranet-dashboard-base'),
		86 => __('Pancadas de neve fortes', 'intranet-dashboard-base'),
		95 => __('Trovoada', 'intranet-dashboard-base'),
		96 => __('Trovoada com granizo leve', 'intranet-dashboard-base'),
		99 => __('Trovoada com granizo forte', 'intranet-dashboard-base'),
	);

	return isset($labels[$code]) ? $labels[$code] : __('Condicao indisponivel', 'intranet-dashboard-base');
}

function intranet_dashboard_base_get_weather_code_icon($code, $is_day = true) {
	$is_day = (bool) $is_day;

	if (0 === (int) $code) {
		return $is_day ? 'SOL' : 'NOI';
	}

	if (in_array((int) $code, array(1, 2), true)) {
		return $is_day ? 'SOL' : 'NUB';
	}

	if (in_array((int) $code, array(3, 45, 48), true)) {
		return 'NUB';
	}

	if (in_array((int) $code, array(51, 53, 55, 56, 57, 61, 63, 65, 66, 67, 80, 81, 82), true)) {
		return 'CHV';
	}

	if (in_array((int) $code, array(71, 73, 75, 77, 85, 86), true)) {
		return 'FRI';
	}

	if (in_array((int) $code, array(95, 96, 99), true)) {
		return 'TRV';
	}

	return 'CLM';
}

function intranet_dashboard_base_get_weather_visual($icon_code) {
	$visuals = array(
		'SOL' => array(
			'slug'   => 'sun',
			'symbol' => '☀',
		),
		'NOI' => array(
			'slug'   => 'night',
			'symbol' => '☾',
		),
		'NUB' => array(
			'slug'   => 'cloud',
			'symbol' => '☁',
		),
		'CHV' => array(
			'slug'   => 'rain',
			'symbol' => '☂',
		),
		'FRI' => array(
			'slug'   => 'cold',
			'symbol' => '❄',
		),
		'TRV' => array(
			'slug'   => 'storm',
			'symbol' => '⚡',
		),
		'CLM' => array(
			'slug'   => 'mild',
			'symbol' => '◌',
		),
	);

	return isset($visuals[$icon_code]) ? $visuals[$icon_code] : $visuals['CLM'];
}

function intranet_dashboard_base_get_weather_visual_tokens($icon_code) {
	$visuals = array(
		'SOL' => array(
			'slug'   => 'sun',
			'symbol' => '&#9728;',
		),
		'NOI' => array(
			'slug'   => 'night',
			'symbol' => '&#9790;',
		),
		'NUB' => array(
			'slug'   => 'cloud',
			'symbol' => '&#9729;',
		),
		'CHV' => array(
			'slug'   => 'rain',
			'symbol' => '&#9730;',
		),
		'FRI' => array(
			'slug'   => 'cold',
			'symbol' => '&#10052;',
		),
		'TRV' => array(
			'slug'   => 'storm',
			'symbol' => '&#9889;',
		),
		'CLM' => array(
			'slug'   => 'mild',
			'symbol' => '&#9676;',
		),
	);

	return isset($visuals[$icon_code]) ? $visuals[$icon_code] : $visuals['CLM'];
}

function intranet_dashboard_base_get_weekday_short_label($date_string) {
	$timestamp = strtotime((string) $date_string);

	if (! $timestamp) {
		return '';
	}

	$labels = array(
		'Sun' => __('Dom', 'intranet-dashboard-base'),
		'Mon' => __('Seg', 'intranet-dashboard-base'),
		'Tue' => __('Ter', 'intranet-dashboard-base'),
		'Wed' => __('Qua', 'intranet-dashboard-base'),
		'Thu' => __('Qui', 'intranet-dashboard-base'),
		'Fri' => __('Sex', 'intranet-dashboard-base'),
		'Sat' => __('Sab', 'intranet-dashboard-base'),
	);
	$key = gmdate('D', $timestamp);

	return isset($labels[$key]) ? $labels[$key] : wp_date('D', $timestamp);
}

function intranet_dashboard_base_get_weather_data() {
	$cache_key   = 'intranet_dashboard_base_weather';
	$cached_data = get_transient($cache_key);

	if (false !== $cached_data && is_array($cached_data)) {
		return $cached_data;
	}

	$query_args = array(
		'latitude'        => '-21.79',
		'longitude'       => '-45.25',
		'current_weather' => 'true',
		'daily'           => 'weathercode,temperature_2m_max,temperature_2m_min',
		'timezone'        => 'America/Sao_Paulo',
		'forecast_days'   => 5,
	);

	$response = wp_remote_get(
		add_query_arg($query_args, 'https://api.open-meteo.com/v1/forecast'),
		array(
			'timeout' => 12,
		)
	);

	if (is_wp_error($response)) {
		return null;
	}

	$status_code = (int) wp_remote_retrieve_response_code($response);

	if (200 !== $status_code) {
		return null;
	}

	$body = json_decode(wp_remote_retrieve_body($response), true);

	if (! is_array($body) || empty($body['current_weather']) || empty($body['daily'])) {
		return null;
	}

	$current = $body['current_weather'];
	$daily   = $body['daily'];
	$forecast = array();

	$times = isset($daily['time']) && is_array($daily['time']) ? $daily['time'] : array();

	foreach ($times as $index => $date_string) {
		$forecast[] = array(
			'date'        => $date_string,
			'weekday'     => intranet_dashboard_base_get_weekday_short_label($date_string),
			'code'        => isset($daily['weathercode'][$index]) ? (int) $daily['weathercode'][$index] : null,
			'label'       => intranet_dashboard_base_get_weather_code_label(isset($daily['weathercode'][$index]) ? (int) $daily['weathercode'][$index] : null),
			'icon'        => intranet_dashboard_base_get_weather_code_icon(isset($daily['weathercode'][$index]) ? (int) $daily['weathercode'][$index] : null),
			'temp_max'    => isset($daily['temperature_2m_max'][$index]) ? round((float) $daily['temperature_2m_max'][$index]) : null,
			'temp_min'    => isset($daily['temperature_2m_min'][$index]) ? round((float) $daily['temperature_2m_min'][$index]) : null,
			'date_label'  => wp_date('d/m', strtotime((string) $date_string)),
		);
	}

	$data = array(
		'location_label' => __('Tres Coracoes MG', 'intranet-dashboard-base'),
		'updated_at'     => current_time('timestamp'),
		'current'        => array(
			'temperature' => isset($current['temperature']) ? round((float) $current['temperature']) : null,
			'code'        => isset($current['weathercode']) ? (int) $current['weathercode'] : null,
			'is_day'      => ! empty($current['is_day']),
			'label'       => intranet_dashboard_base_get_weather_code_label(isset($current['weathercode']) ? (int) $current['weathercode'] : null),
			'icon'        => intranet_dashboard_base_get_weather_code_icon(
				isset($current['weathercode']) ? (int) $current['weathercode'] : null,
				! empty($current['is_day'])
			),
		),
		'forecast'       => $forecast,
	);

	set_transient($cache_key, $data, HOUR_IN_SECONDS);

	return $data;
}

function intranet_dashboard_base_sanitize_checkbox($checked) {
	return ! empty($checked);
}

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

function intranet_dashboard_base_login_styles() {
	$logo_url       = intranet_dashboard_base_get_login_logo_url();
	$disable_scroll = (bool) get_theme_mod('intranet_dashboard_base_login_disable_scroll', false);
	?>
	<style id="intranet-dashboard-base-login-styles">
		body.login {
			min-height: 100vh;
			display: grid;
			place-items: center;
			padding: 32px 16px;
			background:
				radial-gradient(circle at top left, rgba(102, 192, 189, 0.26), transparent 28%),
				radial-gradient(circle at right bottom, rgba(201, 159, 45, 0.18), transparent 24%),
				linear-gradient(135deg, #0d2740 0%, #123f63 48%, #eaf1f5 48%, #f7fafc 100%);
			font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
			<?php if ($disable_scroll) : ?>
			overflow: hidden;
			<?php endif; ?>
		}

		<?php if ($disable_scroll) : ?>
		html {
			overflow: hidden;
		}
		<?php endif; ?>

		body.login::before,
		body.login::after {
			content: "";
			position: fixed;
			inset: auto;
			border-radius: 999px;
			pointer-events: none;
			filter: blur(6px);
			opacity: 0.7;
		}

		body.login::before {
			width: 280px;
			height: 280px;
			top: 56px;
			right: min(8vw, 80px);
			background: rgba(102, 192, 189, 0.24);
		}

		body.login::after {
			width: 220px;
			height: 220px;
			left: min(6vw, 56px);
			bottom: 48px;
			background: rgba(255, 255, 255, 0.18);
		}

		.login h1 {
			margin: 0 0 22px;
		}

		.login h1 a {
			width: min(100%, 240px);
			height: 84px;
			margin: 0 auto;
			background-image: url('<?php echo esc_url($logo_url); ?>');
			background-size: contain;
			background-position: center;
			background-repeat: no-repeat;
		}

		.login #login {
			position: relative;
			width: min(100%, 420px);
			padding: 0;
			margin: 0 auto;
		}

		.login #loginform,
		.login #lostpasswordform,
		.login #registerform,
		.login #resetpassform {
			margin: 0;
			padding: 30px 30px 26px;
			border: 1px solid rgba(16, 59, 97, 0.12);
			border-radius: 28px;
			background:
				linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(244, 248, 251, 0.95));
			box-shadow:
				0 28px 60px rgba(9, 35, 57, 0.18),
				0 10px 24px rgba(9, 35, 57, 0.08);
			backdrop-filter: blur(12px);
		}

		.login #backtoblog,
		.login #nav {
			text-align: center;
			padding: 0;
		}

		.login #nav {
			margin: 18px 0 0;
		}

		.login #backtoblog {
			margin: 10px 0 0;
		}

		.login #backtoblog a,
		.login #nav a,
		.login .privacy-policy-page-link a {
			color: #eaf4fb;
			font-weight: 500;
			transition: opacity 0.2s ease, color 0.2s ease;
		}

		.login #backtoblog a:hover,
		.login #nav a:hover,
		.login .privacy-policy-page-link a:hover,
		.login #backtoblog a:focus,
		.login #nav a:focus,
		.login .privacy-policy-page-link a:focus {
			color: #ffffff;
			opacity: 0.95;
		}

		.login label {
			color: #17324d;
			font-size: 0.92rem;
			font-weight: 600;
		}

		.login form .input,
		.login input[type="text"],
		.login input[type="password"] {
			min-height: 52px;
			padding: 0 16px;
			border: 1px solid #d2dde6;
			border-radius: 16px;
			background: #f7fafc;
			box-shadow: none;
			color: #17324d;
			font-size: 0.98rem;
			transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
		}

		.login form .input:focus,
		.login input[type="text"]:focus,
		.login input[type="password"]:focus {
			border-color: #66c0bd;
			background: #ffffff;
			box-shadow: 0 0 0 4px rgba(102, 192, 189, 0.18);
		}

		.login .button.wp-hide-pw {
			width: 46px;
			height: 46px;
			min-height: 46px;
			margin-top: 3px;
			border-radius: 14px;
			color: #17324d;
		}

		.login .forgetmenot {
			margin-top: 8px;
		}

		.login .forgetmenot label {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			font-weight: 500;
		}

		.login .button-primary {
			min-height: 52px;
			padding: 0 24px;
			border: 0;
			border-radius: 16px;
			background: linear-gradient(135deg, #66c0bd, #103b61);
			box-shadow: 0 16px 28px rgba(16, 59, 97, 0.24);
			color: #ffffff;
			font-weight: 700;
			text-shadow: none;
			transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
		}

		.login .button-primary:hover,
		.login .button-primary:focus {
			background: linear-gradient(135deg, #72ccc8, #0f4875);
			box-shadow: 0 20px 32px rgba(16, 59, 97, 0.28);
			transform: translateY(-1px);
		}

		.login .message,
		.login .notice,
		.login .success {
			border-left: 4px solid #66c0bd;
			border-radius: 16px;
			background: rgba(255, 255, 255, 0.92);
			box-shadow: 0 12px 30px rgba(9, 35, 57, 0.1);
		}

		.login .language-switcher {
			margin-top: 20px;
		}

		.login .language-switcher select {
			border-radius: 12px;
		}

		@media (max-width: 560px) {
			body.login {
				padding: 20px 12px;
				background:
					linear-gradient(180deg, #103b61 0%, #164c77 38%, #eef3f6 38%, #f8fafc 100%);
				<?php if ($disable_scroll) : ?>
				overflow: auto;
				<?php endif; ?>
			}

			.login #loginform,
			.login #lostpasswordform,
			.login #registerform,
			.login #resetpassform {
				padding: 24px 20px 22px;
				border-radius: 24px;
			}

			.login h1 a {
				height: 72px;
			}

			<?php if ($disable_scroll) : ?>
			html {
				overflow: auto;
			}
			<?php endif; ?>
		}
	</style>
	<?php
}
add_action('login_enqueue_scripts', 'intranet_dashboard_base_login_styles');

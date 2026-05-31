<?php

if (! defined('ABSPATH')) {
	exit;
}

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

function intranet_dashboard_base_sanitize_checkbox($checked) {
	return ! empty($checked);
}

function intranet_dashboard_base_search_url() {
	return home_url('/busca-interna/');
}

function intranet_dashboard_base_profile_edit_url() {
	return home_url('/meu-perfil/');
}

function intranet_dashboard_base_search_post_types() {
	return apply_filters('intranet_dashboard_base_search_post_types', array('post', 'page', 'comunicado', 'evento', 'documento', 'link_util'));
}

function intranet_dashboard_base_user_can_access_department($user_id, $department) {
	if (! $department) {
		return true;
	}

	if (user_can($user_id, 'manage_options')) {
		return true;
	}

	$user_department = get_user_meta($user_id, 'department', true);

	if (! $user_department) {
		return false;
	}

	return $user_department === $department;
}

function intranet_dashboard_base_document_is_allowed_for_current_user($document_id) {
	$terms = get_the_terms($document_id, 'documento_categoria');

	if (! $terms || is_wp_error($terms)) {
		return true;
	}

	$current_user_id = get_current_user_id();

	foreach ($terms as $term) {
		$allowed_departments = get_term_meta($term->term_id, 'documento_categoria_allowed_departments', true);

		if (! $allowed_departments || ! is_array($allowed_departments)) {
			continue;
		}

		foreach ($allowed_departments as $dept) {
			if (intranet_dashboard_base_user_can_access_department($current_user_id, $dept)) {
				return true;
			}
		}
	}

	return false;
}

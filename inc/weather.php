<?php

if (! defined('ABSPATH')) {
	exit;
}

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

function intranet_dashboard_base_get_weather_visual($icon_code, $output = 'literal') {
	$visuals = array(
		'SOL' => array(
			'slug'         => 'sun',
			'symbol'       => '☀',
			'symbol_clean' => '&#9728;',
		),
		'NOI' => array(
			'slug'         => 'night',
			'symbol'       => '☾',
			'symbol_clean' => '&#9790;',
		),
		'NUB' => array(
			'slug'         => 'cloud',
			'symbol'       => '☁',
			'symbol_clean' => '&#9729;',
		),
		'CHV' => array(
			'slug'         => 'rain',
			'symbol'       => '☂',
			'symbol_clean' => '&#9730;',
		),
		'FRI' => array(
			'slug'         => 'cold',
			'symbol'       => '❄',
			'symbol_clean' => '&#10052;',
		),
		'TRV' => array(
			'slug'         => 'storm',
			'symbol'       => '⚡',
			'symbol_clean' => '&#9889;',
		),
		'CLM' => array(
			'slug'         => 'mild',
			'symbol'       => '◌',
			'symbol_clean' => '&#9676;',
		),
	);

	$data = isset($visuals[$icon_code]) ? $visuals[$icon_code] : $visuals['CLM'];

	if ('clean' === $output) {
		$data['symbol'] = $data['symbol_clean'];
	}

	unset($data['symbol_clean']);

	return $data;
}

function intranet_dashboard_base_get_weather_data() {
	$cache_key   = 'intranet_dashboard_base_weather';
	$cached_data = get_transient($cache_key);

	if (false !== $cached_data && is_array($cached_data)) {
		return $cached_data;
	}

	$latitude  = get_theme_mod('intranet_dashboard_base_weather_latitude', '-21.79');
	$longitude = get_theme_mod('intranet_dashboard_base_weather_longitude', '-45.25');

	$query_args = array(
		'latitude'        => $latitude,
		'longitude'       => $longitude,
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

	$current  = $body['current_weather'];
	$daily    = $body['daily'];
	$forecast = array();

	$times = isset($daily['time']) && is_array($daily['time']) ? $daily['time'] : array();
	$location_label = get_theme_mod('intranet_dashboard_base_weather_location_label', __('Tres Coracoes MG', 'intranet-dashboard-base'));

	foreach ($times as $index => $date_string) {
		$weather_code = isset($daily['weathercode'][$index]) ? (int) $daily['weathercode'][$index] : null;

		$forecast[] = array(
			'date'       => $date_string,
			'weekday'    => intranet_dashboard_base_get_weekday_short_label($date_string),
			'code'       => $weather_code,
			'label'      => intranet_dashboard_base_get_weather_code_label($weather_code),
			'icon'       => intranet_dashboard_base_get_weather_code_icon($weather_code),
			'temp_max'   => isset($daily['temperature_2m_max'][$index]) ? round((float) $daily['temperature_2m_max'][$index]) : null,
			'temp_min'   => isset($daily['temperature_2m_min'][$index]) ? round((float) $daily['temperature_2m_min'][$index]) : null,
			'date_label' => wp_date('d/m', strtotime((string) $date_string)),
		);
	}

	$current_code = isset($current['weathercode']) ? (int) $current['weathercode'] : null;

	$data = array(
		'location_label' => $location_label,
		'updated_at'     => current_time('timestamp'),
		'current'        => array(
			'temperature' => isset($current['temperature']) ? round((float) $current['temperature']) : null,
			'code'        => $current_code,
			'is_day'      => ! empty($current['is_day']),
			'label'       => intranet_dashboard_base_get_weather_code_label($current_code),
			'icon'        => intranet_dashboard_base_get_weather_code_icon($current_code, ! empty($current['is_day'])),
		),
		'forecast'       => $forecast,
	);

	set_transient($cache_key, $data, HOUR_IN_SECONDS);

	return $data;
}

function intranet_dashboard_base_weather_customize_register($wp_customize) {
	$wp_customize->add_section(
		'intranet_dashboard_base_weather_section',
		array(
			'title'       => __('Previsao do Tempo', 'intranet-dashboard-base'),
			'description' => __('Configure a localizacao para a previsao do tempo no dashboard.', 'intranet-dashboard-base'),
			'priority'    => 165,
		)
	);

	$wp_customize->add_setting(
		'intranet_dashboard_base_weather_latitude',
		array(
			'default'           => '-21.79',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'intranet_dashboard_base_weather_latitude',
		array(
			'type'        => 'text',
			'section'     => 'intranet_dashboard_base_weather_section',
			'label'       => __('Latitude', 'intranet-dashboard-base'),
			'description' => __('Coordenada de latitude para a previsao do tempo.', 'intranet-dashboard-base'),
		)
	);

	$wp_customize->add_setting(
		'intranet_dashboard_base_weather_longitude',
		array(
			'default'           => '-45.25',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'intranet_dashboard_base_weather_longitude',
		array(
			'type'        => 'text',
			'section'     => 'intranet_dashboard_base_weather_section',
			'label'       => __('Longitude', 'intranet-dashboard-base'),
			'description' => __('Coordenada de longitude para a previsao do tempo.', 'intranet-dashboard-base'),
		)
	);

	$wp_customize->add_setting(
		'intranet_dashboard_base_weather_location_label',
		array(
			'default'           => __('Tres Coracoes MG', 'intranet-dashboard-base'),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'intranet_dashboard_base_weather_location_label',
		array(
			'type'        => 'text',
			'section'     => 'intranet_dashboard_base_weather_section',
			'label'       => __('Rotulo de localizacao', 'intranet-dashboard-base'),
			'description' => __('Nome da cidade/exibicao para a localizacao.', 'intranet-dashboard-base'),
		)
	);
}
add_action('customize_register', 'intranet_dashboard_base_weather_customize_register');

<?php
/**
 * Header template.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

$current_user      = wp_get_current_user();
$current_user_name = $current_user instanceof WP_User && $current_user->exists() ? $current_user->display_name : '';
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="site-shell">
	<header class="site-header">
		<div class="header-panel">
			<div class="topbar">
				<div class="brand-area">
					<div class="brand-mark">
						<?php
						if (has_custom_logo()) {
							the_custom_logo();
						} else {
							?>
							<a class="brand-fallback" href="<?php echo esc_url(home_url('/')); ?>">
								<span class="brand-icon">ID</span>
								<span class="brand-text"><?php bloginfo('name'); ?></span>
							</a>
							<?php
						}
						?>
					</div>
					<div class="brand-copy">
						<h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
					</div>
				</div>

				<button
					class="header-menu-toggle"
					type="button"
					aria-expanded="false"
					aria-controls="site-header-nav"
				>
					<span class="header-menu-toggle-icon" aria-hidden="true"></span>
					<span class="header-menu-toggle-label"><?php esc_html_e('Menu', 'intranet-dashboard-base'); ?></span>
				</button>
			</div>

			<div class="header-nav-row" id="site-header-nav">
				<nav class="primary-nav" aria-label="<?php esc_attr_e('Menu principal', 'intranet-dashboard-base'); ?>">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'primary',
							'container'      => false,
							'menu_class'     => 'menu dashboard-menu',
							'fallback_cb'    => 'intranet_dashboard_base_menu_fallback',
							'depth'          => 2,
						)
					);
					?>
				</nav>
				<div class="utility-area">
					<nav class="utility-nav" aria-label="<?php esc_attr_e('Menu utilitario', 'intranet-dashboard-base'); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'utility',
								'container'      => false,
								'menu_class'     => 'menu utility-menu',
								'fallback_cb'    => '__return_empty_string',
								'depth'          => 1,
							)
						);
						?>
					</nav>
					<div class="utility-profile-card">
						<div class="utility-profile-avatar">
							<?php echo wp_kses_post(intranet_dashboard_base_get_avatar_markup(get_current_user_id(), 'avatar-circle', 'thumbnail')); ?>
						</div>
						<div class="utility-profile-copy">
							<span class="utility-profile-label"><?php esc_html_e('Conectado como', 'intranet-dashboard-base'); ?></span>
							<strong><?php echo esc_html($current_user_name ?: __('Colaborador', 'intranet-dashboard-base')); ?></strong>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<main class="site-main">

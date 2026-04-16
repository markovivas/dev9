<?php
/**
 * Single event template.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>

<section class="content-shell">
	<?php
	while (have_posts()) :
		the_post();
		$timestamp = intranet_dashboard_base_get_event_start_timestamp(get_the_ID());
		$location  = intranet_dashboard_base_get_event_location(get_the_ID());
		$type_name = intranet_dashboard_base_get_event_type_name(get_the_ID());
		?>
		<article <?php post_class('dashboard-card single-card evento-single-card'); ?>>
			<p class="card-kicker"><?php esc_html_e('Evento', 'intranet-dashboard-base'); ?></p>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="evento-meta-grid">
				<div class="evento-meta-item">
					<strong><?php esc_html_e('Data', 'intranet-dashboard-base'); ?></strong>
					<span><?php echo esc_html($timestamp ? wp_date('d/m/Y', $timestamp) : '--'); ?></span>
				</div>
				<div class="evento-meta-item">
					<strong><?php esc_html_e('Hora', 'intranet-dashboard-base'); ?></strong>
					<span><?php echo esc_html($timestamp ? wp_date('H:i', $timestamp) : '--'); ?></span>
				</div>
				<div class="evento-meta-item">
					<strong><?php esc_html_e('Local', 'intranet-dashboard-base'); ?></strong>
					<span><?php echo esc_html($location ?: __('Nao informado', 'intranet-dashboard-base')); ?></span>
				</div>
				<div class="evento-meta-item">
					<strong><?php esc_html_e('Tipo', 'intranet-dashboard-base'); ?></strong>
					<span><?php echo esc_html($type_name ?: __('Geral', 'intranet-dashboard-base')); ?></span>
				</div>
			</div>
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</section>

<?php
get_footer();

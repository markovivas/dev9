<?php
/**
 * Event archive template.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

$upcoming_events = intranet_dashboard_base_get_upcoming_events(8);
?>

<section class="eventos-page-grid">
	<div class="dashboard-card eventos-calendar-panel">
		<div class="eventos-panel-header">
			<div>
				<p class="card-kicker"><?php esc_html_e('Agenda corporativa', 'intranet-dashboard-base'); ?></p>
				<h1 class="entry-title"><?php post_type_archive_title(); ?></h1>
			</div>
			<?php if (current_user_can('edit_posts')) : ?>
				<a class="action-button" href="<?php echo esc_url(admin_url('post-new.php?post_type=evento')); ?>"><?php esc_html_e('Novo evento', 'intranet-dashboard-base'); ?></a>
			<?php endif; ?>
		</div>

		<div class="em-calendario-wrapper" data-view="full">
			<div class="em-calendario-header em-toolbar">
				<div class="em-toolbar-section">
					<button class="em-nav-btn" type="button" data-nav="prev" aria-label="<?php esc_attr_e('Mes anterior', 'intranet-dashboard-base'); ?>">&lt;</button>
					<button class="em-nav-btn" type="button" data-nav="next" aria-label="<?php esc_attr_e('Proximo mes', 'intranet-dashboard-base'); ?>">&gt;</button>
					<button class="em-nav-btn em-today-btn" type="button" data-nav="today"><?php esc_html_e('Hoje', 'intranet-dashboard-base'); ?></button>
				</div>
				<div class="em-toolbar-section em-toolbar-center"><h3 class="em-mes-ano"></h3></div>
				<div class="em-toolbar-section em-toolbar-right">
					<button class="em-view-btn" type="button" data-view="fullscreen" aria-label="<?php esc_attr_e('Tela cheia', 'intranet-dashboard-base'); ?>">[]</button>
				</div>
			</div>
			<div class="em-dias-semana"></div>
			<div class="em-dias-grid"></div>
		</div>
	</div>

	<aside class="dashboard-column">
		<article class="dashboard-card">
			<h2 class="widget-title"><?php esc_html_e('Proximos eventos', 'intranet-dashboard-base'); ?></h2>
			<?php if ($upcoming_events) : ?>
				<div class="event-list">
					<?php
					foreach ($upcoming_events as $event) :
						?>
						<a class="event-item" href="<?php echo esc_url($event['permalink']); ?>">
							<div class="event-date-badge">
								<strong><?php echo esc_html(wp_date('d', $event['timestamp'])); ?></strong>
								<span><?php echo esc_html(wp_date('M', $event['timestamp'])); ?></span>
							</div>
							<div class="event-content">
								<h3><?php echo esc_html($event['title']); ?></h3>
								<p><?php echo esc_html(wp_date('d/m/Y H:i', $event['timestamp'])); ?></p>
								<?php if ($event['location']) : ?>
									<small><?php echo esc_html($event['location']); ?></small>
								<?php endif; ?>
								<?php if ($event['type_name']) : ?>
									<small class="event-type-pill"><?php echo esc_html($event['type_name']); ?></small>
								<?php endif; ?>
							</div>
						</a>
						<?php
					endforeach;
					?>
				</div>
			<?php else : ?>
				<p class="empty-state-copy"><?php esc_html_e('Nenhum evento futuro cadastrado no momento.', 'intranet-dashboard-base'); ?></p>
			<?php endif; ?>
		</article>
	</aside>
</section>

<?php
get_footer();

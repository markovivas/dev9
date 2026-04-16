<?php
/**
 * Front page dashboard.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

$current_user = wp_get_current_user();
$display_name = $current_user->display_name ?: __('Colaborador', 'intranet-dashboard-base');
$job_title    = get_user_meta($current_user->ID, 'job_title', true) ?: __('Area / Cargo', 'intranet-dashboard-base');
$department   = get_user_meta($current_user->ID, 'department', true);
$extension    = get_user_meta($current_user->ID, 'extension_number', true);
$today_label   = wp_date('d/m');
$weekday_label = wp_date('l');
$hour_now      = (int) current_time('G');
$birthdays     = intranet_dashboard_base_get_birthdays_for_current_month();
$announcements = intranet_dashboard_base_get_latest_announcements(3);
$events        = intranet_dashboard_base_get_upcoming_events();
$useful_links  = intranet_dashboard_base_get_featured_links();
$documents     = intranet_dashboard_base_get_featured_documents();
$weather_data  = intranet_dashboard_base_get_weather_data();
$news_posts    = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 5,
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
	)
);
$news_archive_url = home_url('/noticias/');

if ($hour_now < 12) {
	$greeting_label = __('Bom dia', 'intranet-dashboard-base');
} elseif ($hour_now < 18) {
	$greeting_label = __('Boa tarde', 'intranet-dashboard-base');
} else {
	$greeting_label = __('Boa noite', 'intranet-dashboard-base');
}
?>

<section class="dashboard-grid dashboard-hero">
	<div class="dashboard-card profile-card">
		<?php if (is_active_sidebar('home-profile')) : ?>
			<?php dynamic_sidebar('home-profile'); ?>
		<?php else : ?>
			<?php echo wp_kses_post(intranet_dashboard_base_get_avatar_markup($current_user->ID, 'profile-avatar', 'medium')); ?>
			<div class="profile-content">
				<h2><?php echo esc_html($display_name); ?></h2>
				<p><?php echo esc_html($job_title); ?></p>
				<?php if ($department) : ?>
					<p><?php echo esc_html($department); ?></p>
				<?php endif; ?>
				<?php if ($extension) : ?>
					<p><?php echo esc_html(sprintf(__('Ramal %s', 'intranet-dashboard-base'), $extension)); ?></p>
				<?php endif; ?>
				<p class="profile-card-action">
					<a class="profile-edit-link" href="<?php echo esc_url(intranet_dashboard_base_profile_edit_url()); ?>" aria-label="<?php esc_attr_e('Editar perfil', 'intranet-dashboard-base'); ?>">
						<span class="profile-edit-link-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false">
								<path d="M19.14 12.94a7.43 7.43 0 0 0 .05-.94 7.43 7.43 0 0 0-.05-.94l2.03-1.58a.5.5 0 0 0 .12-.64l-1.92-3.32a.5.5 0 0 0-.6-.22l-2.39.96a7.2 7.2 0 0 0-1.63-.94l-.36-2.54a.49.49 0 0 0-.49-.42h-3.84a.49.49 0 0 0-.49.42l-.36 2.54a7.2 7.2 0 0 0-1.63.94l-2.39-.96a.5.5 0 0 0-.6.22L2.71 8.84a.5.5 0 0 0 .12.64l2.03 1.58a7.43 7.43 0 0 0-.05.94 7.43 7.43 0 0 0 .05.94l-2.03 1.58a.5.5 0 0 0-.12.64l1.92 3.32a.5.5 0 0 0 .6.22l2.39-.96c.5.39 1.05.71 1.63.94l.36 2.54a.49.49 0 0 0 .49.42h3.84a.49.49 0 0 0 .49-.42l.36-2.54c.58-.23 1.13-.55 1.63-.94l2.39.96a.5.5 0 0 0 .6-.22l1.92-3.32a.5.5 0 0 0-.12-.64ZM12 15.5A3.5 3.5 0 1 1 12 8.5a3.5 3.5 0 0 1 0 7Z" fill="currentColor"/>
							</svg>
						</span>
						<span class="screen-reader-text"><?php esc_html_e('Editar perfil', 'intranet-dashboard-base'); ?></span>
					</a>
				</p>
			</div>
		<?php endif; ?>
	</div>

	<div class="dashboard-card hero-card">
		<?php if (is_active_sidebar('home-highlight')) : ?>
			<?php dynamic_sidebar('home-highlight'); ?>
		<?php else : ?>
			<p class="card-kicker"><?php echo esc_html($greeting_label); ?></p>
			<div class="hero-date"><?php echo esc_html($today_label); ?></div>
			<p class="hero-weekday"><?php echo esc_html(ucfirst($weekday_label)); ?></p>
			<p class="hero-copy">
				<?php
				echo esc_html(
					sprintf(
						__('Bem-vindo, %s. Use este painel para encontrar pessoas, acompanhar comunicados e acessar rapidamente o que voce mais usa na intranet.', 'intranet-dashboard-base'),
						$display_name
					)
				);
				?>
			</p>
		<?php endif; ?>
	</div>

	<div class="dashboard-card actions-card">
		<?php if (is_active_sidebar('home-actions')) : ?>
			<?php dynamic_sidebar('home-actions'); ?>
		<?php else : ?>
			<form class="quick-search" role="search" method="get" action="<?php echo esc_url(intranet_dashboard_base_search_url()); ?>">
				<label class="screen-reader-text" for="dashboard-search"><?php esc_html_e('Buscar', 'intranet-dashboard-base'); ?></label>
				<input id="dashboard-search" type="search" name="term" placeholder="<?php esc_attr_e('Busque pessoas, paginas e conteudos', 'intranet-dashboard-base'); ?>" value="<?php echo esc_attr((string) get_query_var('term')); ?>">
				<button type="submit"><?php esc_html_e('Buscar', 'intranet-dashboard-base'); ?></button>
			</form>

			<div class="action-panel">
				<p class="mini-label"><?php esc_html_e('Acesso rapido', 'intranet-dashboard-base'); ?></p>
				<div class="action-icon-grid">
					<a class="action-icon-link" href="<?php echo esc_url(home_url('/')); ?>">
						<span class="action-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false">
								<path d="M12 3.2 3.5 9.8v10a1 1 0 0 0 1 1H9.5v-6h5v6h5a1 1 0 0 0 1-1v-10Z" fill="currentColor"/>
							</svg>
						</span>
						<span class="action-icon-label" aria-hidden="true"><?php esc_html_e('Para Voce', 'intranet-dashboard-base'); ?></span>
						<span class="screen-reader-text"><?php esc_html_e('Para Voce', 'intranet-dashboard-base'); ?></span>
					</a>
					<a class="action-icon-link" href="<?php echo esc_url(home_url('/institucional')); ?>">
						<span class="action-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false">
								<path d="M12 3 4 7v2h16V7Zm-6 8h2v7H6Zm5 0h2v7h-2Zm5 0h2v7h-2ZM4 20h16v1H4Z" fill="currentColor"/>
							</svg>
						</span>
						<span class="action-icon-label" aria-hidden="true"><?php esc_html_e('Institucional', 'intranet-dashboard-base'); ?></span>
						<span class="screen-reader-text"><?php esc_html_e('Institucional', 'intranet-dashboard-base'); ?></span>
					</a>
					<a class="action-icon-link" href="<?php echo esc_url(get_post_type_archive_link('comunicado')); ?>">
						<span class="action-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false">
								<path d="M5 5h14a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H9l-4 3v-3H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" fill="currentColor"/>
							</svg>
						</span>
						<span class="action-icon-label" aria-hidden="true"><?php esc_html_e('Comunicados', 'intranet-dashboard-base'); ?></span>
						<span class="screen-reader-text"><?php esc_html_e('Comunicados', 'intranet-dashboard-base'); ?></span>
					</a>
					<a class="action-icon-link" href="<?php echo esc_url(get_post_type_archive_link('evento')); ?>">
						<span class="action-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false">
								<path d="M7 3h2v2h6V3h2v2h2a1 1 0 0 1 1 1v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a1 1 0 0 1 1-1h2Zm12 7H5v9a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1Z" fill="currentColor"/>
							</svg>
						</span>
						<span class="action-icon-label" aria-hidden="true"><?php esc_html_e('Eventos', 'intranet-dashboard-base'); ?></span>
						<span class="screen-reader-text"><?php esc_html_e('Eventos', 'intranet-dashboard-base'); ?></span>
					</a>
					<a class="action-icon-link" href="<?php echo esc_url(home_url('/links')); ?>">
						<span class="action-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false">
								<path d="M10.6 13.4a1 1 0 0 1 0-1.4l3-3a3 3 0 1 1 4.2 4.2l-2.1 2.1a3 3 0 0 1-4.2 0 1 1 0 1 1 1.4-1.4 1 1 0 0 0 1.4 0l2.1-2.1a1 1 0 1 0-1.4-1.4l-3 3a1 1 0 0 1-1.4 0Zm2.8-2.8a1 1 0 0 1 0 1.4l-3 3a3 3 0 1 1-4.2-4.2l2.1-2.1a3 3 0 0 1 4.2 0 1 1 0 1 1-1.4 1.4 1 1 0 0 0-1.4 0L7.6 12.2a1 1 0 1 0 1.4 1.4l3-3a1 1 0 0 1 1.4 0Z" fill="currentColor"/>
							</svg>
						</span>
						<span class="action-icon-label" aria-hidden="true"><?php esc_html_e('Links', 'intranet-dashboard-base'); ?></span>
						<span class="screen-reader-text"><?php esc_html_e('Links', 'intranet-dashboard-base'); ?></span>
					</a>
					<a class="action-icon-link" href="<?php echo esc_url(intranet_dashboard_base_profile_edit_url()); ?>">
						<span class="action-icon" aria-hidden="true">
							<svg viewBox="0 0 24 24" focusable="false">
								<path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Zm0 2c-4 0-7 2-7 4.5V20h14v-1.5C19 16 16 14 12 14Z" fill="currentColor"/>
							</svg>
						</span>
						<span class="action-icon-label" aria-hidden="true"><?php esc_html_e('Meu Perfil', 'intranet-dashboard-base'); ?></span>
						<span class="screen-reader-text"><?php esc_html_e('Meu Perfil', 'intranet-dashboard-base'); ?></span>
					</a>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>

<section class="dashboard-grid dashboard-content">
	<div class="dashboard-column">
		<?php if (is_active_sidebar('home-left')) : ?>
			<?php dynamic_sidebar('home-left'); ?>
		<?php else : ?>
			<article class="dashboard-card">
				<h2 class="widget-title"><?php esc_html_e('Aniversariantes do mes', 'intranet-dashboard-base'); ?></h2>
				<?php if ($birthdays) : ?>
					<ul class="list-card birthday-list">
						<?php foreach ($birthdays as $birthday) : ?>
							<li>
								<strong><?php echo esc_html(sprintf('%02d', $birthday['day'])); ?></strong>
								<div>
									<span><?php echo esc_html($birthday['display_name']); ?></span>
									<?php if ($birthday['department']) : ?>
										<small><?php echo esc_html($birthday['department']); ?></small>
									<?php endif; ?>
								</div>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php else : ?>
					<p class="empty-state-copy"><?php esc_html_e('Cadastre aniversarios no perfil dos usuarios para preencher este modulo.', 'intranet-dashboard-base'); ?></p>
				<?php endif; ?>
			</article>

			<article class="dashboard-card">
				<h2 class="widget-title"><?php esc_html_e('Documentos', 'intranet-dashboard-base'); ?></h2>
				<?php if ($documents->have_posts()) : ?>
					<ul class="document-list">
						<?php
						while ($documents->have_posts()) :
							$documents->the_post();
							$document_url  = intranet_dashboard_base_get_document_url(get_the_ID());
							$document_type = get_post_meta(get_the_ID(), '_document_file_type', true);
							$terms         = get_the_terms(get_the_ID(), 'documento_categoria');
							$category_name = (! is_wp_error($terms) && ! empty($terms)) ? $terms[0]->name : '';
							?>
							<li>
								<a class="document-item" href="<?php echo esc_url($document_url); ?>">
									<div class="document-badge"><?php echo esc_html($document_type ?: 'DOC'); ?></div>
									<div class="document-content">
										<strong><?php the_title(); ?></strong>
										<?php if ($category_name) : ?>
											<small><?php echo esc_html($category_name); ?></small>
										<?php endif; ?>
									</div>
								</a>
							</li>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</ul>
				<?php else : ?>
					<p class="empty-state-copy"><?php esc_html_e('Publique documentos e marque-os para destaque para exibir neste modulo.', 'intranet-dashboard-base'); ?></p>
				<?php endif; ?>
			</article>

			<article class="dashboard-card weather-card">
				<h2 class="widget-title"><?php esc_html_e('Previsao do tempo', 'intranet-dashboard-base'); ?></h2>
				<?php if (! empty($weather_data['current'])) : ?>
					<?php $current_weather_visual = intranet_dashboard_base_get_weather_visual_tokens($weather_data['current']['icon']); ?>
					<div class="weather-current weather-current--<?php echo esc_attr($current_weather_visual['slug']); ?>">
						<div class="weather-current-main">
							<span class="weather-current-icon" aria-hidden="true">
								<span class="weather-current-symbol"><?php echo wp_kses_post($current_weather_visual['symbol']); ?></span>
								<span class="weather-current-code"><?php echo esc_html($weather_data['current']['icon']); ?></span>
							</span>
							<div>
								<p class="mini-label"><?php echo esc_html($weather_data['location_label']); ?></p>
								<div class="weather-current-temp">
									<?php echo esc_html($weather_data['current']['temperature']); ?><small>&deg;C</small>
								</div>
								<p class="weather-current-status"><?php echo esc_html($weather_data['current']['label']); ?></p>
							</div>
						</div>
						<p class="weather-updated">
							<?php
							echo esc_html(
								sprintf(
									__('Atualizado as %s', 'intranet-dashboard-base'),
									wp_date('H:i', $weather_data['updated_at'])
								)
							);
							?>
						</p>
					</div>

					<?php if (! empty($weather_data['forecast'])) : ?>
						<ul class="weather-forecast-list">
							<?php foreach ($weather_data['forecast'] as $forecast_day) : ?>
								<?php $forecast_weather_visual = intranet_dashboard_base_get_weather_visual_tokens($forecast_day['icon']); ?>
								<li class="weather-forecast-item weather-forecast-item--<?php echo esc_attr($forecast_weather_visual['slug']); ?>">
									<div class="weather-forecast-day">
										<strong><?php echo esc_html($forecast_day['weekday']); ?></strong>
										<small><?php echo esc_html($forecast_day['date_label']); ?></small>
									</div>
									<span class="weather-forecast-icon" aria-hidden="true">
										<span class="weather-forecast-symbol"><?php echo wp_kses_post($forecast_weather_visual['symbol']); ?></span>
										<span class="weather-forecast-code"><?php echo esc_html($forecast_day['icon']); ?></span>
									</span>
									<div class="weather-forecast-temps">
										<strong><?php echo esc_html($forecast_day['temp_max']); ?>&deg;</strong>
										<small><?php echo esc_html($forecast_day['temp_min']); ?>&deg;</small>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				<?php else : ?>
					<p class="empty-state-copy"><?php esc_html_e('Nao foi possivel carregar a previsao agora. Tente novamente em instantes.', 'intranet-dashboard-base'); ?></p>
				<?php endif; ?>
			</article>
		<?php endif; ?>
	</div>

	<div class="dashboard-column dashboard-column-wide">
		<?php if (is_active_sidebar('home-middle')) : ?>
			<?php dynamic_sidebar('home-middle'); ?>
		<?php else : ?>
			<article class="dashboard-card">
				<h2 class="widget-title"><?php esc_html_e('Comunicados internos', 'intranet-dashboard-base'); ?></h2>
				<div class="notice-list">
					<?php
					if ($announcements->have_posts()) :
						while ($announcements->have_posts()) :
							$announcements->the_post();
							?>
							<a class="notice-item" href="<?php the_permalink(); ?>">
								<p class="notice-date"><?php echo esc_html(get_the_date('d/m/Y - H:i')); ?></p>
								<h3><?php the_title(); ?></h3>
								<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 22)); ?></p>
							</a>
							<?php
						endwhile;
						wp_reset_postdata();
					else :
						?>
						<div class="notice-item is-empty">
							<p class="notice-date"><?php echo esc_html(wp_date('d/m/Y - H:i')); ?></p>
							<h3><?php esc_html_e('Seu primeiro comunicado aparece aqui', 'intranet-dashboard-base'); ?></h3>
							<p><?php esc_html_e('Use o modulo Comunicados no painel para preencher automaticamente esta area do dashboard.', 'intranet-dashboard-base'); ?></p>
						</div>
					<?php endif; ?>
				</div>
				<p class="notice-card-footer"><a href="<?php echo esc_url(get_post_type_archive_link('comunicado')); ?>"><?php esc_html_e('Ver todos os comunicados', 'intranet-dashboard-base'); ?></a></p>
			</article>

			<article class="dashboard-card news-card">
				<h2 class="widget-title"><?php esc_html_e('Noticias', 'intranet-dashboard-base'); ?></h2>
				<div class="news-list">
					<?php if ($news_posts->have_posts()) : ?>
						<?php
						while ($news_posts->have_posts()) :
							$news_posts->the_post();
							?>
							<a class="news-item" href="<?php the_permalink(); ?>">
								<div class="news-item-media">
									<?php if (has_post_thumbnail()) : ?>
										<?php the_post_thumbnail('medium_large', array('class' => 'news-item-image')); ?>
									<?php else : ?>
										<span class="news-item-image news-item-image-fallback" aria-hidden="true"><?php esc_html_e('Sem imagem', 'intranet-dashboard-base'); ?></span>
									<?php endif; ?>
								</div>
								<div class="news-item-content">
									<p class="notice-date"><?php echo esc_html(get_the_date('d/m/Y')); ?></p>
									<h3><?php the_title(); ?></h3>
									<p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 24)); ?></p>
								</div>
							</a>
						<?php endwhile; ?>
						<?php wp_reset_postdata(); ?>
					<?php else : ?>
						<div class="news-item is-empty">
							<div class="news-item-media">
								<span class="news-item-image news-item-image-fallback" aria-hidden="true"><?php esc_html_e('Sem imagem', 'intranet-dashboard-base'); ?></span>
							</div>
							<div class="news-item-content">
								<p class="notice-date"><?php echo esc_html(wp_date('d/m/Y')); ?></p>
								<h3><?php esc_html_e('As noticias mais recentes aparecerao aqui', 'intranet-dashboard-base'); ?></h3>
								<p><?php esc_html_e('Publique posts no WordPress para preencher esta secao automaticamente.', 'intranet-dashboard-base'); ?></p>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<p class="news-card-footer"><a href="<?php echo esc_url($news_archive_url); ?>"><?php esc_html_e('Veja todas as noticias', 'intranet-dashboard-base'); ?></a></p>
			</article>
		<?php endif; ?>
	</div>

	<div class="dashboard-column">
		<article class="dashboard-card event-card">
			<h2 class="widget-title"><?php esc_html_e('Eventos', 'intranet-dashboard-base'); ?></h2>
			<div class="em-calendario-wrapper em-calendario-card" data-view="widget">
				<div class="em-calendario-header em-toolbar">
					<div class="em-toolbar-section">
						<button class="em-nav-btn" type="button" data-nav="prev" aria-label="<?php esc_attr_e('Mes anterior', 'intranet-dashboard-base'); ?>">&lt;</button>
						<button class="em-nav-btn" type="button" data-nav="next" aria-label="<?php esc_attr_e('Proximo mes', 'intranet-dashboard-base'); ?>">&gt;</button>
					</div>
					<div class="em-toolbar-section em-toolbar-center"><h3 class="em-mes-ano"></h3></div>
					<div class="em-toolbar-section"></div>
				</div>
				<div class="em-dias-semana"></div>
				<div class="em-dias-grid"></div>
			</div>
			<?php if ($events) : ?>
				<div class="event-list">
					<?php
					foreach ($events as $event) :
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
				<p class="event-card-footer"><a href="<?php echo esc_url(get_post_type_archive_link('evento')); ?>"><?php esc_html_e('Ver agenda completa', 'intranet-dashboard-base'); ?></a></p>
			<?php else : ?>
				<p class="empty-state-copy"><?php esc_html_e('Cadastre eventos com data e local para exibir a agenda da intranet.', 'intranet-dashboard-base'); ?></p>
			<?php endif; ?>
		</article>

		<?php if (is_active_sidebar('home-right')) : ?>
			<?php dynamic_sidebar('home-right'); ?>
		<?php else : ?>
			<article class="dashboard-card">
				<h2 class="widget-title"><?php esc_html_e('Links uteis', 'intranet-dashboard-base'); ?></h2>
				<?php if ($useful_links->have_posts()) : ?>
					<ul class="link-list">
						<?php
						while ($useful_links->have_posts()) :
							$useful_links->the_post();
							$link_url   = get_post_meta(get_the_ID(), '_useful_link_url', true);
							$link_blurb = get_post_meta(get_the_ID(), '_useful_link_description', true);
							?>
							<li>
								<a href="<?php echo esc_url($link_url ?: '#'); ?>">
									<strong><?php the_title(); ?></strong>
									<?php if ($link_blurb) : ?>
										<small><?php echo esc_html($link_blurb); ?></small>
									<?php endif; ?>
								</a>
							</li>
							<?php
						endwhile;
						wp_reset_postdata();
						?>
					</ul>
				<?php else : ?>
					<p class="empty-state-copy"><?php esc_html_e('Use o modulo Links Uteis para publicar atalhos corporativos neste card.', 'intranet-dashboard-base'); ?></p>
				<?php endif; ?>
			</article>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();

<?php

if (! defined('ABSPATH')) {
	exit;
}

get_header();

$search_term   = sanitize_text_field((string) get_query_var('term'));
$paged         = max(1, absint(get_query_var('paged')));
$search_types  = intranet_dashboard_base_search_post_types();
$has_search    = '' !== $search_term;

$content_query = null;
$user_results  = array();

if ($has_search) {
	$content_query = new WP_Query(
		array(
			'post_type'      => $search_types,
			'posts_per_page' => 15,
			'paged'          => $paged,
			's'              => $search_term,
			'post_status'    => 'publish',
		)
	);

	$user_query = new WP_User_Query(
		array(
			'search'         => '*' . $search_term . '*',
			'search_columns' => array('display_name', 'user_login', 'user_email'),
			'fields'         => array('ID', 'display_name', 'user_email'),
			'number'         => 8,
		)
	);
	$user_results = $user_query->get_results();
}
?>

<section class="content-shell default-content">
	<header class="dashboard-card archive-header">
		<p class="card-kicker"><?php esc_html_e('Busca interna', 'intranet-dashboard-base'); ?></p>
		<h1 class="entry-title"><?php echo $has_search ? sprintf(esc_html__('Resultados para "%s"', 'intranet-dashboard-base'), esc_html($search_term)) : esc_html__('Digite um termo para buscar', 'intranet-dashboard-base'); ?></h1>
	</header>

	<form class="dashboard-card quick-search search-results-form" method="get" action="<?php echo esc_url(intranet_dashboard_base_search_url()); ?>">
		<label class="screen-reader-text" for="search-results-term"><?php esc_html_e('Buscar', 'intranet-dashboard-base'); ?></label>
		<input id="search-results-term" type="search" name="term" value="<?php echo esc_attr($search_term); ?>" placeholder="<?php esc_attr_e('Busque pessoas, paginas e conteudos', 'intranet-dashboard-base'); ?>">
		<button type="submit"><?php esc_html_e('Buscar', 'intranet-dashboard-base'); ?></button>
	</form>

	<?php if ($has_search) : ?>

		<?php if (! empty($user_results)) : ?>
			<section class="dashboard-card">
				<h2 class="widget-title"><?php esc_html_e('Pessoas', 'intranet-dashboard-base'); ?></h2>
				<ul class="list-card birthday-list">
					<?php foreach ($user_results as $found_user) : ?>
						<?php
						$job_title  = get_user_meta($found_user->ID, 'job_title', true);
						$department = get_user_meta($found_user->ID, 'department', true);
						?>
						<li>
							<?php echo wp_kses_post(intranet_dashboard_base_get_avatar_markup($found_user->ID, 'avatar-circle', 'thumbnail')); ?>
							<div>
								<strong><?php echo esc_html($found_user->display_name); ?></strong>
								<?php if ($job_title) : ?>
									<small><?php echo esc_html($job_title); ?></small>
								<?php endif; ?>
								<?php if ($department) : ?>
									<small><?php echo esc_html($department); ?></small>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php if ($content_query && $content_query->have_posts()) : ?>
			<div class="post-list">
				<?php
				while ($content_query->have_posts()) :
					$content_query->the_post();
					?>
					<article <?php post_class('dashboard-card post-card'); ?>>
						<p class="card-kicker"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name ?? get_post_type()); ?></p>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="entry-summary"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?></div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php
			$total_pages = $content_query->max_num_pages;
			if ($total_pages > 1) :
				?>
				<nav class="archive-pagination" aria-label="<?php esc_attr_e('Navegacao dos resultados', 'intranet-dashboard-base'); ?>">
					<?php
					echo wp_kses_post(
						paginate_links(
							array(
								'base'    => add_query_arg('paged', '%#%'),
								'format'  => '',
								'current' => $paged,
								'total'   => $total_pages,
							)
						)
					);
					?>
				</nav>
			<?php endif; ?>

			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<article class="dashboard-card empty-card">
				<h2><?php esc_html_e('Nenhum resultado encontrado', 'intranet-dashboard-base'); ?></h2>
				<p><?php esc_html_e('Tente outro termo ou verifique se o conteudo ja foi publicado.', 'intranet-dashboard-base'); ?></p>
			</article>
		<?php endif; ?>

	<?php endif; ?>
</section>

<?php
get_footer();

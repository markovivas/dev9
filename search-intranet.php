<?php
/**
 * Custom intranet search results.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

$search_term = sanitize_text_field((string) get_query_var('term'));
$results     = new WP_Query(
	array(
		'post_type'      => array('page', 'post', 'comunicado', 'evento', 'documento'),
		'posts_per_page' => 12,
		's'              => $search_term,
		'post_status'    => 'publish',
	)
);
?>

<section class="content-shell default-content">
	<header class="dashboard-card archive-header">
		<p class="card-kicker"><?php esc_html_e('Busca interna', 'intranet-dashboard-base'); ?></p>
		<h1 class="entry-title"><?php echo esc_html($search_term ? sprintf(__('Resultados para "%s"', 'intranet-dashboard-base'), $search_term) : __('Digite um termo para buscar', 'intranet-dashboard-base')); ?></h1>
		<p class="entry-content"><?php esc_html_e('Esta busca consulta apenas os modulos internos do tema.', 'intranet-dashboard-base'); ?></p>
	</header>

	<form class="dashboard-card quick-search search-results-form" method="get" action="<?php echo esc_url(intranet_dashboard_base_search_url()); ?>">
		<label class="screen-reader-text" for="search-results-term"><?php esc_html_e('Buscar', 'intranet-dashboard-base'); ?></label>
		<input id="search-results-term" type="search" name="term" value="<?php echo esc_attr($search_term); ?>" placeholder="<?php esc_attr_e('Digite para buscar', 'intranet-dashboard-base'); ?>">
		<button type="submit"><?php esc_html_e('Buscar', 'intranet-dashboard-base'); ?></button>
	</form>

	<?php if ($search_term && $results->have_posts()) : ?>
		<div class="post-list">
			<?php
			while ($results->have_posts()) :
				$results->the_post();
				?>
				<article <?php post_class('dashboard-card post-card'); ?>>
					<p class="card-kicker"><?php echo esc_html(get_post_type_object(get_post_type())->labels->singular_name ?? get_post_type()); ?></p>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="entry-summary"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?></div>
				</article>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
	<?php elseif ($search_term) : ?>
		<article class="dashboard-card empty-card">
			<h2><?php esc_html_e('Nenhum resultado encontrado', 'intranet-dashboard-base'); ?></h2>
			<p><?php esc_html_e('Tente outro termo ou verifique se o conteudo ja foi publicado.', 'intranet-dashboard-base'); ?></p>
		</article>
	<?php endif; ?>
</section>

<?php
get_footer();

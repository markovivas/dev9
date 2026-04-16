<?php
/**
 * Archive template.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();

$is_news_archive      = (bool) get_query_var('intranet_news_archive');
$archive_kicker_label = $is_news_archive ? __('Noticias', 'intranet-dashboard-base') : __('Arquivo', 'intranet-dashboard-base');
$archive_title        = $is_news_archive ? __('Todas as noticias', 'intranet-dashboard-base') : get_the_archive_title();
$archive_description  = $is_news_archive ? __('Confira as noticias mais recentes publicadas na intranet.', 'intranet-dashboard-base') : get_the_archive_description();
?>

<section class="content-shell default-content news-archive-shell">
	<header class="dashboard-card archive-header">
		<p class="card-kicker"><?php echo esc_html($archive_kicker_label); ?></p>
		<h1 class="entry-title"><?php echo esc_html($archive_title); ?></h1>
		<?php if (! empty($archive_description)) : ?>
			<div class="entry-content"><?php echo wp_kses_post($archive_description); ?></div>
		<?php endif; ?>
	</header>

	<?php if (have_posts()) : ?>
		<div class="news-archive-list">
			<?php
			while (have_posts()) :
				the_post();
				?>
				<article <?php post_class('dashboard-card news-archive-item'); ?>>
					<a class="news-archive-link" href="<?php the_permalink(); ?>">
						<div class="news-archive-media">
							<?php if (has_post_thumbnail()) : ?>
								<?php the_post_thumbnail('large', array('class' => 'news-archive-image')); ?>
							<?php else : ?>
								<span class="news-archive-image news-archive-image-fallback" aria-hidden="true"><?php esc_html_e('Sem imagem', 'intranet-dashboard-base'); ?></span>
							<?php endif; ?>
						</div>
						<div class="news-archive-content">
							<p class="card-kicker"><?php echo esc_html(get_the_date('d/m/Y')); ?></p>
							<h2><?php the_title(); ?></h2>
							<div class="entry-summary"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 32)); ?></div>
						</div>
					</a>
				</article>
			<?php endwhile; ?>
		</div>
		<?php
		the_posts_pagination(
			array(
				'mid_size'  => 1,
				'prev_text' => __('Anterior', 'intranet-dashboard-base'),
				'next_text' => __('Proxima', 'intranet-dashboard-base'),
				'class'     => 'archive-pagination',
			)
		);
		?>
	<?php else : ?>
		<?php get_template_part('template-parts/content', 'none'); ?>
	<?php endif; ?>
</section>

<?php
get_footer();

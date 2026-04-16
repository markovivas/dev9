<?php
/**
 * Main template file.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>

<section class="content-shell default-content">
	<?php if (have_posts()) : ?>
		<div class="post-list">
			<?php
			while (have_posts()) :
				the_post();
				?>
				<article <?php post_class('dashboard-card post-card'); ?>>
					<p class="card-kicker"><?php echo esc_html(get_the_date()); ?></p>
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="entry-summary"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<?php get_template_part('template-parts/content', 'none'); ?>
	<?php endif; ?>
</section>

<?php
get_footer();

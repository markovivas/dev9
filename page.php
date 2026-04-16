<?php
/**
 * Page template.
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
		?>
		<article <?php post_class('dashboard-card page-card'); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<div class="entry-content"><?php the_content(); ?></div>
		</article>
	<?php endwhile; ?>
</section>

<?php
get_footer();

<?php
/**
 * Single post template.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header();
?>

<section class="content-shell single-news-shell">
	<?php
	while (have_posts()) :
		the_post();
		$categories       = get_the_category();
		$post_url         = get_permalink();
		$author_id        = (int) get_the_author_meta('ID');
		$reading_time     = intranet_dashboard_base_get_reading_time_label(get_the_ID());
		$share_links      = array(
			array(
				'label' => __('WhatsApp', 'intranet-dashboard-base'),
				'url'   => 'https://wa.me/?text=' . rawurlencode(get_the_title() . ' - ' . $post_url),
				'icon'  => 'whatsapp',
			),
			array(
				'label' => __('LinkedIn', 'intranet-dashboard-base'),
				'url'   => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($post_url),
				'icon'  => 'linkedin',
			),
			array(
				'label' => __('Email', 'intranet-dashboard-base'),
				'url'   => 'mailto:?subject=' . rawurlencode(get_the_title()) . '&body=' . rawurlencode($post_url),
				'icon'  => 'email',
			),
		);
		?>
		<article <?php post_class('dashboard-card single-card single-news-card'); ?>>
			<header class="single-news-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>

			<?php if (has_post_thumbnail()) : ?>
				<div class="single-news-media">
					<?php the_post_thumbnail('full', array('class' => 'single-news-image')); ?>
				</div>
			<?php endif; ?>

			<div class="entry-content single-news-content"><?php the_content(); ?></div>

			<div class="single-news-divider" aria-hidden="true"></div>

			<section class="single-news-details">
				<div class="single-news-meta">
					<div class="single-news-meta-item">
						<span class="single-news-meta-label"><?php esc_html_e('Publicado por', 'intranet-dashboard-base'); ?></span>
						<strong><?php echo esc_html(get_the_author()); ?></strong>
					</div>
					<div class="single-news-meta-item">
						<span class="single-news-meta-label"><?php esc_html_e('Leitura', 'intranet-dashboard-base'); ?></span>
						<strong><?php echo esc_html($reading_time); ?></strong>
					</div>
					<div class="single-news-meta-item">
						<span class="single-news-meta-label"><?php esc_html_e('Publicado em', 'intranet-dashboard-base'); ?></span>
						<strong><?php echo esc_html(get_the_date('d/m/Y')); ?></strong>
					</div>
				</div>

				<?php if (! empty($categories)) : ?>
					<div class="single-news-taxonomy">
						<span class="single-news-meta-label"><?php esc_html_e('Categoria', 'intranet-dashboard-base'); ?></span>
						<div class="single-news-taxonomy-list">
							<?php foreach ($categories as $category) : ?>
								<span class="single-news-pill"><?php echo esc_html($category->name); ?></span>
							<?php endforeach; ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="single-news-share">
					<span class="single-news-share-label"><?php esc_html_e('Compartilhar', 'intranet-dashboard-base'); ?></span>
					<div class="single-news-share-links">
						<?php foreach ($share_links as $share_link) : ?>
							<a class="single-news-share-link" href="<?php echo esc_url($share_link['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($share_link['label']); ?>">
								<span class="single-news-share-icon single-news-share-icon--<?php echo esc_attr($share_link['icon']); ?>" aria-hidden="true">
									<?php if ('whatsapp' === $share_link['icon']) : ?>
										<svg viewBox="0 0 24 24" focusable="false"><path d="M12 2.5A9.5 9.5 0 0 0 4 17.2L2.9 21.1l4-1A9.5 9.5 0 1 0 12 2.5Zm0 17.2a7.67 7.67 0 0 1-3.9-1.06l-.28-.16-2.38.6.64-2.32-.18-.3A7.7 7.7 0 1 1 12 19.7Zm4.22-5.75c-.23-.12-1.36-.67-1.57-.75s-.36-.11-.51.11-.59.75-.72.9-.26.17-.49.06a6.23 6.23 0 0 1-1.83-1.13 6.88 6.88 0 0 1-1.27-1.58c-.13-.22 0-.34.1-.45.1-.1.22-.26.33-.39a1.5 1.5 0 0 0 .22-.37.41.41 0 0 0 0-.39c-.06-.11-.51-1.24-.7-1.7s-.37-.38-.51-.39h-.44a.84.84 0 0 0-.61.28 2.56 2.56 0 0 0-.8 1.9 4.42 4.42 0 0 0 .93 2.35 10.13 10.13 0 0 0 3.88 3.42 13.2 13.2 0 0 0 1.29.47 3.13 3.13 0 0 0 1.44.09 2.35 2.35 0 0 0 1.54-1.09 1.92 1.92 0 0 0 .14-1.09c-.06-.09-.21-.14-.44-.25Z" fill="currentColor"/></svg>
									<?php elseif ('linkedin' === $share_link['icon']) : ?>
										<svg viewBox="0 0 24 24" focusable="false"><path d="M6.94 8.5H3.56V19h3.38ZM5.25 3A1.97 1.97 0 1 0 5.3 6.94 1.97 1.97 0 0 0 5.25 3Zm13.19 9.56c0-3.14-1.68-4.6-3.93-4.6a3.42 3.42 0 0 0-3.08 1.69V8.5H8.06c.04.76 0 10.5 0 10.5h3.37v-5.86c0-.31 0-.62.11-.84a1.84 1.84 0 0 1 1.73-1.22c1.22 0 1.7.93 1.7 2.3V19h3.37Z" fill="currentColor"/></svg>
									<?php else : ?>
										<svg viewBox="0 0 24 24" focusable="false"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5Zm2.1.5 5.9 4.72L17.9 7Zm11.9 1.28-5.38 4.3a1 1 0 0 1-1.25 0L6 8.28V17.5a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5Z" fill="currentColor"/></svg>
									<?php endif; ?>
								</span>
								<span class="screen-reader-text"><?php echo esc_html($share_link['label']); ?></span>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			</section>

			<footer class="single-news-author-box">
				<div class="single-news-author-avatar">
					<?php echo get_avatar($author_id, 72, '', get_the_author(), array('class' => 'single-news-author-image')); ?>
				</div>
				<div class="single-news-author-content">
					<span class="single-news-meta-label"><?php esc_html_e('Escrito por', 'intranet-dashboard-base'); ?></span>
					<strong><?php echo esc_html(get_the_author()); ?></strong>
					<?php if (get_the_author_meta('description', $author_id)) : ?>
						<p><?php echo esc_html(get_the_author_meta('description', $author_id)); ?></p>
					<?php else : ?>
						<p><?php esc_html_e('Conteudo publicado na intranet corporativa.', 'intranet-dashboard-base'); ?></p>
					<?php endif; ?>
				</div>
			</footer>

			<?php
			the_post_navigation(
				array(
					'prev_text' => '<span class="single-news-nav-label">' . esc_html__('Noticia anterior', 'intranet-dashboard-base') . '</span><strong>%title</strong>',
					'next_text' => '<span class="single-news-nav-label">' . esc_html__('Proxima noticia', 'intranet-dashboard-base') . '</span><strong>%title</strong>',
					'class'     => 'single-news-navigation',
				)
			);
			?>
		</article>
	<?php endwhile; ?>
</section>

<?php
get_footer();

<?php
/**
 * Footer template.
 *
 * @package IntranetDashboardBase
 */

if (! defined('ABSPATH')) {
	exit;
}

$site_name          = get_bloginfo('name');
$footer_brand_label = function_exists('mb_substr') ? mb_substr($site_name, 0, 1) : substr($site_name, 0, 1);
?>
	</main>

	<footer class="site-footer">
		<div class="footer-branding">
			<a class="footer-brand-link" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($site_name); ?>">
				<span class="footer-brand-mark">
					<?php
					if (has_custom_logo()) {
						the_custom_logo();
					} else {
						?>
						<span class="footer-brand-fallback"><?php echo esc_html($footer_brand_label); ?></span>
						<?php
					}
					?>
				</span>
				<span class="footer-brand-copy">
					<strong><?php echo esc_html($site_name); ?></strong>
					<span><?php echo esc_html(gmdate('Y')); ?></span>
				</span>
			</a>
		</div>
	</footer>
</div>
<?php wp_footer(); ?>
</body>
</html>

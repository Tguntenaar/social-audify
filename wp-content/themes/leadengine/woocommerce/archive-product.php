<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();

$redux_ThemeTek = get_option( 'redux_ThemeTek' );
$shop_columns = $shop_sidebar = '';
if (!isset($redux_ThemeTek['tek-woo-shop-columns'])) {
	$shop_columns = 'woo-3-columns';
} else {
	$shop_columns = $redux_ThemeTek['tek-woo-shop-columns'];
}

if (isset($redux_ThemeTek['tek-woo-sidebar-position'])) {
	$shop_sidebar = $redux_ThemeTek['tek-woo-sidebar-position'];
}
?>


<section>
	<div class="ShopFiltersWrapper">
		<div class="container">
			<?php if ( have_posts() ) : ?>
				<?php
					/**
					 * woocommerce_before_shop_loop hook.
					 *
					 * @hooked wc_print_notices - 10
					 * @hooked woocommerce_result_count - 20
					 * @hooked woocommerce_catalog_ordering - 30
					 */
					do_action( 'woocommerce_before_shop_loop' );
				?>
		</div>
	</div>
	<div class="container">
		<?php if (isset($shop_columns) && isset($shop_sidebar)) : ?>
			<?php if ($shop_columns == 'woo-2-columns' && $shop_sidebar == 'woo-sidebar-left') : ?>
				<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
					<div class="woo-sidebar">
						<?php dynamic_sidebar('shop-sidebar'); ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>

		<?php if (isset($shop_columns)) : ?>
			<?php if ($shop_columns != 'woo-2-columns') : ?>
	  		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 <?php echo esc_html($shop_columns); ?>">
			<?php else : ?>
				<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 woo-2-columns">
			<?php endif; ?>
		<?php endif; ?>
			<?php woocommerce_product_loop_start(); ?>
				<?php woocommerce_product_subcategories(); ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php wc_get_template_part( 'content', 'product' ); ?>
				<?php endwhile; // end of the loop. ?>
			<?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook.
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

			<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>
				<?php wc_get_template( 'loop/no-products-found.php' ); ?>
			<?php endif; ?>
		</div>
		<?php if (isset($shop_columns) && isset($shop_sidebar)) : ?>
			<?php if ($shop_columns == 'woo-2-columns' && $shop_sidebar == 'woo-sidebar-right') : ?>
				<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
					<div class="woo-sidebar">
						<?php dynamic_sidebar('shop-sidebar'); ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>

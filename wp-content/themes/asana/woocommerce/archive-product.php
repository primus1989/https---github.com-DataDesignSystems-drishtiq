<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' );
global $woocommerce;
$sProdCatSlug = get_query_var( 'product_cat', null ); ?>

	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>

			<div class="pagePanel clear">
				<ul class="productFilter clear">
					<li><a<?php if ( $sProdCatSlug == null ) { echo ' class="active"'; } ?> href="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>"><?php _e('All', 'asana') ?></a></li>
                <?php $aAllParentCats = get_terms( 'product_cat', array('hide_empty' => false, 'parent' => 0) );
                if ( isset($aAllParentCats) ) {
                    foreach ( $aAllParentCats as $oParentCat ) {
                ?>
					<li><a<?php if ( $sProdCatSlug == $oParentCat->slug ) { echo ' class="active"'; } ?> href="<?php echo get_term_link($oParentCat) ?>"><?php echo esc_html( $oParentCat->name ); ?></a></li>
				<?php }
                } ?>
				</ul>
				<div class="miniCart">
					<i></i>
                    <?php if ( sizeof( $woocommerce->cart->get_cart() ) > 0 ) : ?>
					<span><?php echo sizeof( $woocommerce->cart->get_cart() ) ?></span>
                    <?php else : ?>
                    <span>0</span>
                    <?php endif; ?>
				</div>
			</div>

        <?php woocommerce_breadcrumb(); ?>

		<?php do_action( 'woocommerce_archive_description' ); ?>

		<?php if ( have_posts() ) : ?>

			<?php
				/**
				 * woocommerce_before_shop_loop hook
				 *
				 * @hooked woocommerce_result_count - 20
				 * @hooked woocommerce_catalog_ordering - 30
				 */
				do_action( 'woocommerce_before_shop_loop' );
			?>

			<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>

			<?php
				/**
				 * woocommerce_after_shop_loop hook
				 *
				 * @hooked woocommerce_pagination - 10
				 */
				do_action( 'woocommerce_after_shop_loop' );
			?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>

        <?php uni_minicart_content(); ?>

	<?php
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

<?php get_footer( 'shop' ); ?>
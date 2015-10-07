<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' );
global $woocommerce; ?>

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
					<li><a class="active" href="<?php echo get_permalink( woocommerce_get_page_id( 'shop' ) ); ?>"><?php _e('All', 'asana') ?></a></li>
                <?php $aAllParentCats = get_terms( 'product_cat', array('hide_empty' => false, 'parent' => 0) );
                if ( isset($aAllParentCats) ) {
                    foreach ( $aAllParentCats as $oParentCat ) {
                ?>
					<li><a href="<?php echo get_term_link($oParentCat) ?>"><?php echo esc_html( $oParentCat->name ); ?></a></li>
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

		<?php while ( have_posts() ) : the_post(); ?>

			<?php wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>

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
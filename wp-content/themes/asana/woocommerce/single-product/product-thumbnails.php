<?php
/**
 * Single Product Thumbnails
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_attachment_ids();
/*
if ( $attachment_ids ) {
	$loop 		= 0;
	$columns 	= apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
	?>
	<div class="thumbnails <?php echo 'columns-' . $columns; ?>"><?php

		foreach ( $attachment_ids as $attachment_id ) {

			$classes = array( 'zoom' );

			if ( $loop == 0 || $loop % $columns == 0 )
				$classes[] = 'first';

			if ( ( $loop + 1 ) % $columns == 0 )
				$classes[] = 'last';

			$image_link = wp_get_attachment_url( $attachment_id );

			if ( ! $image_link )
				continue;

			$image       = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ) );
			$image_class = esc_attr( implode( ' ', $classes ) );
			$image_title = esc_attr( get_the_title( $attachment_id ) );

			echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a href="%s" class="%s" title="%s" data-rel="prettyPhoto[product-gallery]">%s</a>', $image_link, $image_class, $image_title, $image ), $attachment_id, $post->ID, $image_class );

			$loop++;
		}

	?></div>
	<?php
}*/

if ( !has_post_thumbnail() && !$attachment_ids ) {
?>

						<div class="galleryThumb">
							<a href="<?php the_permalink() ?>" class="galleryThumbItem active">
								<?php echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID ); ?>
							</a>
						</div>
						<div class="productGalleryWrap">
						</div>

<?php
} else if ( has_post_thumbnail() && !$attachment_ids ) {
			$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
			$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
			$image       	= get_the_post_thumbnail( $post->ID, 'shop_thumbnail', array(
				'title'	=> $image_title,
				'alt'	=> $image_title
				) );
                    $aImage = wp_get_attachment_image_src( get_post_thumbnail_id(), 'shop_single' );
?>

						<div class="galleryThumb">
							<a href="#imgId1" class="galleryThumbItem active">
								<?php echo $image; ?>
							</a>
						</div>
						<div class="productGalleryWrap">
							<img class="current" id="imgId1" src="<?php echo esc_url( $aImage[0] ); ?>" alt="<?php echo esc_attr( $image_title ); ?>" width="540" height="540">
						</div>

<?php
} else if ( $attachment_ids ) {
    $i = $l = 0;
?>

						<div class="galleryThumb">
                <?php foreach ( $attachment_ids as $attachment_id ) {
                    $i++;
                    $aImageThumb = wp_get_attachment_image_src( $attachment_id, 'shop_thumbnail' );
                    $image_title = esc_attr( get_the_title( $attachment_id ) );
                ?>
							<a href="#imgId<?php echo $i; ?>" class="galleryThumbItem<?php if ( $i == 1 ) echo ' active'; ?>">
								<img src="<?php echo esc_url( $aImageThumb[0] ) ?>" alt="<?php echo esc_attr( $image_title ); ?>" width="118" height="118">
							</a>
				<?php } ?>
						</div>
						<div class="productGalleryWrap">
                <?php foreach ( $attachment_ids as $attachment_id ) {
                    $l++;
                    $aImage = wp_get_attachment_image_src( $attachment_id, 'shop_single' );
                    $image_title = esc_attr( get_the_title( $attachment_id ) );
                ?>
							<img<?php if ( $l == 1 ) echo ' class="current"'; ?> id="imgId<?php echo $l; ?>" src="<?php echo esc_url( $aImage[0] ); ?>" alt="<?php echo esc_attr( $image_title ); ?>" width="540" height="540">
				<?php } ?>
						</div>

<?php } ?>
<?php
/*
*  Template Name: Cart
*/
get_header();
        $iCartAttachId = ( ot_get_option( 'uni_cart_header_bg' ) ) ? ot_get_option( 'uni_cart_header_bg' ) : '';
        if ( !empty($iCartAttachId) ) {
            $aPageHeaderImage = wp_get_attachment_image_src( $iCartAttachId, 'full' );
            $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
            $sPageHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-cart.jpg';
        }
?>

	<section class="container">
		<div class="pageHeader" style="background-image: url(<?php echo esc_url( $sPageHeaderImage ); ?>);">
            <?php
            $sTitleColor = ( ot_get_option( 'uni_cart_header_title_color' ) ) ? ot_get_option( 'uni_cart_header_title_color' ) : '#ffffff';
		    if ( ot_get_option( 'uni_cart_header_title' ) ) {
                $sOutput = ot_get_option( 'uni_cart_header_title' );
            } else {
			    $sOutput = __('ONLINE BOUTIQUE', 'asana');
            }
            echo '<h1 class="page-title">'.esc_html( $sOutput ).'</h1>';
            echo '<style>.pageHeader h1 {color:'.$sTitleColor.';}</style>';
            ?>
		</div>
		<div class="contentWrap">

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

			<div class="pagePanel clear">
				<div class="pageTitle"><?php the_title() ?></div>
			</div>
			<div class="cartPage clear">

                <?php the_content() ?>                      

			</div>

			<div class="overlay"></div>
		</div>

        <?php endwhile; endif; ?>

	</section>

<?php get_footer(); ?>
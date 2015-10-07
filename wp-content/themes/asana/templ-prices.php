<?php
/*
*  Template Name: Prices
*/
get_header();
?>

	<section class="container">
        <?php if (have_posts()) : while (have_posts()) : the_post();
        if ( has_post_thumbnail() ) {
            $iAttachId = get_post_thumbnail_id( $post->ID );
            $aPageHeaderImage = wp_get_attachment_image_src( $iAttachId, 'full' );
            $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
            $sPageHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-classes.jpg';
        }
        ?>
		<div class="pageHeader" style="background-image: url(<?php echo esc_url( $sPageHeaderImage ); ?>);">
			<h1><?php the_title() ?></h1>
		</div>
		<div class="contentWrap">

            <div class="pagePanel clear">
                <div class="pageTitle"><?php echo ( ot_get_option( 'uni_home_membership_cards_title' ) ) ? esc_html( ot_get_option( 'uni_home_membership_cards_title' ) ) : __('Membership cards', 'asana'); ?></div>
            </div>

            <div class="membershipCardsBlock">

                <div class="membershipCardsWrap">
    <?php
    $aPricesArgs = array(
        'post_type'	=> 'uni_price',
        'post_status' => 'publish',
        'ignore_sticky_posts' => 1,
        'posts_per_page' => -1,
    );

    $oPricesQuery = new WP_Query( $aPricesArgs );
    if ( $oPricesQuery->have_posts() ) :
    while ( $oPricesQuery->have_posts() ) : $oPricesQuery->the_post();
    $aPostCustom = get_post_custom( $post->ID );
    ?>
                    <div class="membershipCardItem">
                        <h3><?php the_title() ?></h3>
                        <div class="membershipCard">
                            <span><span><?php if ( !empty($aPostCustom['uni_currency'][0]) ) echo esc_html($aPostCustom['uni_currency'][0]) ?></span><?php if ( !empty($aPostCustom['uni_price_val'][0]) ) echo esc_html($aPostCustom['uni_price_val'][0]) ?></span>
                            <em><?php if ( !empty($aPostCustom['uni_period'][0]) ) echo esc_html($aPostCustom['uni_period'][0]) ?></em>
                        </div>
                        <?php uni_excerpt(40, '', true) ?>
                        <?php if ( isset($aPostCustom['uni_order_button_ext_url_enable'][0]) && $aPostCustom['uni_order_button_ext_url_enable'][0] == 'on' && !empty($aPostCustom['uni_order_button_uri'][0]) ) { ?>
                        <a href="<?php echo $aPostCustom['uni_order_button_uri'][0]; ?>" class="membership-card-order"><?php echo ( !empty($aPostCustom['uni_order_button_text'][0]) ) ? $aPostCustom['uni_order_button_text'][0] : __('Order Now', 'asana') ?></a>
                        <?php } else { ?>
                        <a href="#membershipCardOrderPopup" class="membershipCardOrder membership-card-order" data-priceid="<?php echo $post->ID; ?>" data-pricetitle="<?php echo esc_attr( get_the_title($post->ID) ) ?>"><?php echo ( !empty($aPostCustom['uni_order_button_text'][0]) ) ? $aPostCustom['uni_order_button_text'][0] : __('Order Now', 'asana') ?></a>
                        <?php } ?>
                    </div>
	<?php endwhile; endif;
	wp_reset_postdata(); ?>
                </div>
            </div>

		</div>
        <?php endwhile; endif; ?>
	</section>

<?php get_footer(); ?>
<?php
/*
*  Template Name: About Page
*/
get_header();
        $iCheckoutAttachId = ( ot_get_option( 'uni_about_header_bg' ) ) ? ot_get_option( 'uni_about_header_bg' ) : '';
        if ( !empty($iCheckoutAttachId) ) {
            $aPageHeaderImage = wp_get_attachment_image_src( $iCheckoutAttachId, 'full' );
            $sPageHeaderImage = $aPageHeaderImage[0];
        } else {
            $sPageHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-cart.jpg';
        }
?>

	<section class="container">

    <?php if (have_posts()) : while (have_posts()) : the_post();
        $aCustomData = get_post_custom( $post->ID );
    ?>
		<div class="pageHeader" style="background-image: url(<?php echo esc_url( $sPageHeaderImage ); ?>);">
			<h1>
            <?php
                $sTitleColor = ( ot_get_option( 'uni_about_header_title_color' ) ) ? ot_get_option( 'uni_about_header_title_color' ) : '#ffffff';
                echo ( ot_get_option( 'uni_about_header_title' ) ) ? ot_get_option( 'uni_about_header_title' ) : __('About Our Studio', 'asana');
            ?>
            </h1>
            <?php echo '<style>.pageHeader h1 {color:'.$sTitleColor.';}</style>'; ?>
		</div>
		<div class="ourStory">
			<div class="wrapper">
				<div class="storyItem clear">
					<div class="storyImg">
                        <?php if ( has_post_thumbnail() ) { ?>
                            <?php the_post_thumbnail( 'unithumb-contactgallery', array( 'alt' => the_title_attribute('echo=0') ) ); ?>
                        <?php } else { ?>
						    <img src="<?php echo get_template_directory_uri(); ?>/images/placeholders/story.jpg" alt="<?php the_title_attribute() ?>" width="570" height="405">
                        <?php } ?>
					</div>
					<div class="storyDesc">
                        <h3><?php the_title() ?></h3>
						<?php the_content() ?>
					</div>
				</div>
			</div>
		</div>

    <?php if ( !empty($aCustomData['uni_meet_team_enable'][0]) && $aCustomData['uni_meet_team_enable'][0] == 'on' ) { ?>
    <?php
    $oUserQuery = new WP_User_Query( array('role' => 'instructor') );
    if ( ! empty( $oUserQuery->results ) ) {
    ?>
		<div class="ourTeam">
			<div class="blockTitle"><?php if ( !empty($aCustomData['uni_meet_team_title'][0]) ) { echo esc_html( $aCustomData['uni_meet_team_title'][0] ); } else { echo 'meet our team'; } ?></div>
			<div class="teamItemWrap clear">
    <?php
        foreach ( $oUserQuery->results as $oUser ) {
            $aUserData = ( get_user_meta( $oUser->ID, '_uni_user_data', true ) ) ? get_user_meta( $oUser->ID, '_uni_user_data', true ) : array();
    ?>
				<div class="teamItem" data-userid="user_<?php echo $oUser->ID ?>">
					<?php echo do_shortcode('[uav-display-avatar id="'.$oUser->ID.'" size="342" alt="'.esc_attr($oUser->display_name).'"]') ?>
					<div class="overlay">
						<div class="teamItemNameWrap">
							<h3><?php echo esc_html( $oUser->display_name ); ?></h3>
						</div>
						<p><?php if ( !empty($aUserData['profession']) ) echo esc_attr( $aUserData['profession'] ) ?></p>
					</div>
				</div>

				<div class="teamItemDesc" id="user_<?php echo $oUser->ID ?>">
                    <div class="teamItemDescWrap">
    					<?php echo do_shortcode('[uav-display-avatar id="'.$oUser->ID.'" size="342" alt="'.esc_attr( $oUser->display_name ).'"]') ?>
    					<h3><?php echo esc_html( $oUser->display_name ); ?></h3>
    					<p class="teamItemDescText1"><?php if ( !empty($aUserData['profession']) ) echo esc_attr( $aUserData['profession'] ) ?></p>
    					<p class="teamItemDescText"><?php echo esc_html( $oUser->description ); ?></p>
    					<div class="teamItemSocial">
                        <?php if ( !empty($aUserData['fb_link']) ) { ?>
    						<a href="<?php echo esc_url( $aUserData['fb_link'] ) ?>"><i class="fa fa-facebook"></i></a>
                        <?php } ?>
                        <?php if ( !empty($aUserData['tw_link']) ) { ?>
    						<a href="<?php echo esc_url( $aUserData['tw_link'] ) ?>"><i class="fa fa-twitter"></i></a>
                        <?php } ?>
                        <?php if ( !empty($aUserData['gplus_link']) ) { ?>
    						<a href="<?php echo esc_url( $aUserData['gplus_link'] ) ?>"><i class="fa fa-google-plus"></i></a>
                        <?php } ?>
                        <?php if ( !empty($aUserData['pi_link']) ) { ?>
    						<a href="<?php echo esc_url( $aUserData['pi_link'] ) ?>"><i class="fa fa-pinterest"></i></a>
                        <?php } ?>
    					</div>
                    </div>
					<span class="closeTeamDesc">
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" x="0px" y="0px" width="16px" height="16px" viewBox="1.5 -1 16 16" enable-background="new 1.5 -1 16 16" xml:space="preserve">
							<path fill="#C1F4E8" d="M11.185 7l6.315 6.314L15.814 15L9.5 8.685L3.186 15L1.5 13.314L7.815 7L1.5 0.686L3.186-1L9.5 5.3 L15.814-1L17.5 0.686L11.185 7z"/>
						</svg>
					</span>
				</div>
    <?php
        }
    ?>
			</div>
		</div>
    <?php
    }
    ?>
    <?php } ?>

        <?php endwhile; endif; ?>

    <?php if ( !empty($aCustomData['uni_about_values_enable'][0]) && $aCustomData['uni_about_values_enable'][0] == 'on' ) { ?>
		<div class="ourValues">
			<div class="blockTitle"><?php echo ( $aCustomData['uni_about_values_title'][0] ) ? esc_html( $aCustomData['uni_about_values_title'][0] ) : __('our values', 'asana'); ?></div>
    <?php
    $aValuesArgs = array(
        'post_type'	=> 'uni_value',
        'post_status' => 'publish',
        'ignore_sticky_posts'	=> 1,
        'posts_per_page' => -1,
    );

    $oValuesQuery = new WP_Query( $aValuesArgs );
    if ( $oValuesQuery->have_posts() ) :
    $i = 1;
    while ( $oValuesQuery->have_posts() ) : $oValuesQuery->the_post();
        if ( has_post_thumbnail() ) {
            $iImageId = get_post_thumbnail_id();
            $aValueHeaderImage = wp_get_attachment_image_src( $iImageId, 'full' );
            $sValueHeaderImage = $aValueHeaderImage[0];
        } else {
            $sValueHeaderImage = get_template_directory_uri().'/images/placeholders/pageheader-value.jpg';
        }
    ?>
			<div class="parallaxBox" data-type="parallax" data-speed="10" style="background-image:url('<?php echo esc_url( $sValueHeaderImage ); ?>')">
				<h3><?php the_title() ?></h3>
			</div>
			<div class="wrapper">
				<?php the_content() ?>
			</div>
    <?php $i++;
    endwhile; endif;
    wp_reset_postdata(); ?>
		</div>
    <?php } ?>

    <?php if ( !empty($aCustomData['uni_instagram_enable'][0]) && $aCustomData['uni_instagram_enable'][0] == 'on' ) { ?>
		<div class="ourInstagram">
			<?php echo do_shortcode('[instagram-feed showheader=true widthunit=273 heightunit=273 imagepadding=0 showfollow=true showbutton=false]'); ?>
		</div>
    <?php } ?>

	</section>

<?php get_footer(); ?>
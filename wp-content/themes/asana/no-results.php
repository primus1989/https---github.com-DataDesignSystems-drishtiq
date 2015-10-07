<?php if ( is_search() ) : ?>
			<div class="page404Wrap">
        		<h1 class="blockTitle"><?php printf( __( 'Search results for: "%s"', 'asana' ), get_search_query() ); ?></h1>
        		<p><?php _e('Nothing Found', 'asana') ?><br><?php _e( 'Sorry, but nothing matched your search terms. <br> Please try again with some different keywords.', 'asana' ); ?></p>
				<a href="<?php if ( function_exists('icl_get_languages') ) { echo esc_url( icl_get_home_url() ); } else { echo esc_url( home_url() ); } ?>" class="homePage"><?php _e('Homepage', 'asana') ?></a>
			</div>
<?php else : ?>
			<div class="page404Wrap">
        		<h1 class="blockTitle"><?php _e( 'Nothing Found', 'asana' ); ?></h1>
        		<p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for.', 'asana' ); ?></p>
				<a href="<?php if ( function_exists('icl_get_languages') ) { echo esc_url( icl_get_home_url() ); } else { echo esc_url( home_url() ); } ?>" class="homePage"><?php _e('Homepage', 'asana') ?></a>
			</div>
<?php endif; ?>
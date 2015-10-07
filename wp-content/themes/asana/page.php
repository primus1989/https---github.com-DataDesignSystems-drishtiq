<?php get_header(); ?>

	<section class="container">

        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="wrapper">

			<div id="post-<?php the_ID(); ?>" <?php post_class('singlePostWrap clear') ?>>

				<h1 class="singleTitle"><?php the_title() ?></h1>

                <?php the_content() ?>

			</div>

            <?php comments_template(); ?>

		</div>

        <?php endwhile; endif; ?>
	</section>

<?php get_footer(); ?>
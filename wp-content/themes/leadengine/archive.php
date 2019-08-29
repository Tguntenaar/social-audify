<?php
/**
 * The template for displaying Archive pages.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 * @package leadengine
 * by KeyDesign
 */

 get_header(); ?>

<?php
	$redux_ThemeTek = get_option( 'redux_ThemeTek' );
  $kd_post_content = $blog_template_class = $page_layout = '';

  if (!isset($redux_ThemeTek['tek-blog-sidebar'])) {
		$redux_ThemeTek['tek-blog-sidebar'] = 0;
	}

  if( !class_exists( 'ReduxFrameworkPlugin' ) ) {
    $kd_post_content .= "img-top-list";
  } elseif (isset($redux_ThemeTek['tek-blog-template']) && ($redux_ThemeTek['tek-blog-template'] != '')) {
    $kd_post_content .= $redux_ThemeTek['tek-blog-template'];
    $blog_template_class .= 'blog-'.$redux_ThemeTek['tek-blog-template'];
  }

  if ($redux_ThemeTek['tek-blog-sidebar']) {
    $page_layout = "use-sidebar";
  }
?>

<div id="posts-content" class="container <?php echo esc_html( $page_layout ); ?> <?php echo esc_html( $blog_template_class ); ?>" >
	<?php if ( ($redux_ThemeTek['tek-blog-sidebar']) ) { ?>
		<div class="col-xs-12 col-sm-12 col-lg-8">
	<?php } else { ?>
		<div class="col-xs-12 col-sm-12 col-lg-8 BlogFullWidth">
	<?php } ?>
	<?php
		if ( have_posts() ) :

			/* Start the Loop */
			while ( have_posts() ) : the_post();

				get_template_part( 'core/templates/post/blog', $kd_post_content );

			endwhile;

			the_posts_pagination( array(
				'mid_size' => 1,
				'prev_text' => __( 'Previous', 'leadengine' ),
				'next_text' => __( 'Next', 'leadengine' ),
			) );

		else :

			get_template_part( 'core/templates/post/content', 'none' );

		endif;
	?>
	</div>
  <?php if (($redux_ThemeTek['tek-blog-sidebar'])) : ?>
    <div class="col-xs-12 col-sm-12 col-lg-4">
      <div class="right-sidebar">
		     <?php get_sidebar(); ?>
      </div>
		</div>
	<?php endif; ?>
</div>

<?php get_footer(); ?>

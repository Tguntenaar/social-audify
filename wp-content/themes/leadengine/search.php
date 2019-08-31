<?php
/**
 * The template for displaying Search Results pages.
 * @package leadengine
 * by KeyDesign
 */

 get_header(); ?>

 <?php
 	$redux_ThemeTek = get_option( 'redux_ThemeTek' );

   if (!isset($redux_ThemeTek['tek-blog-sidebar'])) {
 		$redux_ThemeTek['tek-blog-sidebar'] = 0;
 	}

   if ($redux_ThemeTek['tek-blog-sidebar']) {
     $page_layout = "use-sidebar";
   }
 ?>

<div id="posts-content" class="container" >
  <?php if (($redux_ThemeTek['tek-blog-sidebar'])) : ?>
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
  <?php else : ?>
    <div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 BlogFullWidth">
  <?php endif; ?>
    	<?php
    		if ( have_posts() ) :

    			while ( have_posts() ) : the_post();
    				get_template_part( 'core/templates/post/content', 'search' );
    			endwhile;

    			the_posts_pagination( array(
    				'mid_size' => 1,
    				'prev_text' => __( 'Previous', 'leadengine' ),
    				'next_text' => __( 'Next', 'leadengine' ),
    			) );
      ?>
    <?php
		  else :
        get_template_part( 'core/templates/post/content', 'none' );
      endif;
    ?>
    </div>
    <?php if (($redux_ThemeTek['tek-blog-sidebar'])) : ?>
      <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
        <div class="right-sidebar">
  		     <?php get_sidebar(); ?>
        </div>
  		</div>
  	<?php endif; ?>
</div>

<?php get_footer(); ?>

<?php
/**
 * The Template for displaying all single posts.
 * @package leadengine
 * by KeyDesign
 */

get_header(); ?>

<?php
	$redux_ThemeTek = get_option( 'redux_ThemeTek' );
	$page_layout = '';

	if (!isset($redux_ThemeTek['tek-blog-single-sidebar'])) {
		$redux_ThemeTek['tek-blog-single-sidebar'] = 1;
	}

	if ($redux_ThemeTek['tek-blog-single-sidebar']) {
		$page_layout .= "use-sidebar";
	}
?>

<div id="posts-content" class="container blog-single <?php echo esc_html( $page_layout ); ?>">
	<?php if ($redux_ThemeTek['tek-blog-single-sidebar']) { ?>
		<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
	<?php } else { ?>
		<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8 BlogFullWidth">
	<?php } ?>
		<?php
		while ( have_posts() ) : the_post();
			get_template_part( 'core/templates/post/content', 'single' );
		endwhile; // End of the loop.
		?>
	</div>
	<?php if ($redux_ThemeTek['tek-blog-single-sidebar']) { ?>
		<div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
      <div class="right-sidebar">
		     <?php get_sidebar(); ?>
      </div>
		</div>
	<?php } ?>
</div>

<?php if (isset($redux_ThemeTek['tek-related-posts']) && $redux_ThemeTek['tek-related-posts'] == true) : ?>
	<?php get_template_part( 'core/templates/post/content', 'post-related' ); ?>
<?php endif; ?>

<?php get_footer(); ?>

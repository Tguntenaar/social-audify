<?php
/**
 * Template part for displaying standard posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package leadengine
 * by KeyDesign
 */

?>

<?php
	$redux_ThemeTek = get_option( 'redux_ThemeTek' );
	$without_image_class = '';

  if (!isset($redux_ThemeTek['tek-blog-sidebar'])) {
		$redux_ThemeTek['tek-blog-sidebar'] = 0;
	}

	if (!has_post_thumbnail()) {
		$without_image_class .= 'without-image';
	}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
	<?php if (has_post_thumbnail()) : ?>
		<div class="entry-image">
			<a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('keydesign-grid-image'); ?></a>
		</div>
	<?php endif; ?>
	<div class="entry-wrapper <?php echo esc_html($without_image_class); ?>">
		<h4 class="blog-single-title"><a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
		<div class="entry-meta">
			<?php if ( is_sticky() ) echo '<span class="fa fa-thumb-tack"></span> Sticky <span class="blog-separator">|</span>  '; ?>
			<div class="post-meta-child">
				<span class="published"><span class="fa fa-clock-o"></span><a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php  the_time( get_option('date_format') ); ?></a></span>
				<span class="blog-label"><span class="fa fa-folder-open-o"></span><?php  the_category(', '); ?></span>
			</div>
			<div class="post-meta-child">
				<span class="author"><span class="fa fa-keyboard-o"></span><?php  the_author_posts_link(); ?></span>
				<span class="comment-count"><span class="fa fa-comment-o"></span><?php  comments_popup_link( esc_html__('No comments yet', 'leadengine'), esc_html__('1 comment', 'leadengine'), esc_html__('% comments', 'leadengine') ); ?></span>
			</div>
		</div>
		<div class="entry-content">
			<?php the_excerpt(); ?>
			<?php wp_link_pages(); ?>
			<a class="tt_button" href="<?php esc_url(the_permalink()); ?>"><?php _e( 'Read more', 'leadengine' ); ?></a>
		</div>
	</div>
</article>

<?php
/**
 * Template part for displaying posts with excerpts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package leadEngine
 * by KeyDesign
 */

?>

<?php
	$redux_ThemeTek = get_option( 'redux_ThemeTek' );

  if (!isset($redux_ThemeTek['tek-blog-sidebar'])) {
		$redux_ThemeTek['tek-blog-sidebar'] = 0;
	}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
	<h3 class="blog-single-title"><a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
	<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<span class="page-type"><span class="fa fa-file-text-o"></span><?php _e( 'Post', 'leadengine' ); ?></span>
			<span class="published"><span class="fa fa-clock-o"></span><a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php  the_time( get_option('date_format') ); ?></a></span>
			<span class="author"><span class="fa fa-keyboard-o"></span><?php  the_author_posts_link(); ?></span>
			<span class="blog-label"><span class="fa fa-folder-open-o"></span><?php  the_category(', '); ?></span>
			<span class="comment-count"><span class="fa fa-comment-o"></span><?php  comments_popup_link( esc_html__('No comments yet', 'leadengine'), esc_html__('1 comment', 'leadengine'), esc_html__('% comments', 'leadengine') ); ?></span>
		</div>
	<?php else : ?>
		<div class="entry-meta">
			<?php if ( 'page' === get_post_type() ) : ?>
				<span class="page-type"><span class="fa fa-file-text-o"></span><?php _e( 'Page', 'leadengine' ); ?></span>
			<?php elseif ( 'portfolio' === get_post_type() ) : ?>
				<span class="page-type"><span class="fa fa-file-image-o"></span><?php _e( 'Portfolio', 'leadengine' ); ?></span>
			<?php elseif ( 'product' === get_post_type() ) : ?>
				<span class="page-type"><span class="fa fa-shopping-cart"></span><?php _e( 'Product', 'leadengine' ); ?></span>
			<?php endif; ?>
			<span class="published"><span class="fa fa-clock-o"></span><a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php  the_time( get_option('date_format') ); ?></a></span>
		</div>
	<?php endif; ?>
		<div class="entry-content">
			<?php if ( has_excerpt() ) {
				the_excerpt();
			} ?>
			<a class="post-link" href="<?php esc_url(the_permalink()); ?>"><?php _e( 'Read more', 'leadengine' ); ?></a>
		</div>
</article>

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

  if (!isset($redux_ThemeTek['tek-blog-sidebar'])) {
		$redux_ThemeTek['tek-blog-sidebar'] = 0;
	}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
	<a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><h2 class="blog-single-title"><?php the_title(); ?></h2></a>
		<div class="entry-meta">
			<?php if ( is_sticky() ) echo '<span class="fa fa-thumb-tack"></span> Sticky <span class="blog-separator">|</span>  '; ?>
			<span class="published"><span class="fa fa-clock-o"></span><a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php  the_time( get_option('date_format') ); ?></a></span>
				<span class="author"><span class="fa fa-keyboard-o"></span><?php  the_author_posts_link(); ?></span>
				<span class="blog-label"><span class="fa fa-folder-open-o"></span><?php  the_category(', '); ?></span>
				<span class="comment-count"><span class="fa fa-comment-o"></span><?php  comments_popup_link( esc_html__('No comments yet', 'leadengine'), esc_html__('1 comment', 'leadengine'), esc_html__('% comments', 'leadengine') ); ?></span>
		</div>
		<a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('large'); ?></a>
		<div class="entry-content">
			<?php if(has_excerpt()) : ?>
			<?php the_excerpt(); ?>
			<?php else : ?>
			<div class="page-content"><?php the_content(); ?></div>
			<?php endif; ?>
			<?php wp_link_pages(); ?>
			<a class="tt_button" href="<?php esc_url(the_permalink()); ?>"><?php echo apply_filters( 'blog_readmore_text', esc_html__("Read more", "leadengine") ); ?><span class="fa fa-chevron-right"></span></a>
		</div>
</article>

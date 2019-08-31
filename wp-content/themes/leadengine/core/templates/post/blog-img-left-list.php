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
	$url = $entry_class = '';
  if (!isset($redux_ThemeTek['tek-blog-sidebar'])) {
		$redux_ThemeTek['tek-blog-sidebar'] = 0;
	}

	if ( !has_post_thumbnail() ) {
		$entry_class .="without-image";
	}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-image">
		<a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail('keydesign-left-image'); ?></a>
	</div>
	<div class="entry-wrapper <?php echo esc_html($entry_class); ?>">
		<div class="entry-meta">
			<?php if ( is_sticky() ) echo '<span class="fa fa-thumb-tack"></span> Sticky <span class="blog-separator">|</span>  '; ?>
			<span class="published"><span class="fa fa-clock-o"></span><a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php  the_time( get_option('date_format') ); ?></a></span>
			<span class="blog-label"><span class="fa fa-folder-open-o"></span><?php  the_category(', '); ?></span>
		</div>
		<?php if ('quote' === get_post_format()) : ?>
		  <h2 class="blog-single-title quote"><?php the_title(); ?></h2>
		<?php else : ?>
		  <h2 class="blog-single-title"><a href="<?php esc_url(the_permalink()); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
		<?php endif; ?>
		<div class="entry-content">
			<?php the_excerpt(); ?>
			<?php wp_link_pages(); ?>
			<a class="tt_button tt_secondary_button" href="<?php esc_url(the_permalink()); ?>"><?php _e( 'Read more', 'leadengine' ); ?></a>
		</div>
	</div>
</article>

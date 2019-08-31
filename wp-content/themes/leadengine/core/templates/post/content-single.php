<?php
/**
 * The Template for displaying all single posts.
 * @package leadengine
 * by KeyDesign
 */

?>

<?php
	$redux_ThemeTek = get_option( 'redux_ThemeTek' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="blog-single-content">
		<?php if ('quote' === get_post_format()) : ?>
		  <h1 class="blog-single-title quote"><?php the_title(); ?></h1>
		<?php else : ?>
		  <h1 class="blog-single-title"><?php the_title(); ?></h1>
		<?php endif; ?>
		<div class="entry-meta">
			<?php  if ( is_sticky() ) echo '<span class="fa fa-thumb-tack"></span> Sticky <span class="blog-separator">|</span>  '; ?>
			<span class="published"><span class="fa fa-clock-o"></span><?php the_time( get_option('date_format') ); ?></span>
			<span class="author"><span class="fa fa-keyboard-o"></span><?php the_author_posts_link(); ?></span>
			<span class="blog-label"><span class="fa fa-folder-open-o"></span><?php the_category(', '); ?></span>
		</div>
		<?php get_template_part( 'core/templates/post/post-type/content', get_post_format() ); ?>
		<div class="blog-content">
			<?php the_content(); ?>
			<?php wp_link_pages(); ?>
		</div>
		<div class="meta-content">

			<?php 
				the_tags(
				    '<div class="tags"><span class="tags-label">Tags:</span>',
				    ' ',
				    '</div>'
				); 
			?>

			<?php if (class_exists('KEYDESIGN_ADDON_CLASS')) {
				if (isset($redux_ThemeTek['tek-blog-social-sharing']) && $redux_ThemeTek['tek-blog-social-sharing'] == true) {
					get_template_part( 'core/templates/post/content', 'post-share' );
				}
			} ?>

			<?php if ($redux_ThemeTek['tek-blog-single-nav']) : ?>
				<div class="navigation pagination">
					<?php previous_post_link('%link', __('Previous', 'leadengine')); ?>
					<?php next_post_link('%link', __('Next', 'leadengine')); ?>
				</div>
			<?php endif; ?>

			<?php if (isset($redux_ThemeTek['tek-author-box']) && $redux_ThemeTek['tek-author-box'] == true) : ?>
				<?php get_template_part( 'core/templates/post/content', 'post-author' ); ?>
			<?php endif; ?>

		</div>
	</div>
</article>
<div class="page-content comments-content">
	<?php
		// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() || '0' != get_comments_number() ) {
			comments_template();
		}
	?>
</div>

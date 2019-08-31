<?php
/**
 * Template part for displaying the portfolio page content
 *
 * @package leadengine
 * by KeyDesign
 */

?>

<?php
	$load_nav = $load_related = $load_comments = '';
	$redux_ThemeTek = get_option( 'redux_ThemeTek' );
	$themetek_page_bgcolor = get_post_meta( get_the_ID(), '_themetek_page_bgcolor', true );
	$themetek_page_background_color = ' background-color:'.$themetek_page_bgcolor.';';
	$themetek_page_top_padding = get_post_meta( get_the_ID(), '_themetek_page_top_padding', true );
	$themetek_page_bottom_padding = get_post_meta( get_the_ID(), '_themetek_page_bottom_padding', true );
	if (isset($redux_ThemeTek['tek-portfolio-single-nav']) && $redux_ThemeTek['tek-portfolio-single-nav'] == true ) {
		$load_nav = true;
	}
	if (isset($redux_ThemeTek['tek-portfolio-related-posts']) && $redux_ThemeTek['tek-portfolio-related-posts'] == true ) {
		$load_related = true;
	}
	if (isset($redux_ThemeTek['tek-portfolio-comments']) && $redux_ThemeTek['tek-portfolio-comments'] == true ) {
		$load_comments = true;
	}
?>

<section id="single-page" class="section portfolio-wrapper <?php echo esc_attr($post->post_name);?>" style="
	<?php echo ( !empty($themetek_page_bgcolor) ? esc_attr($themetek_page_background_color) : '' ); ?>
	<?php echo ( !empty($themetek_page_top_padding) ? ' padding-top:'. esc_attr($themetek_page_top_padding) .';' : '' );?>
	<?php echo ( !empty($themetek_page_bottom_padding) ? ' padding-bottom:'. esc_attr($themetek_page_bottom_padding) .';' : '' );?> ">
    <div class="container">
			<div class="row single-page-content portfolio-single">
				<?php the_content(); ?>
			</div>
    </div>
</section>
<?php
	// If comments are open or we have at least one comment, load up the comment template.
	if ( $load_comments ) :
		if ( comments_open() || get_comments_number() ) : ?>
			<div class="page-content comments-content container">
				<?php comments_template(); ?>
			</div>
		<?php endif;
	endif;
?>
<?php if ($load_related) : ?>
	<?php get_template_part( 'core/templates/portfolio/content', 'project-related' ); ?>
<?php endif; ?>
<?php if ($load_nav) : ?>
	<?php get_template_part( 'core/templates/portfolio/content', 'project-nav' ); ?>
<?php endif; ?>

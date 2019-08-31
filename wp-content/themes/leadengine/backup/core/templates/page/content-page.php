<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package leadengine
 * by KeyDesign
 */

?>

<?php
	$themetek_page_bgcolor = get_post_meta( get_the_ID(), '_themetek_page_bgcolor', true );
	$themetek_page_background_color = ' background-color:'.$themetek_page_bgcolor.';';
?>

<section id="single-page" class="section <?php echo esc_attr($post->post_name);?>" style="<?php echo ( !empty($themetek_page_bgcolor) ? esc_attr($themetek_page_background_color) : '' ); ?>">
    <div class="container">
			<div class="row single-page-content">
				<?php if ( is_active_sidebar( 'bbpress-sidebar' ) && class_exists( 'bbPress' ) && is_bbpress() ) : ?>
					<div class="col-xs-12 col-sm-12 col-md-8 col-lg-8">
				<?php endif; ?>
					<?php the_content(); ?>
				<?php if ( is_active_sidebar( 'bbpress-sidebar' ) && class_exists( 'bbPress' ) && is_bbpress() ) : ?>
					</div>
			    <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4">
			      <div class="right-sidebar">
					     <?php dynamic_sidebar( 'bbpress-sidebar' ); ?>
			      </div>
					</div>
			  <?php endif; ?>
			</div>
    </div>
</section>

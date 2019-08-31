<?php
/**
 * The template for displaying the Social Sharing buttons
 */
 $redux_ThemeTek = get_option( 'redux_ThemeTek' );
 if( false === $redux_ThemeTek['tek-blog-social-sharing'] ) {
 	return;
 }
 ?>

<div class="blog-social-sharing">
  <a class="tt_button btn-facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>">
    <span class="btn-text">
      <i class="fa fa-facebook"></i>
      <?php esc_html_e('Share on Facebook', 'leadengine'); ?>
    </span>
  </a>

  <a class="tt_button btn-twitter" target="_blank" href="https://twitter.com/share?url=<?php the_permalink(); ?>">
    <span class="btn-text">
      <i class="fa fa-twitter"></i>
      <?php esc_html_e('Share on Twitter', 'leadengine'); ?>
    </span>
  </a>
</div>

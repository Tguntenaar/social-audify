<?php
/**
 * Displays content for front page
 *
 * @package leadengine
 * by KeyDesign
 */

?>

<?php
   $themetek_page_bgcolor = get_post_meta( get_the_ID(), '_themetek_page_bgcolor', true );
   $themetek_page_background_color = ' background-color:'.$themetek_page_bgcolor.';';
?>
<section id="<?php echo esc_attr($post->post_name);?>" class="section" style="<?php echo ( !empty($themetek_page_bgcolor) ? esc_attr($themetek_page_background_color) : '' ); ?>">
   <div class="container" >
      <div class="row">
         <?php the_content(); ?>
      </div>
   </div>
</section>

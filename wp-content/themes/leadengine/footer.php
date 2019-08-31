<?php $redux_ThemeTek = get_option( 'redux_ThemeTek' ); ?>
</div>
<footer id="footer" class="<?php if (isset($redux_ThemeTek['tek-footer-fixed'])) { if ($redux_ThemeTek['tek-footer-fixed'] == '1') { echo esc_html('fixed'); } else { echo esc_html('classic');} } ?>">
      <?php get_sidebar( 'footer' ); ?>
      <div class="lower-footer">
          <div class="container">
             <div class="pull-left">
               <span>
                 <?php if (isset($redux_ThemeTek['tek-footer-text'])) {
                   echo wp_specialchars_decode(esc_html($redux_ThemeTek['tek-footer-text']));
                 } else {
                   echo esc_html('LeadEngine by KeyDesign. All rights reserved.');
                 } ?>
               </span>
            </div>
            <div class="pull-right">
               <?php if ( has_nav_menu( 'footer-menu' ) ) {
                   wp_nav_menu( array( 'theme_location' => 'footer-menu', 'depth' => 1, 'container' => false, 'menu_class' => 'navbar-footer', 'fallback_cb' => 'false' ) );
                } ?>
            </div>
         </div>
      </div>
</footer>
<?php if (isset($redux_ThemeTek['tek-backtotop']) && $redux_ThemeTek['tek-backtotop'] == "1") : ?>
      <div class="back-to-top">
         <i class="fa fa-angle-up"></i>
      </div>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>

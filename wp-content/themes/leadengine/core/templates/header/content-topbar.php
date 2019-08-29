<?php
$redux_ThemeTek = get_option( 'redux_ThemeTek' );
$social_icon_class = '';

if ($redux_ThemeTek['tek-topbar'] && ($redux_ThemeTek['tek-topbar-template'] == '1') || ($redux_ThemeTek['tek-topbar-template'] == '3')) : ?>
  <div class="topbar first-template">
<?php elseif ($redux_ThemeTek['tek-topbar'] && ($redux_ThemeTek['tek-topbar-template'] == '2') || ($redux_ThemeTek['tek-topbar-template'] == '4')) : ?>
  <div class="topbar second-template">
<?php endif; ?>
    <div class="container">
      <?php if ( in_array($redux_ThemeTek['tek-topbar-template'], array('1', '2', '3') ) ) : ?>
         <div class="topbar-contact">
             <?php if (isset($redux_ThemeTek['tek-business-phone']) && $redux_ThemeTek['tek-business-phone'] != '' ) : ?>
                 <span class="topbar-phone"><a href="tel:<?php echo esc_attr($redux_ThemeTek['tek-business-phone']); ?>"><?php echo esc_attr($redux_ThemeTek['tek-business-phone']); ?></a></span>
             <?php endif; ?>
             <?php if (isset($redux_ThemeTek['tek-business-email']) && $redux_ThemeTek['tek-business-email'] != '' ) : ?>
                 <span class="topbar-email"><a href="mailto:<?php echo esc_attr($redux_ThemeTek['tek-business-email']); ?>"><?php echo esc_attr($redux_ThemeTek['tek-business-email']); ?></a></span>
             <?php endif; ?>
         </div>
       <?php endif; ?>
       <?php if ( in_array($redux_ThemeTek['tek-topbar-template'], array('1', '2', '4') ) ) : ?>
         <div class="topbar-socials">
             <?php if( class_exists( 'ReduxFramework' )) { echo do_shortcode('[social_profiles]'); } ?>
         </div>
       <?php endif; ?>
       <?php if ( in_array($redux_ThemeTek['tek-topbar-template'], array('3', '4') ) ) : ?>
         <div class="topbar-menu-search">
            <div class="topbar-menu">
               <?php if ( has_nav_menu( 'topbar-menu' ) ) {
                   wp_nav_menu( array( 'theme_location' => 'topbar-menu', 'depth' => 1, 'container' => false, 'menu_class' => 'navbar-topbar', 'fallback_cb' => 'false' ) );
                }
               ?>
            </div>
            <?php if( !empty($redux_ThemeTek['tek-topbar-search']) && $redux_ThemeTek['tek-topbar-search'] == 1 ) : ?>
              <div class="topbar-search">
                 <?php get_search_form(); ?>
                 <span class="toggle-search fa-search fa"></span>
              </div>
            <?php endif; ?>
            <?php if ( function_exists('icl_object_id') ) : ?>
              <div class="topbar-lang-switcher">
                <div class="lang-switcher-wpml">
                  <?php do_action( 'wpml_add_language_selector' ); ?>
                </div>
              </div>
            <?php elseif ( class_exists('Polylang') && function_exists('pll_the_languages')) : ?>
              <div class="topbar-lang-switcher">
                <ul class="lang-switcher-polylang">
                  <?php pll_the_languages( array( 'show_flags' => 1,'show_names' => 1) ); ?>
                </ul>
              </div>
            <?php endif; ?>

            <!-- WooCommerce Cart -->
              <?php
                if (isset($redux_ThemeTek['tek-woo-hide-cart-icon']) && ($redux_ThemeTek['tek-woo-hide-cart-icon'] == '1')) {

                }
                else if( class_exists( 'WooCommerce' )) {
                    $keydesign_minicart = '';
                    $keydesign_minicart = keydesign_add_cart_in_menu();
                    echo do_shortcode( shortcode_unautop( $keydesign_minicart ) );
                }
              ?>
            <!-- END WooCommerce Cart -->
         </div>
       <?php endif; ?>
    </div>
</div>

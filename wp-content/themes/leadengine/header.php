<?php
/**
 * Theme header
 * @package leadEngine
 * by KeyDesign
 */
 ?>

<?php
  $redux_ThemeTek = get_option( 'redux_ThemeTek' );
  $hide_title_section_class = $disable_animations_class = '';
  $themetek_page_showhide_title_section = get_post_meta( get_the_ID(), '_themetek_page_showhide_title_section', true );
  if ($themetek_page_showhide_title_section && !is_search()) {
    $hide_title_section_class = 'hide-title-section';
  }

  if (isset($redux_ThemeTek['tek-disable-animations']) && $redux_ThemeTek['tek-disable-animations'] == true ) {
    $disable_animations_class = 'no-mobile-animation';
  }
?>
<!DOCTYPE html>
<html <?php language_attributes( 'html' ); ?>>
   <head>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
      <meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <?php if (isset($redux_ThemeTek['tek-main-color']) && $redux_ThemeTek['tek-main-color'] != '' ) : ?>
        <meta name="theme-color" content="<?php echo esc_attr($redux_ThemeTek['tek-main-color']); ?>" />
      <?php endif; ?>
      <link rel="profile" href="http://gmpg.org/xfn/11">
      <?php if ( ! function_exists( 'has_site_icon' ) || ! has_site_icon() ) : ?>
        <link href="<?php echo esc_url($redux_ThemeTek['tek-favicon']['url']); ?>" rel="icon">
      <?php endif; ?>
      <link rel="pingback" href="<?php esc_url(bloginfo( 'pingback_url' )); ?>" />
      <?php wp_head(); ?>
	  <meta name="google-site-verification" content="y0l2vQS9j-JuIM5dmnkggqcWfIRS7wWLaT2HSp1mKX8" />

    <!-- Facebook Pixel Code -->
    <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '693894334380047');
      fbq('track', 'PageView');
    </script>

    <noscript><img height="1" width="1" style="display:none"
      src="https://www.facebook.com/tr?id=693894334380047&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Facebook Pixel Code -->
   </head>
    <body <?php body_class();?>>
      <?php if( !empty($redux_ThemeTek['tek-preloader']) && $redux_ThemeTek['tek-preloader'] == 1 ) : ?>
        <div id="preloader">
           <div class="spinner"></div>
        </div>
      <?php endif; ?>

      <!-- Contact Modal template -->
      <?php
      if (isset($redux_ThemeTek['tek-header-button'])) {
        if ($redux_ThemeTek['tek-header-button'] && ($redux_ThemeTek['tek-header-button-action'] == '1')) {
          get_template_part( 'core/templates/header/content', 'contact-modal' );
        }
      }
      ?>
      <!-- END Contact Modal template -->

      <nav class="navbar navbar-default navbar-fixed-top <?php if (isset($redux_ThemeTek['tek-menu-style'])) { if ($redux_ThemeTek['tek-menu-style'] == '2') { echo esc_html('full-width'); }} ?> <?php if (isset($redux_ThemeTek['tek-menu-behaviour'])) { if ($redux_ThemeTek['tek-menu-behaviour'] == '2') { echo esc_html('fixed-menu'); }} ?> <?php if (isset($redux_ThemeTek['tek-topbar'])) { if ($redux_ThemeTek['tek-topbar'] == '1') { echo esc_html('with-topbar '); }} if (isset($redux_ThemeTek['tek-topbar-sticky'])) { if ($redux_ThemeTek['tek-topbar-sticky'] == '1') { echo esc_html('with-topbar-sticky '); }} ?>
      <?php if (isset($redux_ThemeTek['tek-sticky-nav-logo'])) { if ($redux_ThemeTek['tek-sticky-nav-logo'] == 'nav-secondary-logo') { echo esc_html('nav-secondary-logo'); }} ?> -->
      <?php if (isset($redux_ThemeTek['tek-transparent-nav-logo'])) { if ($redux_ThemeTek['tek-transparent-nav-logo'] == 'nav-secondary-logo' && $redux_ThemeTek['tek-transparent-homepage-menu'] == true ) { echo esc_html('nav-transparent-secondary-logo'); }} ?> " >
        <!-- Topbar template -->
        <?php if( !empty($redux_ThemeTek['tek-topbar']) && $redux_ThemeTek['tek-topbar'] == 1 ) {
          get_template_part( 'core/templates/header/content', 'topbar' );
        } ?>
        <!-- END Topbar template -->

        <div class="menubar">
          <div class="container">
           <div id="logo">


                 <!-- Image logo -->
                 <a class="logo" style="max-width: 230px;" href="<?php echo esc_url(home_url()); ?>">

                     <img class="fixed-logo" src="<?php echo esc_url(get_template_directory_uri() . '/core/assets/images/logo_socialaudify.png'); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
                     <img class="nav-logo" src="<?php echo esc_url(get_template_directory_uri() . '/core/assets/images/logo_socialaudify.png'); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />

                 </a>

           </div>
           <div class="navbar-header page-scroll">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-menu">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    </button>
                    <div class="mobile-cart">
                        <?php
                          if( !class_exists( 'WooCommerce' ))  {
                              function is_woocommerce() {}
                          }
                          if (isset($redux_ThemeTek['tek-woo-hide-cart-icon']) && ($redux_ThemeTek['tek-woo-hide-cart-icon'] == '1')) {

                          }
                          else if( class_exists( 'WooCommerce' ) && (isset($redux_ThemeTek['tek-topbar'])) && ($redux_ThemeTek['tek-topbar'] == '1')) {
                              $keydesign_minicart = '';
                              $keydesign_minicart = keydesign_add_cart_in_menu();
                              echo do_shortcode( shortcode_unautop( $keydesign_minicart ) );
                          }
                        ?>
                    </div>
            </div>
            <div id="main-menu" class="collapse navbar-collapse  navbar-right">
               <?php
                  wp_nav_menu( array( 'theme_location' => 'header-menu', 'depth' => 3, 'container' => false, 'menu_class' => 'nav navbar-nav', 'fallback_cb' => 'wp_bootstrap_navwalker::fallback', 'walker' => new wp_bootstrap_navwalker()) );
               ?>
               <?php if (isset($redux_ThemeTek['tek-header-button']) && !is_user_logged_in()){
                   get_template_part( 'core/templates/header/content', 'header-button' );
               } ?>
            </div>
            </div>
         </div>
      </nav>

      <div id="wrapper" class="<?php echo esc_html( $hide_title_section_class ).' '.esc_html( $disable_animations_class ); ?>">
        <?php get_template_part( 'core/templates/header/content', 'title-bar' ); ?>

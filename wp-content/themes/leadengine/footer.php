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
<script>

    var listAllCountries = [
    'AT',
    'BE',
    'BG',
    'HR',
    'CY',
    'CZ',
    'DK',
    'EE',
    'FI',
    'FR',
    'DE',
    'GR',
    'HU',
    'IE',
    'IT',
    'LV',
    'LT',
    'LU',
    'MT',
    'NL',
    'PL',
    'PT',
    'RO',
    'SK',
    'SI',
    'ES',
    'SE',
    'GB'
    ];

    var $ = jQuery.noConflict();
    var currentTab = 0;
    showTab(currentTab);

    var tabs = $('.tab');
    console.log(tabs);

    function nextPrev(n) {
      // This function will figure out which tab to display
      var tab = $('.tab');

      if (currentTab == 0) {
          if ($( "select#rcp_country option:checked" ).val() == "NL") {
              $(".rcp_subscription_level_1").css({'display':'none'});
              $(".rcp_subscription_level_2").css({'display':'block'});
              $(".rcp_subscription_level_3").css({'display':'block'});
              $(".rcp_subscription_level_4").css({'display':'none'});

              $("#rcp_subscription_level_2").click();
          } else if(listAllCountries.includes($( "select#rcp_country option:checked" ).val())) {
              if ($( "#rcp_btw_number" ).val() == "") {
                  $(".rcp_subscription_level_1").css({'display':'none'});
                  $(".rcp_subscription_level_2").css({'display':'block'});
                  $(".rcp_subscription_level_3").css({'display':'block'});
                  $(".rcp_subscription_level_4").css({'display':'none'});
                  $("#rcp_subscription_level_2").click();
              } else {
                  $(".rcp_subscription_level_1").css({'display':'block'});
                  $(".rcp_subscription_level_2").css({'display':'none'});
                  $(".rcp_subscription_level_3").css({'display':'none'});
                  $(".rcp_subscription_level_4").css({'display':'block'});
                  $("#rcp_subscription_level_1").click();
              }
          } else {
              $(".rcp_subscription_level_1").css({'display':'block'});
              $(".rcp_subscription_level_2").css("display", "none");
              $(".rcp_subscription_level_3").css({'display':'none'});
              $(".rcp_subscription_level_4").css({'display':'block'});
              $("#rcp_subscription_level_1").click();
          }
      }

      // Hide the current tab:
      tab.eq(currentTab).css({'display':'none'});

      // Increase or decrease the current tab by 1:
      currentTab += n;

      // Display correct tab if length not exceeded
      if (currentTab < tab.length)
        showTab(currentTab);
    }

    function showTab(index) {
      // This function will display the specified tab of the form ...
      var tab = $('.tab');
      tab.eq(index).css({'display': 'block'});

      // ... and fix the previous button:
      $('#prevBtn').css({'display': index == 0 ? 'none' : 'inline'});
      tab.eq(index).find('input[type=text]').focus();

      // Fix the next button
      var display = (index == (tab.length - 1)) ? "none": "block";
      $('#nextBtn').css({display:display});
    }

</script>
<?php wp_footer(); ?>

</body>
</html>

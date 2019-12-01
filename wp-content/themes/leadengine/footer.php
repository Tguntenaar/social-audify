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
<?php if(get_post_field( 'post_name', get_post() ) == "register" && (empty($_GET) || !empty($_GET['ref'] || !empty($_GET['discount'])))) { ?>
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

        var currentTab = 0;
        showTab(currentTab);

        var tabs = document.getElementsByClassName('tab');
        <?php $bool = (is_user_logged_in() != True) ? "1" : "0"; ?>

        function nextPrev(n) {
          document.body.scrollTop = document.documentElement.scrollTop = 0;
          // This function will figure out which tab to display
          var tab = document.getElementsByClassName('tab');

          if(<?php echo $bool; ?>) {

              if (currentTab == 0) {
                  var e = document.getElementById("rcp_country");
                  var selectedCountry = e.options[e.selectedIndex].value;

                  if (selectedCountry == "NL") {
                        document.getElementsByClassName('rcp_subscription_level_9')[0].style.display = 'none';
                        document.getElementsByClassName('rcp_subscription_level_7')[0].style.display = 'block';
                        document.getElementsByClassName('rcp_subscription_level_8')[0].style.display = 'block';
                        document.getElementsByClassName('rcp_subscription_level_10')[0].style.display = 'none';
                        document.getElementById("rcp_subscription_level_7").click();
                  } else if(listAllCountries.includes(selectedCountry)) {
                      var btw_number = document.getElementById("rcp_btw_number").value;

                      if (btw_number == "") {
                        document.getElementsByClassName('rcp_subscription_level_9')[0].style.display = 'none';
                        document.getElementsByClassName('rcp_subscription_level_7')[0].style.display = 'block';
                        document.getElementsByClassName('rcp_subscription_level_8')[0].style.display = 'block';
                        document.getElementsByClassName('rcp_subscription_level_10')[0].style.display = 'none';
                          document.getElementById("rcp_subscription_level_7").click();
                      } else {
                        document.getElementsByClassName('rcp_subscription_level_9')[0].style.display = 'block';
                        document.getElementsByClassName('rcp_subscription_level_7')[0].style.display = 'none';
                        document.getElementsByClassName('rcp_subscription_level_8')[0].style.display = 'none';
                        document.getElementsByClassName('rcp_subscription_level_10')[0].style.display = 'block';
                        document.getElementById("rcp_subscription_level_9").click();
                      }
                  } else {
                        document.getElementsByClassName('rcp_subscription_level_9')[0].style.display = 'block';
                        document.getElementsByClassName('rcp_subscription_level_7')[0].style.display = 'none';
                        document.getElementsByClassName('rcp_subscription_level_8')[0].style.display = 'none';
                        document.getElementsByClassName('rcp_subscription_level_10')[0].style.display = 'block';
                         document.getElementById("rcp_subscription_level_9").click();
                  }
              }


              if(document.getElementById('rcp_user_email').value == ''
                 || document.getElementById('rcp_password').value == ''
                 || document.getElementById('rcp_password_again').value == '') {
                     alert("Fill in all the required fields.");
              } else {
                  // Hide the current tab:
                  document.getElementsByClassName('tab')[currentTab].style.display = 'none';

                  // Increase or decrease the current tab by 1:
                  currentTab += n;

                  // Display correct tab if length not exceeded
                  if (currentTab < tab.length)
                    showTab(currentTab);
                }
            } else {
            // Hide the current tab:
            document.getElementsByClassName('tab')[currentTab].style.display = 'none';

            // Increase or decrease the current tab by 1:
            currentTab += n;

            // Display correct tab if length not exceeded
            if (currentTab < tab.length)
              showTab(currentTab);
          }
        }

        function showTab(index) {
          // This function will display the specified tab of the form ...
          var tab = document.getElementsByClassName('tab');
          document.getElementsByClassName('tab')[index].style.display = 'block';

          if(index == 0) {
              document.getElementById("prevBtn").style.display = 'none';
          } else {
              document.getElementById("prevBtn").style.display = 'inline';
          }

          // Fix the next button
          if(index == (tab.length - 1)) {
              var display = "none";
          } else {
              var display = "block";
          }

          if(<?php echo $bool; ?>) {
              if (index == 2) {
                  document.getElementById("nextBtn").style.display = "none";
              } else {
                  document.getElementById("nextBtn").style.display = "block";
              }
          } else {

              if (index == 1) {
                  document.getElementById("nextBtn").style.display = "none";
              } else {
                  document.getElementById("nextBtn").style.display = "block";
              }
          }
          // $('#nextBtn').css({display:display});
        }

</script>
<?php } ?>
<?php wp_footer(); ?>

</body>
</html>

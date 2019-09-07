<?php
  include(dirname(__FILE__)."/../header/php_header.php");
?>

<head>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/assets/styles/dashboard.css" type="text/css" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Raleway:800" rel="stylesheet">

  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/functions.js" charset="utf-8" defer></script>
  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/modal.js" charset="utf-8" defer></script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="utf-8">
  <script>var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';</script>
  <script>var testing_git = true;</script>
</head>
<body>
  <!-- Facebook JS SDK moet direct na de opening body tag -->
  <script>
    window.fbAsyncInit = function() {
      FB.init({
        appId      : '277584586167398',
        cookie     : true,
        xfbml      : true,
        version    : 'v4.0'
      });

      // TODO: voor facebook analytics zorgt voor ad content blockers (kijk maar in de console)
      FB.AppEvents.logPageView();

      var path = window.location.pathname;

      if (path.includes('audit-setup') || path.includes('report-setup') || path.includes('client-dashboard')) {
        FB.getLoginStatus(function(response) {
          console.log('statusChangeCallback');

          if (response.status === 'connected') {
            $('.submitBttn').css('display', 'block');
            if (path.includes('client-dashboard')) {
              showConnectAdAccount()
            } else {
              nextPrev(1); // skip facebook step if already logged in
            }
          } else {
            $('.submitBttn').css('display', 'none');
          }
        });
      }

    };

    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "https://connect.facebook.net/en_US/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script>
    $(document).ready(function() {
      $('#nav-icon1').click(function() {
        $(this).toggleClass('open');
        $('nav').toggleClass('block');
      });
    });
  </script>

  <!-- Use this modal instead of alerts. -->
  <div id="errorModal" class="modal"></div>

  <div class="white-screen" style="display: none;">
    <div class="wrap">
      <div class="loading">
        <div class="bounceball"></div>
        <div class="text"></div>
      </div>
    </div>
  </div>

  <div id="instagramErrorModal" class="modal"></div>
  <div id="competitorModal" class="modal"></div>
  <div id="adAccountModal" class="modal"></div>

  <div class="sub-header col-lg-12">
    <a href="../dashboard/">
      <i class="fas fa-th-large"></i> &nbsp; Dashboard
    </a>
    <a href="../search-page/" class="search-icon">
      <i class="fas fa-search"></i>
    </a>
  </div>

  <section class="content">
    <div class="sidebar col-xs-12 col-sm-12 col-md-12 col-lg-3">
      <div class="person-info">
        <a href="/profile-page/#profile-avatar" class="person-image center">
          <?php
            if ( ($wp_current_user instanceof WP_User) ) {
              echo get_avatar( $user_id, $size = 32, $alt = 'Profile Picture' );
            }
          ?>
      </a>
        <div class="person-info">
          <span class="person-name block"><?php echo $wp_current_user->display_name; ?></span>
            <?php
              $user_meta = get_userdata($user_id);
              $user_roles = $user_meta->roles;
            ?>
          <span class="person-title block"> <?php echo $user_roles[0]; ?></span>
        </div>
      </div>
      <div class="stats">
        <div class="stat-block custom-height col-lg-4">
          <span class="title">Contacts</span>
          <span class="data"><?php echo $number_of_clients; ?></span>
        </div>
        <div class="stat-block custom-height col-lg-4">
          <span class="title">Audits</span>
          <span class="data"><?php echo $number_of_audits; ?></span>
        </div>
        <div class="stat-block custom-height col-lg-4">
          <span class="title">Reports</span>
          <span class="data"><?php echo $number_of_reports; ?></span>
        </div>
      </div>
      <nav>
        <ul>
          <a href="../dashboard/" class="responsive-item" ><li>Dashboard</li></a>
          <a href="../audit-dashboard/"><li><i class="fas fa-file-alt"></i></i>Audits</li></a>
          <a href="../report-dashboard/"><li><i class="fas fa-chart-line"></i>Reports</li></a>
          <a href="../client-dashboard/"><li><i class="far fa-building"></i>Contacts</li></a>
          <a href="../profile-page/"><li><i class="fas fa-cog"></i>Config</i></li></a>
          <a href="../search-page/" class="responsive-item search-icon" ><li>Search page</li></a>
          <a href="<?php echo wp_logout_url( home_url() ); ?>" title="Logout" ><li><i class="fas fa-power-off"></i>Logout</i></a>
        </ul>
      </nav>
      <!-- Animated CSS stuff -->
      <div id="nav-icon1">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>

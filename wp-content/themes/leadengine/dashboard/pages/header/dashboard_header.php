<?php
  include(dirname(__FILE__)."/../header/php_header.php");
?>

<head>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/assets/styles/dashboard.css" type="text/css" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Raleway:800" rel="stylesheet">

  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/functions.js<?php echo $cache_version; ?>" charset="utf-8" defer></script>
  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/modal.js<?php echo $cache_version; ?>" charset="utf-8" defer></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="utf-8">
  <script>var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';</script>
   <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-149815594-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-149815594-1');
  </script>
  <?php if ($_SERVER['HTTP_HOST'] == 'www.socialaudify.com' || $_SERVER['HTTP_HOST'] == 'socialaudify.com'): ?>
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-KC4JDF9');</script>
    <!-- End Google Tag Manager -->
  <?php endif; ?>
</head>
<body>
  <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KC4JDF9" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
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

  <!-- Facebook JS SDK moet direct na de opening body tag -->
  <script>
    window.fbAsyncInit = function() {
      FB.init({
        appId      : '277584586167398',
        cookie     : true,
        xfbml      : true,
        version    : 'v4.0'
      });

      FB.AppEvents.logPageView();

      var path = window.location.pathname;
      if (path.includes('client-dashboard')) {
        FB.getLoginStatus(function(response) {
          if (response.status === 'connected') {
            showConnectAdAccount();
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
  <script>
    $(document).ready(function() {
      $('#nav-icon1').click(function() {
        $(this).toggleClass('open');
        $('nav').toggleClass('block');
      });
    });
  </script>

  <div class="white-screen" style="display: none;">
    <div class="wrap">
      <div class="loading">
        <div class="bounceball"></div>
        <div class="text"></div>
      </div>
    </div>
  </div>


  <!-- Global popup modals -->
  <div id="errorModal" class="modal"></div>

  <div id="instagramErrorModal" class="modal"></div>
  <div id="competitorModal" class="modal"></div>
  <div id="adAccountModal" class="modal"></div>

  <div class="sub-header col-lg-12">
    <a href="../dashboard/" class="home-link">
      <i class="fas fa-th-large"></i> Dashboard
    </a>



    <a href="https://www.facebook.com/socialaudify/" target="_blank" rel="norefferer" style="float:right;margin-right:50px;">
     <i style="margin-right: 5px;" class="far fa-comment"></i>Questions
    </a>

    <a href="/tutorial/" target="_blank" rel="norefferer" style="float:right;margin-right:30px;">
        <i class="fab fa-youtube" style="margin-right: 5px;"></i>Tutorial
    </a>
    <a href="../search-page/" class="search-icon">
      <i style="margin-right: 5px;" class="fas fa-search"></i>Search
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
          <a href="../report-dashboard/"><li><i class="fas fa-chart-line"></i>Reports<span class="beta">Beta</span></li></a>
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

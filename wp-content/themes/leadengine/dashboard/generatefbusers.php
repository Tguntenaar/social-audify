<?php
/**
 * Template Name: SharingFBButton
 */
?>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Social Audify</title>
  <meta property="og:site_name" content="Social Audify"/>
  <meta property="og:title" content="Facebook login page" />
  <meta property="og:description" content="Login zodat we users krijgen" />
  <meta property="og:type" content="website" />
  <script>
    window.fbAsyncInit = function() {
      FB.init({
        appId      : '277584586167398',
        cookie     : true,
        xfbml      : true,
        version    : 'v3.3'
      });
    };

    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = "https://connect.facebook.net/en_US/sdk.js";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>
</head>
<body>
  <h1>Bedankt voor het helpen!</h1>
  <div class="fb-login-button" data-scope="manage_pages,instagram_basic,instagram_manage_insights,ads_read" auth_type="rerequest" data-width="100" data-max-rows="1" data-size="large" data-button-type="continue_with" data-show-faces="false" data-auto-logout-link="true" data-use-continue-as="false" onlogin="checkLoginState();"></div>
</body>
</html>
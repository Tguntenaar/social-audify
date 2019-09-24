<?php
/**
 * Template Name: custom Affiliate Area
 */
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php
  // Header
  include(dirname(__FILE__)."/../header/dashboard_header.php");
?>

<head>
  <meta charset="utf-8">
  <title>Affiliate Area</title>
</head>
  <body>
    <?php
      echo do_shortcode("[affiliate_area]");
    ?>
  </body>
</html>
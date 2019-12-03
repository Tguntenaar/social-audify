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
    <div class="content-right y-scroll col-lg-9" style="padding-left: 25px;">
      <div class="activities col-lg-12">
        <div id="profile-member">
          <h3 class="h3-fix">Affiliate Area</h3>
          <?php echo do_shortcode("[affiliate_area]"); ?>
          <!-- TODO: itjes -->
          <!-- <div class="profile-exp">
            <i id="profile-exp" class="information fas fa-info"></i>
          </div> -->
        </div>
      </div>
    </div>
  </body>
</html>
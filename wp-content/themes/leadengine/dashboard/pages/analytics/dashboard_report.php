<?php
  include(dirname(__FILE__)."/../header/php_header.php");

  /**
   * Template Name: Analytics dashboard Report
   */
?>
<html>
<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/assets/styles/dashboard.css" type="text/css" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Raleway:800" rel="stylesheet">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/functions.js<?php echo $cache_version; ?>" charset="utf-8" defer></script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="utf-8">
</head>
<body>

<div class="content-right y-scroll" style="height:100vh;">
  <div class="overview-audit-report col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 right" style="margin-top:0px; margin-bottom:40px;"><?php
      $users = $user_control->get_all(); 
      $all_audits = array();
      $all_reports = array(); ?>
      <div>
        <?php 
        // TODO: maybe not a button the makkelijk om het perongeluk aan te klikken
        // if (isset($_POST['newupdate'])) {
        //   // echo "<script>alert(1);</script>";
        //   $user_control->update_all("new_update", 1,"User");
        // }?>
        <!-- <form action="" method="POST">
          <input type="hidden" name="newupdate" value="1">
          <input type="submit" style="width:10%" value="Show New Update">
        </form> -->
      </div>
      <div class="inner no-scroll client-dashboard">
        <span class="title"><span class="title-background" style="width:200px">User Overview</span>
          <span class="count" id="counterSpanUser"><?php echo sizeof($users); ?></span>
        </span>
        <input type="text" id="search-input-user" placeholder="Search..."/>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title" >
          <div class="col-12 col-sm-3 col-md-3 col-lg-2 row-title-style" style="padding:0;">Id | Name</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Email</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Instagram Business Account</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0; text-align:center;">Clients / Audits / Reports</div>
        </div>
        <div class="inner-scroll client-dashboard" id="user-results"><?php
          foreach ($users as $user) {
            $clients = $client_control->get_all(NULL, $user->id); 
            $audits = $audit_control->get_all(NULL, $user->id); 
            $reports = $report_control->get_all(NULL, $user->id); 
            
            $all_audits = array_merge($all_audits, $audits);
            $all_reports = array_merge($all_reports, $reports); ?>

            <a class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" name="<?php echo $user->id." ".$user->name; ?>">
              <div style="overflow:hidden" class="col-12 col-sm-2 col-md-2 col-lg-2 audit-row-style">
                <?php echo "$user->id | $user->name"; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style">
                <?php echo $user->email; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style">
                <?php echo $user->instagram_business_account_id; ?></div>
              <div style="overflow:hidden; text-align:center;" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style">
                <?php echo count($clients)." / ".count($audits)." / ".count($reports); ?></div>
            </a><?php
          } ?>
        </div>
      </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 right" style="margin-top:0px; margin-bottom:40px;">
      <div class="inner no-scroll client-dashboard" style="height:440px">
        <span class="title"><span class="title-background" style="width:200px">Audit Overview</span>
          <span class="count" id="counterSpanAudit"><?php echo sizeof($all_audits); ?></span>
        </span>
        <input type="text" id="search-input-audit" placeholder="Search..."/>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title" >
          <div class="col-12 col-sm-3 col-md-3 col-lg-2 row-title-style" style="padding:0;">User</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Audit Name</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">View Date</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Create Date</div>
        </div>
        <div class="inner-scroll client-dashboard" id="audit-results"><?php
          $today_count_audit = 0;
          foreach ($all_audits as $audit) {
            $user = $user_control->get((int)$client_control->get($audit->client_id)->user_id);
            $today_count_audit += (date('Ymd') == date('Ymd', strtotime($audit->create_date)));
            $slug = make_slug('audit', $audit->name, $audit->id);

            $viewed = "not viewed";
            if ($audit->view_time != NULL) {
              $viewed = $audit->view_time;
              $view_count_audit++;
            } ?>

            <a href="<?php echo $slug; ?>" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" name="<?php echo $audit->name; ?>">
              <div style="overflow:hidden" class="col-12 col-sm-2 col-md-2 col-lg-2 audit-row-style">
                <?php echo "$user->id | $user->name"; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style">
                <?php echo $audit->name; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style">
                <?php echo $viewed; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style">
                <?php echo $audit->create_date; ?></div>
            </a><?php
          } ?>
        </div>
        <span class="analytics-stats" style="margin-top:10px">
          Viewed audits : <strong><?php echo $view_count_audit." / ".sizeof($all_audits); ?></strong></span>
        <span class="analytics-stats" style="margin-top:10px">
          Audits created today : <strong><?php echo $today_count_audit; ?></strong></span>
      </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 right" style="margin-top:0px; margin-bottom:40px;">
      <div class="inner no-scroll client-dashboard" style="height:440px">
        <span class="title"><span class="title-background" style="width:200px">Report Overview</span>
          <span class="count" id="counterSpanReport"><?php echo sizeof($all_reports); ?></span>
        </span>
        <input type="text" id="search-input-report" placeholder="Search..."/>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title" >
          <div class="col-12 col-sm-3 col-md-3 col-lg-2 row-title-style" style="padding:0;">User</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Report Name</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">View Date</div>
          <div class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 row-title-style" style="padding:0;">Create Date</div>
        </div>
        <div class="inner-scroll client-dashboard" id="report-results"><?php
          $today_count_report = 0;
          foreach ($all_reports as $report) {
            $user = $user_control->get((int)$client_control->get($report->client_id)->user_id);
            $today_count_report += (date('Ymd') == date('Ymd', strtotime($report->create_date)));
            $slug = make_slug('report', $report->name, $report->id);

            $viewed = "not viewed";
            if ($report->view_time != NULL) {
              $viewed = $report->view_time;
              $view_count_report++;
            } ?>

            <a href="<?php echo $slug; ?>" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" name="<?php echo $report->name; ?>">
              <div style="overflow:hidden" class="col-12 col-sm-2 col-md-2 col-lg-2 audit-row-style">
                <?php echo "$user->id | $user->name"; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style">
                <?php echo $report->name; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style">
                <?php echo $viewed; ?></div>
              <div style="overflow:hidden" class="col remove-on-mobile col-sm-3 col-md-3 col-lg-3 audit-row-style">
                <?php echo $report->create_date; ?></div>
            </a><?php
          } ?>
        </div>
        <span class="analytics-stats" style="margin-top:10px">
          Viewed reports : <strong><?php echo $view_count_report." / ".sizeof($all_reports); ?></strong></span>
        <span class="analytics-stats" style="margin-top:10px">
          Reports created today : <strong><?php echo $today_count_report; ?></strong></span>
      </div>
    </div>
  </div>
</div>

<script charset="utf-8">
  // Search lists
  var counterSpanUser = $("#counterSpanUser");
  var elems_users = $("#user-results .audit-row");
  $(document).on('keyup', 'input#search-input-user', function() {
    filterSearch($(this).val(), elems_users, counterSpanUser);
  });

  var counterSpanAudit = $("#counterSpanAudit");
  var elems_audits = $("#audit-results .audit-row");
  $(document).on('keyup', 'input#search-input-audit', function() {
    filterSearch($(this).val(), elems_audits, counterSpanAudit);
  });

  var counterSpanReport = $("#counterSpanReport");
  var elems_reports = $("#report-results .audit-row");
  $(document).on('keyup', 'input#search-input-report', function() {
    filterSearch($(this).val(), elems_reports, counterSpanReport);
  });

  // call in console
  function btw() {
    var u = [<?php 
    foreach($users as $user) {
      echo "\n[".$user->id.",'".get_user_meta($user->id, 'rcp_btw_number', true)."'],";
    }?>];
    // var u = [{id:"68",number:""}, {}, {},];
    exportcsv("User ID,BTW", u);
  }

  // list of objects
  function exportcsv(columns, list) {
    let csvContent = columns + "\n" + list.map(e => e.join(",")).join("\n");

    var encodedUri = 'data:text/csv;charset=utf-8,%EF%BB%BF' + encodeURIComponent(csvContent);
    link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', "btw_nummers.csv");
    link.click();
  }
</script>

</body>
</html>

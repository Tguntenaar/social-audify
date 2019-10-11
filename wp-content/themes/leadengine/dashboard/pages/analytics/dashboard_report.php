<?php
  include(dirname(__FILE__)."/../header/php_header.php");

  /**
   * Template Name: Analytics dashboard Report
   */
?>
<html>
<head>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/assets/styles/dashboard.css" type="text/css" />
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css?family=Raleway:800" rel="stylesheet">

  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/functions.js" charset="utf-8" defer></script>
  <script src="<?php echo get_template_directory_uri(); ?>/dashboard/assets/scripts/modal.js" charset="utf-8" defer></script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="utf-8">
  <script>var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';</script>
  <script>var testing_git = 'thomas git test';</script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>

<div class="a-container col-lg-12">
  <h2>User overview</h2>
  <div class="stat-container-a col-lg-12"> <?php
    $audits = $audit_control->get_all_audits();
    $count = sizeof($audits);
    $view_count = 0;
    $today_count = 0;

    foreach($audits as $audit) {
      $user = $user_control->get((int)$client_control->get($report->client_id)->user_id)->name;
      if (date('Ymd') == date('Ymd', strtotime($audit->create_date))) {
        $audit_today_count++;
      }

      if ($audit->view_time == NULL) {
        $viewed = "not viewed";
      } else {
        $viewed = "viewed";
        $view_count++;
      }

      $slug = strtolower('audit-'.$audit->name.'-'.$audit->id.'/'); ?>
      <a href="<?php echo $slug; ?>" class="a-row col-xs-12 col-ms-12 col-md-12 col-lg-12">
        <div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-2'><?php echo $user; ?></div>
        <div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-2'><?php echo $audit->name; ?></div>
        <div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-2'><?php echo $viewed; ?></div>
        <div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-3'><?php echo $audit->create_date; ?></div>
      </a><?php
    } ?>
  </div>

  <span class="analytics-stats">
    Viewed audits: <strong><?php echo $view_count; ?></strong> / <?php echo $audit_count; ?>
  </span>

  <span class="analytics-stats">
    Audits created today: <strong><?php echo $today_count; ?></strong>
  </span>
</div>

<div class="a-container col-lg-12">
  <h2>Report overview</h2>
  <div class="stat-container-a col-lg-12"> <?php
    $reports = $report_control->get_all_reports();
    $count = sizeof($reports);
    $view_count = 0;
    $today_count = 0;

    foreach($reports as $report) {
      $user = $user_control->get((int)$client_control->get($report->client_id)->user_id)->name;
      if (date('Ymd') == date('Ymd', strtotime($report->create_date))) {
        $today_count++;
      }

      if($report->view_time == NULL) {
        $viewed = "not viewed";
      } else {
        $viewed = "viewed";
        $view_count++;
      }

      $slug = strtolower('report-'.$report->name.'-'.$report->id.'/'); ?>
      <a href="<?php echo $slug; ?>" class="a-row col-xs-12 col-ms-12 col-md-12 col-lg-12">
        <div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-2'><?php echo $user; ?></div>
        <div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-2'><?php echo $report->name; ?></div>
        <div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-2'><?php echo $viewed; ?></div>
        <div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-3'><?php echo $report->create_date; ?></div>
      </a><?php
    } ?>
  </div>

  <span class="analytics-stats">
    Viewed reports: <strong><?php echo $view_count; ?></strong> / <?php echo $count; ?>
  </span>

  <span class="analytics-stats">
    Reports created today: <strong><?php echo $today_count; ?></strong>
  </span>
</div>

</body>
</html>

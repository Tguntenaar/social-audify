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
    <h2>Report overview</h2>
    <div class="stat-container col-lg-12">
        <?php
            $reports = $report_control->get_all_reports();
            $report_count = sizeof($reports);
            $report_view_count = 0;
            $report_today_count = 0;

            foreach($reports as $report) {
                $user = $user_control->get((int)$client_control->get($report->client_id)->user_id)->name;
                $earlier = new DateTime($report->create_date);
                $later = new DateTime(date('Y-m-d H:i:s'));
                $day_difference = $later->diff($earlier)->format("%a");

                if($day_difference == 0) {
                    $report_today_count++;
                }

                if($report->view_time == NULL) {
                    $viewed = "not viewed";
                } else {
                    $viewed = "viewed";
                    $report_view_count++;
                }


                $slug = strtolower('/audit-'.$report->name.'-'.$report->id.'/');

                ?><a href="<?php echo $slug; ?>" class="a-row col-xs-12 col-ms-12 col-md-12 col-lg-12"><?php
                    ?><div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-2'><?php echo $user; ?></div><?php
                    ?><div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-2'><?php echo $report->name; ?></div><?php
                    ?><div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-2'><?php echo $viewed; ?></div><?php
                    ?><div class='a-col col-xs-2 col-ms-2 col-md-2 col-lg-3'><?php echo $report->create_date; ?></div><?php
                ?></a><?php

            }
        ?>
    </div>
    <span class="analytics-stats">
        Viewed reports: <strong><?php echo $report_view_count; ?></strong> / <?php echo $report_count; ?>
    </span>

    <span class="analytics-stats">
        Reports created today: <strong><?php echo $report_today_count; ?></strong>
    </span>

</div>

</body>
</html>

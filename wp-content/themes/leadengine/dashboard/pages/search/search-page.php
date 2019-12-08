<?php
/**
 * Template Name: Search page
 */
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <title>SA Dashboard</title>
</head>
  <?php
    // Header

    // TODO : style van de spanCounts wordt nog gedefinieerd in de span ipv css.

    include(dirname(__FILE__)."/../header/dashboard_header.php");

    $reports = $report_control->get_all();
    $audits = $audit_control->get_all();

    function get_time_dif_days($date) {
      $interval = date_diff(date_create($date), date_create(date('Y-m-d H:i:s')));
      $days = $interval->format('%a');
      return $days < 1 ? "today" : ($days < 2 ? "yesterday" : $interval->format('%a days ago'));
    }
  ?>

  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9">
    <div class="search-container">
      <input type="text" name="search" id="search-input" placeholder="Type audit/report name here...">
    </div>
    <div class="result-container">
      <div class="search-results">
        <span class="title"><span class="title-background">Audits</span>
          <span class="count" id="counterSpanAudit" style="float:right; color:#000;"><?php echo $number_of_audits; ?></span>
        </span>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 row-title">
          <div class="row-title-style remove-on-mobile col col-sm-5 col-md-5 col-lg-5">Client</div>
          <div class="row-title-style col-12 col-sm-5 col-md-5 col-lg-5">Audit Name</div>
          <div class="row-title-style remove-on-mobile col col-sm-2 col-md-2 col-lg-2">Viewed</div>
        </div>
        <div class="overflow-y">
          <div class="result-box" id="audit-results">
            <?php
              foreach ($audits as $audit) {
                $slug = strtolower("/audit-".str_replace(' ', '-', $audit->name).'-'.$audit->id."/");
                $audit->viewed = $audit->view_time !== NULL ? get_time_dif_days($audit->view_time) : "not yet";
                echo '<a href="'.$slug.'" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" name="'.$audit->name.'">
                  <div class="col remove-on-mobile col-sm-5 col-md-5 col-lg-5 audit-row-style" style="padding-left: 0;">'.$audit->client_name.'</div>
                  <div class="col-12 col-sm-5 col-md-5 col-lg-5 audit-row-style" style="padding-left: 0;">'.$audit->name.'</div>
                  <div class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 audit-row-style" style="padding-left: 0;">'.$audit->viewed.'</div>
                </a>';
              }
            ?>
          </div>
        </div>
      </div>
      <div class="search-results" style="margin-bottom: 0;">
        <span class="title"><span class="title-background">Reports</span>
          <span class="count" id="counterSpanReport" style="float:right; color:#000;"><?php echo $number_of_reports; ?></span>
        </span>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 row-title">
          <div class="row-title-style remove-on-mobile col col-sm-5 col-md-5 col-lg-5">Client</div>
          <div class="row-title-style col-12 col-sm-5 col-md-5 col-lg-5">Report Name</div>
          <div class="row-title-style remove-on-mobile col col-sm-2 col-md-2 col-lg-2">Viewed</div>
        </div>
        <div class="overflow-y">
          <div class="result-box" id="report-results">
            <?php
              foreach ($reports as $report) {
  							$slug = strtolower('/report-'.str_replace(' ', '-', $report->name).'-'.$report->id.'/');
                $report->viewed = $report->view_time !== NULL ? get_time_dif_days($report->view_time) : "not yet";
                echo ' <a href="'.$slug.'" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" name="'. $report->name .'">
                  <div class="col remove-on-mobile col-sm-5 col-md-5 col-lg-5  audit-row-style">'. $report->client_name .'</div>
                  <div class="col-12 col-sm-5 col-md-5 col-lg-5  audit-row-style">'. $report->name .'</div>
                  <div class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 audit-row-style fontsize">'. $report->viewed .'</div>
                </a>';
              }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </section>
</body>

<script charset="utf-8">
  // Search lists
  var elems_audits = $("#audit-results .audit-row");
  var elems_reports = $("#report-results .audit-row");

  var counterSpanAudit = $("#counterSpanAudit");
  var counterSpanReport = $("#counterSpanReport");

  $(document).on('keyup', 'input#search-input', function() {
    filterSearch($(this).val(), elems_audits, counterSpanAudit);
    filterSearch($(this).val(), elems_reports, counterSpanReport);
  });
</script>
</html>
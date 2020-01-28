<?php
/**
 * Template Name: Report dashboard
 */
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <title>Report Dashboard</title>
  <script src="//code.tidio.co/shn7vki15l32gvyiv33o2bmemv7ckkc2.js" async></script>
</head>
  <?php
    // Header
    require_once(dirname(__FILE__)."/../header/dashboard_header.php");

    // Get reports from external DB
    $this_month = $report_control->get_all(0);
    $this_year = $report_control->get_all(12);

    // Counts the reports
    $monthly_values = calculate_monthly_amount($this_year);
    $daily_values   = calculate_daily_amount($this_month);
    $alter_percent_report = $monthly_values[11] > 0 ? "100%" : "-";

    // Functions for caculating percentages above chart.
    $average_previous_months = (array_sum($monthly_values) - $monthly_values[11]) / 11;
    $yearly_increase = percent_diff($monthly_values[11], $average_previous_months);
    $last_month_increase = percent_diff($monthly_values[11], $monthly_values[10]);

    function get_time_dif_days($date) {
      $interval = date_diff(date_create($date), date_create(date('Y-m-d H:i:s')));
      $days = $interval->format('%a');
      return $days < 1 ? "today" : ($days < 2 ? "yesterday" : $interval->format('%a days ago'));
    }
  ?>
  <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9">
    <div class="overview-audit-report col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 screen-height">
        <div class="center-center">
          <h1 class="create-report-h1">Create a report in a few steps.</h1>
          <a class="create-audit-button" href="/report-setup/">Create Report</a>
        </div>
      </div>
      <div class="graph-box no-border col-xs-12 col-sm-12 col-md-12 col-lg-6">
        <span class="stat-box-title">% increase in number of reports this month</span>
        <span class="graph-procent"><?php echo percent_print($yearly_increase, $alter_percent_report); ?></span>
        <span class="graph-info">
          <?php echo percent_print($last_month_increase, $alter_percent_report); ?> compared to last month<br />
          (<?php echo $monthly_values[11]." Reports in ".date("F Y"); ?>)
        </span>
        <canvas id="chart-report"></canvas>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 right float-right report-input">
        <div class="inner no-scroll">
          <span class="title"><span class="title-background">Reports</span>
            <span class="count" id="counterSpan"><?php echo $number_of_reports; ?></span>
            <span class="selectDelete" style="color:black; display:none"><i class="fas fa-trash"></i></span>
          </span>
          <input type="text" name="search" id="search-input" placeholder="Search..."/>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title">
            <div class="row-title-style col-12 col-sm-5 col-md-5 col-lg-5" style="padding-left: 0;">Report Name</div>
            <div class="row-title-style remove-on-mobile col col-sm-5 col-md-5 col-lg-5" style="padding-left: 0;">Client</div>
            <div class="row-title-style remove-on-mobile col col-sm-2 col-md-2 col-lg-2" style="padding-left: 0;">Viewed</div>
          </div>
          <div class="inner-scroll" id="report-results"><?php
            foreach ($this_year as $report) {
              $slug = strtolower('/report-'.str_replace(" ", "-", $report->name).'-'.$report->id.'/');
              $report->viewed = $report->view_time !== NULL ? get_time_dif_days($report->view_time) : "not yet"; ?>

             <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" data-id="<?php echo $report->id; ?>" data-post="<?php echo $report->post_id; ?>" name="<?php echo $report->name; ?>">
                <div class="col-12 col-sm-5 col-md-5 col-lg-5 audit-row-style"><a href="<?php echo $slug; ?>"><?php echo $report->name; ?></a></div>
                <div class="col remove-on-mobile col-sm-5 col-md-5 col-lg-5 audit-row-style"><?php echo $report->client_name; ?></div>
                <div class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 audit-row-style"><?php echo $report->viewed ?></div>
              </div><?php
            } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </section>

	<script charset='utf-8'>
    $(function() {
      generateChart('chart-report', [<?php echo json_encode($daily_values); ?>]);

      var elems = $("#report-results .audit-row");
      var selectedList = [];
      var postIds = [];

      elems.on('click', function() {
        const results = toggleSelected($(this), selectedList, $(".selectDelete"), postIds);
        selectedList = results.selectedList;
        postIds = results.postIds;
      });

      $(".selectDelete").click(function() {
        showModal(initiateModal('confirmModal', 'confirm', {
          'text': `Delete Reports`,
          'subtext': `Would you like to delete the selected Report${(selectedList.length == 1 ? '' : 's')}?`,
          'confirm': 'delete_confirmed'
        }));

        $("#delete_confirmed").click(function() {
          showBounceBall(true, 'Deleting Reports...');
          $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: 'delete_multiple', ids: selectedList, posts: postIds, type: 'report'},
            success: function(response) { location.reload(); },
            error: function (xhr, textStatus, errorThrown) {
              var send_error = error_func(xhr, textStatus, errorThrown, selectedList);
              logError(send_error, 'setups/delete_reports.php', 'submit');
              location.reload();
            }
          });
        });
      });

      // Search list
      var counterSpan = $("#counterSpan");
      $(document).on('keyup', 'input#search-input', function() {
        filterSearch($(this).val(), elems, counterSpan);
      });
		});
	</script>
</body>
</html>

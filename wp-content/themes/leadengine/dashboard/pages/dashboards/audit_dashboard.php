<?php
/**
 * Template Name: Audit Dashboard
 */
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <title>Audit Dashboard</title>
</head>
  <?php
    // Header
    include(dirname(__FILE__)."/../header/dashboard_header.php");

    // Get audits from external DB
    $this_month = $audit_control->get_all(0);
    $this_year = $audit_control->get_all(12);

    // Counts the audits
    $monthly_values = calculate_monthly_amount($this_year);
    $daily_values   = calculate_daily_amount($this_month);
    $alter_percent_audit = $monthly_values[11] > 0 ? "100%" : "-";

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
          <h1 class="create-report-h1">Create an audit in a few steps.</h1>
          <a class="create-audit-button" href="/audit-setup/">Create Audit</a>
        </div>
      </div>
      <div class="graph-box no-border col-xs-12 col-sm-12 col-md-12 col-lg-6">
        <span class="stat-box-title">% increase in number of audits this month</span>
        <span class="graph-procent"><?php echo percent_print($yearly_increase, $alter_percent_audit); ?></span>
        <span class="graph-info">
          <?php echo percent_print($last_month_increase, $alter_percent_audit); ?> compared to last month<br />
          (<?php echo $monthly_values[11]." Audits in ".date("F Y"); ?>)
        </span>
        <canvas id="chart-audit"></canvas>
      </div>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6 right float-right audit-input">
        <div class="inner no-scroll">
          <span class="title"><span class="title-background">Audits</span>
            <span class="count" id="counterSpan"><?php echo $number_of_audits; ?></span>
            <span class="selectDelete" style="color:black; display:none"><i class="fas fa-trash"></i></span>
          </span>
          <input type="text" name="search" id="search-input" placeholder="Search..."/>
          <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 row-title">
            <div class="row-title-style col-12 col-sm-5 col-md-5 col-lg-5" style="padding-left: 0;">Audit Name</div>
            <div class="row-title-style remove-on-mobile col col-sm-5 col-md-5 col-lg-5" style="padding-left: 0;">Client</div>
            <div class="row-title-style remove-on-mobile col col-sm-2 col-md-2 col-lg-2" style="padding-left: 0;">Viewed</div>
          </div>
          <div class="inner-scroll" id="audit-results"><?php
            foreach ($this_year as $audit) {
              $slug = strtolower('/audit-'.str_replace(" ", "-", $audit->name).'-'.$audit->id.'/');
              $audit->viewed = $audit->view_time !== NULL ? get_time_dif_days($audit->view_time) : "not yet"; ?>

              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row" data-id="<?php echo $audit->id; ?>" data-post="<?php echo $audit->post_id; ?>" data-name="<?php echo $audit->name; ?>">
                <div class="col-12 col-sm-5 col-md-5 col-lg-5 audit-row-style"><a href="<?php echo $slug; ?>"><?php echo $audit->name; ?></a></div>
                <div class="col remove-on-mobile col-sm-5 col-md-5 col-lg-5 audit-row-style"><?php echo $audit->client_name; ?></div>
                <div class="col remove-on-mobile col-sm-2 col-md-2 col-lg-2 audit-row-style"><?php echo $audit->viewed ?></div>
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
      generateChart('chart-audit', [<?php echo json_encode($daily_values); ?>]);

      var elems = $('#audit-results .audit-row');
      var selectedList = [];
      var postIds = [];

      elems.on('click', function() {
        const results = toggleSelected($(this), selectedList, $(".selectDelete"), postIds);
        selectedList = results.selectedList;
        postIds = results.postIds;
      });

      $(".selectDelete").click(function() {
        showModal(initiateModal('confirmModal', 'confirm', {
          'text': `Delete Audits`,
          'subtext': `Would you like to delete the selected Audit${(selectedList.length == 1 ? '' : 's')}?`,
          'confirm': 'delete_confirmed'
        }));

        $("#delete_confirmed").click(function() {
          showBounceBall(true, 'Deleting Audits...');
          $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: 'delete_multiple', ids: selectedList, posts: postIds, type: 'audit'},
            success: function(response) { 
              location.reload(); 
              },
            error: function (xhr, textStatus, errorThrown) {
              var send_error = error_func(xhr, textStatus, errorThrown, selectedList);
              logError(send_error, 'setups/delete_audits.php', 'submit');
              location.reload();
            }
          });
        });
      });

      // Search list
      var counterSpan = $('#counterSpan');
      $(document).on('keyup', 'input#search-input', function() {
        filterSearch($(this).val(), elems, counterSpan, true);
      });
		});
	</script>
</body>
</html>

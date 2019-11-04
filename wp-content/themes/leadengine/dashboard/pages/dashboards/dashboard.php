<?php
/**
 * Template Name: Dashboard new
 */
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <title>SA Dashboard</title>
</head>
  <?php
    // Header
    include(dirname(__FILE__)."/../header/dashboard_header.php");
   /* $user_id $current_user set in the dashboard_header */

    // Weergave intro - Eerste login
    $intro_bit = 0;

    // Dit moet ergens in de code waar een user word aangemaakt in de wpdb.
    if($user_control->get($user_id) == NULL) {
        $user_control->create($user_id, $current_user->user_login, $current_user->user_email);
        $intro_bit = 1;
    }

    // Audit variables
    $month_audits = $audit_control->get_all(0);
    $year_audits = $audit_control->get_all(12);

    // Counter for audits
    $audit_values = calculate_monthly_amount($year_audits);
    $audit_daily_values = calculate_daily_amount($month_audits);

    // Check how many audits are viewed
    $viewed_audit = $viewed_audit_month = 0;
    $viewed_audits = array();

    foreach ($year_audits as $audit) {
      if ($audit->view_time !== NULL) {
        array_push($viewed_audits, array(
          "name" => $audit->name, 
          "id"   => $audit->id,
          "slug" => make_slug("audit", $audit->name, $audit->id)
        ));
      }
    }
    $viewed_audit = count($viewed_audits);

    foreach ($month_audits as $audit) {
      $viewed_audit_month += $audit->view_time !== NULL ? 1 : 0;
    }

    $alter_percent_audit = $audit_values[11] > 0 ? "100%" : "-";

    // Functions for caculating audit percentages.
    $average_prev_months = (array_sum($audit_values) - $audit_values[11]) / 11;

    $yearly_increase_audit = percent_diff($audit_values[11], $average_prev_months);
    $month_increase_audit = percent_diff($audit_values[11], $audit_values[10]);

    $open_rate_audit = percent_diff($viewed_audit, count($year_audits));
    $prev_open_rate_audit = percent_diff($viewed_audit - $viewed_audit_month, count($year_audits) - $audit_values[11]);


    // Report variables
    $month_reports = $report_control->get_all(0);
    $year_reports = $report_control->get_all(12);

    $report_values = calculate_monthly_amount($year_reports);
    $report_daily_values = calculate_daily_amount($month_reports);

    $viewed_report = $viewed_report_month = 0;
    foreach ($year_reports as $report) {
      $viewed_report += $report->view_time !== NULL ? 1 : 0;
    }

    foreach ($month_reports as $report) {
      $viewed_report_month += $report->view_time !== NULL ? 1 : 0;
    }

    $alter_percent_report = $report_values[11] > 0 ? "100%" : "-";

    // Functions for caculating audit percentages.
    $average_prev_months = (array_sum($report_values) - $report_values[11]) / 11;

    $yearly_increase_report = percent_diff($report_values[11], $average_prev_months);
    $month_increase_report = percent_diff($report_values[11], $report_values[10]);

    $open_rate_report = percent_diff($viewed_report, count($year_reports));
    $prev_open_rate_report = percent_diff($viewed_report - $viewed_report_month, count($year_reports) - $report_values[11]);

    // Graph variable
    $graph_values = [$audit_daily_values, $report_daily_values];

  ?>

  <!-- $intro_bit -->
    <?php if ($intro_bit) { ?>

      <div class="intro-overlay">
        <div class="vertical-align" style="width: 100%; margin: 0 auto; height: auto;">
          <h1>Welcome to Social Audify!</h1>
          <!-- <p>
          The tool for generating more sales and automating your reporting process!
          In order to make your audits and reports as efficient as possible, we advise you to take a few minutes to configure your account to make sure you get the most out of this software.
          Within the settings you can configure your automatic emails / follow ups you want us to send, you can add your phone number so your leads can call you after watching your audit, and you can add a little avatar to add to every audit and other features.
          </p> -->
          <iframe style="display: block; margin: 0 auto; margin-bottom: 20px;" width="560" height="315" src="https://www.youtube.com/embed/O_JeCXnd3k0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
          <a href="/tutorial/#1566310210069-10357311-75eb" target="_blank" rel="norefferer" class="advice-button" style="background: #27ae60; padding: 12px 20px; font-weight: 100; margin: 0 25px;">Full tutorial<span style="position: relative; left: 5px; font-size: 9px;">(Recommended)</span></a>
          <div style="clear: both; height: 20px;"></div>
          <a href="/profile-page" class="advice-button" style="padding: 12px 20px; font-weight: 100; margin-top: 45px;">Configure profile</a>
        </div>
      </div>
      <script>
        FB.AppEvents.logEvent(FB.AppEvents.EventNames.COMPLETED_REGISTRATION);
      </script>
    <?php } ?>

    <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-9" style="margin-top: 0;">
      <h4 style="padding-left: 15px;">Status recent sent Reports and Audits</h4>
      <hr style="margin-left: 15px;" class="under-line" />
        <div class="overflow-x">
        <div class="acitivities">
          <div class="recent-send"><?php
            // Recently send Audits and Reports
            $recent_items = $connection->get_all_recent($user_id, 15);
            foreach ($recent_items as $item) {
              $slug = make_slug($item->type, $item->name, $item->id);
              $name = strlen($item->name) <= 15 ? $item->name : substr($item->name, 0, 15).'...'; ?>

              <a href="<?php echo $slug; ?>" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 audit-row">
                <div class="recent-send-box">
                  <i class='fas fa-envelope<?php echo $item->view_time === NULL ? "" : "-open"; ?>'
                    style='color: <?php echo $item->type == 'audit' ? '#16a085' : '#2980b9'; ?>'></i>
                  <span class="report-audit-name"><?php echo $name; ?></span><br/>
                  <span class="report-audit-name-client"><?php echo $item->client_name; ?></span>
                </div>
              </a><?php
            } ?>
          </div>
        </div>
      </div>
        <div class="report-dash-stat col-xs-12 col-ms-12 col-ld-12 col-lg-6">
          <h4>Statistics for <?php echo date('F Y'); ?></h4>
          <hr class="under-line" />
          <div style="clear:both"></div>

          <!-- AUDIT STATS -->
          <div class="stat-box" style="border-top: 2px solid #16a085;">
            <span class="stat-box-title">Audits sent</span>
            <span class="stat-box-data"> <?php echo count($year_audits); ?> </span>
            <span class="stat-box-procent "> <?php echo percent_print($month_increase_audit); ?> </span>
          </div>
          <div class="stat-box" style="border-top: 2px solid #16a085;">
            <span class="stat-box-title">Audits opened</span>
            <span class="stat-box-data"> <?php echo $viewed_audit; ?> </span>
            <span class="stat-box-procent ">
              <?php echo percent_diff($viewed_audit_month, $viewed_audit - $viewed_audit_month, true); ?>
            </span>
          </div>
          <div class="stat-box" onclick="showOpenedAudits()" style="border-top: 2px solid #16a085;">
            <span class="stat-box-title">Open rate</span>
            <span class="stat-box-data"><?php echo percent_print($open_rate_audit); ?></span>
            <span class="stat-box-procent ">
              <?php echo percent_diff($prev_open_rate_audit, $open_rate_audit, true); ?>
            </span>
          </div>

          <!-- REPORT STATS -->
          <div class="stat-box" style="border-top: 2px solid #2980b9;">
            <span class="stat-box-title">Reports sent</span>
            <span class="stat-box-data"> <?php echo count($year_reports); ?> </span>
            <span class="stat-box-procent "><?php echo percent_print($month_increase_report); ?> </span>
          </div>
          <div class="stat-box" style="border-top: 2px solid #2980b9;">
            <span class="stat-box-title">Reports opened</span>
            <span class="stat-box-data"> <?php echo $viewed_report; ?> </span>
            <span class="stat-box-procent ">
              <?php echo percent_diff($viewed_report_month, $viewed_report - $viewed_report_month, true); ?>
            </span>
          </div>
          <div class="stat-box" style="border-top: 2px solid #2980b9;">
            <span class="stat-box-title">Open rate</span>
            <span class="stat-box-data" ><?php echo percent_print($open_rate_report); ?></span>
            <span class="stat-box-procent ">
              <?php echo percent_diff($prev_open_rate_report, $open_rate_report, true); ?>
            </span>
          </div>
        </div>

        <!-- COMPARISON WITH LAST MONTH -->
        <div class="report-compared-last-month col-xs-12 col-sm-12 col-md-12 col-lg-6">
          <h4>Compared to previous Months</h4>
          <hr class="under-line" />
          <div class="graph-box" style="border-top: 2px solid #16a085;">
            <span class="stat-box-title">% increase in number of audits this month</span>
            <span class="graph-procent"><?php echo percent_print($yearly_increase_audit, $alter_percent_audit); ?></span>
            <span class="graph-info">
              <?php echo percent_print($month_increase_audit, $alter_percent_audit); ?> compared to last month<br />
              (<?php echo $audit_values[11]." Audits in ".date("F Y"); ?>)
            </span>
            <canvas id="chart-audit"></canvas>
          </div>
          <div class="graph-box" style="border-top: 2px solid #2980b9;">
            <span class="stat-box-title">% increase in number of reports this month</span>
            <span class="graph-procent"><?php echo percent_print($yearly_increase_report, $alter_percent_report); ?></span>
            <span class="graph-info">
              <?php echo percent_print($month_increase_report, $alter_percent_report); ?> compared to last month<br />
              (<?php echo $report_values[11]." Reports in ".date("F Y"); ?>)
            </span>
            <canvas id="chart-report"></canvas>
          </div>
        </div>
    </div>
  </section>

	<script charset="utf-8">

    function showOpenedAudits() {
      var openedAudits = <?php echo json_encode($viewed_audits); ?>;
      var list = openedAudits.map( audit => `<a href="${audit.slug}">${audit.name}</a>`);
      showModal(initiateModal('confirmModal', 'select', {
        'text': `#audits opened ${openedAudits.length}`,
        'subtext': `${list.join("<br/>")}`,
      }));
    }

		$(function() {
			var data = <?php echo json_encode($graph_values); ?>;

      generateChart('chart-audit', [data[0]]);
      generateChart('chart-report', [data[1]]);


		});
	</script>
</body>
</html>

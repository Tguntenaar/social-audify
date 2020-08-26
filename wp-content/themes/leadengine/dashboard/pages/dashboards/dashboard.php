<?php
/**
 * Template Name: Dashboard new
 */
?>

<!DOCTYPE html>
<html lang='en'>
<head>
  <title>SA Dashboard</title>
  <script src="//code.tidio.co/shn7vki15l32gvyiv33o2bmemv7ckkc2.js" async></script>
</head>
  <?php
    // Header
    include(dirname(__FILE__)."/../header/dashboard_header.php");
    
   /* $user_id $current_user set in the dashboard_header */

    // Weergave intro - Eerste login
    $intro_bit = 0;
    $user = $user_control->get($user_id);

    // Dit moet ergens in de code waar een user word aangemaakt in de wpdb.
    if($user == NULL) {
        $user_control->create($user_id, $current_user->user_login, $current_user->user_email);
        $user = $user_control->get($user_id);
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

    // Get clients
    $clients = $client_control->get_all();
    
    $jsclients = array();
    foreach ($clients as $c) {
      $c = array($c->name, $c->facebook, $c->instagram, $c->website, $c->mail);
      array_push($jsclients, $c);
    }
  ?>
  <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/dashboard/assets/styles/client_dashboard.css<?php echo $cache_version; ?>" type="text/css" />

  <!-- $intro_bit -->
  <?php if ($intro_bit): ?>
    <div class="intro-overlay">
      <div class="vertical-align" style="width: 100%; margin: 0 auto; height: auto;">
        <h1>Welcome to Social Audify!</h1>
        <iframe style="display: block; margin: 0 auto; margin-bottom: 20px;" width="560" height="315" src="https://www.youtube.com/embed/O_JeCXnd3k0" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        <a href="/tutorial/#1566310210069-10357311-75eb" target="_blank" rel="norefferer" class="advice-button" style="background: #27ae60; padding: 12px 20px; font-weight: 100; margin: 0 25px;">Full tutorial<span style="position: relative; left: 5px; font-size: 9px;">(Recommended)</span></a>
        <div style="clear: both; height: 20px;"></div>
        <a href="/profile-page" class="advice-button" style="padding: 12px 20px; font-weight: 100; margin-top: 45px;">Configure profile</a>
      </div>
    </div>
    <script>
      FB.AppEvents.logEvent(FB.AppEvents.EventNames.COMPLETED_REGISTRATION);
    </script>
  <?php endif; ?>

  <!-- new Update -->
  <div class="intro-overlay update-overlay" style="display:none" id="update-overlay">
    <div class="vertical-align" style="width: 100%; margin: 0 auto; height: auto;">
      <h1>New Updates!</h1>
      <span class="feature-title">Feature 1</span>
      <p>
        The tool for generating more sales and automating your reporting process! <br />
        In order to make your audits and reports as efficient as possible, we advise you to take a few minutes to configure your <br />
        account to make sure you get the most out of this software.<br />
        Within the settings you can configure your automatic emails / follow ups you want us to send, you can <br />
        add your phone number so your leads can call you after watching your audit, and you can add a little avatar to add to<br />
        every audit and other features.
      </p>
      <span class="feature-title">Feature 2</span>
      <p>
        This feature let's you do even more work with less time!<br />
      </p>
      <span class="feature-title">Bug fixes:</span>
      <p>
        <ul>
          <li>Mails subjects</li>
          <li>Emoji's</li>
          <li>Audits that where openend by mail clients (frustrating)</li>
        </ul>
      </p>
      <button onclick="$('#update-overlay').slideUp()" class="advice-button">Understood</button>
    </div>
  </div>
  <div class="client-dashboard content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-6" style="padding-bottom: 50px;">
    <div class="sub-nav-client">
      <div class="center-buttons">
        <a href='/client-setup/' class="create-button-client" style="margin-right: 15px;">Create client</a>
        <a href='/client-import/' class="create-button-client" style="margin-right: 15px;">Mass import client</a>
        <a style="color: #fff" class="create-button-client" onclick="exportClients()">Export clients</a>
      </div>
    </div>
    <input type="text" name="search" class="search-client" id="search-input" placeholder="Search..."/>
    <div class="client-overview" id="client-results"><?php
      foreach($clients as $client) { 
        $data = ["id"=> $client->id, "name"=>$client->name, "fb"=> $client->facebook, "ig"=> $client->instagram,
          "wb"=> $client->website, "ml" => $client->mail, "ad_id" => $client->ad_id]; ?>

        <div class="client-overview-row" data-name="<?php echo $client->name; ?>">
          <div class="client-overview-row-inner" data-id="<?php echo $client->id; ?>" data-client="<?php echo htmlentities(json_encode($data)); ?>"><?php
            echo $client->audit_count > 0 ? 
              "<div class='client-status converted'>{$client->audit_count} Audit".($client->audit_count > 1 ? 's' : '')."</div>" :
              "<div class='client-status no_reply'>New</div>"; ?>

            <div class="details">
              <span class="client-name-n"><?php echo $client->name; ?></span><?php

              if($client->facebook != NULL) { ?>
                <a class="social-icon" target="_blank" rel="norefferer" href="https://www.facebook.com/<?php echo $client->facebook; ?>">
                  <i class="fab fa-facebook-f"></i>
                </a><?php
              }
              if($client->instagram != NULL) { ?>
                <a class="social-icon instagram-social mail-social" target="_blank" rel="norefferer" href="https://www.instagram.com/<?php echo $client->instagram; ?>">
                  <i class="fab fa-instagram"></i>
                </a><?php
              } 
              if($client->website != NULL) { ?>
                <a class="social-icon web-social" target="_blank" rel="norefferer" href="<?php
                  echo (substr($client->website, 0, 4) === "http" ? "" : "http://").$client->website; ?>">
                  <i class="fas fa-globe"></i>
                </a><?php
              } 
              if($client->mail != NULL) { ?>
                <a class="social-icon instagram-social mail-social" href="mailto: <?php echo $client->mail; ?>">
                  <i class="far fa-envelope"></i>
                </a><?php
              } ?>
            </div>
            <i class="fas fa-ellipsis-v edit-client" style="cursor:pointer;" onclick="editClient(this)"></i>
            <button class="create-audit-dashboard"> Create audit</button>
          </div>
        </div><?php
      } ?>
    </div>
    
  <!-- <div class="content-right y-scroll col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-top: 0;">
    <div class="col-lg-12">
      <div class="download-viewed">
        <span class="clickable" onclick="export_viewed('audit')" style="margin-right:15px;">Export viewed audits</span>
        <span class="clickable" onclick="export_viewed('report')">Export viewed reports</span> 
      </div>
      <h4>Status recent sent Reports and Audits</h4>
      <hr class="under-line" />
      <div class="overflow-x">
        <div class="activities">
          <div class="recent-send"><?php
            // Recently send Audits and Reports
            $recent_items = $connection->get_all_recent($user_id, 15);
            foreach ($recent_items as $item) {
              $slug = make_slug($item->type, $item->name, $item->id);
              $name = strlen($item->name) <= 15 ? $item->name : substr($item->name, 0, 15).'...'; ?>

              <a href="<?php echo $slug; ?>" class="recent-send-box clickable">
                  <i class='fas fa-envelope<?php echo $item->view_time === NULL ? "" : "-open"; ?>'
                    style='color: <?php echo $item->type == 'audit' ? '#16a085' : '#2980b9'; ?>'></i>
                  <span class="report-audit-name"><?php echo $name; ?></span><br/>
                  <span class="report-audit-name-client"><?php echo $item->client_name; ?></span>
              </a><?php
            } ?>
          </div>
        </div>
      </div>
    </div> -->

    <!-- <div class="report-dash-stat col-xs-12 col-ms-12 col-ld-12 col-lg-6"> -->
      <!-- <select> TODO:
        <option><?php //echo date('Y'); ?></option>
        <option><?php //echo date('F Y'); ?></option>
      </select> -->
      <!-- <h4>Statistics for <?php echo date('Y'); ?></h4>
      <hr class="under-line" />
      <div style="clear:both"></div> -->

      <!-- AUDIT STATS -->
      <!-- <div class="stat-box audit">
        <span class="stat-box-title">Audits sent</span>
        <span class="stat-box-data"> <?php echo count($year_audits); ?> </span>
        <span class="stat-box-procent "> <?php echo percent_print($month_increase_audit); ?> </span>
      </div>
      <div class="stat-box audit clickable" onclick="showOpenedAudits()">
        <span class="stat-box-title">Audits opened</span>
        <span class="stat-box-data"> <?php echo $viewed_audit; ?> </span>
        <span class="stat-box-procent ">
          <?php echo percent_diff($viewed_audit_month, $viewed_audit - $viewed_audit_month, true); ?>
        </span>
      </div>
      <div class="stat-box audit clickable" onclick="showOpenedAudits()">
        <span class="stat-box-title">Open rate</span>
        <span class="stat-box-data"><?php echo percent_print($open_rate_audit); ?></span>
        <span class="stat-box-procent ">
          <?php echo percent_diff($prev_open_rate_audit, $open_rate_audit, true); ?>
        </span>
      </div> -->

      <!-- REPORT STATS -->
      <!-- <div class="stat-box report">
        <span class="stat-box-title">Reports sent</span>
        <span class="stat-box-data"> <?php echo count($year_reports); ?> </span>
        <span class="stat-box-procent "><?php echo percent_print($month_increase_report); ?> </span>
      </div>
      <div class="stat-box report">
        <span class="stat-box-title">Reports opened</span>
        <span class="stat-box-data"> <?php echo $viewed_report; ?> </span>
        <span class="stat-box-procent ">
          <?php echo percent_diff($viewed_report_month, $viewed_report - $viewed_report_month, true); ?>
        </span>
      </div>
      <div class="stat-box report">
        <span class="stat-box-title">Open rate</span>
        <span class="stat-box-data" ><?php echo percent_print($open_rate_report); ?></span>
        <span class="stat-box-procent ">
          <?php echo percent_diff($prev_open_rate_report, $open_rate_report, true); ?>
        </span>
      </div>
    </div> -->

    <!-- COMPARISON WITH LAST MONTH -->
    <!-- <div class="report-compared-last-month col-xs-12 col-sm-12 col-md-12 col-lg-6">
      <h4>Compared to previous Months</h4>
      <hr class="under-line" />
      <div class="graph-box">
        <span class="stat-box-title">% increase in number of audits this month</span>
        <span class="graph-procent"><?php echo percent_print($yearly_increase_audit, $alter_percent_audit); ?></span>
        <span class="graph-info">
          <?php echo percent_print($month_increase_audit, $alter_percent_audit); ?> compared to last month<br />
          (<?php echo $audit_values[11]." Audits in ".date("F Y"); ?>)
        </span>
        <canvas id="chart-audit"></canvas>
      </div>
      <div class="graph-box">
        <span class="stat-box-title">% increase in number of reports this month</span>
        <span class="graph-procent"><?php echo percent_print($yearly_increase_report, $alter_percent_report); ?></span>
        <span class="graph-info">
          <?php echo percent_print($month_increase_report, $alter_percent_report); ?> compared to last month<br />
          (<?php echo $report_values[11]." Reports in ".date("F Y"); ?>)
        </span>
        <canvas id="chart-report"></canvas>
      </div>
    </div> -->
      <!-- <button onclick="$('#update-overlay').slideDown()">View Recent Updates!</button> -->
  </div>
  </section>

	<script charset="utf-8">
    function export_viewed(type) {
      $.ajax({
        type: "POST",
        url: ajaxurl,
        data: {
          action: 'export_viewed',
          type: type,
          user_id: <?php echo $user_id; ?>
        },
        success: function(response) {
          response = JSON.parse(response);
          console.log(response);
          let csvContent = "data:text/csv;charset=utf-8," 
                            + response.map(e => e.join(",")).join("\n");

          var encodedUri = encodeURI(csvContent);
          window.open(encodedUri);
        },
        error: function (xhr, textStatus, errorThrown) {
          var send_error = error_func(xhr, textStatus, errorThrown, data);
          logError(send_error, 'dashboards/dashboard.php', 'export_viewed');
          showModal(initiateModal('errorModal', 'error', {
            'text': `Can't export ${type}s`,
            'subtext': "Please try again later or notify an admin if the issue persists"
          }));
        }
      });
    }

    function showOpenedAudits() {
      var openedAudits = <?php echo json_encode($viewed_audits); ?>;
      var list = openedAudits.map( audit => `<a href="${audit.slug}" class="viewed-list">${audit.name}</a>`);
      showModal(initiateModal('confirmModal', 'select', {
        'text': `Audits opened this month: ${openedAudits.length}`,
        'subtext': `<div class="scrollable">${list.join("")}</div>`,
        'confirmtext': `Close`
      }));
    }

		$(function() {<?php
      if ($user->new_update == 1) {
        $user->update('User', 'new_update', 0); ?>
        $('#update-overlay').show();<?php
      } ?>

			var data = <?php echo json_encode($graph_values); ?>;

      generateChart('chart-audit', [data[0]], null, [false, false], "22,160,133");
      generateChart('chart-report', [data[1]], null, [false, false], "41,128,185");
    });
	</script>
</body>
</html>

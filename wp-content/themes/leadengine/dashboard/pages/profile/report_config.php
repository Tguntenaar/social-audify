<?php
/**
 * Template Name: Report config
 */
?>
<!DOCTYPE html>
<html lang="en" style="overflow-y: scroll;">

<?php
  // Error Logging
  include(dirname(__FILE__)."/../../controllers/log_controller.php");
  $ErrorLogger = new Logger;

  $post_id = get_the_ID();
  $author_id = (int)get_post_field('post_author', $post_id);
  $user_id = get_current_user_id();
  $env = getenv('HTTP_HOST');
  $slug = get_post_field("post_name", $post_id);
  $slug_s = $_SERVER['REQUEST_URI'];
  $leadengine = get_template_directory_uri();

  // Get Author data
  $phone =  get_user_meta($author_id, 'rcp_number', true);
  $author = get_userdata($author_id);

  // Mode check
  $edit_mode = !(isset($_GET['preview_mode']) && $_GET['preview_mode'] == "True") ?
                ($user_id == $author_id) : false;

  // Import controllers & models
  include(dirname(__FILE__)."/../../controllers/user_controller.php");
  include(dirname(__FILE__)."/../../controllers/report_controller.php");
  include(dirname(__FILE__)."/../../services/connection.php");

  include(dirname(__FILE__)."/../../models/report.php");
  include(dirname(__FILE__)."/../../models/user.php");

  // Import block titles
  include(dirname(__FILE__)."/../../assets/php/report_blocks.php");

  // Cache busting
  include(dirname(__FILE__)."/../../assets/php/cache_version.php");

  $connection = new connection;
  $user_control   = new user_controller($connection);
  $report_control = new report_controller($connection);

  $user = $user_control->get($user_id !== 0 ? $user_id : $author_id);

  $theme_color = $user->color_report;

  $post_names =  ['introduction', 'conclusion', 'social_advice', 'campaign_advice'];
  $company_name = get_user_meta($author_id, 'rcp_company', true );

  foreach ($post_names as $post_name) {
    if (isset($_POST[$post_name]) && $edit_mode) {
      $report->update($post_name, sanitize_textarea_field(stripslashes($_POST[$post_name])), 'Report_content');
    }
  }

  if (isset($_POST['followers_count']) && $edit_mode) {

      $social_stats =  array(
        "avgEngagement"=> floatval($_POST["avgEngagement"]),
        "followers_count"=> absint($_POST["followers_count"]),
        "postsLM"=> absint($_POST["postsLM"]),
        "follows_count"=> absint($_POST["follows_count"]),
        "averageComments"=> floatval($_POST["averageComments"]),
        "averageLikes"=> floatval($_POST["averageLikes"]),
      );

      $report->update('social_stats', json_encode($social_stats), 'Report_content');
  }

  function create_graph_data($graph_data, $campaign_blocks) {
    $graph_labels = array();
    $graph_data_list = array();
    $temp_name_array = array();

    foreach($graph_data as $campaign) {
      if (strpos($campaign->name, ':') !== false)
        $campaign->name = explode(':', $campaign->name, 2)[1];

      preg_match_all('/.{0,22}(\s+|$)/', $campaign->name, $temp_name_array);
      array_push($graph_labels, $temp_name_array[0]);

      foreach($campaign_blocks as $block) {
        if (!array_key_exists($block['fb_name'], $graph_data_list)) {
          $graph_data_list[$block['fb_name']] = array();
        }

        if (!isset(((array)$campaign->insights)[$block['fb_name']])) {
            $push_object = "0";
        } else {
            $push_object = ((array)$campaign->insights)[$block['fb_name']];
        }

        array_push($graph_data_list[$block['fb_name']], $push_object);
      }
    }
    return array($graph_data_list, $graph_labels);
  }

  // Visibility functions
  function show_block($edit_mode, $visible, $defined = true) {
    return (($edit_mode || $visible) && $defined);
  }

  function visibility_icon($edit_mode, $visible) {
    if ($edit_mode) {
      $slash = $visible == 1 ? '' : '-slash';
      echo '<i class="far fa-eye'.$slash.'"></i>';
    }
  }

  // Percent Calculator
  function procent_calc($new, $old) {
    return round((($new - $old) / max($old, 1)) * 100);
  }
?>

<head>
  <title>Report</title>
  <!-- TODO: Moet nog met JMeter worden gecheckt... -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="<?php echo $leadengine; ?>/dashboard/assets/styles/dashboard.css" type="text/css">

  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/utils.js<?php echo $cache_version; ?>"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/modal.js<?php echo $cache_version; ?>"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/functions.js<?php echo $cache_version; ?>" charset="utf-8" defer></script>

  <script>var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';</script>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    $(document).ready(function() {
      $('#nav-icon2').click(function() {
        $(this).toggleClass('open');
        $('.mobile-hide').toggleClass('block');
      });
    });
  </script>
  <style>
    .title-report-box, .audit-company-name {
      color: <?php echo $theme_color; ?> !important;
    }
    .under-line {
      border: 1px solid <?php echo $theme_color; ?> !important;
    }
    .sub-header {
      background:  <?php echo $theme_color; ?> !important;
    }
  </style>
</head>
<body class="custom-body">
  <div id="shareModal" class="modal"></div>
  <div id="configModal" class="modal"></div>
  <div id="confirmModal" class="modal"></div>
  <div id="errorModal" class="modal"></div>

  <div class="sub-header col-lg-12" style="display: block !important;">
      <!-- Animated CSS stuff -->
      <div id="nav-icon2">
        <span></span>
        <span></span>
        <span></span>
      </div>

      <?php
      if ($edit_mode) { ?>
        <button id="universal-update" class="advice-button floating-update"> Update </button> <?php
      } ?>

      <div class="mobile-hide">
        <?php
        if ($edit_mode) { ?>
          <a href="/dashboard/" class="home-link"><i class="fas fa-th-large"></i> Dashboard</a><?php
        } ?>

        Report: <?php echo 'config report';

        if ($edit_mode) { ?>
          <button id="config_link" class="copy-link"> <i class="fas fa-cog"></i> Config </button>
          <a href="?preview_mode=True"; class="preview"><i class="far fa-eye"></i> Preview </a><?php
        } else {
          if ($user_id == $author_id) {?>
            <a href="?preview_mode=False"; class="edit"><i class="far fa-eye"></i> Edit </a><?php
          }
        } ?>
        <a href="/tutorial/" target="_blank" rel="norefferer" style="float:right;margin-right:30px;">
          <i class="fab fa-youtube" style="margin-right: 5px;"></i>Tutorial
        </a>
    </div>
  </div>

  <section class="content report-page custom-content min-height">
  <input type="text" class="offscreen" aria-hidden="true" name="public_link" id="public_link"
         value="https://<?php echo $env; ?>/public/<?php echo $slug; ?>" />

    <!-- Intro -->
    <div class="audit-intro report-variant-intro col-lg-10 col-lg-offset-2">
      <div class="client-profile-picture">
        <?php echo get_avatar($author_id, 32); ?>
      </div>
      <div class="audit-intro-text">
        <span class="audit-company-name"><?php echo ($company_name != "") ? $company_name : $author->display_name; ?></span><?php
        if ($edit_mode) { ?>
          <form action="<?php echo $slug_s; ?>#introduction" method="post" enctype="multipart/form-data">
            <textarea maxlength="999" input="text" name="introduction" id="intro_report"><?php echo $user->intro_report; ?></textarea>
          </form><?php
        } else { ?>
          <p><?php
            echo $user->intro_report; ?>
          </p><?php
        } ?>
      </div>
    </div> <?php

    if ($user->campaign_vis_bit || $edit_mode) { ?>

    <div id="social-stats" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 stat-container" >
      <!-- Social Statistics -->
      <span class="facebook-inf-title" style="text-align:center; margin: 0;">Social Stats:</span>
      <span class="sub-title" style="text-align:center; padding:0; margin-top: 5px;">Statistics of your Facebook and Instagram page.</span><?php

      if($edit_mode) { ?>
      <div onclick="toggle_visibility('campaign_vis_bit')" id="campaign_vis_bit_icon" style="top: 20px;" class="visibility-first-level">
        <?php if($user->campaign_vis_bit == 1) { ?>

            <i class="far fa-eye"></i>
        <?php } else { ?>
            <i class="far fa-eye-slash"></i>
        <?php } ?>
    </div> <?php } ?>
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6" style="float:left; height: auto;"><?php
      foreach ($social_blocks as $item) {
          if (show_block($edit_mode, $user->{$item["type"]}, 1)) { ?>
          <div class="col-lg-6 report-social-style" style="float: left; padding:5px;">
            <div class="stat-block col-lg-12">
              <div class="inner">
                <span class="title-box facebook"><?php echo $item["name"]; ?></span>

                  <span class="explenation"><?php echo $item["desc"]; ?></span>
                  <span class="data_animation"><?php
                    echo 0; ?>
                  </span>
              <div onclick="toggle_visibility('<?php echo $item['type']; ?>')" id="<?php echo $item['type']; ?>_icon" class="visibility"><?php
                visibility_icon($edit_mode, $user->{$item["type"]}); ?>
              </div>
            </div>
          </div>
          </div> <?php
        }
      } ?>
      </div>

      <div class="col-lg-6 float outer-chart" style="padding-left: 15px; margin-top: 25px;">
        <div class="col-lg-12 inner-chart" style="height: 460px;">
          <span class="title-report-box">Social Notes</span><?php
          if ($edit_mode) { ?>
            <form action="<?php echo $slug_s; ?>#social_advice" method="post" enctype="multipart/form-data">
              <textarea maxlength="999" style="height: 290px;" input="text" name="campaign_advice" id="campaign_advice"><?php echo $user->campaign_advice; ?></textarea>
            </form><?php
          } else {
            echo "<p>$user->campaign_advice</p>";
          } ?>
        </div>
      </div>
      </div><?php
    } ?>

      <!-- Campaign Statistics -->
      <?php if($user->graph_vis_bit || $edit_mode) { ?>
      <div class="graph-report">
      <div style="clear:both; margin-top: 90px;"></div>
      <span class="facebook-inf-title" style="text-align:center; margin: 0; margin-top: 50px;">Campaign Stats:</span>
      <span class="sub-title" style="text-align:center; padding:0; margin-top: 5px;">Statistics on the Ads or Campaigns you are running.</span><?php

      if($edit_mode) { ?>
      <div onclick="toggle_visibility('graph_vis_bit')" id="graph_vis_bit_icon" style="top: 150px;" class="visibility-first-level">
        <?php if($user->graph_vis_bit == 1) { ?>
            <i class="far fa-eye"></i>
        <?php } else { ?>
            <i class="far fa-eye-slash"></i>
        <?php } ?>
      </div> <?php }

      $counter = 1;


      foreach ($campaign_blocks as $item) {
        if (show_block($edit_mode, $user->{$item["type"]}, 1)) {
          $float = $counter % 2 == 0 ? 'right' : 'left' ?>
          <div class="col-lg-6 report-style instagram-<?php echo $float; ?>" style="float:<?php echo $float; ?>">
            <div class="col-lg-12 left custom-left" style="padding: 0;">
              <div onclick="toggle_visibility('<?php echo $item['type']; ?>')" id="<?php echo $item['type']; ?>_icon" class="visibility">
              <?php visibility_icon($edit_mode, $user->{$item["type"]}); ?></div>
              <span class="title-box facebook"><?php echo $item["name"]; ?><i id="block-info-<?php echo $item['type']; ?>" class="info-i info-i-report fas fa-info"></i></span>
              <div class="chart-info">

                <span class="stat-box-title"><?php echo $item["desc"]; ?></span>
                <span class="graph-procent" style="margin-top: 4px;">
                    <?php

                        if ($item["currency"]) {?>
                            <span class="currency"> <?php echo 'EU'; ?> </span>
                    <?php } ?>
                </span>

              </div>
              <div class="inner custom-inner" style="padding: 0;">
                <canvas id="<?php echo "canvas$counter"; ?>" class="chart-instagram"  style="height: 292px;"></canvas>
              </div>
            </div>
            </div><?php
          $counter++;
        }
       } ?>
      <div class="col-lg-6 float outer-chart" style="padding-left: 15px; margin-top: 38px;">
        <div class="col-lg-12 inner-chart" style="height: 499px;">
          <span class="title-report-box">Campaign Notes</span><?php
          if ($edit_mode) { ?>
            <form action="<?php echo $slug_s; ?>#campaign_advice" method="post" enctype="multipart/form-data">
              <textarea maxlength="999" style="height: 330px;" input="text" name="graph_advice" id="graph_advice"><?php echo $user->graph_advice; ?></textarea>
            </form><?php
          } else {
            echo "<p>$user->graph_advice</p>";
          } ?>
        </div>
      </div>
    </div>
    <?php } ?>
  </section>
  <section class="audit-conclusion audit-conclusion-variant col-lg-12">
    <div class="left-conlusion col-lg-7">
      <h3>Conclusion</h3>
      <hr class="under-line" />
      <div style="clear:both"></div><?php
      if ($edit_mode) { ?>
        <form action="<?php echo $slug_s; ?>#conclusion" method="post" enctype="multipart/form-data">
          <textarea maxlength="999" input="text" name="conclusion_report" id="conclusion_report"><?php echo $user->conclusion_report; ?></textarea>
        </form><?php
      } else { ?>
        <p><?php
          echo $user->conclusion_report; ?>
        </p><?php
      } ?>
    </div>
  </section>
  <div class="footer">
    <span class="phone-number">Phone number: <a href="callto:<?php echo $phone; ?>"><?php echo $phone; ?></a></span>
    <span class="mailadres">Email: <a href="mailto:<?php echo $author->user_email; ?>"><?php echo $author->user_email; ?></a></span>
  </div>

  <script charset='utf-8'>
    var commonPost = {
      'user': '<?php echo $user_id; ?>',
      'type': 'user',
    } <?php

    if ($user->graph_vis_bit == 1 || $edit_mode) { ?>

      $(function() {
          var data = {"impressions":["2159","2350"],"reach":["1751","1668"],"cpc":["0.012706","0.03866"],"cpm":["2.566003","3.191489"],"ctr":["20.194535","8.255319"],"frequency":["1.23301","1.408873"],
                      "spend":["5.54","7.5"],"unique_inline_link_clicks":["2","5"],"website_purchase_roas":["3","5"]};
         var blockNames = [{"type":"cam_imp","name":"Impressions","fb_name":"impressions","desc":"Average impressions","currency":0},{"type":"cam_rch","name":"Reach","fb_name":"reach","desc":"Average reach","currency":0},{"type":"cam_cpc",
                            "name":"Cost per click","fb_name":"cpc","desc":"Average cost per click","currency":1},{"type":"cam_cpm","name":"Cost per mille","fb_name":"cpm","desc":"The average cost for 1,000 impressions","currency":1},{"type":"cam_ctr","name":"Click through ratio","fb_name":"ctr",
                            "desc":"Average ratio click throughs","currency":0},{"type":"cam_frq","name":"Frequency","fb_name":"frequency","desc":"Average frequency ads","currency":0},{"type":"cam_spd","name":"Spend","fb_name":"spend","desc":"Average amount spend","currency":1},
                            {"type":"cam_lcl","name":"Link clicks","fb_name":"unique_inline_link_clicks","desc":"Average link clicks","currency":0},{"type":"cam_ras","name":"Return on ad spent","fb_name":"website_purchase_roas","desc":"Average return on ads spend","currency":0}];
          var labels = [["Example campaign"],["Example campaign"]];


        blockNames.forEach(function(block, index) {
          $(`#block-info-${block.type}`).on('click', function() {
            showModal(initiateModal('errorModal', 'error', { 'text': block.name, 'subtext': block.desc }));
          });
        });


          blockNames.forEach(function(block, index) {
            generateBarChart(`canvas${index + 1}`, [data[block['fb_name']]], [labels], [true, true]);
          });

      });<?php
    } ?>


    <?php
    if ($edit_mode) { ?>
      // TODO: , #manual-ig-form input[type=text]
      $("textarea").on('keyup paste change', function() {
          $(this).data('changed', true);
          toggleUpdate(true);
      });

      $('#universal-update').on('click', function() {
        updateAll();
      });


      function updateAll() {
          var data = {
            ...getChanged('textarea'),
            // TODO: ...getChanged("#manual-ig-form input[type=text]", true),
          };
          console.log(data);
          if (!$.isEmptyObject(data)) {

            $.ajax({
              type: "POST",
              url: ajaxurl,
              data: {action: 'universal_update', ...data, ...commonPost},
              success: function(response) {
                toggleUpdate(false);
                console.log(response);
              },
              error: function(xhr, textStatus, errorThrown) {
                var error = error_func(xhr, textStatus, errorThrown, data);
                logError(JSON.stringify(error), 'page-templates/report_page.php', 'updateAll');
                logResponse(response);
              },
            });

          }
        }

      var toggle_visibility = function(field_name) {
        var field = $(`#${field_name}_icon`);
        var icon = field.find('i');
        field.html("<div class='lds-dual-ring'></div>");

        if (typeof icon[0] !== 'undefined') {
          var visible = icon.attr('class').endsWith("-slash");
          var html = '<i class="far fa-eye' + (visible ? '"' : '-slash"') + '></i>'

          $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
              'action': 'toggle_visibility',
              'field': field_name,
              ...commonPost,
            },
            success: function () { field.html(html) },
            error: function(xhr, textStatus, errorThrown) {
                var error = error_func(xhr, textStatus, errorThrown, data);
                logError(JSON.stringify(error), 'page-templates/report_page.php', 'toggle_visiblity');
                logResponse(response);
              },
          });
        }
      };

      // Share & Track Modal
      var modalData = {
        'text': "This link is copied to your clipboard:",
        'html': "<span class='public-link'><?php echo 'https://www.socialaudify.com/public/'.$slug; ?></span>",
        'subtext': "If your client clicks on the link, you will see it in your dashboard, \
                    so don't click this link yourself if you want to keep track."
      }

      var shareModal = initiateModal('shareModal', 'notification', modalData);
      $('#copy_link').click(function() {
        showModal(shareModal);
        document.getElementById("public_link").select();
        document.execCommand("copy");
      });

        // Auto color Model
        var modalData = {
        text:`Configuration report`,
        subtext:`
          Do you want a custom color for this audit?<br>
          Theme color: <input type="color" id="color" value="<?php echo $theme_color; ?>">
          <i class="fas fa-undo" onclick="$('#color').val('<?php echo $user->color_report; ?>')" ></i>`,
        confirm: 'config_confirmed'
      }

      var configModal = initiateModal('configModal', 'confirm', modalData);
      $('#config_link').click(function() {
        showModal(configModal);
      });

      $("#config_confirmed").click(function() {
        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: 'update_config',
            flag: 'report',
            color: $('#color').val(),
            user_id: <?php echo $user_id; ?>,
            ...commonPost,
          },
          success: function(r) {
            console.log(r);
            window.location.reload();
          },
          error: function(xhr, textStatus, errorThrown) {
            var error = error_func(xhr, textStatus, errorThrown, data);
            logError(JSON.stringify(error), 'page-templates/report_page.php', '$(\"#config_confirmed\").click(');
            logResponse(response);

            var modalData = {
              'text': "Can't update color",
              'subtext': "Please try again later or notify an admin if the issue persists"
            }
            showModal(initiateModal('errorModal', 'error', modalData));
          }
        });
      });

      // Delete Audit Modal
      var modalData = {
        'text': 'Sure you want to delete this Report?',
        'subtext': 'This action is irreversible',
        'confirm': 'delete_confirmed'
      }

      var deleteModal = initiateModal('confirmModal', 'confirm', modalData);
      $('#delete-this-audit').click(function() {
        showModal(deleteModal);
      }); <?php
    } ?>
  </script>
</body>
</html>

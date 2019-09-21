<?php
/**
 * Template Name: Profile Page
 */
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php
  // Header
  include(dirname(__FILE__)."/../header/dashboard_header.php");

  include(dirname(__FILE__)."/../../assets/php/audit_blocks.php");
  include(dirname(__FILE__)."/../../assets/php/report_blocks.php");

  $user = $user_control->get($user_id);
  $audit_visibility = $user->get_visibility('audit');
  $report_visibility = $user->get_visibility('report');

  $ranges = (object) array(
    ["name" => "facebook", "code" => "fb", "db" => "fb"],
    ["name" => "instagram", "code" => "ig", "db" => "insta"],
    ["name" => "website", "code" => "wb", "db" => "website"]
  );

  function print_list_checkboxes($blocks, $title, $visibility_list) {
    echo "<h4>${title}</h4>";
    foreach ($blocks as $block) {
      $item = (object) $block;
      $checked = $visibility_list[0]->{$item->type} ? 'checked' : '';
      echo "<div class='form-check'>
        <input type='hidden' name='check-$item->type' value='0'>
        <input type='checkbox' name='check-$item->type' class='form-check-input' value='1' id='check-$item->type' $checked>
        <label class='form-check-label' for='defaultCheck1'>$item->name</label>
      </div>";
    }
  }
?>
<head>
  <meta charset="utf-8">
  <title>SA Account</title>
</head>
<body>
  <div id="confirmModal" class="modal"></div>

  <div class="profile-page">
      <div class="content-right y-scroll col-lg-9" style="padding-left: 25px;">
        <div class="acitivities col-lg-12">
          <ul class="sub-menu-profile">
            <li id="profile-click">Profile settings</li>
            <li id="avatar-click">Avatar settings</li>
            <li id="audit-click">Intro / conlusion Audits</li>
            <li id="report-click">Intro / conlusion Reports</li>
            <li id="mail-click">Mail config</li>
          </ul>
          
          <div id="profile-member">
            <h3 class="h3-fix">Profile settings</h3>
            <?php echo do_shortcode('[rcp_profile_editor]'); ?>
            <div class="profile-exp">
              <i id="profile-exp" class="information fas fa-info"></i>
            </div>
          </div>

          <div id="profile-avatar" class="profile-avatar">
            <?php echo do_shortcode('[avatar_upload]'); ?>
            <div class="profile-exp">
              <i id="avatar-exp" class="information fas fa-info"></i>
            </div>
          </div>

          <div id="audit-settings">
            <h3 class="h3-fix">Audit text</h3>
            <ul>
              <li id="intro-audit-item" class="active-menu-item">Intro text</li>
              <li id="visibility-audit-item">Visibility preference</li>
              <li id="conclusion-audit-item">Conclusion text</li>
              <li id="fb-audit-item">Facebook text</li>
              <li id="ig-audit-item">Instagram text</li>
              <li id="wb-audit-item">Website text</li>
            </ul>
            <form action="/ppc?settings=audit" id="audit-form" method="post" enctype="multipart/form-data">
              <!-- intro -->
              <div class="intro-audit-block">
                <h4>Introduction Audit</h4>
                <textarea maxlength="999" input="text"  name="introduction-audit" id="introduction-audit"><?php
                  echo trim($user->intro_audit);
                ?></textarea>
              </div>
              <!-- visibility block -->
              <div class="visibility-audit-block" style="display:none">
                <h4>Audit visibility</h4>
                <ul>
                  <li id="fb-audit-visibility-item" class="active-menu-item">Facebook</li>
                  <li id="ig-audit-visibility-item">Instagram</li>
                  <li id="wb-audit-visibility-item">Website</li>
                </ul>
                <div class="fb-audit-visibility-block"><?php
                  print_list_checkboxes(array_merge($facebook_blocks, $facebook_ad_blocks), 'facebook', $audit_visibility); ?>
                </div>
                <div class="ig-audit-visibility-block" style='display:none'><?php
                  print_list_checkboxes($instagram_blocks, 'instagram', $audit_visibility); ?>
                </div>
                <div class="wb-audit-visibility-block" style='display:none'><?php
                  print_list_checkboxes($website_blocks, 'website', $audit_visibility); ?>
                </div>
              </div>
              <!-- conclusion -->
              <div class="conclusion-audit-block">
                <h4>Conclusion Audit</h4>
                <textarea maxlength="999" input="text"  name="conclusion-audit" id="conclusion-audit"><?php
                  echo trim($user->conclusion_audit);
                ?></textarea>
              </div><?php

              // ranges
              foreach ($ranges as $range) { 
                $item = (object) $range; ?>
                <div class="<?php echo $item->code; ?>-audit-block">
                  <h4><?php echo ucfirst($item->name); ?> Audit text</h4><?php
                  for ($i = 1; $i <= 3; $i++) { 
                    if ($i < 3) { ?>
                      <h6>Show this text up to the selected range, making it faster to create an audit</h6>
                      <input maxlength="2" type="text" name="<?php echo "range_{$item->code}_$i"; ?>" placeholder="<?php echo $i * 30; ?>"
                          value="<?php echo $user->{"range_number_{$item->db}_$i"}; ?>"><?php
                    } else { ?>
                      <h6>The last range is less than or equal to 100</h6><?php
                    } ?>
                    <textarea maxlength="999" input="text" name="<?php echo "$item->code-audit_$i"; ?>"><?php
                      echo $user->{"text_{$item->db}_$i"}; ?></textarea><?php
                  } ?>
                </div><?php
              } ?> 
              <!-- error notify -->
              <div class="error-display-audit"></div>
              <input type="submit" value="Update" class="update-button" >
            </form>

            <div class="profile-exp">
              <i id="audit-exp" class="information fas fa-info"></i>
            </div>
          </div>

          <div id="report-settings">
            <h3 class="h3-fix">Report text</h3>
            <ul>
              <li id="intro-report-item" class="active-menu-item">Intro text</li>
              <li id="conclusion-report-item">Conclusion text</li>
              <li id="visibility-report-item">Visibility report</li>
            </ul>
            <form action="/ppc?settings=report" id="report-form" method="post" enctype="multipart/form-data">
              <!-- intro report -->
              <div class="intro-report-block">
                <h4>Introduction Report</h4>
                <textarea maxlength="999" input="text"  name="introduction-report"><?php
                  echo trim($user->intro_report);
                ?></textarea>
              </div>
              <!-- conclusion report -->
              <div class="conclusion-report-block">
                <h4>Conclusion Report</h4>
                <textarea maxlength="999" input="text"  name="conclusion-report"><?php
                  echo trim($user->conclusion_report);
                ?></textarea>
              </div>
              <!-- Visibility Report -->
              <div class="visibility-report-block" style="display:none">
                <h4>Report visibility</h4>
                <ul>
                  <li id="social-report-visibility-item" class="active-menu-item">Social</li>
                  <li id="campaign-report-visibility-item">Campaign</li>
                </ul>
                <div class="social-report-visibility-block">
                  <?php print_list_checkboxes($social_blocks, 'social', $report_visibility); ?>
                </div>
                <div class="campaign-report-visibility-block" style='display:none'>
                  <?php print_list_checkboxes($campaign_blocks, 'campaign', $report_visibility); ?>
                </div>
              </div>
              <div class="error-display-report"></div>
              <input type="submit" value="Update" class="update-button" >
            </form>
            <div class="profile-exp">
              <i id="report-exp" class="information fas fa-info"></i>
            </div>
          </div>

          <div id="mail-settings">
            <h3 class="h3-fix">Mail config</h3>
            <ul>
              <li id="when-mail-item" class="active-menu-item">When</li>
              <li id="what-mail-item">What</li>
            </ul>
            <div class="error-display-mail"></div>
            <form action="/ppc?settings=mail" id="mail_config" method="post" enctype="multipart/form-data">
              <!-- mail when -->
              <div class="when-mail-block">
                <h6>Send first reply after <span id="first-day-value"><?php echo $user->day_1; ?></span> days:</h6>
                <input type="text" id="day_1" name="day_1" placeholder="x" value="<?php echo $user->day_1; ?>" />

                <h6>Send second reply after <span id="second-day-value"><?php echo $user->day_2; ?></span> days:</h6>
                <input type="text" id="day_2" name="day_2" placeholder="x" value="<?php echo $user->day_2; ?>" />

                <h6>Send third reply after <span id="third-day-value"><?php echo $user->day_3; ?></span> days:</h6>
                <input type="text" id="day_3" name="day_3" placeholder="x" value="<?php echo $user->day_3; ?>" />
              </div>
              <!-- mail what -->
              <div class="what-mail-block" style="display:none">
                <ul>
                  <li id="first-what-mail-item" class="active-menu-item">Mail 1</li>
                  <li id="second-what-mail-item">Mail 2</li>
                  <li id="third-what-mail-item">Mail 3</li>
                </ul>
                <!-- mail 1 block -->
                <p>Use #{name} to type the name of receiver in the mail.</p>
                <div class="first-what-mail-block" style="">
                  <textarea maxlength="1999" input="text" name="mail_text" id="mail_text"><?php
                    echo trim($user->mail_text);
                  ?></textarea>
                </div>
                <!-- mail 2 block -->
                <div maxlength="1999" class="second-what-mail-block" style="display:none">
                  <textarea  input="text" name="second_mail_text" id="mail_text2"><?php
                    echo trim($user->second_mail_text);
                  ?></textarea>
                </div>
                <!-- mail 3 block -->
                <div maxlength="1999" class="third-what-mail-block" style="display:none">
                  <textarea  input="text" name="third_mail_text" id="mail_text3"><?php
                    echo trim($user->third_mail_text);
                  ?></textarea>
                </div>
              </div>
              <input type="submit" value="Update" class="update-button" >
            </form>
            <div class="profile-exp">
              <i id="mail-exp" class="information fas fa-info"></i>
            </div>
          </div>

          <div id="mail-settings">
            <h3 class="h3-fix">Your data</h3>
            <?php echo do_shortcode("[subscription_details]"); ?>
          </div>
        </div>
      </div>
    </div>
  <?php wp_footer(); ?>
</body>
<script>
  $(function() {
    $('#day_1, #day_2, #day_3').change(function() {
      $('#first-day-value').text($('#day_1').val());
      $('#second-day-value').text($('#day_2').val());
      $('#third-day-value').text($('#day_3').val());
    });

    $("#audit-form").submit(function(e) {
      e.preventDefault();
      $('.error-display-audit').empty();

      var location = $('#audit-form');

      if (location.find("input[name='range_fb_1']").val() >= location.find("input[name='range_fb_2']").val() ||
          location.find("input[name='range_ig_1']").val() >= location.find("input[name='range_ig_2']").val() ||
          location.find("input[name='range_wb_1']").val() >= location.find("input[name='range_wb_2']").val()) {

        $('.error-display-audit').append("<span style='display: block; color: red; font-size: 14px;'>The first range number always has to be smaller than the second one.</span>");
        return;
      }

      this.submit();
    });

    $("#report-form").submit(function(e){
      e.preventDefault();
      $('.error-display-report').empty();

      this.submit();
    });

    $("#mail_config").submit(function(e) {
      e.preventDefault();
      $('.error-display-mail').empty();

      if (!$.isNumeric($("#day_1").val()) || !$.isNumeric($("#day_2").val()) || !$.isNumeric($("#day_3").val())) {
        $('.error-display-mail').prepend("<span style='color: red; font-size: 14px;'>Input is not a number.</span>");
        return;
      }

      if (parseInt($("#day_1").val()) >= parseInt($("#day_2").val()) || parseInt($("#day_2").val()) >= parseInt($("#day_3").val())) {
        $('.error-display-mail').prepend("<span style='color: red; font-size: 14px;'>Day 1 has to be smaller than Day 2 and Day 2 smaller than Day 3.</span>");
        return;
      }
      
      this.submit();
    });

    $("#intro-audit-item").click(function() { togglePreferenceUI('intro', 'audit') });
    $("#conclusion-audit-item").click(function() { togglePreferenceUI('conclusion', 'audit') });
    // old
    $("#visibility-audit-item").click(function() { togglePreferenceUI('visibility', 'audit') });

    $("#fb-audit-item").click(function() { togglePreferenceUI('fb', 'audit') });
    $("#ig-audit-item").click(function() { togglePreferenceUI('ig', 'audit') });
    $("#wb-audit-item").click(function() { togglePreferenceUI('wb', 'audit') });

    $("#conclusion-report-item").click(function() { togglePreferenceUI('conclusion', 'report') });
    $("#intro-report-item").click(function() { togglePreferenceUI('intro', 'report') });
    // new
    $("#visibility-report-item").click(function() { togglePreferenceUI('visibility', 'report') });

    $("#when-mail-item").click(function() { togglePreferenceUI('when', 'mail') });
    $("#what-mail-item").click(function() { togglePreferenceUI('what', 'mail') });

    $("#first-what-mail-item").click(function() { togglePreferenceUI('first', 'what-mail')});
    $("#second-what-mail-item").click(function() { togglePreferenceUI('second', 'what-mail')});
    $("#third-what-mail-item").click(function() { togglePreferenceUI('third', 'what-mail')});

    $("#fb-audit-visibility-item").click(function() { togglePreferenceUI('fb', 'audit-visibility'); });
    $("#ig-audit-visibility-item").click(function() { togglePreferenceUI('ig', 'audit-visibility'); });
    $("#wb-audit-visibility-item").click(function() { togglePreferenceUI('wb', 'audit-visibility'); });
    // new
    $("#social-report-visibility-item").click(function() { togglePreferenceUI('social', 'report-visibility'); });
    $("#campaign-report-visibility-item").click(function() { togglePreferenceUI('campaign', 'report-visibility'); });


    /**
     * it = item zit in de lijst waarop je klikt
     * bl = block zitten de inputs in
     * type = type of menu
     * show = item from blocks array to show
     */
    function togglePreferenceUI(show, type) {
      var blocks, it, bl;
      if (type == 'report') {
        blocks = ["intro", "conclusion", "visibility"];
      } else if (type == 'audit') {
        blocks = ["wb", "fb", "ig", "intro", "conclusion", "visibility"];
      } else if (type == 'mail') {
        blocks = ['when', 'what'];
      } else if (type == 'audit-visibility') {
        blocks = ['fb', 'ig', 'wb'];
      } else if (type == 'report-visibility') {
        blocks = ['social', 'campaign'];
      } else if (type == 'what-mail') {
        blocks = ['first', 'second', 'third'];
      } else {
        console.log('menu type needs to be added');
        return;
      }
      it = `-${type}-item`;
      bl = `-${type}-block`;
      toggle(blocks, it, bl, show);
    }

    function toggle(blocks, it, bl, show) {
      blocks.forEach(function (el) {
        if (el == show) {
          $("#" + el + it).addClass("active-menu-item");
          $("." + el + bl).css("display", "block");
        } else {
          $("#" + el + it).removeClass("active-menu-item");
          $("." + el + bl).css("display", "none");
        }
      });
    }


    $("#phone-exp").on('click', function(event){
      $("#phone-exp-text").toggle();
    });

    $("#mail-exp").on('click', function(event){
      $("#mail-exp-text").toggle();
    });

    $("#avatar-exp").on('click', function(event){
      $("#avatar-exp-text").toggle();
    });

    $("#avatar-click").on('click', function(event){
      document.getElementById('profile-avatar').scrollIntoView(false);
    });

    $("#audit-click").on('click', function(event){
      document.getElementById('audit-settings').scrollIntoView(false);
    });

    $("#report-click").on('click', function(event){
      document.getElementById('report-settings').scrollIntoView(false);
    });

    $("#mail-click").on('click', function(event){
      document.getElementById('mail-settings').scrollIntoView(false);
    });

    var explanations = {
      profile: {
        title: 'Profile Fields',
        description: '<strong>Your phone number:</strong> Adding a phone number will allow your leads you call with with the click on a button after looking at the audit! <br /><br /><strong>Your e-mail:</strong> This e-mail address will be shown to your clients (for reports) and leads (for audits) and they will reply to this e-mail address Add your VAT number for [Bob]',
      },
      avatar: {
        title: 'Avatar',
        description: 'Your Avatar will be shown on the audit and report page. This could be your logo or a professional photo of yourself.',
      },
      audit: {
        title: 'Audit Fields',
        description: '<strong>Audit text:</strong>In order to speed up the process of sending audits you can add a standard introduction, conclusion, facebook, instagram and website text. This text will automatically be added to every audit you create. You can still add / change this per individual audit after filling this out.<br /><br /><strong>Visibility audit:</strong>Here you can configure which parts of the audit you want to be visible for your lead. For example: if you don’t offer web development, maybe you only want to check if they have the pixel installed. Of course, you can still decide to turn the visibility of individual parts on/off for every audit.',
      },
      report: {
        title: 'Report Fields',
        description: '<strong>Report text</strong>In order to speed up the process of sending your monthly reports, you can add a standard introduction and conclusion text which will be included in every report you generate. You can still add / change the text in every individual report after filling this out.<br /><br /><strong>Visibility report:</strong>Here you can configure which parts of the report you want to be visible for your client. Of course, you can still decide to turn the visibility of individual parts on/off for every audit.',
      },
      mail: {
        title: 'Mail config',
        description: '<strong>When:</strong> If you want, we can send your leads automatic reminders if they do not view their audit. You can configure the amount of days between every single email you want to send here.</br ><br /> <strong>Copy: </strong>You can configure the emails we will send to your leads concerning the audit. Every email and follow up can be configured individually. Use #{name} to enter the name of the client in the email. The emails will be send from our server, but it will show your email address as the sender. They will reply to your email!',
      },
    };

    ['profile', 'avatar', 'audit', 'report', 'mail'].forEach(function(elem) {
      $(`#${elem}-exp`).on('click', function() {
        showModal(initiateModal('errorModal', 'error', {
          'text': `${explanations[elem].title}`,
          'subtext': `${explanations[elem].description}`,
        }));
      });
    });
  });
</script>
</html>
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
  $user = $user_control->get($user_id);
?>
<head>
  <meta charset="utf-8">
  <title>SA Account</title>
</head>
<body>
  <div id="confirmModal" class="modal"></div>

  <div class="profile-page">
      <div class="content-right y-scroll col-lg-9" style="padding-left: 25px;">
        <div class="activities col-lg-12">
          <ul class="sub-menu-profile">
            <li id="member-click">Profile settings</li>
            <li id="avatar-click">Avatar settings</li>
            <li id="audit-click">Audit settings</li>
            <li id="report-click">Reports settings</li>
            <li id="mail-click">Mail configuration</li>
            <li id="account-click">Subscription Info</li><?php
            if ( 'active' == affwp_get_affiliate_status( affwp_get_affiliate_id() ) ) { ?>
              <li><a href="/affiliate-area/">Affiliate Area</a></li> <?php
            } ?>
          </ul>

          <div id="profile-member">
            <h3 class="h3-fix">Profile settings</h3>
            <?php echo do_shortcode('[rcp_profile_editor]'); ?>
            <div class="profile-exp">
              <i id="profile-exp" class="info-i fas fa-info"></i>
            </div>
          </div>

          <div id="profile-avatar" class="profile-avatar">
            <?php echo do_shortcode('[avatar_upload]'); ?>
            <div class="profile-exp">
              <i id="avatar-exp" class="info-i fas fa-info"></i>
            </div>
          </div>

          <div id="audit-settings">
            <h3 class="h3-fix">Audit Settings</h3>
            <a href="/audit-config" class="easy-config-button"><i class="fas fa-cogs"></i>Easy configuration</a>
            <div style="clear:both;"></div>
            <div class="profile-exp">
              <i id="audit-exp" class="info-i fas fa-info"></i>
            </div>
          </div>

          <div id="report-settings">
            <h3 class="h3-fix">Report Settings</h3>
            <a href="/report-config" class="easy-config-button"><i class="fas fa-cogs"></i>Easy configuration</a>
            <div style="clear:both;"></div>
            <div class="profile-exp">
              <i id="report-exp" class="info-i fas fa-info"></i>
            </div>
          </div>

          <div id="mail-settings">
            <h3 class="h3-fix">Mail configuration</h3>
            <ul>
              <li id="when-mail-item" class="active-menu-item">When</li>
              <li id="content-mail-item">Content</li>
            </ul>
            <div class="error-display-mail"></div>
            <form action="/ppc?settings=mail" id="mail_config" method="post" enctype="multipart/form-data">
              <!-- mail when -->
              <div class="when-mail-block tab">
                <h6>Send first reply after <span id="first-day-value"><?php echo $user->day_1; ?></span> days:</h6>
                <input type="text" id="day_1" name="day_1" placeholder="x" value="<?php echo $user->day_1; ?>" />

                <h6>Send second reply after <span id="second-day-value"><?php echo $user->day_2; ?></span> days:</h6>
                <input type="text" id="day_2" name="day_2" placeholder="x" value="<?php echo $user->day_2; ?>" />

                <h6>Send third reply after <span id="third-day-value"><?php echo $user->day_3; ?></span> days:</h6>
                <input type="text" id="day_3" name="day_3" placeholder="x" value="<?php echo $user->day_3; ?>" />
              </div>
              <!-- mail content -->
              <div class="content-mail-block tab" style="display:none">
                <ul>
                  <li id="first-content-mail-item" class="active-menu-item">Mail 1</li>
                  <li id="second-content-mail-item">Mail 2</li>
                  <li id="third-content-mail-item">Mail 3</li>
                  <li id="test-content-mail-item">Test</li>
                </ul>
                <div class="mail-components">
                  <p>Use #{name} to type the name of receiver in the subject/mail.</p>
                  <p>Use #{audit} to type the name your audit in the subject/mail.</p>
                  <p>Use #{auditlink} to type audit name as a trackable link of your audit in the subject/mail.</p>
                </div>
                <!-- mail 1 block -->
                <div class="first-content-mail-block tab">
                  <input class="subject-line" type="text" name="mail_subject_1" id="mail_subject_1" placeholder="Subject" value="<?php echo $user->subject_1?>">
                  <textarea maxlength="1999" input="text" name="mail_text_1" id="mail_text"><?php
                    echo trim($user->mail_text_1);
                  ?></textarea>
                </div>
                <!-- mail 2 block -->
                <div class="second-content-mail-block tab" style="display:none">
                  <input class="subject-line" type="text" name="mail_subject_2" id="mail_subject_2" placeholder="Subject" value="<?php echo $user->subject_2?>">
                  <textarea maxlength="1999" input="text" name="mail_text_2" id="mail_text2"><?php
                    echo trim($user->mail_text_2);
                  ?></textarea>
                </div>
                <!-- mail 3 block -->
                <div class="third-content-mail-block tab" style="display:none">
                  <input class="subject-line" type="text" name="mail_subject_3" id="mail_subject_3" placeholder="Subject" value="<?php echo $user->subject_3?>">
                  <textarea maxlength="1999" input="text" name="mail_text_3" id="mail_text3"><?php
                    echo trim($user->mail_text_3);
                  ?></textarea>
                </div>
                <!-- mail test block -->
                <div class="test-content-mail-block tab" style="display:none">
                  <div style="width:75px;">
                    <input type="radio" name="mail" value="1" checked>
                      <span class="radio-label">mail 1</span>
                    <input type="radio" name="mail" value="2">
                      <span class="radio-label">mail 2</span>
                    <input type="radio" name="mail" value="3">
                    <span class="radio-label">mail 3</span>
                  </div>
                  <input class="subject-line" type="text" name="user_mail"  placeholder="example@mail.com" id="recipient_email" value="<?php echo $user->email?>" style="margin-top:10px;">
                  <button type="button" class="create-button-client" id="send-mail">Send test mail</button>
                </div>
              </div>
              <div class="mail-components">
                <input type="submit" value="Update" class="update-button" >
              </div>
            </form>
            <div class="profile-exp">
              <i id="mail-exp" class="info-i fas fa-info"></i>
            </div>
          </div>

          <div class="profile-page-blocks">
            <h3 class="h3-fix">Mail signature</h3>
            
            <form action="<?php echo get_stylesheet_directory_uri() ?>/process_signature.php" method="post" enctype="multipart/form-data">
              <?php 
                $wordpress_upload_dir = wp_upload_dir();
                $signature_directory = $wordpress_upload_dir["basedir"] . "/signature";
                $upload_id = $user->signature;
                $signature_url = wp_get_attachment_url($upload_id);
                if ($signature_url):
              ?>
              Your Photo: <br/>
              <img src=<?php echo $signature_url ?> alt="Signature" width="250" id="signature-img">
              <br/>
              <?php endif; ?>
              <input class="button" style="margin-top: 20px" type="file" name="mail-signature" size="25" accept="image/png,image/jpg" required/>
              <div style="clear:both; margin-bottom: 25px;"></div>
              <button id="delete-signature">Delete</button>
              <input type="submit" name="submit" value="Submit" />
            </form>
            <br/>
          </div>

          <div id="account-settings">
            <h3 class="h3-fix">Your data</h3><?php
            echo do_shortcode("[subscription_details]"); ?>
          </div>
        </div>
      </div>
    </div>
  <?php wp_footer(); ?>
</body>
<script>
  $(function() {
    $('#delete-signature').click(function() {
      $.ajax({
        type: "POST",
        url: ajaxurl,
        data: { 'action': "delete_signature" },
        success: function(response) {
          logResponse(response);
          $('#signature-img').hide();
        },
        error: logResponse,
      });
    });

    $('#send-mail').click(function() {
      $.ajax({
        type: "POST",
        url: ajaxurl,
        data: { 'action': "test_mail",
                "mail": $("#recipient_email").val(),
                "mailcount": $("input[name=mail]:checked").val() },
        success: logResponse,
        error: logResponse,
      });
    });

    $('#day_1, #day_2, #day_3').change(function() {
      $('#first-day-value').text($('#day_1').val());
      $('#second-day-value').text($('#day_2').val());
      $('#third-day-value').text($('#day_3').val());
    });

    /**
    * MAIL FORM
    */
    $("#mail_config").submit(function(e) {
      e.preventDefault();
      $('.error-display-mail').empty();

      ['day_1', 'day_2', 'day_3'].forEach(function(day) {
        if (!$.isNumeric($(`#${day}`).val())) {
          $("#when-mail-item").click();
          $('.error-display-mail').prepend("<span style='color: red; font-size: 14px;'>Input is not a number.</span>");
          return;
        }
      });

      if (parseInt($("#day_1").val()) >= parseInt($("#day_2").val()) || parseInt($("#day_2").val()) >= parseInt($("#day_3").val())) {
        $("#when-mail-item").click();
        $('.error-display-mail').prepend("<span style='color: red; font-size: 14px;'>Day 1 has to be smaller than Day 2 and Day 2 smaller than Day 3.</span>");
        return;
      }

      this.submit();
    });

    var mailBlocks = ['when', 'content'];
    $("#when-mail-item").click(function() { toggle(mailBlocks, 'when', 'mail') });
    $("#content-mail-item").click(function() { toggle(mailBlocks, 'content', 'mail') });

    var contentMailBlocks = ['first', 'second', 'third', 'test'];
    $("#first-content-mail-item").click(function() { 
      toggle(contentMailBlocks, 'first', 'content-mail');
      $('.mail-components').show();
    });
    $("#second-content-mail-item").click(function() { 
      toggle(contentMailBlocks, 'second', 'content-mail');
      $('.mail-components').show();
    });
    $("#third-content-mail-item").click(function() { 
      toggle(contentMailBlocks, 'third', 'content-mail');
      $('.mail-components').show();
    });
    $("#test-content-mail-item").click(function() { 
      toggle(contentMailBlocks, 'test', 'content-mail');
      $('.mail-components').hide();
    });

    function toggle(blocks, show, type) {
      blocks.forEach(function (el) {
        if (el == show) {
          $(`#${el}-${type}-item`).addClass("active-menu-item");
          $(`.${el}-${type}-block`).css("display", "block");
        } else {
          $(`#${el}-${type}-item`).removeClass("active-menu-item");
          $(`.${el}-${type}-block`).css("display", "none");
        }
      });
    }

    ["member", "avatar"].forEach(function(el) {
      $(`#${el}-click`).on('click', function() {
        $(`#profile-${el}`)[0].scrollIntoView(false);
      });
    });

    ["audit", "report", "mail", "account"].forEach(function(el) {
      $(`#${el}-click`).on('click', function() {
        $(`#${el}-settings`)[0].scrollIntoView(false);
      });
    });

    var explanations = {
      profile: {
        title: 'Profile Fields',
        description: '<strong>Your phone number: </strong> Adding a phone number will allow your leads you call with with the click on a button after looking at the audit! <br /><br /><strong>Your e-mail: </strong>This e-mail address will be shown to your clients (for reports) and leads (for audits) and they will reply to this e-mail address<br><br> <strong>VAT number: </strong>For Businesses within the European Union (except for Dutch businesses): By adding your VAT number we do not have to charge you VAT as we can use international treaties. For businesses outside of the European Union we will not need to have your VAT-number. <br> For Dutch Businesses: As our business is based in The Netherlands we will have to charge you VAT. You can simply file it with your \'OB-aangifte\' to get the VAT refunded.',
      },
      avatar: {
        title: 'Avatar',
        description: 'Your Avatar will be shown on the audit and report page. This could be your logo or a professional photo of yourself.',
      },
      audit: {
        title: 'Audit Fields',
        description: '<strong>Audit text: </strong>In order to speed up the process of sending audits you can add a standard introduction, conclusion, facebook, instagram and website text. This text will automatically be added to every audit you create. You can still add / change this per individual audit after filling this out.<br /><br /><strong>Visibility audit: </strong>Here you can configure which parts of the audit you want to be visible for your lead. For example: if you don’t offer web development, maybe you only want to check if they have the pixel installed. Of course, you can still decide to turn the visibility of individual parts on/off for every audit.',
      },
      report: {
        title: 'Report Fields',
        description: '<strong>Report text: </strong>In order to speed up the process of sending your monthly reports, you can add a standard introduction and conclusion text which will be included in every report you generate. You can still add / change the text in every individual report after filling this out.<br /><br /><strong>Visibility report: </strong>Here you can configure which parts of the report you want to be visible for your client. Of course, you can still decide to turn the visibility of individual parts on/off for every audit.',
      },
      mail: {
        title: 'Mail config',
        description: '<strong>When: </strong>If you want, we can send your leads automatic reminders if they do not view their audit. You can configure the amount of days between every single email you want to send here.</br ><br /> <strong>Copy: </strong>You can configure the emails we will send to your leads concerning the audit. Every email and follow up can be configured individually. Use #{name} to enter the name of the client in the email. The emails will be send from our server, but it will show your email address as the sender. They will reply to your email!',
      },
    };

    ['profile', 'avatar'].forEach(function(el) {
      $(`#${el}-exp`).on('click', function() {
        showModal(initiateModal('errorModal', 'error', {
          'text': `${explanations[el].title}`,
          'subtext': `${explanations[el].description}`,
        }));
      });
    });

    [{type:'audit', url:'tutorial/#1488725417825-2758920e-e7ef'},
     {type:'report', url:'tutorial/#1489503964921-3acbdde1-0dcf'},
     {type:'mail', url:'tutorial/#1489503963784-5b2be039-5cee'}].forEach(function(el) {
      $(`#${el.type}-exp`).on('click', function() {

        showModal(initiateModal('errorModal', 'link', {
          'text': `${explanations[el.type].title}`,
          'subtext': `${explanations[el.type].description}`,
          'link': el.url,
          'confirmtext': 'Watch video',
        }));
      });
    });
  });
</script>
</html>

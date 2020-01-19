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
  $leadengine = get_template_directory_uri();

  // Get Author data
  $phone =  get_user_meta($author_id, 'rcp_number', true);
  $calendar_link =  get_user_meta($author_id, 'rcp_calendar', true);
  $author = get_userdata($author_id);

  // Mode check
  $edit_mode = !(isset($_GET['preview_mode']) && $_GET['preview_mode'] == "True") ?
                ($user_id == $author_id || $user_id == 2) : false;

  // Language file
  include(dirname(__FILE__)."/../../assets/languages/language_file.php");

  // Import controllers & models
  include(dirname(__FILE__)."/../../services/connection.php");
  include(dirname(__FILE__)."/../../controllers/audit_controller.php");
  include(dirname(__FILE__)."/../../controllers/user_controller.php");
  include(dirname(__FILE__)."/../../controllers/client_controller.php");

  include(dirname(__FILE__)."/../../models/audit.php");
  include(dirname(__FILE__)."/../../models/user.php");
  include(dirname(__FILE__)."/../../models/client.php");

  // Import block titles
  include(dirname(__FILE__)."/../../assets/php/audit_blocks.php");

  // Cache busting
  include(dirname(__FILE__)."/../../assets/php/cache_version.php");

  $connection = new connection;
  $user_control   = new user_controller($connection);
  $audit_control  = new audit_controller($connection);
  $client_control  = new client_controller($connection);

  // Get audit by post_id
  $id = $audit_control->get_id($post_id);
  $audit = $audit_control->get($id);
  $client = $client_control->get($audit->client_id);
  $user = $user_control->get($user_id !== 0 ? $user_id : $author_id);

  $theme_color = ($audit->color == "") ? $user->color_audit : $audit->color;

  if ($audit->manual == 0) {
    $sumPostLikes = $audit->instagram_bit == "1" ? array_sum($audit->instagram_data->likesPerPost) : NULL;
  }

  function advice_equal_to_user($user, $audit, $type) {
    if (
      $type == 'fb' &&
      ($user->text_fb_1 == $audit->facebook_advice ||
      $user->text_fb_2 == $audit->facebook_advice ||
      $user->text_fb_3 == $audit->facebook_advice)
    ) {
      return true;
    }
    if (
      $type == 'ig' &&
      ($user->text_insta_1 == $audit->instagram_advice ||
      $user->text_insta_2 == $audit->instagram_advice ||
      $user->text_insta_3 == $audit->instagram_advice)
    ) {
      return true;
    }
    if (
      $type == 'wb' &&
      ($user->text_website_1 == $audit->website_advice ||
      $user->text_website_2 == $audit->website_advice ||
      $user->text_website_3 == $audit->website_advice)
    ) {
      return true;
    }
    return false;
  }

   // Overall scores
   $score = array(
    'fb' => $audit->facebook_score  != NULL ? $audit->facebook_score : 50,
    'ig' => $audit->instagram_score != NULL ? $audit->instagram_score : 50,
    'wb' => $audit->website_score   != NULL ? $audit->website_score : 50
  );

  // Advice boxes
  $advice = array(
    'fb' => selectAdvice($audit->facebook_advice, $score['fb'], $user, "fb"),
    'ig' => selectAdvice($audit->instagram_advice, $score['ig'], $user, "insta"),
    'wb' => selectAdvice($audit->website_advice, $score['wb'], $user, "website")
  );

  function selectAdvice($advice, $score, $user, $type) {
    if ($advice != NULL) {
      return $advice;
    } if ($score < (int) $user->{"range_number_{$type}_1"}) {
      return $user->{"text_{$type}_1"};
    } if ($score < (int) $user->{"range_number_{$type}_2"}) {
      return $user->{"text_{$type}_2"};
    }
    return $user->{"text_{$type}_3"};
  }

  function procent_calc($new, $old) {
    return round((($new - $old) / max($old, 1)) * 100);
  }

  function show_block($edit_mode, $visible) {
    return ($edit_mode || $visible);
  }

  function printValue($value, $is_icon = false) {
    if ($is_icon) {
      return $value == 0 ?
      '<i class="fas fa-times" style="color: #c0392b; display: inline"></i>' :
      '<i class="fas fa-check" style="color: #27ae60; display: inline"></i>';
    }
    return $value;
  }

  function visibility_short_code($edit_mode, $visible, $name, $class = 'visibility') {
    if ($edit_mode) {
      $slash = $visible == 1 ? '' : '-slash';?>
      <div onclick="toggle_visibility('<?php echo $name; ?>')" id="<?php echo $name; ?>_icon" class="<?php echo $class; ?>">
        <i class="far fa-eye<?php echo $slash; ?>"></i>
      </div><?php
    }
  }

  function change_tags($text, $client, $audit) {
    // Client name -> #{client}
    if (strpos($text, '#{client}') !== false) {
      $text = str_replace('#{client}', $client->name, $text);
    }

    // Competitor name -> #{competitor}
    if (strpos($text, '#{competitor}') !== false) {
      $text = str_replace('#{competitor}', $audit->competitor_name, $text);
    }

    // Facebook score -> #{fb_score}
    if (strpos($text, '#{fb_score}') !== false) {
      $score = ($audit->facebook_score == NULL) ? 50 : $score = $audit->facebook_score;
      $text = str_replace('#{fb_score}', $score, $text);
    }

    // Instagram score -> #{instagram_score}
    if (strpos($text, '#{insta_score}') !== false) {
      $score = ($audit->instagram_score == NULL) ? 50 :$score = $audit->instagram_score;
      $text = str_replace('#{insta_score}', $score, $text);
    }

    // Website score -> #{website_score}
    if (strpos($text, '#{website_score}') !== false) {
      $score = ($audit->website_score == NULL) ? 50 : $audit->website_score;
      $text = str_replace('#{website_score}', $score, $text);
    }
    return $text;
  }

  $video_nothing = ($audit->video_iframe == NULL && $user->std_iframe == NULL) ? 'checked' : '';
  $video_iframe = ($audit->video_iframe != NULL || $user->std_iframe != NULL) ? 'checked' : '';

  $display_nothing = ($audit->video_iframe == NULL && $user->std_iframe == NULL) ? 'style="display:block;"' : 'style="display:none;"';
  $display_iframe = ($audit->video_iframe != NULL || $user->std_iframe != NULL) ? 'style="display:block;"' : 'style="display:none;"';
  $company_name = get_user_meta($author_id, 'rcp_company', true );

  $post_url = htmlentities(base64_encode(get_site_url() . "/" . get_post_field( 'post_name', get_post() )));
  if ($_SERVER['SERVER_NAME'] == "dev.socialaudify.com") {
    $url = "https://livecrawl.socialaudify.com/pdf/" . $post_url;
  } else {
    $url = "https://livecrawl.socialaudify.com/pdf/" . $post_url;
  }

  $options = "";
  foreach ($language as $key => $value) {
    if ($audit->language == $key) {
      $options .= "<option value='". $key ."' selected >". $key ."</option>";           
    } else {
      $options .= "<option value='". $key ."' >". $key ."</option>";           
    }
  }

  $language_options = "<select style='margin-top: 7px;' id='language'>" . $options . "</select>";
  $language = $language[$audit->language];
  
  function call_to_contact($phone, $mail, $calendar_link, $language, $user) { ?>
    <div class="info">
      <?php if (isset($phone) && $phone != "") { ?><a href="callto:<?php echo $phone;?>"><i class="fas fa-phone"></i><?php echo $phone; ?></a><?php } ?>
      <a href="mailto:<?php echo $mail; ?>"><i class="fas fa-envelope"></i><?php echo $mail; ?></a>
      <?php
      if ($calendar_link != "") { ?>
        <a class="calendar" href="<?php echo $calendar_link; ?>"><i class="fas fa-calendar"></i>
        <?php if ($user->appointment_text == "") { ?>
              <?php echo $language['make_appointment']; ?>
            <?php } else {
                echo $user->appointment_text;
            } ?>
        </a><?php
      } ?>
    </div><?php
  }
  // $mail_contents = 'Hi, dit is een test. %0D%0A %0D%0A Test test test %0D%0A %0D%0A https://www.socialaudify.com/public/' . get_post_field( 'post_name', get_post() );
?>
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-149815594-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-149815594-1');
  </script>

  <title>Audit - <?php echo $audit->name; ?></title>
  <!-- TODO: Moet nog met chrome canary worden gecheckt... -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <link rel="stylesheet" href="<?php echo $leadengine; ?>/dashboard/assets/styles/dashboard.css<?php echo $cache_version; ?>" type="text/css">
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/modal.js<?php echo $cache_version; ?>"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/functions.js<?php echo $cache_version; ?>"></script>

  <script>var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';</script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    function generatePDF() {
      $(".load-screen").toggle();

      $.ajax({
        method: 'GET',
        url: '<?php echo $url; ?>',
        crossDomain: true,
        success: function(data) {
          const linkSource = `data:application/pdf;base64,${$.parseJSON(data)}`;
          const downloadLink = document.getElementById("testje");
          const fileName = "<?php echo $audit->name; ?>";

          downloadLink.href = linkSource;
          downloadLink.download = fileName;
          downloadLink.click();

          $(".load-screen").toggle();
        },
        error: function (xhr, textStatus, errorThrown) {
          $(".load-screen").toggle();
          alert("Error generating PDF.");
          console.log(xhr);
        }
      });
    }

    $(document).ready(function() {
      $('#nav-icon2').click(function() {
        $(this).toggleClass('open');
        $('.mobile-hide').toggleClass('block');
      });
    });
  </script>
  <style>
    .score-text, .advice-title, .audit-company-name, .footer .phone-number a,
    .footer .mailadres a {
      color: <?php echo $theme_color; ?> !important;
    }

    .under-line {
      border: 1px solid <?php echo $theme_color; ?> !important;
    }

    .sub-header {
      background:  <?php echo $theme_color; ?> !important;
    }
    .slider::-webkit-slider-thumb,
    .slider::-moz-range-thumb {
        background:  <?php echo $theme_color; ?> !important;
    }
  </style>
</head>
<body class="custom-body">
  <div class="load-screen"><div class='lds-dual-ring'></div> <h3>Generating PDF, wait a minute.</h3></div>
    <div class="sub-header col-lg-12" style="display: block !important;">

    <?php if ($user_id == $author_id) { ?>

      <!-- Animated CSS stuff -->
      <div id="nav-icon2">
        <span></span>
        <span></span>
        <span></span>
      </div>

    <?php } ?>

    <?php if ($user_id != $author_id) { ?>
        Audit: <?php echo $audit->name;
    } ?>

    <?php
    if ($edit_mode) { ?>
      <button id="universal-update" class="advice-button floating-update"> Update </button><?php
    } ?>

    <div class="mobile-hide"><?php
      if ($edit_mode) { ?>
        <a href="/dashboard/" class="home-link"><i class="fas fa-th-large"></i> Dashboard </a><?php
      } ?>


      Audit: <?php echo $audit->name;


      if ($edit_mode) { ?>
        <div id="delete-this-audit"> <i class="fas fa-trash"></i> </div>
        <button id="copy_link" class="copy-link" style="margin-right: 15px;"> <i class="fas fa-share-alt-square"></i> Share & Track </button>
        <button id="config_link" class="copy-link"> <i class="fas fa-cog"></i> Config </button>
        <a href="?preview_mode=True" class="preview" style="float: right; margin-right:5px"><i class="far fa-eye"></i> Preview </a>
        <a class="copy-link" onclick="generatePDF()" style="margin-right: 15px;"><i class="fas fa-file-pdf"></i>Pdf</a>
        <a id="testje"  class="copy-link" style="display:none;" download="file.pdf"></a>
        <?php
      } else {
        if ($user_id == $author_id) {?>
          <a href="?preview_mode=False"; class="edit"><i class="far fa-eye"></i> Edit </a><?php
        }
      } ?>
    </div>
  </div>

  <div id="shareModal" class="modal"></div>
  <input type="text" class="offscreen" aria-hidden="true" name="public_link" id="public_link" value=<?php echo "https://".$env."/public/".$slug; ?> />
  
  <div id="configModal" class="modal"></div>
  <div id="confirmModal" class="modal"></div>
  <div id="reloadModal" class="modal"></div>
  <div id="errorModal" class="modal"></div>
  <div id="firstTimeModal" class="modal"></div>
  <section class="content white custom-content min-height">
    <?php
    if (($audit->video_iframe == "" || $audit->video_iframe == "") && !$edit_mode) {

    } else if (($audit->video_iframe == "" || $audit->video_iframe == "") && $edit_mode) {
        ?><div class="intro-video"></div><?php
    } else if (($audit->video_iframe != "" && $audit->video_iframe != NULL) || $edit_mode) { ?>
         <div class="intro-video"><?php
              $video = str_replace("&#34;", '"', stripslashes($audit->video_iframe));

              if (strpos($video, 'height') !== false) {
                  echo "<iframe ". $video ."</iframe>";
              } ?>
            </div><?php
    }

    if ($audit->video_iframe != NULL && $audit->video_iframe != "") {
        $video_iframe_link = '<iframe '.stripslashes($audit->video_iframe).'</iframe>';
    } else {
        $video_iframe_link = '';
    }

    if ($edit_mode) { ?>
      <div class="video-options">
        <h3>Video banner:</h3>
        <span class="eplenation-banner">You can add a video on top of your audit by adding the iframe link here. Click <a href="tutorial/#1570543881921-3fd7746a-9da5">[here]</a> to learn how to find this link.</span>
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="banner-form" method="post" enctype="multipart/form-data">

          <input type="radio" class="iframe-radio" data-display="block" <?php echo ($audit->video_iframe != NULL && $audit->video_iframe != "") ? 'checked' : ''; ?>/>
            <span class="radio-label">Video</span>
          <input type="radio" class="iframe-radio" id="video_iframe" value="" data-display="none" <?php echo ($audit->video_iframe == NULL || $audit->video_iframe == "") ? 'checked' : ''; ?>/>
            <span class="radio-label">Nothing</span>
          <input type="text" id="iframe-input" placeholder="Insert iframe(Loom/Youtube etc.)" style="display:<?php echo ($audit->video_iframe != NULL & $audit->video_iframe != '') ? 'block' : 'none'; ?>"
            pattern="(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))" value='<?php echo $video_iframe_link; ?>'/>
        </form>
      </div><?php
    } ?>

    <?php visibility_short_code($edit_mode, $audit->introduction_vis_bit, 'introduction_vis_bit', 'visibility-first-level'); ?>

    <div class="audit-intro<?php echo ($audit->video_iframe != NULL && $audit->video_iframe != "") ? " with-video" : ""; ?> col-lg-10 col-lg-offset-2">
      <?php if ($audit->picture_vis_bit == 1 || $edit_mode) { ?>
      <div class="client-profile-picture">
        <?php echo get_wp_user_avatar($author_id, "original"); ?>
        <?php visibility_short_code($edit_mode, $audit->picture_vis_bit, 'picture_vis_bit', 'custom-visibility'); ?>
      </div>
      <div class="audit-intro-text">
        <span class="audit-company-name"><?php $company = get_user_meta($author_id, 'rcp_company', true ); if ($company == "") { echo $author->display_name; } else { echo $company; }?></span><?php
      } else { echo '<div class="audit-intro-text">'; }

      if ($audit->introduction_vis_bit == 1 || $edit_mode) {
        if ($edit_mode) { ?>
          <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#introduction" method="post" enctype="multipart/form-data">
            <textarea maxlength="999" input="text"  name="introduction" id="introduction" style="background: #f5f6fa;"><?php if ($audit->introduction == NULL) { echo $user->intro_audit; } else { echo $audit->introduction; } ?></textarea>
          </form>
          <div class="description-tags">
              You can insert the following tags in all the text fields: <span style="color: #000;">#{client}, #{competitor}, #{fb_score}, #{insta_score}, #{website_score}</span>
          </div>
          <?php
      } else {  ?>
          <p style='font-size: 14px; font-weight: 100; line-height: 24px;'>
              <?php if ($audit->introduction == NULL) { echo "<pre>" . change_tags($user->intro_audit, $client, $audit) . "</pre>"; } else { echo "<pre>" . change_tags($audit->introduction, $client, $audit) . "</pre>"; } ?></p><?php
        } ?>
      </div>
    <?php }  else { echo '</div>'; }?>
    </div><?php
    if ($audit->facebook_bit == "1" && ($audit->facebook_vis_bit || $edit_mode)) { ?>
      <div class="col-lg-12 facebook-info" id="facebook-info">
        <span class="facebook-inf-title"><span class="round facebook"><i class="fab fa-facebook-f"></i></span> &nbsp;
            <?php if ($user->facebook_title == "") { ?>
                <?php echo $language['fb_title']; ?>:
            <?php } else {
                echo $user->facebook_title;
            } ?>
        </span>

        <span class="sub-title">
            <?php if ($user->facebook_sub_title == "") { ?>
                <?php echo $language['fb_subtitle']; ?>
            <?php } else {
                echo $user->facebook_sub_title;
            } ?>
        </span><?php

        visibility_short_code($edit_mode, $audit->facebook_vis_bit, 'facebook_vis_bit', 'visibility-first-level'); ?>

        <div class="col-lg-6 left bottom-40">
          <div class="inner"><?php
            foreach ($facebook_blocks as $item) {
              if (show_block($edit_mode, $audit->{$item["type"]})) { ?>
                <div class="stat-block col-lg-6 col-md-12">
                  <div class="inner">
                    <span class="title-box facebook"><?php echo $language[$item["name"]]; ?></span>
                    <span class="data_animation"><?php
                    if ($audit->has_comp) { ?>
                      <span class="data-view"><span class="comp-label">You: <br />
                      </span><?php echo printValue(round($audit->facebook_data->{$item["fb_name"]}, 2), $item['is_icon']); ?></span>
                      <!--  -->
                      <span class="vertical-line"></span>
                      <span class="competitor-stats"><span class="comp-label"><?php echo ucfirst($audit->competitor_name); ?>: <br /></span>
                        <?php echo printValue(round($audit->competitor->facebook_data->{$item["fb_name"]}, 2), $item['is_icon']); ?></span><?php
                    } else {
                      echo printValue(round($audit->facebook_data->{$item["fb_name"]}, 2), $item['is_icon']);
                    } ?>
                    </span>
                    <span class="explenation"><?php echo $language[$item["name"] . " exp"]; ?></span><?php
                      visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]); ?>
                  </div>
                </div><?php
              }
            }
            foreach ($facebook_ad_blocks as $item) {
              if (($audit->has_comp || !$item["is_comp"]) && show_block($edit_mode, $audit->{$item["type"]})) {
                $path = $item["is_comp"] ? $audit->competitor : $audit; ?>
                <div class="stat-block col-lg-6" id="fb_ads">
                  <div class="inner">
                    <span class="title-box facebook"><?php echo $language[$item["name"]]; ?></span><?php
                    // preview mode
                    if (!$edit_mode) {
                      $class = $path->facebook_data->runningAdds ? "check" : "times";
                      $color = $path->facebook_data->runningAdds ? "#27ae60" : "#c0392b"; ?>

                      <span class="explenation"><?php echo $language[$item["name"] . " exp"]; ?></span>
                      <span class="data_animation">
                        <i class='fas fa-<?php echo $class; ?>' style='color: <?php echo $color; ?>'></i>
                      </span><?php
                    // edit mode
                    } else { ?>
                      <form class="ads-radio" action=""><?php
                        $checked = $path->facebook_data->runningAdds;
                        $name = $item["is_comp"] ? "ads_c" : "ads"; ?>
                        <input type="radio" name="<?php echo $name; ?>" value="yes" <?php echo $checked ? "checked" : ""; ?>/>
                          <span class="label_ads">Yes</span>
                        <input type="radio" name="<?php echo $name; ?>" value="no" <?php echo !$checked ? "checked" : ""; ?>/>
                          <span class="label_ads">No</span>
                      </form><?php
                        visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]); ?>
                      <span class="explenation-ads">
                        <a target="_blank" rel="noreferrer" href="<?php echo 'https://www.facebook.com/pg/'. $path->facebook_name .'/ads/'; ?>">
                          Click here to watch if this page is currently running ads. (This can't be automated)
                        </a>
                      </span><?php
                    } ?>
                  </div>
                </div><?php
              }
            } ?>
          </div>
        </div>
        <div class="col-lg-6 right">
          <div class="inner custom-inner">
            <div class="score col-lg-12">
              <div class="inner custom-text">
                <span class="score-tag"><?php echo $language['score']; ?></span><?php
                if ($edit_mode) { ?>
                  <span class="score-text"><span id="facebook_value"></span>%</span>
                  <div class="slidecontainer">
                    <input type="range" min="1" max="100" value="<?php echo $score['fb']; ?>" class="slider" id="facebook_score">
                  </div><?php
                } else { ?>
                  <span class="score-text"><?php echo $score['fb']; ?>%</span><?php
                } ?>

                <span class="advice-title"><?php echo $language['facebook_advice']; ?></span><?php
                if ($edit_mode) { ?>
                  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#facebook-info" method="post" enctype="multipart/form-data">
                    <textarea maxlength="999" input="text"  name="facebook_advice" id="facebook_advice"><?php echo  $advice['fb']; ?></textarea>
                  </form><?php
                } else { ?>
                  <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo "<pre>" . change_tags($advice['fb'], $client, $audit) . "</pre>"; ?><?php
                  call_to_contact($phone, $author->user_email, $calendar_link, $language, $user);
                } ?>
              </div>
            </div>
          </div>
        </div>
      </div><?php
    }
    if ($audit->instagram_bit == "1" && ($audit->instagram_vis_bit || $edit_mode)) { ?>
      <div class="col-lg-12 facebook-info" id="instagram-info">
        <span class="facebook-inf-title"><span class="round instagram"><i class="fab fa-instagram"></i></span> &nbsp; 
            <?php if ($user->instagram_title == "") { ?>
                <?php echo $language['insta_title']; ?>:
            <?php } else {
                echo $user->instagram_title;
            } ?>
        </span>
        </span>
        <span class="sub-title">
           <?php if ($user->instagram_sub_title == "") { ?>
              <?php echo $language['insta_subtitle']; ?>
            <?php } else {
                echo $user->instagram_sub_title;
            } ?>
        </span><?php
        visibility_short_code($edit_mode, $audit->instagram_vis_bit, 'instagram_vis_bit', 'visibility-first-level');

        if ($audit->manual && $edit_mode) { ?>
          <span class="manual-text"><span style="color: #e74c3c;">Attention: </span>
            There is no instagram or instagram business account found, so <a target="_blank" rel="noreferrer" href="https://www.instagram.com/<?php echo $audit->instagram_name; ?>">click here</a> to gather your data!
          </span><?php
        }
        if ($edit_mode && (isset($audit->competitor->manual) && $audit->competitor->manual)) { ?>
          <span class="manual-text" style="margin-top: 15px;"><span style=" color: #e74c3c;">
            Attention: </span>There is no competitor instagram or instagram business account found, so <a href="https://www.instagram.com/<?php echo $audit->competitor_name; ?>">click here</a> to gather your data!
          </span><?php
        } ?>

        <div style="clear:both"></div>
        <div class="col-lg-6 instagram-left" style="float:left;"><?php
          if (show_block($edit_mode, $audit->insta_hashtag) && (!$audit->manual) && isset($audit->instagram_data->hashtags[0][0])) { ?>
            <div class="col-lg-12 left custom-left" style="padding: 0;"><?php
              visibility_short_code($edit_mode, $audit->insta_hashtag, 'insta_hashtag'); ?>

              <div class="chart-info">
                <span class="stat-box-title"><?php echo $language['hastag_used']; ?></span>
                <span class="graph-procent" style="margin-top: 4px;"><?php echo $language['hastag_most_used']; ?> '<?php echo $audit->instagram_data->hashtags[0][0]; ?>'</span>
              </div>
              <div class="inner custom-inner" style="padding: 0;">
                <canvas id="hashtag-chart" class="chart-instagram"  style="height: 292px;"></canvas>
              </div>

              <div class="legend">
                  <span class="round-color you-color"></span> <span class="space"><?php echo $client->name; ?></span>
                  <?php if ($audit->has_comp && !$audit->competitor->manual) { ?><span class="round-color competitor-color"></span> <?php echo ucfirst($audit->competitor_name); } ?>
              </div>
            </div><?php
          }
          if (show_block($edit_mode, $audit->insta_lpd) && (!$audit->manual)) { ?>
            <div class="col-lg-12 left custom-left" style="padding: 0;"><?php
              visibility_short_code($edit_mode, $audit->insta_lpd, 'insta_lpd'); ?>

              <div class="chart-info">
                <span class="stat-box-title"><?php echo $language['likes_on_post']; ?></span>
                <span class="graph-procent" style="margin-top: 2px;"><?php echo $language['average']; ?> <?php
                  echo number_format($sumPostLikes / count($audit->instagram_data->likesPerPost), 2); ?></span>
                <span class="graph-info"><?php
                  if ($audit->has_comp && (isset($audit->competitor) && !$audit->competitor->manual)) {

                    $likes_comp = array_sum($audit->competitor->instagram_data->likesPerPost);
                    $procent_increace = procent_calc($sumPostLikes, $likes_comp);
                    $color = $sumPostLikes < $likes_comp ? "#c0392b" : ($sumPostLikes == $likes_comp ? "#2980b9" : "#27ae60"); ?>

                    <span style="color: <?php echo $color; ?>"><?php echo $procent_increace; ?>% compared to competitor</span><?php
                  } ?>
                </span>
              </div>
              <div class="inner custom-inner">
                <canvas id="lpd-chart" class="chart-instagram"  style="height: 292px;"></canvas>
              </div>
              <div class="legend">
                  <span class="round-color you-color"></span> <span class="space"><?php echo $client->name; ?></span><?php
                  if ($audit->has_comp && !$audit->competitor->manual) { ?>
                    <span class="round-color competitor-color" style="margin-right: 4px;"></span><?php
                    echo ucfirst($audit->competitor_name);
                  } ?>
              </div>
            </div><?php
          } ?>
        </div><?php

        if (($audit->manual == 1)) { ?>
          <div class="col-lg-12 instagram-right" style="padding: 0;float: right;">
          <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#instagram-info" style="width: 50%; float:left;" method="post" enctype="multipart/form-data" id="manual-ig-form"><?php
        } else { ?>
          <div class="col-lg-6 instagram-right" style="padding: 0;float: right;">
          <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#instagram-info" style="width: 100%; float:left;" method="post" enctype="multipart/form-data" id="manual-ig-form"><?php
        }

        function competitor_code($audit, $edit_mode, $item, $language) {
          // Preview mode hide description animation
          if (!$audit->manual) { ?>
            <span class="data_animation"><?php
          }
          if ($audit->has_comp) { ?>
            <span class="data-view">
              <span class="comp-label">
                You: <br />
              </span><?php
              manual_check($audit, $item, $edit_mode, 0);?>
            </span>

            <!-- LEFT SIDE OF BLOCK -->
            <span class="vertical-line"></span>
            <!-- RIGHT SIDE OF BLOCK -->

              <span class="competitor-stats">
                <span class="comp-label"><?php
                  echo ucfirst($audit->competitor_name); ?>: <br />
                </span><?php
                manual_check($audit, $item, $edit_mode, 1);?>
              </span><?php

          } else { // heeft geen competitor
            manual_check($audit, $item, $edit_mode, 0);
          }
          // Preview mode hide description animation
          if (!$audit->manual) { ?>
            </span>
            <span class="explenation"><?php echo $language[$item['name'] . " exp"]; ?></span><?php
          }
        }

        function manual_check($audit, $item, $edit_mode, $comp) {
          $base = ($comp) ? $audit->competitor : $audit;
          $value = $base->instagram_data->{$item['ig_name']};
          $str = ($comp) ? "comp-" : "";

          if ($base->manual && $edit_mode) {?>
            <input type="text" id="<?php echo "{$str}".$item["ig_name"]; ?>" value="<?php echo round($value, 2); ?>" /></span><?php
          } else {
            echo round($value, 2);
          }
        }

        foreach ($instagram_blocks as $item) {
          // Laat hem zien als edit mode aanstaat ?? of die bestaat in de database..
          if (show_block($edit_mode, $audit->{$item["type"]})) { ?>
            <div class="stat-block col-lg-6" id="<?php echo $item['type']; ?>">
              <div class="inner">
                <span class="title-box instagram"><?php
                  echo $language[$item["name"]]; ?>
                </span><?php
                // Als preview mode laat description staan en hide client info

                competitor_code($audit, $edit_mode, $item, $language);
                // preview mode show visibility icon
                visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]); ?>

              </div>
            </div><?php
          }
        }?>
        </form><?php

          if ($audit->manual == 1) { ?>
            <div class="col-lg-6 instagram-score" style="margin-top: -10px; float:left; "><?php
          } else { ?>
            <div class="col-lg-12 instagram-score" style="float:right; "><?php
          } ?>
            <div class="col-lg-12 insta-score" >
              <div class="col-lg-12 align">
                <span class="score-tag insta-advice-tag"><?php echo $language['score']; ?></span><?php
                if ($edit_mode) { ?>
                  <span class="score-text"><span id="instagram_value"></span>%</span>
                  <div class="slidecontainer">
                    <input type="range" min="1" max="100" value="<?php echo $score['ig']; ?>" class="slider" id="instagram_score">
                  </div><?php
                } else { ?>
                  <span class="score-text"><?php echo $score['ig']; ?>%</span><?php
                } ?>
              </div>
              <div class="col-lg-12 align" id="instagram-info">
                <span class="advice-title"><?php echo $language['instagram_advice']; ?></span><?php
                if ($edit_mode) { ?>
                  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#instagram-info" method="post" enctype="multipart/form-data">
                    <textarea maxlength="999" input="text"  name="instagram_advice" id="instagram_advice"><?php echo $advice['ig']; ?></textarea>
                  </form><?php
                } else { ?>
                  <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo "<pre>" . change_tags($advice['ig'], $client, $audit) . "</pre>"; ?> </p>
                  <?php
                  call_to_contact($phone, $author->user_email, $calendar_link, $language, $user);
                } ?>
            </div>
          </div>
          </div>
        </div>
      </div><?php
    }
    if ($audit->website_bit == "1" && ($audit->website_vis_bit || $edit_mode)) { ?>
      <div class="col-lg-12 facebook-info website-info" id="website-info"><?php
        if (!$audit_control->check_website($audit->id, $audit->has_comp)) { ?>
          <div class="wait-for-crawl"><p>Please wait a moment, the website data is being prepared.</p></div><?php
        } ?>
        <span class="facebook-inf-title"><span class="round website">W</span> &nbsp; 
        <?php if ($user->website_title == "") { ?>
                <?php echo $language['website_title']; ?>:
            <?php } else {
                echo $user->website_title;
            } ?>
        </span>
        <span class="sub-title">
            <?php if ($user->website_sub_title == "") { ?>
              <?php echo $language['website_subtitle']; ?>
            <?php } else {
                echo $user->website_sub_title;
            } ?>
          </span><?php
        visibility_short_code($edit_mode, $audit->website_vis_bit, 'website_vis_bit', 'visibility-first-level'); ?>

        <div class="col-lg-6 left" style="background: transparent; border: 0; margin-top: 0;">
          <div class="inner custom-inner"><?php

            foreach ($website_blocks as $item) {
              if (show_block($edit_mode, $audit->{$item["type"]})) { ?>
                <div class="stat-block col-lg-6" id="<?php echo $item['type']; ?>">
                  <div class="inner">
                    <span class="title-box website"><?php echo $language[$item["name"]]; ?></span>
                    <span class="data_animation"><?php
                    if ($audit->has_comp) { ?>
                      <span class="data-view"><span class="comp-label">You: <br />
                        </span><?php echo printValue($audit->{$item["db_name"]}, $item['is_icon']); ?></span>
                      <span class="vertical-line"></span>
                      <span class="competitor-stats"><span class="comp-label"><?php echo ucfirst($audit->competitor_name); ?>: <br /></span>
                        <?php echo printValue($audit->competitor->{$item["db_name"]}, $item['is_icon']) ?></span><?php
                    } else {
                      echo printValue($audit->{$item["db_name"]}, $item['is_icon']);
                    } ?>
                    </span><?php
                      visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]); ?>
                    <span class="explenation"><?php echo $language[$item["name"] . " exp"] ?></span>
                  </div>
                </div><?php
              }
            } ?>
          </div>
        </div>
        <div class="col-lg-6 right instagram-right" style="padding: 20px 20px; margin-top: 35px !important;">
          <span class="score-tag website-advice-tag"><?php echo $language['score']; ?></span><?php
          if ($edit_mode) { ?>
            <span class="score-text"><span id="website_value"></span>%</span>
            <div class="slidecontainer">
              <input type="range" min="1" max="100" value="<?php echo $score['wb']; ?>" class="slider" id="website_score">
            </div>
            <span class="advice-title margin-advice-title"><?php echo $language['website_advice']; ?></span>
            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#website-info" method="post" enctype="multipart/form-data">
              <textarea maxlength="999" input="text"  name="website_advice" id="website_advice"><?php echo $advice['wb']; ?></textarea>
            </form>
            <?php
          } else { ?>
            <span class="score-text"><?php echo $score['wb']; ?>%</span>
            <span class="advice-title margin-advice-title"><?php echo $language['website_advice']; ?></span>
            <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo "<pre>" . change_tags($advice['wb'], $client, $audit) . "</pre>"; ?></p>
            <?php
              call_to_contact($phone, $author->user_email, $calendar_link, $language, $user);
          } ?>
        </div>
      </div><?php
    } ?>
  </section>

  <?php if ($audit->conclusion_vis_bit == 1 || $edit_mode) { ?>
      <section class="audit-conclusion col-lg-12">
        <?php visibility_short_code($edit_mode, $audit->conclusion_vis_bit, 'conclusion_vis_bit', 'visibility-first-level'); ?>

        <div class="left-conlusion col-lg-7">
          <h3><?php echo $language['conclusion']; ?></h3>
          <hr class="under-line" />
          <div style="clear:both"></div><?php
          if ($edit_mode) { ?>
            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#conclusion" method="post" enctype="multipart/form-data">
              <textarea maxlength="999" input="text"  name="conclusion" id="conclusion"><?php
                echo $audit->conclusion == NULL ? $user->conclusion_audit : $audit->conclusion;
              ?></textarea>
            </form><?php
          } else { ?>
            <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php
              echo $audit->conclusion == NULL ? "<pre>" . change_tags($user->conclusion_audit, $client, $audit) . "</pre>" : "<pre>" . change_tags($audit->conclusion, $client, $audit) . "</pre>";
            ?></p><?php
          } ?>
        </div>
      </section>
  <?php } ?>
  <div class="footer">
    <?php if (isset($phone) && $phone != "") { ?><span class="phone-number"><?php echo $language['phone_number']; ?>: <a href="callto:<?php echo $phone; ?>"><?php echo $phone; ?></a></span><?php } ?>
    <span class="mailadres"><?php echo $language['email']; ?>: <a href="mailto:<?php echo $author->user_email; ?>"><?php echo $author->user_email; ?></a></span><?php
         if ($calendar_link != "") { ?>
          <div class='footer-calendar'></div>
          <a class="calendar" href="<?php echo $calendar_link; ?>"><i class="fas fa-calendar"></i>
          <?php if ($user->appointment_text == "") { ?>
                <?php echo $language['make_appointment']; ?>
              <?php } else {
                  echo $user->appointment_text;
              } ?>
          </a><?php
        } ?>
</body>
</html>

<script charset='utf-8'>
  var commonPost = {
    'type': 'audit',
    'audit': '<?php echo $audit->id; ?>',
  }

  <?php // Website Crawl
    if (isset($_GET['view'])) { ?>
       $(window).ready(function(){
          $(this).one('mousemove', function() { 
              // mouse move
          }).one('scroll', function(){
            $.ajax({
              type: "POST",
              url: ajaxurl,
              data: { action: 'insert_view',  ...commonPost },
              success: function (response) {
                  console.log(response);
              },
              error: function (xhr, textStatus, errorThrown) {
                  var send_error = error_func(xhr, textStatus, errorThrown, data);
                  logError(send_error, 'page-templates/audit_page.php', 'insert_view');
              },
            });
          });
      });
  <?php }
  if ($audit->website_bit && !$audit->has_website): ?>

    var modalData = {
      'text': 'Website data available',
      'subtext': 'Confirm to reload the page and view the crawled website data',
      'confirm': 'reload_confirmed'
    }

    var reloadModal = initiateModal('reloadModal', 'confirm', modalData);
    $('#reload_confirmed').click(function() {
      window.location.reload();
    });

    function crawlFinishedCheck() {
      $.ajax({
        type: "POST",
        url: ajaxurl,
        data: { action: 'crawl_data_check', comp: '<?php echo $audit->has_comp; ?>', ...commonPost },
        success: function (response) {
          if (response == true) {
            showModal(reloadModal);
          } else {
            setTimeout(function() { crawlFinishedCheck(); }, 8000);
          }
        },
        error: function (xhr, textStatus, errorThrown) {
            var send_error = error_func(xhr, textStatus, errorThrown, data);
            logError(send_error, 'page-templates/audit_page.php', 'toggle_visibility');
        },
      });
    }
    crawlFinishedCheck();<?php
  endif; ?>

  <?php // Graph Generate
  
  if ($audit->instagram_bit == "1" && $audit->manual == 0) { ?>

    // Line Chart values
    var data_array = [<?php echo json_encode($audit->instagram_data->likesPerPost); ?>];
    // Bar Chart values
    var bar_labels = [<?php echo json_encode($audit->instagram_data->hashtags[0]); ?>];
    var bar_data = [<?php echo json_encode($audit->instagram_data->hashtags[1]); ?>];

    <?php if ($audit->has_comp && (isset($audit->competitor) & !$audit->competitor->manual)) { ?>
      data_array.push(<?php echo json_encode($audit->competitor->instagram_data->likesPerPost); ?>);
      bar_labels.push(<?php echo json_encode($audit->competitor->instagram_data->hashtags[0]); ?>);
      bar_data.push(<?php echo json_encode($audit->competitor->instagram_data->hashtags[1]); ?>);
    <?php } ?>

    var allLines = Array(Math.max(data_array[0].length, 12)).fill().map((_, index) => index);
    generateChart('lpd-chart', data_array, allLines, [true, true]);
    generateAreaChart('hashtag-chart', bar_data, bar_labels); <?php
  } ?>

  <?php
  if ($edit_mode) { ?>
    // Visibility function : TODO : hier ook mooier als functions.php de geupdate visibility bool terug geeft...
    var toggle_visibility = function(field_name) {
      var field = $(`#${field_name}_icon`);
      var icon = field.find('i');
      field.html("<div class='lds-dual-ring'></div>");

      if (typeof icon[0] !== 'undefined') {
        var visible = icon.attr('class').endsWith("-slash");
        var icon = '<i class="far fa-eye' + (visible ? '"' : '-slash"') + '></i>'

        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: { action: 'toggle_visibility', field: field_name , ...commonPost },
          success: function () { field.html(icon) },
          error: function (xhr, textStatus, errorThrown) {
              var send_error = error_func(xhr, textStatus, errorThrown, data);
              logError(send_error, 'page-templates/audit_page.php', 'toggle_visibility');
          },
        });
      }
    };

    $(function() {
        $("#picture_vis_bit_icon").hover(function(){
            $('.client-profile-picture').css("opacity", "0.6");
            $('.audit-company-name').css("opacity", "0.4");
        });

        $( "#picture_vis_bit_icon" ).mouseleave(function() {
            $('.client-profile-picture').css("opacity", "1");
            $('.audit-company-name').css("opacity", "1");
        });

        $("#introduction_vis_bit_icon").hover(function(){
            $('#introduction').css("opacity", "0.4");
        });

        $( "#introduction_vis_bit_icon" ).mouseleave(function() {
            $('#introduction').css("opacity", "1");
        });

        $("#conclusion_vis_bit_icon").hover(function(){
            $('.left-conlusion').css("opacity", "0.4");
        });

        $("#conclusion_vis_bit_icon").mouseleave(function(){
            $('.left-conlusion').css("opacity", "1");
        });

        $("#facebook_vis_bit_icon").hover(function(){
            $('#facebook-info').css("opacity", "0.4");
        });

        $("#facebook_vis_bit_icon").mouseleave(function(){
            $('#facebook-info').css("opacity", "1");
        });

        $("#instagram_vis_bit_icon").hover(function(){
            $('#instagram-info').css("opacity", "0.4");
        });

        $("#instagram_vis_bit_icon").mouseleave(function(){
            $('#instagram-info').css("opacity", "1");
        });

        $("#website_vis_bit_icon").hover(function(){
            $('#website-info').css("opacity", "0.4");
        });

        $("#website_vis_bit_icon").mouseleave(function(){
            $('#website-info').css("opacity", "1");
        });



      // On change of an text area show update all
      $("textarea, #manual-ig-form input[type=text]").on('keyup paste change', function() {
        $(this).data('changed', true);
        toggleUpdate(true);

        var propId = $(this).prop('id');
        // Disable slider
        if ($(this).is('textarea') && propId.includes('_advice')) {
          var adviceType = propId.replace('_advice', '');
          handleSlider(adviceType);

          // Enable slider if value is empty
          if ($(this).val() == '') {
            type = (propId.includes('facebook')) ? 'fb' : (propId.includes('instagram')) ? 'ig' : 'wb';
            if (!!sliderData[type]) {
              handleSlider(adviceType, sliderData[type].range, sliderData[type].text);
            }
          }
        }
      });

      $("input[type=range]").on('mouseup', function() {
        $(this).data('changed', true);
        toggleUpdate(true);
      });

      $("input:radio[class=iframe-radio]").on('click', function() {
        $(this).parent().children('input:radio:checked').prop("checked", false);
        $(this).parent().children('#iframe-input').css("display", $(this).data('display'));
        $(this).prop("checked", true);
        toggleUpdate(true);
      });

      $("#iframe-input").on('change paste keyup', function() { toggleUpdate(true) });

      $('#universal-update').on('click', function() {
        updateAll();
      });

      function getIframe() {
        var selected = $('#iframe-input:visible');
        if (typeof selected[0] != 'undefined') {
          var value = selected.val().replace('<iframe','').replace('</iframe>', '');
          if (value != '<?php echo $audit->video_iframe; ?>') {
            return { "video_iframe" : value };
          }
        }
        return { "video_iframe" : '' };
      }

      function updateAll() {
        var data = {
          ...getChanged('textarea'),
          ...getChanged("#manual-ig-form input[type=text]", true),
          ...getChanged("input[type=range]"),
          ...getChanged("input[type=radio]"),
          ...getIframe(),
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
              // TODO : dit kan beter, db wordt nu gevuld met string.empty ipv NULL,
              //  - succesvolle iframe value kan worden gereturned, en hier uitgelezen
              //  - daarbij zit er ook een php check op.
              if (data.video_iframe.includes("src=") || data.video_iframe == "") {
                $('.intro-video').html(`<iframe${data.video_iframe}</iframe>`);

              } else {
                alert("You have to insert a Iframe.");
              }
            },
            error: function (xhr, textStatus, errorThrown) {
              var send_error = error_func(xhr, textStatus, errorThrown, data);
              logError(send_error, 'page-templates/audit_page.php', 'updateAll');
            }
          });
        }
      }

      // Share & Track Modal
      var modalData = {
        'text': "This link is copied to your clipboard:",
        'html': `<span class='public-link'>${window.location.hostname}/public/<?php echo $slug; ?></span>`,
        'subtext': `You can send this link from your own email address to your lead. If your lead
          clicks on the link, you will see it in your dashboard, so make sure you don’t
          click on the link yourself in order to be able to track this.`,
      }

      var shareModal = initiateModal('shareModal', 'notification', modalData);
      $('#copy_link').click(function() {
        showModal(shareModal);
        var a = document.createElement("a");
        a.href = ""
        document.getElementById("public_link").select();
        document.execCommand("copy");
      });

      // Auto Mail + color Model
      var modalData = {
        text:`<span style="font-weight:bold; font-size: 18px;">Configuration audit</span>`,
        subtext:`Do you want to sent this client automatic reminders?<br/>
          <input type="checkbox" id="mail_bit_check" <?php echo $audit->mail_bit ? 'checked': ''; ?>><br/><br/>
          Social Audify can send automatic reminders if your lead does not open the audit. You can configure the emails:
          <a style="margin-bottom:10px" href='/profile-page/#mail-settings'>[here]</a><br><br>
          Do you want a custom color for this audit?<br/><br />
          <span style="font-weight: 500;">Theme color:</span><br /> <input type="color" id="color" value="<?php echo $theme_color; ?>">
          <i class="fas fa-undo" onclick="$('#color').val('<?php echo $theme_color; ?>')" ></i><br /><br />
          <span style="font-weight: 500;">Audit language:</span><br />
          <?php echo $language_options; ?>`,
        confirm: 'config_confirmed'
      }

      var configModal = initiateModal('configModal', 'confirm', modalData);
      $('#config_link').click(function() {
        $('#color').val('<?php echo $theme_color; ?>');
        showModal(configModal);
      });

      $("#config_confirmed").click(function() {
        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: {
            action: 'update_config',
            color: $('#color').val(),
            value: $("#mail_bit_check").is(':checked'),
            language: $("#language :selected").val(),
            ...commonPost
          },
          success: function(response) {
            console.log(response);
            window.location.reload()
          },
          error: function (xhr, textStatus, errorThrown) {
            var send_error = error_func(xhr, textStatus, errorThrown, data);
            logError(send_error, 'page-templates/audit_page.php', 'mail_config_confirm');
            showModal(initiateModal('errorModal', 'error', {
              'text': "Can't update mail function",
              'subtext': "Please try again later or notify an admin if the issue persists"
            }));
          }
        });
      });

      // Delete Audit Modal
      var modalData = {
        'text': 'Sure you want to delete this Audit?',
        'subtext': 'This action is irreversible',
        'confirm': 'delete_confirmed'
      }

      var deleteModal = initiateModal('confirmModal', 'confirm', modalData);
      $('#delete-this-audit').click(function() {
        showModal(deleteModal);
      });

      // Delete Audit Modal
      var firstTimeModalData = {
        'text': 'Please note',
        'subtext': 'We do not send the first email about the audit at this time! Click on share and track to copy the link and email from your own email. Then select in configuration whether or not you would like us to start sending the follow ups.',
        'confirm': ''
      }

      var firstTimeModal = initiateModal('firstTimeModal', 'error', firstTimeModalData);

      <?php if ($user->first_time == 0) { ?>
           showModal(firstTimeModal);
           <?php $user->update('User', 'first_time', 1); ?>
      <?php } ?>


      $('#delete_confirmed').click(function() {
        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: {'action': 'delete_page', ...commonPost},
          success: function (response) {
            window.location.replace('https://<?php echo $env; ?>/audit-dashboard')
          },
          error: function (xhr, textStatus, errorThrownr) {
             var send_error = error_func(xhr, textStatus, errorThrown, data);
            logError(send_error, 'page-templates/audit_page.php', 'delete_audit_confirm');
            showModal(initiateModal('errorModal', 'error', {
              'text': "Can't delete this audit",
              'subtext': "Please try again later or notify an admin if the issue persists"
            }));
          }
        });
      });

      function update_ads(button, competitor) {
        var data = {
          action: 'update_ads_audit',
          competitor: (competitor) ? 'true' : 'false',
          ads: button,
          ...commonPost
        };

        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: data,
          success: logResponse,
          error: function (xhr, textStatus, errorThrown) {
              var send_error = error_func(xhr, textStatus, errorThrown, data);
              logError(send_error, 'page-templates/audit_page.php', 'update_ads');
          },
        });
      }

      $('input:radio[name=ads]').change(function () {
        update_ads(this.value, false);
      });

      $('input:radio[name=ads_c]').change(function () {
        
        update_ads(this.value, true);
      });
    });

    // Dynamic slider functions
    function handleSlider(type, range = false, text = false) {
      var value = $('#' + type + '_value');
      var slider = $('#' + type + '_score');
      var advice = $('#' + type + '_advice');
      // set
      value.html(slider.val());

      slider.off('input');
      slider.on('input', function(e) {
        value.html($(e.target).val());
        if (text) {
          changeAdvice($(e.target).val(), range, advice, text);
        }
      });
    }

    function changeAdvice(sliderValue, range, adviceArea, text) {
      if (sliderValue < range.one) {
        adviceArea.val(text.one);
      } else if (sliderValue < range.two) {
        adviceArea.val(text.two);
      } else {
        adviceArea.val(text.three);
      }
    }

    <?php
    function replace_lbs($string) {
      echo json_encode(preg_replace("/\r|\n/", '\n', $string));
    } ?>

    var sliderData = {<?php
      if ($audit->facebook_bit == "1") { ?>
        fb: { <?php
          if ($audit->facebook_advice != "" && !advice_equal_to_user($user, $audit, 'fb')) { ?>
            range: false,
            text: false,<?php
          } else { ?>
            range: {
              one: <?php echo $user->range_number_fb_1; ?>,
              two: <?php echo $user->range_number_fb_2; ?>,
            },
            text: {
              one: <?php replace_lbs($user->text_fb_1); ?>,
              two: <?php replace_lbs($user->text_fb_2); ?>,
              three: <?php replace_lbs($user->text_fb_3); ?>,
            },<?php
          } ?>
        },<?php
      }
      if ($audit->instagram_bit == "1") { ?>
        ig: {<?php
          if ($audit->instagram_advice != "" && !advice_equal_to_user($user, $audit, 'ig')) { ?>
            range: false,
            text: false,<?php
          } else { ?>
            range: {
              one: <?php echo $user->range_number_insta_1; ?>,
              two: <?php echo $user->range_number_insta_2; ?>,
            },
            text: {
              one: <?php replace_lbs($user->text_insta_1); ?>,
              two: <?php replace_lbs($user->text_insta_2); ?>,
              three: <?php replace_lbs($user->text_insta_3); ?>,
            },<?php
          } ?>
        }, <?php
      }
      if ($audit->website_bit == "1") { ?>
        wb: {<?php
          if ($audit->website_advice != "" && !advice_equal_to_user($user, $audit, 'wb')) { ?>
            range: false, // disabled slider text
            text: false,<?php
          } else { ?>
            range: {
              one: <?php echo $user->range_number_website_1; ?>,
              two: <?php echo $user->range_number_website_2; ?>,
            },
            text: {
              one: <?php replace_lbs($user->text_website_1); ?>,
              two: <?php replace_lbs($user->text_website_2); ?>,
              three: <?php replace_lbs($user->text_website_3); ?>,
            }, <?php
          } ?>
        },<?php
      } ?>
    }
    if (!!sliderData.fb) {
      handleSlider('facebook', sliderData.fb.range, sliderData.fb.text);
    }
    if (!!sliderData.ig) {
      handleSlider('instagram', sliderData.ig.range, sliderData.ig.text);
    }
    if (!!sliderData.wb) {
      handleSlider('website', sliderData.wb.range, sliderData.wb.text);
    }<?php
  } ?>
</script>

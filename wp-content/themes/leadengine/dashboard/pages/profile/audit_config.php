<?php
/**
 * Template Name: Audit config
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
  include(dirname(__FILE__)."/../../controllers/user_controller.php");
  include(dirname(__FILE__)."/../../models/user.php");

  // Import block titles
  include(dirname(__FILE__)."/../../assets/php/audit_blocks.php");

  // Cache busting
  include(dirname(__FILE__)."/../../assets/php/cache_version.php");

  $connection = new connection;
  $user_control   = new user_controller($connection);


  // Get audit by post_id
  $user = $user_control->get($user_id !== 0 ? $user_id : $author_id);

  $theme_color = $user->color_audit;

   // Overall scores
   $score = array(
    'fb' => 50,
    'ig' => 50,
    'wb' => 50
  );
  //
  // // Advice boxes
  $advice = array(
    'fb' => selectAdvice("", $score['fb'], $user, "fb"),
    'ig' => selectAdvice("", $score['ig'], $user, "insta"),
    'wb' => selectAdvice("", $score['wb'], $user, "website")
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

  function printValue($value, $is_icon = false, $requires_reload = false) {
    if ($is_icon) {
      return $value == 0 ?
      '<i class="fas fa-times" style="color: #c0392b; display: inline"></i>' :
      '<i class="fas fa-check" style="color: #27ae60; display: inline"></i>';
    }
    return $requires_reload ? '-' : $value;
  }

  function visibility_short_code($edit_mode, $visible, $name, $class = 'visibility') {
    if ($edit_mode) {
      $slash = $visible == 1 ? '' : '-slash';?>
      <div onclick="toggle_visibility('<?php echo $name; ?>')" id="<?php echo $name; ?>_icon" class="<?php echo $class; ?>">
        <i class="far fa-eye<?php echo $slash; ?>"></i>
      </div><?php
    }
  }

  function call_to_contact($phone, $mail, $calendar_link) { ?>
    <div class="info">
      <a href="callto:<?php echo $phone;?>"><i class="fas fa-phone"></i><?php echo $phone; ?></a>
      <a href="mailto:<?php echo $mail; ?>"><i class="fas fa-envelope"></i><?php echo $mail; ?></a>
      <?php
      if ($calendar_link != "") { ?>
        <a class="calendar" href="<?php echo $calendar_link; ?>"><i class="fas fa-calendar"></i>Make appointment</a><?php
      } ?>
    </div><?php
  }

  function change_tags($text) {
      // Client name -> #{client}
      if (strpos($text, '#{client}') !== false) {
            $text = str_replace('#{client}', "Example client", $text);
      }

      // Competitor name -> #{competitor}
      if (strpos($text, '#{competitor}') !== false) {
            $text = str_replace('#{competitor}', "Example competitor", $text);
      }

      // Facebook score -> #{fb_score}
      if (strpos($text, '#{fb_score}') !== false) {
            $text = str_replace('#{fb_score}', "100", $text);
      }

      // Instagram score -> #{instagram_score}
      if (strpos($text, '#{insta_score}') !== false) {
            $text = str_replace('#{insta_score}', "90", $text);
      }

      // Website score -> #{website_score}
      if (strpos($text, '#{website_score}') !== false) {
            $text = str_replace('#{website_score}', "80", $text);
      }

      return $text;
  }

  // $video_nothing = ($audit->video_iframe == NULL) ? 'checked' : '';
  // $video_iframe = ($audit->video_iframe != NULL) ? 'checked' : '';
  // $display_nothing = ($audit->video_iframe == NULL) ? 'style="display:block;"' : 'style="display:none;"';
  // $display_iframe = ($audit->video_iframe != NULL) ? 'style="display:block;"' : 'style="display:none;"';
  $company_name = get_user_meta($author_id, 'rcp_company', true );

  $post_url = htmlentities(base64_encode(get_site_url() . "/" . get_post_field( 'post_name', get_post() )));
  if ($_SERVER['SERVER_NAME'] == "dev.socialaudify.com") {
    $url = "https://crawl.socialaudify.com/pdf/" . $post_url;
  } else {
    $url = "https://livecrawl.socialaudify.com/pdf/" . $post_url;
  }

  $options = "";
  foreach($language as $key => $value) {
        if($user->language == NULL && $key == "Englisch") {
            $options .= "<option value='". $key ."' selected>". $key ."</option>";    
        } elseif($user->language == $key) {
            $options .= "<option value='". $key ."' selected >". $key ."</option>";           
        } else {
             $options .= "<option value='". $key ."' >". $key ."</option>";           
        }
  }
  
  $language_options = "<select style='margin-top: 7px;' id='language'>" . $options . "</select>";

  if($user->language == NULL) {
    $user->language = "Englisch";
  }

  $language = $language[$user->language];
?>
<head>
  <title>Audit config</title>
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
    $(document).ready(function() {
      $('#nav-icon2').click(function() {
        $(this).toggleClass('open');
        $('.mobile-hide').toggleClass('block');
      });


      // tutorial
      $('.yes-tut').click(function() {
          $('.into-tutorial').css("display", "none");
          $('.video-options').css({"z-index": "5555"});
          $('.video-explenation').css({"display": "block"});
          $('.video-button').css({"display": "block"});
      });

      $('.video-button').click(function() {
          $('.video-options').css({"z-index": "5"});
          $('.video-explenation').css({"display": "none"});
          $('.video-button').css({"display": "none"});
          $('.introtext-button').css({"display": "block"});
          $('.audit-intro').css({"z-index": "5555", "position": "relative"});
          $('html, body').animate({
                scrollTop: $(".audit-intro-text").offset().top
          }, 2000);
          $('.intro-explenation').css({"display": "block"});
      });

      $('.introtext-button').click(function() {
          $('.audit-intro').css({"z-index": "5"});
          $('.audit-intro').css({"z-index": "5"});
          $('.title-button').css({"display": "block"});
          $('.video-button').css({"display": "none"});
          $('.title-explenation').css({"display": "block"});
          $('.intro-explenation').css({"display": "none"});

          $('.facebook-inf-title').css({"z-index": "5555", "position": "relative"});
          $('.sub-title').css({"z-index": "5555", "position": "relative"});

          $('html, body').animate({
                scrollTop: $(".facebook-inf-title").offset().top
          }, 2000);
      });

      $('.title-button').click(function() {

          $('.facebook-inf-title').css({"z-index": "5", "position": "relative"});
          $('.sub-title').css({"z-index": "5", "position": "relative"});

          $('#facebook_vis_bit_icon').css({"z-index": "5555", "color": "#fff"});

          $('.title-button').css({"display": "none"});
          $('.introtext-button').css({"display": "none"});
          $('.visibility-button').css({"display": "block"});

          $('.title-explenation').css({"display": "none"});
          $('.visibility-explenation').css({"display": "block"});
      });

      $('.visibility-button').click(function() {
          $('#facebook_vis_bit_icon').css({"z-index": "5", "color": "#fff"});
          $('.title-button').css({"display": "none"});
          $('.visibility-button').css({"display": "none"});
          $('.between-button').css({"display": "block"});

          $('.social-text-explenation').css({"display": "block"});
          $('.visibility-explenation').css({"display": "none"});

          $('.config-right').css({"z-index": "5555"});

          $('html, body').animate({
                scrollTop: $(".config-right").offset().top
          }, 2000);
      });

      $('.between-button').click(function() {
          $('.between-button').css({"display": "none"});
          $('.visibility-button').css({"display": "none"});

          $('.social-text-explenation').css({"display": "none"});
          $('.end-explenation').css({"display": "block"});

          $('.config-right').css({"z-index": "5"});

          $('html, body').animate({
                scrollTop: $(".config-right").offset().top
          }, 2000);
      });

      $('.no-tut').click(function() {
          $('.tutorial-screen').css({"display": "none"});
      });

      $('.no-end-tut').click(function() {
          $('.tutorial-screen').css({"display": "none"});
      });

      $('.yes-end-tut').click(function() {
          $('.tutorial-screen').css({"display": "none"});
      });

      $('#tutorial_link').click(function() {
          console.log("Test");
          $('.tutorial-screen').css({"display": "block"});
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
    <?php if($edit_mode &&
            ($user->intro_audit == "" && $user->intro_conclusion == ""
            && $user->text_fb_1 == "" && $user->text_fb_2 == ""
            && $user->text_fb_3 == "" && $user->text_insta_1 == ""
            && $user->text_insta_2 == "" && $user->text_fb_3 == ""
            && $user->text_website_1 == "" && $user->text_website_2 == ""
            && $user->text_website_3 == "")) {
                $display = "block";

    } else {
        $display = "none";
    }?>

      <div class='tutorial-screen' style="display: <?php echo $display; ?>;">
          <div class="into-tutorial vertical-align" style="text-align: center;">
              <div style="height: auto; width: 340px; margin: 0 auto;">
                  <h2 style="color: #6e9d9c;">Do you want to follow a tutorial?</h2>
                  <p>We will walk you through all steps of the audit. You can start adding text right now as we go through the audit, this text will be shown as standard for every audit (but you can still change the individual text per audit of course).</p><br />
                  <br />
                  <div class="create-audit-button yes-tut" style="cursor: pointer;">Yes, recommended!</div>
                  <div class="create-audit-button no-tut" style="color: #fff !important; cursor: pointer; background: #c0392b; margin-left: 20px;">No</div>
              </div>
          </div>

          <div class="video-explenation vertical-align" style="text-align: center; left: 100px;">
              <span class="tut-title">Video explenation</span>
              <p>You can add a video at the top of every audit. This can be a video you uploaded to YouTube, or a Loom you recorded. In order to do this, you need to select 'video' and then paste the iframe code. Both YouTube and Loom offer this when you go to the 'share' option and select 'embed code'. Copy and past the code and the video will be added, adding a personal touch to your audit!</p>
          </div>

          <div class="intro-explenation vertical-align" style="text-align: center;">
              <span class="tut-title">Introduction explenation</span>
              <p>This is the standard introduction that will be shown on top of your audit. If you already use a video, you may also choose to hide the introductoin as your video may already be your introduction.</p>
          </div>

          <div class="title-explenation vertical-align" style="text-align: center;">
              <span class="tut-title">Facebook stats</span>
              <p>This will be the title of the sectoin about Facebook. Here you can choose the title which will be shown for this section, as well as the subtitle.</p>
          </div>

          <div class="visibility-explenation vertical-align" style="text-align: center;">
              <span class="tut-title">Visibility explenation</span>
              <p>You can turn off the visibility of a certain statistic by clicking on the eye. It will no longer be shown in the audit.</p>
          </div>

          <div class="social-text-explenation vertical-align" style="text-align: center; right: 300px">
              <span class="tut-title">Facebook Text</span>
              <p>When making an audit, you can give a score to someone's FaceBook. Using this option allows you to have a few standard texts appear when you give a certain score. For example: the first textblock will be shown if the score you give is between 0 and 30 (you can also change the percentage). The 2nd text block will be shown for scores between 30 and 70. The final text block will be shown for a score of 70 and above.</p>
          </div>

          <div class="end-explenation vertical-align" style="text-align: center;">
              <div style="height: auto; width: 340px; margin: 0 auto;">
                  <span class="tut-title" style="margin-bottom: 15px; line-height:23px;">This was a short tutorial on how to configurate your audits, want to see more check out our tutorial.</span>
                  <div style="clear: both; margin-bottom: 15px;"></div>
                  <a href="https://www.socialaudify.com/tutorial/" target="_blank" style="border: 0 !important;" rel="norefferer" class="create-audit-button yes-end-tut" style="cursor: pointer;">Yes, recommended!</a>
                  <div class="create-audit-button no-end-tut" style="color: #fff !important; border: 0 !important; cursor: pointer; background: #c0392b; margin-left: 20px;">No</div>
              </div>
          </div>

          <div class="create-audit-button video-button tut-button">Next</div>
          <div class="create-audit-button introtext-button tut-button">Next</div>
          <div class="create-audit-button title-button tut-button">Next</div>
          <div class="create-audit-button between-button tut-button" style="left: 40px; right: auto;">Next</div>
          <div class="create-audit-button visibility-button tut-button" >Next</div>
      </div>
  <div class="load-screen"><div class='lds-dual-ring'></div> <h3>Generating PDF, wait a minute.<h3></div>
    <div class="sub-header col-lg-12" style="display: block !important;">
    <!-- Animated CSS stuff -->
    <div id="nav-icon2">
      <span></span>
      <span></span>
      <span></span>
    </div>

    <?php
    if ($edit_mode) { ?>
      <button id="universal-update" class="advice-button floating-update"> Update </button><?php
    } ?>

    <div class="mobile-hide"><?php
      if ($edit_mode) { ?>
        <a href="/dashboard/" class="home-link"><i class="fas fa-th-large"></i> Dashboard </a><?php
      } ?>

      Audit config

      <?php if ($edit_mode) { ?>

        <button id="config_link" class="copy-link"> <i class="fas fa-cog"></i> Config </button>
        <a href="?preview_mode=True"; class="preview"><i class="far fa-eye"></i> Preview </a>
        <button id="tutorial_link" class="copy-link" style="margin-right: 10px; margin-bottom: 5px;"> <i class="fab fa-youtube"></i> Tutorial </button>
        <?php
      } else {
        if ($user_id == $author_id) {?>
          <a href="?preview_mode=False"; class="edit"><i class="far fa-eye"></i> Edit </a><?php
        }
      } ?>
    </div>
  </div>

  <div id="configModal" class="modal"></div>
  <div id="confirmModal" class="modal"></div>
  <div id="reloadModal" class="modal"></div>
  <div id="errorModal" class="modal"></div>
  <section class="content white custom-content min-height">
    <input type="text" class="offscreen" aria-hidden="true" name="public_link" id="public_link" value=<?php echo "https://".$env."/public/".$slug; ?> />
    <?php
    if ($user->std_iframe != NULL && $user->std_iframe != "") { ?>
      <div class="intro-video"><?php
        $video = str_replace("&#34;", '"', stripslashes($user->std_iframe));

        if(strpos($video, 'height') !== false) {
            echo "<iframe ". $video ."</iframe>";
        } ?>
      </div><?php
    } else if ($user->std_iframe != "" || $edit_mode) { ?>
      <div class="intro-video"></div><?php
    }

    if ($edit_mode) { ?>
      <div class="video-options">
        <h3>Video banner:</h3>
        <span class="eplenation-banner">You can add a video on top of your audit by adding the iframe link here. Click <a href="tutorial/#1570543881921-3fd7746a-9da5">[here]</a> to learn how to find this link.</span>
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="banner-form" method="post" enctype="multipart/form-data">
          <input type="radio" class="iframe-radio" data-display="block" <?php echo ($user->std_iframe != NULL && $user->std_iframe != "") ? 'checked' : ''; ?>/>
            <span class="radio-label">Video</span>
          <input type="radio" class="iframe-radio" id="std_iframe" value="" name="std_iframe" data-display="none" <?php echo ($user->std_iframe == NULL || $user->std_iframe == "") ? 'checked' : ''; ?>/>
            <span class="radio-label">Nothing</span>
          <input type="text" id="iframe-input" placeholder="Insert iframe(Loom/Youtube etc.)" style="display:<?php echo ($user->std_iframe != NULL) ? 'block' : 'none'; ?>"
            pattern="(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))" value='<?php echo $user->std_iframe != NULL ? '<iframe '.stripslashes($user->std_iframe).'</iframe>' : ''; ?>'/>
          </div>
        </form>
      </div><?php
    } ?>

    <?php visibility_short_code($edit_mode, $user->introduction_vis_bit, 'introduction_vis_bit', 'visibility-first-level'); ?>

    <div class="audit-intro<?php echo ($user->std_iframe != NULL && $user->std_iframe != "") ? " with-video" : ""; ?> col-lg-10 col-lg-offset-2">
     <?php if($user->picture_vis_bit == 1 || $edit_mode) { ?>
          <div class="client-profile-picture">
            <?php echo get_avatar($author_id, 32); ?>
            <?php visibility_short_code($edit_mode, $user->picture_vis_bit, 'picture_vis_bit', 'custom-visibility'); ?>
          </div>
      <div class="audit-intro-text">
        <span class="audit-company-name"><?php echo ($company_name != "") ? $company_name : $author->display_name; ?></span><?php
        } else { echo '<div class="audit-intro-text">'; }

        if($user->introduction_vis_bit == 1 || $edit_mode) {
            if ($edit_mode) { ?>
              <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#introduction" method="post" enctype="multipart/form-data">
                <textarea maxlength="999" input="text"  name="intro_audit" id="intro_audit" style="background: #f5f6fa;"><?php echo $user->intro_audit; ?></textarea>
              </form>
              <div class="description-tags">
                  You can insert the following tags in all the text fields: <span style="color: #000;">#{client}, #{competitor}, #{fb_score}, #{insta_score}, #{website_score}</span>
              </div> <?php
            } else { ?>
              <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php  echo "<pre>" . change_tags($user->intro_audit) . "</pre>";  ?></p><?php
            }
        } else { echo '</div>'; }?>
      </div>
  </div><?php
    if ($user->facebook_vis_bit || $edit_mode) { ?>
      <div class="col-lg-12 facebook-info" id="facebook-info">
        <?php if($user->facebook_title != NULL) { $facebook_title = $user->facebook_title; } else { $facebook_title = $language['fb_title'] . ":"; } ?>
        <?php if($user->facebook_sub_title != NULL) { $facebook_sub_title = $user->facebook_sub_title; } else { $facebook_sub_title = $language['fb_subtitle']; } ?>

        <span class="facebook-inf-title"><span class="round facebook"><i class="fab fa-facebook-f"></i></span> &nbsp;
            <?php if(!$edit_mode) { ?>
                <?php echo $facebook_title; ?>
            <?php } else { ?>
                <input type="text" maxlength="40" name="facebook_title" id="facebook_title" value="<?php echo $facebook_title; ?>" />
            <?php } ?>
        </span>

        <span class="sub-title">
            <?php if(!$edit_mode) { ?>
                <?php echo $facebook_sub_title; ?>
            <?php } else { ?>
                <input maxlength="40" type="text" name="facebook_sub_title" id="facebook_sub_title" value="<?php echo $facebook_sub_title; ?>" />
            <?php } ?>
        </span>
        <?php
        visibility_short_code($edit_mode, $user->facebook_vis_bit, 'facebook_vis_bit', 'visibility-first-level'); ?>

        <div class="col-lg-6 left bottom-40">
          <div class="inner"><?php
            foreach ($facebook_blocks as $item) {
              if (show_block($edit_mode, $user->{$item["type"]})) { ?>
                <div class="stat-block col-lg-6 col-md-12">
                  <div class="inner">
                    <span class="title-box facebook"><?php echo $language[$item["name"]]; ?></span>
                    <span class="data_animation"><?php
                        echo printValue(0, $item['is_icon']);
                     ?>
                    </span>
                    <span class="explenation"><?php echo $language[$item["name"] . " exp"]; ?></span><?php
                      visibility_short_code($edit_mode, $user->{$item["type"]}, $item["type"]); ?>
                  </div>
                </div><?php
              }
            }
            foreach ($facebook_ad_blocks as $item) { ?>
                <div class="stat-block col-lg-6" id="fb_ads">
                  <div class="inner">
                    <span class="title-box facebook"><?php echo $language[$item["name"]]; ?></span><?php
                    // preview mode
                    if (!$edit_mode) {
                      $class = 0 ? "check" : "times";
                      $color = 0 ? "#27ae60" : "#c0392b"; ?>

                      <span class="explenation"><?php echo $language[$item["name"] . " exp"]; ?></span>
                      <span class="data_animation">
                        <i class='fas fa-<?php echo $class; ?>' style='color: <?php echo $color; ?>'></i>
                      </span><?php
                    // edit mode
                    } else { ?>
                      <form class="ads-radio" action="">
                        <input type="radio"  value="yes" <?php echo 0 ? "checked" : ""; ?>/>
                          <span class="label_ads">Yes</span>
                        <input type="radio" value="no" <?php echo !0 ? "checked" : ""; ?>/>
                          <span class="label_ads">No</span>
                      </form><?php
                        visibility_short_code($edit_mode, $user->{$item["type"]}, $item["type"]); ?>
                      <span class="explenation-ads">
                        <a target="_blank" rel="noreferrer" href="<?php echo 'https://www.facebook.com/pg/'. $path->facebook_name .'/ads/'; ?>">
                          Click here to watch if this page is currently running ads. (This can't be automated)
                        </a>
                      </span><?php
                    } ?>
                  </div>
                </div><?php
            } ?>
          </div>
        </div>
        <?php if(!$edit_mode) { ?>
        <div class="col-lg-6 right">
          <div class="inner custom-inner">
            <div class="score col-lg-12">
              <div class="inner custom-text">
                <span class="score-tag"><?php echo $language['score']; ?></span><?php
                if ($edit_mode) { ?>
                  <span class="score-text"><span id="facebook_value"></span>%</span>
                  <div class="slidecontainer">
                    <input type="range" min="1" max="100" value="50" class="slider" id="facebook_score">
                  </div><?php
                } else { ?>
                  <span class="score-text"><?php echo $score['fb']; ?>%</span><?php
                } ?>

                <span class="advice-title"><?php echo $language['facebook_advice']; ?></span><?php
                if ($edit_mode) { ?>
                  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#facebook-info" method="post" enctype="multipart/form-data">
                    <textarea maxlength="999" input="text"  name="facebook_advice" id="facebook_advice"><?php echo $advice['fb']; ?></textarea>
                  </form><?php
                } else { ?>
                  <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo "<pre>" . change_tags($advice['fb']) . "</pre>"; ?></p><?php
                  call_to_contact($phone, $author->user_email, $calendar_link);
                } ?>
              </div>
            </div>
          </div>
        </div>
      <?php } else { ?>
          <div class="col-lg-6 right config-right" style="padding-left: 15px; padding-right: 15px;">
              <h2 class="config-title">Facebook text</h2>
              <div class="diplay-facebook-error" style="margin-top: 10px;"></div>
              <?php

              $ranges_fb = (object) array(
                ["name" => "facebook", "code" => "fb", "db" => "fb"]
              );
                $item = (object) $ranges_fb; ?>
                <?php
                  for ($i = 1; $i <= 3; $i++) {
                    if ($i < 3) { ?>
                      <h6>Show this text up to the selected range, making it faster to create an audit</h6>
                      <input maxlength="2" type="text" id="<?php echo "range_number_fb_$i"; ?>" name="<?php echo "range_number_fb_$i"; ?>" placeholder="<?php echo $i * 30; ?>"
                      value="<?php echo $user->{"range_number_fb_$i"}; ?>"><?php
                    } else { ?>
                      <h6>The last range is less than or equal to 100 percent</h6><?php
                    } ?>
                    <textarea maxlength="999" input="text" id="<?php echo "text_fb_$i"; ?>" name="<?php echo "audit_facebook_$i"; ?>"><?php
                      echo $user->{"text_fb_$i"}; ?></textarea><?php
                    }
                ?>
          </div>
      <?php } ?>
      </div><?php
    }
    if ($user->instagram_vis_bit || $edit_mode) { ?>
      <div class="col-lg-12 facebook-info" id="instagram-info">
        <?php if($user->instagram_title != NULL) { $instagram_title = $user->instagram_title; } else { $instagram_title = $language['insta_title']; } ?>
        <?php if($user->instagram_sub_title != NULL) { $instagram_sub_title = $user->instagram_sub_title; } else { $instagram_sub_title = $language['insta_subtitle']; } ?>

        <span class="facebook-inf-title"><span class="round instagram"><i class="fab fa-instagram"></i></span> &nbsp;
            <?php if(!$edit_mode) { ?>
                <?php echo $instagram_title; ?>
            <?php } else { ?>
                 <input type="text" maxlength="40" name="instagram_title" id="instagram_title" value="<?php echo $instagram_title; ?>" />
            <?php } ?>
        </span>

        <span class="sub-title">
            <?php if(!$edit_mode) { ?>
                <?php echo $instagram_sub_title; ?>
            <?php } else { ?>
                <input maxlength="40" type="text" name="instagram_sub_title" id="instagram_sub_title" value="<?php echo $instagram_sub_title; ?>" />
            <?php } ?>
        </span>

        <?php
        visibility_short_code($edit_mode,  $user->instagram_vis_bit, 'instagram_vis_bit', 'visibility-first-level'); ?>

        <div style="clear:both"></div>
        <div class="col-lg-6 instagram-left" style="float:left;">

            <div class="col-lg-12 left custom-left" style="padding: 0;"><?php
              visibility_short_code($edit_mode, $user->insta_hashtag, 'insta_hashtag'); ?>

              <div class="chart-info">
                <span class="stat-box-title"><?php echo $language['hastag_used']; ?></span>
                <span class="graph-procent" style="margin-top: 4px;"><?php echo $language['hastag_most_used']; ?> 'example'</span>
              </div>
              <div class="inner custom-inner" style="padding: 0;">
                <canvas id="hashtag-chart" class="chart-instagram"  style="height: 292px;"></canvas>
              </div>

              <div class="legend">
                  <span class="round-color you-color"></span> <span class="space">You</span>
              </div>
            </div><?php

          if (show_block($edit_mode, $user->insta_lpd)) { ?>
            <div class="col-lg-12 left custom-left" style="padding: 0;"><?php
              visibility_short_code($edit_mode, $user->insta_lpd, 'insta_lpd'); ?>

              <div class="chart-info">
                <span class="stat-box-title"><?php echo $language['likes_on_post']; ?></span>
                <span class="graph-procent" style="margin-top: 2px;"><?php echo $language['average']; ?> 50</span>
                <span class="graph-info">

                </span>
              </div>
              <div class="inner custom-inner" style="">
                <canvas id="lpd-chart" class="chart-instagram"  style="height: 292px;"></canvas>
              </div>
              <div class="legend">
                  <span class="round-color you-color"></span> <span class="space">You</span>

              </div>
            </div><?php
          } ?>
        </div>


        <div class="col-lg-6 instagram-right" style="padding: 0;float: right;">
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#instagram-info" style="width: 100%; float:left;" method="post" enctype="multipart/form-data" id="manual-ig-form"><?php


        function competitor_code($audit, $edit_mode, $item, $language) {
          // Preview mode hide description animation
            ?>
                <span class="data_animation"><?php
                echo 0; ?>
                </span>
                <span class="explenation"><?php echo $language[$item["name"] . " exp"]; ?></span>
            <?php
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
          if (show_block($edit_mode, $user->{$item["type"]})) { ?>
            <div class="stat-block col-lg-6" id="<?php echo $item['type']; ?>">
              <div class="inner">
                <span class="title-box instagram"><?php
                  echo $language[$item["name"]]; ?>
                </span><?php
                // Als preview mode laat description staan en hide client info

                competitor_code($user, $edit_mode, $item, $language);
                // preview mode show visibility icon
                visibility_short_code($edit_mode, $user->{$item["type"]}, $item["type"]); ?>

              </div>
            </div><?php
          }
        }?>
        </form>

        <div class="col-lg-12 instagram-score" style="float:right; "><?php


          if(!$edit_mode) { ?>
            <div class="col-lg-12 insta-score">
              <div class="col-lg-12 align">
                <span class="score-tag insta-advice-tag"><?php echo $language['score']; ?></span><?php
                if ($edit_mode) { ?>
                  <span class="score-text"><span id="instagram_value"></span>%</span>
                  <div class="slidecontainer">
                    <input type="range" min="1" max="100" value="50" class="slider" id="instagram_score">
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
                  <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo "<pre>" . change_tags($advice['ig']) . "</pre>"; ?> </p>
                  <?php
                  call_to_contact($phone, $author->user_email, $calendar_link);
                } ?>
            </div>
          </div>
          <?php } else { ?>
              <div class="col-lg-12 insta-score config-right" id="instagram-info" style="padding: 20px;">
                  <h2 class="config-title">Instagram text</h2>
                  <div class="diplay-instagram-error" style="margin-top: 10px;"></div>
                  <?php

                    $ranges_fb = (object) array(
                        ["name" => "instagram", "code" => "ig", "db" => "insta"]
                    );
                    $item = (object) $ranges_fb; ?>
                    <?php
                      for ($i = 1; $i <= 3; $i++) {
                        if ($i < 3) { ?>
                          <h6>Show this text up to the selected range, making it faster to create an audit</h6>
                          <input maxlength="2" type="text" id="<?php echo "range_number_insta_$i"; ?>" name="<?php echo "range_number_insta_$i"; ?>" placeholder="<?php echo $i * 30; ?>"
                          value="<?php echo $user->{"range_number_insta_$i"}; ?>"><?php
                        } else { ?>
                          <h6>The last range is less than or equal to 100 percent</h6><?php
                        } ?>
                        <textarea maxlength="999" input="text" id="<?php echo "text_insta_$i"; ?>" name="<?php echo "audit_insta_$i"; ?>"><?php
                          echo $user->{"text_insta_$i"}; ?></textarea><?php
                        }
                    ?>
                </div>
          <?php } ?>
          </div>
        </div>
      </div><?php
    }
    if ($user->website_vis_bit || $edit_mode) { ?>
      <div class="col-lg-12 facebook-info website-info" id="website-info">

          <?php if($user->website_title != NULL) { $website_title = $user->website_title; } else { $website_title = $language['website_title']; } ?>
          <?php if($user->website_sub_title != NULL) { $website_sub_title = $user->website_sub_title; } else { $website_sub_title = $language['website_subtitle']; } ?>

          <span class="facebook-inf-title"><span class="round website">W</span> &nbsp;
              <?php if(!$edit_mode) { ?>
                  <?php echo $website_title; ?>
              <?php } else { ?>
                  <input type="text" maxlength="40" name="website_title" id="website_title" value="<?php echo $website_title; ?>" />
              <?php } ?>
           </span>
           <span class="sub-title">
              <?php if(!$edit_mode) { ?>
                  <?php echo $website_sub_title; ?>
              <?php } else { ?>
                  <input maxlength="40" type="text" name="website_sub_title" id="website_sub_title" value="<?php echo $website_sub_title; ?>" />
              <?php } ?>
          </span>


        <?php visibility_short_code($edit_mode, $user->website_vis_bit, 'website_vis_bit', 'visibility-first-level'); ?>
        <div class="col-lg-6 left" style="background: transparent; border: 0; margin-top: 0;">
          <div class="inner custom-inner"><?php

            foreach ($website_blocks as $item) {
              if (show_block($edit_mode, $user->{$item["type"]})) { ?>
                <div class="stat-block col-lg-6" id="<?php echo $item['type']; ?>">
                  <div class="inner">
                    <span class="title-box website"><?php echo $language[$item["name"]]; ?></span>
                    <span class="data_animation"><?php
                      echo 0; ?>
                    </span><?php
                      visibility_short_code($edit_mode, $user->{$item["type"]}, $item["type"]); ?>
                    <span class="explenation"><?php echo $language[$item["name"]]; ?></span>
                  </div>
                </div><?php
              }
            } ?>
          </div>
        </div>
        <?php if(!$edit_mode) { ?>
        <div class="col-lg-6 right instagram-right" style="padding: 20px 20px; margin-top: 35px !important;">
              <span class="score-tag website-advice-tag"><?php echo $language['score']; ?></span><?php
              if ($edit_mode) { ?>
                <span class="score-text"><span id="website_value"></span>%</span>
                <div class="slidecontainer">
                  <input type="range" min="1" max="100" value="50" class="slider" id="website_score">
                </div>
                <span class="advice-title margin-advice-title"><?php echo $language['website_advice']; ?></span>
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#website-info" method="post" enctype="multipart/form-data">

                  <textarea maxlength="999" input="text"  name="website_advice" id="website_advice"><?php echo "<pre>" . change_tags($advice['wb']) . "</pre>"; ?></textarea>
                </form><?php
              } else { ?>
                <span class="score-text"><?php echo $score['wb']; ?>%</span>
                <span class="advice-title margin-advice-title"><?php echo $language['website_advice']; ?></span>
                <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo $advice['wb']; ?></p>
                <?php
                call_to_contact($phone, $author->user_email, $calendar_link);
            } ?>
        </div>
        <?php } else { ?>
            <div class="col-lg-6 right instagram-right config-right" id="website-info" style="padding: 20px !important; margin-top: 35px !important;">
                <h2 class="config-title">Website text</h2>
                <div class="diplay-website-error" style="margin-top: 10px;"></div>
                <?php

                  $ranges_fb = (object) array(
                      ["name" => "website", "code" => "wb", "db" => "website"]
                  );
                  $item = (object) $ranges_fb; ?>
                  <?php
                    for ($i = 1; $i <= 3; $i++) {
                      if ($i < 3) { ?>
                        <h6>Show this text up to the selected range, making it faster to create an audit</h6>
                        <input maxlength="2" type="text" id="<?php echo "range_number_website_$i"; ?>" name="<?php echo "range_number_website_$i"; ?>" placeholder="<?php echo $i * 30; ?>"
                        value="<?php echo $user->{"range_number_website_$i"}; ?>"><?php
                      } else { ?>
                        <h6>The last range is less than or equal to 100 percent</h6><?php
                      } ?>
                      <textarea maxlength="999" input="text" id="<?php echo "text_website_$i"; ?>" name="<?php echo "audit_website_$i"; ?>"><?php
                        echo $user->{"text_website_$i"}; ?></textarea><?php
                      }
                  ?>
            </div>
        <?php } ?>
      </div><?php
    } ?>
  </section>
  <?php if($user->conclusion_vis_bit == 1 || $edit_mode) { ?>
      <section class="audit-conclusion col-lg-12">
        <?php visibility_short_code($edit_mode, $user->conclusion_vis_bit, 'conclusion_vis_bit', 'visibility-first-level'); ?>

        <div class="left-conlusion col-lg-7">
          <h3><?php echo $language['conclusion']; ?></h3>
          <hr class="under-line" />
          <div style="clear:both"></div><?php
          if ($edit_mode) { ?>
            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#conclusion" method="post" enctype="multipart/form-data">
              <textarea maxlength="999" input="text"  name="conclusion_audit" id="conclusion_audit"><?php
                echo $user->conclusion_audit;
              ?></textarea>
            </form><?php
          } else { ?>
            <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php
              echo "<pre>" . change_tags($user->conclusion_audit) . "</pre>";
            ?></p><?php
          } ?>
        </div>
      </section>
  <?php } ?>

  <div class="footer">
    <span class="phone-number"><?php echo $language['phone_number']; ?>: <a href="callto:<?php echo $phone; ?>"><?php echo $phone; ?></a></span>
    <span class="mailadres"><?php echo $language['email']; ?>: <a href="mailto:<?php echo $author->user_email; ?>"><?php echo $author->user_email; ?></a></span><?php
    if ($calendar_link != "") { ?>
      <a class="calendar" href="<?php echo $calendar_link; ?>"><i class="fas fa-calendar"></i><?php echo $language['make_appointment']; ?></a><?php
    } ?>
  </div>
</body>
</html>

<script charset='utf-8'>
  var commonPost = {
    'type': 'user',
    'user': '<?php echo $user_id; ?>',
  }


    // Line Chart values
    var data_array = [[131,80,74,32,32,78,49,37,53,93,54,86,50,153,77,104,92,104,44,123,74,54,78,52,69]];
    // Bar Chart values
    var bar_labels = [["Social media","SMMT","Social Audify","Example"]];
    var bar_data = [[23,23,22,20]];

    var allLines = Array(Math.max(data_array[0].length, 12)).fill().map((_, index) => index);
    generateChart('lpd-chart', data_array, allLines, [true, true]);
    generateAreaChart('hashtag-chart', bar_data, bar_labels);

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
          data: { action: 'toggle_visibility', type_table: 'audit', field: field_name, ...commonPost },
          success: function (response) {
              console.log(response);
              field.html(icon)
          },
          error: function (xhr, textStatus, errorThrown) {
              var send_error = error_func(xhr, textStatus, errorThrown, null);
              logError(send_error, 'page-templates/audit_page.php', 'toggle_visibility');
          },
        });
      }
    };

    $(function() {
       $("#picture_vis_bit_icon").hover(function(){
           $('.client-profile-picture').css("opacity", "0.4");
           $('.audit-company-name').css("opacity", "0.4");
       });

       $( "#picture_vis_bit_icon" ).mouseleave(function() {
           $('.client-profile-picture').css("opacity", "1");
           $('.audit-company-name').css("opacity", "1");
       });

       $("#introduction_vis_bit_icon").hover(function(){
           $('#intro_audit').css("opacity", "0.4");
       });

       $( "#introduction_vis_bit_icon" ).mouseleave(function() {
           $('#intro_audit').css("opacity", "1");
       });

       $("#conclusion_vis_bit_icon").hover(function(){
           $('.left-conlusion').css("opacity", "0.4");
       });

       $("#conclusion_vis_bit_icon" ).mouseleave(function() {
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
      $("textarea, input[type=text]").on('keyup paste change', function() {
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
          if (value != '<?php echo $user->std_iframe; ?>') {
            return { "std_iframe" : value };
          }
        }
        return { "video_iframe" : '' };
      }

      function updateAll() {
          $('.diplay-website-error').empty();
          $('.diplay-instagram-error').empty();
          $('.diplay-facebook-error').empty();

          if ($('#range_number_fb_1').val() >= $('#range_number_fb_2').val()) {
                document.querySelector('#facebook-info').scrollIntoView({
                  behavior: 'smooth'
                });

              $('.diplay-facebook-error').append("<span style='display: block; color: red; font-size: 14px;'>The first range number always has to be smaller than the second one.</span>");
              return;
          }

          if ($('#range_number_insta_1').val() >= $('#range_number_insta_2').val()) {
              document.querySelector('#instagram-info').scrollIntoView({
                behavior: 'smooth'
              });

              $('.diplay-instagram-error').append("<span style='display: block; color: red; font-size: 14px;'>The first range number always has to be smaller than the second one.</span>");
              return;
          }
          if ($('#range_number_website_1').val() >= $('#range_number_website_2').val()) {
              document.querySelector('#website-info').scrollIntoView({
                behavior: 'smooth'
              });

              $('.diplay-website-error').append("<span style='display: block; color: red; font-size: 14px;'>The first range number always has to be smaller than the second one.</span>");
              return;
          }

        var data = {
          ...getChanged('textarea'),
          ...getChanged("input[type=text]", true),
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
              $('.intro-video').html(`<iframe${data.std_iframe}</iframe>`);
            },
            error: function (xhr, textStatus, errorThrown) {
              var send_error = error_func(xhr, textStatus, errorThrown, data);
              logError(send_error, 'page-templates/audit_page.php', 'updateAll');
            }
          });
        }
      }

      // Auto Mail + color Model
      var modalData = {
        text:`<span style="font-weight:bold; display:block; font-size: 18px; margin-bottom: 10px;">Configuration audit</span>`,
        subtext:`Do you want to sent this client automatic reminders?<br/>
          <input type="checkbox" id="std_mail_bit" <?php echo $user->std_mail_bit ? 'checked': ''; ?>><br><br>
          Social Audify can send automatic reminders if your lead does not open the audit. You can configure the emails
          <a style="margin-bottom:10px" href='/profile-page/#mail-settings'>[here]</a>.<br><br>
          Do you want a custom color for this audit?<br>
          Theme color: <input type="color" id="color" value="<?php echo $theme_color; ?>">
          <i class="fas fa-undo" onclick="$('#color').val('#6e9d9c')" ></i><br /><br />
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
            flag: 'audit',
            language: $("#language :selected").val(),
            action: 'update_config', color: $('#color').val(),
            value: $("#std_mail_bit").is(':checked'), ...commonPost,
            user_id: <?php echo $user_id; ?>
          },
          success: function() {
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
        update_ads(this.value, competitor = false);
      });

      $('input:radio[name=ads_c]').change(function () {
        update_ads(this.value, competitor = true);
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

    <?php
  } ?>
</script>

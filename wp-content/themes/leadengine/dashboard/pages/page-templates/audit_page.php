<!DOCTYPE html>
<html lang="en" style="overflow-y: scroll;">

<?php
  $post_id = get_the_ID();
  $author_id = (int)get_post_field('post_author', $post_id);
  $user_id = get_current_user_id();
  $env = getenv('HTTP_HOST');
  $slug = get_post_field("post_name", $post_id);
  $leadengine = get_template_directory_uri();

  // Get Author data
  $phoneMeta =  get_user_meta($author_id, 'rcp_number');
  $phone = isset($phoneMeta[0]) ? $phoneMeta[0] : "";
  $author = get_userdata($author_id);

  // Mode check
  $edit_mode = !(isset($_GET['preview_mode']) && $_GET['preview_mode'] == "True") ?
                ($user_id == $author_id || $user_id == 2) : false;

  // Import controllers & models
  include(dirname(__FILE__)."/../../services/connection.php");
  include(dirname(__FILE__)."/../../controllers/audit_controller.php");
  include(dirname(__FILE__)."/../../controllers/user_controller.php");

  include(dirname(__FILE__)."/../../models/audit.php");
  include(dirname(__FILE__)."/../../models/user.php");

  // Import block titles
  include(dirname(__FILE__)."/../../assets/php/audit_blocks.php");

  $connection = new connection;
  $user_control   = new user_controller($connection);
  $audit_control  = new audit_controller($connection);

  // Get audit by post_id
  $id = $audit_control->get_id($post_id);
  $audit = $audit_control->get($id);
  $user = $user_control->get($user_id !== 0 ? $user_id : $author_id);

  // Define authority hash
  $auth_hash = hash('sha256', 'what'.$post_id.'who'.$audit->id.'how'.$user_id);

  if ($audit->manual == 0) {
    $sumPostLikes = $audit->instagram_bit == "1" ? array_sum($audit->instagram_data->likesPerPost) : NULL;
  }

  // Post handling
  if (isset($_POST['iframe']) && $edit_mode) {
    $value=  ($_POST['video-option'] == 'nothing') ? NULL : base64_encode($_POST['iframe']);
    $audit->update('video_iframe', $value, 'Audit_template');
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

  $post_names =  ['introduction', 'conclusion', 'facebook_advice',
                  'instagram_advice','website_advice', 'facebook_score',
                  'instagram_score', 'website_score'];

  foreach ($post_names as $post_name) {
    if (isset($_POST[$post_name]) && $edit_mode) {
      $audit->update($post_name, sanitize_textarea_field(stripslashes($_POST[$post_name])), 'Audit_template');
    }
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

  function getWebIcon($value, $has_website) {
    if ($has_website && $value === '0') {
      return '<i class="fas fa-times" style="color: #c0392b; display: inline"></i>';
    } elseif ($has_website && $value === '1') {
      return '<i class="fas fa-check" style="color: #27ae60; display: inline"></i>';
    } else {
      return $value;
    }
    return "";
  }

  function getWebIconFacebook($value, $is_icon) {
    if ($is_icon && $value == 0) {
      return '<i class="fas fa-times" style="color: #c0392b; display: inline"></i>';
    } elseif ($is_icon && $value == 1) {
      return '<i class="fas fa-check" style="color: #27ae60; display: inline"></i>';
    }

    return $value;
  }

  function visibility_short_code($edit_mode, $visible, $item_type) {
    if ($edit_mode) {
      $slash = $visible == 1 ? '' : '-slash';?>
      <div onclick="toggle_visibility('<?php echo $item_type; ?>')" id="<?php echo $item_type; ?>_icon" class="visibility">
        <i class="far fa-eye<?php echo $slash; ?>"></i>
      </div><?php
    }
  }

  $video_nothing = ($audit->video_iframe == NULL) ? 'checked' : '';
  $video_iframe = ($audit->video_iframe != NULL) ? 'checked' : '';
  $display_nothing = ($audit->video_iframe == NULL) ? 'style="display:block;"' : 'style="display:none;"';
  $display_iframe = ($audit->video_iframe != NULL) ? 'style="display:block;"' : 'style="display:none;"';
?>
<head>
  <title>Audit</title>
  <!-- TODO: Moet nog met chrome canary worden gecheckt... -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <link rel="stylesheet" href="<?php echo $leadengine; ?>/dashboard/assets/styles/dashboard.css" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/modal.js"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/functions.js"></script>
  <script>var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';</script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    $(document).ready(function() {
      $('#nav-icon2').click(function() {
        $(this).toggleClass('open');
        $('.mobile-hide').toggleClass('block');
      });
    });
  </script>
</head>
<body class="custom-body">
    <div class="sub-header col-lg-12" style="display: block !important;">
    <!-- Animated CSS stuff -->
    <div id="nav-icon2">
      <span></span>
      <span></span>
      <span></span>
    </div>

    <?php
    if ($edit_mode) { ?>
      <button id="universal-update" class="advice-button floating-update">
        Update All
      </button> <?php
    } ?>

    <div class="mobile-hide"><?php
        if ($edit_mode) { ?>
          <a href="/dashboard/" class="home-link"><i class="fas fa-th-large"></i> Dashboard </a><?php
        } ?>

        Audit: <?php echo $audit->name;

        if ($edit_mode) { ?>
          <div id="delete-this-audit"> <i class="fas fa-trash"></i> </div>
          <button id="copy_link" class="copy-link"> <i class="fas fa-share-alt-square"></i> Share & Track </button>
          <button id="mail_link" class="copy-link"> <i class="fas fa-cog"></i> Mail </button>
          <a href="?preview_mode=True"; class="preview"><i class="far fa-eye"></i> Preview </a><?php
        } else { ?>
          <a href="?preview_mode=False"; class="edit"><i class="far fa-eye"></i> Edit </a><?php
        } ?>
    </div>
  </div>

  <div id="shareModal" class="modal"></div>
  <div id="mailModal" class="modal"></div>
  <div id="confirmModal" class="modal"></div>
  <div id="errorModal" class="modal"></div>
  <div id="crawlModal" class="modal"></div>

  <section class="content white custom-content min-height">
    <!-- TODO: hidden? -->
    <div class="call-to-action-container">
      <a href="callto:<?php echo $phone; ?>" class="call-to-call"><i class="fas fa-phone"></i></a>
      <a href="mailto:<?php echo $author->user_email; ?>" class="call-to-mail"><i class="fas fa-envelope"></i></a>
    </div>
    <input type="text" class="offscreen" aria-hidden="true" name="public_link" id="public_link"
           value=<?php echo "https://".$env."/public/".$slug; ?> />

    <?php 
    if ($audit->video_iframe != NULL) { ?>
      <div class="intro-video"><?php
        echo "<iframe ". stripslashes(base64_decode($audit->video_iframe)) ."</iframe>"; ?>
      </div><?php
    } else if ($audit->video_iframe != "" || $edit_mode) { ?>
      <div class="intro-video"></div><?php
    }

    if ($edit_mode) { ?>
      <div class="video-options">
        <h3>Video banner:</h3>
        <span class="eplenation-banner">You can add a video on top of your audit by adding the iframe link here. Click <a href="https://www.google.nl">[here]</a> to learn how to find this link.</span>
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="banner-form" method="post" enctype="multipart/form-data">
          <input type="radio" name="video-option" id="iframe-option" <?php echo $video_iframe; ?> value="video"/> <span class="radio-label">Iframe</span>
          <input type="radio" name="video-option" id="nothing-option" <?php echo $video_nothing; ?> value="nothing"/> <span class="radio-label">Nothing</span>
          <div id="iframe-input" <?php echo $display_iframe; ?> >
            <input type="text" id="iframe-input" name="iframe" placeholder="Insert iframe(Loom/Youtube etc.)" pattern="(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))"
                   value='<?php if ($audit->video_iframe != NULL) { echo '<iframe '. stripslashes(base64_decode($audit->video_iframe)) .'</iframe>'; }?>'/>
          </div>
          <input type="submit" value="Update" class="advice-button">
        </form>
      </div><?php
    }

    $classType = ($audit->video_iframe != NULL && $audit->video_iframe != "") ? " with-video" : ""; ?>
    <div class="audit-intro<?php echo $classType; ?> col-lg-10 col-lg-offset-2">
      <div class="client-profile-picture">
        <?php echo get_avatar($author_id, 32); ?>
      </div>
      <div class="audit-intro-text">
        <span class="audit-company-name"><?php echo $author->display_name;?></span><?php
        if ($edit_mode) { ?>
          <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#introduction" method="post" enctype="multipart/form-data">
            <textarea maxlength="999" input="text"  name="introduction" id="introduction" style="background: #f5f6fa;"><?php if ($audit->introduction == NULL) { echo $user->intro_audit; } else { echo $audit->introduction; } ?></textarea>
          </form><?php
        } else { ?>
          <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php if ($audit->introduction == NULL) { echo $user->intro_audit; } else { echo $audit->introduction; } ?></p><?php
        } ?>
      </div>
    </div><?php
    if ($audit->facebook_bit == "1") { ?>
      <div class="col-lg-12 facebook-info" id="facebook-info">
        <span class="facebook-inf-title"><span class="round facebook"><i class="fab fa-facebook-f"></i></span> &nbsp; Facebook stats:</span>
        <span class="sub-title">Statistics of your Facebook page.</span>
        <div class="col-lg-6 left bottom-40">
          <div class="inner"><?php
            foreach ($facebook_blocks as $item) {
              if (show_block($edit_mode, $audit->{$item["type"]})) { ?>
                <div class="stat-block col-lg-6 col-md-12">
                  <div class="inner">
                    <span class="title-box facebook"><?php echo $item["name"]; ?></span>
                    <span class="data_animation"><?php
                    if ($audit->has_comp) { ?>
                      <span class="data-view"><span class="comp-label">You: <br />
                      </span><?php echo getWebIconFacebook(round($audit->facebook_data->{$item["fb_name"]}, 2), $item['is_icon']); ?></span>
                      <!--  -->
                      <span class="vertical-line"></span>
                      <span class="competitor-stats"><span class="comp-label"><?php echo ucfirst($audit->competitor_name); ?>: <br /></span>
                        <?php echo getWebIconFacebook(round($audit->competitor->facebook_data->{$item["fb_name"]}, 2), $item['is_icon']); ?></span><?php
                    } else {
                      echo getWebIconFacebook(round($audit->facebook_data->{$item["fb_name"]}, 2), $item['is_icon']);
                    } ?>
                    </span>
                    <span class="explenation"><?php echo $item["desc"]; ?></span>
                    <?php
                      visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]);
                    ?>
                  </div>
                </div><?php
              }
            }
            foreach ($facebook_ad_blocks as $item) {
              if (($audit->has_comp || !$item["is_comp"]) && show_block($edit_mode, $audit->{$item["type"]})) {
                $path = $item["is_comp"] ? $audit->competitor : $audit; ?>
                <div class="stat-block col-lg-6" id="fb_ads">
                  <div class="inner">
                    <span class="title-box facebook"><?php echo $item["name"]; ?></span><?php
                    // preview mode
                    if (!$edit_mode) {
                      $class = $path->facebook_data->runningAdds ? "check" : "times";
                      $color = $path->facebook_data->runningAdds ? "#27ae60" : "#c0392b"; ?>

                      <span class="explenation">Is the page currently running ads</span>
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
                      </form>
                      <?php
                        visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]);
                      ?>
                      <span class="explenation-ads">
                        <a target="_blank" href="<?php echo 'https://www.facebook.com/pg/'. $path->facebook_name .'/ads/'; ?>">
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
                <span class="score-tag">Score</span><?php
                if ($edit_mode) { ?>
                  <span class="score-text"><span id="facebook_value"></span>%</span>
                  <div class="slidecontainer">
                    <input type="range" min="1" max="100" value="<?php echo $score['fb']; ?>" class="slider" id="facebook_range">
                  </div><?php
                } else { ?>
                  <span class="score-text"><?php echo $score['fb']; ?>%</span><?php
                } ?>

                <span class="advice-title">Facebook advice</span><?php
                if ($edit_mode) { ?>
                  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#facebook-info" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="facebook_score" id="facebook_score" value='<?php echo $score['fb']; ?>'/>
                    <textarea maxlength="999" input="text"  name="facebook_advice" id="facebook_advice"><?php echo $advice['fb']; ?></textarea>
                  </form><?php
                } else { ?>
                  <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo $advice['fb']; ?></p>
                  <div class="info">
                    <a href="callto:<?php echo $phone;?>"><i class="fas fa-phone"></i><?php echo $phone; ?></a>
                    <a href="mailto:<?php echo $author->user_email; ?>"><i class="fas fa-envelope"></i><?php echo $author->user_email; ?></a>
                  </div><?php
                } ?>
              </div>
            </div>
          </div>
        </div>
      </div><?php
    }
    if ($audit->instagram_bit == "1") { ?>
      <div class="col-lg-12 facebook-info">
        <span class="facebook-inf-title"><span class="round instagram"><i class="fab fa-instagram"></i></span> &nbsp; Instagram stats:</span>
        <span class="sub-title">Statistics of your Instagram page.</span>
        <?php if ($audit->manual && $edit_mode) { ?><span class="manual-text"><span style="color: #e74c3c;">Attention: </span>There is no instagram or instagram business account found, so <a target="_blank" href="https://www.instagram.com/<?php echo $audit->instagram_name; ?>">click here</a> to gather your data!</span><?php } ?>
        <?php if ($edit_mode && (isset($audit->competitor->manual) && $audit->competitor->manual)) { ?><span class="manual-text" style="margin-top: 15px;"><span style=" color: #e74c3c;">Attention: </span>There is no competitor instagram or instagram business account found, so <a href="https://www.instagram.com/<?php echo $audit->competitor_name; ?>">click here</a> to gather your data!</span><?php } ?>
        <div style="clear:both"></div>
        <div class="col-lg-6 instagram-left" style="float:left;"><?php
          if (show_block($edit_mode, $audit->insta_hashtag) && (!$audit->manual) && isset($audit->instagram_data->hashtags[0][0])) { ?>
            <div class="col-lg-12 left custom-left" style="padding: 0;">
              <?php
                visibility_short_code($edit_mode, $audit->insta_hashtag, 'insta_hashtag');
              ?>

              <div class="chart-info">
                <span class="stat-box-title">Hashtags used</span>
                <span class="graph-procent" style="margin-top: 4px;">Most used '<?php echo $audit->instagram_data->hashtags[0][0]; ?>'</span>
              </div>
              <div class="inner custom-inner" style="padding: 0;">
                <canvas id="hashtag-chart" class="chart-instagram"  style="height: 292px;"></canvas>
              </div>

              <div class="legend">
                  <span class="round-color you-color"></span> <span class="space">You</span>
                  <?php if ($audit->has_comp && !$audit->competitor->manual) { ?><span class="round-color competitor-color"></span> <?php echo ucfirst($audit->competitor_name); } ?>
              </div>
            </div><?php
          }
          if (show_block($edit_mode, $audit->insta_lpd) && (!$audit->manual)) { ?>
            <div class="col-lg-12 left custom-left" style="padding: 0;">
              <?php
                visibility_short_code($edit_mode, $audit->insta_lpd, 'insta_lpd');
              ?>

              <div class="chart-info">

                <span class="stat-box-title">Likes on your posts Instagram</span>
                <span class="graph-procent" style="margin-top: 2px;">Average <?php
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
              <div class="inner custom-inner" style="">
                <canvas id="lpd-chart" class="chart-instagram"  style="height: 292px;"></canvas>
              </div>
              <div class="legend">
                  <span class="round-color you-color"></span> <span class="space">You</span><?php
                  if ($audit->has_comp && !$audit->competitor->manual) { ?>
                    <span class="round-color competitor-color" style="margin-right: 4px;"></span><?php
                    echo ucfirst($audit->competitor_name);
                  } ?>
              </div>
            </div><?php
          } ?>
        </div><?php

        if (($audit->manual == 1)) { ?>
          <div class="col-lg-12 instagram-right" style="float: right;">
          <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#instagram-info" style="width: 50%; float:left;" method="post" enctype="multipart/form-data" id="manual-ig-form"><?php
        } else { ?>
          <div class="col-lg-6 instagram-right" style="float: right;">
          <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#instagram-info" style="width: 100%; float:left;" method="post" enctype="multipart/form-data" id="manual-ig-form"><?php
        }

        function competitor_code($audit, $edit_mode, $item) {
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
            <span class="explenation"><?php
              echo $item["desc"]; ?>
            </span><?php
          }
        }

        function manual_check($audit, $item, $edit_mode, $comp) {
          $base = ($comp) ? $audit->competitor : $audit;
          $value = $base->instagram_data->{$item['ig_name']};
          $str = ($comp) ? "comp-" : "";

          if ($base->manual && $edit_mode) {?>
            <input type="text" name="<?php echo "{$str}".$item["ig_name"]; ?>" value="<?php echo round($value, 2); ?>" /></span><?php
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
                  echo $item["name"]; ?>
                </span><?php
                // Als preview mode laat description staan en hide client info

                competitor_code($audit, $edit_mode, $item);
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
                <span class="score-tag insta-advice-tag">Score</span><?php
                if ($edit_mode) { ?>
                  <span class="score-text"><span id="instagram_value"></span>%</span>
                  <div class="slidecontainer">
                    <input type="range" min="1" max="100" value="<?php echo $score['ig']; ?>" class="slider" id="instagram_range">
                  </div><?php
                } else { ?>
                  <span class="score-text"><?php echo $score['ig']; ?>%</span><?php
                } ?>
              </div>
              <div class="col-lg-12 align" id="instagram-info">
                <span class="advice-title">Instagram advice</span><?php
                if ($edit_mode) { ?>
                  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#instagram-info" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="instagram_score" id="instagram_score" value="<?php echo $score['ig']; ?>"/>
                    <textarea maxlength="999" input="text"  name="instagram_advice" id="instagram_advice"><?php echo $advice['ig']; ?></textarea>
                  </form><?php
                } else { ?>
                  <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo $advice['ig']; ?> </p>
                  <div class="info">
                    <a href="callto:<?php echo $phone;?>"><i class="fas fa-phone"></i><?php echo $phone; ?></a>
                    <a href="mailto:<?php echo $author->user_email; ?>"><i class="fas fa-envelope"></i><?php echo $author->user_email; ?></a>
                  </div><?php
                } ?>
            </div>
          </div>
          </div>
        </div>
      </div><?php
    }
    if ($audit->website_bit == "1") { ?>
      <div class="col-lg-12 facebook-info website-info" id="website-info"><?php
        if (!$audit->has_website) { ?>
          <div class="wait-for-crawl"><p>Please wait a moment, the website data is being prepared.</p></div><?php
        } ?>
        <span class="facebook-inf-title"><span class="round website">W</span> &nbsp; Website stats:</span>
        <span class="sub-title">Statistics of your webpage.</span>
        <div class="col-lg-6 left" style="background: transparent; border: 0; margin-top: 0;">
          <div class="inner custom-inner"><?php

            foreach ($website_blocks as $item) {
              if (show_block($edit_mode, $audit->{$item["type"]})) { ?>
                <div class="stat-block col-lg-6" id="<?php echo $item['type']; ?>">
                  <div class="inner">
                    <span class="title-box website"><?php echo $item["name"]; ?></span>
                    <span class="data_animation"><?php
                    if ($audit->has_comp) { ?>
                      <span class="data-view"><span class="comp-label">You: <br />
                        </span><?php echo getWebIcon($audit->{$item["db_name"]}, $audit->has_website); ?></span>
                      <span class="vertical-line"></span>
                      <span class="competitor-stats"><span class="comp-label"><?php echo ucfirst($audit->competitor_name); ?>: <br /></span>
                        <?php echo getWebIcon($audit->competitor->{$item["db_name"]}, $audit->has_website) ?></span><?php
                    } else {
                      echo getWebIcon($audit->{$item["db_name"]}, $audit->has_website);
                    } ?>
                    </span>
                    <?php
                      visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]);
                    ?>
                    <span class="explenation"><?php echo $item["desc"]; ?></span>
                  </div>
                </div><?php
              }
            } ?>
          </div>
        </div>
        <div class="col-lg-6 right instagram-right" style="padding: 20px 20px; margin-top: 35px !important;">
          <span class="score-tag website-advice-tag">Score</span><?php
          if ($edit_mode) { ?>
            <span class="score-text"><span id="website_value"></span>%</span>
            <div class="slidecontainer">
              <input type="range" min="1" max="100" value="<?php echo $score['wb']; ?>" class="slider" id="website_range">
            </div>
            <span class="advice-title margin-advice-title">Website advice</span>
            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#website-info" method="post" enctype="multipart/form-data">
              <input type="hidden" name="website_score" id="website_score" value="<?php echo $score['wb']; ?>"/>
              <textarea maxlength="999" input="text"  name="website_advice" id="website_advice"><?php echo $advice['wb']; ?></textarea>
            </form><?php
          } else { ?>
            <span class="score-text"><?php echo $score['wb']; ?>%</span>
            <span class="advice-title margin-advice-title">Website advice</span>
            <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo $advice['wb']; ?></p>
            <div class="info">
              <a href="callto:<?php echo $phone;?>"><i class="fas fa-phone"></i><?php echo $phone;?></a>
              <a href="mailto:<?php echo $author->user_email; ?>"><i class="fas fa-envelope"></i><?php echo $author->user_email; ?></a>
            </div><?php
          } ?>
        </div>
      </div><?php
    } ?>
  </section>
  <section class="audit-conclusion col-lg-12">
    <div class="left-conlusion col-lg-7">
      <h3>Conclusion</h3>
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
          echo $audit->conclusion == NULL ? $user->conclusion_audit : $audit->conclusion;
        ?></p><?php
      } ?>
    </div>
  </section>
  <div class="footer">
    <span class="phone-number">Phonenumber: <a href="callto:<?php echo $phone; ?>"><?php echo $phone; ?></a></span>
    <span class="mailadres">Mailadress: <a href="mailto:<?php echo $author->user_email; ?>"><?php echo $author->user_email; ?></a></span>
  </div>
</body>
</html>

<script charset='utf-8'>
  var commonPost = {
    'audit': '<?php echo $audit->id; ?>',
    'user': '<?php echo $user_id; ?>',
    'post': '<?php echo $post_id; ?>',
    'auth': '<?php echo $auth_hash; ?>',
    'type': 'audit'
  }

  <?php
  if (!$audit->has_website) { ?>
    function crawlFinishedCheck() {
      console.log("test");
      var modalData = {
        'text': 'The crawler has finished parsing the website',
        'subtext': 'Do you wish to reload the page now?',
        'confirm': 'reload_page'
      }

      var crawlModal = initiateModal('crawlModal', 'confirm', modalData);
      $('#reload_page').click(function() {
        location.reload();
      });
     
      $.ajax({
        type: "POST",
        url: ajaxurl,
        data: {action: 'crawl_data_check', ...commonPost},
        success: function (response) {
          console.log('retrieved response : ' + response);
          if (response > <?php echo (int)$audit->has_comp ?>) {
            showModal(crawlModal);
          } else {
            setTimeout(function() { crawlFinishedCheck(); }, 8000);
          }
        },
        error: logResponse,
      });
    }
    crawlFinishedCheck();<?php
  } ?>

  <?php
  // Graph Generate
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
    generateAreaChart('hashtag-chart', bar_data, bar_labels);<?php
  } ?>

  <?php
  if ($edit_mode) { ?>
    // IFrame functions
    var iframe  = $("#iframe-input");
    var inputField = $('#iframe-value');

    $("#iframe-option").click(function() {
      iframe.addClass("block");
      iframe.removeClass("none");
    });

    $("#nothing-option").click(function() {
      iframe.addClass("none");
      iframe.removeClass("block");
      // inputField.value = NULL;
    });

    // Visibility function
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
          data: {action: 'toggle_visibility', field: field_name , ...commonPost},
          success: function () { field.html(icon) },
          error: logResponse,
        });
      }
    };

    $(function() { 
      <?php
      if ($edit_mode) { ?>
        // On change of input instagram manual
        $("#manual-ig-form").find('input[type=text]').on('keyup paste change', function() {
          $("#universal-update").show(600);
        });

        // On change of an text area show update all
        $("textarea").on('keyup paste change', function() {
          $(this).data('changed', true);
          $("#universal-update").show(600);
          // Enable navigation prompt
          window.onbeforeunload = function() {
              return true; // TODO: add message?
          };
          var advice_type = ($(this).prop('id').includes('_advice')) ? $(this).prop('id').replace('_advice', '') : false;
          if (advice_type) {
            // disable slider text
            handleSlider(advice_type);
          }
          if ($(this).val() == '' && $(this).prop('id').includes('_advice')) {
            // activate TODO: add parameters. To activate slider
            handleSlider(advice_type);
          }
        });

        $("input[type=range]").on('mouseup', function() {
          var data = {action: 'textareas', ...commonPost};
          var translate = {
            'facebook_range': 'facebook_score',
            'instagram_range': 'instagram_score',
            'website_range': 'website_score',
          }
          data[translate[$(this).prop('id')]] = $(this).val();
          $.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
            success: logResponse,
            error: logResponse,
          });
        });

        var manualData = getInstagramFields({});

        $('#universal-update').on('click', function() {
          updateTextAreas();
        });
        
        function updateTextAreas() {
          var areas = getChangedTextAreas();
          var igFields = getInstagramFields();
          if ($.isEmptyObject(areas) && $.isEmptyObject(igFields)) { return }
          $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: 'textareas', ...areas, ...igFields, ...commonPost},
            success: function(response) {
              // Remove navigation prompt
              window.onbeforeunload = null;
              $('#universal-update').hide(600);
            },
            error: logResponse,
          });
        }

        function getInstagramFields(manualData = null) {
          var changed = {};
          $("#manual-ig-form input[type=text]").each(function(index, element) {
            changed[$(this).prop('name')] = $(this).prop('value');
          });
          return changed;
        }

        function getChangedTextAreas() {
          var changedAreas = {};
          $('textarea').each(function(index, element) {
            if ($(element).data('changed')) {
              changedAreas[$(this).prop('id')] = $(this).val();
            }
          });
          return changedAreas;
        }<?php
      }?>

      // IFrame Submit
      $("#banner-form").submit(function(e){
        e.preventDefault();
        var updated = $('form input[name="iframe"]').val();
        $('form input[name="iframe"]').val(updated.replace('<iframe','').replace('</iframe>',''));
        this.submit();
      });

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
        document.getElementById("public_link").select();
        document.execCommand("copy");
      });

      // Auto Mail Model
      var modalData = {
        text:`Do you want to sent this client automatic reminders?`,
        html:`<input type="checkbox" style="margin: 15px auto;" id="mail_bit_check" <?php echo $audit->mail_bit ? 'checked': '';?>>`,
        subtext:`Social Audify can send automatic reminders if your lead does
                  not open the audit. You can configure the emails
                  <a href='/profile-page'>here</a>.`,
        confirm: 'mail_confirmed'
      }

      var mailModal = initiateModal('mailModal', 'confirm', modalData);
      $('#mail_link').click(function() {
        showModal(mailModal);
      });

      var mailValue = <?php echo $audit->mail_bit; ?>;
      $("#mail_confirmed").click(function() {
        if (mailValue != $("#mail_bit_check").is(':checked')) {
          $.ajax({
            type: "POST",
            url: ajaxurl,
            data: { 
              action: 'flip_mail',
              value: $("#mail_bit_check").is(':checked'),
              ...commonPost
            },
            success: logResponse,
            error: function (errorThrown) {
              console.log(errorThrown);
              var modalData = {
                'text': "Can't update mail function",
                'subtext': "Please try again later or notify an admin if the issue persists"
              }
              showModal(initiateModal('errorModal', 'error', modalData));
            }
          });
        }
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
          success: function () {
            window.location.replace('https://<?php echo $env; ?>/audit-dashboard')
          },
          error: function (errorThrown) {
            console.log(errorThrown);
            var modalData = {
              'text': "Can't delete this audit",
              'subtext': "Please try again later or notify an admin if the issue persists"
            }
            showModal(initiateModal('errorModal', 'error', modalData));
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
          error: logResponse,
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
      var score = $('#' + type + '_score');
      var slider = $('#' + type + '_range');
      var advice = $('#' + type + '_advice');
      // set
      value.html(slider.val());

      slider.off('input'); 
      slider.on('input', function(e) {
        value.html($(e.target).val());
        score.val($(e.target).val());
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
      echo preg_replace("/\r|\n/", '\n', $string);
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
              one: '<?php replace_lbs($user->text_fb_1); ?>',
              two: '<?php replace_lbs($user->text_fb_2); ?>',
              three: '<?php replace_lbs($user->text_fb_3); ?>',
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
              one: '<?php replace_lbs($user->text_insta_1); ?>',
              two: '<?php replace_lbs($user->text_insta_2); ?>',
              three: '<?php replace_lbs($user->text_insta_3); ?>',
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
              one: '<?php replace_lbs($user->text_website_1); ?>',
              two: '<?php replace_lbs($user->text_website_2); ?>',
              three: '<?php replace_lbs($user->text_website_3); ?>',
            },<?php 
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
    }
    <?php
  } ?>
  // TODO: algemene update functie, die itereert over alle mogelijke velden
  //        - en ze update als ze verandert zijn...
</script>

<!DOCTYPE html>
<html lang="en" style="overflow-y: scroll;">

<?php
  $post_id = get_the_ID();
  $author_id = (int)get_post_field('post_author', $post_id); // TODO:
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
                ($user_id == $author_id) : false;

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

  // Get competitor data & decode json data (TODO : dynamisch dit beiden laten gebeuren...)
  $audit->get_competitor();
  $audit->decode_json();

  $has_comp_bit = ($audit->has_comp) ? 1 : 0;

  if ($audit->manual == 0) {
      $sumPostLikes = $audit->instagram_bit == "1" ? array_sum($audit->instagram_data->likesPerPost) : NULL;
  }

  // Post handling
  if (isset($_POST['iframe'])) {
    $audit->update('video_iframe', base64_encode($_POST['iframe']), 'Audit_template');
  }

  foreach (['introduction', 'conclusion', 'facebook_advice', 'instagram_advice',
            'website_advice', 'facebook_score', 'instagram_score', 'website_score'] as $post_name) {
    if (isset($_POST[$post_name])) {
      $audit->update($post_name, $_POST[$post_name], 'Audit_template');
    }
  }

    // Update competitor insta manual
    if (isset($_POST['followers_count']) || isset($_POST['avgEngagement']) ||
       isset($_POST['postsLM']) || isset($_POST['follows_count']) ||
       isset($_POST['averageLikes']) || isset($_POST['averageComments'])) {
        if (isset($_POST['followers_count'])) {
          $audit->instagram_data->followers_count = $_POST['followers_count'];
        }

        if (isset($_POST['avgEngagement'])) {
          $audit->instagram_data->avgEngagement = $_POST['avgEngagement'];
        }

        if (isset($_POST['postsLM'])) {
          $audit->instagram_data->postsLM = $_POST['postsLM'];
        }

        if (isset($_POST['follows_count'])) {
          $audit->instagram_data->follows_count = $_POST['follows_count'];
        }

        if (isset($_POST['averageComments'])) {
          $audit->instagram_data->averageComments = $_POST['averageComments'];
        }

        if (isset($_POST['averageLikes'])) {
          $audit->instagram_data->averageLikes = $_POST['averageLikes'];
        }

        $audit->update_manual('instagram_data', $audit->instagram_data, 0);

        $audit = $audit_control->get($id);
        $audit->get_competitor();
        $audit->decode_json();
    }

    // Update competitor insta manual
    if (isset($_POST['comp-followers_count']) || isset($_POST['comp-avgEngagement']) ||
       isset($_POST['comp-postsLM']) || isset($_POST['comp-follows_count']) ||
       isset($_POST['comp-averageLikes']) || isset($_POST['comp-averageComments'])) {
        if (isset($_POST['comp-followers_count'])) {
          $audit->competitor->instagram_data->followers_count = $_POST['comp-followers_count'];
        }

        if (isset($_POST['comp-avgEngagement'])) {
          $audit->competitor->instagram_data->avgEngagement = $_POST['comp-avgEngagement'];
        }

        if (isset($_POST['comp-postsLM'])) {
          $audit->competitor->instagram_data->postsLM = $_POST['comp-postsLM'];
        }

        if (isset($_POST['comp-follows_count'])) {
          $audit->competitor->instagram_data->follows_count = $_POST['comp-follows_count'];
        }

        if (isset($_POST['comp-averageComments'])) {
          $audit->competitor->instagram_data->averageComments = $_POST['comp-averageComments'];
        }

        if (isset($_POST['comp-averageLikes'])) {
          $audit->competitor->instagram_data->averageLikes = $_POST['comp-averageLikes'];
        }

        $audit->update_manual('instagram_data', $audit->competitor->instagram_data, 1);

        $audit = $audit_control->get($id);
        $audit->get_competitor();
        $audit->decode_json();
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

  function getWebIcon($has_website, $value) {

    if ($has_website) {
      if ($value === '0') {
        return '<i class="fas fa-times" style="color: #c0392b; display: inline"></i>';
      }
      elseif ($value === '1') {
        return '<i class="fas fa-check" style="color: #27ae60; display: inline"></i>';
      }
      return $value;
    }

    return "";
  }

  function getWebIconFacebook($value, $is_icon) {
    if ($is_icon) {
          if ($value == 0) {
            return '<i class="fas fa-times" style="color: #c0392b; display: inline"></i>';
          }
          elseif ($value == 1) {
            return '<i class="fas fa-check" style="color: #27ae60; display: inline"></i>';
          }
          return $value;
    }

    return $value;
  }

  function visibility_icon($edit_mode, $visible) {
    if ($edit_mode) {
      $slash = $visible == 1 ? '' : '-slash';
      echo '<i class="far fa-eye'.$slash.'"></i>';
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
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link rel="stylesheet" href="<?php echo $leadengine; ?>/dashboard/assets/styles/dashboard.css" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/modal.js"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/functions.js"></script>
  <script>var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';</script>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="custom-body">
  <div class="sub-header col-lg-12">

    <?php if ($edit_mode) { ?>
      <a href="/dashboard/" class="home-link"><i class="fas fa-th-large"></i> Dashboard </a>
    <?php } ?>

    Audit: <?php echo $audit->name; ?>

    <?php if ($edit_mode) { ?>
      <div id="delete-this-audit"> <i class="fas fa-trash"></i> </div>
      <button id="copy_link" class="copy-link"> <i class="fas fa-share-alt-square"></i> Share & Track </button>
      <button id="mail_link" class="copy-link"> <i class="fas fa-cog"></i> Mail </button>
    <?php }

    if ($user_id === $author_id) {
      if ($edit_mode) { ?>
				<a href="?preview_mode=True"; class="preview"><i class="far fa-eye"></i> Preview </a><?php
      } else { ?>
				<a href="?preview_mode=False"; class="edit"><i class="far fa-eye"></i> Edit </a><?php
      }
    } ?>
  </div>

  <div id="shareModal" class="modal"></div>
  <div id="mailModal" class="modal"></div>
  <div id="confirmModal" class="modal"></div>
  <div id="errorModal" class="modal"></div>
  <div id="crawlModal" class="modal"></div>

  <section class="content white custom-content min-height">
    <div class="call-to-action-container">
      <a href="callto:<?php echo $phone; ?>" class="call-to-call"><i class="fas fa-phone"></i></a>
      <a href="mailto:<?php echo $author->user_email; ?>" class="call-to-mail"><i class="fas fa-envelope"></i></a>
    </div>
    <input type="text" class="offscreen" aria-hidden="true" name="public_link" id="public_link"
           value=<?php echo "https://".$env."/public/".$slug; ?> />

    <?php if ($audit->video_iframe != NULL) { ?>
      <div class="intro-video"><?php
        echo "<iframe ". stripslashes(base64_decode($audit->video_iframe)) ."</iframe>"; ?>
      </div><?php
      // TODO: ik snap deze check niet...
    } else if ($audit->video_iframe != "" || $edit_mode) { ?>
      <div class="intro-video"></div><?php
    }

    if ($edit_mode) { ?>
      <div class="video-options">
        <h3>Banner options:</h3>
        <span class="eplenation-banner">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem <a href="https://www.google.nl">aperiam</a>.</span>
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="banner-form" method="post" enctype="multipart/form-data">
          <input type="radio" name="video-option" id="iframe-option" <?php echo $video_iframe; ?> /> <span class="radio-label">Iframe</span>
          <input type="radio" name="video-option" id="nothing-option" <?php echo $video_nothing; ?>/> <span class="radio-label">Nothing</span>
          <div id="iframe-input" <?php echo $display_iframe; ?> >
            <input type="text" id="iframe-input" name="iframe" placeholder="Insert iframe(Loom/Youtube etc.)" pattern="(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))"
                   value='<?php if ($audit->video_iframe != NULL) { echo '<iframe '. stripslashes(base64_decode($audit->video_iframe)) .'</iframe>'; }?>'/>
          </div>
          <input type="submit" onclick="function() { document.getElementById('video-iframe').value = ''; }" value="Update" class="advice-button">
        </form>
      </div><?php
    }

    $classType = ($audit->video_iframe != NULL && $audit->video_iframe != "") ? " with-video" : ""; ?>
    <div class="audit-intro<?php echo $classType; ?> col-lg-10 col-lg-offset-2">
      <div class="client-profile-picture">
        <?php echo get_avatar($author_id, 32); ?>
      </div>
      <div class="audit-intro-text" id="introduction">
        <span class="audit-company-name">Social Audify <!-- TODO: Moet dynamisch --></span><?php
        if ($edit_mode) { ?>
          <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#introduction" method="post" enctype="multipart/form-data">
            <textarea input="text"  name="introduction" id="introduction"><?php echo $audit->introduction; ?></textarea>
            <input type="submit" value="Update" class="advice-button">
          </form><?php
        } else { ?>
          <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo $audit->introduction; ?></p><?php
        } ?>
      </div>
    </div><?php
    if ($audit->facebook_bit == "1") { ?>
      <div class=" col-lg-12 facebook-info" id="facebook-info">
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
                      <span class="vertical-line"></span>
                      <span class="competitor-stats"><span class="comp-label"><?php echo ucfirst($audit->competitor_name); ?>: <br /></span>
                        <?php echo getWebIconFacebook(round($audit->competitor->facebook_data->{$item["fb_name"]}, 2), $item['is_icon']); ?></span><?php
                    } else {
                      echo getWebIconFacebook(round($audit->facebook_data->{$item["fb_name"]}, 2), $item['is_icon']);
                    } ?>
                    </span>
                    <span class="explenation"><?php echo $item["desc"]; ?></span>
                    <div onclick="toggle_visibility('<?php echo $item["type"]; ?>')" id="<?php echo $item['type']; ?>_icon" class="visibility">
                      <?php visibility_icon($edit_mode, $audit->{$item["type"]}); ?></div>

                    <!-- <div class="comp-better">Competitor better<span class="competitor-round"></span></div> -->
                  </div>
                </div><?php
              }
            }

            // TODO : Nog geen mooie manier om competitor data weer te geven...
            if (show_block($edit_mode, $audit->fb_ads)) { ?>
              <div class="stat-block col-lg-6" id="fb_ads">
                <div class="inner">
                  <span class="title-box facebook">Running ads</span><?php
                  if (!$edit_mode) {
                    $class = $audit->facebook_data->runningAdds ? "check" : "times";
                    $color = $audit->facebook_data->runningAdds ? "#27ae60" : "#c0392b"; ?>

                    <span class="explenation">Is the page currently running ads</span>
                    <span class="data_animation">
                      <i class='fas fa-<?php echo $class; ?>' style='color: <?php echo $color; ?>'></i>
                    </span><?php
                  } else { ?>
                    <form class="ads-radio" action="">
                      <?php $checked = $audit->facebook_data->runningAdds; ?>
                      <input type="radio" name="ads" value="yes" <?php echo $checked ? "checked" : ""; ?>> <span class="label_ads">Yes</span>
                      <input type="radio" name="ads" value="no" <?php  echo !$checked ? "checked" : ""; ?>> <span class="label_ads">No</span>
                    </form>

                    <div onclick="toggle_visibility('fb_ads')" id="fb_ads_icon" class="visibility"><?php visibility_icon($edit_mode, $audit->fb_ads); ?></div>
                    <span class="explenation-ads">
                      <a target="_blank" href="<?php echo 'https://www.facebook.com/pg/'. $audit->facebook_name .'/ads/'; ?>">
                        Click here to watch if this page is currently running ads. (This can't be automated)
                      </a>
                    </span><?php
                  } ?>
                </div>
              </div><?php
              if ($audit->has_comp) { ?>
                <div class="stat-block col-lg-6" id="fb_ads">
                  <div class="inner">
                    <span class="title-box facebook">Competitor running ads</span><?php
                    if (!$edit_mode) {
                      $class = $audit->competitor->facebook_data->runningAdds ? "check" : "times";
                      $color = $audit->competitor->facebook_data->runningAdds ? "#27ae60" : "#c0392b"; ?>

                      <span class="explenation">Is the competitor page currently running ads</span>
                      <span class="data_animation">
                        <i class='fas fa-<?php echo $class; ?>' style='color: <?php echo $color; ?>'></i>
                      </span><?php
                    } else { ?>
                      <form class="ads-radio" action="">
                        <?php $checked = $audit->competitor->facebook_data->runningAdds; ?>
                        <input type="radio" name="ads_c" value="yes" <?php echo $checked ? "checked" : ""; ?>> <span class="label_ads">Yes</span>
                        <input type="radio" name="ads_c" value="no" <?php  echo !$checked ? "checked" : ""; ?>> <span class="label_ads">No</span>
                      </form>

                      <div onclick="toggle_visibility('fb_ads')" id="fb_ads_icon" class="visibility"><?php visibility_icon($edit_mode, $audit->fb_ads); ?></div>
                      <span class="explenation-ads">
                        <a target="_blank" href="<?php echo 'https://www.facebook.com/pg/'. $audit->competitor->facebook_name .'/ads/'; ?>">
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
                    <textarea input="text"  name="facebook_advice" id="facebook_advice"><?php echo $advice['fb']; ?></textarea>
                    <input type="submit" value="Update" class="edite-button">
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
        <div class="col-lg-6 instagram-left" style="float:left;">
        <?php
          if (show_block($edit_mode, $audit->insta_hashtag) && (!$audit->manual) && isset($audit->instagram_data->hashtags[0][0])) { ?>
            <div class="col-lg-12 left custom-left" style="padding: 0;">
              <div onclick="toggle_visibility('insta_hashtag')" id="insta_hashtag_icon" class="visibility">
                <?php visibility_icon($edit_mode, $audit->insta_hashtag); ?></div>
              <div class="chart-info">
                <span class="stat-box-title">Hashtags used</span>
                <span class="graph-procent" style="margin-top: -2px;">Most used '<?php echo $audit->instagram_data->hashtags[0][0]; ?>'</span>
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
              <div onclick="toggle_visibility('insta_lpd')" id="insta_lpd_icon" class="visibility"><?php
                visibility_icon($edit_mode, $audit->insta_lpd); ?></div>
              <div class="chart-info">
                <span class="stat-box-title">Likes on your posts Instagram</span>
                <span class="graph-procent" style="margin-top: -8px;">Average <?php
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
                  <span class="round-color you-color"></span> <span class="space">You</span>
                  <?php if ($audit->has_comp && !$audit->competitor->manual) { ?><span class="round-color competitor-color"></span> <?php echo ucfirst($audit->competitor_name); } ?>
              </div>
            </div><?php
          } ?>
        </div>

        <?php if (($audit->manual == 1)) { ?>
            <div class="col-lg-12 instagram-right" style="float: right;">
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#instagram-info" style="width: 50%; float:left;" method="post" enctype="multipart/form-data">
        <?php } else {
            ?><div class="col-lg-6 instagram-right" style="float: right;">
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#instagram-info" style="width: 100%; float:left;" method="post" enctype="multipart/form-data">
        <?php
        }
          foreach ($instagram_blocks as $item) {
            if (show_block($edit_mode, $audit->{$item["type"]})) { ?>
              <div class="stat-block col-lg-6" id="<?php echo $item['type']; ?>">
                <div class="inner">
                  <span class="title-box instagram"><?php echo $item["name"]; ?></span>
                  <?php if (!$edit_mode) { ?><span class="data_animation"><?php }
                  if ($audit->has_comp) { ?>
                    <span class="data-view"><span class="comp-label">You: <br /></span>
                      <?php if ($audit->manual) {
                                if ($edit_mode) { ?>
                                    <input type="text" name="<?php echo $item["ig_name"]; ?>" value="<?php echo round($audit->instagram_data->{$item["ig_name"]}, 2); ?>" /></span>
                                <?php } else { ?>
                                    <?php echo round($audit->instagram_data->{$item["ig_name"]}, 2); ?><?php if (!$edit_mode) { ?></span><?php } ?>
                                <?php } ?>
                      <?php } else { ?>
                          <?php echo round($audit->instagram_data->{$item["ig_name"]}, 2); ?><?php if (!$edit_mode) { ?></span><?php } ?>
                      <?php } ?>
                    </span>
                    <span class="vertical-line"></span>
                    <?php if (!$edit_mode) { ?><span class="competitor-animation"><?php } ?>
                        <span class="competitor-stats"><span class="comp-label"><?php echo ucfirst($audit->competitor_name); ?>: <br /></span>
                         <?php if ($audit->competitor->manual) {
                                    if ($edit_mode) { ?>
                                        <input type="text" name="comp-<?php echo $item["ig_name"]; ?>" value="<?php echo round($audit->competitor->instagram_data->{$item["ig_name"]}, 2); ?>" />
                        </span>
                    <?php if (!$edit_mode) { ?></span><?php } ?>
                          <?php } else { ?>
                              <?php echo round($audit->competitor->instagram_data->{$item["ig_name"]}, 2); ?><?php if (!$edit_mode) { ?></span><?php }
                                } ?>
                      <?php } else { ?>
                          <?php echo round($audit->competitor->instagram_data->{$item["ig_name"]}, 2); ?></span>
                      <?php }

                  } else {
                      if ($audit->manual) { ?>
                          <input type="text" name="<?php echo $item["ig_name"]; ?>" value="<?php echo $audit->instagram_data->{$item["ig_name"]}; ?>" /></span>

                      <?php } else { ?>
                          <?php echo $audit->instagram_data->{$item["ig_name"]}; ?></span>
                      <?php }
                  } ?>
                  </span>
                  <?php if (!$edit_mode) { ?><span class="explenation"><?php echo $item["desc"]; ?></span><?php } ?>
                  <div onclick="toggle_visibility('<?php echo $item["type"]; ?>')" id="<?php echo $item['type']; ?>_icon" class="visibility">
                    <?php visibility_icon($edit_mode, $audit->{$item["type"]}); ?></div>
                </div>
              </div><?php
            }
          }
          if ($audit->manual || (isset($audit->competitor->manual) && $audit->competitor->manual)) { ?>
            <input type="submit" class="edite-button" value="Update data" style="margin-left: 10px;"/>
            </form>
          <?php } ?>
          <?php if ($audit->manual == 1) { ?>
              <div class="col-lg-6 instagram-score" style="margin-top: -10px; float:left; ">
          <?php } else { ?>
              <div class="col-lg-12 instagram-score" style="float:right; ">
          <?php } ?>
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
                    <textarea input="text"  name="instagram_advice" id="instagram_advice"><?php echo $advice['ig']; ?></textarea>
                    <input type="submit" value="Update" class="edite-button" >
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
                        </span><?php echo getWebIcon($audit->has_website, $audit->{$item["db_name"]}); ?></span>
                      <span class="vertical-line"></span>
                      <span class="competitor-stats"><span class="comp-label"><?php echo ucfirst($audit->competitor_name); ?>: <br /></span>
                        <?php echo getWebIcon($audit->has_website, $audit->competitor->{$item["db_name"]}) ?></span><?php
                    } else {
                      echo getWebIcon($audit->has_website, $audit->{$item["db_name"]});
                    } ?>
                    </span>
                    <div onclick="toggle_visibility('<?php echo $item['type']; ?>')" id="<?php echo $item['type']; ?>_icon" class="visibility"><?php
                      visibility_icon($edit_mode, $audit->{$item["type"]}); ?></div>
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
              <textarea input="text"  name="website_advice" id="website_advice"><?php echo $advice['wb']; ?></textarea>
              <input type="submit" value="Update" class="edite-button">
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
  <section class="audit-conclusion col-lg-12" id="conclusion">
    <div class="left-conlusion col-lg-7">
      <h3>Conclusion</h3>
      <hr class="under-line" />
      <div style="clear:both"></div><?php
      if ($edit_mode) { ?>
        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#conclusion" method="post" enctype="multipart/form-data">
          <textarea input="text"  name="conclusion" id="conclusion"><?php echo $audit->conclusion; ?></textarea>
          <input type="submit" value="Update" class="advice-button">
        </form><?php
      } else { ?>
        <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php echo $audit->conclusion; ?></p><?php
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
        data: {...{action: 'crawl_data_check'}, ...commonPost}, // $.extend({}, commonPost, { 'action': 'crawl_data_check' })
        success: function (response) {
          console.log('retrieved response : ' + response);
          if (response > <?php echo $has_comp_bit; ?>) {
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
    generateBarChart('hashtag-chart', bar_data, bar_labels, [true, true]);<?php
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

    function clear() {
      document.getElementById('video-iframe').value = "";
    }

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
          data: $.extend({}, commonPost, { action: 'toggle_visibility', field: field_name }),
          success: function () { field.html(icon) },
          error: logResponse,
        });
      }
    };

    $(document).ready(function() {

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
            data: $.extend({}, commonPost, { action: 'flip_mail',
              value: $("#mail_bit_check").is(':checked')
            }),
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
          data: $.extend({}, commonPost, { 'action': 'delete_page' }),
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
        var data = $.extend({}, commonPost, {
          action: 'update_ads_audit',
          competitor: (competitor) ? 'true' : 'false',
          ads: button
        });

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
    function handleSlider(type, range, text) {
      var value = $('#' + type + '_value');
      var score = $('#' + type + '_score');
      var slider = $('#' + type + '_range');
      var advice = $('#' + type + '_advice');

      value.html(slider.val());
      slider.on('input', function(e) {
        value.html($(e.target).val());
        score.val($(e.target).val());

        if ($(e.target).val() < range.one) {
          advice.val(text.one);
        } else if ($(e.target).val() < range.two) {
          advice.val(text.two);
        } else {
          advice.val(text.three);
        }
      });
    }

    <?php
    if ($audit->facebook_bit == "1") { ?>
      var range_fb = {
        one: <?php echo $user->range_number_fb_1; ?>,
        two: <?php echo $user->range_number_fb_2; ?>,
      }
      var text_fb = {
        one: '<?php echo $user->text_fb_1; ?>',
        two: '<?php echo $user->text_fb_2; ?>',
        three: '<?php echo $user->text_fb_3; ?>',
      }
      handleSlider('facebook', range_fb, text_fb); <?php
    }
    if ($audit->instagram_bit == "1") { ?>
      var range_ig = {
        one: <?php echo $user->range_number_insta_1; ?>,
        two: <?php echo $user->range_number_insta_2; ?>,
      }
      var text_ig = {
        one: '<?php echo $user->text_insta_1; ?>',
        two: '<?php echo $user->text_insta_2; ?>',
        three: '<?php echo $user->text_insta_3; ?>',
      }
      handleSlider('instagram', range_ig, text_ig); <?php
    }
    if ($audit->website_bit == "1") { ?>
      var range_ws = {
        one: <?php echo $user->range_number_website_1; ?>,
        two: <?php echo $user->range_number_website_2; ?>,
      }
      var text_ws = {
        one: '<?php echo $user->text_website_1; ?>',
        two: '<?php echo $user->text_website_2; ?>',
        three: '<?php echo $user->text_website_3; ?>',
      }
      handleSlider('website', range_ws, text_ws); <?php
    }
  } ?>
  // TODO : algemene update functie, die itereert over alle mogelijke velden
  //        - en ze update als ze verandert zijn...
</script>

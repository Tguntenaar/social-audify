<?php
/**
 * Template Name: Audit page v2
 */
?>

<!DOCTYPE html>
<html lang="en" style="overflow-y: scroll;">

<?php 
  /**
   * TODO:
   * 
   * 1. CSS
   * 2. MODALS
   * 3. LANGUAGE SUPPORT 
   * 4. COLOR SUPPORT
   * 5. 
   */
  // Error Logging
  include(dirname(__FILE__)."/../header/php_header.php");

  $post_id = get_the_ID();
  $author_id = (int)get_post_field('post_author', $post_id);
  $env = getenv('HTTP_HOST');
  $slug = get_post_field("post_name", $post_id);
  $leadengine = get_template_directory_uri();

  // Get Author data
  $phone =  get_user_meta($author_id, 'rcp_number', true);
  $calendar_link =  get_user_meta($author_id, 'rcp_calendar', true);
  $author = get_userdata($author_id);
  $mail = $author->user_email;

  // Mode check
  $edit_mode = !(isset($_GET['preview_mode']) && $_GET['preview_mode'] == "True") ?
                ($user_id == $author_id || $user_id == 2) : false;

  // Language file
  include(dirname(__FILE__)."/../../assets/languages/language_file.php");

  // Import block titles
  include(dirname(__FILE__)."/../../assets/php/audit_blocks.php");

  // Get audit by post_id
  $id = $audit_control->get_id($post_id);
  $audit = $audit_control->get($id);
  $client = $client_control->get($audit->client_id);
  $user = $user_control->get($user_id !== 0 ? $user_id : $author_id);

  $theme_color = ($audit->color == "") ? $user->color_audit : $audit->color;

  if ($audit->manual == 0) {
    $sumPostLikes = $audit->instagram_bit == "1" ? array_sum($audit->instagram_data->likesPerPost) : NULL;
  }

  $leadengine = get_template_directory_uri();

  $options = "";
  foreach ($language as $key => $value):
    if ($audit->language == $key) {
      $options .= "<option value='". $key ."' selected >". $key ."</option>";           
    } else {
      $options .= "<option value='". $key ."' >". $key ."</option>";           
    }
  endforeach;

  $language_options = "<select style='margin-top: 7px;' id='language'>" . $options . "</select>";
  $language = $language[$audit->language];

  $template_options = "<select id='template'> <option>Audit Version</option> <option>Version 1.0</option> </select>";

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

  function get_contact_info($phone, $mail, $calendar_link, $language, $user) {
    if (isset($mail) && $mail != "") { ?>
      <a href='mailto: <?php echo $mail; ?>' class="text-link">
        <i class="fas fa-envelope"></i><?php echo $mail; ?>
      </a><?php 
    }

    if (isset($phone) && $phone != "") { ?>
      <a href="callto: <?php echo $phone; ?>" class="text-link">
        <i class="fas fa-phone"></i><?php echo $phone; ?>
      </a><?php
    }
   
    if ($calendar_link != "") { ?>
      <div class="buttons">
        <a href="<?php echo $calendar_link; ?>" target='_blank' rel='noreferrer' class="button" style="margin-left: 0px;"><?php
          echo $user->appointment_text == "" ? $language['make_appointment'] : $user->appointment_text; ?>
        </a>
      </div>
    <?php }
  }

  function show_block($edit_mode, $visible) {
    return ($edit_mode || $visible);
  }

  function normalize($val1, $val2) {
    if ($val1 > $val2) {
      return ($val2 / max($val1, 1)) * 100;
    } else {
      return ($val1 / max($val2, 1)) * 100;
    }
  }

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

  if ($audit->manual == 0) {
    $sumPostLikes = $audit->instagram_bit == "1" 
                  ? array_sum($audit->instagram_data->likesPerPost) 
                  : NULL;

      if ($audit->has_comp) {
          $compSumPostLikes = array_sum($audit->competitor->instagram_data->likesPerPost);
      }
  }

  $post_url = htmlentities(base64_encode(get_site_url() . "/" . get_post_field( 'post_name', get_post() )));
  if ($_SERVER['SERVER_NAME'] == "dev.socialaudify.com") {
    $url = "https://livecrawl.socialaudify.com/pdf/" . $post_url;
  } else {
    $url = "https://livecrawl.socialaudify.com/pdf/" . $post_url;
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

  if ($audit->video_iframe != NULL && $audit->video_iframe != "") {
    $video_iframe_link = '<iframe '.stripslashes($audit->video_iframe).'</iframe>';
  } else {
    $video_iframe_link = '';
  }

  function visibility_short_code($edit_mode, $visible, $name, $class = 'visibility') {
    if ($edit_mode) {
      $slash = $visible == 1 ? '' : '-slash';?>
      <div onclick="toggle_visibility('<?php echo $name; ?>')" id="<?php echo $name; ?>_icon" class="<?php echo $class; ?>">
        <i style="color: #000 !important" class="information far fa-eye<?php echo $slash; ?>"></i>
      </div><?php
    }
  }
?>

<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-149815594-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-149815594-1');
  </script>

  <title>Audit - <?php echo $audit->name; ?></title>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <link rel="stylesheet" href="<?php echo $leadengine; ?>/dashboard/assets/styles/audit.css<?php echo $cache_version; ?>" type="text/css">
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/functions.js<?php echo $cache_version; ?>"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/modal.js<?php echo $cache_version; ?>"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/chart.js<?php echo $cache_version; ?>"></script>

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

</head>
<body>
<a id="testje"  class="copy-link" style="display:none;" download="file.pdf"></a>
<div class="load-screen"><div class='lds-dual-ring'></div> <h3>Generating PDF, wait a minute.</h3></div>
<header>
    <div class="audit-name"><?php echo $audit->name; ?></div>
    <?php if ($edit_mode) { ?>
        <a href="/dashboard/" class="home-link"><i class="fas fa-th-large"></i> Dashboard </a>
        <button id="copy_link" class="languages"> <i class="fas fa-share-alt-square"></i> Share & Track </button>
        <button id="config_link" class="languages"> <i class="fas fa-cog"></i> Config </button>
        <a href="?preview_mode=True" class="languages previewMode"><i class="far fa-eye"></i> Preview </a>
        <?php
      } else {
        if ($user_id == $author_id) {?>
          <a href="?preview_mode=False" style="text-decoration: none; font-weight: 300;" class="languages"><i class="far fa-eye"></i> Edit </a><?php
        }
      } ?>
</header>

<?php
if ($edit_mode) { ?>
  <!-- TODO: Bram CSS -->
  <button id="universal-update" style="position:fixed;bottom:6px;right:16px;width:200px;z-index:555;display:none;"class="advice-button floating-update"> Update </button><?php
} ?>

<?php if ($audit->introduction_vis_bit == 1 || $edit_mode) { ?>

<div id="shareModal" class="modal"></div>
<input type="text" style="display:none;" aria-hidden="true" name="public_link" id="public_link" value=<?php echo "https://".$env."/public/".$slug; ?> />

<div id="configModal" class="modal"></div>
<div id="confirmModal" class="modal"></div>
<div id="reloadModal" class="modal"></div>
<div id="errorModal" class="modal"></div>
<div id="firstTimeModal" class="modal"></div>

<section class="introduction">
    <div class="sidebar">
      <div class="audit-owner <?php if ($edit_mode) {echo "mobile-sidebar";} ?>">
        <div class="profile-picture">
          <?php echo get_wp_user_avatar($author_id, "original"); ?>
        </div>
        <span class="name"><?php $company = get_user_meta($author_id, 'rcp_company', true ); if ($company == "") { echo $author->display_name; } else { echo $company; }?></span>
        <span class="contactme">Contact me</span>
        <div class="contact-icons">
          <?php if (isset($mail) && $mail != "") { ?><a href="mailto: <?php echo $mail; ?>"><i class="fas fa-envelope"></i></a><?php } ?>
          <!-- <a href="#"><i class="fas fa-globe"></i></a> -->
          <?php if (isset($phone) && $phone != "") { ?><a href="callto:<?php echo $phone; ?>"><i class="fas fa-phone"></i></a><?php } ?>
        </div>
      </div>
    </div>
    <div class="introduction-right <?php if ($edit_mode) {echo "mobile-index";} ?>">
        <span class="intro-vis"><?php visibility_short_code($edit_mode, $audit->introduction_vis_bit, 'introduction_vis_bit', 'visibility-first-level'); ?></span>
        
        <div class="video">
        <?php
            if (!$edit_mode) {
                if (($audit->video_iframe == "" || $audit->video_iframe == "") && !$edit_mode) {

                } else if (($audit->video_iframe == "" || $audit->video_iframe == "") && $edit_mode) {
                    ?><div class="video-iframe"></div><?php
                } else if (($audit->video_iframe != "" && $audit->video_iframe != NULL) || $edit_mode) { ?>
                    <div class="video-iframe"><?php
                        $video = str_replace("&#34;", '"', stripslashes($audit->video_iframe));

                        if (strpos($video, 'height') !== false) {
                            echo "<iframe height='315' ". $video ."</iframe>";
                        } ?>
                        </div><?php
                }

                if ($audit->video_iframe != NULL && $audit->video_iframe != "") {
                    $video_iframe_link = '<iframe '.stripslashes($audit->video_iframe).'</iframe>';
                } else {
                    $video_iframe_link = '';
                }
            } else {
                ?><div class="video-options">
                    <span class="title">Intro video</span><br />
                    <span class="explenation-banner">You can add a video on top of your audit by adding the iframe link here. Click <a href="tutorial/#1570543881921-3fd7746a-9da5">[here]</a> to learn how to find this link.</span>
                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" id="banner-form" method="post" enctype="multipart/form-data">
            
                    <input type="radio" class="iframe-radio" data-display="block" <?php echo ($audit->video_iframe != NULL && $audit->video_iframe != "") ? 'checked' : ''; ?>/>
                        <span class="radio-label">Video</span>
                    <input type="radio" class="iframe-radio" id="video_iframe" value="" data-display="none" <?php echo ($audit->video_iframe == NULL || $audit->video_iframe == "") ? 'checked' : ''; ?>/>
                        <span class="radio-label">Nothing</span>
                    <input type="text" id="iframe-input" placeholder="Insert iframe(Loom/Youtube etc.)" style="display:<?php echo ($audit->video_iframe != NULL & $audit->video_iframe != '') ? 'block' : 'none'; ?>"
                        pattern="(?:<iframe[^>]*)(?:(?:\/>)|(?:>.*?<\/iframe>))" value='<?php echo $video_iframe_link; ?>'/>
                    </form>
                </div><?php
            }
        ?>
            <!-- <div class="video-iframe">

                <iframe height="315" src="https://www.youtube.com/embed/unU9vpLjHRk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div> -->
        </div>

        <div class="introduction-text">
            <div class="intro-text-block">
                <span class="title">Improvements</span>
                <?php 
                    if ($audit->introduction_vis_bit == 1 || $edit_mode) {
                        if ($edit_mode) { ?>
                        <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#introduction" method="post" enctype="multipart/form-data">
                            <textarea maxlength="999" input="text"  name="introduction" id="introduction"><?php if ($audit->introduction == NULL) { echo $user->intro_audit; } else { echo $audit->introduction; } ?></textarea>
                        </form>

                        <div class="description-tags">
                            You can insert the following tags in all the text fields: <span style="font-size: 10px; color: #000;">#{client}, #{competitor}, #{fb_score}, #{insta_score}, #{website_score}</span>
                        </div>
                        <?php
                        } else {  ?>
                            <p style='font-size: 14px; font-weight: 100; line-height: 24px;'>
                                <?php if ($audit->introduction == NULL) { echo "<pre>" . change_tags($user->intro_audit, $client, $audit) . "</pre>"; } else { echo "<pre>" . change_tags($audit->introduction, $client, $audit) . "</pre>"; } ?></p><?php
                            get_contact_info($phone, $mail, $calendar_link, $language, $user); 
                         }
                    }
                ?>
            </div>
        </div>
    </div>
</section>
<?php } ?>

<?php if ($audit->facebook_bit && ($audit->facebook_vis_bit == 1 || $edit_mode)) { ?>
<section id="facebook-section">
    <div class="sidebar">
      <span class="title">Statistics</span>
      <ul>
        <?php if ($audit->facebook_bit && ($audit->facebook_vis_bit == 1 || $edit_mode)) { ?><li class="facebook-option active"><i class="fab fa-facebook-square"></i><span class="nav-position">Facebook</span></li><?php } ?>
        <?php if ($audit->instagram_bit && ($audit->instagram_vis_bit == 1 || $edit_mode)) { ?><li class="instagram-option"><i class="fab fa-instagram"></i><span class="nav-position">Instagram</span></li><?php } ?>
        <?php 
            if ($audit->website_bit && ($audit->website_vis_bit == 1 || $edit_mode)) { 
              if ($audit->has_website) {?>
                <li class="website-option"><i class="fas fa-globe"></i><span class="nav-position">Website</span></li>
        <?php } else { ?>
              <li class="" style="cursor: initial;"><i class="fas fa-globe"></i><span class="nav-position">Website</span><span style="font-size: 9px; position: absolute; right: 0px; bottom: -18px;">Wait a minute</span><span></li>
        <?php } 

        }?>
        <?php if ($audit->conclusion_vis_bit == 1 || $edit_mode) { ?><li class="conclusion-option"><i class="fas fa-check"></i><span class="nav-position">Conclusion</span></li><?php } ?>
      </ul>
      <a href="#" onclick="generatePDF()" class="button generate-pdf" style="background: #dbecfd; font-weight: bold; color: #4da1ff; box-shadow: none;">Generate PDF</a>
    </div>
    
    <div class="facebook-right">
        <span class="section-vis"><?php visibility_short_code($edit_mode, $audit->facebook_vis_bit, 'facebook_vis_bit', 'visibility-first-level'); ?></span>
        
        <i class="fab fa-facebook-square section-icon"></i>
        <span class="section-title">
            <?php if ($user->facebook_title == "") { ?>
                <?php echo $language['fb_title']; ?>:
            <?php } else {
                echo $user->facebook_title;
            } ?>
        </span>     
        <span class="section-subtitle">
            <?php if ($user->facebook_sub_title == "") { ?>
                <?php echo $language['fb_subtitle']; ?>
            <?php } else {
                echo $user->facebook_sub_title;
            } ?>
        </span>   

        <div class="statistics">
            <?php
            foreach ($facebook_blocks as $item):
                if (show_block($edit_mode, $audit->{$item["type"]}) && !$item["is_icon"]) { 
                  list($your_procent, $competitor_procent) = array("100", "0");
                  $max_value = $audit->facebook_data->{$item["fb_name"]};
                  if ($audit->has_comp) {
                    list($your_procent, $competitor_procent) = percent_tuple($audit->facebook_data->{$item["fb_name"]},
                                                                $audit->competitor->facebook_data->{$item["fb_name"]});

                    $max_value = max($audit->facebook_data->{$item["fb_name"]}, 
                                  $audit->competitor->facebook_data->{$item["fb_name"]});
                  } ?>
                    <div class="stat-box">
                        <span class="stat-title"><?php echo $language[$item["name"]]; ?></span><?php 
                        if (!$edit_mode) { ?>
                          <div class="link">
                              <i class="fas fa-info-circle information"></i>
                              <div class="arrow" style="margin-top: 28px; margin-left: 22px;">
                                <div class="drop">
                                  <div class="line one"><?php echo $language[$item["name"] . " exp"]; ?></div>
                                </div>
                              </div>
                          </div><?php 
                        } else { 
                          visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]); 
                        }
                        
                        if ($audit->has_comp) { ?>
                          <div class="skills" data-percent="<?php echo $your_procent; ?>%">
                            <div class="title-bar">
                                <h5>You</h5>
                            </div>
                            <span class="procent font-blue">
                              <?php echo ($audit->has_comp) ? round($audit->facebook_data->{$item["fb_name"]}):""; ?>
                            </span>
                            <div style="clear: both;"></div>
                            <div class="skillbar blue"></div>  
                          </div>

                          <div class="skills" data-percent="<?php echo $competitor_procent; ?>%">
                            <div class="title-bar">
                                <h5><?php echo $audit->competitor_name; ?></h5>
                            </div>
                            <span class="procent font-red"><?php echo round($audit->competitor->facebook_data->{$item["fb_name"]}); ?></span>
                            <div style="clear: both;"></div>
                            <div class="skillbar red"></div>  
                          </div><?php 
                        } else { ?>
                          <span class="data-single font-blue"><?php echo round($audit->facebook_data->{$item["fb_name"]}); ?></span><?php 
                        } 

                        if ($audit->has_comp) { ?>
                          <hr class="x-as" />
                          <span class="left-value">0</span>
                          <span class="center-value"><?php echo ceil(($max_value / 2)); ?></span>
                          <span class="right-value"><?php echo ceil($max_value); ?></span><?php 
                        } ?>
                    </div> <?php 
                }
            endforeach; ?>
        </div>
        <div class="small-statistics">
        <?php
            foreach($facebook_blocks as $item):
                if (show_block($edit_mode, $audit->{$item["type"]}) && $item["is_icon"]) { 
                    $your_val = ($audit->facebook_data->{$item["fb_name"]} == 1) 
                                ? '<i class="fas fa-check-circle check"></i>' 
                                : '<i class="fas fa-times-circle not-check"></i>';
                    
                    ?>
                    <div class="stat-box">
                        <span class="stat-title"><?php echo $language[$item["name"]]; ?></span>
                        <?php 
                        if (!$edit_mode) { ?>
                            <div class="link">
                              <i class="fas fa-info-circle information"></i>
                              <div class="arrow">
                                <div class="drop">
                                  <div class="line one"><?php echo $language[$item["name"] . " exp"]; ?></div>
                                </div>
                              </div>
                            </div><?php 
                          } else { 
                            visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]); 
                          } 
                          if ($audit->has_comp) { ?>
                            <div class="your-stat">
                              <span class="title-bar">You</span>
                              <?php echo $your_val; ?>
                            </div>
                            <?php 
                              $comp_val = ($audit->competitor->facebook_data->{$item["fb_name"]} == 1) 
                                  ? '<i class="fas fa-check-circle check"></i>' 
                                  : '<i class="fas fa-times-circle not-check"></i>';
                            ?>
                            <div class="competitor-stat">
                                <span class="title-bar"><?php echo $audit->competitor_name; ?></span>
                                <?php echo $comp_val; ?>
                            </div><?php 
                          } else { ?>
                            <span class="check-field"><?php echo $your_val; ?></span><?php 
                          } ?>
                    </div>
                <?php 
                }
              endforeach; ?>
        </div>
        <div style="clear: both;"></div>
        <div class="facebook-advice advice">
            <span class="advice-title"><?php echo $language['facebook_advice']; ?></span>
            <div class="skills" data-percent="<?php echo $score['fb']; ?>%">
              <span class="procent font-red">
                <?php
                  if (!$edit_mode) { 
                    echo $score['fb'] . "%";
                  } else { ?>
                    <input type="number" min="1" max="100" class="score-input" data-score="facebook" value="<?php echo $score['fb']; ?>" name="facebook_score" id="facebook_score"/><?php
                  } 
                ?>
              </span>
              <div style="margin-top: 12px;" class="skillbar red"></div>  
            </div>
            <p> <?php 
              if ($edit_mode) { ?>
                <div style="clear: both;"></div>
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#facebook-info" method="post" enctype="multipart/form-data">
                  <textarea maxlength="999" input="text"  name="facebook_advice" id="facebook_advice"><?php echo  $advice['fb']; ?></textarea>
                </form><?php
              } else {  ?>
                <p style='font-size: 14px; font-weight: 100; line-height: 24px;'>
                    <?php echo "<pre>" . change_tags($advice['fb'], $client, $audit) . "</pre>"; ?></p><?php
                get_contact_info($phone, $mail, $calendar_link, $language, $user); 
              } ?>
            </p>
        </div>
    </div>
</section>
<?php } ?>
<?php if ($audit->instagram_bit && ($audit->instagram_vis_bit == 1 || $edit_mode)) { ?>
<section id="instagram-section">
    <div class="sidebar">
      <span class="title">Statistics</span>
      <ul>
        <?php if ($audit->facebook_bit && ($audit->facebook_vis_bit == 1 || $edit_mode)) { ?><li class="facebook-option active"><i class="fab fa-facebook-square"></i><span class="nav-position">Facebook</span></li><?php } ?>
        <?php if ($audit->instagram_bit && ($audit->instagram_vis_bit == 1 || $edit_mode)) { ?><li class="instagram-option"><i class="fab fa-instagram"></i><span class="nav-position">Instagram</span></li><?php } ?>
        <?php 
            if ($audit->website_bit && ($audit->website_vis_bit == 1 || $edit_mode)) { 
              if ($audit->has_website) {?>
                <li class="website-option"><i class="fas fa-globe"></i><span class="nav-position">Website</span></li>
        <?php } else { ?>
              <li class="" style="cursor: initial;"><i class="fas fa-globe"></i><span class="nav-position">Website</span><span style="font-size: 9px; position: absolute; right: 0px; bottom: -18px;">Wait a minute</span><span></li>
        <?php } 
        }?>
        <?php if ($audit->conclusion_vis_bit == 1 || $edit_mode) { ?><li class="conclusion-option"><i class="fas fa-check"></i><span class="nav-position">Conclusion</span></li><?php } ?>
      </ul>
      <a href="#" onclick="generatePDF()" class="button generate-pdf" style="background: #dbecfd; font-weight: bold; color: #4da1ff; box-shadow: none;">Generate PDF</a>
    </div>
    <div class="facebook-right">
        <span class="section-vis"><?php visibility_short_code($edit_mode, $audit->instagram_vis_bit, 'instagram_vis_bit', 'visibility-first-level'); ?></span>

        <i class="fab fa-instagram section-icon"></i>
        <span class="section-title">
          <?php echo ($user->instagram_title == "") ? $language['insta_title'] : $user->instagram_title; ?>
        </span>     
        <span class="section-subtitle">
          <?php echo ($user->instagram_sub_title == "") ? $language['insta_subtitle'] : $user->instagram_sub_title; ?>
        </span><?php
        if (!$audit->manual) { ?>
          <div style="width: 100%; height: auto; padding-right: 70px;">
              <div class="chart-holder">
                  <span class="stat-title"><?php echo $language['likes_on_post']; ?></span>
                  <div class="averages">
                      <span class="your_averages">You<span class="data font-blue"><?php echo number_format($sumPostLikes / count($audit->instagram_data->likesPerPost), 2); ?></span></span>
                      <?php if ($audit->has_comp): ?>
                      <span class="competitor_averages"><?php echo $audit->competitor_name; ?><span class="data font-red"><?php echo number_format($compSumPostLikes / count($audit->competitor->instagram_data->likesPerPost), 2); ?></span></span>
                      <?php endif; ?>
                  </div>
                  <div style="height: 220px">
                      <canvas id="lpd-chart" style="display: block; height: 100%;" class="chartjs-render-monitor"></canvas>
                  </div>
              </div>
          </div><?php 
        } ?>
        <div class="statistics">
          <?php
          // $audit->instagram_data->hashtags[1][0] = NULL;
          // $audit->instagram_data->hashtags[1][1] = NULL;
          // $audit->instagram_data->hashtags[1][2] = NULL;
          // $audit->competitor->instagram_data->hashtags[1][0] = NULL;
          // $audit->competitor->instagram_data->hashtags[1][1] = NULL;
          // $audit->competitor->instagram_data->hashtags[1][2] = NULL;
          // $audit->instagram_data->hashtags = NULL;
          // $audit->competitor->instagram_data->hashtags = 0;
          $height = ($audit->has_comp
            && isset($audit->competitor->instagram_data->hashtags[1][0])
            && isset($audit->instagram_data->hashtags[1][0])) ? "525px !important" : "345px !important"; 

            if (!$audit->manual && (isset($audit->instagram_data->hashtags[1][0])||isset($audit->competitor->instagram_data->hashtags[1][0]))) { ?>
              <div class="stat-box custom-height" style="height: <?php echo $height; ?>;">
                  <span class="stat-title"><?php echo $language['hastag_used']; ?></span>
                  <h3 style="margin-top: -35px;">You</h3>
                  <?php
                    $max_value = $audit->instagram_data->hashtags[1][0];
                    if ($audit->has_comp && (isset($audit->instagram_data->hashtags[1][0])&&isset($audit->competitor->instagram_data->hashtags[1][0]))) {
                      $max_value = max($audit->instagram_data->hashtags[1][0], 
                      $audit->competitor->instagram_data->hashtags[1][0]);
                    }
                  ?>
                  <?php if (isset($audit->instagram_data->hashtags[1][0])) { ?>
                    <div class="skills" data-percent="<?php echo normalize($max_value, $audit->instagram_data->hashtags[1][0])."%";?>">
                        <div class="title-bar-hashtags">
                            <h5>#<?php echo $audit->instagram_data->hashtags[0][0]; ?></h5>
                        </div>
                        <span class="procent font-blue procent-custom"><?php echo $audit->instagram_data->hashtags[1][0]; ?></span>
                        <div style="clear: both;"></div>
                        <div class="skillbar blue"></div>  
                  </div>
                  <?php } ?>
                  <?php if (isset($audit->instagram_data->hashtags[1][1])) { ?>
                    <div class="skills" data-percent="<?php echo normalize($audit->instagram_data->hashtags[1][0], $audit->instagram_data->hashtags[1][1]);?>%">
                        <div class="title-bar-hashtags">
                            <h5>#<?php echo $audit->instagram_data->hashtags[0][1]; ?></h5>
                        </div>
                        <span class="procent font-blue procent-custom"><?php echo $audit->instagram_data->hashtags[1][1]; ?></span>
                        <div style="clear: both;"></div>
                        <div class="skillbar blue"></div>  
                    </div>
                  <?php } ?>
                  <?php if (isset($audit->instagram_data->hashtags[1][2])) { ?>
                    <div class="skills" data-percent="<?php echo normalize($audit->instagram_data->hashtags[1][0], $audit->instagram_data->hashtags[1][2]);?>%">
                        <div class="title-bar-hashtags">
                            <h5>#<?php echo $audit->instagram_data->hashtags[0][2]; ?></h5>
                        </div>
                        <span class="procent font-blue procent-custom"><?php echo $audit->instagram_data->hashtags[1][2]; ?></span>
                        <div style="clear: both;"></div>
                        <div class="skillbar blue"></div>  
                    </div>
                  <?php } ?>
                  <div style="clear:both; margin-bottom: 20px;"></div>
                  <?php if ($audit->has_comp && isset($audit->competitor->instagram_data->hashtags[1][0])) { ?>
                      <h3><?php echo $audit->competitor_name; ?></h3>
                      <?php if (isset($audit->competitor->instagram_data->hashtags[1][0])) { ?>
                          <div class="skills" data-percent="<?php echo normalize($max_value, $audit->competitor->instagram_data->hashtags[1][0])."%";?>">
                              <div class="title-bar-hashtags">
                                  <h5>#<?php echo $audit->competitor->instagram_data->hashtags[0][0]; ?></h5>
                              </div>
                              <span class="procent font-red procent-custom"><?php echo $audit->competitor->instagram_data->hashtags[1][0]; ?></span>
                              <div style="clear: both;"></div>
                              <div class="skillbar red"></div>  
                          </div>
                      <?php } ?>
                      <?php if (isset($audit->competitor->instagram_data->hashtags[1][1])) { ?>
                        <div class="skills" data-percent="<?php echo normalize($max_value, $audit->competitor->instagram_data->hashtags[1][1]);?>%">
                            <div class="title-bar-hashtags">
                                <h5>#<?php echo $audit->competitor->instagram_data->hashtags[0][1]; ?></h5>
                            </div>
                            <span class="procent font-red procent-custom"><?php echo $audit->competitor->instagram_data->hashtags[1][1]; ?></span>
                            <div style="clear: both;"></div>
                            <div class="skillbar red"></div>  
                        </div>
                      <?php } ?>
                      <?php if (isset($audit->competitor->instagram_data->hashtags[1][2])) { ?>
                        <div class="skills" data-percent="<?php echo normalize($max_value, $audit->competitor->instagram_data->hashtags[1][2]);?>%">
                            <div class="title-bar-hashtags">
                                <h5>#<?php echo $audit->competitor->instagram_data->hashtags[0][2]; ?></h5>
                            </div>
                            <span class="procent font-red procent-custom"><?php echo $audit->competitor->instagram_data->hashtags[1][2]; ?></span>
                            <div style="clear: both;"></div>
                            <div class="skillbar red"></div>  
                        </div>
                      <?php } ?>
                  <?php } ?>
                  <hr class="x-as" style="margin-top:20px;" />
                  <span class="left-value">0</span>
                  <span class="center-value"><?php echo round(($max_value / 2)); ?></span>
                  <span class="right-value"><?php echo round($max_value); ?></span>
              </div><?php 
            }

            function manual_check($audit, $item, $edit_mode, $comp) {
              $base = ($comp) ? $audit->competitor : $audit;
              $value = $base->instagram_data->{$item['ig_name']};
              $str = ($comp) ? "comp-" : "";

              if ($base->manual && $edit_mode) {?>
                <input type="number" class="instagram" id="<?php echo "{$str}".$item["ig_name"]; ?>" value="<?php echo round($value); ?>" /></span><?php
              } else {
                echo round($value); // ,2
              }
            }

            foreach($instagram_blocks as $item):
                if (show_block($edit_mode, $audit->{$item["type"]})) { 
                    list($your_procent, $competitor_procent) = array("100", "0");
                    $max_value = $audit->instagram_data->{$item["ig_name"]};
                    if ($audit->has_comp) {
                      list($your_procent, $competitor_procent) = percent_tuple($audit->instagram_data->{$item["ig_name"]},
                        $audit->competitor->instagram_data->{$item["ig_name"]});
                    
                      $max_value = max($audit->instagram_data->{$item["ig_name"]}, 
                                    $audit->competitor->instagram_data->{$item["ig_name"]});
                    }
                    ?>
                    <div class="stat-box">
                        <span class="stat-title"><?php echo $language[$item["name"]]; ?></span><?php 

                        if (!$edit_mode) { ?>
                            <div class="link">
                              <i class="fas fa-info-circle information"></i>
                              <div class="arrow" style="margin-top: 28px; margin-left: 22px;">
                                <div class="drop">
                                  <div class="line one"><?php echo $language[$item["name"] . " exp"]; ?></div>
                                </div>
                              </div>
                            </div> <?php 
                        } else { 
                          visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]);
                        }
                        
                        if ($audit->has_comp) { ?>
                          <div class="skills you" data-percent="<?php echo $your_procent; ?>%">
                            <div class="title-bar">
                              <h5>You</h5>
                            </div>
                            <span class="procent font-blue"><?php 
                              echo ($audit->has_comp) ? manual_check($audit, $item, $edit_mode, 0) : ""; ?>
                            </span>
                            <div style="clear: both;"></div>
                            <div class="skillbar blue"></div>  
                          </div>
                      
                          <div class="skills competitor" data-percent="<?php echo $competitor_procent; ?>%">
                                  <div class="title-bar">
                                      <h5><?php echo $audit->competitor_name; ?></h5>
                                  </div>
                                  <span class="procent font-red"><?php manual_check($audit, $item, $edit_mode, 1); //echo round($audit->competitor->instagram_data->{$item["ig_name"]}); ?></span>
                                  <div style="clear: both;"></div>
                                  <div class="skillbar red"></div>  
                          </div>
                      
                          <hr class="x-as" />
                          <span class="left-value">0</span>
                          <span class="center-value"><?php echo floor(($max_value / 2)); ?></span>
                          <span class="right-value"><?php echo floor($max_value); ?></span> <?php 
                        } else { ?>
                          <span class="data-single font-blue"><?php manual_check($audit, $item, $edit_mode, 0); ?></span><?php 
                        } ?>
                    </div>
                <?php 
                }
            endforeach; ?>
        </div>
        <div style="clear: both;"></div>
        <div class="instagram-advice advice">
            <span class="advice-title"><?php echo $language['instagram_advice']; ?></span>
            <div class="skills" data-percent="<?php echo $score['ig']; ?>%">
                <span class="procent font-red">
                <?php
                    if (!$edit_mode) { 
                        echo $score['ig'] . "%";
                    } else {
                        ?><input type="number" min="1" max="100" class="score-input" data-score="instagram" value="<?php echo $score['ig']; ?>" name="instagram_score" id="instagram_score"/><?php
                    } 
                ?>
                </span>
                <div style="margin-top: 12px;" class="skillbar red"></div>  
            </div>
            <p>
                <?php if ($edit_mode) { ?>
                  <div style="clear: both;"></div>
                  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#instagram-info" method="post" enctype="multipart/form-data">
                    <textarea maxlength="999" input="text"  name="instagram_advice" id="instagram_advice"><?php echo  $advice['ig']; ?></textarea>
                  </form><?php
                } else {  ?>
                    <p style='font-size: 14px; font-weight: 100; line-height: 24px;'>
                        <?php echo "<pre>" . change_tags($advice['ig'], $client, $audit) . "</pre>"; ?></p><?php
                    get_contact_info($phone, $mail, $calendar_link, $language, $user); 
                } ?>
            </p>
        </div>
    </div>
</section>
<?php } ?>
<?php if ($audit->website_bit && ($audit->website_vis_bit == 1 || $edit_mode)) { ?>
<section id="website-section">
    <div class="sidebar">
      <span class="title">Statistics</span>
      <ul>
        <?php if ($audit->facebook_bit && ($audit->facebook_vis_bit == 1 || $edit_mode)) { ?><li class="facebook-option active"><i class="fab fa-facebook-square"></i><span class="nav-position">Facebook</span></li><?php } ?>
        <?php if ($audit->instagram_bit && ($audit->instagram_vis_bit == 1 || $edit_mode)) { ?><li class="instagram-option"><i class="fab fa-instagram"></i><span class="nav-position">Instagram</span></li><?php } ?>
        <?php 
            if ($audit->website_bit && ($audit->website_vis_bit == 1 || $edit_mode)) { 
              if ($audit->has_website) {?>
                <li class="website-option"><i class="fas fa-globe"></i><span class="nav-position">Website</span></li>
        <?php } else { ?>
              <li class="" style="cursor: initial;"><i class="fas fa-globe"></i><span class="nav-position">Website</span><span style="font-size: 9px; position: absolute; right: 0px; bottom: -18px;">Wait a minute</span><span></li>
        <?php } 
        }?>
        <?php if ($audit->conclusion_vis_bit == 1 || $edit_mode) { ?><li class="conclusion-option"><i class="fas fa-check"></i><span class="nav-position">Conclusion</span></li><?php } ?>
      </ul>
      <a href="#" onclick="generatePDF()" class="button generate-pdf" style="background: #dbecfd; font-weight: bold; color: #4da1ff; box-shadow: none;">Generate PDF</a>
    </div>
    <div class="facebook-right">
    <!-- <div class="wait-screen">Wait a minute till crawl is completed.</div> -->

    <span class="section-vis"><?php visibility_short_code($edit_mode, $audit->website_vis_bit, 'website_vis_bit', 'visibility-first-level'); ?></span>

    <i class="fas fa-globe section-icon"></i>
        <span class="section-title"><?php 
          echo ($user->website_title == "") ? $language['website_title'] : $user->website_title; ?>
        </span>     
        <span class="section-subtitle"><?php 
          echo ($user->website_sub_title == "") ? $language['website_subtitle'] : $user->website_sub_title; ?>
        </span>   

        <div class="statistics">
        <?php
            foreach($website_blocks as $item):
                if (show_block($edit_mode, $audit->{$item["type"]}) && !$item["is_icon"]) {

                    if ($item['name'] != "Mobile Friendly") {
                      $arr = explode("s", $audit->{$item['db_name']}, 2);
                      $first = $arr[0];
                      $your_string = $first . "s";
                      $your_value = (double) $first;

                      if ($audit->has_comp) {
                        $arr = explode("s", $audit->competitor->{$item['db_name']}, 2);
                        $first = $arr[0];
                        $comp_string = $first . "s";
                        $comp_value = (int) $first;
                      
                        $max_value = max($your_value, $comp_value);
                      }

                    } else {
                      $arr = explode("/", $audit->{$item['db_name']}, 2);
                      $first = $arr[0];
                      $your_string = $first;
                      $your_value = (double) $first;

                      if ($audit->has_comp) {
                        $arr = explode("/", $audit->{$item['db_name']}, 2);
                        $first = $arr[0];
                        $your_string = ($audit->has_comp) ? $first : $first . "/100";
                        $your_value = (double) $first;

                        if ($audit->has_comp) {
                            $arr = explode("/", $audit->competitor->{$item['db_name']}, 2);
                            $first = $arr[0];
                            $comp_string = $first;

                            $comp_value = (int) $first;
                        }

                        if ($audit->has_comp) {
                            $max_value = 100;
                        }
                    }
                  }
                    if ($audit->has_comp) {
                        if ($item['name'] != "Mobile Friendly") {
                          if (round($your_value) > round($comp_value)) {
                            $your_procent = "100";
                            $competitor_procent = (string)normalize($your_value, $comp_value);
                          } else {
                            $competitor_procent = "100";
                            $your_procent = (string)normalize($your_value, $comp_value);
                          }
                        } else {
                          $competitor_procent = $comp_value;
                          $your_procent = $your_value;
                        }

                        if (round($your_value) > round($comp_value)) {
                          $your_procent = (string)normalize($your_value, 100);
                          $competitor_procent = (string)normalize($your_value, $comp_value);
                        } else {
                          $competitor_procent = (string)normalize($comp_value, 100);
                          $your_procent = (string)normalize($your_value, $comp_value);
                        } 
                    }
                  ?>
                    <div class="stat-box">
                        <span class="stat-title"><?php echo $language[$item["name"]]; ?></span><?php
                        if (!$edit_mode) { ?>
                            <div class="link">
                              <i class="fas fa-info-circle information"></i>
                              <div class="arrow" style="margin-top: 28px; margin-left: 22px;">
                                <div class="drop">
                                  <div class="line one"><?php echo $language[$item["name"] . " exp"]; ?></div>
                                </div>
                              </div>
                            </div><?php 
                        } else { 
                          visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]);
                        } 
                        
                        if ($audit->has_comp) { ?>
                            <div class="skills" data-percent="<?php echo $your_procent; ?>%">
                                <div class="title-bar">
                                    <h5>You</h5>
                                </div>
                                <span class="procent font-blue">
                                    <?php echo ($audit->has_comp) ? $your_string: ""; ?>
                                </span>
                                <div style="clear: both;"></div>
                                <div class="skillbar blue"></div>  
                            </div>
                        
                            <div class="skills" data-percent="<?php echo $competitor_procent; ?>%">
                                    <div class="title-bar">
                                        <h5><?php echo $audit->competitor_name; ?></h5>
                                    </div>
                                    <span class="procent font-red"><?php echo $comp_string; ?></span>
                                    <div style="clear: both;"></div>
                                    <div class="skillbar red"></div>  
                            </div>
                        
                            <hr class="x-as" />
                            <span class="left-value">0</span>
                            <span class="center-value"><?php echo ceil(($max_value / 2)); ?></span>
                            <span class="right-value"><?php echo $max_value; ?></span>
                        <?php } else { ?>
                            <span class="data-single font-blue"><?php echo $your_string; ?></span>                            
                        <?php } ?>
                    </div>
                <?php 
            }
          endforeach; ?>
        </div>
        <div class="small-statistics">
        <?php
            foreach($website_blocks as $item):
                if (show_block($edit_mode, $audit->{$item["type"]}) && $item["is_icon"]) { 
                    $your_val = ($audit->{$item["db_name"]} == 1) 
                                ? '<i class="fas fa-check-circle check"></i>' 
                                : '<i class="fas fa-times-circle not-check"></i>';
                    ?>
                    <div class="stat-box">
                        <span class="stat-title"><?php echo $language[$item["name"]]; ?></span><?php 
                        if (!$edit_mode) { ?>
                            <div class="link">
                              <i class="fas fa-info-circle information"></i>
                              <div class="arrow">
                                <div class="drop">
                                  <div class="line one"><?php echo $language[$item["name"] . " exp"]; ?></div>
                                </div>
                              </div>
                            </div><?php 
                        } else {
                          visibility_short_code($edit_mode, $audit->{$item["type"]}, $item["type"]);
                        } ?>
                        
                        <?php if ($audit->has_comp) { ?>
                        <div class="your-stat">
                            <span class="title-bar">You</span>
                            <?php echo $your_val; ?>
                        </div>
                        <?php
                            $comp_val = ($audit->competitor->{$item["db_name"]} == 1) 
                                ? '<i class="fas fa-check-circle check"></i>' 
                                : '<i class="fas fa-times-circle not-check"></i>';
                        ?>
                            <div class="competitor-stat">
                                <span class="title-bar"><?php echo $audit->competitor_name; ?></span>
                                <?php echo $comp_val; ?>
                            </div><?php 
                          } else { ?>
                            <span class="check-field"><?php echo $your_val; ?></span>
                        <?php } ?>
                    </div>
                <?php 
                }
              endforeach; ?>
        </div>
        <div style="clear: both;"></div>
        <div class="facebook-advice advice">
          <span class="advice-title"><?php echo $language['website_advice']; ?></span>
            <div class="skills" data-percent="<?php echo $score['wb']; ?>%">
              <span class="procent font-red"> <?php
                if (!$edit_mode) { 
                  echo $score['wb'] . "%";
                } else { ?>
                  <input type="number" min="1" max="100" class="score-input" data-score="website" value="<?php echo $score['wb']; ?>" name="wesbite_score" id="website_score"/><?php
                } ?>
              </span>
              <div style="margin-top: 12px;" class="skillbar red"></div>  
            </div>
            <p>
              <?php if ($edit_mode) { ?>
                <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#website-info" method="post" enctype="multipart/form-data">
                  <textarea maxlength="999" input="text"  name="website_advice" id="website_advice"><?php echo  $advice['wb']; ?></textarea>
                </form><?php
              } else {  ?>
                  <p style='font-size: 14px; font-weight: 100; line-height: 24px;'>
                      <?php echo "<pre>" . change_tags($advice['wb'], $client, $audit) . "</pre>"; ?></p><?php
                  get_contact_info($phone, $mail, $calendar_link, $language, $user); 
              } ?>
            </p>
        </div>
    </div>
</section>
<?php } ?>
<section id="conclusion-section">
    <div class="sidebar">
      <span class="title">Statistics</span>
      <ul>
        <?php if ($audit->facebook_bit && ($audit->facebook_vis_bit == 1 || $edit_mode)) { ?><li class="facebook-option active"><i class="fab fa-facebook-square"></i><span class="nav-position">Facebook</span></li><?php } ?>
        <?php if ($audit->instagram_bit && ($audit->instagram_vis_bit == 1 || $edit_mode)) { ?><li class="instagram-option"><i class="fab fa-instagram"></i><span class="nav-position">Instagram</span></li><?php } ?>
        <?php 
            if ($audit->website_bit && ($audit->website_vis_bit == 1 || $edit_mode)) { 
              if ($audit->has_website) {?>
                <li class="website-option"><i class="fas fa-globe"></i><span class="nav-position">Website</span></li>
        <?php } else { ?>
              <li class="" style="cursor: initial;"><i class="fas fa-globe"></i><span class="nav-position">Website</span><span style="font-size: 9px; position: absolute; right: 0px; bottom: -18px;">Wait a minute</span><span></li>
        <?php } 
        }?>
        <?php if ($audit->conclusion_vis_bit == 1 || $edit_mode) { ?><li class="conclusion-option"><i class="fas fa-check"></i><span class="nav-position">Conclusion</span></li><?php } ?>
      </ul>
      <a href="#" onclick="generatePDF()" class="button generate-pdf" style="background: #dbecfd; font-weight: bold; color: #4da1ff; box-shadow: none;">Generate PDF</a>
    </div>
    <div class="facebook-right">
        <div class="left">
            <span class="section-title"><?php echo $language['conclusion']; ?></span>   
            <span class="section-vis"><?php visibility_short_code($edit_mode, $audit->conclusion_vis_bit, 'conclusion_vis_bit', 'visibility-first-level'); ?></span>

            <?php 
                if ($audit->conclusion_vis_bit == 1 || $edit_mode) {
                  if ($edit_mode) { ?>
                  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#conclusion" method="post" enctype="multipart/form-data">
                    <textarea maxlength="999" input="text"  name="conclusion" id="conclusion"><?php if ($audit->conclusion == NULL) { echo $user->conclusion_audit; } else { echo $audit->conclusion; } ?></textarea>
                  </form>

                  <div class="description-tags">
                    You can insert the following tags in all the text fields: <span style="font-size: 10px; color: #000;">#{client}, #{competitor}, #{fb_score}, #{insta_score}, #{website_score}</span>
                  </div> <?php
                  } else {?>
                    <p style='font-size: 14px; font-weight: 100; line-height: 24px;'><?php 
                      echo "<pre>" . change_tags(($audit->conclusion == NULL) ? 
                        $user->conclusion_audit : $audit->conclusion, $client, $audit) . "</pre>" ?>
                    </p><?php
                    ?><span class="mobile-hide"><?php get_contact_info($phone, $mail, $calendar_link, $language, $user); ?></span><?php 
                  }
                }
            ?>
        </div>
        <div class="right">
          <div class="facebook-advice advice">
            <span class="advice-title"><?php echo $language['facebook_advice']; ?></span>
            <div class="skills facebook" data-percent="<?php echo $score['fb']; ?>%">
              <span class="procent facebook font-red"><?php echo $score['fb']; ?>%</span>
              <div style="clear: both;"></div>
              <div class="skillbar red facebook"></div>
            </div>
            <span class="advice-title"><?php echo $language['instagram_advice']; ?></span>
            <div class="skills instagram" data-percent="<?php echo $score['ig']; ?>%">
              <span class="procent instagram font-red"><?php echo $score['ig']; ?>%</span>
              <div style="clear: both;"></div>
              <div class="skillbar red instagram"></div>
            </div>
            <span class="advice-title"><?php echo $language['website_advice']; ?></span>
            <div class="skills website" data-percent="<?php echo $score['wb']; ?>%">
              <span class="procent website font-red"><?php echo $score['wb']; ?>%</span>
              <div style="clear: both;"></div>
              <div class="skillbar red website"></div>
            </div>
          </div>
          <span class="desktop-hide">
            <div style="clear: both;"></div>
              <?php get_contact_info($phone, $mail, $calendar_link, $language, $user); ?>
          </span> 
        </div>
    </div>
</section>
</body>
<script>
  var commonPost = {
    'type': 'audit',
    'audit': '<?php echo $audit->id; ?>',
  }
  <?php if ($audit->website_bit && !$audit->has_website): ?>
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
            logError(send_error, 'page-templates/audit_page_v2.php', 'toggle_visibility');
        },
      });
    }
    crawlFinishedCheck();
  <?php endif; ?>
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
                  logError(send_error, 'page-templates/audit_page_v2.php', 'insert_view');
              },
            });
          });
      });
  <?php } ?>

  SetColor("<?php echo $theme_color; ?>");
  function SetColor(color) {
    var body = document.getElementsByTagName("body")[0];
    body.style.setProperty("--base-color", color);
    body.style.setProperty("--base-shaded-dark", shadeColor(color));
    body.style.setProperty("--complement-color", complementColor(color));
  }

  <?php // Website Crawl
  if (isset($_GET['view'])) { ?>
    $(window).ready(function() {
      $(this).one('mousemove', function() { 
        // mouse move
      }).one('scroll', function() {
        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: { action: 'insert_view',  ...commonPost },
          success: function (response) { console.log(response); },
          error: function (xhr, textStatus, errorThrown) {
            var send_error = error_func(xhr, textStatus, errorThrown, data);
            logError(send_error, 'page-templates/audit_page.php', 'insert_view');
          },
        });
      });
    });<?php
  }

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

  $(function() {
    // Share & Track Modal
    var modalData = {
      'text': "<span class='title'>This link is copied to your clipboard:</span>",
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

    // Auto Mail + color Model
    var modalData = {
      text:`<span style="font-weight:bold; font-size: 18px;">Configuration audit</span>`,
      subtext:`Do you want to sent this client automatic reminders?<br/>
        <input type="checkbox" id="mail_bit_check" <?php echo $audit->mail_bit ? 'checked': ''; ?>><br/><br/>
        Social Audify can send automatic reminders if your lead does not open the audit. You can configure the emails:
        <a style="margin-bottom:10px" href='/profile-page/#mail-settings'>[here]</a><br><br>
        <span style="font-weight: 500;">Theme color:</span><br /> <input type="color" id="color" value="<?php echo $theme_color; ?>">
        <i class="fas fa-undo" onclick="$('#color').val('<?php echo $theme_color; ?>')" ></i><br /><br />
        <span style="font-weight: 500;">Audit language:</span><br /><div id="delete-this-audit" class="languages"> <i class="fas fa-trash"></i> </div>
        <?php echo $language_options; ?><br/><?php echo $template_options; ?>`,
      confirm: 'config_confirmed'
    }

    var configModal = initiateModal('configModal', 'confirm', modalData);
    $('#config_link').click(function() {
      $('#color').val('<?php echo $theme_color; ?>');
      showModal(configModal);
      template_callback();
    });

    function template_callback() {
      $('#template').on('change',function() {
        console.log("VERSION" + $(this).val().slice(-3, -2));
        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: { action: 'update_meta_template', 
                  template: $(this).val().slice(-3, -2), 
                  post_id: <?php echo $post_id ?>,
                  ...commonPost },
          success: function (response) {
            console.log(response);
            window.location.replace(`${window.location.pathname}?action=configmodal`)
          },
          error: function (xhr, textStatus, errorThrown) {
            var send_error = error_func(xhr, textStatus, errorThrown, data);
            logError(send_error, 'page-templates/audit_page.php', 'toggle_visibility');
          }
        });
      });
    }

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
      'subtext': `We do not send the first email about the audit at this time!
        Click on share and track to copy the link and email from your own email. 
        Then select in configuration whether or not you would like us to start sending the follow ups.`,
      'confirm': ''
    }

    var firstTimeModal = initiateModal('firstTimeModal', 'error', firstTimeModalData);

    <?php 
    if ($user->first_time == 0) { ?>
      showModal(firstTimeModal);
      <?php $user->update('User', 'first_time', 1); 
    } ?>


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
  });

  $(document).ready(function() {
    startAnimation();

    $( ".instagram-option" ).click(function() {
      $(".facebook-option").removeClass("active");
      $(".website-option").removeClass("active");
      $(".conclusion-option").removeClass("active");
      $(".instagram-option").addClass("active");
      $("#facebook-section").css("display", "none");
      $("#instagram-section").css("display", "block");
      $("#website-section").css("display", "none");
      $("#conclusion-section").css("display", "none");
      startAnimation();
    });

    $( ".facebook-option" ).click(function() {
      $(".instagram-option").removeClass("active");
      $(".website-option").removeClass("active");
      $(".conclusion-option").removeClass("active");
      $(".facebook-option").addClass("active");
      $("#facebook-section").css("display", "block");
      $("#instagram-section").css("display", "none");
      $("#website-section").css("display", "none");
      $("#conclusion-section").css("display", "none");
      startAnimation();
    });

    $( ".website-option" ).click(function() {
      $(".instagram-option").removeClass("active");
      $(".facebook-option").removeClass("active");
      $(".conclusion-option").removeClass("active");
      $(".website-option").addClass("active");
      $("#website-section").css("display", "block");
      $("#instagram-section").css("display", "none");
      $("#facebook-section").css("display", "none");
      $("#conclusion-section").css("display", "none");
      startAnimation();
    });

    $( ".conclusion-option" ).click(function() {
      $(".instagram-option").removeClass("active");
      $(".facebook-option").removeClass("active");
      $(".website-option").removeClass("active");
      $(".conclusion-option").addClass("active");
      $("#conclusion-section").css("display", "block");
      $("#instagram-section").css("display", "none");
      $("#facebook-section").css("display", "none");
      $("#website-section").css("display", "none");
      startAnimation();
    });

    <?php if ($audit->facebook_bit == 0 && $audit->instagram_bit || ($audit->facebook_vis_bit == 0 && $audit->instagram_vis_bit != 0 && !$edit_mode)) { ?>
        $(".facebook-option").removeClass("active");
        $(".instagram-option").addClass("active");
        $("#facebook-section").css("display", "none");
        $("#instagram-section").css("display", "block");
    <?php } elseif ((!$audit->facebook_bit && !$audit->instagram_bit && $audit->website_bit)  || ($audit->facebook_vis_bit == 0  
                    && $audit->instagram_vis_bit == 0
                    && $audit->website_vis_bit != 0 && !$edit_mode)) { ?>
        $(".facebook-option").removeClass("active");
        $(".website-option").addClass("active");
        $("#facebook-section").css("display", "none");
        $("#website-section").css("display", "block");
    <?php } elseif ((!$audit->facebook_bit && !$audit->instagram_bit && !$audit->website_bit) || ($audit->facebook_vis_bit == 0
                    && $audit->instagram_vis_bit == 0 
                    && $audit->website_vis_bit == 0 && !$edit_mode)) { ?>
        $(".facebook-option").removeClass("active");
        $(".conclusion-option").addClass("active");
        $("#facebook-section").css("display", "none");
        $("#conclusion-section").css("display", "block");
    <?php } ?>
    

    function startAnimation() {
      $('.skills').each(function() {
        // console.log($(this).data("percent"));
        var bar = $(this).find('.skillbar');
        bar.width("0");
        bar.animate({
          width:$(this).data("percent"),
        }, 1000);  
      });

      // TODO: raar side effectje dat de getallen inet meer kloppen
      // $(".procent").each(function() {
      //   if (!$(this).html().includes("%")) {
      //     var v = parseInt($(this).html());
      //     if (!isNaN(v)) {
      //       countAnimationFromTo($(this), Math.round(v / 2), v, 500);
      //     }
      //   }
      // });

      <?php if (!$audit->has_comp): ?>
      // $('.data-single').each(function() {
      //   var v = parseInt($(this).text());
      //   if (!isNaN(v)) {
      //       countAnimationFromTo($(this), Math.round(v / 2), v, 500);
      //   }
      // });
      <?php endif; ?>
    }

    // On change of an text area show update all
    $("textarea, input[type=number].instagram").on('keyup paste change', function() {
      $(this).data('changed', true);
      toggleUpdate(true);

      var propId = $(this).prop('id');
      // Disable slider TODO: kijken wat we gaan doen met die sliders
      if ($(this).is('textarea') && propId.includes('_advice')) {
        var adviceType = propId.replace('_advice', '');
        console.log('disabled');
        handleSlider(adviceType);

        // console.log($(this).val());
        // Enable slider if value is empty
        if ($(this).val() == '') {
          console.log('enabled');
          type = (propId.includes('facebook')) ? 'fb' : (propId.includes('instagram')) ? 'ig' : 'wb';
          if (!!sliderData[type]) {
            handleSlider(adviceType, sliderData[type].range, sliderData[type].text);
          }
        }
      }
    });

    function percent_diff(val1, val2) {
      return Math.round(val1 / Math.max(val2, 1) * 100);
    }

    function percent_tuple(val1, val2) {
      if (val1 > val2) {
        return ["100", percent_diff(val2, val1).toString()];
      } else {
        return [percent_diff(val1, val2).toString(), "100"];
      }
    }

    $('input[type=number].instagram').on('focusout', function() {
      // todol
      var box = $(this).parents(".stat-box");
      var you = box.find('.skills.you');
      var comp = box.find('.skills.competitor');

      var val1 = parseInt(you.find('input[type=number]').val());
      var val2 = parseInt(comp.find('input[type=number]').val());
      var [p1, p2] = percent_tuple(val1, val2);


      you.data('percent', `${p1}%`);
      you.find('.skillbar').animate({
        width:`${p1}%`,
      }, 1000);  

      comp.data('percent', `${p2}%`);
      comp.find('.skillbar').animate({
        width:`${p2}%`,
      }, 1000);
      var biggestValue = Math.max(val1, val2);
      // instant box.find('.right-value').text(biggestValue);
      countAnimation(box.find('.center-value'), Math.round(biggestValue / 2));
      countAnimation(box.find('.right-value'), biggestValue);
    });
    
    // NEW ranges
    $("input[type=number].score-input").on('change paste keyup', function() {
      $(this).data('changed', true);
      
      // Animate while changing line 1241
      $(this).val(Math.min($(this).val(), 100));
      $(this).parent().next(".skillbar").width(`${$(this).val()}%`);
      
      // changes the percentages in the conclusion
      var type = $(this).data("score");
      $(`.procent.${type}`).text(`${$(this).val()}%`);
      $(`.skills.${type}`).data("percent", `${$(this).val()}%`);

      toggleUpdate(true);
    });

    // Animate after changing line 1230
    // $("input[type=number]").on('focusout', function() {      
    //   $(this).parent().parent().data("percent", `${$(this).val()}%`);
    //   startAnimation();
    // });

    // if the iframe choice changes
    $("input:radio[class=iframe-radio]").on('click', function() {
      $(this).parent().children('input:radio:checked').prop("checked", false);
      $(this).parent().children('#iframe-input').css("display", $(this).data('display'));
      $(this).prop("checked", true);
      toggleUpdate(true);
    });

    // if the iframe changes 
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
        ...getChanged("input[type=number].instagram", true),
        ...getChanged("input[type=number].score-input"),
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
  });
  console.log("<?php echo $audit->instagram_bit ?>");

  // Graph Generate
  <?php if ($audit->instagram_bit == "1" && $audit->manual == 0) { ?>
    var data_array = [<?php echo json_encode($audit->instagram_data->likesPerPost); ?>];

    <?php if ($audit->has_comp && (isset($audit->competitor) & !$audit->competitor->manual)) { ?>
      data_array.push(<?php echo json_encode($audit->competitor->instagram_data->likesPerPost); ?>);
    <?php } ?>
    
    var theme = "<?php echo $theme_color; ?>";
    var allLines = Array(Math.max(data_array[0].length, 12)).fill().map((_, index) => index);
    // complementColor(theme)
    generateLineChart('lpd-chart', data_array, allLines, [false, true], [theme, "#e36364"]);
  <?php } ?>

  <?php if ($edit_mode) { ?>
    // Visibility function : TODO : hier ook mooier als functions.php de geupdate visibility bool terug geeft...
    var toggle_visibility = function(field_name) {
      var field = $(`#${field_name}_icon`);
      var icon = field.find('i');
      field.html("<div class='lds-dual-ring'></div>");

      if (typeof icon[0] !== 'undefined') {
        var visible = icon.attr('class').endsWith("-slash");
        var icon = '<i style="color: #000 !important;" class="information far fa-eye' + (visible ? '"' : '-slash"') + '></i>'

        $.ajax({
          type: "POST",
          url: ajaxurl,
          data: { action: 'toggle_visibility', field: field_name , ...commonPost },
          success: function () { field.html(icon) },
          error: function (xhr, textStatus, errorThrown) {
            var send_error = error_func(xhr, textStatus, errorThrown, data);
            logError(send_error, 'page-templates/audit_page_v2.php', 'toggle_visibility');
          },
        });
      }
    };

      // Dynamic slider functions
    function handleSlider(type, range = false, text = false) {
      var slider = $('#' + type + '_score');
      var advice = $('#' + type + '_advice');

      slider.off('change');
      slider.on('change', function(e) {
        if (text) {
          changeAdvice(slider.val(), range, advice, text);
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
    ?>

    var sliderData = {<?php
      if ($audit->facebook_bit == "1") : ?>
        fb: { 
          <?php if ($audit->facebook_advice != "" && !advice_equal_to_user($user, $audit, 'fb')) :?>
            range: false,
            text: false,
          <?php else: ?>
            range: {
              one: <?php echo $user->range_number_fb_1; ?>,
              two: <?php echo $user->range_number_fb_2; ?>,
            },
            text: {
              one: <?php replace_lbs($user->text_fb_1); ?>,
              two: <?php replace_lbs($user->text_fb_2); ?>,
              three: <?php replace_lbs($user->text_fb_3); ?>,
            },
          <?php endif; ?>
        },
      <?php endif;
      if ($audit->instagram_bit == "1"): ?>
        ig: {
          <?php if ($audit->instagram_advice != "" && !advice_equal_to_user($user, $audit, 'ig')) : ?>
            range: false,
            text: false,
          <?php else: ?>
            range: {
              one: <?php echo $user->range_number_insta_1; ?>,
              two: <?php echo $user->range_number_insta_2; ?>,
            },
            text: {
              one: <?php replace_lbs($user->text_insta_1); ?>,
              two: <?php replace_lbs($user->text_insta_2); ?>,
              three: <?php replace_lbs($user->text_insta_3); ?>,
            },
          <?php endif; ?>
        }, 
      <?php endif;
      if ($audit->website_bit == "1"): ?>
        wb: {
          <?php if ($audit->website_advice != "" && !advice_equal_to_user($user, $audit, 'wb')): ?>
            range: false, // disabled slider text
            text: false,
          <?php else: ?>
            range: {
              one: <?php echo $user->range_number_website_1; ?>,
              two: <?php echo $user->range_number_website_2; ?>,
            },
            text: {
              one: <?php replace_lbs($user->text_website_1); ?>,
              two: <?php replace_lbs($user->text_website_2); ?>,
              three: <?php replace_lbs($user->text_website_3); ?>,
            },
          <?php endif; ?>
        },
      <?php  endif; ?>
    }; // END slider data
    if (!!sliderData.fb) {
      handleSlider('facebook', sliderData.fb.range, sliderData.fb.text);
    }
    if (!!sliderData.ig) {
      handleSlider('instagram', sliderData.ig.range, sliderData.ig.text);
    }
    if (!!sliderData.wb) {
      handleSlider('website', sliderData.wb.range, sliderData.wb.text);
    }
  <?php } ?>
</script>
</html>
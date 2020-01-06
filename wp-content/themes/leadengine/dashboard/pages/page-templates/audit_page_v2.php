<?php
/**
 * Template Name: Audit page v2
 */
?>
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
 $mail = $author->user_email;

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

$leadengine = get_template_directory_uri();

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
    if (isset($mail) && $mail != "") { ?><a href='mailto: <?php echo $mail; ?>' class="text-link"><i class="fas fa-envelope"></i><?php echo $mail; ?></a> <?php }
    if (isset($phone) && $phone != "") { ?><a href="callto: <?php echo $phone; ?>" class="text-link"><i class="fas fa-phone"></i><?php echo $phone; ?></a> <?php }
   
    if($calendar_link != "") { ?>
    <div class="buttons">
        <a href="<?php echo $calendar_link; ?>" class="button" style="margin-left: 0px;">
        <?php if ($user->appointment_text == "") { ?>
              <?php echo $language['make_appointment']; ?>
            <?php } else {
                echo $user->appointment_text;
            } ?>
        </a>
    </div>
    <?php }
}

function show_block($edit_mode, $visible) {
    return ($edit_mode || $visible);
}

function normalize($val1, $val2) {
    if($val1 > $val2) {
        return ($val2 / $val1) * 100;
    } else {
        return ($val1 / $val2) * 100;
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

?>
<html>
<head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-149815594-1"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-149815594-1');
  </script>

  <title>Audit</title>
  <!-- TODO: Moet nog met chrome canary worden gecheckt... -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

  <link rel="stylesheet" href="<?php echo $leadengine; ?>/dashboard/assets/styles/audit.css<?php echo $cache_version; ?>" type="text/css">
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/modal.js<?php echo $cache_version; ?>"></script>
  <script src="<?php echo $leadengine; ?>/dashboard/assets/scripts/functions.js<?php echo $cache_version; ?>"></script>

  <script>var ajaxurl = '<?php echo admin_url('admin-ajax.php');?>';</script>

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
<header>
    <div class="audit-name"><?php echo $audit->name; ?></div>
    <div class="languages">Dutch <i class="fas fa-chevron-down"></i></div>
</header>
<section class="introduction">
    <div class="sidebar">
        <div class="audit-owner">
            <div class="profile-picture">
                <?php echo get_wp_user_avatar($author_id, "original"); ?>
            </div>
            <span class="name"><?php $company = get_user_meta($author_id, 'rcp_company', true ); if ($company == "") { echo $author->display_name; } else { echo $company; }?></span>
            <span class="contactme">Contact me</span>
            <div class="contact-icons">
                <?php if (isset($mail) && $mail != "") { ?><a href="mailto: <?php echo $mail; ?>"><i class="fas fa-envelope"></i></a><?php } ?>
                <a href="#"><i class="fas fa-globe"></i></a>
                <?php if (isset($phone) && $phone != "") { ?><a href="callto:<?php echo $phone; ?>"><i class="fas fa-phone"></i></a><?php } ?>
            </div>
        </div>
    </div>
    <div class="introduction-right">
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
        <div class="video">
        <?php
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
        ?>
            <!-- <div class="video-iframe">

                <iframe height="315" src="https://www.youtube.com/embed/unU9vpLjHRk" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div> -->
        </div>
    </div>
</section>
<section id="facebook-section">
    <div class="sidebar">
        <span class="title">Statistics</span>
        <ul>
            <li class="facebook-option active"><i class="fab fa-facebook-square"></i><span class="nav-position">Facebook</span></li>
            <li class="instagram-option"><i class="fab fa-instagram"></i><span class="nav-position">Instagram</span></li>
            <li class="website-option"><i class="fas fa-globe"></i><span class="nav-position">Website</span></li>
            <li class="conclusion-option"><i class="fas fa-check"></i><span class="nav-position">Conclusion</span></li>
        </ul>
        <a href="#" class="button" style="background: #dbecfd; font-weight: bold; color: #4da1ff; box-shadow: none;">Generate PDF</a>
    </div>
    <div class="facebook-right">
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
            foreach ($facebook_blocks as $item) {
                if (show_block($edit_mode, $audit->{$item["type"]}) && !$item["is_icon"]) { 
                    if(round($audit->facebook_data->{$item["fb_name"]}) > round($audit->competitor->facebook_data->{$item["fb_name"]})) {
                        $your_procent = "100";
                        $competitor_procent = (string)round(normalize(round($audit->facebook_data->{$item["fb_name"]}), round($audit->competitor->facebook_data->{$item["fb_name"]})));
                    } else {
                        $competitor_procent = "100";
                        $your_procent = (string)round(normalize(round($audit->facebook_data->{$item["fb_name"]}), round($audit->competitor->facebook_data->{$item["fb_name"]})));
                    }

                    if($audit->has_comp) {
                        $max_value = ($audit->facebook_data->{$item["fb_name"]} 
                                      > $audit->competitor->facebook_data->{$item["fb_name"]}) 
                                      ? $audit->facebook_data->{$item["fb_name"]}
                                      : $audit->competitor->facebook_data->{$item["fb_name"]};
                    }
                    ?>
                    <div class="stat-box">
                        <span class="stat-title"><?php echo $language[$item["name"]]; ?></span>
                        <i class="fas fa-info-circle information"></i>
                        <div class="skills" data-percent="<?php echo $your_procent; ?>%">
                            <div class="title-bar">
                                <h5>You</h5>
                            </div>
                            <span class="procent font-blue">
                                <?php if($audit->has_comp) { ?>
                                     <?php echo round($audit->facebook_data->{$item["fb_name"]}); ?>
                                <?php } ?>
                            </span>
                            <div style="clear: both;"></div>
                            <div class="skillbar blue"></div>  
                        </div>
                        <?php if($audit->has_comp) { ?>
                            <div class="skills" data-percent="<?php echo $competitor_procent; ?>%">
                                    <div class="title-bar">
                                        <h5><?php echo $audit->competitor_name; ?></h5>
                                    </div>
                                    <span class="procent font-red"><?php echo round($audit->competitor->facebook_data->{$item["fb_name"]}); ?></span>
                                    <div style="clear: both;"></div>
                                    <div class="skillbar red"></div>  
                            </div>
                        <?php } ?>
                        <hr class="x-as" />
                        <span class="left-value">0</span>
                        <span class="center-value"><?php echo ceil(($max_value / 2)); ?></span>
                        <span class="right-value"><?php echo ceil($max_value); ?></span>
                    </div>
                <?php 
                }
            } ?>
        </div>
        <div class="small-statistics">
        <?php
            foreach ($facebook_blocks as $item) {
                if (show_block($edit_mode, $audit->{$item["type"]}) && $item["is_icon"]) { 
                    $your_val = ($audit->facebook_data->{$item["fb_name"]} == 1) 
                                ? '<i class="fas fa-check-circle check"></i>' 
                                : '<i class="fas fa-times-circle not-check"></i>';
                    
                    ?>
                    <div class="stat-box">
                        <span class="stat-title"><?php echo $language[$item["name"]]; ?></span>
                        <i class="fas fa-info-circle information"></i>
                        <div class="your-stat">
                            <span class="title-bar">You</span>
                            <?php echo $your_val; ?>
                        </div>
                        <?php if($audit->has_comp) { 
                            $comp_val = ($audit->competitor->facebook_data->{$item["fb_name"]} == 1) 
                                ? '<i class="fas fa-check-circle check"></i>' 
                                : '<i class="fas fa-times-circle not-check"></i>';
                        ?>
                            <div class="competitor-stat">
                                <span class="title-bar"><?php echo $audit->competitor_name; ?></span>
                                <?php echo $comp_val; ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php 
                }
            } ?>
        </div>
        <div class="facebook-advice advice">
            <span class="advice-title"><?php echo $language['facebook_advice']; ?></span>
            <div class="skills" data-percent="<?php echo $score['fb']; ?>%">
                <span class="procent font-red">
                    <?php
                        if (!$edit_mode) { 
                            echo $score['fb'] . "%";
                        } else {
                            ?><input type="text" class="score-input" value="<?php echo $score['fb']; ?>" name="facebook_score" id="facebook_score"/><?php
                        } 
                    ?>
                </span>
                <div style="margin-top: 12px;" class="skillbar red"></div>  
            </div>
            <p>
                <?php if ($edit_mode) { ?>
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
<section id="instagram-section">
    <div class="sidebar">
        <span class="title">Statistics</span>
        <ul>
            <li class="facebook-option" class="active"><i class="fab fa-facebook-square"></i><span class="nav-position">Facebook</span></li>
            <li class="instagram-option"><i class="fab fa-instagram"></i><span class="nav-position">Instagram</span></li>
            <li class="website-option"><i class="fas fa-globe"></i><span class="nav-position">Website</span></li>
            <li class="conclusion-option"><i class="fas fa-check"></i><span class="nav-position">Conclusion</span></li>
        </ul>
        <a href="#" class="button" style="background: #dbecfd; font-weight: bold; color: #4da1ff; box-shadow: none;">Generate PDF</a>
    </div>
    <div class="facebook-right">
        <i class="fab fa-instagram section-icon"></i>
        <span class="section-title">
            <?php if ($user->instagram_title == "") { ?>
                <?php echo $language['insta_title']; ?>:
            <?php } else {
                echo $user->instagram_title;
            } ?>
        </span>     
        <span class="section-subtitle">
        <?php if ($user->instagram_sub_title == "") { ?>
              <?php echo $language['insta_subtitle']; ?>
            <?php } else {
                echo $user->instagram_sub_title;
            } ?>
        </span> 

        <div style="width: 100%; height: auto; padding-right: 70px;">
            <div class="chart-holder">
                <span class="stat-title"><?php echo $language['likes_on_post']; ?></span>
                <i class="fas fa-info-circle information"></i>
                <div class="averages">
                    <span class="your_averages">You<span class="data font-blue">250.1</span></span>
                    <span class="competitor_averages">NOS<span class="data font-red">320.42</span></span>
                </div>
                <div style="height: 220px">
                     <canvas id="canvas" style="display: block; height: 100%;" class="chartjs-render-monitor"></canvas>
                </div>
            </div>
        </div>

        <div class="statistics">
            <div class="stat-box custom-height">
                <span class="stat-title"><?php echo $language['hastag_used']; ?></span>
                <i class="fas fa-info-circle information"></i>
                <h3 style="margin-top: -35px;">You</h3>
                <div class="skills" data-percent="100%">
                    <div class="title-bar-hashtags">
                        <h5>#<?php echo $audit->instagram_data->hashtags[0][0]; ?></h5>
                    </div>
                    <span class="procent font-blue procent-custom"><?php echo $audit->instagram_data->hashtags[1][0]; ?></span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div class="skills" data-percent="<?php echo normalize($audit->instagram_data->hashtags[1][0], $audit->instagram_data->hashtags[1][1]);?>%">
                    <div class="title-bar-hashtags">
                        <h5>#<?php echo $audit->instagram_data->hashtags[0][1]; ?></h5>
                    </div>
                    <span class="procent font-blue procent-custom"><?php echo $audit->instagram_data->hashtags[1][1]; ?></span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div class="skills" data-percent="<?php echo normalize($audit->instagram_data->hashtags[1][0], $audit->instagram_data->hashtags[1][2]);?>%">
                    <div class="title-bar-hashtags">
                        <h5>#<?php echo $audit->instagram_data->hashtags[0][2]; ?></h5>
                    </div>
                    <span class="procent font-blue procent-custom"><?php echo $audit->instagram_data->hashtags[1][2]; ?></span>
                    <div style="clear: both;"></div>
                    <div class="skillbar blue"></div>  
                </div>
                <div style="clear:both; margin-bottom: 20px;"></div>
                <h3>NOS</h3>
                <div class="skills" data-percent="100%">
                    <div class="title-bar-hashtags">
                        <h5>#<?php echo $audit->competitor->instagram_data->hashtags[0][0]; ?></h5>
                    </div>
                    <span class="procent font-red procent-custom"><?php echo $audit->competitor->instagram_data->hashtags[1][0]; ?></span>
                    <div style="clear: both;"></div>
                    <div class="skillbar red"></div>  
                </div>
                <div class="skills" data-percent="<?php echo normalize($audit->competitor->instagram_data->hashtags[1][0], $audit->competitor->instagram_data->hashtags[1][1]);?>%">
                    <div class="title-bar-hashtags">
                        <h5>#<?php echo $audit->competitor->instagram_data->hashtags[0][1]; ?></h5>
                    </div>
                    <span class="procent font-red procent-custom"><?php echo $audit->competitor->instagram_data->hashtags[1][1]; ?></span>
                    <div style="clear: both;"></div>
                    <div class="skillbar red"></div>  
                </div>
                <div class="skills" data-percent="<?php echo normalize($audit->competitor->instagram_data->hashtags[1][0], $audit->competitor->instagram_data->hashtags[1][2]);?>%">
                    <div class="title-bar-hashtags">
                        <h5>#<?php echo $audit->competitor->instagram_data->hashtags[0][2]; ?></h5>
                    </div>
                    <span class="procent font-red procent-custom"><?php echo $audit->competitor->instagram_data->hashtags[1][2]; ?></span>
                    <div style="clear: both;"></div>
                    <div class="skillbar red"></div>  
                </div>
                <hr class="x-as" style="margin-top:20px;" />
                <span class="left-value">0</span>
                <span class="center-value"><?php echo ceil(($max_value / 2)); ?></span>
                <span class="right-value"><?php echo ceil($max_value); ?></span>
            </div>

            <?php
            foreach ($instagram_blocks as $item) {
                if (show_block($edit_mode, $audit->{$item["type"]})) { 
                    if(round($audit->instagram_data->{$item["ig_name"]}) > round($audit->competitor->instagram_data->{$item["ig_name"]})) {
                        $your_procent = "100";
                        $competitor_procent = (string)round(normalize(round($audit->instagram_data->{$item["ig_name"]}), round($audit->competitor->instagram_data->{$item["ig_name"]})));
                    } else {
                        $competitor_procent = "100";
                        $your_procent = (string)round(normalize(round($audit->instagram_data->{$item["ig_name"]}), round($audit->competitor->instagram_data->{$item["ig_name"]})));
                    }

                    if($audit->has_comp) {
                        $max_value = ($audit->instagram_data->{$item["ig_name"]} 
                                      > $audit->competitor->instagram_data->{$item["ig_name"]}) 
                                      ? $audit->instagram_data->{$item["ig_name"]}
                                      : $audit->competitor->instagram_data->{$item["ig_name"]};
                    }
                    ?>
                    <div class="stat-box">
                        <span class="stat-title"><?php echo $language[$item["name"]]; ?></span>
                        <i class="fas fa-info-circle information"></i>
                        <div class="skills" data-percent="<?php echo $your_procent; ?>%">
                            <div class="title-bar">
                                <h5>You</h5>
                            </div>
                            <span class="procent font-blue">
                                <?php if($audit->has_comp) { ?>
                                     <?php echo round($audit->instagram_data->{$item["ig_name"]}); ?>
                                <?php } ?>
                            </span>
                            <div style="clear: both;"></div>
                            <div class="skillbar blue"></div>  
                        </div>
                        <?php if($audit->has_comp) { ?>
                            <div class="skills" data-percent="<?php echo $competitor_procent; ?>%">
                                    <div class="title-bar">
                                        <h5><?php echo $audit->competitor_name; ?></h5>
                                    </div>
                                    <span class="procent font-red"><?php echo round($audit->competitor->instagram_data->{$item["ig_name"]}); ?></span>
                                    <div style="clear: both;"></div>
                                    <div class="skillbar red"></div>  
                            </div>
                        <?php } ?>
                        <hr class="x-as" />
                        <span class="left-value">0</span>
                        <span class="center-value"><?php echo ceil(($max_value / 2)); ?></span>
                        <span class="right-value"><?php echo ceil($max_value); ?></span>
                    </div>
                <?php 
                }
            } ?>
        </div>
        <div class="instagram-advice advice">
            <span class="advice-title"><?php echo $language['instagram_advice']; ?></span>
            <div class="skills" data-percent="<?php echo $score['ig']; ?>%">
                <span class="procent font-red">
                <?php
                    if (!$edit_mode) { 
                        echo $score['ig'] . "%";
                    } else {
                        ?><input type="text" class="score-input" value="<?php echo $score['ig']; ?>" name="instagram_score" id="instagram_score"/><?php
                    } 
                ?>
                </span>
                <div style="margin-top: 12px;" class="skillbar red"></div>  
            </div>
            <p>
                <?php if ($edit_mode) { ?>
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
<section id="website-section">
    <div class="sidebar">
        <span class="title">Statistics</span>
        <ul>
            <li class="facebook-option active"><i class="fab fa-facebook-square"></i><span class="nav-position">Facebook</span></li>
            <li class="instagram-option"><i class="fab fa-instagram"></i><span class="nav-position">Instagram</span></li>
            <li class="website-option"><i class="fas fa-globe"></i><span class="nav-position">Website</span></li>
            <li class="conclusion-option"><i class="fas fa-check"></i><span class="nav-position">Conclusion</span></li>
        </ul>
        <a href="#" class="button" style="background: #dbecfd; font-weight: bold; color: #4da1ff; box-shadow: none;">Generate PDF</a>
    </div>
    <div class="facebook-right">
    <i class="fas fa-globe section-icon"></i>
        <span class="section-title">
            <?php if ($user->website_title == "") { ?>
                <?php echo $language['website_title']; ?>:
            <?php } else {
                echo $user->website_title;
            } ?>
        </span>     
        <span class="section-subtitle">
            <?php if ($user->website_sub_title == "") { ?>
              <?php echo $language['website_subtitle']; ?>
            <?php } else {
                echo $user->website_sub_title;
            } ?>
        </span>   

        <div class="statistics">
        <?php
            foreach ($website_blocks as $item) {
                if (show_block($edit_mode, $audit->{$item["type"]}) && !$item["is_icon"]) { 
                    if($item['name'] != "Mobile Friendly") {
                        $arr = explode("s", $audit->{$item['db_name']}, 2);
                        $first = $arr[0];
                        $your_string = $first . "s";
                        $your_value = (double) $first;

                        if($audit->has_comp) {
                            $arr = explode("s", $audit->competitor->{$item['db_name']}, 2);
                            $first = $arr[0];
                            $comp_string = $first . "s";
                            $comp_value = (int) $first;
                        }

                        if($audit->has_comp) {
                            $max_value = ($your_value  > $comp_value) ? $your_value : $comp_value;
                        }

                    } else {
                        $arr = explode("/", $audit->{$item['db_name']}, 2);
                        $first = $arr[0];
                        $your_string = $first;
                        $your_value = (double) $first;

                        if($audit->has_comp) {
                            $arr = explode("/", $audit->competitor->{$item['db_name']}, 2);
                            $first = $arr[0];
                            $comp_string = $first;
                            $comp_value = (int) $first;
                        }

                        if($audit->has_comp) {
                            $max_value = ($your_value  > $comp_value) ? $your_value : $comp_value;
                        }
                    }

                    if(round($your_value) > round($comp_value)) {
                        $your_procent = "100";
                        $competitor_procent = (string)normalize($your_value, $comp_value);
                    } else {
                        $competitor_procent = "100";
                        $your_procent = (string)normalize($your_value, $comp_value);
                    }


                    ?>

                    <div class="stat-box">
                        <span class="stat-title"><?php echo $language[$item["name"]]; ?></span>
                        <i class="fas fa-info-circle information"></i>
                        <div class="skills" data-percent="<?php echo $your_procent; ?>%">
                            <div class="title-bar">
                                <h5>You</h5>
                            </div>
                            <span class="procent font-blue">
                                <?php if($audit->has_comp) { ?>
                                     <?php echo $your_string; ?>
                                <?php } ?>
                            </span>
                            <div style="clear: both;"></div>
                            <div class="skillbar blue"></div>  
                        </div>
                        <?php if($audit->has_comp) { ?>
                            <div class="skills" data-percent="<?php echo $competitor_procent; ?>%">
                                    <div class="title-bar">
                                        <h5><?php echo $audit->competitor_name; ?></h5>
                                    </div>
                                    <span class="procent font-red"><?php echo $comp_string; ?></span>
                                    <div style="clear: both;"></div>
                                    <div class="skillbar red"></div>  
                            </div>
                        <?php } ?>
                        <hr class="x-as" />
                        <span class="left-value">0</span>
                        <span class="center-value"><?php echo ceil(($max_value / 2)); ?></span>
                        <span class="right-value"><?php echo $max_value; ?></span>
                    </div>
                <?php 
                }
            } ?>
        </div>
        <div class="small-statistics">
        <?php
            foreach ($website_blocks as $item) {
                if (show_block($edit_mode, $audit->{$item["type"]}) && $item["is_icon"]) { 
                    $your_val = ($audit->{$item["db_name"]} == 1) 
                                ? '<i class="fas fa-check-circle check"></i>' 
                                : '<i class="fas fa-times-circle not-check"></i>';
                    ?>
                    <div class="stat-box">
                        <span class="stat-title"><?php echo $language[$item["name"]]; ?></span>
                        <i class="fas fa-info-circle information"></i>
                        <div class="your-stat">
                            <span class="title-bar">You</span>
                            <?php echo $your_val; ?>
                        </div>
                        <?php if($audit->has_comp) { 
                            $comp_val = ($audit->competitor->{$item["db_name"]} == 1) 
                                ? '<i class="fas fa-check-circle check"></i>' 
                                : '<i class="fas fa-times-circle not-check"></i>';
                        ?>
                            <div class="competitor-stat">
                                <span class="title-bar"><?php echo $audit->competitor_name; ?></span>
                                <?php echo $comp_val; ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php 
                }
            } ?>
        </div>
        <div class="facebook-advice advice">
            <span class="advice-title"><?php echo $language['website_advice']; ?></span>
            <div class="skills" data-percent="<?php echo $score['wb']; ?>%">
                <span class="procent font-red">
                <?php
                    if (!$edit_mode) { 
                        echo $score['wb'] . "%";
                    } else {
                        ?><input type="text" class="score-input" value="<?php echo $score['wb']; ?>" name="wesbite_score" id="website_score"/><?php
                    } 
                ?>
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
<section id="conclusion-section">
    <div class="sidebar">
        <span class="title">Statistics</span>
        <ul>
            <li class="facebook-option active"><i class="fab fa-facebook-square"></i><span class="nav-position">Facebook</span></li>
            <li class="instagram-option"><i class="fab fa-instagram"></i><span class="nav-position">Instagram</span></li>
            <li class="website-option"><i class="fas fa-globe"></i><span class="nav-position">Website</span></li>
            <li class="conclusion-option"><i class="fas fa-check"></i><span class="nav-position">Conclusion</span></li>
        </ul>
        <a href="#" class="button" style="background: #dbecfd; font-weight: bold; color: #4da1ff; box-shadow: none;">Generate PDF</a>
    </div>
    <div class="facebook-right">
        <div class="left">
            <span class="section-title"><?php echo $language['conclusion']; ?></span>      
            <?php 
                if ($audit->conclusion_vis_bit == 1 || $edit_mode) {
                    if ($edit_mode) { ?>
                    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>#conclusion" method="post" enctype="multipart/form-data">
                        <textarea maxlength="999" input="text"  name="conclusion" id="conclusion"><?php if ($audit->conclusion == NULL) { echo $user->conclusion_audit; } else { echo $audit->conclusion; } ?></textarea>
                    </form>

                    <div class="description-tags">
                        You can insert the following tags in all the text fields: <span style="font-size: 10px; color: #000;">#{client}, #{competitor}, #{fb_score}, #{insta_score}, #{website_score}</span>
                    </div>
                    <?php
                    } else {  ?>
                        <p style='font-size: 14px; font-weight: 100; line-height: 24px;'>
                            <?php if ($audit->conclusion == NULL) { echo "<pre>" . change_tags($user->conclusion_audit, $client, $audit) . "</pre>"; } else { echo "<pre>" . change_tags($audit->conclusion, $client, $audit) . "</pre>"; } ?></p><?php
                        get_contact_info($phone, $mail, $calendar_link, $language, $user); 
                        }
                }
            ?>
        </div>
        <div class="right">
            <div class="facebook-advice advice">
                <span class="advice-title"><?php echo $language['facebook_advice']; ?></span>
                <div class="skills" data-percent="<?php echo $score['fb']; ?>%">
                    <span class="procent font-red"><?php echo $score['fb']; ?>%</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar red"></div>  
                </div>
                <span class="advice-title"><?php echo $language['instagram_advice']; ?></span>
                <div class="skills" data-percent="<?php echo $score['ig']; ?>%">
                    <span class="procent font-red"><?php echo $score['ig']; ?>%</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar red"></div>  
                </div>
                <span class="advice-title"><?php echo $language['website_advice']; ?></span>
                <div class="skills" data-percent="<?php echo $score['wb']; ?>%">
                    <span class="procent font-red"><?php echo $score['wb']; ?>%</span>
                    <div style="clear: both;"></div>
                    <div class="skillbar red"></div>  
                </div>
            </div>
        </div>
    </div>
</section>
</body>
<script>
$(document).ready(function(){
    
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

    function startAnimation(){
        jQuery('.skills').each(function(){

            jQuery(this).find('.skillbar').animate({
                width:jQuery(this).attr('data-percent')
            },1000); 
            
        });
    }                
});

var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
var config = {
    type: 'line',
    data: {
        labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
        datasets: [{
            pointHighlightFill: "#000",
             pointHighlightStroke: "rgba(75, 192, 192, 0.2)",
            borderWidth: 8,
            pointRadius: 0,
            label: 'My First dataset',
            backgroundColor: "#e36364",
            borderColor: "#e36364",
            data: [
                10,
                40,
                20,
                70,
                60,
                70,
                40
            ],
            fill: false,
        }, {
            borderWidth: 8,
            pointRadius: 0,
            label: 'My Second dataset',
            fill: false,
            backgroundColor: "#4da1ff",
            borderColor: "#4da1ff",
            data: [
                30,
                10,
                40,
                50,
                40,
                20,
                10
            ],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        title: {
            display: false,
            
        },
        legend: {
            display: false
        },
        tooltips: {
            mode: 'index',
            intersect: false,
            bevelWidth: 3,
            bevelHighlightColor: 'rgba(255, 255, 255, 0.75)',
            bevelShadowColor: 'rgba(0, 0, 0, 0.5)'
        },
        hover: {
            mode: 'nearest',
            intersect: true
        },
        scales: {
            xAxes: [{
                ticks: {
                  fontColor: "#b7b7b7", // this here
                },
                display: true,
                gridLines: {
                    color: "rgba(0, 0, 0, 0)",
                },
                scaleLabel: {
                    display: true,
                    labelString: ''
                }
            }],
            yAxes: [{
                gridLines: { color: "#b7b7b7" }, 

                ticks: {
                  maxTicksLimit: 4,
                  fontColor: "#b7b7b7"
                },
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: ''
                }
            }]
        }
    }
};


$.getScript("https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js", function () { window.onload = function() {
    var ctx = document.getElementById('canvas').getContext('2d');
    window.myLine = new Chart(ctx, config);
    }    
});



</script>
</html>
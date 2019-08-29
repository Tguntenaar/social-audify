<?php
/**
 * Template Name: process profile changes
 */
?>
<?php

  error_reporting(E_ALL);
  ini_set("display_errors", 1);

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // clean var_dump
    // echo '<pre>' . var_export($_POST, true) . '</pre>';
    $user_id = get_current_user_id();
    include(dirname(__FILE__)."/../../services/connection.php");
    include(dirname(__FILE__)."/../../controllers/user_controller.php");
    include(dirname(__FILE__)."/../../models/user.php");

    $connection = new connection;

    $user_control = new user_controller($connection);
    $user = $user_control->get($user_id);

    $audit_visibility_stat = $user->get_visibility('audit');
    $report_visibility_stat = $user->get_visibility('report');

    function check_length($text) {
      if (strlen($text) > 999) {
        return 0;
      }
      return 1;
    }

    function check_interval($number) {
      if (0 <= absint($number) && absint($number) <= 365) {
        return 1;
      }
      return 0;
    }

    function if_set_update_int($user, $post_field, $db_field, $table) {
      if (isset($_POST[$post_field]) && check_interval($_POST[$post_field])) {
        $user->update($table, $db_field, absint($_POST[$post_field]));
      }
    }

    function if_set_update_textfield($user, $post_field, $db_field, $table) {
      if (isset($_POST[$post_field]) && check_length(trim($_POST[$post_field]))) {
        $user->update($table, $db_field, sanitize_textarea_field(trim($_POST[$post_field])));
      }
    }

    /**
     * Facebook
     */
    if_set_update_textfield($user, 'introduction-audit', 'intro_audit', 'Configtext');
    if_set_update_textfield($user, 'conclusion-audit', 'conclusion_audit', 'Configtext');

    if_set_update_textfield($user, 'introduction-report', 'intro_report', 'Configtext');
    if_set_update_textfield($user, 'conclusion-report', 'conclusion_report', 'Configtext');


    if_set_update_int($user, 'range_facebook_1', 'range_number_fb_1', 'Configtext');
    if_set_update_int($user, 'range_facebook_2', 'range_number_fb_2', 'Configtext');

    if_set_update_textfield($user, 'facebook-audit_1', 'text_fb_1', 'Configtext');
    if_set_update_textfield($user, 'facebook-audit_2', 'text_fb_2', 'Configtext');
    if_set_update_textfield($user, 'facebook-audit_3', 'text_fb_3', 'Configtext');

    /**
     * Instagram
     */
    if_set_update_int($user, 'range_insta_1', 'range_number_insta_1', 'Configtext');
    if_set_update_int($user, 'range_insta_2', 'range_number_insta_2', 'Configtext');

    if_set_update_textfield($user, 'insta-audit_1', 'text_insta_1', 'Configtext');
    if_set_update_textfield($user, 'insta-audit_2', 'text_insta_2', 'Configtext');
    if_set_update_textfield($user, 'insta-audit_3', 'text_insta_3', 'Configtext');

    /**
     * Website
     */
    if_set_update_int($user, 'range_website_1', 'range_number_website_1', 'Configtext');
    if_set_update_int($user, 'range_website_2', 'range_number_website_2', 'Configtext');

    if_set_update_textfield($user, 'website-audit_1', 'text_website_1', 'Configtext');
    if_set_update_textfield($user, 'website-audit_2', 'text_website_2', 'Configtext');
    if_set_update_textfield($user, 'website-audit_3', 'text_website_3', 'Configtext');

    /**
     * Mail
     */
    if_set_update_int($user, 'day_1', 'day_1', 'Mail_config');
    if_set_update_int($user, 'day_2', 'day_2', 'Mail_config');
    if_set_update_int($user, 'day_3', 'day_3', 'Mail_config');

    if_set_update_textfield($user, 'mail_text', 'mail_text', 'Mail_config');
    if_set_update_textfield($user, 'second_mail_text', 'second_mail_text', 'Mail_config');
    if_set_update_textfield($user, 'third_mail_text', 'third_mail_text', 'Mail_config');


    $audit_visibility_fields = array(
      'fb_likes',
      'fb_pem',
      'fb_apl',
      'fb_ads',
      'fb_dpp',
      'fb_dph',
      'fb_ntv',
      'fb_tab',
      'fb_loc',
      'fb_cp',
      'insta_ae',
      'insta_nof',
      'insta_nopf',
      'insta_hashtag',
      'insta_lpd',
      'insta_nplm',
      'website_ga',
      'website_googletag',
      'website_pixel',
      'website_ws',
      'website_mf',
      'website_lt');

    $report_visibility_fields = array(
      'cam_imp',
      'cam_cpc',
      'cam_cpm',
      'cam_cpp',
      'cam_ctr',
      'cam_frq',
      'cam_spd',
      'soc_pl',
      'soc_aml',
      'soc_inf',
      'soc_inaf',
      'soc_iae',
      'soc_plm',
      'graph_imp',
      'graph_cpc',
      'graph_cpm',
      'graph_cpp',
      'graph_ctr',
      'graph_frq',
      'graph_spd');


    foreach ($audit_visibility_fields as $field) {
      if (isset($_POST["check-${field}"])) {
        if ((int)$_POST["check-${field}"] xor (int)$audit_visibility_stat[0]->{$field}) {
          $user->toggle_visibility($field, 'audit');
        }
      }
    }

    foreach ($report_visibility_fields as $field) {
      if (isset($_POST["check-${field}"])) {
        if ((int)$_POST["check-${field}"] xor (int)$report_visibility_stat[0]->{$field}) {
          $user->toggle_visibility($field, 'report');
        }
      }
    }

    // clean var_dump
    // echo '<pre>' . var_export($report_visibility_stat[0], true) . '</pre>';


    if (true) {
      if (isset($_GET['settings'])) {
        header("Location: https://".getenv('HTTP_HOST')."/profile-page/#".$_GET['settings']."-settings", true, 303);
      } else {
        header("Location: https://".getenv('HTTP_HOST')."/profile-page", true, 303);
      }
      exit();
    }
  } else {
    header("HTTP/1.0 404 Not Found", true, 404);
    include(dirname(__FILE__)."/../../../404.php");
  }
  exit();
?>

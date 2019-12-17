<?php
/**
 * leadengine functions file
 *
 * @package leadengine
 * by KeyDesign
 */

  add_action('after_setup_theme', 'remove_admin_bar');

  function remove_admin_bar() {
    if (!current_user_can('administrator') && !is_admin()) {
      show_admin_bar(false);
    }
  }

  function modify_search_filter($query) {
    if ($query->is_search && ! is_admin() ) {
      $query->set('post_type', 'post');
    }
    return $query;
  }
  
  add_filter('pre_get_posts','modify_search_filter');


  function custom_login_logo() {
    echo '<style type="text/css">
        #wp-submit {background-color:#6e9d9a !important;border-color: #6e9d9a !important; text-shadow: 0px 0px 0px #6e9d9a !important;}
        h1 a { background-image:url('.get_bloginfo('template_directory').'/core/assets/images/logo_socialaudify.png) !important; }
    </style>';
  }

  add_action('login_head', 'custom_login_logo'); 

  function not_logged_in() {
    wp_send_json_error($errormsg = array('message'=>'you are not logged in.'));
    wp_die();
  }

  add_action( 'wp_ajax_log_error', 'log_js_error');
  add_action( 'wp_ajax_nopriv_log_error', 'not_logged_in');

  function log_js_error() {
    require_once(dirname(__FILE__)."/dashboard/controllers/log_controller.php");
    $errorLogger = new Logger;

    $message = isset($_POST['message']) ? $_POST['message'] : "";
    $stacktrace = isset($_POST['stacktrace']) ? $_POST['stacktrace'] : "";
    $stacktrace .= isset($_POST['function']) ? "in {$_POST['function']}" : "";

    $errorLogger->printJs(get_current_user_id(), $message, $stacktrace);
    wp_die();
  }


  add_action( 'wp_ajax_toggle_visibility', 'toggle_visibility');
  add_action( 'wp_ajax_nopriv_toggle_visibility', 'not_logged_in');

  function toggle_visibility() {
      require_once(dirname(__FILE__)."/dashboard/services/connection.php");
      $connection = new connection;

      $field = $_POST['field'];
      $type = $_POST['type'];

      if ($type === 'audit') {
        require_once(dirname(__FILE__)."/dashboard/services/audit_service.php");

        $audit_service = new audit_service($connection);
        $audit_id = $_POST['audit'];
        $audit_service->toggle_config_visibility($audit_id, $field);
      } elseif ($type === 'report') {
        require_once(dirname(__FILE__)."/dashboard/services/report_service.php");

        $report_service = new report_service($connection);
        $report_id = $_POST['report'];
        $report_service->toggle_config_visibility($report_id, $field);
      } elseif ($type === 'user') {
        require_once(dirname(__FILE__)."/dashboard/services/user_service.php");

        $user_service = new user_service($connection);
        $user_id = $_POST['user'];
        $type_table = $_POST['type_table'];

        $testje = $user_service->toggle_config_visibility($user_id, $field, $type_table);
      }

      // wp_send_json(array('test'=>$testje));

      wp_send_json(array('TABLE'=>$type_table, 'USER'=>$user_id, 'FIELD'=>$field));

      wp_die();
  }

  add_action( 'wp_ajax_import_clients', 'add_multiple_clients');
  add_action( 'wp_ajax_nopriv_import_clients', 'not_logged_in');

  function add_multiple_clients() {
    require_once(dirname(__FILE__)."/dashboard/assets/php/global_regex.php");
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/client_controller.php");
    $Regex = new Regex;
    $connection = new connection;
    $client_controller = new client_controller($connection);

    $clients = $_POST['clients'];

    $parsedClients = array();
    foreach ($clients as $c) {
      if ($c['name'] != "" && $c['email'] != "" && $Regex->valid_fb($c["facebook"]) &&
        $Regex->valid_ig($c["instagram"]) && $Regex->valid_wb($c["website"])) {

        array_push($parsedClients, array(
          "name" => $c["name"], 
          "fb" => $c["facebook"], 
          "ig" => $c["instagram"], 
          "wb" => $c["website"], 
          "mail" => sanitize_email( $c["email"] )
        ));
        $client_controller->create($c["name"], $c["facebook"], $c["instagram"], $c["website"], sanitize_email( $c["email"] ));
      }
    }

    // $test = $client_controller->create_multiple(get_current_user_id(), $parsedClients);
    // , "test"=>$test
    // TODO meegeven als parsedClients count < dan clients count...
    wp_send_json(array("Succes"=>"added: ".count($parsedClients) ));
    wp_die();
  }

  add_action( 'wp_ajax_update_ads_audit', 'edit_ads_audit');
  add_action( 'wp_ajax_nopriv_update_ads_audit', 'not_logged_in');

  function edit_ads_audit() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
    require_once(dirname(__FILE__)."/dashboard/models/audit.php");

    $connection = new connection;
    $audit_control = new audit_controller($connection);

    $audit_id = $_POST['audit'];
    $running = ($_POST['ads'] == "yes") ? 1 : 0;
    $competitor = ($_POST['competitor'] == 'true') ? 1 : 0;

    $audit = $audit_control->get($audit_id);
    if ($competitor) {
      $audit->competitor->facebook_data->runningAdds = $running;
      $fb_data = $audit->competitor->facebook_data;
    } else {
      $audit->facebook_data->runningAdds = $running;
      $fb_data = $audit->facebook_data;
    }
    
    $audit_control->update($audit_id, "facebook_data", json_encode($fb_data), "Audit_data",  $competitor);

    wp_send_json(array('audit'=>$audit, "runningAds"=>$fb_data));
    wp_die();
  }



  add_action( 'wp_ajax_update_meta_audit', 'create_audit');
  add_action( 'wp_ajax_nopriv_update_meta_audit', 'not_logged_in');

  function create_audit() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
    require_once(dirname(__FILE__)."/dashboard/models/audit.php");

    $safe_audit = sanitize_audit();
    list($client, $options, $page, $competitor) = validate_audit($safe_audit);

    $connection = new connection;
    $audit_control = new audit_controller($connection);

    $slug = $audit_control->create($page, $client, $options, $competitor);

    wp_send_json(array('slug'=>$slug));
    wp_die();
  }

  function sanitize_audit() {
    $client     = json_decode(stripslashes($_POST['client']), true);
    $options    = json_decode(stripslashes($_POST['options']), true);
    $page       = json_decode(stripslashes($_POST['page_info']), true);
    $competitor = json_decode(stripslashes($_POST['competitor']), true);

    return array($client, $options, $page, $competitor);
  }

  /**
   * discard any unwanted text
   */
  function validate_audit($safe_audit) {
    list($client, $options, $page, $competitor) = $safe_audit;

    if (strlen($page['name']) > 25) {
      $page['name'] = substr($page['name'], 0, 5);
    }

    return array($client, $options, $page, $competitor);
  }


  add_action( 'wp_ajax_crawl_data_check', 'crawl_data_check');
  add_action( 'wp_ajax_nopriv_crawl_data_check', 'not_logged_in');

  // Check if crawl has completed
  function crawl_data_check() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");

    $connection = new connection;
    $audit_control = new audit_controller($connection);

    $audit_id = $_POST['audit'];
    $has_competitor = $_POST['comp'];

    $has_website = $audit_control->check_website($audit_id, $has_competitor);

    wp_send_json($has_website);
    wp_die();
  }


  add_action( 'wp_ajax_update_config', 'update_page_configuration');
  add_action( 'wp_ajax_nopriv_update_config', 'not_logged_in');

  function update_page_configuration() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    $connection = new connection;
    $type = $_POST['type'];
    $page_id = $_POST[$type];

    if ($type == 'audit') {
      require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
      require_once(dirname(__FILE__)."/dashboard/models/audit.php");
      $control = new audit_controller($connection);
    } else if ($type == 'report') {
      require_once(dirname(__FILE__)."/dashboard/controllers/report_controller.php");
      require_once(dirname(__FILE__)."/dashboard/models/report.php");
      $control = new report_controller($connection);
    } else if ($type == 'user') {
      require_once(dirname(__FILE__)."/dashboard/controllers/user_controller.php");
      require_once(dirname(__FILE__)."/dashboard/models/user.php");
      $control = new user_controller($connection);
    }

    $page = $control->get($page_id);

    if($type == 'audit') {
        $table = 'Audit_template';
        $page->update('color', sanitize_hex_color($_POST['color']), $table);
        $page->update('language', sanitize_text_field($_POST['language']), $table);
    } else if($type == 'report') {
        $table = 'Report_content';
        $page->update('color', sanitize_hex_color($_POST['color']), $table);
    } else {
        $table = 'Configtext';

        if($_POST['flag'] == 'report') {
            $control->update($_POST['user_id'], 'color_report', sanitize_hex_color($_POST['color']), $table);
        } else {
            $control->update($_POST['user_id'], 'color_audit', sanitize_hex_color($_POST['color']), $table);
            $control->update($_POST['user_id'], 'language', sanitize_text_field($_POST['language']), $table);            
        }
    }


    if ($type == 'audit') {
       $page->update('mail_bit', (($_POST['value'] == 'true') ? 1 : 0));
    } else if($type == 'user') {
       $control->update($_POST['user_id'], 'std_mail_bit', $_POST['value'] == 'true', $table);
    }

    wp_send_json(array('color' => $_POST['language']));
    wp_die();
  }


  add_action( 'wp_ajax_update_iba_id', 'assign_iba_id');
  add_action( 'wp_ajax_nopriv_update_iba_id', 'not_logged_in');

  function assign_iba_id() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/user_controller.php");
    require_once(dirname(__FILE__)."/dashboard/models/user.php");

    $connection = new connection;
    $user_control = new user_controller($connection);
    $user = $user_control->get(get_current_user_id());

    $iba_id = $_POST['iba_id'];
    // TODO: thomas
    // $iba_name = $_POST['iba_name'];

    $value = $user->update('User', 'instagram_business_account_id', $iba_id);
    // $status = $user->update_list('User', array('instagram_business_account_id'=>$iba_id,'instagram_business_name'=>$iba_name));

    wp_send_json(array('instagram_business_account updated succes if 0'=>$value));
    wp_die();
  }


  add_action( 'wp_ajax_update_ad_account', 'update_client_ad_account');
  add_action( 'wp_ajax_nopriv_update_ad_adaccount', 'not_logged_in');

  function update_client_ad_account() {
    $ad_id = isset($_POST['ad_id']) ? $_POST['ad_id'] : 0;
    $client_id = isset($_POST['client_id']) ? $_POST['client_id'] : 0;

    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/client_controller.php");
    require_once(dirname(__FILE__)."/dashboard/models/client.php");

    $connection = new connection;
    $client_control = new client_controller($connection);
    $client = $client_control->get($client_id);
    $client->update('ad_id', $ad_id);

    wp_send_json(array('client_id'=>$client_id));
    wp_die();
  }

  function check_manual_instagram_postfields($control, $id, $competitor, $type = 'audit') {
    $str = $competitor == 1 ? "comp-" : "";

    if (isset($_POST["{$str}followers_count"])) {
      $instagram_data = array(
        "avgEngagement"=> floatval($_POST["{$str}avgEngagement"]),
        "followers_count"=> absint($_POST["{$str}followers_count"]),
        "postsLM"=> absint($_POST["{$str}postsLM"]),
        "follows_count"=> absint($_POST["{$str}follows_count"]),
        "averageComments"=> floatval($_POST["{$str}averageComments"]),
        "averageLikes"=> floatval($_POST["{$str}averageLikes"]),
      );

      $control->update($id, "instagram_data", json_encode($instagram_data), "Audit_data", $competitor);
    }
  }

  add_action( 'wp_ajax_universal_update', 'update_all');
  add_action( 'wp_ajax_nopriv_universal_update', 'not_logged_in');

  function update_all() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    $connection = new connection;

    $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $type = $_POST['type'];
    $id = $_POST[$type];

    if ($type == 'audit') {
      require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
      $control = new audit_controller($connection);
      $fields = $control->get_area_fields();
      $table = 'Audit_template';

      check_manual_instagram_postfields($control, $id, 0);
      check_manual_instagram_postfields($control, $id, 1);

      if(!isset($_POST['iframe-input'])) {
          $_POST['iframe-input'] = "";
      }
    } else if ( $type == 'report' ) {
      require_once(dirname(__FILE__)."/dashboard/controllers/report_controller.php");
      $control = new report_controller($connection);
      $fields = $control->get_area_fields();
      $table = 'Report_content';
    } else if ( $type == 'user' ) {
      require_once(dirname(__FILE__)."/dashboard/controllers/user_controller.php");
      $control = new user_controller($connection);
      $fields = $control->get_area_fields();
      $table = 'Configtext';

      if(!isset($_POST['std_iframe'])) {
          $_POST['std_iframe'] = "";
      }

    }

    if ($type == 'audit' || $type == 'report' || $type == 'user') {
      foreach( $fields as $field ) {
        if (isset($_POST[$field])) {
          $test = $control->update($id, $field, sanitize_textarea_field(stripslashes($_POST[$field])), $table);
        }
      }
    }

    wp_send_json(array('succes'=>'1'));
    wp_die();
  }

  add_action( 'wp_ajax_update_meta_report', 'create_report');
  add_action( 'wp_ajax_nopriv_update_meta_report', 'not_logged_in');


  function create_report() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/report_controller.php");
    require_once(dirname(__FILE__)."/dashboard/models/report.php");

    $connection = new connection;
    $report_control = new report_controller($connection);

    $client     = json_decode(stripslashes($_POST['client']), true);
    $page_info  = json_decode(stripslashes($_POST['page_info']), true);
    $options    = json_decode(stripslashes($_POST['options']), true);
    $competitor = json_decode(stripslashes($_POST['competitor']), true);
    $currency   = json_decode(stripslashes($_POST['currency']), true);

    $page_name = $page_info['name'];
    $manual = (isset($page_info['manual'])) ? $page_info['manual'] : 0;
    $slug = $report_control->create($page_name, $client, $options, $competitor, $manual, $currency);

    wp_send_json(array('slug'=>$slug));
    wp_die();
  }


  add_action( 'wp_ajax_delete_page', 'delete_page');
  add_action( 'wp_ajax_nopriv_delete_page', 'not_logged_in');

  function delete_page() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    $connection = new connection;

    $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    switch ($_POST['type']) {
      case 'audit':
        require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
        require_once(dirname(__FILE__)."/dashboard/models/audit.php");
        $control = new audit_controller($connection);
        break;

      case 'report':
        require_once(dirname(__FILE__)."/dashboard/controllers/report_controller.php");
        require_once(dirname(__FILE__)."/dashboard/models/report.php");
        $control = new report_controller($connection);
        break;

      default:
        $error = new WP_Error( '101', 'type error', 'unknown type' );
        wp_send_json_error($error);
        wp_die();
    }

    require_once(dirname(__FILE__)."/dashboard/controllers/client_controller.php");
    require_once(dirname(__FILE__)."/dashboard/models/client.php");
    $client_control = new client_controller($connection);

    $page_id = $_POST[$_POST['type']];
    $page = $control->get($page_id);
    $client = $client_control->get($page->client_id);

    if (get_current_user_id() == $client->user_id) {
      $page->delete();
    }

    wp_send_json(array('deleted'=>"everyting"));
    wp_die();
  }


  add_action( 'wp_ajax_delete_multiple', 'delete_multiple');
  add_action( 'wp_ajax_nopriv_delete_multiple', 'not_logged_in');

  function delete_multiple() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    $connection = new connection;

    $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    switch ($_POST['type']) {
      case 'audit':
        require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
        $control = new audit_controller($connection);
        break;

      case 'report':
        require_once(dirname(__FILE__)."/dashboard/controllers/report_controller.php");
        $control = new report_controller($connection);
        break;

      case 'client':
        require_once(dirname(__FILE__)."/dashboard/controllers/client_controller.php");
        $control = new client_controller($connection);
        break;

      default:
        $error = new WP_Error( '101', 'type error', 'unknown type' );
        wp_send_json_error($error);
        wp_die();
    }

    $user_id = get_current_user_id();
    $results = [];

    if (isset($_POST['posts'])) {
      $deletions = 0;
      foreach ($_POST['posts'] as $post_id) {
        if (get_post_field('post_author', $post_id) == $user_id) {
          wp_delete_post($post_id);
          $deletions++;
        }
      }
      $results["posts-deleted"] = $deletions;
    }

    if (isset($_POST['ids'])) {
      $results["audits-deleted"] =  $control->delete_multiple($user_id, $_POST['ids']);
    }

    wp_send_json($results);
    wp_die();
  }

  add_action( 'wp_ajax_insert_view', 'insert_view');
  add_action( 'wp_ajax_nopriv_insert_view', 'not_logged_in');

  function insert_view() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/report_controller.php");
    
    $connect = new connection;
    $audit_controller = new audit_controller($connect);
    $report_controller = new report_controller($connect);

    $type = $_POST['type'];
    $id = $_POST[$type];

    if ($type == 'report') {
       $report_controller->update($id, 'view_time', date('Y-m-d'), 'Report', NULL);
    } else if ($type == 'audit') {
       $audit_controller->update($id, 'view_time', date('Y-m-d'), 'Audit', NULL);
    } 

     wp_die();
  }

  add_action( 'wp_ajax_export_viewed', 'export_viewed');
  add_action( 'wp_ajax_nopriv_export_viewed', 'not_logged_in');

  function export_viewed() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/services/audit_service.php");
    require_once(dirname(__FILE__)."/dashboard/services/report_service.php");
    
    // require_once(dirname(__FILE__)."/dashboard/controllers/report_controller.php");
    
    $connect = new connection;
    $audit_service = new audit_service($connect);
    $report_service = new report_service($connect);

    // $report_controller = new report_controller($connect);

    $type = $_POST['type'];
    $id = (int) $_POST['user_id'];

    $return_array = array();

    if($type == 'audit') {
        $audits = $audit_service->get_all($id, date('Y-m-1', strtotime("-2 month")));
        
        $later = new DateTime(date('Y-m-d H:i:s'));


        array_push($return_array, array("Audit name", "Client name", "Client email", "Client Facebook", "Client Instagram", "Client Website", "Days viewed"));
        foreach($audits as $audit) {
            if($audit->view_time != NULL) {
              $earlier = new DateTime($audit->view_time);
              $day_difference = $later->diff($earlier)->format("%a");

              array_push($return_array, array($audit->name, $audit->client_name, $audit->client_mail,
                                              $audit->client_facebook, $audit->client_instagram, $audit->client_website, $day_difference . " days"));
            }
        }
    } else if($type = 'report') {
        $reports = $report_service->get_all($id, date('Y-m-1', strtotime("-2 month")));
        
        array_push($return_array, array("Report name", "Report email", "Client email", "Client Facebook", "Client Instagram", "Client Website", "Days viewed"));
        foreach($reports as $report) {
            if($report->view_time != NULL) {
              $earlier = new DateTime($report->view_time);
              $day_difference = $later->diff($earlier)->format("%a");
              array_push($return_array, array($report->name, $report->client_name, $report->client_mail, 
                                              $report->client_facebook, $report->client_instagram, $report->client_website, $day_difference));
            }
        }
    }

    wp_send_json(json_encode($return_array));
    wp_die();
  }


  require_once(get_template_directory() . '/core/init.php');
  function get_country() {
    $countries = array(
      "AF" => array("country" => "Afghanistan", "continent" => "Asia"),
      "AX" => array("country" => "Åland Islands", "continent" => "Europe"),
      "AL" => array("country" => "Albania", "continent" => "Europe"),
      "DZ" => array("country" => "Algeria", "continent" => "Africa"),
      "AS" => array("country" => "American Samoa", "continent" => "Oceania"),
      "AD" => array("country" => "Andorra", "continent" => "Europe"),
      "AO" => array("country" => "Angola", "continent" => "Africa"),
      "AI" => array("country" => "Anguilla", "continent" => "North America"),
      "AQ" => array("country" => "Antarctica", "continent" => "Antarctica"),
      "AG" => array("country" => "Antigua and Barbuda", "continent" => "North America"),
      "AR" => array("country" => "Argentina", "continent" => "South America"),
      "AM" => array("country" => "Armenia", "continent" => "Asia"),
      "AW" => array("country" => "Aruba", "continent" => "North America"),
      "AU" => array("country" => "Australia", "continent" => "Oceania"),
      "AT" => array("country" => "Austria", "continent" => "Europe"),
      "AZ" => array("country" => "Azerbaijan", "continent" => "Asia"),
      "BS" => array("country" => "Bahamas", "continent" => "North America"),
      "BH" => array("country" => "Bahrain", "continent" => "Asia"),
      "BD" => array("country" => "Bangladesh", "continent" => "Asia"),
      "BB" => array("country" => "Barbados", "continent" => "North America"),
      "BY" => array("country" => "Belarus", "continent" => "Europe"),
      "BE" => array("country" => "Belgium", "continent" => "Europe"),
      "BZ" => array("country" => "Belize", "continent" => "North America"),
      "BJ" => array("country" => "Benin", "continent" => "Africa"),
      "BM" => array("country" => "Bermuda", "continent" => "North America"),
      "BT" => array("country" => "Bhutan", "continent" => "Asia"),
      "BO" => array("country" => "Bolivia", "continent" => "South America"),
      "BA" => array("country" => "Bosnia and Herzegovina", "continent" => "Europe"),
      "BW" => array("country" => "Botswana", "continent" => "Africa"),
      "BV" => array("country" => "Bouvet Island", "continent" => "Antarctica"),
      "BR" => array("country" => "Brazil", "continent" => "South America"),
      "IO" => array("country" => "British Indian Ocean Territory", "continent" => "Asia"),
      "BN" => array("country" => "Brunei Darussalam", "continent" => "Asia"),
      "BG" => array("country" => "Bulgaria", "continent" => "Europe"),
      "BF" => array("country" => "Burkina Faso", "continent" => "Africa"),
      "BI" => array("country" => "Burundi", "continent" => "Africa"),
      "KH" => array("country" => "Cambodia", "continent" => "Asia"),
      "CM" => array("country" => "Cameroon", "continent" => "Africa"),
      "CA" => array("country" => "Canada", "continent" => "North America"),
      "CV" => array("country" => "Cape Verde", "continent" => "Africa"),
      "KY" => array("country" => "Cayman Islands", "continent" => "North America"),
      "CF" => array("country" => "Central African Republic", "continent" => "Africa"),
      "TD" => array("country" => "Chad", "continent" => "Africa"),
      "CL" => array("country" => "Chile", "continent" => "South America"),
      "CN" => array("country" => "China", "continent" => "Asia"),
      "CX" => array("country" => "Christmas Island", "continent" => "Asia"),
      "CC" => array("country" => "Cocos (Keeling) Islands", "continent" => "Asia"),
      "CO" => array("country" => "Colombia", "continent" => "South America"),
      "KM" => array("country" => "Comoros", "continent" => "Africa"),
      "CG" => array("country" => "Congo", "continent" => "Africa"),
      "CD" => array("country" => "The Democratic Republic of The Congo", "continent" => "Africa"),
      "CK" => array("country" => "Cook Islands", "continent" => "Oceania"),
      "CR" => array("country" => "Costa Rica", "continent" => "North America"),
      "CI" => array("country" => "Cote D'ivoire", "continent" => "Africa"),
      "HR" => array("country" => "Croatia", "continent" => "Europe"),
      "CU" => array("country" => "Cuba", "continent" => "North America"),
      "CY" => array("country" => "Cyprus", "continent" => "Asia"),
      "CZ" => array("country" => "Czech Republic", "continent" => "Europe"),
      "DK" => array("country" => "Denmark", "continent" => "Europe"),
      "DJ" => array("country" => "Djibouti", "continent" => "Africa"),
      "DM" => array("country" => "Dominica", "continent" => "North America"),
      "DO" => array("country" => "Dominican Republic", "continent" => "North America"),
      "EC" => array("country" => "Ecuador", "continent" => "South America"),
      "EG" => array("country" => "Egypt", "continent" => "Africa"),
      "SV" => array("country" => "El Salvador", "continent" => "North America"),
      "GQ" => array("country" => "Equatorial Guinea", "continent" => "Africa"),
      "ER" => array("country" => "Eritrea", "continent" => "Africa"),
      "EE" => array("country" => "Estonia", "continent" => "Europe"),
      "ET" => array("country" => "Ethiopia", "continent" => "Africa"),
      "FK" => array("country" => "Falkland Islands (Malvinas)", "continent" => "South America"),
      "FO" => array("country" => "Faroe Islands", "continent" => "Europe"),
      "FJ" => array("country" => "Fiji", "continent" => "Oceania"),
      "FI" => array("country" => "Finland", "continent" => "Europe"),
      "FR" => array("country" => "France", "continent" => "Europe"),
      "GF" => array("country" => "French Guiana", "continent" => "South America"),
      "PF" => array("country" => "French Polynesia", "continent" => "Oceania"),
      "TF" => array("country" => "French Southern Territories", "continent" => "Antarctica"),
      "GA" => array("country" => "Gabon", "continent" => "Africa"),
      "GM" => array("country" => "Gambia", "continent" => "Africa"),
      "GE" => array("country" => "Georgia", "continent" => "Asia"),
      "DE" => array("country" => "Germany", "continent" => "Europe"),
      "GH" => array("country" => "Ghana", "continent" => "Africa"),
      "GI" => array("country" => "Gibraltar", "continent" => "Europe"),
      "GR" => array("country" => "Greece", "continent" => "Europe"),
      "GL" => array("country" => "Greenland", "continent" => "North America"),
      "GD" => array("country" => "Grenada", "continent" => "North America"),
      "GP" => array("country" => "Guadeloupe", "continent" => "North America"),
      "GU" => array("country" => "Guam", "continent" => "Oceania"),
      "GT" => array("country" => "Guatemala", "continent" => "North America"),
      "GG" => array("country" => "Guernsey", "continent" => "Europe"),
      "GN" => array("country" => "Guinea", "continent" => "Africa"),
      "GW" => array("country" => "Guinea-bissau", "continent" => "Africa"),
      "GY" => array("country" => "Guyana", "continent" => "South America"),
      "HT" => array("country" => "Haiti", "continent" => "North America"),
      "HM" => array("country" => "Heard Island and Mcdonald Islands", "continent" => "Antarctica"),
      "VA" => array("country" => "Holy See (Vatican City State)", "continent" => "Europe"),
      "HN" => array("country" => "Honduras", "continent" => "North America"),
      "HK" => array("country" => "Hong Kong", "continent" => "Asia"),
      "HU" => array("country" => "Hungary", "continent" => "Europe"),
      "IS" => array("country" => "Iceland", "continent" => "Europe"),
      "IN" => array("country" => "India", "continent" => "Asia"),
      "ID" => array("country" => "Indonesia", "continent" => "Asia"),
      "IR" => array("country" => "Iran", "continent" => "Asia"),
      "IQ" => array("country" => "Iraq", "continent" => "Asia"),
      "IE" => array("country" => "Ireland", "continent" => "Europe"),
      "IM" => array("country" => "Isle of Man", "continent" => "Europe"),
      "IL" => array("country" => "Israel", "continent" => "Asia"),
      "IT" => array("country" => "Italy", "continent" => "Europe"),
      "JM" => array("country" => "Jamaica", "continent" => "North America"),
      "JP" => array("country" => "Japan", "continent" => "Asia"),
      "JE" => array("country" => "Jersey", "continent" => "Europe"),
      "JO" => array("country" => "Jordan", "continent" => "Asia"),
      "KZ" => array("country" => "Kazakhstan", "continent" => "Asia"),
      "KE" => array("country" => "Kenya", "continent" => "Africa"),
      "KI" => array("country" => "Kiribati", "continent" => "Oceania"),
      "KP" => array("country" => "Democratic People's Republic of Korea", "continent" => "Asia"),
      "KR" => array("country" => "Republic of Korea", "continent" => "Asia"),
      "KW" => array("country" => "Kuwait", "continent" => "Asia"),
      "KG" => array("country" => "Kyrgyzstan", "continent" => "Asia"),
      "LA" => array("country" => "Lao People's Democratic Republic", "continent" => "Asia"),
      "LV" => array("country" => "Latvia", "continent" => "Europe"),
      "LB" => array("country" => "Lebanon", "continent" => "Asia"),
      "LS" => array("country" => "Lesotho", "continent" => "Africa"),
      "LR" => array("country" => "Liberia", "continent" => "Africa"),
      "LY" => array("country" => "Libya", "continent" => "Africa"),
      "LI" => array("country" => "Liechtenstein", "continent" => "Europe"),
      "LT" => array("country" => "Lithuania", "continent" => "Europe"),
      "LU" => array("country" => "Luxembourg", "continent" => "Europe"),
      "MO" => array("country" => "Macao", "continent" => "Asia"),
      "MK" => array("country" => "Macedonia", "continent" => "Europe"),
      "MG" => array("country" => "Madagascar", "continent" => "Africa"),
      "MW" => array("country" => "Malawi", "continent" => "Africa"),
      "MY" => array("country" => "Malaysia", "continent" => "Asia"),
      "MV" => array("country" => "Maldives", "continent" => "Asia"),
      "ML" => array("country" => "Mali", "continent" => "Africa"),
      "MT" => array("country" => "Malta", "continent" => "Europe"),
      "MH" => array("country" => "Marshall Islands", "continent" => "Oceania"),
      "MQ" => array("country" => "Martinique", "continent" => "North America"),
      "MR" => array("country" => "Mauritania", "continent" => "Africa"),
      "MU" => array("country" => "Mauritius", "continent" => "Africa"),
      "YT" => array("country" => "Mayotte", "continent" => "Africa"),
      "MX" => array("country" => "Mexico", "continent" => "North America"),
      "FM" => array("country" => "Micronesia", "continent" => "Oceania"),
      "MD" => array("country" => "Moldova", "continent" => "Europe"),
      "MC" => array("country" => "Monaco", "continent" => "Europe"),
      "MN" => array("country" => "Mongolia", "continent" => "Asia"),
      "ME" => array("country" => "Montenegro", "continent" => "Europe"),
      "MS" => array("country" => "Montserrat", "continent" => "North America"),
      "MA" => array("country" => "Morocco", "continent" => "Africa"),
      "MZ" => array("country" => "Mozambique", "continent" => "Africa"),
      "MM" => array("country" => "Myanmar", "continent" => "Asia"),
      "NA" => array("country" => "Namibia", "continent" => "Africa"),
      "NR" => array("country" => "Nauru", "continent" => "Oceania"),
      "NP" => array("country" => "Nepal", "continent" => "Asia"),
      "NL" => array("country" => "Netherlands", "continent" => "Europe"),
      "AN" => array("country" => "Netherlands Antilles", "continent" => "North America"),
      "NC" => array("country" => "New Caledonia", "continent" => "Oceania"),
      "NZ" => array("country" => "New Zealand", "continent" => "Oceania"),
      "NI" => array("country" => "Nicaragua", "continent" => "North America"),
      "NE" => array("country" => "Niger", "continent" => "Africa"),
      "NG" => array("country" => "Nigeria", "continent" => "Africa"),
      "NU" => array("country" => "Niue", "continent" => "Oceania"),
      "NF" => array("country" => "Norfolk Island", "continent" => "Oceania"),
      "MP" => array("country" => "Northern Mariana Islands", "continent" => "Oceania"),
      "NO" => array("country" => "Norway", "continent" => "Europe"),
      "OM" => array("country" => "Oman", "continent" => "Asia"),
      "PK" => array("country" => "Pakistan", "continent" => "Asia"),
      "PW" => array("country" => "Palau", "continent" => "Oceania"),
      "PS" => array("country" => "Palestinia", "continent" => "Asia"),
      "PA" => array("country" => "Panama", "continent" => "North America"),
      "PG" => array("country" => "Papua New Guinea", "continent" => "Oceania"),
      "PY" => array("country" => "Paraguay", "continent" => "South America"),
      "PE" => array("country" => "Peru", "continent" => "South America"),
      "PH" => array("country" => "Philippines", "continent" => "Asia"),
      "PN" => array("country" => "Pitcairn", "continent" => "Oceania"),
      "PL" => array("country" => "Poland", "continent" => "Europe"),
      "PT" => array("country" => "Portugal", "continent" => "Europe"),
      "PR" => array("country" => "Puerto Rico", "continent" => "North America"),
      "QA" => array("country" => "Qatar", "continent" => "Asia"),
      "RE" => array("country" => "Reunion", "continent" => "Africa"),
      "RO" => array("country" => "Romania", "continent" => "Europe"),
      "RU" => array("country" => "Russian Federation", "continent" => "Europe"),
      "RW" => array("country" => "Rwanda", "continent" => "Africa"),
      "SH" => array("country" => "Saint Helena", "continent" => "Africa"),
      "KN" => array("country" => "Saint Kitts and Nevis", "continent" => "North America"),
      "LC" => array("country" => "Saint Lucia", "continent" => "North America"),
      "PM" => array("country" => "Saint Pierre and Miquelon", "continent" => "North America"),
      "VC" => array("country" => "Saint Vincent and The Grenadines", "continent" => "North America"),
      "WS" => array("country" => "Samoa", "continent" => "Oceania"),
      "SM" => array("country" => "San Marino", "continent" => "Europe"),
      "ST" => array("country" => "Sao Tome and Principe", "continent" => "Africa"),
      "SA" => array("country" => "Saudi Arabia", "continent" => "Asia"),
      "SN" => array("country" => "Senegal", "continent" => "Africa"),
      "RS" => array("country" => "Serbia", "continent" => "Europe"),
      "SC" => array("country" => "Seychelles", "continent" => "Africa"),
      "SL" => array("country" => "Sierra Leone", "continent" => "Africa"),
      "SG" => array("country" => "Singapore", "continent" => "Asia"),
      "SK" => array("country" => "Slovakia", "continent" => "Europe"),
      "SI" => array("country" => "Slovenia", "continent" => "Europe"),
      "SB" => array("country" => "Solomon Islands", "continent" => "Oceania"),
      "SO" => array("country" => "Somalia", "continent" => "Africa"),
      "ZA" => array("country" => "South Africa", "continent" => "Africa"),
      "GS" => array("country" => "South Georgia and The South Sandwich Islands", "continent" => "Antarctica"),
      "ES" => array("country" => "Spain", "continent" => "Europe"),
      "LK" => array("country" => "Sri Lanka", "continent" => "Asia"),
      "SD" => array("country" => "Sudan", "continent" => "Africa"),
      "SR" => array("country" => "Suriname", "continent" => "South America"),
      "SJ" => array("country" => "Svalbard and Jan Mayen", "continent" => "Europe"),
      "SZ" => array("country" => "Swaziland", "continent" => "Africa"),
      "SE" => array("country" => "Sweden", "continent" => "Europe"),
      "CH" => array("country" => "Switzerland", "continent" => "Europe"),
      "SY" => array("country" => "Syrian Arab Republic", "continent" => "Asia"),
      "TW" => array("country" => "Taiwan, Province of China", "continent" => "Asia"),
      "TJ" => array("country" => "Tajikistan", "continent" => "Asia"),
      "TZ" => array("country" => "Tanzania, United Republic of", "continent" => "Africa"),
      "TH" => array("country" => "Thailand", "continent" => "Asia"),
      "TL" => array("country" => "Timor-leste", "continent" => "Asia"),
      "TG" => array("country" => "Togo", "continent" => "Africa"),
      "TK" => array("country" => "Tokelau", "continent" => "Oceania"),
      "TO" => array("country" => "Tonga", "continent" => "Oceania"),
      "TT" => array("country" => "Trinidad and Tobago", "continent" => "North America"),
      "TN" => array("country" => "Tunisia", "continent" => "Africa"),
      "TR" => array("country" => "Turkey", "continent" => "Asia"),
      "TM" => array("country" => "Turkmenistan", "continent" => "Asia"),
      "TC" => array("country" => "Turks and Caicos Islands", "continent" => "North America"),
      "TV" => array("country" => "Tuvalu", "continent" => "Oceania"),
      "UG" => array("country" => "Uganda", "continent" => "Africa"),
      "UA" => array("country" => "Ukraine", "continent" => "Europe"),
      "AE" => array("country" => "United Arab Emirates", "continent" => "Asia"),
      "GB" => array("country" => "United Kingdom", "continent" => "Europe"),
      "US" => array("country" => "United States", "continent" => "North America"),
      "UM" => array("country" => "United States Minor Outlying Islands", "continent" => "Oceania"),
      "UY" => array("country" => "Uruguay", "continent" => "South America"),
      "UZ" => array("country" => "Uzbekistan", "continent" => "Asia"),
      "VU" => array("country" => "Vanuatu", "continent" => "Oceania"),
      "VE" => array("country" => "Venezuela", "continent" => "South America"),
      "VN" => array("country" => "Viet Nam", "continent" => "Asia"),
      "VG" => array("country" => "Virgin Islands, British", "continent" => "North America"),
      "VI" => array("country" => "Virgin Islands, U.S.", "continent" => "North America"),
      "WF" => array("country" => "Wallis and Futuna", "continent" => "Oceania"),
      "EH" => array("country" => "Western Sahara", "continent" => "Africa"),
      "YE" => array("country" => "Yemen", "continent" => "Asia"),
      "ZM" => array("country" => "Zambia", "continent" => "Africa"),
      "ZW" => array("country" => "Zimbabwe", "continent" => "Africa")
    );
    return $countries;
  }

  function get_eu_countries() {
    $eu_countries = array(
      "AT" => "Austria",
      "BE" => "Belgium",
      "BG" => "Bulgaria",
      "CY" => "Cyprus",
      "CZ" => "Czech Republic",
      "DK" => "Denmark",
      "EE" => "Estonia",
      "FI" => "Finland",
      "FR" => "France",
      "DE" => "Germany",
      "GR" => "Greece",
      "HU" => "Hungary",
      "IE" => "Ireland",
      "IT" => "Italy",
      "LV" => "Latvia",
      "LT" => "Lithuania",
      "LU" => "Luxembourg",
      "MT" => "Malta",
      "NL" => "Netherlands",
      "PL" => "Poland",
      "PT" => "Portugal",
      "RO" => "Romania",
      "SK" => "Slovakia (Slovak Republic)",
      "SI" => "Slovenia",
      "ES" => "Spain",
      "SE" => "Sweden",
      "GB" => "United Kingdom",
      "RD" => "Local IP" // added for testing purposes
    );
    return $eu_countries;
  }

  add_action( 'rcp_after_password_registration_field', 'pw_rcp_add_user_fields' );
  add_action( 'rcp_profile_editor_after', 'pw_rcp_add_user_fields' );
  /**
   * Adds the custom fields to the registration form and profile editor
   */
  function pw_rcp_add_user_fields() {
    // $selected_country = isset( $_POST['rcp_country'] ) ? $_POST['rcp_country'] : '';
    $id = get_current_user_id();
    $number = get_user_meta($id, 'rcp_number', true );
    $btw_number = get_user_meta($id, 'rcp_btw_number', true );
    $calendar = get_user_meta($id, 'rcp_calendar', true );
    $selected_country = get_user_meta($id, 'rcp_country', true );
    $company = get_user_meta($id, 'rcp_company', true );

    ?>
    <?php if(!(get_post_field( 'post_name', get_post() ) == "register")) {?>
        <p>
          <label for="rcp_number"><?php _e( 'Your phone number', 'rcp' ); ?></label>
          <input name="rcp_number" id="rcp_number" type="text" value="<?php echo esc_attr( $number ); ?>"/>
        </p>
    <?php } ?>
    <p class="rcp_calendar_custom" style="">
      <?php if(!(get_post_field( 'post_name', get_post() ) == "register")) {?>
          <label for="rcp_calendar"><?php _e( 'Your calendar link', 'rcp' ); ?></label>
          <input name="rcp_calendar" id="rcp_calendar" type="url" placeholder="https://" value="<?php echo esc_attr( $calendar ); ?>"/>
      <?php } ?>
    </p>

    <p class="rcp_company_custom" style="">
        <?php if(!(get_post_field( 'post_name', get_post() ) == "register")) {?>
            <label for="rcp_company"><?php _e( 'Your company name', 'rcp' ); ?></label>
            <input name="rcp_company" id="rcp_company"  placeholder="Name will be shown on the audit/report page and e-mails." type="text" value="<?php echo esc_attr( $company ); ?>"/>
        <?php } ?>
    </p>

    <p id="rcp_country_text" style="width: 47%; margin-top: 50px; float:left;">
          <label for="rcp_country"><?php _e( 'Country', 'rcp' ); ?></label>
          <select style="width: 100%; margin-top: -33px; height:55px; display:block;float:left;" name="rcp_country" id="rcp_country">
              <?php foreach ( get_country() as $key => $value ) { ?>
                  <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $selected_country, $key ); ?>><?php echo esc_attr($value['country']); ?></option>
              <?php } ?>
          </select>
      </p>

      <p class="rcp_btw_number_custom" style="margin-top: 50px; width: 47%; margin-left: 6%; float:left;">
          <label for="rcp_btw_number"><?php _e( 'Your VAT number (optional)', 'rcp' ); ?></label>
          <span class="btw_title_1" style="margin-top: -35px;color: grey; font-size: 12px; display: block;">By adding your VAT-number we will not have to charge VAT, resulting in a lower price.</span>
          <span class="btw_title_2" style="margin-bottom: 45px; color: grey; font-size: 12px; display: block;">*For Dutch citizens: You can request the VAT back when you do your btw-aangifte</span>
          <input  name="rcp_btw_number" placeholder="Example: NL0000.00.000.B.00" id="rcp_btw_number" type="text" value="<?php echo openssl_decrypt(esc_attr( $btw_number ), "AES-128-ECB", "ASDJFLB@JB#@#KB@#$@@#%)$()"); ?>"/>
      </p>
    </p>
    <?php
  }


  add_action( 'rcp_edit_member_after', 'pw_rcp_add_member_edit_fields' );

  /**
   * Adds the custom fields to the member edit screen
   */
  function pw_rcp_add_member_edit_fields($user_id = 0) {
    $number = get_user_meta( $user_id, 'rcp_number', true );
    $btw_number = get_user_meta( $user_id, 'rcp_btw_number', true );
    $company = get_user_meta( $user_id, 'rcp_company', true );
    $country = get_user_meta( $user_id, 'rcp_country', true );
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'WS-SERVICE-KEY';
    $secret_iv = 'WS-SERVICE-VALUE';
    // hash
    $key = hash('sha256', $secret_key);
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    ?>
    <tr valign="top">
      <th scope="row" valign="top">
        <label for="rcp_number"><?php _e( 'Number', 'rcp' ); ?></label>
      </th>
      <td>
        <input name="rcp_number" id="rcp_number" type="text" value="<?php echo esc_attr( $number ); ?>"/>
        <p class="description"><?php _e( 'The member\'s phone number', 'rcp' ); ?></p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row" valign="top">
        <label for="rcp_calendar"><?php _e( 'Calendar', 'rcp' ); ?></label>
      </th>
      <td>
        <input name="rcp_calendar" id="rcp_calendar" type="url" value="<?php echo esc_attr( $number ); ?>"/>
        <p class="description"><?php _e( 'The member\'s Calander link.', 'rcp' ); ?></p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row" valign="top">
        <label for="rcp_btw_number"><?php _e( 'VAT number', 'rcp' ); ?></label>
      </th>
      <td>
        <input name="rcp_btw_number" id="rcp_btw_number" type="text" value="<?php echo openssl_decrypt(esc_attr( $btw_number ), "AES-128-ECB", "ASDJFLB@JB#@#KB@#$@@#%)$()"); ?>"/>
        <p class="description"><?php _e( 'The member\'s VAT number', 'rcp' ); ?></p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row" valign="top">
        <label for="rcp_company"><?php _e( 'Company name', 'rcp' ); ?></label>
      </th>
      <td>
        <input name="rcp_company" id="rcp_company" type="text" value="<?php echo esc_attr( $company ); ?>"/>
        <p class="description"><?php _e( 'The member\'s company name', 'rcp' ); ?></p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row" valign="top">
        <label for="rcp_country"><?php _e( 'Country', 'rcp' ); ?></label>
      </th>
      <td>
        <input name="rcp_country" id="rcp_country" type="text" value="<?php echo esc_attr( $country ); ?>"/>
        <p class="description"><?php _e( 'The member\'s Country', 'rcp' ); ?></p>
      </td>
    </tr>
    <?php
  }


  add_action( 'rcp_form_errors', 'pw_rcp_validate_user_fields_on_register', 10 );
  /**
   * Determines if there are problems with the registration data submitted
   */
  function pw_rcp_validate_user_fields_on_register( $posted ) {
    if (is_user_logged_in()) {
      return;
    }

    if (empty( $posted['rcp_country'] )) {
        rcp_errors()->add( 'empty_country', __( 'Please select your country', 'rcp' ), 'register' );
    }

    if ((!empty( $posted['rcp_btw_number']) && array_key_exists($posted['rcp_country'], get_eu_countries()) && $posted['rcp_country'] != "NL")) {


      $vat_number = empty($posted['rcp_btw_number']) ? "" : $posted['rcp_btw_number'];
      $vat_number = str_replace(array(' ', '.', '-', ',', ', '), '', trim($vat_number));

      $contents = @file_get_contents('https://controleerbtwnummer.eu/api/validate/'.$vat_number.'.json');


        if($contents === false) {
            // throw new Exception('service unavailable');
            rcp_errors()->add( 'empty_country', __( "VAT validation not possible at the moment, contact us: contact@socialaudify.com.", 'rcp' ), 'register' );

        }
        else {
            $res = json_decode($contents);

            if(!$res->valid) {
                rcp_errors()->add( 'invalid_vat', __( 'Wrong VAT number.', 'rcp' ), 'register' );
            }
        }
     }

     if (empty( $posted['rcp_country'] )) {

     }
     if(empty( $posted['rcp_btw_number'])) {

     }
  }


  add_action( 'rcp_form_processing', 'pw_rcp_save_user_fields_on_register', 10, 2 );

  /**
   * Stores the information submitted during registration
   */
  function pw_rcp_save_user_fields_on_register( $posted, $user_id ) {
    $output = false;
    $output = openssl_encrypt($posted['rcp_btw_number'], "AES-128-ECB", "ASDJFLB@JB#@#KB@#$@@#%)$()");


    if( ! empty( $posted['rcp_number'] ) ) {
      update_user_meta( $user_id, 'rcp_number', sanitize_text_field( $posted['rcp_number'] ) );
    }

    if( ! empty( $posted['rcp_calendar'] ) ) {
      update_user_meta( $user_id, 'rcp_calendar', sanitize_url( $posted['rcp_calendar'] ) );
    }

    if( ! empty( $posted['rcp_btw_number'] ) ) {
      update_user_meta( $user_id, 'rcp_btw_number', sanitize_text_field($output));
    }

    if( ! empty( $posted['rcp_country'] ) ) {
      update_user_meta( $user_id, 'rcp_country', sanitize_text_field( $posted['rcp_country'] ));
    }
  }

  /**
     * Stores the information submitted profile update
     *
     */
    function pw_rcp_save_user_fields_on_profile_save( $user_id ) {
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'WS-SERVICE-KEY';
        $secret_iv = 'WS-SERVICE-VALUE';
        // hash
        $key = hash('sha256', $secret_key);
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);


    	if( ! empty( $_POST['rcp_number'] ) ) {
    		update_user_meta( $user_id, 'rcp_number', sanitize_text_field( $_POST['rcp_number'] ) );
    	}

        if( ! empty( $_POST['rcp_calendar'] ) ) {
            update_user_meta( $user_id, 'rcp_calendar', sanitize_text_field( $_POST['rcp_calendar'] ) );
        }

        if( ! empty( $_POST['rcp_btw_number'] ) ) {
            $vat_number = isset($_POST['rcp_btw_number']) ? $_POST['rcp_btw_number'] : "";
            $vat_number = str_replace(array(' ', '.', '-', ',', ', '), '', trim($vat_number));

            $contents = @file_get_contents('https://controleerbtwnummer.eu/api/validate/'.$vat_number.'.json');

            if($contents === false) {
                throw new Exception('service unavailable');
            }
            else {
                $res = json_decode($contents);

                if($res->valid) {
                    $output = openssl_encrypt($vat_number,"AES-128-ECB", "ASDJFLB@JB#@#KB@#$@@#%)$()");
                    update_user_meta( $user_id, 'rcp_btw_number', $output);
                }
            }
        } else {
            update_user_meta( $user_id, 'rcp_btw_number', "");
        }

        if( ! empty( $_POST['rcp_country'] ) ) {
          update_user_meta( $user_id, 'rcp_country', sanitize_text_field( $_POST['rcp_country'] ));
        }

        update_user_meta( $user_id, 'rcp_company', sanitize_text_field( $_POST['rcp_company'] ) );
    }
    add_action( 'rcp_user_profile_updated', 'pw_rcp_save_user_fields_on_profile_save', 10 );
    add_action( 'rcp_edit_member', 'pw_rcp_save_user_fields_on_profile_save', 10 );



  // -------------------------------------
  // Edit below this line
  // -------------------------------------

  add_action( 'wp_login_failed', 'my_front_end_login_fail' );  // hook failed login

  function my_front_end_login_fail( $username ) {
    $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
    // if there's a valid referrer, and it's not the default log-in screen
    if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
      wp_redirect( $referrer . '?login=failed' );  // let's append some information (login=failed) to the URL for the theme to use
      exit;
    }
  }

  /**
   * This will remove the username requirement on the registration form
   * and use the email address as the username.
   */
  function jp_rcp_user_registration_data( $user ) {
    rcp_errors()->remove( 'username_empty' );
    $user['login'] = $user['email'];
    return $user;
  }

  add_filter( 'rcp_user_registration_data', 'jp_rcp_user_registration_data' );
?>

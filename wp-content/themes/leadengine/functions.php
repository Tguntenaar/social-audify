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
    }
    elseif ($type === 'report') {
      require_once(dirname(__FILE__)."/dashboard/services/report_service.php");
      $report_service = new report_service($connection);
      $report_id = $_POST['report'];

      $report_service->toggle_config_visibility($report_id, $field);
    }
    wp_send_json(['success' => 'toggled']);
    wp_die();
  }


  add_action( 'wp_ajax_update_ads_audit', 'edit_ads_audit');
  add_action( 'wp_ajax_nopriv_update_ads_audit', 'not_logged_in');

  function edit_ads_audit() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/services/audit_service.php");

    $connection = new connection;
    $audit_service = new audit_service($connection);

    $audit_id = $_POST['audit'];
    $data = $_POST['ads'];
    $competitor = ($_POST['competitor'] == 'false') ? 0 : 1;

    $audit_data = $audit_service->get($audit_id);
    $audit_data_facebook = json_decode($audit_data[0]->facebook_data);
    $audit_data_facebook->runningAdds = ($data == 'yes') ? 1 : 0;

    $audit_service->update($audit_id, "Audit_data", "facebook_data", json_encode($audit_data_facebook), $competitor);

    wp_send_json(array('audit_data'=>$audit_data_facebook, 'competitor'=>$competitor, 'data'=>$data));
    wp_die();
  }


  add_action( 'wp_ajax_update_meta_audit', 'create_audit');
  add_action( 'wp_ajax_nopriv_update_meta_audit', 'not_logged_in');

  /**
   * FIXME: usercontroller client controller user and client?
   */
  function create_audit() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/user_controller.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/client_controller.php");

    require_once(dirname(__FILE__)."/dashboard/models/audit.php");
    require_once(dirname(__FILE__)."/dashboard/models/client.php");
    require_once(dirname(__FILE__)."/dashboard/models/user.php");

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

    /**
     * TODO:
     * htmlspecialchars()
     * striptags()
     * mysqli_real_escape_string()
     */

    return array($client, $options, $page, $competitor);
  }

  /**
   * discard any unwanted text
   */
  function validate_audit($safe_audit) {
    list($client, $options, $page, $competitor) = $safe_audit;
    /**
     * TODO:
     * check of id's wel kloppen met een whitelist
     * get the users clients check if the client id een van zijn clients is
     */

    if (strlen($page['name']) > 25) {
      $page['name'] = substr($page['name'], 0, 5);
    }

    // if ($competitor != 'false') {
    //   require_once(dirname(__FILE__)."/dashboard/assests/php/parse_functions.php");
    //   $competitor['facebook'] = get_fb_name($competitor['facebook']);
    //   $competitor['instagram'] = get_insta_name($competitor['instagram']);
    // }

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
    $array = $audit_control->check_website($audit_id);

    wp_send_json($array);
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
    }

    $page = $control->get($page_id);
    $table = ($type == 'audit') ? 'Audit_template' : 'Report_content';
    $page->update('color', sanitize_hex_color($_POST['color']), $table);

    if ($type == 'audit') {
      $page->update('mail_bit', $_POST['value'] == 'true');
    }

    wp_send_json(array('color' => $_POST['color']));
    wp_die();
  }


  add_action( 'wp_ajax_update_iba_id', 'assign_iba_id');
  add_action( 'wp_ajax_nopriv_update_iba_id', 'not_logged_in');

  // TODO: vang af
  function assign_iba_id() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/user_controller.php");
    require_once(dirname(__FILE__)."/dashboard/models/user.php");

    $connection = new connection;
    $user_control = new user_controller($connection);
    $user = $user_control->get(get_current_user_id());

    $value = $user->update('User', 'instagram_business_account_id', $_POST['iba_id']);

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
    } else if ( $type == 'report' ) {
      require_once(dirname(__FILE__)."/dashboard/controllers/report_controller.php");
      $control = new report_controller($connection);
      $fields = $control->get_area_fields();
      $table = 'Report_content';
    }

    if ($type == 'audit' || $type == 'report') {
      foreach( $fields as $field ) {
        if (isset($_POST[$field])) {
          $control->update($id, $field, sanitize_textarea_field(stripslashes($_POST[$field])), $table);
        }
      }
    }

    wp_send_json(array('succes'=>'1'));
    wp_die();
  }

  add_action( 'wp_ajax_update_meta_report', 'create_report');
  add_action( 'wp_ajax_nopriv_update_meta_report', 'not_logged_in');

  // TODO: word aangepast nadat de marketing API goed werkt.
  /**
   * $fb_option = $options['facebook_checkbox'];
   * $ig_option = $options['instagram_checkbox'];
   * $wb_option = $options['website_checkbox'];
   */
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

    $type = $_POST['type'];

    if ($type == 'audit') {
      require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
      require_once(dirname(__FILE__)."/dashboard/models/audit.php");
      $control = new audit_controller($connection);
    } elseif ($type == 'report') {
      require_once(dirname(__FILE__)."/dashboard/controllers/report_controller.php");
      require_once(dirname(__FILE__)."/dashboard/models/report.php");
      $control = new report_controller($connection);
    } else {
      $error = new WP_Error( '101', 'type error', 'unknown type' );
      wp_send_json_error($error);
      wp_die();
    }

    require_once(dirname(__FILE__)."/dashboard/controllers/client_controller.php");
    require_once(dirname(__FILE__)."/dashboard/models/client.php");
    $client_control = new client_controller($connection);

    $page_id = $_POST[$type];
    $page = $control->get($page_id);
    $client = $client_control->get($page->client_id);

    if (get_current_user_id() == $client->user_id) {
      $page->delete();
    }

    wp_send_json(array('deleted'=>"everyting"));
    wp_die();
  }


  add_action( 'wp_ajax_delete_client', 'remove_client');
  add_action( 'wp_ajax_nopriv_delete_client', 'not_logged_in');

  function remove_client() {
    include(dirname(__FILE__)."/dashboard/services/connection.php");
    include(dirname(__FILE__)."/dashboard/controllers/client_controller.php");
    include(dirname(__FILE__)."/dashboard/models/client.php");

    $connection = new connection;
    $client_control = new client_controller($connection);
    $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    $client_id = $_POST['client'];
    $client = $client_control->get($client_id);

    if (get_current_user_id() == $client->user_id) {
      $client->delete();
    }

    wp_send_json(array('id'=>$client->id));
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
    $id = get_current_user_id();
    $number = get_user_meta($id, 'rcp_number', true );
    $btw_number = get_user_meta($id, 'rcp_btw_number', true );
    $calendar = get_user_meta($id, 'rcp_calendar', true );
    $country_val = get_user_meta($id, 'rcp_country', true );
    $selected_country = isset($country_val) ? $country_val : '';

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'ABk FA sjdanjk lallLL';
    $secret_iv = 'SAAnkks ksj sknalSAFF';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    ?>
    <p>
      <label for="rcp_number"><?php _e( 'Your phone number', 'rcp' ); ?></label>
      <input name="rcp_number" id="rcp_number" type="text" value="<?php echo esc_attr( $number ); ?>"/>
    </p>
    <!-- <p>
      TODO:
      <label for="rcp_number"><?php //_e( 'Your scheduler', 'rcp' ); ?></label>
      <input name="rcp_number" id="rcp_number" type="url" value="<?php // echo esc_attr( $number ); ?>"/>
    </p> -->
    <p class="calander_p" style="margin-top: -90px;">
      <?php if(!(get_post_field( 'post_name', get_post() ) == "register")) {?>
          <label for="rcp_calendar"><?php _e( 'Your calendar link', 'rcp' ); ?></label>
          <input name="rcp_calendar" id="rcp_calendar" type="url" value="<?php echo esc_attr( $calendar ); ?>"/>
      <?php } ?>

      <p id="rcp_country" style="width: 47%; margin-top: 50px; float:left;">
          <label class="country_p" for="rcp_country"><?php _e( 'Country', 'rcp' ); ?></label>
          <select style="width: 100%; margin-top: -33px; height:55px; display:block;float:left;" name="rcp_country" id="rcp_country">
              <?php foreach ( get_country() as $key => $value ) : ?>
                  <option value="<?php echo esc_attr( $key ); ?>" <?php checked_country( $selected_country, $key ); ?>><?php echo $value['country']; ?></option>
              <?php endforeach; ?>
          </select>

      </p>
      <p class="vat_p" style="margin-top: 50px; width: 47%; margin-left: 6%; float:left;">
          <label  for="rcp_btw_number"><?php _e( 'Your VAT number (optional)', 'rcp' ); ?></label>
          <input name="rcp_btw_number" id="rcp_btw_number" type="text" value="<?php echo openssl_decrypt(base64_decode(esc_attr( $btw_number )), $encrypt_method, $key, 0, $iv); ?>"/>
      </p>
    </p>
    <?php
  }

  function checked_country($string1, $string2) {
    if($string1 == $string2) {
        echo "selected";
    }
  }


  add_action( 'rcp_edit_member_after', 'pw_rcp_add_member_edit_fields' );

  /**
   * Adds the custom fields to the member edit screen
   */
  function pw_rcp_add_member_edit_fields($user_id = 0) {
    $number = get_user_meta( $user_id, 'rcp_number', true );
    $btw_number = get_user_meta( $user_id, 'rcp_btw_number', true );
    $country = get_user_meta( $user_id, 'rcp_country', true );
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'ABk FA sjdanjk lallLL';
    $secret_iv = 'TSAAnkks ksj sknalSAFF';
    // hash
    $key = hash('sha256', $secret_key);
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
        <input name="rcp_btw_number" id="rcp_btw_number" type="text" value="<?php echo openssl_decrypt(base64_decode(esc_attr( $btw_number )), $encrypt_method, $key, 0, $iv); ?>"/>
        <p class="description"><?php _e( 'The member\'s VAT number', 'rcp' ); ?></p>
      </td>
    </tr>

    <tr valign="top">
      <th scope="row" valign="top">
        <label for="rcp_country"><?php _e( 'Country', 'rcp' ); ?></label>
      </th>
      <td>
        <input name="rcp_country" id="rcp_country" type="text" value="<?php echo $country; ?>"/>
        <p class="description"><?php _e( 'The member\'s country', 'rcp' ); ?></p>
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
    if (empty( $posted['rcp_number'])) {
      rcp_errors()->add( 'invalid_profession', __( 'Please enter your phone number', 'rcp' ), 'register' );
    }

    if ( empty( $posted['rcp_country'] ) || $posted['rcp_country'] == '*' ) {
        rcp_errors()->add( 'empty_country', __( 'Please select your country', 'rcp' ), 'register' );
    }

    if ((!empty( $posted['rcp_btw_number']) && $posted['rcp_country'] == "NL")
        || (empty( $posted['rcp_btw_number']) && $posted['rcp_country'] == "NL")
        || (!empty( $posted['rcp_btw_number']) && array_key_exists($posted['rcp_country'], get_eu_countries()))) {
        $vat_number = isset($posted['rcp_btw_number']) ? $posted['rcp_btw_number'] : "";
        $vat_number = str_replace(array(' ', '.', '-', ',', ', '), '', trim($vat_number));

        $contents = @file_get_contents('https://controleerbtwnummer.eu/api/validate/'.$vat_number.'.json');

        if($contents === false) {
            throw new Exception('service unavailable');
        }
        else {
            $res = json_decode($contents);

            if(!($res->valid || (string)$vat_number == "")) {
                rcp_errors()->add( 'invalid_location', __( 'Wrong FAT number.', 'rcp' ), 'register' );
            } else {

            }
        }
    } else if(empty($posted['rcp_btw_number']) && $posted['rcp_level'] == 1) {
        rcp_errors()->add( 'Wrong FAT number.', __( 'Wrong FAT number.', 'rcp' ), 'register' );
    }
  }


  add_action( 'rcp_form_processing', 'pw_rcp_save_user_fields_on_register', 10, 2 );
  /**
   * Stores the information submitted during registration
   */
  function pw_rcp_save_user_fields_on_register( $posted, $user_id ) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = 'ABk FA sjdanjk lallLL';
    $secret_iv = 'SAAnkks ksj sknalSAFF';
    // hash
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    $output = openssl_encrypt(sanitize_text_field( $posted['rcp_btw_number'] ), $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);

    if( ! empty( $posted['rcp_number'] ) ) {
      update_user_meta( $user_id, 'rcp_number', sanitize_text_field( $posted['rcp_number'] ) );
    }

    if( ! empty( $posted['rcp_calendar'] ) ) {
      update_user_meta( $user_id, 'rcp_calendar', sanitize_url( $posted['rcp_calendar'] ) );
    }

    if( ! empty( $posted['rcp_btw_number'] ) ) {
      update_user_meta( $user_id, 'rcp_btw_number', $output);
    }

    if( ! empty( $posted['rcp_country'] ) ) {
      update_user_meta( $user_id, 'rcp_country', $output);
    }
  }

  /**
     * Stores the information submitted profile update
     *
     */
    function pw_rcp_save_user_fields_on_profile_save( $user_id ) {
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'ABk FA sjdanjk lallLL';
        $secret_iv = 'SAAnkks ksj sknalSAFF';
        // hash
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);


    	if( ! empty( $_POST['rcp_number'] ) ) {
    		update_user_meta( $user_id, 'rcp_number', sanitize_text_field( $_POST['rcp_number'] ) );
    	}

        if( ! empty( $_POST['rcp_calendar'] ) ) {
            update_user_meta( $user_id, 'rcp_calendar', sanitize_text_field( $_POST['rcp_calendar'] ) );
        }

        if( ! empty( $_POST['rcp_country'] ) ) {
            update_user_meta( $user_id, 'rcp_country', sanitize_text_field( $_POST['rcp_country'] ) );
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
                    $output = openssl_encrypt(sanitize_text_field( $vat_number ), $encrypt_method, $key, 0, $iv);
                    $output = base64_encode($output);
                    update_user_meta( $user_id, 'rcp_btw_number', $output);
                }
            }
        } else {
            update_user_meta( $user_id, 'rcp_btw_number', "");
        }
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

  function fb_filter_query( $query, $error = true ) {
    if ( is_search() ) {
      $query->is_search = false;
      $query->query_vars[s] = false;
      $query->query[s] = false;

      // to error
      if ( $error == true )
        $query->is_404 = true;
    }
  }
?>

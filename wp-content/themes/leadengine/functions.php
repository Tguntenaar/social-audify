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
      wp_send_json(array('success'=>'toggled'));
    }
    elseif ($type === 'report') {
      require_once(dirname(__FILE__)."/dashboard/services/report_service.php");
      $report_service = new report_service($connection);
      $report_id = $_POST['report_id'];

      $report_service->toggle_config_visibility($report_id, $field);
      wp_send_json(array('success'=>'toggled'));
    }
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

    // $audit_data = $main_control->get_audit_data($audit_id, $competitor)[0];
    $audit_data = $audit_service->get($audit_id);
    $audit_data_facebook = json_decode($audit_data[0]->facebook_data);
    $audit_data_facebook->runningAdds = ($data == 'yes') ? 1 : 0;

    $audit_service->update_ad_field($audit_id, "facebook_data", $audit_data_facebook, $competitor);

    wp_send_json(array('audit_data'=>$audit_data_facebook, 'competitor'=>$competitor, 'data'=>$data));
    wp_die();
  }


  add_action( 'wp_ajax_update_meta_audit', 'create_audit');
  add_action( 'wp_ajax_nopriv_update_meta_audit', 'not_logged_in');

  function create_audit() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/user_controller.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/client_controller.php");

    require_once(dirname(__FILE__)."/dashboard/models/audit.php");
    require_once(dirname(__FILE__)."/dashboard/models/client.php"); // nodig?
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
    $count = $audit_control->check_website($audit_id);

    wp_send_json($count);
    wp_die();
  }


  add_action( 'wp_ajax_flip_mail', 'activate_mail');
  add_action( 'wp_ajax_nopriv_flip_mail', 'not_logged_in');

  // Check if crawl has completed
  function activate_mail() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
    require_once(dirname(__FILE__)."/dashboard/models/audit.php");

    $connection = new connection;
    $audit_control = new audit_controller($connection);

    $audit_id = $_POST['audit'];
    $value = $_POST['value'];

    $audit = $audit_control->get($audit_id);
    $audit->update('mail_bit', $value == 'true');

    wp_send_json(array('value' => $value));
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

    $iba_id = $_POST['iba_id'];

    $value = $user->update('User', 'instagram_business_account_id', $iba_id);

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

  add_action( 'wp_ajax_textareas', 'update_areas');
  add_action( 'wp_ajax_nopriv_textareas', 'not_logged_in');

  function update_areas() {
    require_once(dirname(__FILE__)."/dashboard/services/connection.php");
    $connection = new connection;
    $type = $_POST['type'];
    if ($type == 'audit') {
      require_once(dirname(__FILE__)."/dashboard/controllers/audit_controller.php");
      $audit_control = new audit_controller($connection);
      $fields = $audit_control->get_area_fields();
      foreach($fields as $field) {
        if (isset($_POST[$field])) {
          $id = $_POST[$_POST['type']];
          $audit_control->update($id, $field, sanitize_textarea_field(stripslashes($_POST[$field])), 'Audit_template');
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

    $client_id = $_POST['user'];
    $audit_report_id = $_POST['audit'];
    $post_id = $_POST['post'];
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

    $auth_send = $_POST['auth'];
    $auth_known = hash('sha256', 'what'.$post_id.'who'.$audit_report_id.'how'.$client_id);

    if ($auth_send != $auth_known) {
      $error = new WP_Error( '401', 'Unauthorized', 'auth' );
      wp_send_json_error($error);
    } else {
      $auditreport = $control->get($audit_report_id);
      $auditreport->delete();
    }

    wp_send_json(array('deleted'=>"everyting"));
    wp_die();
  }


  add_action( 'wp_ajax_delete_client', 'delete_client');
  add_action( 'wp_ajax_nopriv_delete_client', 'not_logged_in');

  function delete_client() {
    include(dirname(__FILE__)."/dashboard/services/connection.php");
    include(dirname(__FILE__)."/dashboard/controllers/client_controller.php");
    include(dirname(__FILE__)."/dashboard/models/client.php");

    $connection = new connection;
    $client_control = new client_controller($connection);

    $client_id = $_POST['client'];
    $client = $client_control->get($client_id);

    // TODO: kan er niet bij met mijn hoofd

    // $auth_send = $_POST['auth'];
    // $time =  $_POST['time'];
    // $auth_known_list = get_auth_list(get_current_user_id());
    // $auth = hash('sha256', 'auth'.get_current_user_id().'salted'.$time.'randomstuff');
    // wp_send_json(array('id'=>$client->id, 'auth1'=>$auth_send, 'auth2'=>!in_array($auth_send, $auth_known_list), 'bool'=>($auth_send == $auth)));
    // if ($auth_send == $auth || !in_array($auth_send, $auth_known_list)) {
    //   $error = new WP_Error( '401', 'Unauthorized', 'auth' );
    //   wp_send_json_error($error);
    //   wp_die();
    // }

    $client->delete();
    wp_send_json(array('id'=>$client->id));
    wp_die();
  }

  /**
   * Creates list of possible authenication strings for the last 15 minutes.
   */
  function get_auth_list($user_id) {
    $list = array();
    for ($i = 0; $i < 15; $i++) {
      $time = date("Y-m-d H:i:s", strtotime(date('Y-m-d H:i')) + $i * 60);

      $auth = hash('sha256', 'auth'.$user_id.'salted'.$time.'randomstuff');
      array_push($list, $auth);
    }
    return $list;
  }

  require_once(get_template_directory() . '/core/init.php');


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

    $encrypt_method = "AES-256-CBC";
    $secret_key = 'ABk FA sjdanjk lallLL';
    $secret_iv = 'SAAnkks ksj sknalSAFF';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    ?>
    <p>
      <label for="rcp_number"><?php _e( 'Your phone number', 'rcp' ); ?></label>
      <input name="rcp_number" id="rcp_number" type="text" value="<?php echo esc_attr( $number ); ?>"/>
    <p>
      <?php if(!(get_post_field( 'post_name', get_post() ) == "register")) {?>
          <label for="rcp_calendar"><?php _e( 'Your calendar link', 'rcp' ); ?></label>
          <input name="rcp_calendar" id="rcp_calendar" type="text" value="<?php echo esc_attr( $calendar ); ?>"/>
      <?php } ?>
      <label for="rcp_btw_number"><?php _e( 'Your VAT number', 'rcp' ); ?></label>
      <input name="rcp_btw_number" id="rcp_btw_number" type="text" value="<?php echo openssl_decrypt(base64_decode(esc_attr( $btw_number )), $encrypt_method, $key, 0, $iv); ?>"/>
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
        <input name="rcp_calendar" id="rcp_calendar" type="text" value="<?php echo esc_attr( $number ); ?>"/>
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

    // if (empty( $posted['rcp_calendar'])) {
    //   rcp_errors()->add( 'invalid_profession', __( 'Please enter your calendar link.', 'rcp' ), 'register' );
    // }

    if (empty( $posted['rcp_btw_number'])) {
      rcp_errors()->add( 'invalid_location', __( 'Please enter your VAT number', 'rcp' ), 'register' );
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
      update_user_meta( $user_id, 'rcp_calendar', sanitize_text_field( $posted['rcp_calendar'] ) );
    }

    if( ! empty( $posted['rcp_btw_number'] ) ) {
      update_user_meta( $user_id, 'rcp_btw_number', $output);
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

        $output = openssl_encrypt(sanitize_text_field( $_POST['rcp_btw_number'] ), $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);

    	if( ! empty( $_POST['rcp_number'] ) ) {
    		update_user_meta( $user_id, 'rcp_number', sanitize_text_field( $_POST['rcp_number'] ) );
    	}

        if( ! empty( $_POST['rcp_calendar'] ) ) {
            update_user_meta( $user_id, 'rcp_calendar', sanitize_text_field( $_POST['rcp_calendar'] ) );
        }

    	if( ! empty( $_POST['rcp_btw_number'] ) ) {
    		update_user_meta( $user_id, 'rcp_btw_number', $output);
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
?>

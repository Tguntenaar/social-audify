<?php
/*
Plugin Name: WP User Avatar Pro
Plugin URI: http://www.flippercode.com/
Description: A premium version of wp user avatar wordpress plugin. Assign user roles according to wordpress roles. Users can also upload their own avatars from frontend.
Author: flippercode
Author URI: http://www.flippercode.com/
Version: 5.0.0
Text Domain: wp-user-avatar-pro
Domain Path: /lang/
*/


if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! class_exists( 'WP_User_Avatar_Pro' ) ) {
	/**
	 * Main plugin class
	 *
	 * @author Flipper Code <hello@flippercode.com>
	 * @package Avatar
	 */
	class WP_User_Avatar_Pro {

		/**
		 * List of Modules.
		 *
		 * @var array
		 */
		private $modules = array();
		private $errors = array();
		/**
		 * Intialize variables, files and call actions.
		 *
		 * @var array
		 */
		public function __construct() {

			$this->_define_constants();
			$this->_load_files();
			$this->_wpua_register_plugin_hooks();

		}

		/**
		 * Call WordPress hooks.
		 */
		function _wpua_register_plugin_hooks() {

			register_activation_hook( __FILE__, array( $this, 'wpua_plugin_activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'wpua_plugin_deactivation' ) );
			add_action( 'plugins_loaded', array( $this, 'wpua_load_plugin_languages' ) );
			add_action( 'init', array( $this, 'wpua_register_hooks_init' ) );
			add_action( 'wp_ajax_wpua_save_avatar_action', array( $this, 'wpua_upload_action_callback' ) );
			add_action( 'wp_ajax_nopriv_wpua_save_avatar_action', array( $this, 'wpua_upload_action_callback' ) );
			if ( is_admin() ) {
				add_action( 'admin_init', array( $this, 'wpua_save_settings' ) );
			}

		}


		/**
		 * Call hooks.
		 */
		function wpua_register_hooks_init() {

				global $wpdb,$pagenow,$pages;
			if ( is_admin() ) {
				// Actions.
				add_action( 'admin_menu', array( $this, 'create_menu' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'wpua_editor_enquque_scripts' ) );
				add_action( 'admin_head', array( 'WPUA_Avatar', 'wpua_theme_setting' ) );
			} else {
				// Fronted scripts.
				add_action( 'wp_enqueue_scripts', array( $this, 'wpua_editor_enquque_scripts' ) );
				add_action( 'wp_head', array( 'WPUA_Avatar', 'wpua_theme_setting' ) );
			}
				add_action( 'show_user_profile', array( 'WPUA_Avatar', 'wpua_avatar_html' ) );
				add_action( 'wpua_show_user_profile', array( 'WPUA_Avatar', 'wpua_avatar_html' ) );
				add_action( 'edit_user_profile', array( 'WPUA_Avatar', 'wpua_avatar_html' ) );

				// Update Avatar.
				add_action( 'personal_options_update', array( 'WPUA_Avatar', 'wpua_avatar_save' ) );
				add_action( 'edit_user_profile_update', array( 'WPUA_Avatar', 'wpua_avatar_save' ) );
				add_action( 'wpua_update', array( 'WPUA_Avatar', 'wpua_avatar_save' ) );
				// Fronted init
			if ( WPUA_Avatar::get_setting( 'wp_user_avatar_upload_registration' ) ) {

				$wpua_obj = new WPUA_Avatar();

				// Upload Avatar at Registration form
				add_action( 'user_new_form', array( $wpua_obj, 'wpua_avatar_form_new' ) );
				if ( is_multisite() ) {
					add_action( 'signup_extra_fields', array( $wpua_obj, 'wpua_avatar_form_new' ) );
					add_action( 'wpmu_new_user', array( $wpua_obj, 'wpua_avatar_save' ) );
					add_filter( 'add_signup_meta', array( $wpua_obj, 'wpua_add_signup_meta_mu' ) );
					add_action( 'wpmu_activate_user', array( $wpua_obj, 'wpua_action_process_option_update_mu' ), 10, 3 );
				} else {
					add_action( 'login_head', array( $wpua_obj, 'wpua_theme_setting' ), 20 );
					add_action( 'login_enqueue_scripts', array( $this, 'wpua_editor_enquque_scripts' ) );
					add_action( 'register_form', array( $wpua_obj, 'wpua_avatar_form_new' ), 4 );
					add_action( 'user_register', array( $wpua_obj, 'wpua_avatar_save' ) );
				}
			}
				// ShortCodes.
				ob_start();
				add_shortcode( 'avatar', array( 'WPUA_Avatar', 'wpua_shortcode' ) );
				add_shortcode( 'avatar_listing', array( 'WPUA_Avatar', 'wpua_shortcode_listing' ) );
				add_shortcode( 'avatar_upload', array( 'WPUA_Avatar', 'wpua_avatar_edit_html' ) );
				$this->wpua_global_variables();
		}

		function wpua_save_settings() {

			if ( ! isset( $_POST['WPUAP_save_settings'] ) ) {
				return;
			}

			// Verify Nonce Verification
			if ( isset( $_REQUEST['_wpnonce'] ) ) {
				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );
			}
			if ( isset( $nonce ) and ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {
				die( 'Cheating...' );
			}

			// Verify Authorization
			if ( ! current_user_can( 'administrator' ) ) {
				return;
			}

			// Perform Validations
			if ( is_array( $this->errors ) && ! empty( $this->errors ) ) {
				$this->throw_errors();
			}

			$wp_user_avatar_upload_registration = ( isset( $_POST['WPUAP_save_settings'] ) ) ? ( isset( $_POST['wp_user_avatar_upload_registration'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_user_avatar_upload_registration'] ) ) : '' ) : get_option( 'wp_user_avatar_upload_registration' );

			$wp_user_avatar_hide_webcam = ( isset( $_POST['WPUAP_save_settings'] ) ) ? ( isset( $_POST['wp_user_avatar_hide_webcam'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_user_avatar_hide_webcam'] ) ) : '' ) : get_option( 'wp_user_avatar_hide_webcam' );

			$wp_user_avatar_hide_mediamanager = ( isset( $_POST['WPUAP_save_settings'] ) ) ? ( isset( $_POST['wp_user_avatar_hide_mediamanager'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_user_avatar_hide_mediamanager'] ) ) : '' ) : get_option( 'wp_user_avatar_hide_mediamanager' );

			$avatar_storage_option = isset( $_POST['avatar_storage_option'] ) ? sanitize_text_field( wp_unslash( $_POST['avatar_storage_option'] ) ) : get_option( 'avatar_storage_option' );

			$wp_user_avatar_upload_size_limit = isset( $_POST['wp_user_avatar_upload_size_limit'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_user_avatar_upload_size_limit'] ) ) : get_option( 'wp_user_avatar_upload_size_limit' );

			$wp_user_avatar_upload_size_width = isset( $_POST['wp_user_avatar_upload_size_width'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_user_avatar_upload_size_width'] ) ) : get_option( 'wp_user_avatar_upload_size_width' );

			$wp_user_avatar_upload_size_height = isset( $_POST['wp_user_avatar_upload_size_height'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_user_avatar_upload_size_height'] ) ) : get_option( 'wp_user_avatar_upload_size_height' );

			$wp_user_avatar_upload_size_height = isset( $_POST['wp_user_avatar_upload_size_height'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_user_avatar_upload_size_height'] ) ) : get_option( 'wp_user_avatar_upload_size_height' );

			$wp_user_avatar_thumbnail_w = isset( $_POST['wp_user_avatar_thumbnail_w'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_user_avatar_thumbnail_w'] ) ) : get_option( 'wp_user_avatar_thumbnail_w' );

			$wp_user_avatar_thumbnail_h = isset( $_POST['wp_user_avatar_thumbnail_h'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_user_avatar_thumbnail_h'] ) ) : get_option( 'wp_user_avatar_thumbnail_h' );

			$wp_user_avatar_resize_upload = ( isset( $_POST['WPUAP_save_settings'] ) ) ? ( isset( $_POST['wp_user_avatar_resize_upload'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_user_avatar_resize_upload'] ) ) : '' ) : get_option( 'wp_user_avatar_resize_upload' );

			$show_avatars = ( isset( $_POST['WPUAP_save_settings'] ) ) ? ( isset( $_POST['show_avatars'] ) ? sanitize_text_field( wp_unslash( $_POST['show_avatars'] ) ) : '' ) : get_option( 'show_avatars' );

			$avatar_default = isset( $_POST['avatar_default'] ) ? sanitize_text_field( wp_unslash( $_POST['avatar_default'] ) ) : get_option( 'avatar_default' );

			$wp_user_avatar_disable_gravatar = ( isset( $_POST['WPUAP_save_settings'] ) ) ? ( isset( $_POST['wp_user_avatar_disable_gravatar'] ) ? wp_unslash( $_POST['wp_user_avatar_disable_gravatar'] ) : '' ) : get_option( 'wp_user_avatar_disable_gravatar' );

			$avatar_rating = isset( $_POST['avatar_rating'] ) ? sanitize_text_field( wp_unslash( $_POST['avatar_rating'] ) ) : get_option( 'avatar_rating' );

			$wp_user_avatar_storage = isset( $_POST['wp_user_avatar_storage'] ) ? $_POST['wp_user_avatar_storage'] : get_option( 'wp_user_avatar_storage' );

			if ( count( $wp_user_avatar_storage ) > 0 ) {

				if ( isset( $wp_user_avatar_storage['setting']['aws']['key'] ) ) {

					$wp_user_avatar_storage['setting']['aws']['key'] = sanitize_text_field( $wp_user_avatar_storage['setting']['aws']['key'] );
				}

				if ( isset( $wp_user_avatar_storage['setting']['aws']['secret_key'] ) ) {

					$wp_user_avatar_storage['setting']['aws']['secret_key'] = sanitize_text_field( $wp_user_avatar_storage['setting']['aws']['secret_key'] );
				}

				if ( isset( $wp_user_avatar_storage['aws'] ) ) {
					$wp_user_avatar_storage['aws'] = sanitize_text_field( $wp_user_avatar_storage['aws'] );
				}

				if ( isset( $wp_user_avatar_storage['dropbox']['access_token'] ) ) {
					$wp_user_avatar_storage['dropbox']['access_token'] = sanitize_text_field( $wp_user_avatar_storage['dropbox']['access_token'] );
				}

				if ( isset( $wp_user_avatar_storage['dropbox']['upload_path'] ) ) {
					$wp_user_avatar_storage['dropbox']['upload_path'] = sanitize_text_field( $wp_user_avatar_storage['dropbox']['upload_path'] );
				}
			}

			$avatar_default_wp_user_avatar = isset( $_POST['avatar_default_wp_user_avatar'] ) ? $_POST['avatar_default_wp_user_avatar'] : get_option( 'avatar_default_wp_user_avatar' );

			$wp_user_avatar_settings = isset( $_POST['WPUAP_save_settings'] ) ? $_POST['wp_user_avatar_settings'] : get_option( 'wp_user_avatar_settings' );

			if ( count( $wp_user_avatar_settings ) > 0 ) {
				foreach ( $wp_user_avatar_settings as $key => $setting_value ) {
					if ( ! ( is_array( $setting_value ) ) ) {
						$wp_user_avatar_settings[ $key ] = sanitize_text_field( $setting_value );
					}
				}
			}

			$role_based_avatar = isset( $_POST['role_based_avatar'] ) ? serialize( $_POST['role_based_avatar'] ) : get_option( 'role_based_avatar' );

			$this->remove_old_avatar_files( $avatar_default_wp_user_avatar, $role_based_avatar );
			update_option( 'wp_user_avatar_upload_registration', $wp_user_avatar_upload_registration );
			update_option( 'wp_user_avatar_hide_webcam', $wp_user_avatar_hide_webcam );
			update_option( 'wp_user_avatar_hide_mediamanager', $wp_user_avatar_hide_mediamanager );
			update_option( 'avatar_storage_option', $avatar_storage_option );
			update_option( 'wp_user_avatar_upload_size_limit', $wp_user_avatar_upload_size_limit );
			update_option( 'wp_user_avatar_upload_size_width', $wp_user_avatar_upload_size_width );
			update_option( 'wp_user_avatar_upload_size_height', $wp_user_avatar_upload_size_height );
			update_option( 'wp_user_avatar_thumbnail_w', $wp_user_avatar_thumbnail_w );
			update_option( 'wp_user_avatar_thumbnail_h', $wp_user_avatar_thumbnail_h );
			update_option( 'wp_user_avatar_resize_upload', $wp_user_avatar_resize_upload );
			update_option( 'show_avatars', $show_avatars );
			update_option( 'avatar_default', $avatar_default );
			update_option( 'wp_user_avatar_disable_gravatar', $wp_user_avatar_disable_gravatar );
			update_option( 'avatar_rating', $avatar_rating );
			update_option( 'wp_user_avatar_storage', $wp_user_avatar_storage );
			update_option( 'avatar_default_wp_user_avatar', $avatar_default_wp_user_avatar );
			update_option( 'wp_user_avatar_settings', $wp_user_avatar_settings );
			update_option( 'role_based_avatar', $role_based_avatar );
			$response['success'] = esc_html__( 'Setting(s) saved successfully.', 'wp-user-avatar-pro' );
			$this->wpua_global_variables(); // Resetting Global Varibales After Saving New Settings
			return $response;
		}


		function remove_old_avatar_files( $new_default_avatar, $new_roles_avatar ) {

			$old_default_avatar = get_option( 'avatar_default_wp_user_avatar' );
			$old_roles_avatar = maybe_unserialize( get_option( 'role_based_avatar' ) );
			$new_roles_avatar = maybe_unserialize( $new_roles_avatar );
			$upload_dir = wp_upload_dir();
			if ( is_multisite() ) {
				$pos = strpos( $upload_dir['basedir'], 'uploads' );
				$wpua_upload_dir = substr( $upload_dir['basedir'], 0, ( $pos + 7 ) ) . '/wp-user-avatar/';
				$pos_url = strpos( $upload_dir['baseurl'], 'uploads' );
				$wpua_upload_url = substr( $upload_dir['baseurl'], 0, ( $pos_url + 8 ) ) . '/wp-user-avatar/';
			} else {
				$wpua_upload_dir = $upload_dir['basedir'] . '/wp-user-avatar/';
				$wpua_upload_url = $upload_dir['baseurl'] . '/wp-user-avatar/';
			}
			if ( ! empty( $new_default_avatar ) && ( $new_default_avatar != $old_default_avatar ) ) {
				@unlink( $wpua_upload_dir . '/' . $old_default_avatar );
			}
			foreach ( $new_roles_avatar as $key => $value ) {
				if ( ! empty( $new_default_avatar ) && ( $new_roles_avatar[ $key ] != $old_roles_avatar[ $key ] ) ) {
					@unlink( $wpua_upload_dir . '/' . $old_roles_avatar[ $key ] );
				}
			}
		}
		public function wpua_global_variables() {

			global $avatar_default,
				   $show_avatars,
				   $wpua_avatar_default,
				   $wpua_disable_gravatar,
				   $wpua_upload_registration,
				   $wp_user_avatar_thumbnail_w,
				   $wp_user_avatar_thumbnail_h,
				   $wp_user_avatar_upload_max_w,
				   $wp_user_avatar_upload_max_h,
				   $wpua_tinymce,
				   $mustache_original,
				   $mustache_medium,
				   $mustache_thumbnail,
				   $mustache_avatar,
				   $mustache_admin,
				   $wpua_default_avatar_updated,
				   $upload_size_limit,
				   $upload_size_limit_with_units,
				   $wpua_user_upload_size_limit,
				   $wpua_upload_size_limit,
				   $wpua_upload_size_limit_with_units,
				   $all_sizes,
				   $wpua_imgloader,
				   $wpua_upload_dir,
				   $wpua_upload_url,
				 $avatar_storage_option,
				 $wp_user_avatar_hide_webcam,
				 $wp_user_avatar_hide_mediamanager,
				 $wp_user_avatar_storage;
			// Default avatar name
			$avatar_default = get_option( 'avatar_default' );
			// Attachment ID of default avatar
			$role_based_avatar = unserialize( get_option( 'role_based_avatar' ) );
			$uid = isset( $_GET['user_id'] ) ? sanitize_text_field( $_GET['user_id'] ) : get_current_user_id();
			$users = new WP_User( $uid );
			$wpua_avatar_default = isset( $users->roles[0] ) &&
				isset( $role_based_avatar[ $users->roles[0] ] )
				&& ! empty( $role_based_avatar[ $users->roles[0] ] )
					? $role_based_avatar[ $users->roles[0] ]
					: get_option( 'avatar_default_wp_user_avatar' );
			// Avatar media uploader
				$avatar_storage_option = get_option( 'avatar_storage_option', 'media' );
				$wp_user_avatar_storage = get_option( 'wp_user_avatar_storage' );
			// Booleans
			$show_avatars = get_option( 'show_avatars' );
			$wpua_disable_gravatar = get_option( 'wp_user_avatar_disable_gravatar' );
			$wpua_upload_registration = get_option( 'wp_user_avatar_upload_registration' );
			// Avatar dimensions
			$wp_user_avatar_thumbnail_w = get_option( 'wp_user_avatar_thumbnail_w' );
			$wp_user_avatar_thumbnail_h = get_option( 'wp_user_avatar_thumbnail_h' );
			// Avatar dimensions
			$wp_user_avatar_hide_mediamanager = get_option( 'wp_user_avatar_hide_mediamanager' );
			$wp_user_avatar_hide_webcam = get_option( 'wp_user_avatar_hide_webcam' );
			// Default avatar 512x512
			$mustache_original = WPUAP_URL . 'assets/images/wpua.png';
			// Default avatar 300x300
			$mustache_medium = WPUAP_URL . 'assets/images/wpua-300x300.png';
			// Default avatar 150x150
			$mustache_thumbnail = WPUAP_URL . 'assets/images/wpua-150x150.png';
			// Default avatar 96x96
			$mustache_avatar = WPUAP_URL . 'assets/images/wpua-96x96.png';
			// Default avatar 32x32
			$mustache_admin = WPUAP_URL . 'assets/images/wpua-32x32.png';
			// Check for updates
			$wpua_default_avatar_updated = get_option( 'wp_user_avatar_default_avatar_updated' );
			// Server upload size limit
			$upload_size_limit = wp_max_upload_size();
			// Convert to KB
			if ( $upload_size_limit > 1024 ) {
				$upload_size_limit /= 1024;
			}
			$upload_size_limit_with_units = (int) $upload_size_limit . 'KB';
			// User upload size limit
			$wpua_user_upload_size_limit = get_option( 'wp_user_avatar_upload_size_limit' );
			if ( $wpua_user_upload_size_limit == 0 || $wpua_user_upload_size_limit > wp_max_upload_size() ) {
				$wpua_user_upload_size_limit = wp_max_upload_size();
			}
			// Value in bytes
			$wpua_upload_size_limit = $wpua_user_upload_size_limit;
			// Convert to KB
			if ( $wpua_user_upload_size_limit > 1024 ) {
				$wpua_user_upload_size_limit /= 1024;
			}
			$wpua_upload_size_limit_with_units = (int) $wpua_user_upload_size_limit . 'KB';
			// Check for custom image sizes
			$all_sizes = array_merge( get_intermediate_image_sizes(), array( 'original' ) );
			// Avatar Upload Directory;
			$upload_dir = wp_upload_dir();
			if ( is_multisite() ) {
				$pos = strpos( $upload_dir['basedir'], 'uploads' );
				$wpua_upload_dir = substr( $upload_dir['basedir'], 0, ( $pos + 7 ) ) . '/wp-user-avatar/';
				$pos_url = strpos( $upload_dir['baseurl'], 'uploads' );
				$wpua_upload_url = substr( $upload_dir['baseurl'], 0, ( $pos_url + 8 ) ) . '/wp-user-avatar/';
			} else {
				$wpua_upload_dir = $upload_dir['basedir'] . '/wp-user-avatar/';
				$wpua_upload_url = $upload_dir['baseurl'] . '/wp-user-avatar/';
			}
		}
		/**
		 *
		 */
		public function wpua_upload_action_callback() {

			global $_POST;
			$response = array();

			// Avatar Upload Directory;
			$upload_dir = wp_upload_dir();
			$wp_user_avatar_thumbnail_w = get_option( 'wp_user_avatar_thumbnail_w' );
			$wp_user_avatar_thumbnail_h = get_option( 'wp_user_avatar_thumbnail_h' );
			$is_strickly_resize = get_option( 'wp_user_avatar_resize_upload' );
			if ( is_multisite() ) {
				$pos = strpos( $upload_dir['basedir'], 'uploads' );
				$wpua_upload_dir = substr( $upload_dir['basedir'], 0, ( $pos + 7 ) ) . '/wp-user-avatar/';
				$pos_url = strpos( $upload_dir['baseurl'], 'uploads' );
				$wpua_upload_url = substr( $upload_dir['baseurl'], 0, ( $pos_url + 8 ) ) . '/wp-user-avatar/';
			} else {
				$wpua_upload_dir = $upload_dir['basedir'] . '/wp-user-avatar/';
				$wpua_upload_url = $upload_dir['baseurl'] . '/wp-user-avatar/';
			}
			$cropper = new CI_Image_Uploader();
			if ( isset( $_POST['no_cache'] ) == 'true' ) {
				$cropper->folder = $wpua_upload_dir;
				$cropper->folder_url = $wpua_upload_url;
				$cropper->file_name = isset( $_POST['avatar_for_role'] ) ? 'wp-user-role-' . $_POST['avatar_for_role'] . '-' . rand( 10, 100 ) : 'wp-user-avatar' . rand( 10, 100 );
			} else {
				$cropper->folder = $wpua_upload_dir . 'cache/';
				$cropper->folder_url = $wpua_upload_url . 'cache/';
			}
			if ( 1 == $is_strickly_resize && isset( $wp_user_avatar_thumbnail_w ) && ! empty( $wp_user_avatar_thumbnail_w ) ) {
				$new_width  = $wp_user_avatar_thumbnail_w;
			} else {
				$new_width = $_POST['ci_crop_w']; }
			if ( 1 == $is_strickly_resize && isset( $wp_user_avatar_thumbnail_h ) && ! empty( $wp_user_avatar_thumbnail_h ) ) {
				$new_height = $wp_user_avatar_thumbnail_h;
			} else {
				$new_height = $_POST['ci_crop_h']; }
			$cropper->save_image( $new_width, $new_height );
			echo json_encode( $cropper->get_response() );
			die();
		}

			/**
			 * Process slug and display view in the backend.
			 */
		function processor() {

			$return = '';
			if ( isset( $_GET['page'] ) ) {
				$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			} else {
				$page = 'wpuap_view_overview';
			}
			$pageData = explode( '_', $page );
			$obj_type = $pageData[2];
			$obj_operation = $pageData[1];
			if ( count( $pageData ) < 3 ) {
				die( 'Cheating!' );
			}
			try {
				if ( count( $pageData ) > 3 ) {
					$obj_type = $pageData[2] . '_' . $pageData[3];
				}
				$factoryObject = new WPUAP_Controller();
				$viewObject = $factoryObject->create_object( $obj_type );
				$viewObject->display( $obj_operation );
			} catch ( Exception $e ) {
				echo WPUAP_Template::show_message( array( 'error' => $e->getMessage() ) );
			}
		}
			/**
			 * Create backend navigation.
			 */
		function create_menu() {

			global $navigations;
			$pagehook1 = add_menu_page(
				esc_html__( 'WP User Avatar', 'wp-user-avatar-pro' ),
				esc_html__( 'WP User Avatar', 'wp-user-avatar-pro' ),
				'wpuap_view_overview',
				WPUAP_SLUG,
				array( $this, 'processor' ),
				esc_url( WPUAP_IMAGES . 'fc-small-logo.png' )
			);
			if ( current_user_can( 'manage_options' ) ) {
				$role = get_role( 'administrator' );
				$role->add_cap( 'wpuap_view_overview' );
			}
			$this->load_modules_menu();
			add_action( 'load-' . $pagehook1, array( $this, 'WPUAP_backend_scripts' ) );
		}
			/**
			 * Read models and create backend navigation.
			 */
		function load_modules_menu() {

			$modules = $this->modules;
			$pagehooks = array();
			if ( is_array( $modules ) ) {
				foreach ( $modules as $module ) {
					$object = new $module();
					if ( method_exists( $object, 'navigation' ) ) {
						if ( ! is_array( $object->navigation() ) ) {
							continue;
						}
						foreach ( $object->navigation() as $nav => $title ) {
							if ( current_user_can( 'manage_options' ) && is_admin() ) {
								$role = get_role( 'administrator' );
								$role->add_cap( $nav );
							}
							$pagehooks[] = add_submenu_page(
								WPUAP_SLUG,
								$title,
								$title,
								$nav,
								$nav,
								array( $this, 'processor' )
							);
						}
					}
				}
			}
			if ( is_array( $pagehooks ) ) {
				foreach ( $pagehooks as $key => $pagehook ) {
					add_action( 'load-' . $pagehooks[ $key ], array( $this, 'WPUAP_backend_scripts' ) );
				}
			}
		}
			/**
			 * Eneque scripts in the backend.
			 */
		function WPUAP_backend_scripts() {

			global $current_user, $pagenow, $post;
			$this->wpua_editor_enquque_scripts();
			if ( wpua_is_author_or_above() ) {
				wp_enqueue_script( 'admin-bar' );
			}
			// Admin scripts
			if ( $pagenow == 'options-discussion.php' ) {
				wp_localize_script( 'wp-user-avatar', 'wpua_custom', array( 'avatar_thumb' => get_option( 'mustache_admin', true ) ) );
			}
			wp_enqueue_style( 'wp-color-picker' );
			$wp_scripts = array( 'jQuery', 'wp-color-picker' );
			if ( $wp_scripts ) {
				foreach ( $wp_scripts as $wp_script ) {
					wp_enqueue_script( $wp_script );
				}
			}
			wp_register_script( 'flippercode-ui.js', esc_url( WPUAP_JS . 'flippercode-ui.js' ) );
			$core_script_args = apply_filters(
				'fc_ui_script_args', array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'language' => 'en',
					'urlforajax' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'hide' => esc_html__( 'Hide', 'wp-user-avatar-pro' ),
					'nonce' => wp_create_nonce( 'fc_communication' ),
				)
			);
			wp_localize_script( 'flippercode-ui.js', 'fc_ui_obj', $core_script_args );
			wp_enqueue_script( 'flippercode-ui.js' );

			$scripts = array();
			$scripts[] = array(
				'handle'  => 'wpua-backend',
				'src'   => WPUAP_JS . 'backend.js',
				'deps'    => array(),
			);
			if ( $scripts ) {
				foreach ( $scripts as $script ) {
					wp_enqueue_script( $script['handle'], esc_url( $script['src'] ), $script['deps'] );
				}
			}
			$WPUAP_js_lang = array();
			$WPUAP_js_lang['ajax_url'] = admin_url( 'admin-ajax.php' );
			$WPUAP_js_lang['nonce'] = wp_create_nonce( 'wpua-call-nonce' );
			$WPUAP_js_lang['confirm'] = esc_html__( 'Are you sure to delete item?', 'wp-user-avatar-pro' );
			wp_localize_script( 'wpua-backend', 'WPUAP_js_lang', $WPUAP_js_lang );
			$admin_styles = array(
				'flippercode-ui' => WPUAP_CSS . 'flippercode-ui.css',
				'font-awe-style' => WPUAP_CSS . 'font-awesome.min.css',
				'wpuap-backend-css' => WPUAP_CSS . 'backend.css',
			);
			if ( $admin_styles ) {
				foreach ( $admin_styles as $admin_style_key => $admin_style_value ) {
					wp_enqueue_style( $admin_style_key, esc_url( $admin_style_value ) );
				}
			}
		}
		/**
		 *
		 */
		public function wpua_editor_enquque_scripts() {

			wp_enqueue_style( 'wp-user-avatar', esc_url( WPUAP_CSS . 'wp-user-avatar.css' ), '', WPUAP_VERSION );
			wp_enqueue_style( 'jcrop' );
			wp_enqueue_script( 'jcrop' );
			if ( get_option( 'wp_user_avatar_hide_webcam' ) != '1' ) {
				$display_webcam = 'true';
			} else {
				$display_webcam = 'false';
			}
			if ( $display_webcam == 'true' ) {
				wp_enqueue_script( 'wp-user-avatar-webcam', esc_url( WPUAP_JS . 'webcam.js' ), array( 'jquery' ), WPUAP_VERSION ); }
			wp_enqueue_script( 'wp-user-avatar-imgloader', esc_url( WPUAP_JS . 'wpua-imgloader.js' ), array( 'jquery' ), WPUAP_VERSION );
			if ( current_user_can( 'upload_files' ) && get_option( 'wp_user_avatar_hide_mediamanager' ) != '1' ) {
				$display_mediamanager = 'true';
				wp_enqueue_media();
			} else {
				$display_mediamanager = 'false';
			}
			$restriction = array();
			$restriction = array(
				'strickly_resize' => get_option( 'wp_user_avatar_resize_upload', true ),
				'max_file_size' => get_option( 'wp_user_avatar_upload_size_limit', 0 ),
				'max_file_width' => get_option( 'wp_user_avatar_upload_size_width', 0 ),
				'max_file_height' => get_option( 'wp_user_avatar_upload_size_height', 0 ),
			);
			if ( 0 == $restriction['max_file_size'] || '' == $restriction['max_file_size'] ) {
				$restriction['max_file_size'] = 134217728;
			}
			$file_size_error_message[] = sprintf( esc_html__( 'File uploading limits:  Size %s', 'wp-user-avatar-pro' ), esc_html( format_size_units( $restriction['max_file_size'] ) ) );
			if ( 0 != $restriction['max_file_width'] ) {
				$file_size_error_message[] = sprintf( esc_html__( 'Width %d px', 'wp-user-avatar-pro' ), $restriction['max_file_width'] );
			}
			if ( 0 != $restriction['max_file_height'] ) {
				$file_size_error_message[] = sprintf( esc_html__( 'Height: %d px', 'wp-user-avatar-pro' ), $restriction['max_file_height'] );
			}
			$file_size_error_message = implode( ', ', $file_size_error_message );

			$restriction['wp_user_avatar_thumbnail_w'] = get_option( 'wp_user_avatar_thumbnail_w' );
			$restriction['wp_user_avatar_thumbnail_h'] = get_option( 'wp_user_avatar_thumbnail_h' );

			$string = array(
				'file_type_error' => esc_html__( 'File Type is not valid.', 'wp-user-avatar-pro' ),
				'file_type_error_description' => esc_html__( 'Valid File Type is jpeg, png and gif.', 'wp-user-avatar-pro' ),
				'file_size_error' => esc_html__( 'File Size is too big', 'wp-user-avatar-pro' ),
				'file_size_error_description' => $file_size_error_message,
				'no_image' => esc_html__( 'Oops! Seems you didn\'t select any image.', 'wp-user-avatar-pro' ),
				'no_image_instruction' => esc_html__( 'It\'s easy. Click within dotted area.', 'wp-user-avatar-pro' ),
				'upload_image' => esc_html__( 'Upload Image', 'wp-user-avatar-pro' ),
				'media_uploader' => esc_html__( 'Media Uploader', 'wp-user-avatar-pro' ),
				'drop_instruction' => esc_html__( 'Drop image here or click to upload.', 'wp-user-avatar-pro' ),
				'control_instruction' => esc_html__( 'Select your image, crop and save it.', 'wp-user-avatar-pro' ),

				'close' => esc_html__( 'Close', 'wp-user-avatar-pro' ),
				'save' => esc_html__( 'Save Image', 'wp-user-avatar-pro' ),
				'capture_image' => esc_html__( 'Capture Image', 'wp-user-avatar-pro' ),
				'webcam_on' => esc_html__( 'Use Camera', 'wp-user-avatar-pro' ),
				'mediamanager_on_title' => esc_html__( 'Media Manager is turned ON.', 'wp-user-avatar-pro' ),
				'mediamanager_on_instruction' => esc_html__( 'Click within dotted area to choose image from media manager', 'wp-user-avatar-pro' ),
			);
			if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
				wp_localize_script(
					'wp-user-avatar-imgloader', 'wpua_imgloader_vars', array(
						'url' => admin_url( 'admin-ajax.php', 'https' ),
						'WPUA_URL' => WPUAP_URL,
						'WPUA_MEDIA' => $display_mediamanager,
						'wpua_webcam' => $display_webcam,
						'wpua_string' => $string,
						'restriction' => $restriction,
					)
				);
			} else {
				wp_localize_script(
					'wp-user-avatar-imgloader', 'wpua_imgloader_vars', array(
						'url' => admin_url( 'admin-ajax.php' ),
						'WPUA_URL' => WPUAP_URL,
						'WPUA_MEDIA' => $display_mediamanager,
						'wpua_webcam' => $display_webcam,
						'wpua_string' => $string,
						'restriction' => $restriction,
					)
				);
			}
		}
			/**
			 * Load plugin language file.
			 */
		function wpua_load_plugin_languages() {
			load_plugin_textdomain( 'wp-user-avatar-pro', false, basename( dirname( __FILE__ ) ) . '/lang/' );
		}
			/**
			 * Call hook on plugin activation for both multi-site and single-site.
			 */
		function wpua_plugin_activation( $network_wide ) {

			if ( is_multisite() && $network_wide ) {
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated = array();
				$sql = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids = $wpdb->get_col( $wpdb->prepare( $sql, null ) );
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->wpuap_activation();
					$activated[] = $blog_id;
				}
				switch_to_blog( $currentblog );
				update_site_option( 'op_activated', $activated );
			} else {
				$this->wpuap_activation();
			}
		}
			/**
			 * Call hook on plugin deactivation for both multi-site and single-site.
			 */
		function wpua_plugin_deactivation( $network_wide ) {

			if ( is_multisite() && $network_wide ) {
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated = array();
				$sql = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids = $wpdb->get_col( $wpdb->prepare( $sql, null ) );
				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->wpuap_deactivation();
					$activated[] = $blog_id;
				}
				switch_to_blog( $currentblog );
				update_site_option( 'op_activated', $activated );
			} else {
				$this->wpuap_deactivation();
			}
		}
			/**
			 * Perform tasks on plugin deactivation.
			 */
		function wpuap_deactivation() {

			$avatar_default = get_option( 'avatar_default' );
			update_option( 'wpuap_last_avatar_default', $avatar_default );
			if ( $avatar_default == 'wp_user_avatar' ) {
				update_option( 'avatar_default', 'mystery' );
			}

		}

			/**
			 * Perform tasks on plugin deactivation.
			 */
		function wpuap_activation() {

			global $wpdb;
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			$modules = $this->modules;
			$pagehooks = array();
			if ( is_array( $modules ) ) {
				foreach ( $modules as $module ) {
					$object = new $module();
					if ( method_exists( $object, 'install' ) ) {
							$tables[] = $object->install();
					}
				}
			}
			if ( is_array( $tables ) ) {
				foreach ( $tables as $i => $sql ) {
					dbDelta( $sql );
				}
			}

			if ( $avatar_default = get_option( 'wpuap_last_avatar_default' ) ) {
				update_option( 'avatar_default', $avatar_default );
			}
		}

			/**
			 * Define all constants.
			 */
		private function _define_constants() {

			global $wpdb;
			if ( ! defined( 'WPUAP_SLUG' ) ) {
				define( 'WPUAP_SLUG', 'wpuap_view_overview' );
			}
			if ( ! defined( 'WPUAP_VERSION' ) ) {
				define( 'WPUAP_VERSION', '5.0.0' );
			}
			if ( ! defined( 'WPUAP_FOLDER' ) ) {
				define( 'WPUAP_FOLDER', basename( dirname( __FILE__ ) ) );
			}
			if ( ! defined( 'WPUAP_DIR' ) ) {
				define( 'WPUAP_DIR', plugin_dir_path( __FILE__ ) );
			}
			if ( ! defined( 'WPUAP_ICONS_DIR' ) ) {
				define( 'WPUAP_ICONS_DIR', WPUAP_DIR . '/assets/images/icons/' );
			}
			if ( ! defined( 'WPUAP_CORE_CLASSES' ) ) {
				define( 'WPUAP_CORE_CLASSES', WPUAP_DIR . 'core/' );
			}
			if ( ! defined( 'WPUAP_CONTROLLER' ) ) {
				define( 'WPUAP_CONTROLLER', WPUAP_CORE_CLASSES );
			}
			if ( ! defined( 'WPUAP_PLUGIN_CLASSES' ) ) {
				define( 'WPUAP_PLUGIN_CLASSES', WPUAP_DIR . 'classes/' );
			}
			if ( ! defined( 'WPUAP_CORE_CONTROLLER_CLASS' ) ) {
				define( 'WPUAP_CORE_CONTROLLER_CLASS', WPUAP_CORE_CLASSES . 'class.controller.php' );
			}
			if ( ! defined( 'WPUAP_MODEL' ) ) {
				define( 'WPUAP_MODEL', WPUAP_DIR . 'modules/' );
			}
			if ( ! defined( 'WPUAP_URL' ) ) {
				define( 'WPUAP_URL', plugin_dir_url( WPUAP_FOLDER ) . WPUAP_FOLDER . '/' );
			}
			if ( ! defined( 'FC_CORE_URL' ) ) {
				define( 'FC_CORE_URL', plugin_dir_url( WPUAP_FOLDER ) . WPUAP_FOLDER . '/core/' );
			}
			if ( ! defined( 'WPUAP_CSS' ) ) {
				define( 'WPUAP_CSS', WPUAP_URL . 'assets/css/' );
			}
			if ( ! defined( 'WPUAP_JS' ) ) {
				define( 'WPUAP_JS', WPUAP_URL . 'assets/js/' );
			}
			if ( ! defined( 'WPUAP_IMAGES' ) ) {
				define( 'WPUAP_IMAGES', WPUAP_URL . 'assets/images/' );
			}
			if ( ! defined( 'WPUAP_FONTS' ) ) {
				define( 'WPUAP_FONTS', WPUAP_URL . 'fonts/' );
			}
			if ( ! defined( 'WPUAP_ICONS' ) ) {
				define( 'WPUAP_ICONS', WPUAP_URL . 'assets/images/icons/' );
			}
			$upload_dir = wp_upload_dir();
			if ( ! defined( 'WPUAP_BACKUP' ) ) {
				if ( ! is_dir( $upload_dir['basedir'] . '/wpua-backup' ) ) {
					wp_mkdir_p( $upload_dir['basedir'] . '/wpua-backup' );
				}
				define( 'WPUAP_BACKUP', $upload_dir['basedir'] . '/wpua-backup/' );
				define( 'WPUAP_BACKUP_URL', $upload_dir['baseurl'] . '/wpua-backup/' );
			}
		}
			/**
			 * Load all required core classes.
			 */
		private function _load_files() {

			$coreInitialisationFile = plugin_dir_path( __FILE__ ) . 'core/class.initiate-core.php';
			if ( file_exists( $coreInitialisationFile ) ) {
				require_once( $coreInitialisationFile );
			}

			// Load Plugin Files
			$plugin_files_to_include = array(
				'wpua-form.php',
				'wpuap-controller.php',
				'wpuap-model.php',
				'wpua-functions.php',
				'class-abstract-wpua-storage.php',
				'class-wp-user-avatar-storage.php',
				'class-wp-user-avatar-functions.php',
				'class-avatar.php',
				'class-imgloader.php',
				'class-wp-user-avatar-widget.php',
				'class-wp-user-avatar-plugins.php',
			);

			foreach ( $plugin_files_to_include as $file ) {
				if ( file_exists( WPUAP_PLUGIN_CLASSES . $file ) ) {
					require_once( WPUAP_PLUGIN_CLASSES . $file );
				}
			}

			// Load all modules.
			$core_modules = array( 'overview', 'settings' );
			if ( is_array( $core_modules ) ) {
				foreach ( $core_modules as $module ) {
					$file = WPUAP_MODEL . $module . '/model.' . $module . '.php';
					if ( file_exists( $file ) ) {
						include_once( $file );
						$class_name = 'WPUAP_Model_' . ucwords( $module );
						array_push( $this->modules, $class_name );
					}
				}
			}
			if ( ! is_admin() ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
				require_once( ABSPATH . 'wp-admin/includes/template.php' );
			}
				require_once( ABSPATH . 'wp-admin/includes/screen.php' );
		}
	}
}
new WP_User_Avatar_Pro();

<?php
/**
 * Class: WPUAP_Model_Settings
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 5.0.0
 * @package User Avatar Pro
 */
if ( ! class_exists( 'WPUAP_Model_Settings' ) ) {
	/**
	 * Setting model for Plugin Options.
	 *
	 * @package Avatar
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPUAP_Model_Settings extends FlipperCode_Model_Base {
		/**
		 * Intialize Backup object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Settings Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpuap_view_overview' => esc_html__( 'WP User Avatar', 'wp-user-avatar-pro' ),
				'wpuap_manage_settings' => esc_html__( 'Plugin Settings', 'wp-user-avatar-pro' ),
			);
		}
		/**
		 * Add or Edit Operation.
		 */
		function save() {

			$response['success'] = esc_html__( 'Setting(s) saved successfully.', 'wp-user-avatar-pro' );
			return $response;
		}

		function install() {

			$defaults = array(
				'wp_user_avatar_hide_webcam' => 0,
				'wp_user_avatar_hide_mediamanager' => 0,
				'avatar_storage_option' => 'media',
				'wp_user_avatar_upload_size_limit' => 8388608,
				'wp_user_avatar_upload_registration' => 1,
			);
			foreach ( $defaults as $key => $value ) {
				if ( get_option( $key, false ) === false ) {
					update_option( $key, $value );
				}
			}
		}
	}
}

<?php
/**************************************************
 * Main plugin class
 *
 * @author Flipper Code <hello@flippercode.com>
 * @package Core
 * Author URL : http://www.flippercode.com/
 *****************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

if ( ! class_exists( 'FC_Plugin_Base' ) ) {

	/**
	 * Main plugin class
	 *
	 * @author Flipper Code <hello@flippercode.com>
	 * @package Core
	 */
	class FC_Plugin_Base {

		/**
		 * List of Plugin Base Class Vars.
		 */
		private $productData = array();
		private $modules = array();
		private $dboptions;
		private $pluginPrefix;
		private $childFileRefrence;
		private $selfFileRefrence;
		private $pluginsetting;
		private $controller;
		private $model;
		private $pluginLabel;
		private $pluginTextDomain;
		private $pluginURL;
		private $pluginDirectory;
		private $pluginClasses;
		private $pluginmodules;
		private $pluginmodulesprefix;
		private $pluginCssFilesFrontEnd;
		private $pluginCssFilesBackEnd;
		private $pluginJsFilesFrontEnd;
		private $pluginJsFilesBackEnd;
		private $registered_shortcodes;
		private $registered_shortcodes_resources = array();
		protected $shortcodeDefaults = array();
		protected $isPremium = true;
		private $fcpluginPage = false;
		private $coreRef;
		private $loadCustomizer = false;
		private $productInfo;
		protected $pluginDirectoryBaseName;

		/**
		 * Intialize Class Cariables, Register Common Hooks For Plugin.
		 */
		public function __construct( $pluginData ) {

			if ( method_exists( $this, '_define_constants' ) ) {
				$this->_define_constants();
			}
			$this->productData = $pluginData;
			$this->initialise_plugin();
			$this->_load_files();
			$this->register_default_hooks();

		}

		public function initialise_plugin() {

			$this->_set_up_plugin();
			$this->dboptions = get_option( $this->dboptions );
			if ( ! is_array( $this->dboptions ) ) {
				$this->dboptions = unserialize( $this->dboptions );
			}

		}

		/**
		 * Setup Plugin Definition
		 */
		function _set_up_plugin() {

			foreach ( $this->productData as $property => $propertyValue ) {
				if ( property_exists( $this, $property ) ) {
					$this->$property = $propertyValue;
				}
			}

		}

		public function register_default_hooks() {

			add_action( 'wp_enqueue_scripts', array( $this, 'load_plugin_frontend_resources' ) );
			register_activation_hook( $this->childFileRefrence, array( $this, 'plugin_activation' ) );
			register_deactivation_hook( $this->childFileRefrence, array( $this, 'plugin_deactivation' ) );
			add_action( 'plugins_loaded', array( $this, 'load_plugin_languages' ) );

			if ( ! empty( $this->registered_shortcodes ) ) {
				$this->register_custom_shortcodes();
			}

			add_action( 'wp_enqueue_scripts', array( $this, 'hook_in_header' ) );
			add_action( 'wp_footer', array( $this, 'hook_in_footer' ) );
			add_action( 'wp_ajax_fc_ajax_call', array( $this, 'fc_ajax_call' ) );
			add_action( 'wp_ajax_nopriv_fc_ajax_call', array( $this, 'fc_ajax_call' ) );
			add_action( 'wp_ajax_core_frontend_ajax_calls', array( $this, 'core_frontend_ajax_calls' ) );
			add_action( 'wp_ajax_core_backend_ajax_calls', array( $this, 'core_backend_ajax_calls' ) );

			if ( is_admin() ) {
				add_action( 'admin_menu', array( $this, 'create_menu' ) );
				add_action( 'admin_init', array( $this, 'process_backend_request' ) );
				add_action( 'admin_head', array( $this, 'remove_unwanted_notifications' ) );
			}

		}

		/**
		 * Ajax Call
		 */
		function fc_ajax_call() {

			// We have done security(Nonce) verification & permission check @ operation method instead of here.
			$operation = sanitize_text_field( wp_unslash( $_POST['operation'] ) );
			$value = wp_unslash( $_POST );
			if ( isset( $operation ) ) {
				$this->$operation( $value );
			}
			exit;
		}

		function core_frontend_ajax_calls() {

			// We have done security(Nonce) verification & permission check @ operation method instead of here.
			$operation = sanitize_text_field( wp_unslash( $_POST['operation'] ) );
			$data = wp_unslash( $_POST );
			$this->$operation( $data );
			$response = array( 'updated' => $data );
			echo json_encode( $response );
			exit;

		}

		function core_backend_ajax_calls() {

			// We have done security(Nonce) verification & permission check @ operation method instead of here.
			$operation = sanitize_text_field( wp_unslash( $_POST['operation'] ) );
			$data = wp_unslash( $_POST );
			$response = array();
			if ( isset( $operation ) ) {
				$response = $this->$operation( $data );
				$response = array( 'updated' => $response );
			}
			echo json_encode( $response );
			exit;

		}

		function remove_unwanted_notifications() {

			if ( $this->fcpluginPage ) {
				?>	
				<style>
				.update-nag {display:none;}
				.no-js #loader { display: none;  }
				.js #loader { display: block; position: absolute; left: 100px; top: 0; }
				
				</style>
				<?php
			}
			?>
			<style>
			.se-pre-con {
					display:none;
					position: fixed;
					left: 0px;
					top: 0px;
					width: 100%;
					height: 100%;
					z-index: 999999;
					background: url(<?php echo esc_url( $this->pluginURL . 'assets/images/Preloader_3.gif' ); ?>) center no-repeat #fff; 
				}
			</style> 
			<?php
		}

		function process_backend_request() {

			if ( isset( $_GET['page'] ) && ! empty( sanitize_text_field( $_GET['page'] ) ) && ( strpos( sanitize_text_field( $_GET['page'] ), $this->pluginPrefix ) !== false ) ) {
				$this->fcpluginPage = true;
			}

		}

		function hook_in_header() {

			if ( ! empty( $this->registered_shortcodes ) ) {
				$this->load_shorcode_static_header_resources();
			}
			$this->load_shorcode_dynamic_header_resources();
		}

		function hook_in_footer() {

			if ( ! empty( $this->registered_shortcodes ) ) {
				$this->load_shorcode_static_footer_resources();
			}
			$this->load_shorcode_dynamic_footer_resources();
		}

		function load_shorcode_dynamic_header_resources() {

			global $post;

			if ( ! empty( $this->registered_shortcodes ) ) {

				foreach ( $this->registered_shortcodes as $shortCode ) {

					$pattern = get_shortcode_regex();
					if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches )
					and array_key_exists( 2, $matches )
					and in_array( $shortCode['shortcode'], $matches[2] ) ) {
						$currentShortcodeDynamicParameters = $matches[3];
						foreach ( $currentShortcodeDynamicParameters as $dynamicParameter ) {

							if ( strpos( $dynamicParameter, $shortCode['dynamicparameter'] ) !== false ) {

								$atts = shortcode_parse_atts( $dynamicParameter );
								$dynamicParameterValue = $atts[ $shortCode['dynamicparameter'] ];
								$resourceTypes = array( 'css' );

								foreach ( $resourceTypes as $resourceType ) { // Passed in shortcode params.

									$filePath = $this->pluginDirectory . $shortCode['resourcePath'] . $resourceType . '/' . $dynamicParameterValue . '.' . $resourceType;

									$fileURL = $this->pluginURL . $shortCode['resourcePath'] . $resourceType . '/' . $dynamicParameterValue . '.' . $resourceType;

									$fileExist = ( file_exists( $filePath ) ) ? true : false;
									$previouslyNotLoaded = ( ! in_array( $fileURL, $this->registered_shortcodes_resources ) ) ? true : false;

									if ( $fileExist and $previouslyNotLoaded ) {

										if ( $resourceType == 'css' ) {
											wp_enqueue_style( 'style-dynamnic-' . $dynamicParameterValue, $fileURL );
										} else {
											wp_enqueue_script( 'script-dynamnic-' . $dynamicParameterValue, $fileURL );
										}
										$this->registered_shortcodes_resources[] = $fileURL;
									}
								}
							}
						}
					}
				}
			}
		}

		function load_shorcode_dynamic_footer_resources() {

			global $post;

			if ( ! empty( $this->registered_shortcodes ) ) {

				foreach ( $this->registered_shortcodes as $shortCode ) {

						$pattern = get_shortcode_regex();
					if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches )
						and array_key_exists( 2, $matches )
						and in_array( $shortCode['shortcode'], $matches[2] ) ) {
						$currentShortcodeDynamicParameters = $matches[3];
						foreach ( $currentShortcodeDynamicParameters as $dynamicParameter ) {

							if ( strpos( $dynamicParameter, $shortCode['dynamicparameter'] ) !== false ) {

								$atts = shortcode_parse_atts( $dynamicParameter );
								$dynamicParameterValue = $atts[ $shortCode['dynamicparameter'] ];
								$resourceTypes = array( 'js' );

								foreach ( $resourceTypes as $resourceType ) { // Passed in shortcode params.

									   $filePath = $this->pluginDirectory . $shortCode['resourcePath'] . $resourceType . '/' . $dynamicParameterValue . '.' . $resourceType;

									   $fileURL = $this->pluginURL . $shortCode['resourcePath'] . $resourceType . '/' . $dynamicParameterValue . '.' . $resourceType;

									   $fileExist = ( file_exists( $filePath ) ) ? true : false;
									   $previouslyNotLoaded = ( ! in_array( $fileURL, $this->registered_shortcodes_resources ) ) ? true : false;

									if ( $fileExist and $previouslyNotLoaded ) {

										if ( $resourceType == 'js' ) {
											wp_enqueue_script( 'script-dynamnic-' . $dynamicParameterValue, $fileURL );
										}
										 $this->registered_shortcodes_resources[] = $fileURL;
									}
								}
							}
						}
					}
				}
			}
		}

		function register_custom_shortcodes() {

			foreach ( $this->registered_shortcodes as $shortcode ) {

				add_shortcode( $shortcode['shortcode'], array( $this, $shortcode['callback'] ) );
			}

		}

		/*
		 * Function For Auto Loading Plugin's Current Template Resources @ Frontend.
		*/
		function load_current_template_style() {

			if ( ! isset( $this->dboptions['default_templates'] ) || empty( $this->dboptions['default_templates'] ) ) {
				return;
			}

			$default_templates = $this->dboptions['default_templates'];
			if ( $default_templates ) {

				foreach ( $default_templates as $key => $template ) {

					$cssFile = 'templates/' . $key . '/' . $template . '/' . $template . '.css';
					$templateCSSFilepath = $this->pluginDirectory . $cssFile;
					$templateCSSFile = $this->pluginURL . $cssFile;
					if ( ! file_exists( $templateCSSFilepath ) ) {
						$uploads = wp_upload_dir();
						$templateCSSFilepath = $uploads['basedir'] . '/' . $this->pluginPrefix . '/' . $key . '/' . $template . '/' . $template . '.css';
						$templateCSSFile = $uploads['baseurl'] . '/' . $this->pluginPrefix . '/' . $key . '/' . $template . '/' . $template . '.css';
					}

					if ( file_exists( $templateCSSFilepath ) ) {
						wp_enqueue_style( $template . 'current-template-css', $templateCSSFile );
					}

					$jsFile = 'templates/' . $key . '/' . $template . '/' . $template . '.js';
					$templateJsFilepath = $this->pluginDirectory . $jsFile;
					$templateJsFile = $this->pluginURL . $jsFile;
					if ( ! file_exists( $templateJsFilepath ) ) {
						$uploads = wp_upload_dir();
						$templateJsFilepath = $uploads['basedir'] . '/' . $this->pluginPrefix . '/' . $key . '/' . $template . '/' . $template . '.js';
						$templateCSSFile = $uploads['baseurl'] . '/' . $this->pluginPrefix . '/' . $key . '/' . $template . '/' . $template . '.js';
					}

					if ( file_exists( $templateJsFilepath ) ) {
						wp_enqueue_script( $template . 'current-template-js', $templateJsFile );
					}
				}
			}

		}

		public function load_shorcode_static_footer_resources() {

			wp_enqueue_script( 'jquery' );

			global $post, $wpdb;

			$shortcode_found = false;

			foreach ( $this->registered_shortcodes as $shortcode ) {

				$hasShortcode = has_shortcode( $post->post_content, $shortcode['shortcode'] );
				if ( $hasShortcode ) {

					foreach ( $shortcode['resources']['js'] as $key => $shortcodeJs ) {
						$resource = $this->pluginURL . $shortcodeJs;
						$fileExist = ( file_exists( $this->pluginDirectory . $shortcodeJs ) ) ? true : false;
						$previouslyNotLoaded = ( ! in_array( $resource, $this->registered_shortcodes_resources ) ) ? true : false;

						if ( $fileExist and $previouslyNotLoaded ) {
							 wp_enqueue_script( 'script-' . $shortcodeJs, $resource );
							 $this->registered_shortcodes_resources[] = $resource;
						}
					}
				}
			}

		}

		public function load_shorcode_static_header_resources() {

			global $post, $wpdb;

			$shortcode_found = false;

			foreach ( $this->registered_shortcodes as $shortcode ) {

				$hasShortcode = has_shortcode( $post->post_content, $shortcode['shortcode'] );
				if ( $hasShortcode ) {

					foreach ( $shortcode['resources']['css'] as $key => $shortcodeCSS ) {

						$resource = $this->pluginURL . $shortcodeCSS;
						$fileExist = ( file_exists( $this->pluginDirectory . $shortcodeCSS ) ) ? true : false;
						$previouslyNotLoaded = ( ! in_array( $resource, $this->registered_shortcodes_resources ) ) ? true : false;

						if ( $fileExist and $previouslyNotLoaded ) {

							 wp_enqueue_style( 'style-' . $shortcodeCSS, $resource );
							 $this->registered_shortcodes_resources[] = $resource;
						}
					}
				}
			}

		}

		/**
		 * Eneque scripts at frontend.
		 */
		function load_plugin_frontend_resources() {

			if ( $this->pluginCssFilesFrontEnd ) {
				foreach ( $this->pluginCssFilesFrontEnd as $frontendCSS ) {
					wp_enqueue_style( $frontendCSS, $this->pluginURL . 'assets/css/' . $frontendCSS );
				}
			}

			$this->load_current_template_style();

			$scripts = array();
			wp_enqueue_script( 'jquery' );

			foreach ( $this->pluginJsFilesFrontEnd as $js ) {
				$scripts[] = array(
					'handle'  => $js,
					'src'   => $this->pluginURL . 'assets/js/' . $js,
					'deps'    => array(),
				);

			}

			$where = apply_filters( $this->pluginPrefix . '_script_position', true );
			if ( $scripts ) {
				foreach ( $scripts as $script ) {
					wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], '', $where );
				}
			}

			if ( method_exists( $this, 'frontend_script_localisation' ) ) {
				 $this->frontend_script_localisation();
			}

		}

		/**
		 * Process slug and display view in the backend.
		 */
		function processor() {

			$return = '';
			if ( isset( $_GET['page'] ) ) {
				$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			} else {
				$page = $this->pluginPrefix . '_view_overview';
			}

			$pageData = explode( '_', $page );

			if ( $this->pluginPrefix != strtolower( $pageData[0] ) ) {
				return;
			}
			$obj_type = $pageData[2];
			$obj_operation = $pageData[1];

			if ( count( $pageData ) < 3 ) {
				die( 'Cheating!' );
			}

			try {

				if ( count( $pageData ) > 3 ) {
					$obj_type = $pageData[2] . '_' . $pageData[3];
				}

				if ( class_exists( $this->controller ) ) {

					$factoryObject = new $this->controller();
					$viewObject = $factoryObject->create_object( $obj_type );
					$viewObject->display( $obj_operation );
				}
			} catch ( Exception $e ) {
				echo FlipperCode_HTML_Markup::show_message( array( 'error' => $e->getMessage() ) );

			}

		}

		/**
		 * Create backend navigation.
		 */
		function create_menu() {

			global $navigations;

			if ( method_exists( $this, 'define_admin_menu' ) ) {
				$pluginBackendPageHook = $this->define_admin_menu();
			}

			if ( current_user_can( 'manage_options' ) ) {
				$role = get_role( 'administrator' );
				$role->add_cap( $this->pluginPrefix . '_admin_overview' );
			}

			$this->load_modules_menu();
			add_action( 'load-' . $pluginBackendPageHook, array( $this, 'load_plugin_backend_resources' ) );

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
								$this->pluginPrefix . '_view_overview',
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
					add_action( 'load-' . $pagehooks [ $key ], array( $this, 'load_plugin_backend_resources' ) );
				}
			}

		}

		/**
		 * Eneque scripts in the backend.
		 */
		function load_plugin_backend_resources() {

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'thickbox' );
			$wp_scripts = array( 'jQuery', 'thickbox', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-slider' );

			if ( $wp_scripts ) {
				foreach ( $wp_scripts as $wp_script ) {
					wp_enqueue_script( $wp_script );
				}
			}

			wp_register_script( 'flippercode-ui.js', $this->pluginURL . 'assets/js/flippercode-ui.js' );
			$core_script_args = apply_filters(
				'fc_ui_script_args', array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'language' => 'en',
					'urlforajax' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'hide' => 'Hide',
					'nonce' => wp_create_nonce( 'fc_communication' ),
				)
			);
			wp_localize_script( 'flippercode-ui.js', 'fc_ui_obj', $core_script_args );
			wp_enqueue_script( 'flippercode-ui.js' );

			$scripts = array();
			foreach ( $this->pluginJsFilesBackEnd as $js ) {

				$scripts[] = array(
					'handle'  => $js,
					'src'   => $this->pluginURL . 'assets/js/' . $js,
					'deps'    => array(),
				);

			}
			if ( $scripts ) {
				foreach ( $scripts as $script ) {
					wp_enqueue_script( $script['handle'], $script['src'], $script['deps'] );
				}
			}

			if ( method_exists( $this, 'backend_script_localisation' ) ) {
				$this->backend_script_localisation();
			}

			wp_enqueue_style( 'fc_ui-backend', $this->pluginURL . 'assets/css/flippercode-ui.css' );
			wp_enqueue_style( 'font_awesome_minimised', $this->pluginURL . 'assets/css/font-awesome.min.css' );
			if ( $this->pluginCssFilesBackEnd ) {
				foreach ( $this->pluginCssFilesBackEnd as $backendCSS ) {
					wp_enqueue_style( $backendCSS . '-backend', $this->pluginURL . 'assets/css/' . $backendCSS );
				}
			}

		}

		/**
		 * Load plugin language file.
		 */
		function load_plugin_languages() {

			$this->modules = apply_filters( $this->pluginPrefix . '_extensions', $this->modules );
			load_plugin_textdomain( $this->pluginTextDomain, false, $this->pluginDirectoryBaseName . '/lang/' );
		}

		/**
		 * Call hook on plugin activation for both multi-site and single-site.
		 *
		 * @param  boolean $network_wide IS network activated?.
		 */
		function plugin_activation( $network_wide = null ) {

			if ( is_multisite() && $network_wide ) {
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated = array();
				$sql = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids = $wpdb->get_col( $wpdb->prepare( $sql, null ) );

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					if ( method_exists( $this, 'on_plugin_activation' ) ) {
						$this->on_plugin_activation();
					}
				}

				switch_to_blog( $currentblog );
				update_site_option( $this->pluginPrefix . '_activated', $activated );

			} else {

				if ( method_exists( $this, 'on_plugin_activation' ) ) {
					$this->on_plugin_activation();
				}
			}
		}
		/**
		 * Call hook on plugin deactivation for both multi-site and single-site.
		 *
		 * @param  boolean $network_wide IS network activated?.
		 */
		function plugin_deactivation( $network_wide ) {

			if ( is_multisite() && $network_wide ) {
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated = array();
				$sql = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids = $wpdb->get_col( $wpdb->prepare( $sql, null ) );

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					if ( method_exists( $this, 'on_plugin_deactivation' ) ) {
						$this->on_plugin_deactivation();
						$activated[] = $blog_id;
					}
				}

				switch_to_blog( $currentblog );
				update_site_option( $this->pluginPrefix . '_activated', $activated );

			} else {

				if ( method_exists( $this, 'on_plugin_deactivation' ) ) {
					$this->on_plugin_deactivation();
				}
			}
		}

		/**
		 * Perform tasks on plugin deactivation.
		 */
		function on_plugin_deactivation() {

			if ( method_exists( $this, 'plugin_deactivation_work' ) ) {
				 $this->plugin_deactivation_work();
			}

		}

		/**
		 * Perform tasks on plugin deactivation.
		 */
		function on_plugin_activation() {

			global $wpdb;

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$modules = $this->modules;
			$pagehooks = array();
			$tables = array();
			if ( is_array( $modules ) ) {
				foreach ( $modules as $module ) {
					$object = new $module();
					if ( method_exists( $object, 'install' ) ) {
						$tables[] = $object->install();
					}
				}
			}

			if ( is_array( $tables ) && ! empty( $tables ) ) {
				foreach ( $tables as $i => $sql ) {
					dbDelta( $sql );
				}
			}

			/*
			 * Setup Default Values On Initialisation
			 */
			if ( method_exists( $this, 'plugin_activation_work' ) ) {
				 $this->plugin_activation_work();
			}

		}

		function is_operation_allowed() {

			if ( ! wp_verify_nonce( $_POST['nonce'], 'fc_communication' ) || ! current_user_can( 'administrator' ) ) {
				return false;
			}
			return true;

		}

		function set_default_template() {

			if ( ! $this->is_operation_allowed() ) {
				return;
			}

			$response = array();
			$optionName = sanitize_text_field( $_POST['product'] );
			$data = get_option( $optionName );
			if ( ! is_array( $data ) ) {
				$data = unserialize( $data );
			}
			$templates = $data['default_templates'];
			unset( $data['default_templates'] );
			$templates[ sanitize_text_field( $_POST['templatetype'] ) ] = sanitize_text_field( $_POST['template'] );
			$data['default_templates'] = $templates;
			update_option( $optionName, $data );
			return $data;

		}

		function delete_custom_template() {

			if ( ! $this->is_operation_allowed() ) {
				return;
			}

			// Recursively delete folders and files of user's custom template.
			$upload_dir = wp_upload_dir();
			$base_dir = $upload_dir['basedir'];
			$template_path = $base_dir . '/' . sanitize_text_field( $_POST['instance'] ) . '/' . sanitize_text_field( $_POST['templatetype'] ) . '/' . sanitize_text_field( $_POST['templateName'] );
			$this->rrmdir( $template_path );
			$response = array( 'status' => 'Template was deleted successfully.' );
			return $response;

		}

		function rrmdir( $dir ) {

			if ( is_dir( $dir ) ) {
				$objects = scandir( $dir );
				foreach ( $objects as $object ) {
					if ( $object != '.' && $object != '..' ) {
						if ( is_dir( $dir . '/' . $object ) ) {
							$this->rrmdir( $dir . '/' . $object );
						} else {
							unlink( $dir . '/' . $object );
						}
					}
				}
				rmdir( $dir );
			}

		}

		/**
		 * Load all required core classes.
		 */
		private function _load_files() {

			$coreInitialisationFile = $this->pluginDirectory . 'core/class.initiate-core.php';
			if ( file_exists( $coreInitialisationFile ) ) {
				require_once( $coreInitialisationFile );
			}

			if ( is_array( $this->pluginClasses ) ) {

				foreach ( $this->pluginClasses as $file ) {
					$classFile = $this->pluginDirectory . '/classes/' . $file;
					if ( file_exists( $classFile ) ) {
						require_once( $classFile );
					}
				}
			}

			if ( is_array( $this->pluginmodules ) ) {
				foreach ( $this->pluginmodules as $module ) {
					$file = $this->pluginDirectory . '/modules/' . $module . '/model.' . $module . '.php';
					if ( file_exists( $file ) ) {
						include_once( $file );
						$class_name = $this->pluginmodulesprefix . ucwords( $module );
						array_push( $this->modules, $class_name );
					}
				}
			}

		}

	}
}

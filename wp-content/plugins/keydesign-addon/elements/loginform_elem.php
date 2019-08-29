<?php

if (!class_exists('KD_ELEM_LOGIN_FORM')) {

    class KD_ELEM_LOGIN_FORM extends KEYDESIGN_ADDON_CLASS {

        function __construct() {
            add_action('init', array($this, 'kd_loginform_init'));
            add_shortcode('tek_loginform', array($this, 'kd_loginform_shrt'));
        }

        // Element configuration in admin

        function kd_loginform_init() {
            if (function_exists('vc_map')) {
                vc_map(array(
                    "name" => esc_html__("Login form", "keydesign"),
                    "description" => esc_html__("Simple login form for use anywhere within WordPress.", "keydesign"),
                    "base" => "tek_loginform",
                    "class" => "",
                    "icon" => plugins_url('assets/element_icons/login-form.png', dirname(__FILE__)),
                    "category" => esc_html__("KeyDesign Elements", "keydesign"),
                    "show_settings_on_create" => false,
                    "params" => array(
                      array(
                        "type" => "kd_param_notice",
                        "text" => "<span style='display: block;'>This shortcode displays a login form. No additional configuration needed.</span>",
                        "param_name" => "notification",
                        "edit_field_class" => "vc_column vc_col-sm-12",
                      ),
                    )
                ));
            }
        }

		// Render the element on front-end

        public function kd_loginform_shrt($atts, $content = null)
        {
            return wp_login_form( array( 'echo' => false ) );
        }
    }
}

if (class_exists('KD_ELEM_LOGIN_FORM')) {
    $KD_ELEM_LOGIN_FORM = new KD_ELEM_LOGIN_FORM;
}

?>

<?php

if (!class_exists('KD_ELEM_SOCIAL_ICONS')) {

    class KD_ELEM_SOCIAL_ICONS extends KEYDESIGN_ADDON_CLASS {

        function __construct() {
            add_action('init', array($this, 'kd_socialicons_init'));
            add_shortcode('tek_socialicons', array($this, 'kd_socialicons_shrt'));
        }

        // Element configuration in admin

        function kd_socialicons_init() {
            if (function_exists('vc_map')) {
                vc_map(array(
                    "name" => esc_html__("Social icons", "keydesign"),
                    "description" => esc_html__("Connect your social media profiles to your website.", "keydesign"),
                    "base" => "tek_socialicons",
                    "class" => "",
                    "icon" => plugins_url('assets/element_icons/social-icons.png', dirname(__FILE__)),
                    "category" => esc_html__("KeyDesign Elements", "keydesign"),
                    "params" => array(
                        array(
                          "type" => "kd_param_notice",
                          "text" => '<span style="display: block;">Control which icons are displayed and their urls on the <a href="' . admin_url ( 'admin.php?page=theme-options&tab=0' ) .'">settings page</a>.</span>',
                          "param_name" => "notification",
                          "edit_field_class" => "vc_column vc_col-sm-12",
                        ),
                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Icon alignment","keydesign"),
                            "param_name"	=>	"si_icon_alignment",
                            "value"			=>	array(
                                "Left" => "icon-left-align",
                                "Center" => "icon-center-align",
                                "Right" => "icon-right-align",
                            ),
                            "save_always" => true,
                            "description"	=>	esc_html__("Select icon alignment.", "keydesign"),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Icon font size", "keydesign"),
                            "param_name" => "si_icon_size",
                            "value" => "",
                            "description" => esc_html__("Enter icon font size.", "keydesign")
                        ),
                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html__("CSS Animation", "keydesign"),
                            "param_name" => "css_animation",
                            "value" => array(
                                "No"              => "no_animation",
                                "Fade In"         => "kd-animated fadeIn",
                                "Fade In Down"    => "kd-animated fadeInDown",
                                "Fade In Left"    => "kd-animated fadeInLeft",
                                "Fade In Right"   => "kd-animated fadeInRight",
                                "Fade In Up"      => "kd-animated fadeInUp",
                                "Zoom In"         => "kd-animated zoomIn",
                            ),
                            "description" => esc_html__("Select type of animation for element to be animated when it enters the browsers viewport (Note: works only in modern browsers).", "keydesign"),
                        ),
                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html__("Animation Delay", "keydesign"),
                            "param_name" => "si_animation_delay",
                            "value" => array(
                                "0 ms"              => "",
                                "200 ms"            => "200",
                                "400 ms"            => "400",
                                "600 ms"            => "600",
                                "800 ms"            => "800",
                                "1 s"            => "1000",
                            ),
                            "dependency" =>	array(
                                "element" => "css_animation",
                                "value" => array("kd-animated fadeIn", "kd-animated fadeInDown", "kd-animated fadeInLeft", "kd-animated fadeInRight", "kd-animated fadeInUp", "kd-animated zoomIn")
                            ),
                            "description" => esc_html__("Enter animation delay in ms", "keydesign")
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Extra class name", "keydesign"),
                            "param_name" => "si_extra_class",
                            "value" => "",
                            "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "keydesign")
                        ),
                    )
                ));
            }
        }



    		// Render the element on front-end

        public function kd_socialicons_shrt($atts, $content = null) {
            extract(shortcode_atts(array(
                'si_icon_alignment' => '',
                'si_icon_size' => '',
                'css_animation' => '',
                'si_animation_delay' => '',
                'si_extra_class' => '',
            ), $atts));

            $output = $icon_size = '';

            //CSS Animation
            if ($css_animation == "no_animation") {
                $css_animation = "";
            }

            // Animation delay
            if ($si_animation_delay) {
                $animation_delay = 'data-animation-delay='.$si_animation_delay;
            }

            if( class_exists( 'ReduxFramework' )) {
              $output .= '<div class="kd-social-profiles '.$si_icon_alignment.' '.$css_animation.' '.$si_extra_class.'" '.(!empty($si_icon_size) ? 'style="font-size:' . $si_icon_size . ';"' : '').' '.$animation_delay.'>';
              $output .= do_shortcode('[social_profiles]');
              $output .= '</div>';
            }

            return $output;
        }
    }
}

if (class_exists('KD_ELEM_SOCIAL_ICONS')) {
    $KD_ELEM_SOCIAL_ICONS = new KD_ELEM_SOCIAL_ICONS;
}

?>

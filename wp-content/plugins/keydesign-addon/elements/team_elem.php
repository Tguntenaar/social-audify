<?php

if (!class_exists('KD_ELEM_TEAM')) {

    class KD_ELEM_TEAM extends KEYDESIGN_ADDON_CLASS {
        function __construct() {
            add_action('init', array($this, 'kd_team_init'));
            add_shortcode('tek_team', array($this, 'kd_team_shrt'));
        }

        // Element configuration in admin

        function kd_team_init() {
            if (function_exists('vc_map')) {
                vc_map(array(
                    "name" => esc_html__("Team member", "keydesign"),
                    "description" => esc_html__("Team member element", "keydesign"),
                    "base" => "tek_team",
                    "class" => "",
                    "icon" => plugins_url('assets/element_icons/team-member.png', dirname(__FILE__)),
                    "category" => esc_html__("KeyDesign Elements", "keydesign"),
                    "params" => array(
                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html__("Select design style", "keydesign"),
                            "param_name" => "design_style",
                            "value" => array(
                                esc_html__("Minimal","keydesign") => "minimal",
                                esc_html__("Classic","keydesign") => "classic",
                                esc_html__("Creative","keydesign") => "creative",
                            ),
                            "save_always" => true,
                            "description" => esc_html__("Select Team Member box design.", "keydesign")
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Team member name", "keydesign"),
                            "param_name" => "title",
                            "value" => "",
                            "admin_label" => true,
                        ),
                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Name text color", "keydesign"),
                            "param_name" => "title_color",
                            "value" => "",
                            "description" => esc_html__("Choose team member name color. If none selected, the default theme color will be used.", "keydesign"),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Team member job", "keydesign"),
                            "param_name" => "position",
                            "value" => "",
                        ),
                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Job text color", "keydesign"),
                            "param_name" => "position_color",
                            "value" => "",
                            "description" => esc_html__("Choose team member job color. If none selected, the default theme color will be used.", "keydesign"),
                        ),
                        array(
                            "type" => "textarea",
                            "class" => "",
                            "heading" => esc_html__("Team member description", "keydesign"),
                            "param_name" => "description",
                            "value" => "",
                        ),
                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Description text color", "keydesign"),
                            "param_name" => "description_color",
                            "value" => "",
                            "description" => esc_html__("Choose team member description color. If none selected, the default theme color will be used.", "keydesign"),
                        ),
                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html__("Person image source", "keydesign"),
                            "param_name" => "image_source",
                            "value" => array(
                                "Media library" => "media_library",
                                "External link" => "external_link",
                            ),
                            "description" => esc_html__("Select image source.", "keydesign"),
                            "save_always" => true,
                        ),
                        array(
                            "type" => "attach_image",
                            "heading" => esc_html__("Person image", "keydesign"),
                            "param_name" => "image",
                            "description" => esc_html__("Select or upload your image using the media library."),
                            "dependency" =>	array(
                                "element" => "image_source",
                                "value" => array("media_library")
                            ),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Person image external source", "keydesign"),
                            "param_name" => "ext_image",
                            "value" => "",
                            "description" => esc_html__("Enter image external link.", "keydesign"),
                            "dependency" =>	array(
                                "element" => "image_source",
                                "value" => array("external_link")
                            ),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Person image size", "keydesign"),
                            "param_name" => "ext_image_size",
                            "value" => "",
                            "description" => esc_html__("Enter image size in pixels. Example: 400x450 (Width x Height).", "keydesign"),
                            "dependency" =>	array(
                                "element" => "image_source",
                                "value" => array("external_link")
                            ),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Team member phone", "keydesign"),
                            "param_name" => "tm_phone",
                            "value" => "",
                            "dependency" =>	array(
                                "element" => "design_style",
                                "value" => array("classic", "creative")
                            ),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Phone label", "keydesign"),
                            "param_name" => "tm_phone_label",
                            "value" => "",
                            "dependency" =>	array(
                                "element" => "design_style",
                                "value" => array("classic", "creative")
                            ),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Team member email", "keydesign"),
                            "param_name" => "tm_email",
                            "value" => "",
                            "dependency" =>	array(
                                "element" => "design_style",
                                "value" => array("classic", "creative")
                            ),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Email label", "keydesign"),
                            "param_name" => "tm_email_label",
                            "value" => "",
                            "dependency" =>	array(
                                "element" => "design_style",
                                "value" => array("classic", "creative")
                            ),
                        ),
                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Box background color", "keydesign"),
                            "param_name" => "team_bg_color",
                            "value" => "",
                            "description" => esc_html__("Choose box background color. If none selected, the default theme color will be used.", "keydesign"),
                        ),
                        array(
                             "type" => "href",
                             "class" => "",
                             "heading" => esc_html__("Facebook Link", "keydesign"),
                             "param_name" => "facebook_url",
                             "value" => "",
                             "description" => esc_html__("Set Facebook link.", "keydesign"),
                        ),
                        array(
                             "type" => "href",
                             "class" => "",
                             "heading" => esc_html__("Instagram Link", "keydesign"),
                             "param_name" => "instagram_url",
                             "value" => "",
                             "description" => esc_html__("Set Instagram link.", "keydesign"),
                        ),
                        array(
                             "type" => "href",
                             "class" => "",
                             "heading" => esc_html__("Twitter Link", "keydesign"),
                             "param_name" => "twitter_url",
                             "value" => "",
                             "description" => esc_html__("Set Twitter link.", "keydesign"),
                        ),
                        array(
                             "type" => "href",
                             "class" => "",
                             "heading" => esc_html__("Google Link", "keydesign"),
                             "param_name" => "google_url",
                             "value" => "",
                             "description" => esc_html__("Set Google Plus link.", "keydesign"),
                        ),
                        array(
                             "type" => "href",
                             "class" => "",
                             "heading" => esc_html__("Linkedin Link", "keydesign"),
                             "param_name" => "linkedin_url",
                             "value" => "",
                             "description" => esc_html__("Set Linkedin link.", "keydesign"),
                        ),
                        array(
                             "type" => "href",
                             "class" => "",
                             "heading" => esc_html__("Xing Link", "keydesign"),
                             "param_name" => "xing_url",
                             "value" => "",
                             "description" => esc_html__("Set Xing link.", "keydesign"),
                        ),
                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Social icons color", "keydesign"),
                            "param_name" => "social_color",
                            "value" => "",
                            "description" => esc_html__("Choose social icons color. If none selected, the default theme color will be used.", "keydesign"),
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
                            "heading" => esc_html__("Animation Delay:", "keydesign"),
                            "param_name" => "elem_animation_delay",
                            "value" => array(
                                "0 ms"              => "",
                                "200 ms"            => "200",
                                "400 ms"            => "400",
                                "600 ms"            => "600",
                                "800 ms"            => "800",
                                "1 s"               => "1000",
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
                            "param_name" => "team_extra_class",
                            "value" => "",
                            "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "keydesign")
                        ),
                    )
                ));
            }
        }



        // Render the element on front-end

        public function kd_team_shrt($atts, $content = null) {
            $design_style = '';

            extract(shortcode_atts(array(
                'design_style' => '',
            ), $atts));

            $output = '';

            require_once(KEYDESIGN_PLUGIN_PATH.'/elements/templates/team-elem/team-'.$design_style.'.php');
            $template_func = 'kd_team_set_'.$design_style;
      			$output .= $template_func($atts,$content);

            return $output;
        }
    }
}

if (class_exists('KD_ELEM_TEAM')) {
    $KD_ELEM_TEAM = new KD_ELEM_TEAM;
}
?>

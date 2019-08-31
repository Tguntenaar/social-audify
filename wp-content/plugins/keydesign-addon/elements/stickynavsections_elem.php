<?php
if (class_exists('WPBakeryShortCodesContainer')) {
    class WPBakeryShortCode_tek_featuresections extends WPBakeryShortCodesContainer {
    }
}
if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_tek_featuresections_single extends WPBakeryShortCode {
    }
}
if (!class_exists('tek_featuresections')) {
    class tek_featuresections extends KEYDESIGN_ADDON_CLASS
    {
        function __construct() {
            add_action('init', array($this, 'kd_featuresections_init'));
            add_shortcode('tek_featuresections', array($this, 'kd_featuresections_container'));
            add_shortcode('tek_featuresections_single', array($this, 'kd_featuresections_single'));
        }
        // Element configuration in admin
        function kd_featuresections_init() {
            // Container element configuration
            if (function_exists('vc_map')) {
                vc_map(array(
                    "name" => esc_html__("Sticky navbar sections", "keydesign"),
                    "description" => esc_html__("Simple sections with sticky navigation.", "keydesign"),
                    "base" => "tek_featuresections",
                    "class" => "",
                    "show_settings_on_create" => false,
                    "content_element" => true,
                    "as_parent" => array('only' => 'tek_featuresections_single'),
                    "icon" => plugins_url('assets/element_icons/feature-sections.png', dirname(__FILE__)),
                    "category" => esc_html__("KeyDesign Elements", "keydesign"),
                    "js_view" => 'VcColumnView',
                    "params" => array(

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Extra class name", "keydesign"),
                            "param_name" => "fsp_extra_class",
                            "value" => "",
                            "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "keydesign")
                        ),
                    )
                ));
                // Shortcode configuration
                vc_map(array(
                    "name" => esc_html__("Single section", "keydesign"),
                    "base" => "tek_featuresections_single",
                    "content_element" => true,
                    "as_child" => array('only' => 'tek_featuresections'),
                    "icon" => plugins_url('assets/element_icons/feature-sections-child.png', dirname(__FILE__)),
                    "params" => array(
                        array(
                             "type"	=>	"dropdown",
                             "class" =>	"",
                             "heading" => esc_html__("Template", "keydesign"),
                             "param_name" => "fss_section_template",
                             "value" =>	array(
                                    esc_html__( "Single image", "keydesign" )	=> "side_photo",
                                    esc_html__( "Gallery", "keydesign" )	=> "side_gallery",
                                    esc_html__( "Video", "keydesign" )	=> "side_video",
                                ),
                             "save_always" => true,
                             "group" => esc_html__("Content", "keydesign"),
                             "description" => esc_html__("Select the section general style.", "keydesign"),
                        ),

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Title", "keydesign"),
                            "param_name" => "fss_title",
                            "value" => "",
                            "description" => esc_html__("Enter section title.", "keydesign"),
                            "admin_label" => true,
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Short nav label (*required)", "keydesign"),
                            "param_name" => "fss_nav_label",
                            "value" => "",
                            "description" => esc_html__("Enter navigation label for this section.", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Title color", "keydesign"),
                            "param_name" => "fss_title_color",
                            "value" => "",
                            "description" => esc_html__("Select title color. If none selected, the default theme color will be used.", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type" => "textarea",
                            "class" => "",
                            "heading" => esc_html__("Subtitle", "keydesign"),
                            "param_name" => "fss_subtitle",
                            "value" => "",
                            "description" => esc_html__("Enter section subtitle.", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Subtitle color", "keydesign"),
                            "param_name" => "fss_subtitle_color",
                            "value" => "",
                            "description" => esc_html__("Select subtitle color. If none selected, the default theme color will be used.", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Display icon","keydesign"),
                            "param_name"	=>	"fss_icon_type",
                            "value"			=>	array(
                                    "No icon" => "no_icon",
                                    "Icon browser" => "icon_browser",
                                    "Custom icon" => "custom_icon",
                                ),
                            "save_always" => true,
                            "description"	=>	esc_html__("Icon will be placed above the section title.", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

            						array(
              							"type" => "iconpicker",
              							"heading" => esc_html__( "Icon", "keydesign" ),
              							"param_name" => "icon_iconsmind",
                            "settings" => array(
                        				"type" => "iconsmind",
                        				"iconsPerPage" => 50,
                        		),
              							"dependency" => array(
              								"element" => "fss_icon_type",
              								"value" => "icon_browser",
              							),
              							"description" => esc_html__( "Select icon from library.", "keydesign" ),
                            "group" => esc_html__("Content", "keydesign"),
            						),

                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Icon color", "keydesign"),
                            "param_name" => "fss_icon_color",
                            "value" => "",
                            "dependency" =>	array(
                                "element" => "fss_icon_type",
                                "value" => array("icon_browser")
                            ),
                            "description" => esc_html__("Select icon color. If none selected, the default theme color will be used.", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Icon size", "keydesign"),
                            "param_name" => "fss_icon_size",
                            "value" => "",
                            "dependency" =>	array(
                                "element" => "fss_icon_type",
                                "value" => array("icon_browser")
                            ),
                            "description" => esc_html__("Enter icon size. (eg. 10px, 1em, 1rem)", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type" => "attach_image",
                            "class" => "",
                            "heading" => esc_html__("Upload custom icon", "keydesign"),
                            "param_name" => "fss_icon_img",
                            "value" => "",
                            "description" => esc_html__("Upload your own custom image.", "keydesign"),
                            "dependency" => array(
                                "element" => "fss_icon_type",
                                "value" => array("custom_icon"),
                            ),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type" => "textarea_html",
                            "class" => "",
                            "heading" => esc_html__("Content", "keydesign"),
                            "param_name" => "content",
                            "value" => "",
                            "description" => esc_html__("Enter section content text here.", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Content text color", "keydesign"),
                            "param_name" => "fss_content_color",
                            "value" => "",
                            "description" => esc_html__("Select content text color. If none selected, the default theme color will be used.", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                             "type"	=>	"dropdown",
                             "class" =>	"",
                             "heading" => esc_html__("Add link", "keydesign"),
                             "param_name" => "fss_custom_link",
                             "value" =>	array(
                                    esc_html__( "No link", "keydesign" ) => "#",
                                    esc_html__( "Add a custom link", "keydesign" )	=> "1",
                                ),
                             "save_always" => true,
                             "description" => esc_html__("You can add/remove custom link", "keydesign"),
                             "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                             "type"	=>	"vc_link",
                             "class" =>	"",
                             "heading" => esc_html__("Link settings", "keydesign"),
                             "param_name" => "fss_section_link",
                             "value" =>	"",
                             "description" => esc_html__("You can add or remove the existing link from here.", "keydesign"),
                             "dependency" => array(
                                "element" => "fss_custom_link",
                                "value"	=> array( "1" ),
                            ),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html__("Link style", "keydesign"),
                            "param_name" => "fss_link_style",
                            "value" => array(
                                "Solid button" => "solid_button",
                                "Outline button" => "outline_button",
                                "Simple link" => "simple_link",
                            ),
                            "dependency" => array(
                               "element" => "fss_custom_link",
                               "value"	=> array( "1" ),
                            ),
                            "save_always" => true,
                            "description" => esc_html__("Select link style.", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),
                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html__("Button color scheme", "keydesign"),
                            "param_name" => "fss_button_color_scheme",
                            "value" => array(
                                "Primary color" => "btn_primary_color",
                                "Secondary color" => "btn_secondary_color"
                            ),
                            "dependency" => array(
                               "element" => "fss_link_style",
                               "value"	=> array( "solid_button", "outline_button" ),
                            ),
                            "save_always" => true,
                            "description" => esc_html__("Select button predefined color scheme.", "keydesign"),
                            "group" => esc_html__("Content", "keydesign"),
                        ),

                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html__("Image source", "keydesign"),
                            "param_name" => "featured_image_source",
                            "value" => array(
                                "Media library" => "media_library",
                                "External link" => "external_link",
                            ),
                            "description" => esc_html__("Select image source.", "keydesign"),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_photo"),
                            ),
                            "group" => esc_html__("Image settings", "keydesign"),
                        ),

                        array(
                            "type" => "attach_image",
                            "class" => "",
                            "heading" => esc_html__("Featured image", "keydesign"),
                            "param_name" => "fss_featured_image",
                            "value" => "",
                            "description" => esc_html__("Upload your own custom image. Recommended image size: 650x450 (Width x Height).", "keydesign"),
                            "dependency" => array(
                                "element" => "featured_image_source",
                                "value" => array("media_library"),
                            ),
                            "group" => esc_html__("Image settings", "keydesign"),
                        ),

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Image external source", "keydesign"),
                            "param_name" => "featured_ext_image",
                            "value" => "",
                            "description" => esc_html__("Enter image external link.", "keydesign"),
                            "dependency" =>	array(
                                "element" => "featured_image_source",
                                "value" => array("external_link")
                            ),
                            "group" => esc_html__("Image settings", "keydesign"),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Image size", "keydesign"),
                            "param_name" => "featured_ext_image_size",
                            "value" => "",
                            "description" => esc_html__("Enter image size in pixels. Example: 650x450 (Width x Height).", "keydesign"),
                            "dependency" =>	array(
                                "element" => "featured_image_source",
                                "value" => array("external_link")
                            ),
                            "group" => esc_html__("Image settings", "keydesign"),
                        ),

                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Image align","keydesign"),
                            "param_name"	=>	"fss_image_align",
                            "value"			=>	array(
                                    "Left" => "img-align-left",
                                    "Right" => "img-align-right",
                                ),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_photo"),
                            ),
                            "description"	=>	esc_html__("Select image alignment relative to the content.", "keydesign"),
                            "group" => esc_html__("Image settings", "keydesign"),
                        ),

                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html__("Image gallery source", "keydesign"),
                            "param_name" => "gallery_image_source",
                            "value" => array(
                                "Media library" => "media_library",
                                "External link" => "external_link",
                            ),
                            "description" => esc_html__("Select image source.", "keydesign"),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_gallery"),
                            ),
                            "group" => esc_html__("Gallery settings", "keydesign"),
                        ),

                        array(
                            "type" => "attach_images",
                            "class" => "",
                            "heading" => esc_html__("Photo gallery", "keydesign"),
                            "param_name" => "fss_photo_gallery",
                            "value" => "",
                            "description" => esc_html__("Select images from media library.", "keydesign"),
                            "dependency" => array(
                                "element" => "gallery_image_source",
                                "value" => array("media_library"),
                            ),
                            "save_always" => true,
                            "group" => esc_html__("Gallery settings", "keydesign"),
                        ),

                        array(
                            "type" => "exploded_textarea_safe",
                            "class" => "",
                            "heading" => esc_html__("Image external links", "keydesign"),
                            "param_name" => "gallery_ext_image",
                            "value" => "",
                            "description" => esc_html__("Enter external link for each gallery image (Note: divide links with linebreaks (Enter)).", "keydesign"),
                            "dependency" =>	array(
                                "element" => "gallery_image_source",
                                "value" => array("external_link")
                            ),
                            "group" => esc_html__("Gallery settings", "keydesign"),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Image size", "keydesign"),
                            "param_name" => "gallery_ext_image_size",
                            "value" => "",
                            "description" => esc_html__("Enter image size in pixels. Example: 650x450 (Width x Height).", "keydesign"),
                            "dependency" =>	array(
                                "element" => "gallery_image_source",
                                "value" => array("external_link")
                            ),
                            "group" => esc_html__("Gallery settings", "keydesign"),
                        ),

                        array(
                            "type"          =>  "dropdown",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Enable loop","keydesign"),
                            "param_name"    =>  "fss_loop",
                            "value"         =>  array(
                                    "Off" => "loop_off",
                                    "On" => "loop_on",
                                ),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_gallery"),
                            ),
                            "description"   =>  esc_html__("Infinity loop. Duplicate last and first items to get loop illusion.", "keydesign"),
                            "group" => esc_html__("Gallery settings", "keydesign"),
                        ),

                        array(
                            "type"          =>  "dropdown",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Navigation style","keydesign"),
                            "param_name"    =>  "fss_nav_style",
                            "value"         =>  array(
                                    "Arrows" => "nav-arrows",
                                    "Dots" => "nav-dots",
                                ),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_gallery"),
                            ),
                            "description"   =>  esc_html__("Select navigation style.", "keydesign"),
                            "group" => esc_html__("Gallery settings", "keydesign"),
                        ),

                        array(
                            "type"          =>  "dropdown",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Enable autoplay","keydesign"),
                            "param_name"    =>  "fss_autoplay",
                            "value"         =>  array(
                                    "Off"   => "auto_off",
                                    "On"   => "auto_on"
                                ),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_gallery"),
                            ),
                            "description"   =>  esc_html__("Carousel autoplay settings.", "keydesign"),
                            "group" => esc_html__("Gallery settings", "keydesign"),
                        ),

                        array(
                            "type"          =>  "dropdown",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Autoplay speed","keydesign"),
                            "param_name"    =>  "fss_autoplay_speed",
                            "value"         =>  array(
                                    "10s"   => "10000",
                                    "9s"   => "9000",
                                    "8s"   => "8000",
                                    "7s"   => "7000",
                                    "6s"   => "6000",
                                    "5s"   => "5000",
                                    "4s"   => "4000",
                                    "3s"   => "3000",
                                ),
                            "save_always" => true,
                            "dependency" =>	array(
                                "element" => "fss_autoplay",
                                "value" => array("auto_on")
                            ),
                            "description"   =>  esc_html__("Carousel autoplay speed.", "keydesign"),
                            "group" => esc_html__("Gallery settings", "keydesign"),
                        ),

                        array(
                            "type"          =>  "dropdown",
                            "class"         =>  "",
                            "heading"       =>  esc_html__("Stop on hover","keydesign"),
                            "param_name"    =>  "fss_stoponhover",
                            "value"         =>  array(
                                    "Off"   => "hover_off",
                                    "On"   => "hover_on"
                                ),
                            "save_always" => true,
                            "dependency" =>	array(
                                "element" => "fss_autoplay",
                                "value" => array("auto_on")
                            ),
                            "description"   =>  esc_html__("Stop sliding carousel on mouse over.", "keydesign"),
                            "group" => esc_html__("Gallery settings", "keydesign"),
                        ),

                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Gallery align","keydesign"),
                            "param_name"	=>	"fss_gallery_align",
                            "value"			=>	array(
                                    "Left" => "gallery-align-left",
                                    "Right" => "gallery-align-right",
                                ),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_gallery"),
                            ),
                            "description"	=>	esc_html__("Select image gallery alignment relative to the content.", "keydesign"),
                            "group" => esc_html__("Gallery settings", "keydesign"),
                        ),

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("YouTube URL", "keydesign"),
                            "param_name" => "fss_video_url",
                            "value" => "",
		                        "description" => esc_html__("Paste the YouTube url here.", "keydesign"),
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_video"),
                            ),
                            "group" => esc_html__("Video settings", "keydesign"),
                        ),

                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html__("Video image source", "keydesign"),
                            "param_name" => "video_image_source",
                            "value" => array(
                                "Media library" => "media_library",
                                "External link" => "external_link",
                            ),
                            "description" => esc_html__("Select video cover image source.", "keydesign"),
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_video"),
                            ),
                            "save_always" => true,
                            "group" => esc_html__("Video settings", "keydesign"),
                        ),

                        array(
                            "type" => "attach_image",
                            "heading" => esc_html__("Video cover image", "keydesign"),
                            "param_name" => "fss_video_cover",
                            "description" => esc_html__("Upload video cover image. Recommended image size: 650x450 (Width x Height).", "keydesign"),
                            "dependency" => array(
                                "element" => "video_image_source",
                                "value" => array("media_library"),
                            ),
                            "group" => esc_html__("Video settings", "keydesign"),
                        ),

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Image external link", "keydesign"),
                            "param_name" => "video_ext_image",
                            "value" => "",
		                        "description" => esc_html__("Enter image external link.", "keydesign"),
                            "dependency" =>	array(
                                "element" => "video_image_source",
                                "value" => array("external_link")
                            ),
                            "group" => esc_html__("Video settings", "keydesign"),
                        ),
                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Image size", "keydesign"),
                            "param_name" => "video_ext_image_size",
                            "value" => "",
		                        "description" => esc_html__("Enter image size in pixels. Example: 650x450 (Width x Height).", "keydesign"),
                            "dependency" =>	array(
                                "element" => "video_image_source",
                                "value" => array("external_link")
                            ),
                            "group" => esc_html__("Video settings", "keydesign"),
                        ),

                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Play button style","keydesign"),
                            "param_name"	=>	"fss_play_button",
                            "value"			=>	array(
                                    "Light" => "light-style",
                                    "Dark" => "dark-style",
                                ),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_video"),
                            ),
                            "description"	=>	esc_html__("Select play button color scheme.", "keydesign"),
                            "group" => esc_html__("Video settings", "keydesign"),
                        ),

                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Video align","keydesign"),
                            "param_name"	=>	"fss_video_align",
                            "value"			=>	array(
                                    "Left" => "video-align-left",
                                    "Right" => "video-align-right",
                                ),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "fss_section_template",
                                "value" => array("side_video"),
                            ),
                            "description"	=>	esc_html__("Select video alignment relative to the content.", "keydesign"),
                            "group" => esc_html__("Video settings", "keydesign"),
                        ),

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Extra class name", "keydesign"),
                            "param_name" => "fss_extra_class",
                            "value" => "",
                            "description" => esc_html__("If you wish to style this particular content element differently, then use this field to add a class name and then refer to it in your css file.", "keydesign"),
                            "group" => esc_html__("Extras", "keydesign"),
                        ),

                    )
                ));
            }
        }

        public function kd_featuresections_container($atts, $content = null) {

            $redux_ThemeTek = get_option( 'redux_ThemeTek' );
            $with_sticky_topbar ='';
            $with_fixed_menu ='';

            extract(shortcode_atts(array(
              'fsp_extra_class' => '',
            ), $atts));


            if (isset($redux_ThemeTek['tek-topbar-sticky'])) { if ($redux_ThemeTek['tek-topbar-sticky'] == '1') { $with_sticky_topbar = 'with-sticky-topbar'; }}
            if (isset($redux_ThemeTek['tek-menu-behaviour'])) { if ($redux_ThemeTek['tek-menu-behaviour'] == '2') { $with_fixed_menu = 'with-fixed-menu'; }}

            $output = '<div class="feature-sections-wrapper '.$fsp_extra_class.'">
            <section class="feature-sections-tabs '.$with_sticky_topbar.' '.$with_fixed_menu.'">
              <nav class="kd-feature-tabs">
                <ul class="nav nav-tabs sticky-tabs">
                </ul>
              </nav>
            </section>
            '.do_shortcode($content).'
            </div>';

            return $output;
        }

        public function kd_featuresections_single($atts, $content = null) {

            /* Declare empty vars */
            $output = $icons = $icon_color_style = $icon_size_style = $content_icon = $section_class = $href = $section_bg_class = $link_class = $link_target = $link_title = '';

            extract(shortcode_atts(array(
                'fss_section_template' => '',
                'fss_title' => '',
                'fss_nav_label' => '',
                'fss_title_color' => '',
                'fss_subtitle' => '',
                'fss_subtitle_color' => '',
                'fss_icon_type' => '',
                'icon_iconsmind' => '',
                'fss_icon_color' => '',
                'fss_icon_size' => '',
                'fss_icon_img' => '',
                'fss_content_color' => '',
                'fss_custom_link' => '',
                'fss_section_link' => '',
                'fss_link_style' => '',
                'fss_button_color_scheme' => '',
                'fss_image_align' => '',
                'fss_gallery_align' => '',
                'fss_video_align' => '',
                'fss_extra_class' => '',
            ), $atts));

            // wp_enqueue_script( 'scrollpos-styler' );

            if( $fss_icon_type == 'icon_browser' ) {
              wp_enqueue_style( 'kd_iconsmind' );
            }

            if (strlen($icon_iconsmind) > 0) {
                $icons = $icon_iconsmind;
            }

            if ($fss_icon_color !== '') {
              $icon_color_style = 'color: '.$fss_icon_color.';';
            }

            if ($fss_icon_size !== '') {
              $icon_size_style = 'font-size: '.$fss_icon_size.';';
            }

            if ( $fss_icon_type == 'icon_browser' ) {
				          $content_icon = '<i class="fa '.$icons .'" style="'.$icon_size_style.' '.$icon_color_style.'"></i> ';
            } elseif ( $fss_icon_type == 'custom_icon' && !empty($fss_icon_img) ) {
				          $icon_img_array = wpb_getImageBySize ( $params = array( 'post_id' => NULL, 'attach_id' => $fss_icon_img, 'thumb_size' => 'full', 'class' => "" ) );
			            $content_icon = '<div class="custom-icon">'.$icon_img_array['thumbnail'].'</div>';
			      }


            /* Featured content alignment */
            switch($fss_section_template) {
      				case 'side_photo':
                if ($fss_image_align == 'img-align-left') {
	                $section_class = 'featured-left';
                } elseif ($fss_image_align == 'img-align-right') {
                  $section_class = 'featured-right';
                }
      				break;

              case 'side_gallery':
                if ($fss_gallery_align == 'gallery-align-left') {
	                $section_class = 'featured-left';
                } elseif ($fss_gallery_align == 'gallery-align-right') {
                  $section_class = 'featured-right';
                }
      				break;

      				case 'side_video':
                if ($fss_video_align == 'video-align-left') {
                  $section_class = 'featured-left';
                } elseif ($fss_video_align == 'video-align-right') {
                  $section_class = 'featured-right';
                }
      				break;

      				default:
      			}

            /* Link settings */
            $href = vc_build_link($fss_section_link);
            if ($href['target'] == "") { $href['target'] = "_self"; }

      			if($href['url'] !== '') {
      				$link_target = (isset($href['target'])) ? ' target="'.$href['target'].'"' : 'target="_self"';
      				$link_title = (isset($href['title'])) ? ' title="'.$href['title'].'"' : '';
      			}

            /* Link class */
            switch($fss_link_style) {
              case 'solid_button':
                if ($fss_button_color_scheme == 'btn_primary_color') {
	                $link_class = 'tt_button btn_primary_color';
                } elseif ($fss_button_color_scheme == 'btn_secondary_color') {
                  $link_class = 'tt_button btn_secondary_color';
                }
      				break;

      				case 'outline_button':
                if ($fss_button_color_scheme == 'btn_primary_color') {
                  $link_class = 'tt_button tt_secondary_button btn_primary_color';
                } elseif ($fss_button_color_scheme == 'btn_secondary_color') {
                  $link_class = 'tt_button tt_secondary_button btn_secondary_color';
                }
      				break;

              case 'simple_link':
                $link_class = 'simple-link';
      				break;
            }

            $kd_featuresections_id = uniqid('kd-fss-');

            $output .= '<section id="'.$kd_featuresections_id.'" class="'.$section_class.' '.$fss_extra_class.'">
              <li class="nav-label"><a class="feature-tabs-scroll" href="#'.$kd_featuresections_id.'">'.$fss_nav_label.'</a></li>
              <div class="container">
                <div class="side-content-wrapper">';
                  if ($fss_icon_type != "no_icon") {
                    $output .= $content_icon;
                  }
                  $output .= '<h3 class="side-content-title" '.(!empty($fss_title_color) ? 'style="color: '.$fss_title_color.';"' : '').'>'.$fss_title.'</h3>';
                  if($fss_subtitle != '') {
                    $output .= '<h6 class="side-content-subtitle" '.(!empty($fss_subtitle_color) ? 'style="color: '.$fss_subtitle_color.';"' : '').'>'.$fss_subtitle.'</h6>';
                  }
                  if($content != '') {
                    $output .= '<div class="side-content-text" '.(!empty($fss_content_color) ? 'style="color: '.$fss_content_color.';"' : '').'>'.do_shortcode($content).'</div>';
                  }
                  if ($fss_custom_link == "1" && $href['title'] != '') {
                    $output .= '<div class="side-content-link"><a href="'.$href['url'].'"'.$link_target.''.$link_title.' class="'.$link_class.'">'.$href['title'].'</a></div>';
                  }
                $output .= '</div>';
                require_once(KEYDESIGN_PLUGIN_PATH.'/elements/templates/sticky-nav/section-'.$fss_section_template.'.php');
                $template_func = 'kd_section_set_'.$fss_section_template;
          			$output .= $template_func($atts,$content);
              $output .= '</div>
            </section>';

            return $output;
        }
    }
}
if (class_exists('tek_featuresections')) {
    $tek_featuresections = new tek_featuresections;
}
?>

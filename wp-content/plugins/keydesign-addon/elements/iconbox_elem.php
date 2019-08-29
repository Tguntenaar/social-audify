<?php

if (!class_exists('KD_ELEM_ICON_BOX')) {

    class KD_ELEM_ICON_BOX extends KEYDESIGN_ADDON_CLASS {

        function __construct() {
            add_action('init', array($this, 'kd_iconbox_init'));
            add_shortcode('tek_iconbox', array($this, 'kd_iconbox_shrt'));
        }

        // Element configuration in admin

        function kd_iconbox_init() {
            if (function_exists('vc_map')) {
                vc_map(array(
                    "name" => esc_html__("Icon box", "keydesign"),
                    "description" => esc_html__("Simple text box with icon.", "keydesign"),
                    "base" => "tek_iconbox",
                    "class" => "",
                    "icon" => plugins_url('assets/element_icons/icon-box.png', dirname(__FILE__)),
                    "category" => esc_html__("KeyDesign Elements", "keydesign"),
                    "params" => array(

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Box title", "keydesign"),
                            "param_name" => "title",
                            "value" => "",
                            "admin_label" => true,
                            "description" => esc_html__("Enter box title here.", "keydesign"),
                        ),

                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Title size","keydesign"),
                            "param_name"	=>	"title_size",
                            "value"			=>	array(
                                "Large" => "large-title",
                                "Small title" => "small-title",
                            ),
                            "save_always" => true,
                            "description"	=>	esc_html__("Select title size.", "keydesign"),
                        ),

                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Title color", "keydesign"),
                            "param_name" => "title_color",
                            "value" => "",
                            "description" => esc_html__("Choose title color. If none selected, the default theme color will be used.", "keydesign"),
                        ),

                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Box content type","keydesign"),
                            "param_name"	=>	"box_content_type",
                            "value"			=>	array(
                                "Simple text" => "simple_text",
                                "HTML content" => "html_content",
                            ),
                            "save_always" => true,
                            "description"	=>	esc_html__("Select box content type.", "keydesign"),
                        ),

	                      array(
                            "type" => "textarea",
                            "class" => "",
                            "heading" => esc_html__("Box description - simple text", "keydesign"),
                            "param_name" => "text_box",
                            "value" => "",
                            "description" => esc_html__("Enter box content text here.", "keydesign"),
		                        "dependency" =>	array(
                                "element" => "box_content_type",
                                "value" => array("simple_text")
                            ),
                        ),

                        array(
                            "type" => "textarea_html",
                            "class" => "",
                            "heading" => esc_html__("Box description - HTML content", "keydesign"),
                            "param_name" => "content",
                            "value" => "",
                            "description" => esc_html__("Enter box content text here.", "keydesign"),
			                      "dependency" =>	array(
                                "element" => "box_content_type",
                                "value" => array("html_content")
                            ),
                        ),

                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Content text color", "keydesign"),
                            "param_name" => "text_color",
                            "value" => "",
                            "description" => esc_html__("Choose content text color. If none selected, the default theme color will be used.", "keydesign"),
                        ),

                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Display icon","keydesign"),
                            "param_name"	=>	"icon_type",
                            "value"			=>	array(
                                    "Icon browser" => "icon_browser",
                                    "Custom icon" => "custom_icon",
                                    "No icon" => "no_icon",
                                ),
                            "save_always" => true,
                            "description"	=>	esc_html__("Select icon source.", "keydesign"),
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
              								"element" => "icon_type",
              								"value" => "icon_browser",
              							),
              							"description" => esc_html__( "Select icon from library.", "keydesign" ),
            						),

                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Icon color", "keydesign"),
                            "param_name" => "icon_color",
                            "value" => "",
                            "dependency" =>	array(
                                "element" => "icon_type",
                                "value" => array("icon_browser")
                            ),
                            "description" => esc_html__("Choose icon color. If none selected, the default theme color will be used.", "keydesign"),
                        ),

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Icon size", "keydesign"),
                            "param_name" => "icon_size",
                            "value" => "",
                            "dependency" =>	array(
                                "element" => "icon_type",
                                "value" => array("icon_browser")
                            ),
                            "description" => esc_html__("Enter icon size. (eg. 10px, 1em, 1rem)", "keydesign"),
                        ),

                        array(
                            "type" => "attach_image",
                            "class" => "",
                            "heading" => esc_html__("Upload image icon", "keydesign"),
                            "param_name" => "icon_img",
                            "value" => "",
                            "description" => esc_html__("Upload your own custom image.", "keydesign"),
                            "dependency" => array(
                                "element" => "icon_type",
                                "value" => array("custom_icon"),
                            ),
                        ),

                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Image size","keydesign"),
                            "param_name"	=>	"icon_img_size",
                            "value"			=>	array(
                                    "Small size" => "img_small_size",
                                    "Medium size" => "img_medium_size",
                                    "Big size" => "img_big_size",
                                ),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "icon_type",
                                "value" => array("custom_icon"),
                            ),
                            "description"	=>	esc_html__("Select image size.", "keydesign"),
                        ),

                        array(
                            "type"			=>	"dropdown",
                            "class"			=>	"",
                            "heading"		=>	esc_html__("Icon position","keydesign"),
                            "param_name"	=>	"icon_position",
                            "value"			=>	array(
                                    "Top" => "icon_top",
                                    "Left" => "icon_left",
                                ),
                            "save_always" => true,
                            "dependency" => array(
                                "element" => "icon_type",
                                "value" => array("icon_browser", "custom_icon"),
                            ),
                            "description"	=>	esc_html__("Select icon position relative to the content.", "keydesign"),
                        ),

                        array(
                            "type" => "dropdown",
                            "class" => "",
                            "heading" => esc_html__("Content alignment", "keydesign"),
                            "param_name" => "content_alignment",
                            "value" => array(
                                "Align center"      => "content_center",
                                "Align left"        => "content_left",
                                "Align right"       => "content_right"
                            ),
                            "save_always" => true,
                            "description" => esc_html__("Choose content alignment.", "keydesign"),
                        ),

                        array(
                             "type"	=>	"dropdown",
                             "class" =>	"",
                             "heading" => esc_html__("Link type", "keydesign"),
                             "param_name" => "custom_link",
                             "value" =>	array(
                                    esc_html__( "No link", "keydesign" ) => "#",
                                    esc_html__( "Add a custom link", "keydesign" )	=> "1",
                                ),
                             "save_always" => true,
                             "description" => esc_html__("You can add/remove custom link", "keydesign"),
                        ),

                        array(
                            "type" => "textfield",
                            "class" => "",
                            "heading" => esc_html__("Link text", "keydesign"),
                            "param_name" => "link_text",
                            "value" => "",
                            "description" => esc_html__("Enter link text here.", "keydesign"),
                            "dependency" => array(
                               "element" => "custom_link",
                               "value"	=> array( "1" ),
                           ),
                        ),

                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Link color", "keydesign"),
                            "param_name" => "link_color",
                            "value" => "",
                            "description" => esc_html__("Choose link color. If none selected, the default theme color will be used.", "keydesign"),
                            "dependency" => array(
                               "element" => "custom_link",
                               "value"	=> array( "1" ),
                           ),
                        ),

                        array(
                             "type"	=>	"vc_link",
                             "class" =>	"",
                             "heading" => esc_html__("Link settings", "keydesign"),
                             "param_name" => "iconbox_link",
                             "value" =>	"",
                             "description" => esc_html__("You can add or remove the existing link from here.", "keydesign"),
                             "dependency" => array(
                                "element" => "custom_link",
                                "value"	=> array( "1" ),
                            ),
                        ),

                        array(
              							"type" => "dropdown",
              							"class" => "",
              							"heading" => esc_html__("Box background type", "keydesign"),
              							"param_name" =>	"background_type",
              							"value" =>	array(
              								esc_html__( 'None', 'keydesign' ) => 'none',
              								esc_html__( 'Select color', 'keydesign' )	=> 'custom_bg_color',
              							),
              							"save_always" => true,
              							"description" => esc_html__("Select box background type.", "keydesign"),
            						),

                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Background color", "keydesign"),
                            "param_name" => "background_color",
                            "value" => "",
                            "dependency" =>	array(
                								"element" => "background_type",
                								"value" => array( "custom_bg_color" ),
      							        ),
                            "description" => esc_html__("Choose box background color.", "keydesign"),
                        ),

                        array(
              							"type" => "dropdown",
              							"class" => "",
              							"heading" => esc_html__("Box border type", "keydesign"),
              							"param_name" =>	"border_type",
              							"value" =>	array(
              								esc_html__( 'None', 'keydesign' ) => 'none',
              								esc_html__( 'Select border color', 'keydesign' )	=> 'custom_border_color',
              							),
                            "dependency" =>	array(
                								"element" => "background_type",
                								"value" => array( "custom_bg_color" ),
      							        ),
              							"save_always" => true,
              							"description" => esc_html__("Select box border type.", "keydesign"),
            						),

                        array(
                            "type" => "colorpicker",
                            "class" => "",
                            "heading" => esc_html__("Border color", "keydesign"),
                            "param_name" => "border_color",
                            "value" => "",
                            "dependency" =>	array(
                								"element" => "border_type",
                								"value" => array( "custom_border_color" ),
      							        ),
                            "description" => esc_html__("Choose box border color. If none selected, the default theme border color will be used.", "keydesign"),
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
                            "param_name" => "ib_animation_delay",
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
                            "param_name" => "ib_extra_class",
                            "value" => "",
                            "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "keydesign")
                        ),

                    )
                ));
            }
        }



		// Render the element on front-end

        public function kd_iconbox_shrt($atts, $content = null)
        {

            // Declare empty vars
            $output = $content_icon = $icons = $icon_position_class = $content_alignment_class = $link_title = $link_target = $icon_color_style = $icon_size_style = $normal_bg = $normal_border = $box_border_class = $theme_primary_color = $bg_extra_class = $animation_delay = '';

            extract(shortcode_atts(array(
                'icon_type'		              => '',
                'title'                     => '',
                'title_size'                => '',
                'title_color'               => '',
	              'box_content_type'			    => '',
                'text_box'                  => '',
                'text_color'                => '',
                'icon_type'                 => '',
                'icon_iconsmind'            => '',
                'icon_color'                => '',
                'icon_size'                 => '',
                'icon_img'                  => '',
                'icon_img_size'             => '',
                'icon_position'             => '',
                'content_alignment'         => '',
                'custom_link'               => '',
                'link_text'                 => '',
                'link_color'                => '',
                'iconbox_link'              => '',
                'background_type'           => '',
                'background_color'          => '',
                'border_type'               => '',
                'border_color'              => '',
                'css_animation'             => '',
                'ib_animation_delay'        => '',
                'ib_extra_class'            => '',
            ), $atts));


            if( $icon_type == 'icon_browser' ) {
              // Enqueue needed icon font.
              // vc_icon_element_fonts_enqueue( $icon_library );
              wp_enqueue_style( 'kd_iconsmind' );
            }

            if (strlen($icon_iconsmind) > 0) {
                $icons = $icon_iconsmind;
            }

            if ($icon_color !== '') {
              $icon_color_style = 'color: '.$icon_color.';';
            }

            if ($icon_size !== '') {
              $icon_size_style = 'font-size: '.$icon_size.';';
            }

            $href = vc_build_link($iconbox_link);
            if ($href['target'] == "") { $href['target'] = "_self"; }

      			if($href['url'] !== '') {
      				$link_target = (isset($href['target'])) ? ' target="'.$href['target'].'"' : 'target="_self"';
      				$link_title = (isset($href['title'])) ? ' title="'.$href['title'].'"' : '';
      			}

            if ( $icon_type == 'icon_browser' ) {
				          $content_icon = '<i class="'.$icons .' fa" style="'.$icon_size_style.' '.$icon_color_style.'"></i> ';
            } elseif ( $icon_type == 'custom_icon' && !empty($icon_img) ) {
				          $tt_iconbox_img_array = wpb_getImageBySize ( $params = array( 'post_id' => NULL, 'attach_id' => $icon_img, 'thumb_size' => 'full', 'class' => "" ) );
			            $content_icon = '<div class="tt-iconbox-customimg '.$icon_img_size.'">'.$tt_iconbox_img_array['thumbnail'].'</div>';
			      }

            // Content alignment in box
            if( $content_alignment == 'content_center' ) {
                $content_alignment_class = 'cont-center';
            }
            elseif ( $content_alignment == 'content_left' ) {
                $content_alignment_class = 'cont-left';
            }
            elseif ( $content_alignment == 'content_right' ) {
                $content_alignment_class = 'cont-right';
            }


            // Icon position class
            if ( $icon_position == 'icon_top' ) {
                $icon_position_class = 'icon-top';
            } elseif ( $icon_position == 'icon_left' ) {
                $icon_position_class = 'icon-left';
            }

            //Background type
            switch($background_type){
      				case 'none':
      					$normal_bg = 'none';
      				break;

      				case 'custom_bg_color':
                if ($background_color != '') {
                  $normal_bg = $background_color;
                } else {
                  $normal_bg = 'transparent';
                }
      				break;

      				default:
      			}

            //Border type
            switch($border_type){
      				case 'none':
      					$normal_border = 'border: none;';
      				break;

      				case 'custom_border_color':
                if ($border_color != '') {
	                $normal_border = 'border: 1px solid ' . $border_color . ';';
                } else {
                  $normal_border = '';
                }
      				break;

      				default:
      			}

            if ($border_type == 'custom_border_color') {
              $box_border_class = 'with-border';
            }

            //CSS Animation
            if ($css_animation == "no_animation") {
                $css_animation = "";
            }

            // Animation delay
            if ($ib_animation_delay) {
                $animation_delay = 'data-animation-delay='.$ib_animation_delay;
            }

            // Check if background color matches theme primary colorpicker
            $redux_ThemeTek = get_option( 'redux_ThemeTek' );
            $theme_primary_color = $redux_ThemeTek['tek-main-color'];
            if ($theme_primary_color == $background_color) {
              $bg_extra_class = "iconbox-main-color";
            }


            $output .= '<div class="key-icon-box icon-default '.$icon_position_class.' '.$content_alignment_class.' '.$box_border_class.' '.$css_animation.' '.$ib_extra_class.'" '.(!empty($background_type) ? 'style="background-color: '.$normal_bg.'; '.$normal_border.'"' : '').' '.$animation_delay.'>';
              if ($background_type == "custom_bg_color") {
                $output .= '<div class="ib-wrapper">';
              }
              if ($icon_type != "no_icon") {
                $output .= $content_icon;
              }
                if ( !empty($title) && ($title_size == "small-title") ) {
                  $output .= '<h6 class="service-heading" '.(!empty($title_color) ? 'style="color: '.$title_color.';"' : '').'>'.$title.'</h6>';
                } elseif ( !empty($title) ) {
                  $output .= '<h4 class="service-heading" '.(!empty($title_color) ? 'style="color: '.$title_color.';"' : '').'>'.$title.'</h4>';
                }

                if ($box_content_type == "html_content") {
        					if($content != '') {
        						$output .= '<p '.(!empty($text_color) ? 'style="color: '.$text_color.';"' : '').'>'.do_shortcode($content).'</p>';
        					}
        				} else {
                  if ( !empty($text_box) ) {
        					  $output .= '<p '.(!empty($text_color) ? 'style="color: '.$text_color.';"' : '').'>'.$text_box.'</p>';
        					}
                }

              if ($custom_link == "1") {
                $output .= '<p class="ib-link '.$bg_extra_class.'"><a href="'.$href['url'].'"'.$link_target.''.$link_title.' '.(!empty($link_color) ? 'style="color: '.$link_color.';"' : '').'>'.$link_text.'</a></p>';
              }
              if ($background_type == "custom_bg_color") {
                $output .= '</div>';
              }
            $output .= '</div>';
            return $output;

        }
    }
}

if (class_exists('KD_ELEM_ICON_BOX')) {
    $KD_ELEM_ICON_BOX = new KD_ELEM_ICON_BOX;
}

?>

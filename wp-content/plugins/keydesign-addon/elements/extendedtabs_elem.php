<?php
if (class_exists('WPBakeryShortCodesContainer')) {
  class WPBakeryShortCode_tek_extended_tabs extends WPBakeryShortCodesContainer {
  }}

if (class_exists('WPBakeryShortCode')) {
  class WPBakeryShortCode_tek_extended_tabs_single extends WPBakeryShortCode {
  }}
if (!class_exists('tek_extended_tabs')) {
  class tek_extended_tabs extends KEYDESIGN_ADDON_CLASS {
    function __construct() {
      add_action('init', array($this, 'kd_extended_tabs_init'));
      add_shortcode('tek_extended_tabs', array($this, 'kd_extended_tabs_container'));
      add_shortcode('tek_extended_tabs_single', array( $this, 'kd_extended_tabs_single'));
      add_action('wp_enqueue_scripts', array($this, 'kd_load_element_scripts'));
    }

    function kd_load_element_scripts() {
      wp_enqueue_script("kd_easytabs_script");
    }

    /* VC Elements render in admin */
    function kd_extended_tabs_init() {
      if (function_exists('vc_map')) {
			vc_map(				array(
					"name" => esc_html__("Extended tabs", "keydesign"),
					"description" => __("Vertical tabs with extended features.", "keydesign"),
					"base" => "tek_extended_tabs",
					"class" => "",
					"show_settings_on_create" => true,
					"content_element" => true,
					"as_parent" => array('only' => 'tek_extended_tabs_single'),
					"icon" => plugins_url('assets/element_icons/extended-tabs.png', dirname(__FILE__)),
					"category" => esc_html__("KeyDesign Elements", "keydesign"),
					"js_view" => 'VcColumnView',
					"params" => array(
						array(
							"type" => "dropdown",
							"class" => "",
							"heading" => esc_html__("Tabs alignment","keydesign"),
							"param_name" => "services_tabs_alignment",
							"value" => array(
								"Left image" => "tabs-image-left",
								"Right image" => "tabs-image-right",
							),
							"save_always" => true,
							"description"   =>  esc_html__("Select tabs image alignment.", "keydesign"),
						),
						array(
							"type" => "textfield",
							"class" => "",
							"heading" => esc_html__("Extra class name", "keydesign"),
							"param_name" => "services_extra_class",
							"value" => "",
							"description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", "keydesign"),
						),
					)
				)			);
			vc_map(array(
				"name" => esc_html__("Tab", "keydesign"),
				"base" => "tek_extended_tabs_single",
				"content_element" => true,
				"as_child" => array('only' => 'tek_extended_tabs'),
				"icon" => plugins_url('assets/element_icons/child-tabs.png', dirname(__FILE__)),
				"params" => array(
					array(
						"type" => "textfield",
						"heading" => esc_html__("Tab Title", "keydesign"),
						"param_name" => "services_title",
						"holder" => "tab_title",
						"value" => "",
						"description" => esc_html__("Enter tab title / tab anchor here.", "keydesign"),
					),
					array(
						"type" => "exploded_textarea",
						"heading" => esc_html__("Tab Content", "keydesign"),
						"param_name" => "services_content",
						"value" => "",
						"description" => esc_html__("Content as list - enter one feature per line", "keydesign"),
					),
          array(
              "type" => "dropdown",
              "class" => "",
              "heading" => esc_html__("Image source", "keydesign"),
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
						"heading" => esc_html__("Image", "keydesign"),
						"param_name" => "services_image",
						"value" => "",
						"description" => esc_html__("Select or upload your image using the media library.", "keydesign"),
            "dependency" =>	array(
                "element" => "image_source",
                "value" => array("media_library")
            ),
					),
          array(
              "type" => "textfield",
              "class" => "",
              "heading" => esc_html__("Image external source", "keydesign"),
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
              "heading" => esc_html__("Image size", "keydesign"),
              "param_name" => "ext_image_size",
              "value" => "",
              "description" => esc_html__("Enter image size in pixels. Example: 750x500 (Width x Height).", "keydesign"),
              "dependency" =>	array(
                  "element" => "image_source",
                  "value" => array("external_link")
              ),
          ),
				)
			));
        }
    }
	public function kd_extended_tabs_container($atts, $content = null) {

        extract(shortcode_atts(array(
    			'services_extra_class' => '',
    			'services_tabs_alignment' => '',
        ), $atts));

        $kd_tabs_id = "kd-tabswrapper-".uniqid(rand(10000,99999));

        $output = '<div class="features-tabs '.$kd_tabs_id.' '.$services_tabs_alignment.' '.$services_extra_class.'">
    			' . do_shortcode($content) . '
    			<ul class="tabs"></ul>
        </div>';

        $output .= '<script type="text/javascript">
          jQuery(document).ready(function($){
            $(".features-tabs.'.$kd_tabs_id.'").each( function (){
                $( "li.tab" , this ).appendTo( $( ".tabs" , this ) );
            } );
          });</script>';

		return $output;
    }
    public function kd_extended_tabs_single($atts, $content = null) {
        extract(shortcode_atts(array(
    			'services_title' => '',
    			'services_content' => '',
          'image_source' => '',
    			'services_image' => '',
    			'ext_image' => '',
    			'ext_image_size' => '',
        ), $atts));

        $output = $default_src = $dimensions = $hwstring = $kd_extendtabs_id = '';
        $image  = wpb_getImageBySize($params = array( 'post_id' => NULL, 'attach_id' => $services_image, 'thumb_size' => 'full', 'class' => ""));

        $default_src = vc_asset_url( 'vc/no_image.png' );

        $dimensions = vcExtractDimensions( $ext_image_size );
        $hwstring = $dimensions ? image_hwstring( $dimensions[0], $dimensions[1] ) : '';
        $ext_image = $ext_image ? esc_attr( $ext_image ) : $default_src;

        $kd_extendtabs_id = "kd-extab-".uniqid(rand(10000,99999));

        $output .= '<div id="'.$kd_extendtabs_id.'">
      		<div class="tab-image-container">';
           if ($image_source == 'external_link') {
             if (!$ext_image) {
               $output .='<img src="'.$default_src.'" class="vc_img-placeholder" />';
             } else {
               $output .='<img src="'.$ext_image.'" '.$hwstring.' />';
             }
           } else {
             if (!$image) {
               $output .='<img src="'.$default_src.'" class="vc_img-placeholder" />';
             } else {
               $output .= $image['thumbnail'];
             }
           }
          $output .= '</div>
      		<li class="tab col-md-4">
      			<a href="#' . $kd_extendtabs_id . '">
      				<h4>' . $services_title . '</h4>';
              if (!empty($services_content)) {
  				      $output .= '<p>' . $services_content . '</p>';
              }
      			$output .= '</a>
      		</li>
      	</div>';
        return $output;
    }
   }
 }

 if (class_exists('tek_extended_tabs')) {
    $tek_extended_tabs = new tek_extended_tabs;
}
?>

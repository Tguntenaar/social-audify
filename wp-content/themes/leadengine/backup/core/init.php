<?php

// ------------------------------------------------------------------------
// Add Redux Framework & extras
// ------------------------------------------------------------------------


$redux_ThemeTek = get_option( 'redux_ThemeTek' );

define( 'KEYDESIGN_THEME_PATH', get_template_directory() );
define( 'KEYDESIGN_THEME_PLUGINS_DIR', KEYDESIGN_THEME_PATH . '/plugins' );

// ------------------------------------------------------------------------
// Theme includes
// ------------------------------------------------------------------------

// Wordpress Bootstrap Menu
require_once( get_template_directory() . '/core/assets/extra/wp_bootstrap_navwalker.php');

// ------------------------------------------------------------------------
// WooCommerce
// ------------------------------------------------------------------------
	if( class_exists( 'WooCommerce' )) {
		add_theme_support( 'woocommerce' );
	}
	if( class_exists( 'WooCommerce' )) {
		require_once( get_template_directory() . '/core/theme-woocommerce.php' );
	}

// ------------------------------------------------------------------------
// Enqueue scripts and styles front and admin
// ------------------------------------------------------------------------

	if( !function_exists('keydesign_enqueue_front') ) {
		function keydesign_enqueue_front() {
			$redux_ThemeTek = get_option( 'redux_ThemeTek' );
			// Bootstrap CSS
			wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/core/assets/css/bootstrap.min.css', '', '' );
			// Theme main style CSS
			wp_enqueue_style( 'keydesign-style', get_stylesheet_uri() );
			// Font Awesome
			wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/core/assets/css/font-awesome.min.css', '', '' );
			// Iconsmind
			wp_enqueue_style( 'kd_iconsmind', get_template_directory_uri() . '/core/assets/css/iconsmind.min.css', '', '' );

			wp_enqueue_style( 'keydesign_default_fonts', keydesign_default_fonts_url(), array(), '' );
			// Bootstrap JS
			wp_enqueue_script( 'bootstrapjs', get_template_directory_uri() . '/core/assets/js/bootstrap.min.js', array('jquery'), '', true );
			// Masonry
			if( is_front_page() || is_page_template('portfolio.php') ) {
				wp_enqueue_script( 'masonry' );
			}
			if( is_singular( 'portfolio' ) ) {
				wp_enqueue_style( 'photoswipe', get_template_directory_uri() . '/core/assets/css/photoswipe.css', '', '' );
				wp_enqueue_style( 'photoswipe-skin', get_template_directory_uri() . '/core/assets/css/photoswipe-default-skin.css', '', '' );
				wp_enqueue_script( 'photoswipejs', get_template_directory_uri() . '/core/assets/js/photoswipe.min.js', array('jquery'), '', true );
				wp_enqueue_script( 'photoswipejs-ui', get_template_directory_uri() . '/core/assets/js/photoswipe-ui-default.min.js', array('jquery'), '', true );
			}
			// Theme main scripts
			wp_enqueue_script( 'keydesign-scripts', get_template_directory_uri() . '/core/assets/js/scripts.js', array(), '', true );

			// Particles
			wp_register_script( 'particles', get_template_directory_uri() . '/core/assets/js/particles.min.js', array(), '', true );
			$themetek_page_particles = get_post_meta( get_the_ID(), '_themetek_page_particles', true );
			if ( !isset($redux_ThemeTek['tek-blog-particles']) ) { $redux_ThemeTek['tek-blog-particles'] = false; }

			if( is_home() && $redux_ThemeTek['tek-blog-particles'] != false || !empty($themetek_page_particles)) {
				wp_enqueue_script( 'particles' );
			}

			// Visual composer - move styles to head
			wp_enqueue_style( 'js_composer_front' );
			wp_enqueue_style( 'js_composer_custom_css' );

		}
	}
	add_action( 'wp_enqueue_scripts', 'keydesign_enqueue_front' );

	// ------------------------------------------------------------------------
	// bbPress
	// ------------------------------------------------------------------------
	function kd_bbpress_css_enqueue(){
		if( function_exists( 'is_bbpress' ) ) {
			// Deregister default bbPress CSS
			wp_deregister_style( 'bbp-default' );

			$file = 'core/assets/css/bbpress.css';

			// Check child theme
			if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
				$location = trailingslashit( get_stylesheet_directory_uri() );
				$handle   = 'bbp-child-bbpress';

			// Check parent theme
			} elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
				$location = trailingslashit( get_template_directory_uri() );
				$handle   = 'bbp-parent-bbpress';
			}

			// Enqueue the bbPress styling
			wp_enqueue_style( $handle, $location . $file, 'screen' );
		}
	}
	add_action( 'wp_enqueue_scripts', 'kd_bbpress_css_enqueue' );

	function keydesign_default_fonts_url() {
        $font_url = add_query_arg( 'family', urlencode( 'Open Sans:300,400,600,700&subset=latin-ext' ), "//fonts.googleapis.com/css" );
    	return $font_url;
	}

	if( !function_exists('keydesign_enqueue_admin') ) {
		function keydesign_enqueue_admin() {
					wp_enqueue_style( 'keydesign_wp_admin_css', get_template_directory_uri() . '/core/assets/css/admin-styles.css', '', '' );
	        wp_enqueue_script( 'keydesign_wp_admin_js', get_template_directory_uri() . '/core/assets/js/admin-scripts.js', '', '1.0.0' );
		}
	}
	add_action( 'admin_enqueue_scripts', 'keydesign_enqueue_admin' );

// ------------------------------------------------------------------------
// Theme Setup
// ------------------------------------------------------------------------

	function keydesign_setup(){
		if ( function_exists( 'add_theme_support' ) ) {
			// Add multilanguage support
			load_theme_textdomain( 'leadengine', get_template_directory() . '/languages' );
			// Add theme support for feed links
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support( 'custom-header', array() );
			add_theme_support( 'custom-background', array() );
			// Add theme support for menus
			if ( function_exists( 'register_nav_menus' ) ) {
				register_nav_menus(
					array(
					  'header-menu' => 'Header Menu',
						'topbar-menu' => 'Topbar Menu',
						'footer-menu' => 'Footer Menu',
					)
				);
			}

			// Enable support for Post Formats
			add_theme_support( 'post-formats', array(
				'gallery',
				'video',
				'audio',
				'quote',
			) );

			// Enable support for theme image thumbnails
			add_theme_support( 'post-thumbnails' );
			add_image_size( 'keydesign-grid-image', 400, 250, true );
			add_image_size( 'keydesign-left-image', 320, 280, true );

			// Switch default core markup for search form, comment form, and comments to output valid HTML5.
			add_theme_support( 'html5', array(
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
			) );

			// Enable support for page excerpts
			add_post_type_support( 'page', 'excerpt' );
		}

	}
	add_action( 'after_setup_theme', 'keydesign_setup' );


// ------------------------------------------------------------------------
// Include plugin check, meta boxes, widgets, custom posts
// ------------------------------------------------------------------------

	// Redux theme options config
	include_once( get_template_directory() . '/core/options-init.php' );

	// Theme activation and plugin check
	include_once( get_template_directory() . '/core/theme-activation.php' );

	// Add post meta boxes
	include_once( get_template_directory() . '/core/theme-pagemeta.php' );

	// Register widgetized areas
	include_once( get_template_directory() . '/core/theme-sidebars.php' );

// ------------------------------------------------------------------------
// Content Width
// ------------------------------------------------------------------------

	if ( ! isset( $content_width ) ) $content_width = 1240;

// ------------------------------------------------------------------------
// Blog functionality
// ------------------------------------------------------------------------

	// Custom blog navigation
	function keydesign_link_attributes_1($themetek_output) {
			return str_replace('<a href=', '<a class="next" href=', $themetek_output);
	}
	function keydesign_link_attributes_2($themetek_output) {
			return str_replace('<a href=', '<a class="prev" href=', $themetek_output);
	}

	add_filter('next_post_link', 'keydesign_link_attributes_1');
	add_filter('previous_post_link', 'keydesign_link_attributes_2');

	// Comment reply script enqueued
	function keydesign_enqueue_comments_reply() {
		if( get_option( 'thread_comments' ) )  {
			wp_enqueue_script( 'comment-reply' );
		}
	}
	add_action( 'comment_form_before', 'keydesign_enqueue_comments_reply' );

	// Excerpt length
	function keydesign_excerpt_length( $length ) {
		$redux_ThemeTek = get_option( 'redux_ThemeTek' );
		if( !class_exists( 'ReduxFramework' )) {
			return 20;
		} else {
			return $redux_ThemeTek['tek-blog-excerpt'];
		}

	}
	add_filter( 'excerpt_length', 'keydesign_excerpt_length', 999 );


// ------------------------------------------------------------------------
// Output Theme Options custom code
// ------------------------------------------------------------------------

function keydesign_vc_custom_colors() {
		$redux_ThemeTek = get_option( 'redux_ThemeTek' );
		ob_start();
		include_once( get_template_directory() . '/core/colors-keydesign.css.php' );
		$keydesign_custom_colors = ob_get_clean();
    wp_add_inline_style('keydesign-style', $keydesign_custom_colors);
}
add_action('wp_enqueue_scripts', 'keydesign_vc_custom_colors');


function keydesign_custom_theme_styles() {
		$redux_ThemeTek = get_option( 'redux_ThemeTek' );
		if ( isset($redux_ThemeTek['tek-css']) ) {
			wp_add_inline_style( 'keydesign-style', $redux_ThemeTek['tek-css'] );
		}
}
add_action( 'wp_enqueue_scripts', 'keydesign_custom_theme_styles' );

function keydesign_hook_javascript() {
		$redux_ThemeTek = get_option( 'redux_ThemeTek' );
		if( ! empty( $redux_ThemeTek['tek-javascript'] ) || isset( $redux_ThemeTek['tek-javascript'] ) ) {
			wp_add_inline_script( 'keydesign-scripts', $redux_ThemeTek['tek-javascript'] );
		}
}
add_action( 'wp_enqueue_scripts', 'keydesign_hook_javascript' );

// ------------------------------------------------------------------------
// Force Visual Composer to initialize as "built into the theme".
// ------------------------------------------------------------------------

	function keydesign_vcSetAsTheme() {
		vc_set_as_theme();
	}
	add_action( 'vc_before_init', 'keydesign_vcSetAsTheme' );

// ------------------------------------------------------------------------
// Output Typekit Custom Javascript
// ------------------------------------------------------------------------

	function keydesign_custom_typekit() {
		$redux_ThemeTek = get_option( 'redux_ThemeTek' );
		if ( isset($redux_ThemeTek['tek-typekit']) && $redux_ThemeTek['tek-typekit'] != '' ) {
			wp_enqueue_script( 'keydesign-typekit', 'https://use.typekit.net/'.esc_js($redux_ThemeTek['tek-typekit']).'.js', array(), '1.0' );
   			wp_add_inline_script( 'keydesign-typekit', 'try{Typekit.load({ async: true });}catch(e){}' );
		}
	}
	add_action('wp_enqueue_scripts', 'keydesign_custom_typekit');


// ------------------------------------------------------------------------
// Theme activation
// ------------------------------------------------------------------------

   add_option( 'keydesign-verify', 'no', '', 'yes' );

// ------------------------------------------------------------------------
// Load maintenance page template
// ------------------------------------------------------------------------

add_action( 'template_include', 'keydesign_maintenance_mode', 1 );
function keydesign_maintenance_mode( $template ) {
	$redux_ThemeTek = get_option( 'redux_ThemeTek' );
	if ( ! class_exists( 'ReduxFramework' ) ) {
		return $template;
	}

	$new_template = locate_template( array( '/core/templates/maintenance-page-template.php' ) );

	if ( $redux_ThemeTek['tek-maintenance-mode'] && !is_user_logged_in() ) {
		return $new_template;
	}

	return $template;
}

// ------------------------------------------------------------------------
// Add boxed body class
// ------------------------------------------------------------------------

if (isset($redux_ThemeTek['tek-layout-style'])) {
	if ($redux_ThemeTek['tek-layout-style'] == 'boxed') {
		add_filter( 'body_class','keydesign_body_class' );
		function keydesign_body_class( $classes ) {
		   $classes[] = 'boxed';
		   return $classes;
		}
	}
}

// ------------------------------------------------------------------------
// Page transparent navigation
// ------------------------------------------------------------------------

function keydesign_transparent_nav($classes) {
		if( class_exists( 'WooCommerce' ) && is_shop() ) {
		  $post_id = wc_get_page_id( 'shop' );
		} else {
		  $post_id = get_the_ID();
		}

    $page_transparent_navigation = get_post_meta( $post_id, '_themetek_page_transparent_navbar', true );
    if ( !empty($page_transparent_navigation)) {
	    $classes[] = 'transparent-navigation';
    }
    return $classes;
}
add_filter('body_class', 'keydesign_transparent_nav');

if (isset($redux_ThemeTek['tek-blog-transparent-nav'])) {
	if ($redux_ThemeTek['tek-blog-transparent-nav'] == true) {
		add_filter( 'body_class','keydesign_blog_transparent_nav' );
			function keydesign_blog_transparent_nav( $classes ) {
				$classes[] = '';
				if (is_home() || is_search() || is_category() || is_tag() || is_author()) {
			  	$classes[] = 'transparent-navigation';
				}
		   	return $classes;
			}
	}
}

if (isset($redux_ThemeTek['tek-transparent-homepage-menu'])) {
	if ($redux_ThemeTek['tek-transparent-homepage-menu'] == true) {
		add_filter( 'body_class','keydesign_front_page_transparent_nav' );
			function keydesign_front_page_transparent_nav( $classes ) {
				$classes[] = '';
				if (is_front_page()) {
			  	$classes[] = 'transparent-navigation';
				}
		   	return $classes;
			}
	}
}

// ------------------------------------------------------------------------
// Replace blog post video structure
// ------------------------------------------------------------------------
if( class_exists( 'KEYDESIGN_ADDON_CLASS' ) ) {
	function keydesign_embed_oembed_html($html, $url, $args) {
		global $post;

		if( false !== strpos( $html, 'youtube.com' ) && has_post_thumbnail() && is_singular('post') ){
			$html = '<div class="entry-video"><div class="video-cover">
	        <div class="background-video-image">'. wp_get_attachment_image(get_post_thumbnail_id(), 'large') .'</div>
	        <div class="play-video"><span class="fa fa-play"></span></div>'. $html .'</div></div>';
		}
	    return $html;
	}
	add_filter('embed_oembed_html','keydesign_embed_oembed_html', 10, 3);
}

<?php
// ------------------------------------------------------------------------
// Register widgetized areas
// ------------------------------------------------------------------------
    function keydesign_sidebars_register() {
		register_sidebar( array(
            'name' => esc_html__( 'Blog Sidebar', 'leadengine' ),
            'id' => 'blog-sidebar',
            'description' => esc_html__( 'Add widgets for the blog sidebar area. If none added, default sidebar widgets will be used.', 'leadengine' ),
            'before_widget' => '<div class="blog_widget">',
            'after_widget' => '</div>',
            'before_title' => '<h5 class="widget-title"><span>',
            'after_title' => '</span></h5>',
        ) );
        register_sidebar( array(
            'name' => esc_html__( 'Shop Sidebar', 'leadengine' ),
            'id' => 'shop-sidebar',
            'description' => esc_html__( 'A sidebar that only appears on WooCommerce Shop pages.', 'leadengine' ),
            'before_widget' => '<div class="blog_widget">',
            'after_widget' => '</div>',
            'before_title' => '<h5 class="widget-title"><span>',
            'after_title' => '</span></h5>',
        ) );
        if ( class_exists( 'bbPress' ) ) {
            register_sidebar( array(
                'name' => esc_html__( 'bbPress Sidebar', 'leadengine' ),
                'id' => 'bbpress-sidebar',
                'description' => esc_html__( 'A sidebar that only appears on bbPress pages.', 'leadengine' ),
                'before_widget' => '<div class="blog_widget">',
                'after_widget' => '</div>',
                'before_title' => '<h5 class="widget-title"><span>',
                'after_title' => '</span></h5>',
            ) );
        }
        register_sidebar( array(
            'name' => esc_html__( 'Page Sidebar', 'leadengine' ),
            'id' => 'page-sidebar',
            'description' => esc_html__( 'Add widgets for the single page sidebar area.', 'leadengine' ),
            'before_widget' => '<div class="blog_widget">',
            'after_widget' => '</div>',
            'before_title' => '<h5 class="widget-title"><span>',
            'after_title' => '</span></h5>',
        ) );

        register_sidebar( array(
            'name' => esc_html__( 'Footer first widget area', 'leadengine' ),
            'id' => 'footer-first-widget-area',
            'description' => esc_html__( 'Add one widget for the first footer widget area.', 'leadengine' ),
            'before_widget' => '<div class="footer_widget">',
            'after_widget' => '</div>',
            'before_title' => '<h5 class="widget-title"><span>',
            'after_title' => '</span></h5>',
        ) );

        register_sidebar( array(
            'name' => esc_html__( 'Footer second widget area', 'leadengine' ),
            'id' => 'footer-second-widget-area',
            'description' => esc_html__( 'Add one widget for the second footer widget area.', 'leadengine' ),
            'before_widget' => '<div class="footer_widget">',
            'after_widget' => '</div>',
            'before_title' => '<h5 class="widget-title"><span>',
            'after_title' => '</span></h5>',
        ) );

        register_sidebar( array(
            'name' => esc_html__( 'Footer third widget area', 'leadengine' ),
            'id' => 'footer-third-widget-area',
            'description' => esc_html__( 'Add one widget for the third footer widget area.', 'leadengine' ),
            'before_widget' => '<div class="footer_widget">',
            'after_widget' => '</div>',
            'before_title' => '<h5 class="widget-title"><span>',
            'after_title' => '</span></h5>',
        ) );

        register_sidebar( array(
            'name' => esc_html__( 'Footer fourth widget area', 'leadengine' ),
            'id' => 'footer-fourth-widget-area',
            'description' => esc_html__( 'Add one widget for the fourth footer widget area.', 'leadengine' ),
            'before_widget' => '<div class="footer_widget">',
            'after_widget' => '</div>',
            'before_title' => '<h5 class="widget-title"><span>',
            'after_title' => '</span></h5>',
        ) );
    }

    add_action( 'widgets_init', 'keydesign_sidebars_register' );
?>

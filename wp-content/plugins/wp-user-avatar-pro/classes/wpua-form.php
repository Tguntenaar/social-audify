<?php

if ( ! class_exists( 'WPUAP_FORM' ) ) {

	class WPUAP_FORM extends FlipperCode_HTML_Markup {

		function __construct( $options = array() ) {

			$productOverview = array(
				'subscribe_mailing_list' => esc_html__( 'Subscribe to our mailing list', 'wp-user-avatar-pro' ),
				'product_info_heading' => esc_html__( 'Product Information', 'wp-user-avatar-pro' ),
				'product_info_desc' => esc_html__( 'For our each product we have set up demo pages where you can see the plugin in working mode.', 'wp-user-avatar-pro' ),
				'live_demo_caption' => esc_html__( 'Live Demos', 'wp-user-avatar-pro' ),
				'installed_version' => esc_html__( 'Installed version :', 'wp-user-avatar-pro' ),
				'latest_version_available' => esc_html__( 'Latest Version Available : ', 'wp-user-avatar-pro' ),
				'updates_available' => esc_html__( 'Update Available', 'wp-user-avatar-pro' ),
				'subscribe_now' => array(
					'heading' => esc_html__( 'Subscribe Now', 'wp-user-avatar-pro' ),
					'desc1' => esc_html__( 'Receive updates on our new product features and new products effortlessly.', 'wp-user-avatar-pro' ),
					'desc2' => esc_html__( 'We will not share your email addresses in any case.', 'wp-user-avatar-pro' ),
				),
				'product_support' => array(
					'heading' => esc_html__( 'Product Support', 'wp-user-avatar-pro' ),
					'desc' => esc_html__( 'For our each product we have very well explained starting guide to get you started in matter of minutes.', 'wp-user-avatar-pro' ),
					'click_here' => esc_html__( ' Click Here', 'wp-user-avatar-pro' ),
					'desc2' => esc_html__( 'For our each product we have set up demo pages where you can see the plugin in working mode. You can see a working demo before making a purchase.', 'wp-user-avatar-pro' ),
				),
				'refund' => array(
					'heading' => esc_html__( 'Get Refund', 'wp-user-avatar-pro' ),
					'desc' => esc_html__( 'Please click on the below button to initiate the refund process.', 'wp-user-avatar-pro' ),
					'request' => esc_html__( 'Request a Refund', 'wp-user-avatar-pro' ),
				),
				'support' => array(
					'heading' => esc_html__( 'Extended Technical Support', 'wp-user-avatar-pro' ),
					'desc1' => esc_html__( 'We provide technical support for all of our products. You can opt for 12 months support below.', 'wp-user-avatar-pro' ),
					'link' => esc_html__( 'Extend support', 'wp-user-avatar-pro' ),
					'link2' => esc_html__( 'Get Extended Licence', 'wp-user-avatar-pro' ),
				),

			);

			$productInfo = array(
				'productName' => esc_html__( 'WP User Avatar Pro', 'wp-user-avatar-pro' ),
				'productSlug' => 'wp-user-avatar-pro',
				'productTagLine' => esc_html__( 'WP User Avatar Pro - an excellent product that allows users to upload any custom user avatar even through web-cam with the facility of cropping and resizing avatar before saving', 'wp-user-avatar-pro' ),
				'productTextDomain' => 'wp-user-avatar-pro',
				'productIconImage' => WPUAP_URL . 'core/core-assets/images/wp-poet.png',
				'productVersion' => WPUAP_VERSION,
				'videoURL' => 'https://www.youtube.com/watch?v=CXUQNZLw_bE&list=PLlCp-8jiD3p3DtZ-2ZubVqwyOV1NTgEOn',
				'docURL' => 'http://guide.flippercode.com/avatar/',
				'demoURL' => 'http://www.flippercode.com/product/wp-user-avatar/',
				'productImagePath' => WPUAP_URL . 'core/core-assets/product-images/',
				'productSaleURL' => 'https://codecanyon.net/item/wp-user-avatar-pro/15638832',
				'multisiteLicence' => 'https://codecanyon.net/item/wp-user-avatar-pro/15638832?license=extended&open_purchase_for_item_id=15638832&purchasable=source',
				'productOverview' => $productOverview,
			);

			$productInfo = array_merge( $productInfo, $options );
			parent::__construct( $productInfo );

		}

	}

}

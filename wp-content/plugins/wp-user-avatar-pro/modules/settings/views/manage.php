<?php
/**
 * This class used to manage settings page in backend.
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 5.0.0
 * @package Avatar
 */
$avatar_obj = new WPUA_Avatar();
$data = $avatar_obj->avatar_settings();
// Server upload size limit
$upload_size_limit = wp_max_upload_size();
// Convert to KB
if ( $upload_size_limit > 1024 ) {
	$upload_size_limit /= 1024;
}
$upload_size_limit_with_units = (int) $upload_size_limit . 'KB';
// User upload size limit
if ( ( $wpua_user_upload_size_limit = $data['wp_user_avatar_upload_size_limit'] ) == 0 || $data['wp_user_avatar_upload_size_limit'] > wp_max_upload_size() ) {
	$wpua_user_upload_size_limit = wp_max_upload_size();
}
// Value in bytes
$wpua_upload_size_limit = $wpua_user_upload_size_limit;
// Convert to KB
if ( isset( $wpua_user_upload_size_limit ) && $wpua_user_upload_size_limit > 1024 ) {
	$wpua_user_upload_size_limit /= 1024;
}
$wpua_upload_size_limit_with_units = (int) $wpua_user_upload_size_limit . 'KB';
// Check for custom image sizes
$all_sizes = array_merge( get_intermediate_image_sizes(), array( 'original' ) );
$form  = new WPUAP_FORM();
$form->set_header( esc_html__( 'General Settings', 'wp-user-avatar-pro' ), $response );

$form->add_element(
	'checkbox', 'wp_user_avatar_upload_registration', array(
		'lable' => esc_html__( 'Display on Registration Page', 'wp-user-avatar-pro' ),
		'value' => 1,
		'current' => ( isset( $data['wp_user_avatar_upload_registration'] ) && ! empty( $data['wp_user_avatar_upload_registration'] ) ) ? $data['wp_user_avatar_upload_registration'] : 0,
		'default_value' => 0,
		'desc' => esc_html__( 'Allow to upload Avatar at registration page', 'wp-user-avatar-pro' ),
	)
);

$form->add_element(
	'checkbox', 'wp_user_avatar_settings[woo_edit_profile]', array(
		'lable' => esc_html__( 'Hide on Woocommerce Edit Profile', 'wp-user-avatar-pro' ),
		'value' => 1,
		'current' => ( isset( $data['wp_user_avatar_settings']['woo_edit_profile'] ) && ! empty( $data['wp_user_avatar_settings']['woo_edit_profile'] ) ) ? $data['wp_user_avatar_settings']['woo_edit_profile'] : 0,
		'default_value' => 0,
		'desc' => esc_html__( 'Remove upload Avatar control from woocommerce edit profile page', 'wp-user-avatar-pro' ),
	)
);

$form->add_element(
	'checkbox', 'wp_user_avatar_hide_webcam', array(
		'lable' => esc_html__( 'Hide Webcam', 'wp-user-avatar-pro' ),
		'value' => 1,
		'current' => ( isset( $data['wp_user_avatar_hide_webcam'] ) && ! empty( $data['wp_user_avatar_hide_webcam'] ) ) ? $data['wp_user_avatar_hide_webcam'] : 0,
		'default_value' => 0,
		'desc' => esc_html__( 'Hide webcam option on upload avatar window', 'wp-user-avatar-pro' ),
	)
);
$form->add_element(
	'checkbox', 'wp_user_avatar_hide_mediamanager', array(
		'lable' => esc_html__( 'Hide Media', 'wp-user-avatar-pro' ),
		'value' => 1,
		'current' => ( isset( $data['wp_user_avatar_hide_mediamanager'] ) && ! empty( $data['wp_user_avatar_hide_mediamanager'] ) ) ? $data['wp_user_avatar_hide_mediamanager'] : 0,
		'default_value' => 0,
		'desc' => esc_html__( 'Hide media manager option on upload avatar window', 'wp-user-avatar-pro' ),
	)
);
$form->add_element(
	'group', 'WPUAP_avatar_directory', array(
		'value' => esc_html__( 'Avatar Settings', 'wp-user-avatar-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);
$storage_options = array(
	'media' => esc_html__( 'Media Uploader', 'wp-user-avatar-pro' ),
	'directory' => esc_html__( 'Custom Directory', 'wp-user-avatar-pro' ),
	'aws' => esc_html__( 'Amazon S3 Storage', 'wp-user-avatar-pro' ),
	'dropbox' => esc_html__( 'Dropbox Storage', 'wp-user-avatar-pro' ),
);
$form->add_element(
	'radio', 'avatar_storage_option', array(
		'lable' => esc_html__( 'Avatar Storage', 'wp-user-avatar-pro' ),
		'radio-val-label' => $storage_options,
		'current' => isset( $data['avatar_storage_option'] ) ? $data['avatar_storage_option'] : '',
		'class' => 'chkbox_class',
		'default_value' => 'media',
	)
);
$form->add_element(
	'text', 'wp_user_avatar_storage[directory]', array(
		'lable' => esc_html__( 'Folder Path', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_storage']['directory'] ) ? $data['wp_user_avatar_storage']['directory'] : '',
		'desc' => esc_html__( 'Upload directory for Avatar. Default folder is wp-content/uploads/wp-user-avatar/ ', 'wp-user-avatar-pro' ),
		'default_value' => 'wp-content/uploads/wp-user-avatar/',
		'show' => ( $data['avatar_storage_option'] == 'directory' ) ? 'true' : 'false',
		'before' => '<div class="fc-8 wp_storage_directory">',
		'after' => '</div>',
	)
);
$form->add_element(
	'text', 'wp_user_avatar_storage[setting][aws][key]', array(
		'lable' => esc_html__( 'Access Key', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_storage']['setting']['aws']['key'] ) ? $data['wp_user_avatar_storage']['setting']['aws']['key'] : '',
		'desc' => sprintf( esc_html__( 'Amazon Web Services Access Key. Follow instruction given %s ', 'wp-user-avatar-pro' ), '<a href="' . esc_url( 'http://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSGettingStartedGuide/AWSCredentials.html' ) . '">' . esc_html__( 'here', 'wp-user-avatar-pro' ) . '</a>.' ),
		'default_value' => '',
		'before' => '<div class="fc-8 wp_storage_aws">',
		'after' => '</div>',
		'show' => ( $data['avatar_storage_option'] == 'aws' ) ? 'true' : 'false',
	)
);
$form->add_element(
	'text', 'wp_user_avatar_storage[setting][aws][secret_key]', array(
		'lable' => esc_html__( 'Secret Key', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_storage']['setting']['aws']['secret_key'] ) ? $data['wp_user_avatar_storage']['setting']['aws']['secret_key'] : '',
		'desc' => sprintf( esc_html__( 'Amazon Web Services Secret Key. Follow instruction given %s', 'wp-user-avatar-pro' ), '<a href="http://docs.aws.amazon.com/AWSSimpleQueueService/latest/SQSGettingStartedGuide/AWSCredentials.html">' . esc_html__( 'here', 'wp-user-avatar-pro' ) . '</a>.' ),
		'default_value' => '',
		'before' => '<div class="fc-8 wp_storage_aws">',
		'after' => '</div>',
		'show' => ( $data['avatar_storage_option'] == 'aws' ) ? 'true' : 'false',
	)
);
$form->add_element(
	'text', 'wp_user_avatar_storage[aws]', array(
		'lable' => esc_html__( 'Bucket Name', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_storage']['aws'] ) ? $data['wp_user_avatar_storage']['aws'] : '',
		'desc' => esc_html__( 'Enter bucket name.', 'wp-user-avatar-pro' ),
		'default_value' => '',
		'before' => '<div class="fc-8 wp_storage_aws">',
		'after' => '</div>',
		'show' => ( $data['avatar_storage_option'] == 'aws' ) ? 'true' : 'false',
	)
);
$form->add_element(
	'html', 'wp_user_avatar_html', array(
		'html' => '<ul style="color:#777777;">
<li>' . sprintf( esc_html__( 'Step 1 : Create a dropbox application %s', 'wp-user-avatar-pro' ), '<a target="_blank" href="https://www.dropbox.com/developers/apps/create">' . esc_html__( 'Here', 'wp-user-avatar-pro' ) . '</a>' ) . '</li>
<li>' . sprintf( esc_html__( 'Step 2 : Go to your %s', 'wp-user-avatar-pro' ), '<a target="_blank" href="https://www.dropbox.com/developers/apps">' . esc_html__( 'Application', 'wp-user-avatar-pro' ) . '</a>' ) . '</li>
<li>' . esc_html__( 'Step 3 : Configure your application and get generated access token.', 'wp-user-avatar-pro' ) . '</li>
</ul>',
		'before' => '<div class="fc-8 wp_storage_dropbox">',
		'after' => '</div>',
		'show' => ( $data['avatar_storage_option'] == 'dropbox' ) ? 'true' : 'false',
		'lable' => '&nbsp;',
	)
);
$form->add_element(
	'text', 'wp_user_avatar_storage[dropbox][access_token]', array(
		'lable' => esc_html__( 'Access Token', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_storage']['dropbox']['access_token'] ) ? $data['wp_user_avatar_storage']['dropbox']['access_token'] : '',
		'desc' => esc_html__( 'Dropbox app generated access token.', 'wp-user-avatar-pro' ),
		'default_value' => '',
		'before' => '<div class="fc-8 wp_storage_dropbox">',
		'after' => '</div>',
		'show' => ( $data['avatar_storage_option'] == 'dropbox' ) ? 'true' : 'false',
	)
);
$form->add_element(
	'text', 'wp_user_avatar_storage[dropbox][upload_path]', array(
		'lable' => esc_html__( 'Folder Name', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_storage']['dropbox']['upload_path'] ) ? $data['wp_user_avatar_storage']['dropbox']['upload_path'] : '',
		'desc' => esc_html__( 'Enter for folder name where to upload avatar. Leave empty to upload in app root.', 'wp-user-avatar-pro' ),
		'default_value' => '',
		'before' => '<div class="fc-8 wp_storage_dropbox">',
		'after' => '</div>',
		'show' => ( $data['avatar_storage_option'] == 'dropbox' ) ? 'true' : 'false',
	)
);
$form->add_element(
	'text', 'wp_user_avatar_upload_size_limit', array(
		'lable' => esc_html__( 'Upload File Size', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_upload_size_limit'] ) ? $data['wp_user_avatar_upload_size_limit'] : '',
		'desc' => sprintf( esc_html__( 'Maximum upload file size: %1$d%2$s.', 'wp-user-avatar-pro' ), esc_html( wp_max_upload_size() ), esc_html( ' bytes (' . format_size_units( wp_max_upload_size() ) . ')' ) ),
		'default_value' => wp_max_upload_size(),
	)
);
$form->add_element(
	'text', 'wp_user_avatar_upload_size_width', array(
		'lable' => esc_html__( 'Upload File Width', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_upload_size_width'] ) ? $data['wp_user_avatar_upload_size_width'] : '',
		'desc' => esc_html__( 'Maximum upload file width. Leave it blank for no limit.', 'wp-user-avatar-pro' ),
	)
);
$form->add_element(
	'text', 'wp_user_avatar_upload_size_height', array(
		'lable' => esc_html__( 'Upload File Height', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_upload_size_height'] ) ? $data['wp_user_avatar_upload_size_height'] : '',
		'desc' => esc_html__( 'Maximum upload file height. Leave it blank for no limit.', 'wp-user-avatar-pro' ),
	)
);
$form->set_col( 2 );
$form->add_element(
	'text', 'wp_user_avatar_thumbnail_w', array(
		'lable' => esc_html__( 'Avatar Width', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_thumbnail_w'] ) ? $data['wp_user_avatar_thumbnail_w'] : '',
		'desc' => esc_html__( 'Avatar width in pixels.', 'wp-user-avatar-pro' ),
		'default_value' => '150',
	)
);
$form->add_element(
	'text', 'wp_user_avatar_thumbnail_h', array(
		'lable' => esc_html__( 'Avatar Height', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_thumbnail_h'] ) ? $data['wp_user_avatar_thumbnail_h'] : '',
		'desc' => esc_html__( 'Avatar height in pixels.', 'wp-user-avatar-pro' ),
		'default_value' => '150',
	)
);
$form->set_col( 1 );
$form->add_element(
	'checkbox', 'wp_user_avatar_resize_upload', array(
		'lable' => esc_html__( 'Resize avatars on upload', 'wp-user-avatar-pro' ),
		'value' => 1,
		'current' => ( isset( $data['wp_user_avatar_resize_upload'] ) && ! empty( $data['wp_user_avatar_resize_upload'] ) ) ? $data['wp_user_avatar_resize_upload'] : 0,
		'default_value' => 0,
		'desc' => esc_html__( 'Check if resize your cropped image to fit the above width and height.', 'wp-user-avatar-pro' ),
	)
);
$form->add_element(
	'checkbox', 'show_avatars', array(
		'lable' => esc_html__( 'Show Avatar', 'wp-user-avatar-pro' ),
		'value' => 1,
		'current' => ( isset( $data['show_avatars'] ) && ! empty( $data['show_avatars'] ) ) ? $data['show_avatars'] : '',
		'default_value' => 1,
		'desc' => esc_html__( 'Uncheck to hide the avatar.', 'wp-user-avatar-pro' ),
	)
);
// avatar_default
$default_url = isset( $data['default_avatar_url'] ) ? esc_url( $data['default_avatar_url'] ) : '';
$mystery_url = isset( $data['mystery_url'] ) ? esc_url( $data['mystery_url'] ) : '';
$gravatar_default_url = isset( $data['gravatar_default_url'] ) ? esc_url( $data['gravatar_default_url'] ) : '';
$identicon_url = isset( $data['identicon_url'] ) ? esc_url( $data['identicon_url'] ) : '';

$wavatar_url = isset( $data['wavatar_url'] ) ? esc_url( $data['wavatar_url'] ) : '';
$monsterid_url = isset( $data['monsterid_url'] ) ? esc_url( $data['monsterid_url'] ) : '';
$retro_url = isset( $data['retro_url'] ) ? esc_url( $data['retro_url'] ) : '';


$avatar_defaults = array(
	'wp_user_avatar' => '<div id="wpua-preview"><img src="' . esc_attr( $default_url ) . '" width="32" id="wp-user-avatar-img"></div>&nbsp;' . esc_html__( 'WP User Avatar', 'wp-user-avatar-pro' ) . '&nbsp;<button type="button" data-target="wp-user-avatar-img" data-source="wp-user-avatar" class="button ci_choose_image" data-ip-modal="#default_avatarModal" name="wpua-add" data-avatar_default="true" data-title="' . esc_html__( 'Choose Image: Default Avatar', 'wp-user-avatar-pro' ) . '">' . esc_html__( 'Choose Image', 'wp-user-avatar-pro' ) . '</button>',

	'mystery' => '<img src="' . esc_attr( $mystery_url ) . '" />&nbsp;&nbsp;' . esc_html__( 'Mystery Man', 'wp-user-avatar-pro' ),
	'blank' => '&nbsp;&nbsp;' . esc_html__( 'Blank', 'wp-user-avatar-pro' ),
	'gravatar_default' => '<img src="' . $gravatar_default_url . '" />&nbsp;&nbsp;' . esc_html__( 'Gravatar Logo', 'wp-user-avatar-pro' ),

	'identicon' => '<img src="' . esc_attr( $identicon_url ) . '" />&nbsp;&nbsp;' . esc_html__( 'Identicon', 'wp-user-avatar-pro' ),
	'wavatar' => '<img src="' . esc_attr( $wavatar_url ) . '" />&nbsp;&nbsp; ' . esc_html__( 'Wavatar', 'wp-user-avatar-pro' ),
	'monsterid' => '<img src="' . esc_attr( $monsterid_url ) . '" />&nbsp;&nbsp;' . esc_html__( 'MonsterID', 'wp-user-avatar-pro' ),
	'retro' => '<img src="' . esc_attr( $retro_url ) . '" />&nbsp;&nbsp;' . esc_html__( 'Retro', 'wp-user-avatar-pro' ),
	'letter_based' => '&nbsp;&nbsp;' . esc_html__( 'First Letter Avatar', 'wp-user-avatar-pro' ),
);
$form->add_element(
	'radio', 'avatar_default', array(
		'lable' => esc_html__( 'Default Avatar', 'wp-user-avatar-pro' ),
		'radio-val-label' => $avatar_defaults,
		'current' => isset( $data['avatar_default'] ) ? $data['avatar_default'] : '',
		'class' => 'chkbox_class default-avatar-listing',
		'default_value' => 'mystery',
		'display_mode' => 'radio-vertical',
	)
);
$roles = get_editable_roles();
$roles_avatar_html = '';
foreach ( $roles as $key => $role ) {
	$default_avatar_url = isset( $data[ $data['avatar_default'] . '_url' ] ) ? $data[ $data['avatar_default'] . '_url' ] : '';
	$role_avatar_url = ! empty( $data['role_based_avatar'][ $key ] ) ? $data['wpua_upload_url'] . $data['role_based_avatar'][ $key ] : $default_avatar_url;
	if ( isset( $_SERVER['HTTPS'] ) && ( 'on' == $_SERVER['HTTPS'] || 1 == $_SERVER['HTTPS'] ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && 'https' == $_SERVER['HTTP_X_FORWARDED_PROTO'] ) {
		$role_avatar_url = str_replace( 'http://', 'https://', $role_avatar_url );
	}

	$role_name = isset( $role['name'] ) ? $role['name'] : '';
	$role_based_av = isset( $data['role_based_avatar'][ $key ] ) ? $data['role_based_avatar'][ $key ] : '';


	$roles_avatar_html .= '<div class="fc-12"><div id="wpua-preview"><img src="' . esc_attr( esc_url( $role_avatar_url ) ) . '" width="32" id="wp-user-role-avatar-img' . esc_attr( $key ) . '"></div> <div class="fc-4" style="color: rgba(0,0,0,.57);">' . $role_name . '</div><button type="button" data-target="wp-user-role-avatar-img' . esc_attr( $key ) . '" data-source="role-based-avatar" class="button ci_choose_image role_based_avatar" data-ip-modal="#default_avatarModal" name="wpua-add" data-avatar_default="true" data-role="' . esc_attr( $key ) . '" data-title="' . esc_attr( esc_html__( 'Choose Image: Role Avatar', 'wp-user-avatar-pro' ) ) . '">' . esc_attr( esc_html__( 'Choose Image', 'wp-user-avatar-pro' ) ) . '</button>
		<input type="hidden" name="role_based_avatar[' . esc_attr( $key ) . ']" class="role-based-avatar" value="' . esc_attr( $role_based_av ) . '">
	</div>';
}
$form->add_element(
	'html', 'role_based_avatar', array(
		'lable' => esc_html__( 'Role Based Avatar', 'wp-user-avatar-pro' ),
		'html'  => $roles_avatar_html,
		'class' => 'role-based-avatar-listing',
		'show' => ( $data['avatar_default'] == 'wp_user_avatar' ) ? 'true' : 'false',
	)
);
$form->add_element(
	'group', 'WPUAP_avatar_gravatar', array(
		'value' => esc_html__( 'Gravatar Setting', 'wp-user-avatar-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);
$form->add_element(
	'checkbox', 'wp_user_avatar_disable_gravatar', array(
		'lable' => esc_html__( 'Disable Gravatar', 'wp-user-avatar-pro' ),
		'value' => 1,
		'current' => ( isset( $data['wp_user_avatar_disable_gravatar'] ) && ! empty( $data['wp_user_avatar_disable_gravatar'] ) ) ? $data['wp_user_avatar_disable_gravatar'] : 0,
		'default_value' => 0,
		'desc' => esc_html__( 'Disable Gravatar and use only local avatars', 'wp-user-avatar-pro' ),
	)
);
$ratings = array(
	'G' => esc_html__( 'G — Suitable for all audiences', 'wp-user-avatar-pro' ),
	'PG' => esc_html__( 'PG — Possibly offensive, usually for audiences 13 and above', 'wp-user-avatar-pro' ),
	'R' => esc_html__( 'R — Intended for adult audiences above 17', 'wp-user-avatar-pro' ),
	'X' => esc_html__( 'X — Even more mature than above', 'wp-user-avatar-pro' ),
);
$form->add_element(
	'radio', 'avatar_rating', array(
		'lable' => esc_html__( 'Maximum Rating', 'wp-user-avatar-pro' ),
		'radio-val-label' => $ratings,
		'current' => isset( $data['avatar_rating'] ) ? $data['avatar_rating'] : '',
		'class' => 'chkbox_class',
		'default_value' => 'G',
		'display_mode' => 'radio-vertical',
	)
);
$form->add_element(
	'group', 'WPUAP_avatar_display', array(
		'value' => esc_html__( 'Apperance Settings', 'wp-user-avatar-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);
$form->add_element(
	'text', 'wp_user_avatar_settings[theme_color]', array(
		'lable' => esc_html__( 'Editor Theme', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_settings']['theme_color'] ) ? $data['wp_user_avatar_settings']['theme_color'] : '',
		'class' => 'color {pickerClosable:true} form-control',
		'desc' => esc_html__( 'Choose color for the icons and modal window.', 'wp-user-avatar-pro' ),
		'before' => '<div class="fc-4">',
		'default_value' => '#0073AA',
		'after' => '</div>',
	)
);
$form->add_element(
	'checkbox', 'wp_user_avatar_settings[apply_predefined_design]', array(
		'lable' => esc_html__( 'Use Predefined Color Schema', 'wp-user-avatar-pro' ),
		'value' => 'true',
		'current' => isset( $data['wp_user_avatar_settings']['apply_predefined_design'] ) ? $data['wp_user_avatar_settings']['apply_predefined_design'] : '',
		'desc' => esc_html__( 'Use predefined color schema.', 'wp-user-avatar-pro' ),
		'class' => 'chkbox_class switch_onoff',
		'data' => array( 'target' => '.wpuap_design_listing' ),
	)
);
$color_schema = array(
	'#29B6F6' => "<span class='wpua-color-schema' style='background-color:#29B6F6'></span>",
	'#212F3D' => "<span class='wpua-color-schema' style='background-color:#212F3D'></span>",
	'#dd3333' => "<span class='wpua-color-schema' style='background-color:#dd3333'></span>",
	'#FF7043' => "<span class='wpua-color-schema' style='background-color:#FF7043'></span>",
	'#FFC107' => "<span class='wpua-color-schema' style='background-color:#FFC107'></span>",
	'#9C27B0' => "<span class='wpua-color-schema' style='background-color:#9C27B0'></span>",
	'#673AB7' => "<span class='wpua-color-schema' style='background-color:#673AB7'></span>",
	'#3F51B5' => "<span class='wpua-color-schema' style='background-color:#3F51B5'></span>",
	'#00BCD4' => "<span class='wpua-color-schema' style='background-color:#00BCD4'></span>",
	'#009688' => "<span class='wpua-color-schema' style='background-color:#009688'></span>",
	'#4CAF50' => "<span class='wpua-color-schema' style='background-color:#4CAF50'></span>",
	'#FF9800' => "<span class='wpua-color-schema' style='background-color:#FF9800'></span>",
	'#FF5722' => "<span class='wpua-color-schema' style='background-color:#FF5722'></span>",
	'#795548' => "<span class='wpua-color-schema' style='background-color:#795548'></span>",
	'#9E9E9E' => "<span class='wpua-color-schema' style='background-color:#9E9E9E'></span>",
);
$form->add_element(
	'radio', 'wp_user_avatar_settings[color_schema]', array(
		'lable' => esc_html__( 'Choose Color Schema', 'wp-user-avatar-pro' ),
		'radio-val-label' => $color_schema,
		'current' => isset( $data['wp_user_avatar_settings']['color_schema'] ) ? $data['wp_user_avatar_settings']['color_schema'] : '',
		'class' => 'chkbox_class wpuap_design_listing',
		'show' => 'false',
		'default_value' => '4.png',
	)
);
$form->add_element(
	'textarea', 'wp_user_avatar_settings[custom_css]', array(
		'lable' => esc_html__( 'Custom CSS', 'wp-user-avatar-pro' ),
		'value' => $data['wp_user_avatar_settings']['custom_css'],
		'class' => 'form-control',
		'desc' => esc_html__( 'Custom css for modals and avatar image.', 'wp-user-avatar-pro' ),

	)
);
$form->add_element(
	'group', 'WPUAP_avatar_overlays', array(
		'value' => esc_html__( 'Advanced Settings', 'wp-user-avatar-pro' ),
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	)
);
$form->set_col( 1 );
$form->add_element(
	'checkbox', 'wp_user_avatar_settings[link_profile]', array(
		'lable' => esc_html__( 'Add Link to Profile Image', 'wp-user-avatar-pro' ),
		'value' => 'true',
		'current' => isset( $data['wp_user_avatar_settings']['link_profile'] ) ? $data['wp_user_avatar_settings']['link_profile'] : '',
		'desc' => esc_html__( 'Add a link to user profile.', 'wp-user-avatar-pro' ),
		'class' => 'chkbox_class switch_onoff',
		'data' => array( 'target' => '.avatar_link_setting' ),
	)
);
$form->add_element(
	'text', 'wp_user_avatar_settings[link_url]', array(
		'lable' => esc_html__( 'Profile Link', 'wp-user-avatar-pro' ),
		'value' => isset( $data['wp_user_avatar_settings']['link_url'] ) ? $data['wp_user_avatar_settings']['link_url'] : '',
		'class' => '  form-control avatar_link_setting',
		'desc' => esc_html__( 'Use {website_url} or custom link eg. http://www.flippercode.com', 'wp-user-avatar-pro' ),
		'before' => '<div class="fc-6">',
		'show' => 'false',
		'after' => '</div>',
	)
);
$form->add_element(
	'submit', 'WPUAP_save_settings', array(
		'value' => esc_html__( 'Save Setting', 'wp-user-avatar-pro' ),
	)
);
$form->add_element(
	'hidden', 'avatar_default_wp_user_avatar', array(
		'value' => $data['avatar_default_wp_user_avatar'],
		'id' => 'wp-user-avatar-url',
	)
);
$form->add_element(
	'hidden', 'wpua_mustache_url', array(
		'value' => '',
	)
);
$form->add_element(
	'hidden', 'operation', array(
		'value' => 'save',
	)
);
$form->add_element(
	'hidden', 'page_options', array(
		'value' => 'WPUAP_api_key,WPUAP_scripts_place',
	)
);
$form->render();

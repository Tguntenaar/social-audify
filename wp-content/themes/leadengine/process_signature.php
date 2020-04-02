<?php
// WordPress environment
require( dirname(__FILE__) . '/../../../wp-load.php' );

include(dirname(__FILE__)."/dashboard/services/connection.php");
include(dirname(__FILE__)."/dashboard/controllers/user_controller.php");
include(dirname(__FILE__)."/dashboard/models/user.php");

$user_id = get_current_user_id();

$connection = new connection;
$user_control = new user_controller($connection);
$user =  $user_control->get($user_id);

$wordpress_upload_dir = wp_upload_dir();
// went to uploads/2019/12/
// maar we willen dat niet want dan moet je door al die mappen heen zoeken
// of de user al een image heeft..
$signature_directory = $wordpress_upload_dir['basedir'] . "/signatures";
// $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05, for multisite works good as well
// $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
 
$signature = $_FILES['mail-signature'];

$file_name = $signature['name'];
$path_parts = pathinfo($file_name);
$file_extension = $path_parts['extension'];
$new_file_name = $user_id . "_signature." . $file_extension;

$new_file_path = $signature_directory . '/' . $new_file_name;
$new_file_mime = mime_content_type( $signature['tmp_name'] );

if( empty( $signature ) )
	die( 'File is not selected.' );
 
if( $signature['error'] )
  die("No signature selected");
 
if( $signature['size'] > wp_max_upload_size() )
  wp_redirect( "https://". $_SERVER["HTTP_HOST"] . "/profile-page/#mail-settings?error=size");

if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
	die( 'WordPress doesn\'t allow this type of uploads.' );

// looks like everything is OK
if( move_uploaded_file( $signature['tmp_name'], $new_file_path ) ) {
  if ($user->signature != 0) {
    // wp_delete_attachment_files($user->signature, array $meta, array $backup_sizes, string $file );
    $force_delete = true;
    wp_delete_attachment($user->signature, $force_delete);
  } 

  var_dump($new_file_path);
  var_dump($new_file_mime);
  var_dump($new_file_name);

	$upload_id = wp_insert_attachment( array(
		'guid'           => $new_file_path, 
		'post_mime_type' => $new_file_mime,
		'post_title'     => preg_replace( '/\.[^.]+$/', '', $new_file_name ),
		'post_content'   => '',
		'post_status'    => 'inherit'
  ), False, 0, True);
  
  var_dump($upload_id);
  // TODO:
	// wp_generate_attachment_metadata() won't work if you do not include this file
	// require_once( ABSPATH . 'wp-admin/includes/image.php' );
	// Generate and save the attachment metas into the database
	// wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );
  
  $user->update("User", "signature", $upload_id);

	// Show the uploaded file in browser
  // wp_redirect( $wordpress_upload_dir['baseurl'] . '/signatures'. '/' . basename( $new_file_path ) );
  // wp_redirect( "https://". $_SERVER["HTTP_HOST"] . "/profile-page/#mail-settings");
}
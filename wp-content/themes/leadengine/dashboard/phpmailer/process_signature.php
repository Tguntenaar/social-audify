<?php
 
// WordPress environment
require( dirname(__FILE__) . '/../../../wp-load.php' );
 
$wordpress_upload_dir = wp_upload_dir();
var_dump($wordpress_upload_dir);
// went to uploads/2019/12/
// maar we willen dat niet want dan moet je door al die mappen heen zoeken
// of de user al een image heeft..
$signature_directory = $wordpress_upload_dir['basedir'] . "/signatures";
// $wordpress_upload_dir['path'] is the full server path to wp-content/uploads/2017/05, for multisite works good as well
// $wordpress_upload_dir['url'] the absolute URL to the same folder, actually we do not need it, just to show the link to file
$i = 1; // number of tries when the file with the same name is already exists
 
$signature = $_FILES['mail-signature'];


$file_name = $signature['name'];
$path_parts = pathinfo($file_name);
var_dump($path_parts);
$extension = $path_parts['extension'];
$new_file_name = get_current_user_id() . "_signature." . $extension;

$new_file_path = $signature_directory . '/' . $new_file_name;
$new_file_mime = mime_content_type( $signature['tmp_name'] );
 
if( empty( $signature ) )
	die( 'File is not selected.' );
 
if( $signature['error'] )
	die( $signature['error'] );
 
if( $signature['size'] > wp_max_upload_size() )
	die( 'It is too large than expected.' );
 
if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
	die( 'WordPress doesn\'t allow this type of uploads.' );
 
if ( file_exists($new_file_path) ) {
  // dont put in trash
  $force_delete = true;
  $post_id = 0; // TODO:
  wp_delete_attachment($post_id, $force_delete);
}

// while( file_exists( $new_file_path ) ) {
// 	$i++;
// 	$new_file_path = $signature_directory . '/' . $i . '_' . $new_file_name;
// }
 
// looks like everything is OK
if( move_uploaded_file( $signature['tmp_name'], $new_file_path ) ) {
  
	$upload_id = wp_insert_attachment( array(
		'guid'           => $new_file_path, 
		'post_mime_type' => $new_file_mime,
		'post_title'     => preg_replace( '/\.[^.]+$/', '', $new_file_name ),
		'post_content'   => '',
		'post_status'    => 'inherit'
	), $new_file_path );
 
	// wp_generate_attachment_metadata() won't work if you do not include this file
	// require_once( ABSPATH . 'wp-admin/includes/image.php' );
 
	// Generate and save the attachment metas into the database
	// wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );
  
  // TODO: redirect to profile-page/#mail-settings
	// Show the uploaded file in browser
	wp_redirect( $wordpress_upload_dir['baseurl'] . '/signatures'. '/' . basename( $new_file_path ) );
}
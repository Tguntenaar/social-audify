<form action="" method="POST" >
	<input type="text" name="purchase-code" placeholder="Purchase Code Here">
	<input type="submit" value="Activate"/>
</form>

<?php

if(isset($_POST['purchase-code'])) {
	$product_code = sanitize_text_field($_POST['purchase-code']);
	$url = "http://api.leadengine-wp.com/activate/0e751835-4410-11e8-bf31-00163e6818fb?code=".$product_code;
	$curl = curl_init($url);

	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	    'Referer: '.get_site_url()
    ));

	$envatoRes = curl_exec($curl);
	curl_close($curl);
	$envatoRes = json_decode($envatoRes);

	if ( isset($envatoRes->activated) && ($envatoRes->activated == true)) {   
	    // $data = $envatoRes->message;
	    update_option( 'keydesign-verify', 'yes' );
	    wp_redirect(admin_url( 'admin.php?page=leadengine-dashboard'));
		exit;

	} else {  
		$data = "Your purchase code is not valid. Please provide a valid purchase code";
	    update_option( 'keydesign-verify', 'no' );
	} ?>

	<span class='kdadmin-code-error'> <?php echo esc_html($data); ?> </span> <?php

} 	

?>



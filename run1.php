<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
require_once( dirname(__FILE__) . '/functions.php' );

set_time_limit(3600); 

if( isset( $_POST["count"] ) ) {
	$count = $_POST["count"];
} else {
	$count = null;
}

$links = getCacheLinks();
$options = get_option('gen_cache_options');


$contents = get_data( $links[$count] );
slaap( $options['gen_cache_speed'] );
unset( $contents );

$count++;

$genflag = WP_PLUGIN_DIR . '/generate-cache/generation_running';
if ( ( $count != null ) && ( $count <= count( $links ) ) && ( file_exists( $genflag ) ) ) {
	touch( $genflag );
	$url = WP_PLUGIN_URL . '/generate-cache/run2.php';
	$params = array( 'count' => $count );
	$asynchronous_call = curl_post_async( $url, $params );
	if( $asynchronous_call == FALSE ) {
		if( file_exists( $genflag ) )
			unlink( $genflag );
	}
} else {
	if( file_exists( $genflag ) )
		unlink( $genflag );
}
?>
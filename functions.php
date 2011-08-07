<?php
function initCache() {
	$genflag = WP_PLUGIN_DIR . '/generate-cache/generation_running';
	touch( $genflag );
	$links = getCacheLinks();
	$options = get_option('gen_cache_options');
	$options['gen_cache_cur_cache_dir'] = $links[0];
	update_option( 'gen_cache_options', $options );
	$url = WP_PLUGIN_URL . '/generate-cache/run1.php';
	$params = array( 'count' => 0 );
	$asynchronous_call = curl_post_async( $url, $params );
}

function get_data($url) {
	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

function dirSize($directory) { 
	$size = 0; 
		foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file){ 
		 $size+=$file->getSize(); 
	} 
	return $size; 
}
function getCacheLinks() {
	$options = get_option('gen_cache_options');
	$links = array();
	if ( $options['gen_cache_home'] == 1 ) {
		$links[] = get_bloginfo('url');
	}
	if ( $options['gen_cache_posts'] == 1 ) {
		$posts = query_posts( 'posts_per_page=10000' );
		if ($posts) {
			foreach ($posts as $post) {
				$links[] = get_permalink( $post->ID );
			}
		}
	}
	if ( $options['gen_cache_cats'] == 1 ) {
		$category_ids = get_all_category_ids();
		foreach($category_ids as $cat_id) {
			$links[] = get_category_link($cat_id);
		}
	}
	if ( $options['gen_cache_tags'] == 1 ) {
		$tags = get_tags();
		if ($tags) {
			foreach ($tags as $tag) {
				$links[] = get_tag_link( $tag->term_id );
			}
		}
	}
	if ( $options['gen_cache_pages'] == 1 ) {
		$querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'page' ORDER BY $wpdb->posts.post_title ASC";
		
		global $wpdb;
		$pageposts = $wpdb->get_results($querystr, OBJECT);

			$querystr = "SELECT $wpdb->posts.* FROM $wpdb->posts WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'page' ORDER BY $wpdb->posts.post_title ASC";

			$pageposts = $wpdb->get_results($querystr, OBJECT);

			if ($pageposts):
				foreach ($pageposts as $post):
					setup_postdata($post);
					$links[] = get_page_link( $post->ID );
				endforeach;
			else :
				echo '';
			endif;

	}
	return $links;
}

function format_bytes($size) {
	$units = array(' B', ' KB', ' MB', ' GB', ' TB');
	for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
	return round($size, 2).$units[$i];
}


function curl_post_async($url, $params = null) { 
	if($params) { 
		foreach ($params as $key => &$val) { 
			if (is_array($val)) $val = implode(',', $val); 
				$post_params[] = $key.'='.urlencode($val); 
		} 
		if($post_params) $post_string = implode('&', $post_params); 
	}

	$parts=parse_url($url);

	$fp = fsockopen($parts['host'], 
	isset($parts['port'])?$parts['port']:80, 
	$errno, $errstr, 30);

	if($fp) { 
		$out = "POST ".$parts['path']." HTTP/1.1\r\n"; 
		$out.= "Host: ".$parts['host']."\r\n"; 
		$out.= "Content-Type: application/x-www-form-urlencoded\r\n"; 
		$out.= "Content-Length: ".strlen($post_string)."\r\n"; 
		$out.= "Connection: Close\r\n\r\n"; 
		if (isset($post_string)) $out.= $post_string;
		fwrite($fp, $out); 
		fclose($fp);
		return true; 
	} else { 
		return false; 
	} 
}
?>
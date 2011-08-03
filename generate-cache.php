<?php
/*
Plugin Name: Generate Cache
Plugin URI: http://www.denisbuka.ru/generate-cache/
Description: When your cache is emptied (say, upon a new post or comment publication), the plugin loops through selected items (posts, categories, tags or pages) and makes sure you have them all freshly cached for quicker access.
Version: 0.1
Author: Denis Buka
Author URI: http://www.denisbuka.ru

Copyright (C) 2011 www.denisbuka.ru

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public Licensealong with this program. If not, see <http://www.gnu.org/licenses/>.
*/

register_activation_hook(__FILE__, 'gen_cache_add_defaults');
register_uninstall_hook(__FILE__, 'gen_cache_delete_plugin_options');
add_action('admin_init', 'gen_cache_init' );
add_action('admin_menu', 'gen_cache_add_options_page');

function gen_cache_delete_plugin_options() {
	delete_option('gen_cache_options');
}

function gen_cache_add_defaults() {
	$tmp = get_option('gen_cache_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('gen_cache_options'); 
		$arr = array(	
						"gen_cache_size" => "300000",
						"gen_cache_home" => "1",
						"gen_cache_posts" => "1",
						"gen_cache_cats" => "1",
						"gen_cache_tags" => "1",
						"gen_cache_pages" => "1",
						"gen_cache_dir" => "",
						"gen_cache_cur_cache_dir" => "",
						"gen_cache_final_dir" => "",
						"gen_cache_user_dir" => ""
		);
		update_option('gen_cache_options', $arr);
	}
}

function gen_cache_init(){
	register_setting( 'gen_cache_plugin_options', 'gen_cache_options', 'gen_cache_validate_options' );
}

function gen_cache_add_options_page() {
	add_options_page('Generate Cache Options', 'Generate Cache', 'manage_options', __FILE__, 'gen_cache_render_form');
}

function gen_cache_render_form() {
	?>
	<div class="wrap">
		
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Generate Cache</h2>
		<p>Generate Cache settings can be tweaked here to your liking.</p>

		<form method="post" action="options.php">
			<?php settings_fields('gen_cache_plugin_options'); ?>
			<?php $options = get_option('gen_cache_options'); ?>

			<table class="form-table">

				<tr valign="top">
					<th scope="row"><strong>Select items to be cached:</strong></th>
					<td>
						<label><input name="gen_cache_options[gen_cache_home]" type="checkbox" value="1" <?php if (isset($options['gen_cache_home'])) { checked('1', $options['gen_cache_home']); } ?> /> Home</label><br />

						<label><input name="gen_cache_options[gen_cache_posts]" type="checkbox" value="1" <?php if (isset($options['gen_cache_posts'])) { checked('1', $options['gen_cache_posts']); } ?> /> Posts</label><br />

						<label><input name="gen_cache_options[gen_cache_cats]" type="checkbox" value="1" <?php if (isset($options['gen_cache_cats'])) { checked('1', $options['gen_cache_cats']); } ?> /> Categories</label><br />

						<label><input name="gen_cache_options[gen_cache_tags]" type="checkbox" value="1" <?php if (isset($options['gen_cache_tags'])) { checked('1', $options['gen_cache_tags']); } ?> /> Tags</label><br />

						<label><input name="gen_cache_options[gen_cache_pages]" type="checkbox" value="1" <?php if (isset($options['gen_cache_pages'])) { checked('1', $options['gen_cache_pages']); } ?> /> Pages</label><br />

					</td>
				</tr>
				
				<tr>
					<th scope="row" style="width:270px;"><strong>Cache size low limit:</strong><br /><em>(if cache size drops below the specified value it will be automatically generated)</em></th>
					<td>
						<label><input style="text-align:right;" type="text" size="15" name="gen_cache_options[gen_cache_size]" value="<?php echo $options['gen_cache_size']; ?>" /> bytes&nbsp;&nbsp;&nbsp;<em>( = <?php echo format_bytes( $options['gen_cache_size'] ); ?> )</em></label>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><strong>Your current cache folder location:</strong></th>
					<td>
					<?php foreach(glob( WP_CONTENT_DIR . '/*', GLOB_ONLYDIR) as $dir) {
							if ( ( stripos ( $dir, "cache" ) !== FALSE ) ) { ?>
								<label><input name="gen_cache_options[gen_cache_dir]" type="radio" value="<?php echo $dir; ?>" <?php checked($dir, $options['gen_cache_dir']); ?> /></label> <code><?php echo $dir; ?></code> &ndash; <strong>Size</strong>: <?php echo dirSize( $dir ); ?> bytes<br />
							<?php }
						} ?>
							
							<label><input name="gen_cache_options[gen_cache_dir]" type="radio" value="differ" <?php checked('differ', $options['gen_cache_dir']); ?> /> My cache folder is at a different location:</label><br />
							<label><input style="text-align:left;" type="text" size="80" name="gen_cache_options[gen_cache_user_dir]" value="<?php echo $options['gen_cache_user_dir']; ?>" /></label>
							<?php if( $options['gen_cache_dir'] == "differ" ) { ?>
								<?php if( is_dir( $options['gen_cache_user_dir'] ) ) { ?>
								&nbsp;&ndash; <strong>Size</strong>: <?php echo dirSize( $options['gen_cache_user_dir'] ); ?> bytes
								<?php $options['gen_cache_dir'] = $options['gen_cache_user_dir']; ?>
								<?php } else { ?>
								<?php $options['gen_cache_dir'] = null; ?>
								<br /><span style="color:red;">The directory you've specified appears to be non-existent.</span>
								<?php } ?>
							<?php } ?> 
							<br /><em>(full directory path)</em>

					</td>
				</tr>
				
			</table>
			<?php 	
				$options['gen_cache_final_dir'] = $options['gen_cache_dir'];
				update_option( 'gen_cache_options', $options ); 
			?>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

	</div>
	<?php	
}

function gen_cache_validate_options($input) {
	return $input;
}

add_filter( 'plugin_action_links', 'gen_cache_plugin_action_links', 10, 2 );
function gen_cache_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$gen_cache_links = '<a href="'.get_admin_url().'options-general.php?page=generate-cache/generate-cache.php">'.__('Settings').'</a>';
		array_unshift( $links, $gen_cache_links );
	}

	return $links;
}

require_once( dirname(__FILE__) . '/functions.php' );

add_action('wp_footer', 'triggerCache');
function triggerCache() {
	$options = get_option('gen_cache_options');
	if ( isset( $options['gen_cache_final_dir'] ) ) {
		$genflag = WP_PLUGIN_DIR . '/generate-cache/generation_running';
		if( file_exists( $genflag ) ) { 
			$filetime = filemtime( $genflag );
			$timeout = time()-30; 
			if ($filetime <= $timeout) {
				unlink( $genflag );
			} else {
				echo '<!-- Cache generation is running... -->';
			}
		} else {
			if ( dirSize( $options['gen_cache_final_dir'] ) < $options['gen_cache_size'] ) {
				echo "<!-- Let's generate some cache! -->";
				touch( $genflag );
				$links = getCacheLinks();
				$options['gen_cache_cur_cache_dir'] = $links[0];
				update_option( 'gen_cache_options', $options );
				$url = WP_PLUGIN_URL . '/generate-cache/run1.php';
				$params = array( 'count' => 0 );
				$asynchronous_call = curl_post_async( $url, $params );
			} else {
			echo "<!-- Cache size is sufficient... -->";
			}
		}
	}
}



?>

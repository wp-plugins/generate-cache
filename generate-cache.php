<?php
/*
Plugin Name: Generate Cache
Plugin URI: http://steamingkettle.net/web-design/wordpress-plugins/
Description: When your cache is emptied (say, upon a new post or comment publication), the plugin loops through selected items (posts, categories, tags or pages) and makes sure you have them all freshly cached for quicker access.
Version: 0.3
Author: Denis Buka
Author URI: http://steamingkettle.net

Copyright (C) 2011 SteamingKettle.net

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public Licensealong with this program. If not, see <http://www.gnu.org/licenses/>.
*/



require_once( dirname(__FILE__) . '/functions.php' );

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
						"gen_cache_speed" => "1",
						"gen_cache_dir" => "",
						"gen_cache_final_dir" => "",
						"gen_cache_user_dir" => "",
						"gen_cache_time_hr" => "",
						"gen_cache_time_min" => "",
						"gen_cache_freq" => ""
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
				
				<tr>
					<th scope="row" style="width:270px;"><strong>Caching speed:</strong><br /><em>(interval between subsequent page calls)</em></th>
					<td>
						<label><input style="text-align:right;" type="text" size="4" name="gen_cache_options[gen_cache_speed]" value="<?php echo $options['gen_cache_speed']; ?>" /> seconds&nbsp;&nbsp;&nbsp;<em>&ndash; <strong>Warning:</strong> setting this to less than a second may impede your server's performance!</em></label>
					</td>
				</tr>

				<tr>
					<th scope="row" style="width:270px;"><strong>Schedule cache generation:</strong><br /><em>(you can optionally schedule cache generation to be run periodically even if cache folder size is above limit)</em></th>
					<td>
						<label>
							<em>Start at</em>&nbsp;&nbsp;
							<input style="text-align:right;width:25px;" type="text" size="2" name="gen_cache_options[gen_cache_time_hr]" value="<?php echo $options['gen_cache_time_hr']; ?>" />&nbsp;:&nbsp;<input style="text-align:right;width:25px;" type="text" size="2" name="gen_cache_options[gen_cache_time_min]" value="<?php echo $options['gen_cache_time_min']; ?>" />
							&nbsp;&nbsp;<em>and run</em>&nbsp;&nbsp;
						</label>

						<select name='gen_cache_options[gen_cache_freq]'>
							<option value='hourly' <?php selected('hourly', $options['gen_cache_freq']); ?>>Hourly</option>
							<option value='twicedaily' <?php selected('twicedaily', $options['gen_cache_freq']); ?>>Twice daily</option>
							<option value='daily' <?php selected('daily', $options['gen_cache_freq']); ?>>Daily</option>
						</select>&nbsp;&nbsp;&nbsp;<em>(examples: 15:30, 03:55, 00:15)</em>
						<br /><span><em>(leave blank to disable)</em></span>
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
						
		<?php
		if( isset( $_POST['gen_cache_now'] ) ) initCache();

		$genflag = WP_PLUGIN_DIR . '/generate-cache/generation_running';
		if( isset( $_POST['gen_cache_abort'] ) ) unlink( $genflag );
		if( file_exists( $genflag ) ) { 
			echo '	<form method="post">
						<p class="submit"><strong>Cache generation is now in progress...</strong>&nbsp;&nbsp;&nbsp;
						<input type="submit" name="gen_cache_abort" value="Abort" />
						</p>
					</form>';
		} else {
			echo '	<form method="post">
						<p class="submit">You can start cache generation manually by hitting this button:&nbsp;&nbsp;&nbsp;
						<input type="submit" name="gen_cache_now" value="Generate cache now!" />
						</p>
					</form>';
		}
		?>
		<br />
		<hr />
		<h3>My other plugins:</h3>
		<ul>
			<li><a href="http://wordpress.org/extend/plugins/intuitive-navigation/">Intuitive Navigation</a></li>   
			<li><a href="http://wordpress.org/extend/plugins/drop-in-dropbox/">Drop in Dropbox</a></li>   
		</ul>
	</div>
	<?php	
}

add_action( 'gen_cache_hook', 'gen_cache_hook' );
function gen_cache_hook() {
	initCache();
}

function gen_cache_validate_options($input) {
	if( ( trim( $input['gen_cache_time_hr'] ) != "" ) && ( trim( $input['gen_cache_time_min'] ) != "" ) ) {
		$offset = get_option('gmt_offset') * 3600;
		$now = time();
		$midnight = $now - ( $now%86400 );
		$converted = strtotime( trim( $input['gen_cache_time_hr'] ) . ":" . trim( $input['gen_cache_time_min'] ) );
		$converted = ($converted - $offset)%86400 + $midnight;
		if( $converted > $now ) {
			$start = $converted;
		} else {
			$start = $midnight + ( $converted%86400 ) + 86400;
		}

		$timestamp = wp_next_scheduled( 'gen_cache_hook' );
		wp_unschedule_event($timestamp, 'gen_cache_hook' );
		
		if (!wp_next_scheduled('gen_cache_hook')) {
			wp_schedule_event( $start, $input['gen_cache_freq'], 'gen_cache_hook' );
		}
	} else {
		$timestamp = wp_next_scheduled( 'gen_cache_hook' );
		wp_unschedule_event($timestamp, 'gen_cache_hook' );
	}
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
				initCache();
			} else {
			echo "<!-- Cache size is sufficient... -->";
			}
		}
	}
}



?>

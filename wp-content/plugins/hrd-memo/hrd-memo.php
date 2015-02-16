<?php
/*
 Plugin Name: HRD Memo
 Description: Memo
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdmemo
 Domain Path: /languages*/
function hrd_memo() {

	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$plugins = get_plugins();
//		var_dump($plugins);

	echo '<table>';
		echo '<tr>';
		echo '<th>Name</th>';
		echo '<th>Description</th>';
		echo '<th>Active</th>';
		echo '</tr>';
		foreach ($plugins as $plugin_file => $plugin) {
//			var_dump($plugin);
			if($plugin[Author] == "HRD System Works") {
				echo '<tr>';
				echo '<td>';
				echo $plugin[Name];
//				var_dump($plugin);
				echo '</td>';
				echo '<td>';
				echo $plugin[Description];
				echo '</td>';
				echo '<td>';
				if(is_plugin_active($plugin_file)){
					echo 'Active';
				}
				echo '</td>';
				echo '</tr>';
			}
		}
		echo '</table>';
	}

}
add_shortcode('hrd_memo', 'hrd_memo');
?>
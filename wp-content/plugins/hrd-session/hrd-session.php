<?php
/*
 Plugin Name: HRD 0 Session Control Function
 Description: Empty
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdsession
 Domain Path: /languages
*/

function hrd_disp_session($content) {
//	$content = "<p>hoge</p>" . $content;
	echo "session:".session_id()."<br>";
	echo "_SESSION"; var_dump($_SESSION);
	return $content;
}
add_filter('the_content', 'hrd_disp_session');

function hrd_session_destroy() {
	session_destroy();
}
add_action('wp_logout', 'hrd_session_destroy');

function hrd_init_session(){
	if(!session_id()){
		session_start();
	}
}
add_action('init', 'hrd_init_session');
?>
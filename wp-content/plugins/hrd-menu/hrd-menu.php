<?php
/*
 Plugin Name: HRD 5 Menu Control Function
 Description: DashBoardでlogin,Logoutのメニューを作成し、表示したいページを追加する。単独起動可
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdmenu
 Domain Path: /languages
*/

// メニュー表示
// How to Show Different Menus to Logged in Users in WordPress
// http://www.wpbeginner.com/wp-themes/how-to-show-different-menus-to-logged-in-users-in-wordpress/
function hrd_wp_nav_menu_args( $args = '' ) {

	if( is_user_logged_in() ) {
		$args['menu'] = 'Login';
	} else {
		$args['menu'] = 'Logout';
	}

	return $args;
}
add_filter( 'wp_nav_menu_args', 'hrd_wp_nav_menu_args' );

?>

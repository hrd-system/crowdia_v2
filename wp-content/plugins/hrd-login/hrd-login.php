<?php
/*
 Plugin Name: HRD 4 Login Function
 Description: ThemeMyLoginの代わりにログイン、ログアウト画面を作成する。hrd_core.phpが必須
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdlogin
 Domain Path: /languages
*/

// フロントエンド
/* ページ作成 */
function hrd_login_args($slug=NULL) {
	$page_login = array(
			array('post_type'=>'page','title'=>'ログイン','slug'=>'login','parent'=>'0',	'post_status'=>1,'content'=>'[hrd_login_disp]'),
	);
	$page_logout = array(
			array('post_type'=>'page','title'=>'ログアウト','slug'=>'logout','parent'=>'0',	'post_status'=>1,'content'=>'[hrd_logout_disp]'),
	);

	if($slug == "login"){
		$page_args = $page_login;
	}elseif ($slug == "logout"){
		$page_args = $page_logout;
	}else{
		$page_args = array_merge($page_login , $page_logout);
	}
	return $page_args;
}

function hrd_create_login_page() {

	$page_args = hrd_login_args();
	foreach ($page_args as $page_arg) {

		if(function_exists(hrd_chack_page)) {
			if(hrd_chack_page($page_arg['post_type'], $page_arg['title'])) {
				// echo "pageあり<br>";
			}else{
				// echo "pageなし<br>";
				$return = hrd_create_page($page_arg);
//				echo "Page作成<br>";
			}
		}
	}
}
//add_shortcode('hrd_create_login_page', 'hrd_create_login_page');
register_activation_hook(__FILE__, 'hrd_create_login_page'); // プラグインが有効化されたときに実行される関数を登録


function hrd_delete_login_page() {
	$page_args = hrd_login_args();
	foreach ($page_args as $page_arg) {
		if(function_exists(hrd_chack_page)) {
			if(hrd_chack_page($page_arg['post_type'], $page_arg['title'])) {
				// echo "pageあり<br>";
//				echo "Page削除<br>";
				$return = hrd_delete_page($page_arg);
			}else{
				// echo "pageなし<br>";
			}
		}
	}
}
//add_shortcode('hrd_delete_login_page', 'hrd_delete_login_page');
register_deactivation_hook(__FILE__, 'hrd_delete_login_page'); // プラグインが停止されたときに実行される関数を登録

// ログイン
function hrd_login_signnon() {
	global $hrd_error;

//	$users = get_userdata(get_current_user_id());
	$users= wp_get_current_user();
	$user_role = array_shift($users->roles);
//	if( $user_role == "pendding" ) {
//		wp_redirect(home_url());
//		wp_safe_redirect(home_url());
//		exit;
//	}

	if(!(is_admin())) {
		// Login
		if($_REQUEST['action'] == "login") {
			$creds = array();
			$creds['user_login']	= $_REQUEST['id'];
			$creds['user_password']	= $_REQUEST['pass'];
			$creds['remember'] = true;

			if(function_exists(wp_signon)) {
				$user = wp_signon( $creds, false );
			}
			if ( is_wp_error($user) ) {
//				echo $user->get_error_message();
				$hrd_error = $user->get_error_message();
			}else{
				$hrd_error = "";
				$user_info =  wp_set_current_user($user->ID);

			//	return $user;
				wp_redirect(home_url());
				exit();
			}
		}

		// Logout
		if($_REQUEST['action'] == "logout") {
			if(!is_admin()) {
				wp_logout();
				$user_info =  wp_set_current_user(0);

				wp_redirect(home_url());
				exit;
			}
		}
	}
}
add_action('init', 'hrd_login_signnon');

function hrd_login_disp() {
	global $hrd_error;
/*	if(is_user_logged_in()) {
		echo '<h3>ログアウト</h3>';
		hrd_logout_form();
	}else{
		echo '<h3>ログイン</h3>';
		hrd_login_form();
	}*/
	echo '<h3>ログイン</h3>';
	if(!empty($hrd_error)) {
		echo $hrd_error;
	}

	hrd_login_form();

//	echo '<hr>';
//	wp_loginout();
}
add_shortcode('hrd_login_disp', 'hrd_login_disp');

function hrd_login_form(){
	echo '<form action="" method="POST">';
	echo '<table>';
		echo '<tr>';
			echo '<td>ID</td>';
			echo '<td><input type="text" name="id"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>Password</td>';
			echo '<td><input type="password" name="pass"></td>';
		echo '</tr>';
	echo '</table>';
	echo '<input type="hidden" name="action" value="login">';
	echo '<input type="submit" value="Login">';
	echo '</form>';
}

// ログアウト
// ダイレクトでログアウト
function hrd_direct_logout() {
	$slug_name = basename(get_permalink());
	if($slug_name == "logout") {
		wp_logout();
		wp_safe_redirect( home_url() );
		exit();
	}
}
add_action('init', 'hrd_direct_logout' );

// ログアウトボタン
function hrd_logout_disp() {

		echo '<h3>ログアウト</h3>';
		hrd_logout_form();
}
add_shortcode('hrd_logout_disp', 'hrd_logout_disp');

function hrd_logout_form() {
	echo '<form action="" method="POST">';
		echo '<input type="hidden" name="action" value="logout">';
		echo '<input type="submit" value="Logout">';
	echo '</form>';

	echo '<form action="'.home_url().'" method="POST">';
		echo '<input type="hidden" name="action" value="logout">';
		echo '<input type="submit" value="Cancel">';
	echo '</form>';

}
?>
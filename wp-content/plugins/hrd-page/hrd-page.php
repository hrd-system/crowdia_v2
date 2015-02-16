<?php
/*
 Plugin Name: HRD 3 Create Page Function
 Description: ページ作成ツール。有効化でhomeページのみ作成。無効でページ削除。
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdpage
 Domain Path: /languages
*/
function hrd_debug() {
	echo "DEBUGは、hrd-page.php<br>";
	if(function_exists('hrd_memo')) {
		echo "hrd_memo<br>";
		hrd_memo();
	}
}
add_shortcode('hrd_debug', 'hrd_debug');

function hrd_page_args() {
	$page_args = array(
		array('post_type'=>'page','title'=>'home','slug'=>'home','parent'=>'0',	'post_status'=>1,'content'=>''),
	);
	return $page_args;
}

function hrd_create_home_page() {

	$page_args = hrd_page_args();
	foreach ($page_args as $page_arg) {

		if(function_exists(hrd_chack_page)) {
			if(hrd_chack_page($page_arg['post_type'], $page_arg['title'])) {
				// echo "pageあり<br>";
			}else{
				// echo "pageなし<br>";
				$return = hrd_create_page($page_arg);
				//echo "Page作成<br>";
			}
		}
	}
}
add_shortcode('hrd_create_home_page', 'hrd_create_home_page');
register_activation_hook(__FILE__, 'hrd_create_home_page'); // プラグインが有効化されたときに実行される関数を登録

function hrd_delete_home_page() {
	$page_args = hrd_page_args();
	foreach ($page_args as $page_arg) {
		$page_id = get_page_by_path($page_arg['slug'],OBJECT,'page')->ID;
		if(!empty($page_id)) {
			$delete = wp_delete_post($page_id,$force_delete = true);	// force_delete:ゴミ箱への移動ではなく、完全に削除する
		}
	}
}
add_shortcode('hrd_delete_home_page', 'hrd_delete_home_page');
register_deactivation_hook(__FILE__, 'hrd_delete_home_page'); // プラグインが停止されたときに実行される関数を登録

function hrd_page_url(){
	$page_data = get_page(get_the_ID());
	$page_slug = $page_data->post_name;

	if(function_exists('hrd_get_page_url')){
		$page_url = hrd_get_page_url($page_slug);
		echo "PageUrl:".$page_url;
	}
}
add_shortcode('hrd_page_url', 'hrd_page_url');
?>
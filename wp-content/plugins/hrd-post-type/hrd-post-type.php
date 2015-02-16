<?php
/*
 Plugin Name: HRD 2 Post Type Function
 Description: HRD Function Using PostType Function.
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdposttype
 Domain Path: /languages
*/

function hrd_check_post_type_args() {

	$post_args = array(
			array('post_type'	=> 'user_detail',	'label'	=> 'ユーザー詳細' ), // ユーザー詳細
			array('post_type'	=> 'job',			'label'	=> '案件情報' ), // 案件情報
			array('post_type'	=> 'entry',			'label'	=> '応募情報' ), // 応募情報
			array('post_type'	=> 'time',			'label'	=> 'タイムカード' ), // タイムカード
			array('post_type'	=> 'message',		'label'	=> 'メッセージ' ), // メッセージ
	);

	return $post_args;
}

function hrd_create_post_type() {
	$hrd_args = hrd_check_post_type_args();
	$hrd_arg = array();
	foreach ($hrd_args as $hrd_arg) {
		if(!(post_type_exists($hrd_arg['post_type']))) {
	//		echo "なし<br>";
			if(function_exists(hrd_post_type_register)) {
				hrd_post_type_register($hrd_arg['post_type'], $hrd_arg['label']);
			}
	//		$error = new WP_Error();
		}
	}
}
//register_activation_hook(__FILE__, 'hrd_create_post_type'); // プラグインが有効化されたときに実行される関数を登録
add_action('init','hrd_create_post_type');

/*
function hrd_type_page_args() {
	$page_args = array(
			array('post_type'=>'posttype1','title'=>'テスト１タイプ','slug'=>'test1-type','parent'=>'0',	'post_status'=>1,'content'=>''),
			array('post_type'=>'posttype1','title'=>'テスト１タイプ１','slug'=>'test1-type1','parent'=>'test1-type',	'post_status'=>1,'content'=>''),
			array('post_type'=>'posttype1','title'=>'テスト１タイプ２','slug'=>'test1-type2','parent'=>'test1-type',	'post_status'=>1,'content'=>''),

			array('post_type'=>'posttype2','title'=>'テスト２タイプ','slug'=>'test2-type','parent'=>'0',	'post_status'=>1,'content'=>''
					,'meta'=>array(array('meta_key'=>'posttype1','meta_value'=>'テスト１タイプ'))),
			array('post_type'=>'posttype2','title'=>'テスト２タイプ１','slug'=>'test2-type1','parent'=>'test2-type',	'post_status'=>1,'content'=>''
					,'meta'=>array(array('meta_key'=>'posttype1','meta_value'=>'テスト１タイプ１'))),
			array('post_type'=>'posttype2','title'=>'テスト２タイプ２','slug'=>'test2-type2','parent'=>'test2-type',	'post_status'=>1,'content'=>''
					,'meta'=>array(array('meta_key'=>'posttype1','meta_value'=>'テスト１タイプ２'))),
	);
	return $page_args;
}

function hrd_post_type() {
	echo "hrd_post_type<br>";
	$hrd_args = hrd_check_post_type_args();

	$page_type_args = hrd_type_page_args();

	foreach ($page_type_args as $page_type_arg) {
		if(post_type_exists($page_type_arg['post_type'])){
			if(hrd_chack_page($page_type_arg['post_type'], $page_type_arg['title'])) {
//				echo "ページ:".$page_type_arg['title']."あり<br>";
			}else{
//				echo "ページ:".$page_type_arg['title']."なし<br>";
				if(function_exists('hrd_create_page')) {
					$return = hrd_create_page($page_type_arg);
//					echo "Page作成<br>";
				}
			}
		}else{
//			echo "post_typeなし<br>";
		}
	}

	if(function_exists('hrd_page_join')) {
		$return_join = hrd_page_join('posttype1','posttype2');
//var_dump($return_join);
	}
}
add_shortcode('hrd_post_type', 'hrd_post_type');
*/

?>
<?php
/*
 Plugin Name: HRD 9 Message Function
 Description: Empty
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrddevelop
 Domain Path: /languages
*/

// フロントエンド
/* ページ作成 */
function hrd_message_args() {
	$page_args = array(
			array('post_type'=>'page','title'=>'Message',		'slug'=>'message',		'parent'=>'0',			'post_status'=>1,'content'=>'[hrd_message]'), // メッセージ
			array('post_type'=>'page','title'=>'MessageDetail',	'slug'=>'messagedetail','parent'=>'message',	'post_status'=>1,'content'=>'[hrd_message_detail]'), // メッセージ詳細
			array('post_type'=>'page','title'=>'MessageSend',	'slug'=>'messagesend',	'parent'=>'message',	'post_status'=>1,'content'=>'[hrd_message_send]'), // メッセージ送信
	);
	return $page_args;
}

function hrd_create_message_page() {

	$page_args = hrd_message_args();
	foreach ($page_args as $page_arg) {

		if(function_exists(hrd_chack_page)) {
			if(!(hrd_chack_page($page_arg['post_type'], $page_arg['title']))) {
				// echo "pageなし<br>";
				$return = hrd_create_page($page_arg);
				//echo "Page作成<br>";
			}
		}
	}
}
// プラグインが有効化されたときに実行される関数を登録
register_activation_hook(__FILE__, 'hrd_create_message_page');

function hrd_delete_message_page() {
	$page_args = hrd_message_args();
	foreach ($page_args as $page_arg) {
		$page_id = get_page_by_path($page_arg['slug'],OBJECT,'page')->ID;
		if(!empty($page_id)) {
			// force_delete:ゴミ箱への移動ではなく、完全に削除する
			$delete = wp_delete_post($page_id,$force_delete = true);
		}
	}
}
// プラグインが停止されたときに実行される関数を登録
register_deactivation_hook(__FILE__, 'hrd_delete_message_page');

// メタ情報
function hrd_message_meta_args() {
	$message_meta_args = array(
		array("id"	=> 'dist_id',	"name"	=> 'Destination',	"type"	=> 'text', 	"must"	=> false), //送信先
		array("id"	=> 'email',		"name"	=> 'email',			"type"	=> 'text', 	"must"	=> false), //送信先
		array("id"	=> 'open',		"name"	=> 'Kaifeng',		"type"	=> 'text', 	"must"	=> false), //開封
		array("id"	=> 'job_id',	"name"	=> 'JobNo',			"type"	=> 'text', 	"must"	=> false), //案件番号
	);

	return $message_meta_args;
}
function hrd_message_status_args(){
	$status = array(
					'yet'		=> array(__('Yet','crowdia')),		// 未開封
					'already'	=> array(__('Already','crowdia'))	// 開封済
	);
}


// 更新処理
function hrd_message_init() {
	$request = $_REQUEST;

}
//add_action('init', 'hrd_message_init');

// メッセージトップ
function hrd_message() {
	echo "Request";var_dump($_REQUEST);

	hrd_message_init();

	$paged = get_query_var('paged') ? get_query_var('paged') : 1 ;
	$request = $_REQUEST;

	if( ($request[doing] == "ReciveList") OR (empty($request[doing])) ){
		$action = "recive";
	}
	if($request[doing] == "SendList") {
		$action = "send";
	}

	$recive_message_args = array(
		'post_type'	=>	'message',
		'meta_query' => array(
			array(
					'key'		=> 'dist_id',
					'value'		=> get_current_user_id(),
					'compare'	=> '='
			)
		)
	);
	$send_message_args = array(
		'post_type'	=>	'message',
		'author'	=> get_current_user_id(),
	);

	$messages_args = array();
	if($action == "send"){
		$message_args = $send_message_args;
	}
	if($action == "recive"){
		$message_args = $recive_message_args;
	}
	$messages = new WP_Query($message_args);

//var_dump($messages);
	echo '<form action="" method="POST">';
	echo '<input type="hidden" name="action" value="message">';
	echo '<input type="submit" name="doing" value="ReciveList">';
	echo '<input type="submit" name="doing" value="SendList">';
	echo '</form>';

	echo '<table>';
	echo '<caption>';
	if($action == "send"){
		echo 'SendMessages';
	}else{
		echo 'ReciveMessages';
	}
	echo '</caption>';
	echo '<tr>';
	echo '<th>SendDate</th>';
	echo '<th>Title</th>';
	echo '<th>Destination</th>';
	echo '<th>Attachment</th>';
	echo '<th>Open</th>';
	echo '</tr>';

	if ( $messages->post_count == 0 ) {
		echo '<tr>';
		echo '<td colspan=5>Message Empty</td>';
		echo '</tr>';
	}else{
		$messages = $messages->posts;
		foreach ($messages as $message) {
			echo '<tr>';
			echo '<td>'.'</td>'; //senddate
			echo '<td>'.$message->post_title.'</td>'; // title
			echo '<td>'.'</td>'; // dist id
			echo '<td>'.'</td>'; // attachment
			echo '<td>'.'</td>'; // status
			echo '</tr>';
		}
	}
	echo '</table>';


}
add_shortcode('hrd_message', 'hrd_message');

// メッセージ詳細
function hrd_message_detail() {

}
add_shortcode('hrd_message_detail', 'hrd_message_detail');

// メッセージ送信
function hrd_message_send() {

}
add_shortcode('hrd_message_send', 'hrd_message_send');

?>
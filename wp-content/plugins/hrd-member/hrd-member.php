<?php
/*
 Plugin Name: HRD 8 Member Function
 Description: Create,delete,Member Serch etc.
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdmember
 Domain Path: /languages
*/

// フロントエンド
/* ページ作成 */
function hrd_member_args() {
	$page_args = array(
			array('post_type'=>'page','title'=>'MemberSerch',	'slug'=>'memberserch',		'parent'=>'0',	'post_status'=>1,'content'=>'[hrd_member_serch]'), // メンバー検索
			array('post_type'=>'page','title'=>'ClientSerch',	'slug'=>'clientserch',		'parent'=>'memberserch',	'post_status'=>1,'content'=>'[hrd_client_serch]'), // 依頼者検索
			array('post_type'=>'page','title'=>'DeveloperSerch','slug'=>'developerserch',	'parent'=>'memberserch',	'post_status'=>1,'content'=>'[hrd_developer_serch]'), // 開発者検索
	);
	return $page_args;
}

function hrd_create_member_page() {

	$page_args = hrd_member_args();
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
register_activation_hook(__FILE__, 'hrd_create_member_page'); // プラグインが有効化されたときに実行される関数を登録

function hrd_delete_member_page() {
	$page_args = hrd_member_args();
	foreach ($page_args as $page_arg) {
		$page_id = get_page_by_path($page_arg['slug'],OBJECT,'page')->ID;
		if(!empty($page_id)) {
			$delete = wp_delete_post($page_id,$force_delete = true);	// force_delete:ゴミ箱への移動ではなく、完全に削除する
		}
	}
}
register_deactivation_hook(__FILE__, 'hrd_delete_member_page'); // プラグインが停止されたときに実行される関数を登録


function hrd_member_serch(){
//	echo 'メンバー検索';
	echo '<form action="'.hrd_get_page_url('developerserch').'" method="POST">';
	echo '<input type="submit" name="doing" value="DeveloperSerch">';
	echo '</form>';

	echo '<form action="'.hrd_get_page_url('clientserch').'" method="POST">';
	echo '<input type="submit" name="doing" value="ClientSerch">';
	echo '</form>';

}
add_shortcode('hrd_member_serch', 'hrd_member_serch');

function hrd_client_serch(){
	echo "Request";var_dump($_REQUEST);

//	$user_type = hrd_user_meta_type();
//	$user_meta_args = hrd_user_meta_client();

/*	$user_meta_client;
	companyurl : text
	countrylocation : text
	businesstype : text
	personalname : text
	personaladdress : text
	countryabout : textarea
	note : textarea
*/
	$user_id = $_REQUEST[user_id];
	if(!empty($user_id)) {
		echo '依頼者詳細<br>';
	}else{
		echo '依頼者検索<br>';

		$user_detail_fields = hrd_user_detail_args('client');
//var_dump($user_detail_fields);

	echo '<form action="" method="POST">';
	echo '<table>';
	echo '<caption>ClientSerch</caption>';

	echo '<tr>';
	echo '<td>';
	echo 'Country';
	echo '</td>';
	echo '<td>';
	echo 'Japan';
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>';
	echo 'CompanyName';
	echo '</td>';
	echo '<td>';
	echo '<input type="text" name="companyname">';
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>';
	echo 'User or PersonName';
	echo '</td>';
	echo '<td>';
	echo '<input type="text" name="personname">';
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>';
	echo 'BusinessType';
	echo '</td>';
	echo '<td>';
	echo '<input type="text" name="businesstype">';
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td></td>';
	echo '<td>';
	echo '<input type="submit" name="action" value="Serch">';
	echo '</td>';
	echo '</tr>';

	echo '</table>';
	echo '</form>';
	}

}
add_shortcode('hrd_client_serch', 'hrd_client_serch');

function hrd_developer_serch(){
echo "Request";var_dump($_REQUEST);

//	$user_type = hrd_user_meta_type();
//	$user_meta_client = hrd_user_meta_develop();

	$user_id = $_REQUEST[user_id];
	if(!empty($user_id)) {
		$get_user_data = get_userdata($user_id);
//var_dump($get_user_data);

		echo '<table>';
		echo '<caption></caption>';
		echo '<tr>';
		echo '<td>user_name</td>';
		echo '<td>';
		echo $get_user_data->display_name;
		echo '</td>';
		echo '</tr>';

//		$user_meta_args = hrd_user_meta_develop();
		$user_meta_args = hrd_user_detail_args('developer');
		foreach ($user_meta_args as $user_meta_arg) {
			echo '<tr>';
			echo '<td>';
//			var_dump($user_meta_arg);
			echo $user_meta_arg[id];
			echo '</td>';
			echo '<td>';
			$user_meta = get_user_meta($user_id,$user_meta_arg[id]);
			echo $user_meta = array_shift($user_meta);
//			var_dump($user_meta);
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';

		if($_REQUEST[return_url]) { // 応募者を選択
			$hidden_args = array(
					'id'	=> $_REQUEST[id],
					'paged'	=> $_REQUEST[paged],
					'user_id'	=> $_REQUEST[user_id],
					'select'	=> $_REQUEST[user_id],
					'action'	=> 'select'
			);
			hrd_job_page_button($_REQUEST[return_url],$hidden_args);
		}
		if(!empty($_REQUEST[return_url])) { // 応募一覧へ戻る
			$hidden_args = array(
				'id'	=> $_REQUEST[id],
				'paged'	=> $_REQUEST[paged],
				'user_id'	=> $_REQUEST[user_id]
			);
			hrd_job_page_button($_REQUEST[return_url],$hidden_args);
		}

	} else {

	echo '開発者検索<br>';

/*	$user_meta_develop;
	nationality : select
	expertise1 : text
	skill1 : number
	expertise2 : test
	skill2 : number
	expertise3 : text
	skill3 : number
	career : textarea
	introduction : textarea
	desiredprice : number
*/
		if(!empty($_REQUEST[action])){
			$action = $_REQUEST[action];
		}
		if(!empty($_REQUEST[country])){
			$request[country] = $_REQUEST[country];
		}
		if(!empty($_REQUEST[expertise])){
			$request[expertise] = $_REQUEST[expertise];
		}
		if(!empty($_REQUEST[skill])){
			$request[skill] = $_REQUEST[skill];
		}
		if(!empty($_REQUEST[desiredprice])){
			$request[desiredprice] = $_REQUEST[desiredprice];
		}
		if(!empty($_REQUEST[freeword])){
			$request[freeword] = $_REQUEST[freeword];
		}

		$user_detail_fields = hrd_user_detail_args('develop');
		if($action == "Serch") {
			$member_serch_args = array(
				'post_type'	=> 'user_detail',
				'mata_query'	=> array(
				)
			);
			//Member Serch
			$member_serch_metas = array(
					'key'		=> 'status',
					'value'		=> 'client',
					'compare'	=> '='
			);
			if(!empty($request[country])) {

			}
			if(!empty($request[expertise])) {
			}
			if(!empty($request[skill])) {
			}
			if(!empty($request[desiredprice])) {
			}
			if(!empty($request[freeword])) {
			}
			$member_metas = array($member_serch_metas);
//var_dump($member_metas);
			array_push($member_serch_args, $member_metas);
var_dump($member_serch_args);
			$user_details = new WP_Query($member_serch_args);
//var_dump($user_details);
			if( $user_details->post_count == 0 ) {
				echo "Empty";
			}else{
				$user_details = $user_details->posts;
				foreach ($user_details as $user_detail) {
//var_dump($user_data)		;
				echo $user_detail->post_author;
				$user_data = get_userdata($user_detail->post_author);
				echo ",".$user_data->user_nicename;
//					$develops = get_post_meta($user_data->post_author);
//var_dump($develops);
				var_dump( get_post_meta($user_detail->ID,'status',true) );
					echo "<br>";
				}
			}
			wp_reset_query();


		}else{
			echo '<form action="" method="POST">';
			echo '<table>';
			echo '<caption>DeveloperSerch</caption>';
				echo '<tr>';
					echo '<td>';
						echo 'Nationality';
					echo '</td>';
					echo '<td>';

					echo '<select name="country">';

	 				foreach ($user_detail_fields[nationality][2] as $meta_key => $meta_value) {
						echo '<option value="'.$meta_key.'"';
						if(!empty($request[country])){
							if($request[country] == $meta_key){
								echo ' selected';
							}
						}
						echo '>'.$meta_value.'</option>';
					}
					echo '</select>';

					echo '</td>';
				echo '</tr>';

				echo '<tr>';
					echo '<td>';
						echo 'Expertise';
					echo '</td>';
					echo '<td>';
					echo '<input type="text" name="expertise" value="';
					if(!empty($request[expertise])){
						echo $request[expertise];
					}
					echo '">';
					echo '</td>';
				echo '</tr>';

				echo '<tr>';
					echo '<td>';
					echo 'Skill';
					echo '</td>';
					echo '<td>';
					echo '<input type="number" name="skill" value="';
					if(!empty($request[skill])){
						echo $request[skill];
					}
					echo '">';
					echo '</td>';
				echo '</tr>';

	 			echo '<tr>';
				echo '<td>';
				echo 'Desiredprice';
				echo '</td>';
				echo '<td>';
				echo '<input type="number" name="desiredprice" value="';
				if(!empty($request[desiredprice])){
				echo $request[desiredprice];
				}
				echo '">';
				echo '</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td>FreeWord</td>';
				echo '<td>';
				echo '<input type="text" name="freeword" value="';
					if(!empty($request[freeword])){
						echo $request[freeword];
					}
					echo '">';
				echo '</td>';
			echo '</table>';
			echo '<input type="submit" name="action" value="Serch">';
			echo '</form>';
		}
	}

}
add_shortcode('hrd_developer_serch', 'hrd_developer_serch');


?>
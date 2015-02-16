<?php
/*
 Plugin Name: HRD 6 Account Function
 Description: Account Function. login,logout,LostPassword,Profile
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdaccount
 Domain Path: /languages
*/

/* ページ作成は、ThemeMyLoginを使用
 * 英語で作成するには、wp-content/languages/ja.po(mo)ファイルを修正する必要がある。
 *
 * 自動で作成される固定ページを
 * ・ログイン:Log In, ログアウト:Log Out, 登録:Register
 * ・パスワード紛失:Lost Password, パスワードリセット:Reset Password
 * ・あなたのプロフィール(profileのフロント編集):Your Profile
 *
 * 有効後、ログインメニューに追加する
 */

// フロントエンド
/* ページ作成 */
function hrd_account_args() {
	$page_args = array(
//		array('post_type'=>'page','title'=>'新規会員登録','slug'=>'account_new','parent'=>'0',	'post_status'=>1,'content'=>'[hrd_account_new]'),
//		array('post_type'=>'page','title'=>'アカウント','slug'=>'account_disp','parent'=>'0',	'post_status'=>1,'content'=>'[hrd_account_disp]'),
//		array('post_type'=>'page','title'=>'アカウント情報変更','slug'=>'account_update','parent'=>'account_disp',	'post_status'=>1,'content'=>'[hrd_account_update]'),
//		array('post_type'=>'page','title'=>'退会','slug'=>'account_withdrawel','parent'=>'account_disp',	'post_status'=>1,'content'=>'[hrd_account_withdrawel]'),
//		array('post_type'=>'page','title'=>'会員招待','slug'=>'account_','parent'=>'account_disp',	'post_status'=>1,'content'=>'[hrd_account_]'),

		array('post_type'=>'page','title'=>'account','slug'=>'account','parent'=>'0',	'post_status'=>1,'content'=>''), // アカウントトップ
		array('post_type'=>'page','title'=>'user detail','slug'=>'user_detail','parent'=>'account',	'post_status'=>1,'content'=>'[hrd_user_detail]'), // 会員詳細
		array('post_type'=>'page','title'=>'user identity','slug'=>'user_identity','parent'=>'account',	'post_status'=>1,'content'=>'[hrd_user_identity]'), // 本人確認
		array('post_type'=>'page','title'=>'withdrawel','slug'=>'withdrawel','parent'=>'account',	'post_status'=>1,'content'=>'[hrd_account_withdrawel]'), // 退会
		array('post_type'=>'page','title'=>'invite','slug'=>'invite','parent'=>'account',	'post_status'=>1,'content'=>'[hrd_member_invite]'), // 会員招待
	);
	return $page_args;
}

function hrd_create_account_page() {
	$page_args = hrd_account_args();
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
//add_shortcode('hrd_create_account_page', 'hrd_create_account_page');
register_activation_hook(__FILE__, 'hrd_create_account_page'); // プラグインが有効化されたときに実行される関数を登録

function hrd_delete_account_page() {
	$page_args = hrd_account_args();
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
//add_shortcode('hrd_delete_account_page', 'hrd_delete_account_page');
register_deactivation_hook(__FILE__, 'hrd_delete_account_page'); // プラグインが停止されたときに実行される関数を登録

// メタ情報
function hrd_user_meta_type(){
	$user_meta = array(
		//会員種別 Type
		array("id"	=> "user_type",		"name"	=>"ユーザータイプ",	"type"	=>"select", "must"	=> true, "choice" => hrd_user_meta_type_status() ),
	);
	return $user_meta;
}
// 会員情報詳細入力
function hrd_user_detail_args($field_area=null) {
	$expertise_list	= array(
			''				=> '',
			'java'			=> 'java',
			'php'			=> 'php',
			'javascript'	=> 'javascript',
			'asp'			=> 'asp',
	);

	$fields = array(
			'common' => array(
					//					'status' => 'ステータス',
			//					'pay_account'		=> array('口座情報'	,'text')
					'pay_account'		=> array( __('Pay Account','crowdia')	,'text'),

					'authorization'		=> array( __('authorization','crowdia')		,'select',	// 承認
							array(
									'approved'		=> __('approved','crowdia'),		// 承認
									'unapproved'	=> __('unapproved','crowdia'),	// 未承認
									'unregistered'	=> __('unregistered','crowdia'),	// 未登録
							),
					),
					'registration_status' => array( __('registration','crowdia' )	,'select',
							array(
									'activate'		=> __('activate','crowdia'),	//	登録中
									'delete'		=> __('delete','crowdia'),	//	削除
							),
					),
			),
			'client' => array(
					'sur_name'			=> array( __('Sur Name','crowdia')	,'text'),	// 名
					'given_name'		=> array( __('Given Name','crowdia')	,'text'),	// 姓
					'middle_name'		=> array( __('Middle Name','crowdia')	,'text'),	// ミドルネーム
					'sex'				=> array( __('Sex','crowdia')	,'select',	// 性別
							array(
									''			=> __('','crowdia'),
									'male'		=> __('Male','crowdia'),
									'famale'	=> __('Famale','crowdia'),
							)
					),
//					'space1' => array('','space'), // 空白行
					'company_name'		=> array( __('Company Name','crowdia')	,'text'),	// 会社名
					'company_url'		=> array( __('Company url','crowdia')	,'text'),	// 会社URL
					'founding_date'		=> array( __('FoundingDate','crowdia')	,'date'),	// 創業年月日

					'country'			=> array( __('Country','crowdia')		,'text'),	// 国名
					'business_type'		=> array( __('Business Type','crowdia')		,'text'),	// 業種
//					'space2' => array('','space'), // 空白行
					'person_name'		=> array( __('Person Name','crowdia')	,'text'),	// 担当者
					'person_address'	=> array( __('Person Address','crowdia')	,'text'),	// 連絡先
					'about'				=> array( __('About','crowdia')	,'textarea'),	// 会社紹介
					'special_note'		=> array( __('Special Note','crowdia')	,'textarea')	// 特記事項
			),
			'develop' => array(
					'sur_name'			=> array( __('Sur Name','crowdia')	,'text'),	// 名
					'given_name'		=> array( __('Given Name','crowdia')	,'text'),	// 姓
					'middle_name'		=> array( __('Middle Name','crowdia')	,'text'),	// ミドルネーム

					'sex'				=> array( __('Sex','crowdia')	,'select',	// 性別
							array(
									''			=> __('','crowdia'),
									'male'		=> __('Male','crowdia'),
									'famale'	=> __('Famale','crowdia'),
							)
					),

					'nationality'	=> array(__('Nationality','crowdia')		,'select',	// 国籍
							array(
									''				=> __('','crowdia'),
									'Philippines'	=> __('Philippines','crowdia'),	// フィリピン
									'Vietnam'		=> __('Vietnam','crowdia'),	// ベトナム
									'Singapore'		=> __('Singapore','crowdia'),	// シンガポール
									'Indoneshia'		=> __('Indoneshia','crowdia'),	// インドネシア
							)

					),
					'birthday'		=> array( __('Birthday','crowdia')	,'date'),	// 誕生日
//					'space1' => array('','space'), // 空白行

					'live_country'	=> array( __('Live Country','crowdia')	,'text'	,'','live_country'),	// 居住国
			//					'job_type'	=> array( __('Job Type','crowdia')		,'text'),	// 職種

					'company_name'		=> array( __('Company Name','crowdia')	,'text'),	// 会社名
//					'space2' => array('','space'), // 空白行

			//					'expertise1'	=> array( __('Expertise1','crowdia')	,'select'	,$expertise_list),	// 得意分野１
			//					'expertise1'	=> array( __('Expertise','crowdia')		,'select'	,$expertise_list),	// 得意分野１
					'expertise1'	=> array( __('Expertise','crowdia')		,'textarea'),	// 得意分野１
					'skill1'		=> array( __('Skill1','crowdia')		,'year'),	// 経験年数(年）
							//					'expertise2'	=> array( __('Expertise2','crowdia')	,'select'	,$expertise_list),	// 得意分野２
					'expertise2'	=> array( __('Expertise2','crowdia')	,'textarea'),	// 得意分野２
					'skill2'		=> array( __('Skill2','crowdia')		,'year'),	// 経験年数(年）
							//					'expertise3'	=> array( __('Expertise3','crowdia')	,'text'),	// 得意分野３
					'expertise3'	=> array( __('Expertise3','crowdia')	,'textarea'),	// 得意分野３
					'skill3'		=> array( __('Skill3','crowdia')		,'year'),	// 経験年数(年）

							//					'career'		=> array( __('Career','crowdia')		,'textarea'),	// 経歴
							//					'profile'		=> array( __('Profile','crowdia')		,'textarea'),	// プロフィール
							//					'performance'	=> array( __('Performance','crowdia')	,'textarea'),	// 実績
							//					'product'		=> array( __('Product','crowdia')		,'text'),	// 作品
					'introduction'	=> array( __('Introduction','crowdia')	,'textarea'),	// 自己ＰＲ
							//					'hope_price'	=> array( __('Hope Price','crowdia')	,'text'),	// 希望単価
							//					'lower_price'	=> array( __('Lower Price','crowdia')	,'number'),	// 最低単価
							//					'upper_price'	=> array( __('Upper Price','crowdia')	,'number'),	// 最高単価
					'desiredprice'	=> array( __('DesiredPrice','crowdia')	,'price'),	// 希望価格

							//					'evaluation'	=> array( __('Evaluation','crowdia')	,'text')	// 評価

			),
			'action' => array(
					'action'		=> array(
							array(
							/* 									'すぐに出来ます'		=>'now',
							 '内容によって出来ます'	=>'think',
							'今は出来ません'		=>'busy' */

									__('I can immediately','crowdia')		=>'now',	// すぐに出来ます
									__('I can depending on the content','crowdia')		=>'think',	// 内容によって出来ます
									__('I can not right now','crowdia')		=>'busy'	// 今は出来ません
							)
							,'radio')
			)
	);

	if (!empty($fields[$field_area])) {
		$user_fields_args = $fields[$field_area];
	} else {
		$user_fields_args = $fields;
	}

	return $user_fields_args;

}
/* function hrd_user_meta_type_status(){
	$user_meta = array(
		//状態 Status
		''			=> __('','hrdaccount'),
		'client'	=> __('Client','hrdaccount'),	// 依頼者
		'develop'	=> __('Develop','hrdaccount'),	// 開発者
	);
	return $user_meta;
}

function hrd_user_meta_client(){
	$user_meta = array(
		//依頼者情報 Client
		array("id"	=> "comlanyname",		"name"	=>"会社名",			"type"	=>"text", "must"	=> true),
		array("id"	=> "companyurl",		"name"	=>"会社URL",		"type"	=>"text"),
		array("id"	=> "countrylocation",	"name"	=>"国名",			"type"	=>"text"),
		array("id"	=> "businesstype",		"name"	=>"業種",			"type"	=>"text"),
		array("id"	=> "personalname",		"name"	=>"担当者",			"type"	=>"text"),
		array("id"	=> "personaladdress",	"name"	=>"連絡先",			"type"	=>"text"),
		array("id"	=> "countryabout",		"name"	=>"会社紹介",		"type"	=>"textarea"),
		array("id"	=> "note",				"name"	=>"特記事項",		"type"	=>"textarea"),
	);
	return $user_meta;
}

function hrd_user_meta_develop(){
	$user_meta = array(
		//開発者情報 Develop
		array("id"	=> "borncountry",		"name"	=>"出身国",			"type"	=>"select",	"choice" => hrd_user_meta_born_country_select()),
//		array("id"	=> "livecountry",		"name"	=>"居住国",			"type"	=>"text"),
		array("id"	=> "livecountry",		"name"	=>"居住国",			"type"	=>"select",	"choice" => hrd_user_meta_born_country_select()),
		array("id"	=> "expertise1",		"name"	=>"得意分野１",		"type"	=>"select",	"choice" => hrd_user_meta_expertise_job_select()),
		array("id"	=> "skill1",			"name"	=>"スキル１",		"type"	=>"number"),
		array("id"	=> "expertise2",		"name"	=>"得意分野２",		"type"	=>"select",	"choice" => hrd_user_meta_expertise_job_select()),
		array("id"	=> "skill2",			"name"	=>"スキル２",		"type"	=>"number"),
		array("id"	=> "expertise3",		"name"	=>"得意分野３",		"type"	=>"text"),
		array("id"	=> "skill3",			"name"	=>"スキル３",		"type"	=>"number"),
		array("id"	=> "career",			"name"	=>"経歴",			"type"	=>"textarea"),
		array("id"	=> "profile",			"name"	=>"プロフィール",	"type"	=>"text"),
		array("id"	=> "performance",		"name"	=>"開発実績",		"type"	=>"textarea"),
		array("id"	=> "introduction",		"name"	=>"自己ＰＲ",		"type"	=>"textarea"),
		array("id"	=> "lowerprice",		"name"	=>"最低賃金",		"type"	=>"number"),
		array("id"	=> "highestprice",		"name"	=>"最高賃金",		"type"	=>"number"),
	);
	return $user_meta;
}

function hrd_user_meta_serch_develop(){
	$user_meta = array(
			//開発者情報 Develop
			array("id"	=> "borncountry",		"name"	=>"出身国",			"type"	=>"select",	"choice" => hrd_user_meta_born_country_select()),
			array("id"	=> "livecountry",		"name"	=>"居住国",			"type"	=>"text"),
			array("id"	=> "expertise",			"name"	=>"得意分野",		"type"	=>"select",	"choice" => hrd_user_meta_expertise_job_select()),
			array("id"	=> "skill",				"name"	=>"スキル",			"type"	=>"number"),
			array("id"	=> "career",			"name"	=>"経歴",			"type"	=>"textarea"),
			array("id"	=> "profile",			"name"	=>"プロフィール",	"type"	=>"text"),
			array("id"	=> "performance",		"name"	=>"開発実績",		"type"	=>"textarea"),
			array("id"	=> "introduction",		"name"	=>"自己ＰＲ",		"type"	=>"textarea"),
			array("id"	=> "lowerprice",		"name"	=>"最低賃金",		"type"	=>"number"),
			array("id"	=> "highestprice",		"name"	=>"最高賃金",		"type"	=>"number"),
	);
	return $user_meta;
}


function hrd_user_meta_born_country_select() {
	$result = array(
		''				=> __('','hrdaccount'),	// フィリピン
		'Philippines'	=> __('Philippines','hrdaccount'),	// フィリピン
		'Vietnam'		=> __('Vietnam','hrdaccount'),	// ベトナム
		'Singapore'		=> __('Singapore','hrdaccount'),	// シンガポール
		'Indoneshi'		=> __('Indoneshi','hrdaccount'),	// インドネシア
	);
	return $result;
}
function hrd_user_meta_expertise_job_select() {
	$result = array(
		''				=> '',
		'java'			=> 'java',
		'php'			=> 'php',
		'javascript'	=> 'javascript',
		'asp'			=> 'asp',
	);
	return $result;
} */

// アカウントメタ情報更新
function hrd_user_update(){
	$user_request[id] = $_REQUEST[ID];
	$user_request[meta] = $_REQUEST[meta];
	$user_request[value] = $_REQUEST[value];
	$user_request[action] = $_REQUEST[action];

	if(!empty($user_request)) {
		$result = update_user_meta($user_request[id], $user_request[meta], $user_request[value]);
	}
	return $result;
}

//
function hrd_user_detail($user_id=null) {
//var_dump($_REQUEST);
	if(empty($user_id)) {
		$user_id = get_current_user_id();
	}

	// user status check
	$get_user_mata = new WP_Query(array('post_type'=>'user_detail','author'=>$user_id));
	wp_reset_postdata();

	$user_metas = get_post_meta( $get_user_mata->post->ID );
	if($user_metas) {
		$user_status = array_shift($user_metas['status']);
	}
	switch ($user_status){
		case "develop":
			$develop_items = hrd_user_detail_args('develop');
//var_dump($develop_items);
			echo '<table>';
			echo '<caption>DeveloperDetailUpdate</caption>';
			echo '<form action="'.hrd_get_page_url("user_detail").'" method="POST">';
			foreach ($develop_items as $item_key => $item_value) {
//var_dump($item_value);
				echo '<tr>';
//				hrd_input_meta_form($get_user_mata->post->ID, $item_key, $user_metas[$item_key], $_REQUEST[$item_key] );
				hrd_input_meta_form($get_user_mata->post->ID, $item_key, $item_value, $_REQUEST );
				echo '</tr>';
			}
			echo '<tr><td></td>';
			echo '<td>';
			echo '<input type="hidden" name="action" value="set_user_develop">';
			echo '<input type="hidden" name="status" value="develop">';
//			echo '<input type="submit" name="type" value="Update" title="ユーザー情報詳細">';
			echo hrd_title_maker('Update','input');
			echo '</td>';
			echo '</tr>';
			echo '</form>';
			echo '</table>';

			break;

		case "client":
			$client_items = hrd_user_detail_args('client');

			echo '<table>';
			echo '<caption>ClientDetailUpdate</caption>';
			echo '<form action="'.hrd_get_page_url("user_detail").'" method="POST">';
			foreach ($client_items as $item_key => $item_value) {
				echo '<tr>';
//				hrd_input_meta_form($get_user_mata->post->ID, $item_key, $user_metas[$item_key], $_REQUEST[$item_key] );
				hrd_input_meta_form($get_user_mata->post->ID, $item_key, $item_value, $_REQUEST );

				echo '</tr>';
			}
			echo '<tr><td></td>';
			echo '<td>';
			echo '<input type="hidden" name="action" value="set_user_develop">';
			echo '<input type="hidden" name="status" value="develop">';
//			echo '<input type="submit" name="type" value="Update" title="ユーザー情報詳細">';
			echo hrd_title_maker('Update','input');
			echo '</td>';
			echo '</tr>';
			echo '</form>';
			echo '</table>';

			break;
		default:
			echo "Hi!Visitor Please Select User Type";

			echo '<p>';
//			echo '<form action="'.hrd_get_page_url("user_detail").'" method="POST">';
			echo '<form action="" method="POST">';
			echo '<input type="hidden" name="action" value="set_user_status">';
			echo '<input type="hidden" name="status" value="client">';
			echo '<input type="submit" name="type" value="ask The Job" title="仕事の依頼">';
			echo '</form>';
			echo '</p>';

			echo '<p>';
//			echo '<form action="'.get_post_permalink().'" method="POST">';
			echo '<form action="" method="POST">';
			echo '<input type="hidden" name="action" value="set_user_status">';
			echo '<input type="hidden" name="status" value="develop">';
			echo '<input type="submit" name="type" value="Do The Job" title="仕事の応募">';
			echo '</form>';
			echo '</p>';
	}
}
add_shortcode('hrd_user_detail', 'hrd_user_detail');

function hrd_user_disp($user_id) {

	$user_type = hrd_get_user_type($user_id);
//var_dump($user_type);
	$user_meta_args = hrd_user_detail_args($user_type);
	$user_detail = new WP_Query(array('post_type'=>'user_detail', 'author'=>$user_id));
	wp_reset_postdata();
	$user_metas = get_post_meta( $user_detail->post->ID );

	echo'<table>';
	echo '<caption>UserDetail</caption>';
	foreach ($user_meta_args as $user_meta_key=>$user_meta_value) {
		echo '<tr>';
		echo '<td>';
		echo $user_meta_value[0];
		if($user_meta_value[1]=="price"){
			echo "[USD]";
		}
		if($user_meta_value[1]=="year"){
			echo "[Year]";
		}
		echo '</td>';
		echo '<td>';
		echo $user_metas[$user_meta_key][0];

		echo '</td>';
		echo '</tr>';
	}
	echo'</table>';
}


// ユーザー詳細の更新
function hrd_user_detail_update() {
	if( (!empty($_REQUEST[action])) AND (!empty($_REQUEST[status])) ){
		if($_REQUEST[action] == "set_user_status") {
			if( ($_REQUEST['status'] == "client") OR ($_REQUEST['status'] == "develop") ) {
				$user_id = get_current_user_id();
				$get_user = get_userdata($user_id);
//var_dump($get_user);

				$check_user_args = array(
						'post_type'	=> 'user_detail',
						'author'	=> $user_id,
				);
				$user_detail = new WP_Query($check_user_args);
				wp_reset_postdata();

				if($user_detail->post_count == 0) {
					$insert_user_args['post_type']		= 'user_detail';	// 投稿タイプ
					$insert_user_args['post_author']	= $user_id;		// ユーザーID
					$insert_user_args['post_status']	= 'publish';		// 公開ステータス 'draft'（下書き）,'publish'（公開済み）,'pending'（レビュー待ち）,'private'（非公開）
					$insert_user_args['post_title']		= $get_user->user_nicename.'さんの詳細情報';				// ページタイトル
					$insert_user_args['post_content']	= '';				// 投稿の本文
					$insert_user_args['post_name']		= $get_user->user_login ;	// スラッグ
					$insert_user_args['comment_status']	= 'closed';		// コメントを閉じる 'closed','open'
					$insert_user_args['ping_status']	= 'closed';		// ピンバック／トラックバック 'closed','open'

					$post_id = wp_insert_post($insert_user_args);
				}else{
					$post_id = $user_detail->post->ID;
				}
				update_post_meta($post_id, 'status', $_REQUEST[status]);
			}
		}
		// ユーザー詳細の更新
		if($_REQUEST[action] == "set_user_develop") {
			$user_id = get_current_user_id();
			$get_user = get_userdata($user_id);
			$get_user_detail = new WP_Query(array('post_type'=>'user_detail','author'=>$user_id));

			if($get_user_detail->post_count != 0) {
				$post_id = $get_user_detail->post->ID;

				$develop_items = hrd_user_detail_args('develop');
				foreach ($develop_items as $item_key => $item_value) {
					if(!empty($_REQUEST[$item_key])){
						$update_result = update_post_meta($post_id, $item_key, $_REQUEST[$item_key]);
					}
				}
			}
		}

	}
}
add_action('init', 'hrd_user_detail_update');

// 本人確認書類アップロード
function hrd_insert_attachment($file_handler,$post_id,$setthumb='false') {
	// check to make sure its a successful upload
	if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

	require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	require_once(ABSPATH . "wp-admin" . '/includes/file.php');
	require_once(ABSPATH . "wp-admin" . '/includes/media.php');
	$attach_id = media_handle_upload( $file_handler, $post_id );
	if ($setthumb)
		update_post_meta($post_id,'_thumbnail_id',$attach_id);
	return $attach_id;
}

function hrd_user_identity() {
	echo 'User Identity';
//var_dump($_REQUEST);
	if($_REQUEST['action'] == "delete"){
		wp_delete_attachment($_REQUEST['attach_id']);
	}


	$user_id = get_current_user_id();
	$user_detail_args = array(
		'post_type' => 'user_detail',
		'author'	=> $user_id
	);
	$get_userdetail = new WP_Query($user_detail_args);
	wp_reset_postdata();

	$detail_id = $get_userdetail->post->ID;

	// File Attchement
	if($_FILES['thumbnail']['size'] > 0) {
		$attachment_id = hrd_insert_attachment('thumbnail', $detail_id);
		//			update_post_meta($attachment_id,'authorization','false');
		update_post_meta($attachment_id,'authorization','reject');
	}


	$attachment_args = array(
			'post_type' 	=> 'attachment',
			'author'		=> $user_id,
			'post_parent'	=> $detail_id,
			'post_status'	=> 'any',
			'orderby'		=> 'ID',
			'order'			=> 'ASC'
	);

	$get_attachments = get_children($attachment_args);
//var_dump($get_attachments);
	if (!empty($get_attachments)) {
		$get_attachment = new stdClass();
		echo '<table>';
		echo '<caption></caption>';
		foreach ($get_attachments as $get_attachment) {
			echo '<tr>';
			echo '<td>';
			echo '<img src="'.$get_attachment->guid.'">';
			echo '</td>';

			echo '<td>';
			echo 'name:'.$get_attachment->post_title;
			echo '<br>';
			echo 'date:'.$get_attachment->post_date;
			echo '<form action="" method="POST">';
			echo '<input type="hidden" name="action" value="delete">';
			echo '<input type="hidden" name="attach_id" value="'.$get_attachment->ID.'">';
			echo '<input type="submit" value="Delete">';
			echo '</form>';
	//		var_dump($get_attachment);
			echo '</td>';

			echo '</tr>';
		}
		echo '</table>';
	}else{
		echo '<p>Empty</p>';
	}
	// Up Loader
	echo 'File Upload';
	echo '<form method="post" action="#" enctype="multipart/form-data" >';
	echo '<input type="file" name="thumbnail" accept="image/*" />';
	echo '<input type="hidden" name="post_id" value="1" />';
	echo '<input type="submit" value="'.__('Upload','crowdia').'">';
	echo '</form>';

}


add_shortcode('hrd_user_identity', 'hrd_user_identity');

// 退会
function hrd_account_withdrawel(){
	global $err_msg;
	if(!empty($err_msg)) {
		echo '<p><font color="red" >';
		echo hrd_title_maker($err_msg);
		echo '</font></p>';
	}

	echo '<font color="red">';
//	echo '注意：過去の案件情報が参照できなくなります。';
	echo __('Caution:You will not be able to see past projects information.','hrdaccount');
	echo '<br>';
	echo __('Are you sure you want to unsubscribe really?');  // '本当に退会しますか？';
	echo '</font>';

	__('Are you sure you want to unsubscribe really?','hrdaccount');

	echo '<form action="'; echo hrd_get_page_url('withdrawel'); echo '" method="post">';	// 	退会
	echo '<input type="hidden" name="action" value="withdrawel">';
	echo '<input type="hidden" name="id" value="'.get_current_user_id().'">';
	echo hrd_title_maker('Yes.Unsubscribe','input');  __('Yes.Unsubscribe','hrdaccount');
	echo '</form>';

	echo '<p>';
	//	echo '<form action="'; echo hrd_get_page_url('user_detail'); echo '" method="post">';	// 取消
	echo '<form action="'; echo hrd_get_page_url('mypage'); echo '" method="post">';	// 取消
	//	echo '<input type="submit" value="'.__('Cancel','hrdaccount').'">';
	echo hrd_title_maker('Cancel','input');
	echo '</form>';
	echo '</p>';
}
add_shortcode('hrd_account_withdrawel', 'hrd_account_withdrawel');

//ユーザー情報を 削除してトップへリダイレクト
function hrd_withdrawel_redirect() {
	global $err_msg;
	$err_msg = "";
	if($_REQUEST['action'] == "withdrawel") {
		require_once ABSPATH."/wp-admin/includes/user.php";
		$user_id  = get_current_user_id();
		if($user_id == $_REQUEST[id]){
		//	if(is_client() OR is_develop() OR is_visitor() ){
			if(is_login){
				// ユーザー登録情報以外の削除
				// User Post Delete post_type:'job','entry','time','pay','user_detail','message'));
	//			hrd_delete_post(array('user_id'=>$user_id,'post_type'=>'any'));

				// User delete
				// wp_delete_userの2nd Parm $reassign 割り当てるユーザーID

	//			if(wp_delete_user($user_id)){
				// ユーザーの削除
				$user_delete = 	wp_delete_user($user_id);
				if($user_delete){
					// Notice Email
					if(function_exists('hrd_send_mail')) {
						hrd_send_mail('delete_user');
						hrd_send_mail('delete_user_to_admin');
					}

					wp_logout();	// ログアウト
					wp_safe_redirect(home_url()); // リダイレクト
					exit;
				}else{
					$err_msg = "It was not possible to delete the user information. Please contact us.";
					__('It was not possible to delete the user information. Please contact us.', 'hrdaccount');
					return;
				}
			}
		}
	}
}
add_action('init', hrd_withdrawel_redirect);

// ユーザー情報
/*
function hrd_get_user_type() {	// wp_user -> status
	$metas = array_shift(hrd_user_meta_type());
	$get_user_type = get_user_meta(get_current_user_id(), $metas[id]);
	if(!empty($get_user_type)) {
		$get_user_type = array_shift($get_user_type);
		return $get_user_type;
	} else {
		return FALSE;
	}

}*/

function hrd_get_user_type( $user_id=NULL ) {	// post_type = user_detail
	if(empty($user_id)) {
		$user_id = get_current_user_id();
	}
	$args = array(
			'post_type'	=> 'user_detail',
			'author'	=> $user_id,
	);
	$user_data = new WP_Query($args);
//var_dump($user_data);
	if($user_data->post_count == 0 ) {
		return false;
	}else{
//		$user_status = get_post_meta($user_id, 'status');
		$user_status = get_post_meta($user_data->post->ID, 'status');
		$user_status = array_shift($user_status);
//var_dump($user_status);
		return $user_status;
	}
	wp_reset_postdata();
}

// 会員招待
function hrd_member_invite() {
	echo 'Member Invite';

}
//add_shortcode('hrd_member_invite', 'hrd_member_invite');

// ユーザーチェック
function is_logout() {
	if(is_user_logged_in()){
		return FALSE;
	}else{
		return TRUE;
	}
}
function is_login() {
	return is_user_logged_in();
}
function is_client() {
	if(hrd_get_user_type() == "client") {
		return TRUE;
	}else{
		return FALSE;
	}
}
function is_develop() {
	if(hrd_get_user_type() == "develop") {
		return TRUE;
	}else{
		return FALSE;
	}
}
function is_visitor(){
	if( (is_user_logged_in()) AND !(hrd_user_meta_type()) ) {
		return TRUE;
	}else{
		return FALSE;
	}
}
?>
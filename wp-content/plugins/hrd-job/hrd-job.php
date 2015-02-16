<?php
/*
 Plugin Name: HRD 7 Job Function
 Description: Job Page Function Using Plugin[PayPal Pay Now, Buy Now, Donation and Cart Buttons Shortcode]
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdjob
 Domain Path: /languages
*/
// 定数
function hrd_const($const_key){
	if($const_key == "fee"){
		return 15;
	}
	if($const_key == "tax"){
		return 8;
	}
	if($const_key == "payed_acount"){
		return 'info@crowdia.jp';
	}
	if($const_key == "DomesticFee") {
		return 3.6;
	}
	if($const_key == "OverseasFee") {
		return 3.9;
	}
}

// フロントエンド
/* ページ作成 */
function hrd_job_parent_args() {
	$page_args = array(
		array('post_type'=>'page','title'=>'JobTop',	'slug'=>'job_top',		'parent'=>'0',	'post_status'=>1,'content'=>'[hrd_job_top]'), // 案件トップ
	);
	return $page_args;
}
function hrd_job_child_args() {
	$page_args = array(
		// Client
		array('post_type'=>'page','title'=>'JobCreate',		'slug'=>'job_create',	'parent'=>'job_top','post_status'=>1,'content'=>'[hrd_job_create]'), // 案件登録
		array('post_type'=>'page','title'=>'JobList',		'slug'=>'job_list',		'parent'=>'job_top','post_status'=>1,'content'=>'[hrd_job_list]'), // 案件一覧
			array('post_type'=>'page','title'=>'JobDisp',	'slug'=>'job_disp',		'parent'=>'job_top','post_status'=>1,'content'=>'[hrd_job_disp]'), // 案件情報
			array('post_type'=>'page','title'=>'JobUpdate',	'slug'=>'job_update',	'parent'=>'job_top','post_status'=>1,'content'=>'[hrd_job_update]'), // 案件変更
		// Develop
		array('post_type'=>'page','title'=>'JobSerch',		'slug'=>'job_serch',	'parent'=>'job_top','post_status'=>1,'content'=>'[hrd_job_serch]'), // 案件検索
		array('post_type'=>'page','title'=>'EntryList',		'slug'=>'entry_list',	'parent'=>'job_top','post_status'=>1,'content'=>'[hrd_entry_list]'), // 応募一覧
			array('post_type'=>'page','title'=>'EntryDisp',	'slug'=>'entry_disp',	'parent'=>'job_top','post_status'=>1,'content'=>'[hrd_entry_disp]'), // 応募情報
			array('post_type'=>'page','title'=>'agreement',	'slug'=>'job_agreement',	'parent'=>'job_top','post_status'=>1,'content'=>'[hrd_job_agreement]'), // タイムカード
			array('post_type'=>'page','title'=>'TimeCard',	'slug'=>'time_disp',	'parent'=>'job_top','post_status'=>1,'content'=>'[hrd_time_disp]'), // タイムカード
			array('post_type'=>'page','title'=>'Pay',		'slug'=>'pay_disp',		'parent'=>'job_top','post_status'=>1,'content'=>'[hrd_pay_disp]'), // 決済
	);
	return $page_args;
}
function hrd_job_args() {
	$page_args = array();
	$page_args = array_merge(hrd_job_parent_args() , hrd_job_child_args());
	return $page_args;
}


function hrd_create_job_page() {

	$page_args = hrd_job_args();
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
//add_shortcode('hrd_create_jobt_page', 'hrd_create_job_page');
register_activation_hook(__FILE__, 'hrd_create_job_page'); // プラグインが有効化されたときに実行される関数を登録

function hrd_delete_job_page() {
	$page_args = hrd_job_args();
	foreach ($page_args as $page_arg) {
		$page_id = get_page_by_path($page_arg['slug'],OBJECT,'page')->ID;
		if(!empty($page_id)) {
			$delete = wp_delete_post($page_id,$force_delete = true);	// force_delete:ゴミ箱への移動ではなく、完全に削除する
		}
	}
}
register_deactivation_hook(__FILE__, 'hrd_delete_job_page'); // プラグインが停止されたときに実行される関数を登録


// メタ情報
function hrd_job_meta_base() {
	$base = array(
	// 基本情報
		array("id"	=> "jobno",			"name"	=>"JobNo",		"type"	=>"text", 	"must"	=> true), // 案件番号
		array("id"	=> "author",		"name"	=>"Client",		"type"	=>"text", 	"must"	=> true), // 案件名
		array("id"	=> "title",			"name"	=>"JobName",	"type"	=>"text", 	"must"	=> true), // 案件名
		array("id"	=> "content",		"name"	=>"JobDetail",	"type"	=>"textarea", 	"must"	=> true), // 案件詳細
	);
	return $base;
}

function hrd_job_meta_job_job() {
	$job = array(
	// 案件情報 job
		array("id"	=> "entryend",		"name"	=>"EntryEnd",			"type"	=>"date", 	"must"	=> true), // 応募期間
		array("id"	=> "hourlyprice",	"name"	=>"HourlyPrice[USD]",	"type"	=>"number", "must"	=> true), // 時給単価
		array("id"	=> "deliverydate",	"name"	=>"Deliverydate",		"type"	=>"date", 	"must"	=> true), // 納期
		array("id"	=> "forecastvolume","name"	=>"ForecastVolume[Hour]",	"type"	=>"number", "must"	=> true), // 予定工数
		array("id"	=> "notices",		"name"	=>"Notices",			"type"	=>"text", 	"must"	=> false), // 特記事項
	);
	return $job;
}
function hrd_job_meta_serch() {
	$job_meta = array(
		array('key'	=> 	"entryend", 	"compare"	=>	">=" ),
		array('key'	=> 	"hourlyprice", 	"compare"	=>	">=" ),
		array('key'	=> 	"deliverydate", "compare"	=>	">=" ),
		array('key'	=> 	"forecastvolume","compare"	=>	">=" ),
	);
	return $job_meta;
}

function hrd_job_meta_job_entry() {
	$job = array(
	// 開発者情報 entry
		array("id"	=> "entry_id",			"name"	=>"EntryId",		"type"	=>"text", 	"must"	=> true), // 応募者
		array("id"	=> "client_agreement",	"name"	=>"client_agreement","type"	=>"text", 	"must"	=> true), // 依頼者契約
		array("id"	=> "develop_agreement",	"name"	=>"develop_agreement","type"	=>"text", 	"must"	=> true), // 開発者契約
	);
	return $job;
}
function hrd_job_meta_job_pay() {
	$job = array(
	// 支払情報 pay
		array("id"	=> "workingtime",	"name"	=>"WorkingTime",	"type"	=>"text", 	"must"	=> true), // 作業時間
		array("id"	=> "fee",			"name"	=>"Fee",	"type"	=>"text", 	"must"	=> true), // 手数料
		array("id"	=> "tax",			"name"	=>"Tax",	"type"	=>"text", 	"must"	=> true), // 消費税
		array("id"	=> "payment",		"name"	=>"Payment",	"type"	=>"text", 	"must"	=> true), // 支払金額

		array("id"	=> "paypaldomesticfee",	"name"	=>"PaypalDomesticFee",	"type"	=>"text", 	"must"	=> true), //
		array("id"	=> "clienttotal",		"name"	=>"ClientTotal",	"type"	=>"text", 	"must"	=> true), //
		array("id"	=> "paypaloverseasfee",	"name"	=>"PaypalOverseasFee",	"type"	=>"text", 	"must"	=> true), //
		array("id"	=> "developertotal",	"name"	=>"DeveloperTotal",	"type"	=>"text", 	"must"	=> true), //
		array("id"	=> "crowdiafee",		"name"	=>"CrowdiaFee",	"type"	=>"text", 	"must"	=> true), //

		array("id"	=> "paymentdate",	"name"	=>"PaymentDate",	"type"	=>"text", 	"must"	=> true), // 支払日
	);
	return $job;
}
function hrd_job_meta_job_evaluate() {
	$job = array(
	// 評価 evaluate
		array("id"	=> "clientid",	"name"	=>"ClientID",	"type"	=>"text", 	"must"	=> true), // 依頼者
		array("id"	=> "developid",	"name"	=>"DevelopID",	"type"	=>"text", 	"must"	=> true), // 開発者
	);
	return $job;
}
function hrd_job_meta_job_status() {
	$job = array(
	// 状態 status
		array("id"	=> "jobstatus",	"name"	=>"JobStatus",	"type"	=>"text", 	"must"	=> true), // ステータス
	);
	return $job;
}
function hrd_job_meta_other_status() {
	$job = array(
	// 状態 status
		array("id"	=> "status",	"name"	=>"EntryStatus","type"	=>"text", 	"must"	=> true), // ステータス
	);
	return $job;
}
function hrd_job_status() {
	$result = array(
		''			=>	__('','hrdjob'),
		'entry'		=>	__('entry','hrdjob'),		// 募集中
		'select'	=>	__('select','hrdjob'), 		// 選定中
		'agreement'	=>	__('agreement','hrdjob'),	// 契約
		'work'		=>	__('work','hrdjob'),		// 作業中
		'pay'		=>	__('pay','hrdjob'),			// 支払い中
		'complete'	=>	__('complete','hrdjob'),	// 作業完了
		'end'		=>	__('end','hrdjob'),			// 終了
	);
	return $result;
}
function hrd_entry_status() {
	$result = array(
		''			=>	__('','hrdjob'),
		'entry'		=>	__('entry','hrdjob'),		// 募集中
		'select'	=>	__('select','hrdjob'), 		// 選定中
		'lost'		=>	__('lost','hrdjob'),		// 落選
	);
	return $result;
}

function hrd_job_meta_job() {
	$job = array();

	$job = array_merge((array)$job, (array)hrd_job_meta_job_job());
	$job = array_merge((array)$job, (array)hrd_job_meta_job_entry());
	$job = array_merge((array)$job, (array)hrd_job_meta_job_pay());
	$job = array_merge((array)$job, (array)hrd_job_meta_job_evaluate());
	$job = array_merge((array)$job, (array)hrd_job_meta_job_status());

	return $job;
}

function hrd_job_meta_entry() {
	$entry	= array(
	// 応募情報
		array("id"	=> "jobid",			"name"	=>"JobID",	"type"	=>"text", 	"must"	=> true), // 案件ID
		array("id"	=> "jobno",			"name"	=>"JobNo",	"type"	=>"text", 	"must"	=> true), // 案件番号
		array("id"	=> "entrycomment",	"name"	=>"Comment","type"	=>"text", 	"must"	=> true), // コメント
	);
	return $entry;
}

function hrd_job_meta_time() {
	$time = array(
	// タイムカード
		array("id"	=> "jobid",		"name"	=>"JobID",		"type"	=>"text", 	"must"	=> true), // 案件ID
		array("id"	=> "jobstart",	"name"	=>"JobStart",	"type"	=>"date", 	"must"	=> true), // 開始日時
		array("id"	=> "jobend",	"name"	=>"JobEnd",		"type"	=>"date", 	"must"	=> true), // 終了日時
		array("id"	=> "jobtime",	"name"	=>"JobTime",	"type"	=>"date", 	"must"	=> true), // 作業時間
	);
	return $time;
}
function hrd_job_meta_status() {
	$status = array(
	// 状態
		array("id"	=> "workingstatus",	"name"	=>"WorkingStatus",		"type"	=>"text", 	"must"	=> true), // 作業ステータス
	);
	return $status;
}

function hrd_job_page_button($slug,$parm=NULL) {
//var_dump($parm);
	if(function_exists('hrd_get_page_by_slug')) {
		$page = hrd_get_page_by_slug($slug);
		if($page){
			echo '<form action="'.$page->url.'" method="post">';
			if(!empty($parm)) {
				foreach ($parm as $parm_key => $parm_value) {
					if($parm_key != "action") {
						echo '<input type="hidden" name="'.$parm_key.'" value="'.$parm_value.'">';
					}
				}
			}
			if(empty($parm[action])) {
				echo '<input type="submit" name="action" value="'.$page->post_title.'">';
			}else{
				echo '<input type="submit" name="action" value="'.$parm[action].'">';
			}
			echo '</form>';
		}
	}
}


// InputForm
function hrd_form_input($hrd_job_meta_arg,$get_meta,$today=NULL){

	switch ($hrd_job_meta_arg[type]){
		case "textarea":
//			echo '<textarea name="'.$hrd_job_meta_arg[id].'">'.$get_meta.'</textarea>';
			$form_output = '<textarea name="'.$hrd_job_meta_arg[id].'">'.$get_meta.'</textarea>';
			break;
		case "number":
//			echo '<input type="number" name="'.$hrd_job_meta_arg[id].'" value="'.$get_meta.'">';
			$form_output = '<input type="number" name="'.$hrd_job_meta_arg[id].'" value="'.$get_meta.'">';
			break;
		case "date":
			if(empty($get_meta)) {
				$get_date = $today;
			}else{
				$get_date = $get_meta;
			}
//			echo '<input type="date" name="'.$hrd_job_meta_arg[id].'" value="'.$get_date.'">';
			$form_output = '<input type="date" name="'.$hrd_job_meta_arg[id].'" value="'.$get_date.'">';
			break;
		case "select":
			if(is_array($hrd_job_meta_arg[choice])){
//				echo '<select name="'.$hrd_job_meta_arg[id].'">';
				$form_output = '<select name="'.$hrd_job_meta_arg[id].'">';
				foreach ($hrd_job_meta_arg[choice] as $meta_key => $meta_value) {
//					echo '<option value="'.$meta_key.'"';
					$form_output .= '<option value="'.$meta_key.'"';
					if(!empty($get_meta)){
						if ($get_meta == $meta_key){
//							echo ' selected';
							$form_output .= ' selected';
						}
					}
//					echo '>'.$meta_value.'</option>';
					$form_output .= '>'.$meta_value.'</option>';
				}
//				echo '</select>';
				$form_output .= '</select>';
			}
			break;
		default:
//			echo '<input type="text" name="'.$hrd_job_meta_arg[id].'" value="'.$get_meta.'">';
			$form_output = '<input type="text" name="'.$hrd_job_meta_arg[id].'" value="'.$get_meta.'">';
		}
	return $form_output;
	}

// DataBase
function hrd_job_post_update( $post_type , $post_parm_array , $request_array ,$status){
//	var_dump($post_parm_array);
//echo "hrd_job_post_update";	var_dump($request_array);

	// Post
	$post_args['post_title']		= $request_array['title'];
	$post_args['post_content']		= $request_array['content'];
	$post_args['post_status']		= 'publish';
	$post_args['post_author']		= get_current_user_id();
	$post_args['post_type']			= $post_type;
	$post_args['comment_status']	= 'closed';	// コメントを閉じる 'closed','open'
	$post_args['ping_status']		= 'closed';	// ピンバック／トラックバック 'closed','open'

	if(empty($request_array['id'])){
		// 追加
		//echo 'Insert<br>';
		$post_result = wp_insert_post($post_args);
		if($post_result){ // Slug SET
			$post_args['ID']		=	$post_result;
			$post_args['post_name']	=	date(Ymd).'-'.$post_result;
			$post_result = wp_update_post($post_args);
		}
	} else {
		// 更新
		$post_args['ID']		= $request_array['id'];
		//echo 'Update<br>';
		$post_result = wp_update_post($post_args);
	}
//echo 'PostResult'.$post_result;
	// Post Meta
	if($post_result){
		foreach ($post_parm_array as $post_parm) {
			foreach ($request_array as $request_key => $request_value) {
				if((!empty($request_value)) AND ($post_parm[id] === $request_key)) {
					$meta_result = update_post_meta($post_result, $request_key , $request_value);
				}
			}
		}
		// Status Update
		if($post_type == "job") {
			$status_key = array_shift(hrd_job_meta_job_status());
		}else{
			$status_key = array_shift(hrd_job_meta_other_status());
		}
		//echo $post_result.':'. $status_key[id].':'. $status;
		$meta_result = update_post_meta($post_result, $status_key[id], $status);
	}
	return $post_result;
}

// メタ更新
/*function hrd_job_meta_update(){
	$job_request[id]		= $_REQUEST[ID];
	$job_request[meta]		= $_REQUEST[meta];
	$job_request[value]		= $_REQUEST[value];
	$job_request[action]	= $_REQUEST[action];

	if(!empty($user_request)) {
		$result = update_user_meta($user_request[id], $user_request[meta], $user_request[value]);
	}
	return $result;
}*/

function hrd_get_job($query_args=null) {
//var_dump($query_args);
	$post_args['post_type']	= 'job';
	if($query_args['author']){
		$post_args['author']	= $query_args['author'];
	}
	if($query_args['post_id']){
		$post_args['p'] = $query_args['post_id'];
	}

	if($query_args['freeword']){
		$post_args['s'] = $query_args['freeword'];
	}

	if($query_args['paged']){
		$post_args['paged'] = $query_args['paged'];
	}

//	$job_metas = hrd_job_meta_job_job();
	$job_metas = hrd_job_meta_job();

//var_dump($query_args);
	if(!empty($query_args['meta'])){
		$post_meta_args = array();

		$post_meta_args['meta_query'] = array($query_args['meta']);
//var_dump($post_meta_args);
//		$post_args = array_push($post_args, $post_meta_args);
		$post_args = $post_args + $post_meta_args;
	}
//var_dump($post_args);
	$get_posts	= new WP_Query($post_args);
//var_dump($get_posts);

	wp_reset_postdata();

	return $get_posts;
}


function hrd_get_entry($query_args=null) {
//var_dump($query_args);
	$post_args['post_type']	= 'entry';
	if($query_args['author']){
		$post_args['author']	= $query_args['author'];
	}
	if($query_args['post_id']){
		$post_args['p'] = $query_args['post_id'];
	}
	if($query_args['title']){
		$post_args['s']	= $query_args['title'];
	}

	if(!empty($query_args['meta'])){
		$post_meta_args = array();

		$post_meta_args['meta_query'] = array($query_args['meta']);
		//var_dump($post_meta_args);
		//		$post_args = array_push($post_args, $post_meta_args);
		$post_args = $post_args + $post_meta_args;
	}


	$get_posts	= new WP_Query($post_args);
//var_dump($get_posts);

	wp_reset_postdata();

	return $get_posts;
}

function hrd_get_time($query_args=null) {
	//var_dump($query_args);
	$post_args['post_type']	= 'time';
	if($query_args['author']){
		$post_args['author']	= $query_args['author'];
	}
	if($query_args['post_id']){
		$post_args['p'] = $query_args['post_id'];
	}
	if($query_args['title']){
		$post_args['s']	= $query_args['title'];
	}

	if(!empty($query_args['meta'])){
		$post_meta_args = array();

		$post_meta_args['meta_query'] = array($query_args['meta']);
		//var_dump($post_meta_args);
		//		$post_args = array_push($post_args, $post_meta_args);
		$post_args = $post_args + $post_meta_args;
	}


	$get_posts	= new WP_Query($post_args);
//var_dump($get_posts);

	wp_reset_postdata();

	return $get_posts;
}

function hrd_job_init_action() {
	$request = $_REQUEST;
	$job_id = $_REQUEST['id'];
	$action = $_REQUEST['action'];
	$doing = $_REQUEST['doing'];

	// 応募者選択
	if(($action == "EntryList") AND ($doing == "Select")) {
		$entry_id			= $_REQUEST['entry_id'];
		//		$get_entrys			= hrd_get_entry(array('post_id'=>$entry_id));
		$get_entrys			= hrd_get_entry(array('jobid'=>$_REQUEST[id]));
		$get_entry_posts	= $get_entrys->posts;
		//var_dump($get_entrys);
		$select_user_id = $get_entrys->post->post_author;
		$job_id			= get_post_meta($entry_id , 'jobid', true);

		// 選択したentryはselect、それ以外のentryは、
		foreach ($get_entry_posts as $get_entry_post) {
			if($get_entry_post->ID == $_REQUEST['entry_id']){
				$entry_status = update_post_meta($get_entry_post->ID, 'status', 'select');
			}else{
				$entry_status = update_post_meta($get_entry_post->ID, 'status', 'lost');
			}
		}
		// jobのentry_idへuser_idをセットhrd_job_meta_job_entry()
		$select_user = update_post_meta($job_id, 'entry_id', $select_user_id);
		// jobのjobstatusをselectをセットhrd_job_meta_job_status()
		$status = update_post_meta($job_id, 'jobstatus', 'select');
		wp_safe_redirect(hrd_get_page_url('job_top'));
		exit;
	}

	// 契約更新
	if( ($action == "agreement") AND ($doing == "I agree") ) {
		if(is_client()){
			$post_result = update_post_meta($job_id, 'client_agreement', true);
		}
		if(is_develop()){
			$post_result = update_post_meta($job_id, 'develop_agreement', true);
		}
		$job_metas = hrd_get_post_meta($job_id);
		if( ($job_metas[jobstatus]=="select")
			AND ($job_metas[client_agreement])
			AND ($job_metas[develop_agreement]) ) {
			$post_result = update_post_meta($job_id, 'jobstatus', 'work');
		}
		wp_safe_redirect(hrd_get_page_url('job_top'));
		exit;
	}

	// タイムカード
	if($action == "TimeCard"){
		$get_jobs = hrd_get_job(array('id'=>$job_id));
		$get_job = $get_jobs->post;

		if(is_develop()){
			if($doing == "Start"){

				$get_time_args = array(
					'orderby'	=> 'ID',
					'order'		=> 'DESC',
					'meta'		=> array( 'key'=>'jobid', 'value'=>$job_id,'compare'=>'='  )
				);
				$get_times = hrd_get_time($get_time_args);
				$get_time_start	= get_post_meta($get_times->post->ID,'jobstart',true);
				$get_time_end	= get_post_meta($get_times->post->ID,'jobend',true);

				if( (!empty($get_time_end)) OR ( $get_times->post_count == 0 ) ) {
					$time_args = array();
					$time_args[title]		= $get_job->jobno;
					$time_args[jobid]		= $get_job->ID;
					$time_args[jobstart]	= date('Y-m-d H:i:s');
					$time_args[jobno]		= $get_job_jobno;
					$time_meta_atgs = hrd_job_meta_time();
					$post_result = hrd_job_post_update('time', $time_meta_atgs, $time_args, 'start' );
				}
			}

			if($doing == "End"){
				//時間計算
				$get_start_time	= get_post_meta($_REQUEST['time_id'], 'jobstart',true);
				$get_end_time	= get_post_meta($_REQUEST['time_id'], 'jobend',true);
				if(empty($get_end_time)) {
					$end_time = date('Y-m-d H:i:s');
					$start_caluc = new DateTime($get_start_time);
					$end_caluc = new DateTime($end_time);
					$date_diff = $start_caluc->diff($end_caluc);

					$hour = $date_diff->format('%H');
					$minute = $date_diff->format('%I');
					$minute_dec = $minute / 60;

					$hour_dec = number_format( $hour + $minute_dec , 2 );

					$post_meta = update_post_meta($_REQUEST['time_id'], 'jobend', $end_time);
					$post_meta = update_post_meta($_REQUEST['time_id'], 'jobtime', $hour_dec);
					$post_meta = update_post_meta($_REQUEST['time_id'], 'status', 'end');
				}
			}
		}
	}

	// 支払い集計
	if($action == "TimeCard"){
		$get_jobs = hrd_get_job(array('id'=>$job_id));
		$get_job = $get_jobs->post;

		if(is_client()){
			if($doing == "JobComlete"){

				//時間集計
				$get_time_args = array(
					'meta'	=> array( 'key'=>'jobid', 'value'=>$job_id,'compare'=>'='  )
				);
				$get_times = hrd_get_time($get_time_args);
				$get_times = $get_times->posts;
				$total_time = 0;
				foreach ($get_times as $get_time) {
					$total_time += $get_time->jobtime;
				}

				$pay = $get_job->hourlyprice * $get_job->workingtime;
				$fee = floor( $pay * ( hrd_const('fee') / 100) * 100 ) / 100;
				$tax = floor(( $pay + $fee ) * ( hrd_const('tax') / 100 ) * 100 ) / 100;
//				$paypalfee = 0;
				$total	= $pay + $fee + $tax;
				$paypaldomesticfee = floor( (hrd_const(DomesticFee) / 100) * ($pay + $fee + $tax) * 100) / 100;
				$paypaloverseasfee = floor( (hrd_const(OverseasFee) / 100) * ($pay + $fee) * 100) / 100;

				$client_total = $pay + $fee + $tax + $paypaldomesticfee;
				$developer_total = $pay + $fee + $paypaloverseasfee;

				$crowdia_fee = $client_total - $developer_total;

				$job_meta_args = array();
				$result = update_post_meta($job_id, 'workingtime', $total_time);
				$result = update_post_meta($job_id, 'fee', $fee);
				$result = update_post_meta($job_id, 'tax', $tax);
				$result = update_post_meta($job_id, 'payment', $pay);
				$result = update_post_meta($job_id, 'paypaldomesticfee', $paypaldomesticfee);
				$result = update_post_meta($job_id, 'clienttotal', $client_total );

				$result = update_post_meta($job_id, 'paypaloverseasfee', $paypaloverseasfee);
				$result = update_post_meta($job_id, 'developertotal', $developer_total );

				$result = update_post_meta($job_id, 'crowdiafee', $crowdia_fee );

				$result = update_post_meta($job_id, 'jobstatus', 'pay');
				wp_safe_redirect(hrd_get_page_url('job_top'));
				exit;
			}
		}
	}
	// 終了処理
	if($action == "paid") {
		$get_jobs = hrd_get_job(array('id'=>$job_id));
		$get_job = $get_jobs->post;
		$result = update_post_meta($job_id, 'paymentdate', date('Y-m-d H:i:s'));
		$result = update_post_meta($job_id, 'jobstatus', 'complete');
	}
}
add_action('init', 'hrd_job_init_action');

function hrd_job_top() {
//echo "Request";var_dump($_REQUEST);
//hrd_job_init_action(); // Debug

//	echo 'page:hrd_job_top<br>';
	if(is_client()) {
//		echo 'is_client<br>';
		hrd_job_page_button('job_create');
//		hrd_entry_list();
		hrd_job_list();
	}
	if(is_develop()) {
//		echo 'is_develop<br>';
		hrd_job_page_button('job_serch');
		hrd_entry_list();
		hrd_job_list();

	}

}
add_shortcode('hrd_job_top', 'hrd_job_top');

// Client
function hrd_job_create() {
//	echo 'hrd_job_create<br>';
	//echo "Request";var_dump($_REQUEST);

	$request = $_REQUEST;

	$user_info = wp_get_current_user();
	$job_base_args = hrd_job_meta_base();
	$job_create_meta_args = hrd_job_meta_job_job();

	//	$today = date('Y-m-d');

	$post_result = hrd_job_post_update( 'job' , $job_create_meta_args , $request ,'entry' );

	if($post_result){
		$job_no = date(Ymd)."-".sprintf("%05d", $post_result);
		update_post_meta($post_result, 'jobno', $job_no);
		//		echo 'Result';
		$get_post = get_post($post_result);
		//echo 'Get:';var_dump($get_post);
	}

	if($get_post){
		// Post Result
		echo '<table>';
		echo '<caption>New Job Create</caption>';
		echo '<tr>';
		//		echo '<td>ID</td><td><input type="text" name="id" value="'.$get_post->ID.'"></td>';
		//		echo '<td>ID</td><td>'.$get_post->ID.'"</td>';
		echo '<td>JOB ID</td><td>'.$get_post->post_name.'</td>';
		echo '<input type="hidden" name="id" value="'.$get_post->ID.'">';
		echo '</tr>';

		// ユーザー名
		echo '<tr>';
//		echo '<td>'.__('Username').'</td>';
		echo '<td>'.'Username'.'</td>';
		echo '<td>'.$get_post->user_nicename.'</td>';
		echo '</tr>';

		// タイトル
		echo '<tr>';
		echo '<td>' . $job_base_args[1][name] . '(*)</td>';
		echo '<td colspan=2>';
		echo $get_post->post_title;
		echo '</td>';
		echo '</tr>';

		// コンテンツ
		echo '<tr>';
		echo '<td>' . $job_base_args[2][name] . '(*)</td>';
		echo '<td colspan=2>';
		echo $get_post->post_content;
		echo '</td>';
		echo '</tr>';

		foreach ($job_create_meta_args as $job_create_meta_arg) {
			if($post_result) {
				$get_meta = get_post_meta($post_result , $job_create_meta_arg[id], TRUE);
			}

			echo '<tr>';
			echo '<td>'.$job_create_meta_arg[name];
			if($job_create_meta_arg[must]){
				echo '(*)';
			}
			echo '</td>';
			echo '<td>';
			echo $job_create_meta_arg[type];
			echo $get_meta;
			echo '</td>';
		}

		echo '<tr>';
		echo '<td></td>';
		echo '<td>';
		hrd_job_page_button('job_disp');
		echo '</td>';
		echo '</tr>';
		echo '<table>';

	}else{
		// Create
		echo '<form action="" method="POST">';
		echo '<table>';
		echo '<caption>New Job Create</caption>';

		// ユーザー名
		echo '<tr>';
		echo '<td>'.'Username'.'</td>';
		echo '<td>'.$user_info->user_nicename.'</td>';
		echo '</tr>';

		// タイトル
		echo '<tr>';
		echo '<td>' . $job_base_args[1][name] . '(*)</td>';
		echo '<td colspan=2>';
		echo '<input type="' . $job_base_args[1][type] . '" name="' . $job_base_args[1][id] . '" value="'.$get_post->post_title.'">';
		echo '</td>';
		echo '</tr>';

		// コンテンツ
		echo '<tr>';
		echo '<td>' . $job_base_args[2][name] . '(*)</td>';
		echo '<td colspan=2>';
		echo '<textarea name="'.$job_base_args[2][id].'">'.$get_post->post_content.'</textarea>';
		echo '</td>';
		echo '</tr>';

		foreach ($job_create_meta_args as $job_create_meta_arg) {
			if($post_result) {
				$get_meta = get_post_meta($post_result , $job_create_meta_arg[id], TRUE);
			}

			echo '<tr>';
			echo '<td>';
			echo $job_create_meta_arg[name];
			if($job_create_meta_arg[must]){
				echo '(*)';
			}
			echo '</td>';
			echo '<td>';
//			echo $job_create_meta_arg[type];
			echo hrd_form_input($job_create_meta_arg, $get_meta, date('Y-m-d'));
			echo '</td>';
		}

		echo '<tr>';
		echo '<td></td>';
		echo '<td>';
		echo '<input type="submit" name="action" value="create">';
		echo '</td>';
		echo '</tr>';

		echo '<table>';
		echo '</form>';
	}
	echo '<div>';
	hrd_job_page_button('job_top');
	echo '</div>';
}
add_shortcode('hrd_job_create', 'hrd_job_create');

function hrd_job_list() {
//	echo 'hrd_job_list<br>';
//	echo "Request";var_dump($_REQUEST);

	$paged = get_query_var('paged') ? get_query_var('paged') : 1 ;
	$request = $_REQUEST;

	if(is_client()){
		$get_job_list_args = array(
				'author'=>get_current_user_id(),
				'paged'	=> $paged,
		);
//		$get_jobs = hrd_get_job($get_job_list_args);
	}
	if(is_develop()){
		$get_job_list_args = array(
//				'author'=>get_current_user_id(),
				'paged'	=> $paged,
//				'entry_id'	=> get_current_user_id(),
				'meta'	=> array(
				//		'relation' => 'OR',
				//		array('key'=>'status','value'=>'entry','compare'=>'='),
				//		array('key'=>'status','value'=>'select','compare'=>'='),
						array('key'=>'entry_id','value'=>get_current_user_id(),'compare'=>'='),
						array('key'=>'jobstatus','value'=>'entry','compare'=>'!='),
				),

		);
	}
	$get_jobs = hrd_get_job($get_job_list_args);

	$job_base_args = hrd_job_meta_base();
	$job_meta_args = hrd_job_meta_job_job();
	$job_status_args = array_shift(hrd_job_meta_job_status());

	$user_type = hrd_get_user_type();

	if(empty($post_id)){
		// リスト
		echo '<table>';
		echo '<caption>JobList</caption>';
		echo '<tr>';
//		echo '<th>'. __('Name') .'</th>';
		echo '<th>'. ('JobNo') .'</th>';
		echo '<th>'. ('Title') .'</th>';
		echo '<th>'. ('Entry End') .'</th>';
		echo '<th>'. ('Delivery Date') .'</th>';
		echo '<th>'. ('Author') .'</th>';
		echo '<th>'. ('Detail') .'</th>';
		echo '<th>'. ('Action') .'</th>';
		echo '</tr>';

		$jobs = $get_jobs->posts;
		if(empty($jobs)) {
			echo '<td colspan=5>Empty</td>';
		}else{
			foreach ($jobs as $job){
				$metas = hrd_get_post_meta($job->ID);
				echo '<tr>';
				echo '<td>'.$metas[jobno].'</td>';
				echo '<td>'.$job->post_title.'</td>';
				echo '<td>' . $metas[entryend] . '</td>';
				echo '<td>' . $metas[deliverydate] . '</td>';
				echo '<td>';
				$user_data = get_userdata( $job->post_author);
				echo $user_data->display_name;
				echo '</td>';
				echo '<td>';
				$hidden_args = array('id' => $job->ID ,'paged'=>$paged);
				hrd_job_page_button('job_disp',$hidden_args);
				echo '</td>';
				echo '<td>';
				// 依頼者
				$job_status = array_shift(hrd_get_post_meta($job->ID, 'jobstatus'));
//echo $job_status;
				if(is_client()){
					switch ($job_status) {
					case "entry" : // entry
						$get_entry_args = array(
							'meta'	=> array(array('key'=>'jobno','value'=>$metas[jobno],'compare'=>'='))
						);
						$get_entrys = hrd_get_entry($get_entry_args);

						if($get_entrys->post_count == 0) {
							echo "応募はありません。";
						}else{
							echo "応募は".$get_entrys->post_count."件です。";
							$hidden_args = array('id' => $job->ID ,'paged'=>$paged );
							hrd_job_page_button('entry_list',$hidden_args);
						}
						break;
					case "select": // select
					case "agreement"; // agreement
						echo "契約処理中";
						$hidden_args = array(
								'id'	=> $job->ID,
								'paged'	=> $paged,
						);
						$job_agreement = get_post_meta($job->ID, 'client_agreement', true);
						if(!($job_agreement)) {
							hrd_job_page_button('job_agreement', $hidden_args);
						}
						break;
					case "work"; // work
						echo "作業中";
							hrd_job_page_button('time_disp', $hidden_args);
						break;
					case "pay"; // complete
						echo "支払い処理";
							hrd_job_page_button('pay_disp', $hidden_args);
						break;
					case "complete"; // complete
						echo "支払い完了";
//							hrd_job_page_button('pay_disp', $hidden_args);
						break;
					case "end"; // end
						echo "終了";
						break;
					default:
					}
				}
				if(is_develop()){
					switch ($job_status) {
					case "select": // select
					case "agreement"; // agreement
						echo "契約処理中";
						$hidden_args = array(
//								'action'=>'agreement',
								'id'	=> $job->ID,
								'paged'	=> $paged,
						);
						$job_agreement = get_post_meta($job->ID, 'develop_agreement', true);
						if(!($job_agreement)) {
							hrd_job_page_button('job_agreement', $hidden_args);
						}
						break;
					case "work"; // work
						echo "作業中";
							hrd_job_page_button('time_disp', $hidden_args);
						break;
					case "pay"; // pay
						echo "支払い処理";
							hrd_job_page_button('pay_disp', $hidden_args);
						break;
					case "complete"; // complete
						echo "支払い完了";
//							hrd_job_page_button('pay_disp', $hidden_args);
						break;
					case "end"; // end
						echo "終了";
						break;
					default:
					}
				}
				echo '</td>';

				echo '</tr>';
			}
		}
		echo '</table>';

		if(function_exists('wp_pagenavi')) {
			wp_pagenavi(array('query'=>$get_jobs));
			wp_reset_query();
		}
	}
}
add_shortcode('hrd_job_list', 'hrd_job_list');

function hrd_job_disp() {
//echo 'hrd_job_disp<br>';
//echo "Request";var_dump($_REQUEST);
	$paged = get_query_var('paged') ? get_query_var('paged') : 1 ;

	$post_id = $_REQUEST[id];
	$action = $_REQUEST[action];

	$get_jobs = hrd_get_job($get_job_list_args);
	$job_base_args = hrd_job_meta_base();
	$job_meta_args = hrd_job_meta_job_job();
	$job_status_args = array_shift(hrd_job_meta_job_status());

	$get_post = get_post($post_id);
//var_dump($get_post);
	$metas = hrd_get_post_meta($post_id);

	// 更新処理
	if($action == "update") {
		$request = $_REQUEST;

		$get_post_status = hrd_get_post_meta($post_id,$job_status_args[id]);
		if($get_post_status) {
			$get_post_status = array_shift($get_post_status);
		}else{
			$get_post_status = "entry";
		}
		// Patch
		$get_jobno = hrd_get_post_meta($post_id,'jobno');

		if(empty($get_jobno)) {
//echo $get_post->post_date."<br>";
			$jobno = date('Ymd' , strtotime($get_post->post_date))."-".sprintf("%05d", $post_id);
//echo $jobno."<br>";
			update_post_meta($post_id,'jobno',$jobno);
		}
		//
		//var_dump($get_post_status);
		$update_result = hrd_job_post_update( 'job' , $job_meta_args, $request, $get_post_status);
		if($update_result) {
			$get_post = get_post($post_id);
			$action = "disp";
		}
		$metas = hrd_get_post_meta($post_id);
	}

	switch ($action){
		case "Update":
			// 修正
			echo '<form action="" method="post">';
			echo '<table>';
			echo '<caption>JobUpdate</caption>';
			echo '<tr>';
//			echo '<td>post_name</td>';
//			echo '<td>'.$get_post->post_name.'</td>';
			echo '<td>job_no</td>';
			echo '<td>'.$metas[jobno].'</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>post_author</td>';
			$user_data = get_userdata($get_post->post_author);
			echo '<td>'.$user_data->display_name.'</td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td>post_title</td>';
			echo '<td>';
			echo '<input type="' . $job_base_args[1][type] . '" name="' . $job_base_args[1][id] . '" value="'.$get_post->post_title.'">';
			echo '</td>';

			echo '</tr>';
			echo '<tr>';
			echo '<td>post_content</td>';
			echo '<td>';
			echo '<textarea name="'.$job_base_args[2][id].'">'.$get_post->post_content.'</textarea>';
			echo '</td>';
			echo '</tr>';

			$get_metas = hrd_get_post_meta($get_post->ID);
			foreach ($job_meta_args as $job_meta_arg) {
//			foreach ($metas as $job_meta_arg) {
//				echo hrd_form_input($job_meta_arg, $get_metas[$job_meta_value], date('Y-m-d'));
				echo '<tr>';
				echo '<td>';
				echo $job_meta_arg[id];
//				echo $metas[id];
				echo '</td>';
				echo '<td>';
				echo hrd_form_input($job_meta_arg, $get_metas[$job_meta_arg[id]], date('Y-m-d'));
//				echo hrd_form_input($job_meta_arg, $metas[$job_meta_arg[id]], date('Y-m-d'));
				echo '</td>';
				echo '</tr>';
			}

			echo '<tr>';
			echo '<td>';
			echo '</td>';
			echo '<td>';
			$hidden_args = array('id' => $get_post->ID, 'action' => 'update','paged'=>$paged );
			hrd_job_page_button('job_disp',$hidden_args);
			echo '</td>';
			echo '</tr>';
			echo '</form>';

			echo '<tr>';
			echo '<td>';
			echo '</td>';
			echo '<td>';
			$hidden_args = array('id' => $get_post->ID,'action'=>'disp','paged'=>$paged );
			hrd_job_page_button('job_disp',$hidden_args);
			echo '</td>';
			echo '</tr>';

			echo '</table>';
		break;

		case "JobDisp":
		case "disp":
		default:
			// 表示
			echo '<table>';
			echo '<caption>JobDetail</caption>';
			echo '<tr>';
//			echo '<td>post_name</td>';
//			echo '<td>'.$get_post->post_name.'</td>';
			$jobno = get_post_meta($get_post->ID,'jobno');
//			echo '<td>job_no</td>';
			echo '<td>'.$job_base_args[0]['name'].'</td>'; // jobno
			if(!empty($jobno)){
				$jobno = array_shift($jobno);
			}else{$jobno = ""; }
			echo '<td>'.$jobno.'</td>';
			echo '</tr>';
			echo '<tr>';
//			echo '<td>post_author</td>';
			echo '<td>'.$job_base_args[1]['name'].'</td>'; // author
			$user_data = get_userdata($get_post->post_author);
			echo '<td>'.$user_data->display_name.'</td>';
			echo '</tr>';
			echo '<tr>';
//			echo '<td>post_title</td>';
			echo '<td>'.$job_base_args[2]['name'].'</td>'; // post_title
			echo '<td>'.$get_post->post_title.'</td>';
			echo '</tr>';
			echo '<tr>';
//			echo '<td>post_content</td>';
			echo '<td>'.$job_base_args[3]['name'].'</td>'; // post_content
			echo '<td>'.$get_post->post_content.'</td>';
			echo '</tr>';

			echo '<tr><td colspan=2></td></tr>';

/* 			$metas = hrd_get_post_meta($get_post->ID);
			foreach ($metas as $meta_key => $meta_value) {
				if($meta_key != 'jobno') {
					echo '<tr>';
					echo '<td>'.$meta_key.'</td>';
					echo '<td>'.$meta_value.'</td>';
					echo '</tr>';
				}
			} */
			// job detail
			$job_meta_jobs = hrd_job_meta_job_job();
			foreach ($job_meta_jobs as $job_meta_job) {
				echo '<tr>';
				echo '<td>'.$job_meta_job['name'].'</td>';
				$job_meta_value = hrd_get_post_meta($get_post->ID,$job_meta_job[id] );
				echo '<td>'.$job_meta_value[0].'</td>';
				echo '</tr>';
			}
			$get_post_status = hrd_get_post_meta($get_post->ID , 'jobstatus');
			$get_post_status = array_shift($get_post_status);
			if( ($get_post_status == 'paid')
			 OR ($get_post_status == 'complete')
			 OR ($get_post_status == 'end') ) {
			// Payment
				echo '<tr><td colspan=2></td></tr>';
				$job_meta_pays = hrd_job_meta_job_pay();
				foreach ($job_meta_pays as $job_meta_pay) {
					echo '<tr>';
					echo '<td>'.$job_meta_pay['name'].'</td>';
					$job_meta_pay_value = hrd_get_post_meta($get_post->ID,$job_meta_pay[id] );
					echo '<td>'.$job_meta_pay_value[0].'</td>';
					echo '</tr>';
				}
			}

			echo '<tr><td>JobStatus</td><td>'.$get_post_status.'</td></tr>'; // debug


			// 更新ボタン
			if( ($get_post_status == 'entry') ) {
				echo '<tr>';
				echo '<td>';
				echo '</td>';
				echo '<td>';
				$hidden_args = array('id' => $get_post->ID, 'action'=>'Update','paged'=>$paged);
				hrd_job_page_button('job_disp',$hidden_args);
				echo '</td>';
				echo '</tr>';
			}
			// 評価
			if( ($get_post_status == 'complete') ) {
				if(function_exists('reaction_buttons_html')) {
					echo '<td>';
					echo '</td>';
					echo '<td>';
					echo reaction_buttons_html();
					echo '</td>';
					echo '</tr>';
				}
			}

			echo '</table>';
	} // Swich End
	$hidden_args = array('paged'=>$paged);
	hrd_job_page_button('job_top');
}
add_shortcode('hrd_job_disp', 'hrd_job_disp');

function hrd_job_update() {
//	echo 'hrd_job_update<br>';
	hrd_job_page_button('job_top');

}
//add_shortcode('hrd_job_update', 'hrd_job_update');


// Develop
function hrd_job_serch() {
//	echo 'hrd_job_serch<br>';
//echo "Request";var_dump($_REQUEST);

	$paged = get_query_var('paged') ? get_query_var('paged') : 1 ;
	$request = $_REQUEST;

	$serch_args = hrd_job_meta_serch();
	$job_metas = hrd_job_meta_job_job();

	if(!empty($_REQUEST[action])){
		$action = $_REQUEST[action];
	}
	if(!empty($_REQUEST)){
		//		if(!empty($_REQUEST[freeword])){
		$serch_requests[freeword] = $_REQUEST[freeword];
		foreach ($serch_args as $serch_arg) {
			foreach ($job_metas as $job_meta) {
				if($job_meta[id] == $serch_arg[key]) {
					$serch_requests[$job_meta[key]] = $request[$job_meta[id]];
				}
			}
		}
		$serch_requests[paged]	= $paged;
	}

//echo "Serch";var_dump($serch_requests);

	switch ($action) {
		case "detail":
			$job_args = array();
			$post_id = $_REQUEST['id'];
			$job_args['post_id'] = $post_id;
			$get_jobs = hrd_get_job($job_args);
			$get_job = $get_jobs->post;
			$get_job_meta = hrd_get_post_meta($get_job->ID);

			echo '<table>';
			echo '<caption>JobDetail</caption>';
			echo '<tr>';
//			echo '<th>'. __('Name') .'</th>';
//			echo '<td>'. $get_job->post_name. '</td>';
			echo '<th>'. __('Jobno') .'</th>';
			echo '<td>'. $get_job_meta[jobno]. '</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>'. __('Title') .'</th>';
			echo '<td>'. $get_job->post_title.'</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>'. __('entryend') .'</th>';
			echo '<td>'.$get_job_meta[entryend].'</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>'. __('hourlyprice') .'</th>';
			echo '<td>'.$get_job_meta[hourlyprice].'</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>'. __('deliverydate') .'</th>';
			echo '<td>'.$get_job_meta[deliverydate].'</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>'. __('forecastvolume') .'</th>';
			echo '<td>'.$get_job_meta[forecastvolume].'</td>';
			echo '</tr>';

			echo '<tr>';
			echo '<th>'. __('Author') .'</th>';
			$user_data = get_userdata( $get_job->post_author);
			echo '<td>'.$user_data->display_name.'</td>';
			echo '</tr>';

			if(is_develop()){
				echo '<tr>';
				echo '<th>'. __('status') .'</th>';
				$entry_args = array();
				$entry_args[post_id] = $get_job->ID;
				$entry_args[author]	= get_current_user_id();
				$get_entrys = hrd_get_entry($entry_args);
				if($get_entrys->post_count == 0 ) {
					echo '<td>';
					echo '未応募';
					echo '</td>';
					echo '</tr>';
					echo '<tr>';
					echo '<th>'. __('action') .'</th>';
					echo '<td>';
					$hidden_args = array('job_id' => $get_job->ID, 'action' => 'entry' );
					hrd_job_page_button('entry_disp',$hidden_args);

					echo '</td>';
				}
				echo '</tr>';
			}
			echo '</table>';

			break; // detail end

		case "Serch":
			hrd_job_page_button('job_serch');

			if(!empty($request)){
				$meta_marge = array();
				$serch_requests = array();
				$serch_query = $serch_requests;
				$serch_query[paged] = $paged;
				foreach ($request as $serch_key => $serch_value ){
					foreach ($serch_args as $serch_arg_key => $serch_arg_value) {
						if(($serch_arg_value[key] == $serch_key) AND !empty($serch_value)) {
							$serch_arg_value[value] = $serch_value;
							$meta_marge = array_merge($meta_marge , array($serch_arg_value) );
							break;
						}
					}
				}

				$serch_query[meta] = $meta_marge;
				$get_jobs = hrd_get_job($serch_query);
	//var_dump($get_jobs);
			}else{
				$serch_query[paged] = $paged;
				$get_jobs = hrd_get_job($serch_query);
			}

			echo '<table>';
			echo '<caption>JobSerchResult</caption>';
			echo '<tr>';
			echo '<th>'. __('Jobno') .'</th>';
			echo '<th>'. __('Title') .'</th>';
			echo '<th>'. __('entryend') .'</th>';
			echo '<th>'. __('hourlyprice') .'</th>';
			echo '<th>'. __('deliverydate') .'</th>';
			echo '<th>'. __('forecastvolume') .'</th>';
			echo '<th>'. __('Author') .'</th>';
			echo '<th>'. __('status') .'</th>';
			echo '<th>'. __('action') .'</th>';
			echo '</tr>';

			$jobs = $get_jobs->posts;
			foreach ($jobs as $job) {
				//var_dump($job);
				$metas = hrd_get_post_meta($job->ID);
				echo '<tr>';
//				echo '<td>'.$job->post_name.'</td>';
				echo '<td>' . $metas[jobno].'</td>';
				echo '<td>' . $job->post_title.'</td>';
				echo '<td>' . $metas[entryend] . '</td>';
				echo '<td>' . $metas[hourlyprice] . '</td>';
				echo '<td>' . $metas[deliverydate] . '</td>';
				echo '<td>' . $metas[forecastvolume] . '</td>';
				echo '<td>';
				$user_data = get_userdata( $job->post_author);
				echo $user_data->display_name;
				echo '</td>';

				// 開発者
				if(is_develop()){
					echo '<td>';
					$get_entry_args = array(
							'author'	=> get_current_user_id(),
							'title'		=> $job->post_name,
					);
					$get_entrys = hrd_get_entry($get_entry_args);
					if(($get_entrys->post_count) != 0){
						echo "応募済み";
					}
					echo '</td>';
				}
				echo '<td>';
				echo '<form action="" method="post">';
				echo '<input type="hidden" name="action" value="detail">';
				echo '<input type="hidden" name="id" value="'.$job->ID .'">';
				echo '<input type="submit" value="detail">';
				echo "</form>";
				echo '</td>';
				echo '</tr>';
			}
			echo '</table>';

			if(function_exists('wp_pagenavi')) {
				wp_pagenavi(array('query'=>$get_jobs));
//				$navi = wp_pagenavi(array('query'=>$get_jobs,'echo'=>false));
//				var_dump($navi);
//				wp_reset_query();
			}
			break;

			case "JobSerch":
			default:
				echo '<table>';
				echo '<caption>JobSerch</caption>';
		//		echo '<form action="" method="POST">';
				echo '<form action="" method="GET">';
				echo '<input type="hidden" name="id" value="'.$job->ID.'">';
				// Free Word
				echo '<tr>';
				echo '<td>';
				echo 'FreeWord';
				echo '</td>';
				echo '<td>';
				echo '<input type="text" name="freeword" value="';
				if(!empty($_REQUEST[freeword])){
					echo $_REQUEST[freeword];
				}
				echo '">';
				echo '</td>';
				echo '</tr>';

				// Item
				foreach ($serch_args as $serch_arg) {
					//		echo '<input type="hidden" name="id" value="'.$job->ID.'">';
					foreach ($job_metas as $job_meta) {
						if($job_meta[id] == $serch_arg[key]) {
							echo '<tr>';
							echo '<td>';
							echo $job_meta[name];
							echo '</td>';
							echo '<td>';
							echo '<input ';
							echo 'type="'.$job_meta[type].'"';
							echo 'name="'.$serch_arg[key].'"';
							if(!empty($serch_requests[$job_meta[id]])){
								echo 'value="'.$serch_requests[$job_meta[id]].'"';
							}
							echo '>';
							echo '</td>';
							echo '</tr>';
							break;
						}
					}
				}
				echo '<tr>';
				echo '<td>';
				echo '</td>';
				echo '<td>';
				echo '<input type="submit" name="action" value="Serch">';
				echo '</td>';
				echo '</tr>';
				echo '</form>';
				echo '</table>';

				$action = "default";

//				break; // Serch
	} // Swich End
	hrd_job_page_button('job_top');
}
add_shortcode('hrd_job_serch', 'hrd_job_serch');

function hrd_entry_list() {
//echo 'hrd_entry_list<br>';
//echo "Request";var_dump($_REQUEST);
	$paged = get_query_var('paged') ? get_query_var('paged') : 1 ;
	$action = $_REQUEST['action'];
	$doing = $_REQUEST['doing'];

//	hrd_entry_selected(); // DEBUG

	if(is_client()){

		$get_job = hrd_get_job(array('post_id'=>$_REQUEST[id]));
		$get_job = $get_job->post;
//		$job_id = $_REQUEST[id];
		$job_no = get_post_meta($get_job->ID,'jobno');
		$job_no = array_shift($job_no);
		//var_dump($job_no);
		//var_dump($get_job);
		if(empty($_REQUEST['id'])){
			$get_my_job_args = array(
					'post_type'	=> 'job',
					'author'	=> get_current_user_id()
			);
			$get_my_jobs = new WP_Query($get_my_job_args);
			$get_my_jobs = $get_my_jobs->posts;
			wp_reset_postdata();

/*			$my_jobs = array();
			foreach ($get_my_jobs as $get_my_job) {
				array_push($my_jobs , $get_my_job->ID);
			}
			$meta_args = array();
			foreach ($my_jobs as $key => $value) {
				array_push( $meta_args , array('key'=>'jobid','value'=>$value,'compare'=>'=') );
			}*/
			$get_job = array();
			$meta_args = array();
			foreach ($get_my_jobs as $get_my_job) {
				array_push( $meta_args , array('key'=>'jobid','value'=>$get_my_job->ID,'compare'=>'=') );
			}
//var_dump($meta_args);
			$get_entrys_args = array(
				'meta_query'=> array('relation'=>'OR',$meta_args),
			);
//var_dump($get_entrys_args);
		}else{
	 		$get_entrys_args = array('meta'=> array(array(
									'key'=>'jobid',
									'value'=>$_REQUEST[id],
									'compare'=>'='
								)));
		}
		$get_entrys = hrd_get_entry($get_entrys_args);
//var_dump($get_entrys);

		// 表示
		if($get_entrys->post_count == 0){
			echo '<table>';
			echo '<caption></caption>';
			echo '<tr><td>Empty</td></tr></table>';
		}else{
			$get_entrys = $get_entrys->posts;
//var_dump($get_entrys);
			echo '<table>';
			echo '<caption></caption>';
			echo '<tr>';
			echo '<td>job_no</td>';
			echo '<td>'.$job_no.'</td>';
			echo '</tr>';
			echo '</table>';

			// jobstatus
			$job_status = $get_job->jobstatus;
//echo $job_status;
			switch ($job_status){
			// entry
				case "entry":
					if($action == "EntryList"){
						echo '<table>';
						echo '<caption>EntryList</caption>';
						echo '<tr>';
						echo '<td>develop</td>';
						echo '<td>Entry</td>';
						echo '<td>UserDetail</td>';
						echo '</tr>';
							foreach ($get_entrys as $get_entry) {
								echo '<tr>';
								$user_data = get_userdata($get_entry->post_author);
								echo '<td>'.$user_data->display_name.'</td>';
								echo '<td>'.date('Y-m-d',strtotime($get_entry->post_date)).'</td>';
								echo '<td>';

								echo '<form acton="" method="post">';
								echo '<input type="hidden" name="id" Value="'.$_REQUEST[id].'">';
								echo '<input type="hidden" name="paged" Value="'.$_REQUEST[paged].'">';
								echo '<input type="hidden" name="user_id" Value="'.$_REQUEST[user_id].'">';
								echo '<input type="hidden" name="entry_id" Value="'.$get_entry->ID.'">';
								echo '<input type="hidden" name="action" value="EntryList">';

								if(($doing == 'Open')
								AND( $get_entry->ID == $_REQUEST[entry_id] )){
									echo '<input type="submit" name="doing" value="Close">';
								}else{
									echo '<input type="submit" name="doing" value="Open">';
								}
								echo '<input type="submit" name="doing" value="Select">';
								echo '</form>';

								echo '</td>';
								echo '</tr>';

								if(($doing == "Open")
								AND( $get_entry->ID == $_REQUEST[entry_id] )) {
									echo '<tr>';
									echo '<td colspan=3>';

									hrd_user_disp($get_entry->post_author);

									$hidden_args = array(
										'id' => $_REQUEST['id'],
										'paged'=>$paged,
										'entry_id'	=> $_REQUEST['entry_id'],
									);
								echo '</td>';
								echo '</tr>';
								}

							} // end foreach

//						echo '<tr><td colspan=3>';
//						hrd_job_page_button('entry_list',$hidden_args);
//						echo '</tr></tr>';
						echo '</table>';
					}
					break;
				// select

				// agreement

				// work

				// complete

				// end
			} // switch end
		}
	}
	if(is_develop()){
		$entry_args = array();
		$entry_args[author]	= get_current_user_id();
		$entry_args[paged]	= $paged;
		$entry_args[meta]	= array(
//						'relation' => 'OR',
						array('key'=>'status','value'=>'entry','compare'=>'='),
//						array('key'=>'status','value'=>'select','compare'=>'='),
						);
		$get_entrys = hrd_get_entry($entry_args);
		$get_entrys = $get_entrys->posts;

	//var_dump($get_entrys);
		echo '<table>';
		echo '<caption>EntryList</caption>';
		echo '<tr>';
/* 		echo '<td>job_no</td>';
		echo '<td>job_title</td>';
		echo '<td>entryend</td>';
		echo '<td>deliverydate</td>';
		echo '<td>forecastvolume</td>';
		echo '<td>job_status</td>';
 */
		echo '<th>'.('JobNo').'</th>';
		echo '<th>'.('Title').'</th>';
		echo '<th>'.('Entry End').'</th>';
		echo '<th>'.('Delivery Date').'</th>';
		echo '<th>'.('Forecast Volume').'</th>';
		echo '<th>'.('Job Status').'</th>';

		echo '</tr>';
		if(empty($get_entrys)) {
			echo '<td colspan=6>Empty</td>';
		}else{
			foreach ($get_entrys as $get_entry) {

				$get_job = hrd_get_job(array('post_id'=>$get_entry->jobid));
				$get_job = $get_job->post;
		//var_dump($get_job);
				$job_metas = hrd_get_post_meta($get_job->ID);
		//var_dump($job_metas);
				$entry_metas = hrd_get_post_meta($get_entry->ID);
		//var_dump($metas);
				echo '<tr>';
				echo '<td>'.$job_metas[jobno].'</td>';
				echo '<td>'.$get_entry->post_title.'</td>';

				echo '<td>'.$job_metas[entryend].'</td>';
				echo '<td>'.$job_metas[deliverydate].'</td>';
				echo '<td>'.$job_metas[forecastvolume].'</td>';

				echo '<td>';
				echo $job_metas[jobstatus];
		//		$hidden_args = array('entry_id' => $get_entry->ID ,'paged'=>$paged);
				$hidden_args = array('job_id' => $entry_metas[jobid] ,'paged'=>$paged );
				hrd_job_page_button('entry_disp',$hidden_args);
				echo '</td>';
				echo '</tr>';
			}
		}
		echo '</table>';
	}
	hrd_job_page_button('job_top');
}
add_shortcode('hrd_entry_list', 'hrd_entry_list');

function hrd_entry_disp() {
//echo 'hrd_entry_disp<br>';
//echo "Request";var_dump($_REQUEST);
	$request = $_REQUEST;
	$job_id = $request[job_id];
	//	$entry_id = $request[entry_id];

	//	$job_meta_args = hrd_job_meta_job_job();
	$job_meta_args = hrd_job_meta_job();
	$entry_meta_args = hrd_job_meta_entry();
	$get_job_args = array(
			'post_id'=>$job_id,
	);
	$get_job = hrd_get_job( $get_job_args );
	$get_job = $get_job->post;
	//	$get_job_jobno = get_post_meta($job_id,'jobno');
	$get_job_jobno = hrd_get_post_meta($job_id,'jobno');
	if (!empty($get_job_jobno)) {
		$get_job_jobno = array_shift($get_job_jobno);
	}

	if($request['action'] == "update") {

//		$entry_checks = hrd_get_entry(array('meta'=>array('jobno'=>$get_job->ID)));
		$get_entry_args = array(
				'author'=> get_current_user_id(),
//				'meta'	=> array(array('key'=>'jobno','value'=>$metas[jobno],'compare'=>'='))
				'meta'	=> array(array('key'=>'jobid','value'=>$job_id,'compare'=>'='))
		);
		$entry_checks = hrd_get_entry($get_entry_args);
//var_dump($entry_checks);

		$entry_args = array();
		if($entry_checks->post_count == 0 ) {
//			$entry_args[title]	= $get_job->post_title;
			$entry_args[title]	= $get_job->jobno;
			$entry_args[jobid]	= $get_job->ID;
			$entry_args[jobno]	= $get_job_jobno;
			$entry_args[entrycomment]	= $request[entrycomment];
//var_dump($entry_args);
			$post_result = hrd_job_post_update('entry', $entry_meta_args, $entry_args, 'entry' );
			$request[id] = $post_result;
		}else{
//			$entry_args[id] = $entry_checks->post->ID;
//			$entry_args[entrycomment]	= $request[entrycomment];
//			$post_result = hrd_job_post_update('entry', $entry_meta_args, $entry_args, 'entry' );
			$meta_result = update_post_meta($entry_checks->post->ID, 'entrycomment', $request[entrycomment]);
			$request[id] = $entry_checks->post->ID;
		}
	}

	// 表示
	if(is_client()){
		$get_entry_args = array(
				'post_id'=>$request[id]
		);
	}
	if(is_develop()){
		$get_entry_args = array(
//				'post_id'=>$request[id],
				'author'=>get_current_user_id(),
				'meta'	=> array(
					array('key'=>'jobid', 'value'=>$job_id, 'compare'=>'='),
				)
		);
	}
	$get_entrys = hrd_get_entry($get_entry_args);
//var_dump($get_entrys);
//var_dump($get_entrys->post);
//echo $get_entrys->post_count;

	if($get_entrys->post_count != 0 ) {
		$get_entry = $get_entrys->post;
//echo $get_entrys->post_count;
	}
//var_dump($get_entrys);
//echo $get_entrys->found_posts;
//	$jobstatus = get_post_meta($job_id, 'jobstatus',true);
//	if($jobstatus == "entry"){
		echo '<form action="" method="POST">';
		echo '<table>';
		echo '<caption>EntryDetail</caption>';
		echo '<tr>';
		echo '<td>Jobno</td>';
		echo '<td>'.$get_job_jobno.'</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>post_author</td>';
		$post_author = get_userdata($get_job->post_author);
		echo '<td>'.$post_author->display_name.'</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>post_title</td>';
		echo '<td>'.$get_job->post_title.'</td>';
		echo '</tr>';
		echo '<tr>';
		echo '<td>post_content</td>';
		echo '<td>'.$get_job->post_content.'</td>';
		echo '</tr>';

		$get_job_metas = hrd_get_post_meta($job_id);
		//var_dump($get_job_metas);
		foreach ($job_meta_args as $job_meta_arg) {
			if(!empty($get_job_metas[$job_meta_arg[id]])) {
				echo '<tr>';
				echo '<td>'.$job_meta_arg[id].'</td>';
				echo '<td>'.$get_job_metas[$job_meta_arg[id]].'</td>';
				echo '</tr>';
			}
		}
		echo '<tr>';
		echo '<td>entry_comment</td>';
		echo '<td>';
		echo '<input type="hidden" name="job_id" value="'.$job_id.'">';
		echo '<input type="hidden" name="action" value="update">';
		echo '<textarea name="entrycomment">';
		//	$get_entry_comment = hrd_get_post_meta($get_entry->ID,'entrycomment');
		if(!empty($get_entry)){
			$get_entry_comment = get_post_meta($get_entry->ID,'entrycomment',true);
			if(!empty($get_entry_comment)) {
//				$get_entry_comment = array_shift($get_entry_comment);
				echo $get_entry_comment;
			}
		}
		echo '</textarea>';

		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td></td>';
		echo '<td>';
		echo '<input type="submit" name="action" value="update">';
		echo '</td>';
		echo '</tr>';

		echo '</table>';
		echo '</form>';
//	}
	hrd_job_page_button('job_top');
}
add_shortcode('hrd_entry_disp', 'hrd_entry_disp');

function hrd_job_agreement() {
//echo 'hrd_job_agreement<br>';
//echo "Request";var_dump($_REQUEST);

	$job_id = $_REQUEST[id];
	$jobs = hrd_get_job(array('id'=>$job_id));
	$job = $jobs->post;
	//var_dump($job);
	$job_name = get_post_meta($job_id,'jobno',true);
	$cliant = get_userdata($job->post_author);
	//var_dump($cliant);
	$develop = get_userdata(get_post_meta($job_id,'entry_id',true));
	//var_dump($develop);

	$consent_ja = '<h3>契約書</h3>'
		.'<table>'
		.'<tr><td>委託者</td><td>'.$cliant->display_name.'</td>'
		.'<tr><td>受託者</td><td>'.$develop->display_name.'</td>'
		.'<tr><td>案件番号</td><td>'.$job_name.'</td>'
		.'<tr><td>報告</td><td></td>'
		.'<tr><td>成果物</td><td></td>'
		.'<tr><td>契約終了</td><td></td>'
		.'<tr><td>報酬</td><td></td>'
		.'<tr><td>成果物の権利</td><td></td>'
		.'<tr><td>契約解除</td><td></td>'
		.'</table>'
		.'<b>上記の内容に同意します。</b>';
	$consent_en = '<h3>Contract</h3>'
		.'<table>'
		.'<tr><td>Consignor</td><td>'.$cliant->display_name.'</td>'
		.'<tr><td>Trustee</td><td>'.$develop->display_name.'</td>'
		.'<tr><td>Case number</td><td>'.$job_name.'</td>'
		.'<tr><td>Report</td><td></td>'
		.'<tr><td>Deliverable</td><td></td>'
		.'<tr><td>Contract termination</td><td></td>'
		.'<tr><td>Reward</td><td></td>'
		.'<tr><td>Artifacts right</td><td></td>'
		.'<tr><td>Termination of contract</td><td></td>'
		.'</table>'
		.'<b>I agree to the above-mentioned contents.</b>';

	$jobstatus = get_post_meta($job->ID, 'jobstatus',true);
	if($jobstatus == "select"){
		echo '<table>';
		echo '<tr>';
		echo '<td>job_no</td>';
		echo '<td>'.$job_name.'</td>';
		echo '</tr>';
		echo '</table>';

		if(is_client()){
			echo $consent_ja;
		}
		if(is_develop()){
			echo $consent_en;
		}
		echo '<table>';
		echo '<tr>';
		echo '<td>';
		echo '<form action="'.hrd_get_page_url('job_top').'" method="POST">';
		echo '<input type="hidden" name="id" value="'.$job_id.'" >';
		echo '<input type="hidden" name="paged" value="'.$paged.'" >';
		echo '<input type="hidden" name="action" value="agreement" >';
		echo '<input type="submit" name="doing" value="I agree" >'; // 同意する
		echo '</td>';
		echo '<td>';
		echo '<input type="submit" name="doing" value="Disagree" >'; // 同意しない
		echo '</form>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';
	}

	hrd_job_page_button('job_top');
}
add_shortcode('hrd_job_agreement', 'hrd_job_agreement');

function hrd_time_disp() {

//echo 'hrd_time_disp<br>';
//echo "Request";var_dump($_REQUEST);
//hrd_job_init_action(); // Debug

	$paged = get_query_var('paged') ? get_query_var('paged') : 1 ;
	$job_id = $_REQUEST['id'];

	$get_job_args = array(
			'post_id'=>$job_id,
	);
	$get_job = hrd_get_job( $get_job_args );
	$get_job = $get_job->post;
	$jobstatus = get_post_meta($get_job->ID, 'jobstatus',true);

	$get_time_args = array(
		'orderby'	=> 'ID',
		'order'		=> 'DESC',
		'meta'		=> array( 'key'=>'jobid', 'value'=>$job_id,'compare'=>'='  )
	);
	$get_times = hrd_get_time($get_time_args);
//var_dump($get_times);
//echo $get_times->post_count;
	$get_time_posts = $get_times->posts;
	$get_time = $get_times->post;
//var_dump($get_time);
//echo get_post_meta($get_time->ID, 'status', true);
	if($get_times->post_count == 0){
		$time_status = "start";
	}elseif(get_post_meta($get_time->ID, 'status', true) == 'start'){
		$time_status = "end";
	}else{
		$time_status = "start";
	}

	if($jobstatus == "work"){
		echo '<table>';
		echo '<tr>';
		echo '<td>job_no</td>';
		echo '<td>'.get_post_meta($get_job->ID,'jobno',true).'</td>';
		echo '</tr>';
		echo '</table>';

		echo 'FileUpload';
		echo '<table>';
		echo '<tr>';
		echo '<th>';
		echo 'Date';
		echo '</th>';
		echo '<th>';
		echo 'File';
		echo '</th>';
		echo '</tr>';

		echo '<table>';
		echo '<tr>';
		echo '<th>id</th>'; // debug
		echo '<th>Start</th>';
		echo '<th>End</th>';
		echo '<th>Time[Hour]</th>';
		echo '</tr>';

		if(is_develop()){
			echo '<tr>';
			echo '<td></td>'; // debug
			echo '<td>';
			if($time_status == "start") {
				echo '<form action="" method="POST">';
				echo '<input type="hidden" name="id" value="'.$job_id.'">';
				echo '<input type="hidden" name="paged" value="'.$paged.'">';
				echo '<input type="hidden" name="action" value="'.$_REQUEST['action'].'">';
				echo '<input type="submit" name="doing" value="Start">';
				echo '</form>';
			}
			if($time_status == "end") {
				$start_time = get_post_meta($job_id, 'jobstart',true);
				echo $start_time;
			}
			echo '</td>';

			echo '<td>';
			if($time_status == "end") {
//				echo "id:".$get_time->ID;
				echo '<form action="" method="POST">';
				echo '<input type="hidden" name="id" value="'.$job_id.'">';
				echo '<input type="hidden" name="time_id" value="'.$get_time->ID.'">';
				echo '<input type="hidden" name="paged" value="'.$paged.'">';
				echo '<input type="hidden" name="action" value="'.$_REQUEST['action'].'">';
				echo '<input type="submit" name="doing" value="End">';
				echo '</form>';
			}
			echo '</td>';
			echo '</tr>';
		}
		if(is_client()){
			echo '<tr>';
			echo '<td colspan=2>';
			echo '</td>';
			echo '<td>';
			echo '<form action="" method="POST">';
			echo '<input type="hidden" name="id" value="'.$job_id.'">';
			echo '<input type="hidden" name="paged" value="'.$paged.'">';
			echo '<input type="hidden" name="action" value="'.$_REQUEST['action'].'">';
			echo '<input type="submit" name="doing" value="JobComlete">';
			echo '</form>';
			echo '</td>';
			echo '</tr>';
		}

		$total_time = 0;
		foreach ($get_time_posts as $get_time_post) {
			echo '<tr>';
			echo '<td>'.$get_time_post->ID.'</td>'; // debug
			echo '<td>';
//			echo $get_time_post->ID;
			echo $get_time_post->jobstart;
			echo '</td>';
			echo '<td>';
			echo $get_time_post->jobend;
			echo '</td>';
			echo '<td>';
			echo $get_time_post->jobtime;
			$total_time += $get_time_post->jobtime;
			echo '</td>';
			echo '</tr>';
		}
		echo '<tr>';
		echo '<td colspan=2>';
		echo 'Total';
		echo '</td>';
		echo '<td>';
		echo $total_time;
		echo '</td>';
		echo '</tr>';
		echo '</table>';
	}
	hrd_job_page_button('job_top');

}
add_shortcode('hrd_time_disp', 'hrd_time_disp');

function hrd_pay_disp() {
//echo 'hrd_pay_disp<br>';
//echo "Request";var_dump($_REQUEST);

	$paged = get_query_var('paged') ? get_query_var('paged') : 1 ;
	$job_id = $_REQUEST['id'];

	$get_job_args = array(
			'post_id'=>$job_id,
	);
	$get_job = hrd_get_job( $get_job_args );
	$get_job = $get_job->post;
	$job_no = get_post_meta($get_job->ID,'jobno',true);

	echo '<table>';
	echo '<tr>';
	echo '<td>job_no</td>';
	echo '<td>'.$job_no.'</td>';
	echo '</tr>';
	echo '</table>';

	$time		= get_post_meta($get_job->ID, 'workingtime',true);
	$hourlyprice = get_post_meta($get_job->ID, 'hourlyprice',true);
	$fee		= get_post_meta($get_job->ID, 'fee',true);
	$tax		= get_post_meta($get_job->ID, 'tax',true);
	$payment	= get_post_meta($get_job->ID, 'payment',true);
/* 	$paypalfee	= get_post_meta($get_job->ID, 'paypalfee',true);
	$total		= get_post_meta($get_job->ID, 'total',true); */
	$domesticfee	= get_post_meta($get_job->ID, 'paypaldomesticfee',true);
	$clienttotal		= get_post_meta($get_job->ID, 'clienttotal',true);
	$overseasfee	= get_post_meta($get_job->ID, 'paypaloverseasfee',true);
	$developertotal		= get_post_meta($get_job->ID, 'developertotal',true);

	$crowdiafee		= get_post_meta($get_job->ID, 'crowdiafee',true);

	echo '<table>';

	echo '<tr>';
	echo '<td>A</td><th>WorkingTime[Hour]</th>';
	echo '<td>';
	echo $time;
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>B</td><th>hourlyprice[USD]</th>';
	echo '<td>';
	echo $hourlyprice;
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>C</td><th>Payment[USD] A * B</th>';
	echo '<td>';
	echo $payment;
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>D</td><th>Fee[USD] C * '.hrd_const('fee').'%</th>';
	echo '<td>';
	echo $fee;
	echo '</td>';
	echo '</tr>';

	echo '<tr><td colspan=3>Client</td></tr>';
	echo '<tr>';
	echo '<td>E</td><th>Tax[USD] D * '.hrd_const('tax').'%</th>';
	echo '<td>';
	echo $tax;
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>F</td><th>PayPal Domestic Fee[USD]  '.hrd_const(DomesticFee).'%</th>';
	echo '<td>';
	echo $domesticfee;
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>G</td><th>Client Total[USD] C + D + E + F</th>';
	echo '<td>';
	echo $clienttotal;
	echo '</td>';
	echo '</tr>';

	echo '<tr><td colspan=3>Developer</td></tr>';
	echo '<tr>';
	echo '<td>H</td><th>PayPal Overseas Fee[USD]  '.hrd_const(OverseasFee).'%</th>';
	echo '<td>';
	echo $overseasfee;
	echo '</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<td>I</td><th>Developer Total[USD] C + D + H</th>';
	echo '<td>';
	echo $developertotal;
	echo '</td>';
	echo '</tr>';

	echo '<tr><td colspan=3>Crowdia</td></tr>';
	echo '<tr>';
	echo '<td>J</td><th>Clowdia Fee[USD] G - I</th>';
	echo '<td>';
	echo $crowdiafee;
	echo '</td>';
	echo '</tr>';

	if(is_client()){
		// Paypalからの戻りURL作成
		$site_url = hrd_get_page_url('job_top');
//		$site_url = hrd_get_page_url('job_disp');
		$page_query = array(
			'id'		=> $job_id,
			'paged'		=> $paged,
			'action'	=> 'paid'
		);
		$ret_url = add_query_arg($page_query, $site_url);

		echo'<tr>';
		echo'<th></th>';
		echo'<td>';

		$target_email = hrd_const('payed_acount');
		if(empty($target_email)){
			$admin_userdata = get_user_by('slug', 'admin');
			$target_email = $admin_userdata->data->user_email;
		}

		$paypal_args[target_email] = $target_email;
		$paypal_args[job_no] = $job_no;
		$paypal_args[job_name] = $get_job->post_title;
		$paypal_args[pay] = $payment + $fee;
		$paypal_args[tax] = round( $tax , 2);
		$paypal_args[thankyou_page_url] = $ret_url;
//var_dump($paypal_args);
		if(function_exists('wpdev_paypal_button')){
			hrd_paypal_api($paypal_args);
		}else{
//			echo '<form action="" method="post">';
//			echo '<input type="submit" name="pay" value="Pay">';
//			echo '</form>';
			echo "I'm Sorry.Payment [PayPal] is Not Service!";
		}

		echo'</td>';
		echo'</tr>';
	}

	echo '</table>';


	hrd_job_page_button('job_top');
}
add_shortcode('hrd_pay_disp', 'hrd_pay_disp');

function hrd_paypal_api($paypal_args) {
	$text = '';
	$text = '[paypal_button type=paynow';

	if(!empty($paypal_args[target_email])) { $text .=' email='.$paypal_args[target_email]; }
	if(!empty($paypal_args[job_no])) { $text .=' id='.$paypal_args[job_no]; }
	if(!empty($paypal_args[job_name])) { $text .=' name='.$paypal_args[job_name]; }
	if(!empty($paypal_args[pay])) { $text .=' amount='.$paypal_args[pay]; }
	//	if(!empty($paypal_args[fee])) { $text .=' shipping_charges='.$paypal_args[fee]; }
	$text .=' open_new_window = 1';
	if(!empty($paypal_args[tax])) { $text .=' tax_rate='.$paypal_args[tax]; }
	if(!empty($paypal_args[thankyou_page_url])) { $text .=' thankyou_page_url='.$paypal_args[thankyou_page_url]; }
//	if(!empty($paypal_args[open_new_window])) { $text .=' open_new_window='.$paypal_args[open_new_window]; }
	$text .=' open_new_window=0';
//	$text .=' open_new_window=1'; // Default 1 is Target=_blank

	$text .=']';
//var_dump($paypal_args);
//echo $text;
echo 'Debug:DirectLInk<a href="'.$paypal_args[thankyou_page_url].'">PaypalReturn</a>';
echo '<br>URL:'.$paypal_args[thankyou_page_url];
	echo do_shortcode($text);
}
?>
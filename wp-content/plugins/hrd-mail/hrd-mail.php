<?php
/*
 Plugin Name: HRD Mail Function
 Description: Send mail,Recive mail Functions.
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdmail
 Domain Path: /languages
*/

// メール通知処理
//	wp_mail( $to, $subject, $message, $headers = '', $attachments = array() );
//		$to				メールの送信先,複数の送信先がある場合はメールアドレスを配列で格納
//		$subject		メールの件名
//		$message		メールの本文
//		$headers		header を設定,From のメールアドレスを変えたい場合。HTML メールを送信したい場合。
//		$attachments	添付ファイルも添付できる。複数なら配列。
//		return			成功すれば true が失敗すれば false
//		http://weble.org/2011/05/10/wp_mail

//		ローカルコンピュータにメールサーバーを設定
//		http://www.nishi2002.com/2858.html

function hrd_send_mail($request, $post_id=null) {
	$blog_name = get_bloginfo('name');

	switch ($request){
		// From Client
		case "new_job": //案件登録時
			//	xxxxx-disp.php -> hrd_job_manage() Line577
			//	To			開発者全員
			//	Title		[BLOG NAME Infomation]New Job
			//	Description	JobNoxxxxxx Title description
			$job_args['post_type']	= 'job';
			$job_args['job_id']		= $post_id;
			$get_job = hrd_get_posts($job_args);
			$get_job = array_shift($get_job);

			$to = hrd_user_list('developer');
			$tomail = array();
			foreach ($to as $key => $value) {
				$user_info = get_userdata($value);
				array_push($tomail, $user_info->user_email);
			}

			$subject = "[".$blog_name." Infomation]New Job";
			$message = "JobNo:". $get_job->job_no ."\nTitle:". $get_job->post_title ."\nDescription:". $get_job->post_content;
			break;

		case "job_update": //案件修正時
			//	xxxxx-disp.php -> hrd_job_manage() Line577
			//	To			開発者全員
			//	Title		[BLOG NAME Infomation]New Job
			//	Description	JobNoxxxxxx Title description
			$job_args['post_type']	= 'job';
			$job_args['job_id']		= $post_id;
			$get_job = hrd_get_posts($job_args);
			$get_job = array_shift($get_job);

			$to = hrd_user_list('developer');
			$tomail = array();
			foreach ($to as $key => $value) {
				$user_info = get_userdata($value);
				array_push($tomail, $user_info->user_email);
			}

			$subject = "[".$blog_name." Infomation]Job Update";
			$message = "JobNo:". $get_job->job_no ."\nTitle:". $get_job->post_title ."\nDescription:". $get_job->post_content;
			break;

		case "cancel_job": //案件削除時
			//	xxxxx-disp.php -> hrd_job_manage() Line577
			//	To			開発者全員
			//	Title		[BLOG NAME Infomation]New Job
			//	Description	JobNoxxxxxx Title description
			$job_args['post_type']	= 'job';
			$job_args['job_id']		= $post_id;
			$get_job = hrd_get_posts($job_args);
			$get_job = array_shift($get_job);

			$to = hrd_user_list('developer');
			$tomail = array();
			foreach ($to as $key => $value) {
				$user_info = get_userdata($value);
				array_push($tomail, $user_info->user_email);
			}

			$subject = "[".$blog_name." Infomation]Job Cansel";
			$message = "JobNo:". $get_job->job_no ."\nTitle:". $get_job->post_title ."\nDescription:". $get_job->post_content;
			break;

		case "single_select": //案件確定
			//	XXXXX-disp.php -> hrd_job_manage() Line697
			//	To			開発者
			//	Title		[BLOG NAME Infomation] Your Selected
			//	Description	案件番号xxxxxx 案件名xxxxxx はＸＸさんが確定しました。
			$job_args['post_type']	= 'job';
			$job_args['job_id']		= $post_id;
			$get_job = hrd_get_posts($job_args);
			$get_job = array_shift($get_job);

			$entry_args['post_type']	= 'entry';
			$entry_args['job_id']		= $post_id;
			$entry_args['status']		= 'select';
			$get_entry = hrd_get_posts($entry_args);
			$get_entry = array_shift($get_entry);
			$entry_author = get_userdata($get_entry->post_author);

			$tomail = $entry_author->user_email;
			$subject = "[".$blog_name." Infomation]User Selected";
			$message = "JobNo:". $get_job->job_no ."\nTitle:". $get_job->post_title ."\n". $entry_author->user_nicename. " is Selected";
			break;

		case "client_agreement": //依頼者契約完了
			//	xxxx-disp.php -> hrd_job_manage() Line763
			//	To			開発者
			//	Title		[BLOG NAME Infomation]
			//	Description	案件番号xxxxxx 案件名xxxxxx の契約が完了しました。
			$job_args['post_type']	= 'job';
			$job_args['job_id']		= $post_id;
			$get_job = hrd_get_posts($job_args);
			$get_job = array_shift($get_job);

			$entry_args['post_type']	= 'entry';
			$entry_args['job_id']		= $post_id;
			//			$entry_args['status']		= 'select';
			$entry_args['status']		= 'agreement';
			$get_entry = hrd_get_posts($entry_args);
			$get_entry = array_shift($get_entry);
			//var_dump($get_entry);
			$entry_author = get_userdata($get_entry->post_author);

			$tomail = $entry_author->user_email;
			$subject = "[".$blog_name." Infomation]Client Agreement";
			$message = "JobNo:". $get_job->job_no ."\nTitle:". $get_job->post_title ."\nis Agreement";
			break;

		case "complete": //作業完了
			//	xxxx-disp.php -> hrd_job_manage() Line870
			//	To			開発者
			//	Title		[BLOG NAME Infomation]
			//	Description	案件番号xxxxxx 案件名xxxxxx の作業が終了しました。
			$job_args['post_type']	= 'job';
			$job_args['job_id']		= $post_id;
			$get_job = hrd_get_posts($job_args);
			$get_job = array_shift($get_job);
			//echo "get_job:";var_dump($get_job);

			$entry_args['post_type']	= 'entry';
			$entry_args['job_id']		= $post_id;
			//			$entry_args['status']		= 'working';
			$entry_args['status']		= 'complete';
			$get_entry = hrd_get_posts($entry_args);
			$get_entry = array_shift($get_entry);
			$entry_author = get_userdata($get_entry->post_author);
			//echo "<br>get_entry:";var_dump($get_entry);

			$tomail = $entry_author->user_email;
			$subject = "[".$blog_name." Infomation]WorkComplate";
			$message = "JobNo:". $get_job->job_no ."\nTitle:". $get_job->post_title ."\nis WorkConmpalte";
			break;

		case "paid": //支払完了
			//	xxxx-disp.php -> hrd_job_manage() Line904
			//	To			開発者
			//	Title		[BLOG NAME Infomation]
			//	Description	案件番号xxxxxx 案件名xxxxxx の支払が完了しました。
			$pay_args['post_type']	= 'pay';
			$pay_args['job_id']		= $post_id;
			$pay_args['status']		= 'unpaid';
			$get_pay = hrd_get_posts($pay_args);
			$get_pay = array_shift($get_pay);
			//var_dump($get_pay);

			$job_args['post_type']	= 'job';
			//			$job_args['job_id']		= $post_id;
			$job_args['job_id']		= $get_pay->job_id;
			$get_job = hrd_get_posts($job_args);
			$get_job = array_shift($get_job);

			$entry_args['post_type']	= 'entry';
			//			$entry_args['job_id']		= $post_id;
			$entry_args['job_id']		= $get_pay->job_id;
			//			$entry_args['status']		= 'select';
			$entry_args['status']		= 'complete';
			$get_entry = hrd_get_posts($entry_args);
			$get_entry = array_shift($get_entry);
			$entry_author = get_userdata($get_entry->post_author);

			$tomail = $entry_author->user_email;
			$subject = "[".$blog_name." Infomation]Paied";
			$message = "JobNo:". $get_job->job_no ."\nTitle:". $get_job->post_title ."\nis Paid";
			break;

			// From Developer
		case "entry": //案件応募
			//	xxxx-disp.php -> hrd_job_manage() Line577
			//	To			依頼者
			//	Title		[BLOG NAME Infomation]New Entry
			//	Description	案件番号xxxxxx 案件名xxxxxx にＸＸさんが応募しました。
			$entry_args['post_type']	= 'entry';
			$entry_args['post_id']		= $post_id;
			$get_entry = hrd_get_posts($entry_args);
			$get_entry = array_shift($get_entry);
			//var_dump($get_entry);

			$entry_author = get_userdata($get_entry->post_author);
			//var_dump($entyr_author);

			$job_args['post_type']	= 'job';
			$job_args['job_id']		= $get_entry->job_id;
			$get_job = hrd_get_posts($job_args);
			$get_job = array_shift($get_job);
			//var_dump($get_job);

			$job_author = get_userdata($get_job->post_author);
			$tomail = $job_author->user_email;

			$subject = "[".$blog_name." Infomation]New Entry";
			$message = "JobNo:". $get_job->job_no ."\rTitle:". $get_job->post_title ."\rEntryName:". $entry_author->user_nicename;
			break;

		case "developer_agreement": //開発者契約完了
			//	xxxx-disp.php -> hrd_job_manage() Line1020
			//	To			依頼者
			//	Title		[BLOG NAME Infomation]
			//	Description	案件番号xxxxxx 案件名xxxxxx の契約が完了しました。
			$job_args['post_type']	= 'job';
			$job_args['job_id']		= $post_id;
			$get_job = hrd_get_posts($job_args);
			$get_job = array_shift($get_job);

			$job_author = get_userdata($get_job->post_author);

			$tomail = $job_author->user_email;
			$subject = "[".$blog_name." Infomation]Developer Agreement";
			$message = "JobNo:". $get_job->job_no ."\nTitle:". $get_job->post_title ."\n". $get_job->post_content;
			break;

		case "delete_user":
			//	xxxxx-user_detail-page-create.php -> hrd_withdraw_redirect() Line1285
			//	To			LoginUser
			//	Title		[BLOG NAME Infomation]退会の通知
			//	Description	退会手続きが完了しました。ご利用をありがとうございました。
			$user_data = get_userdata(get_current_user_id());
			$tomail = $user_data->user_email;
			$subject = "[".$blog_name." Infomation]Notification of withdrawal";
			$message = "Withdrawal procedure is now complete. Thank you for your use.";
			break;

		case "delete_user_to_admin":
			//	xxxxx-user_detail-page-create.php -> hrd_withdraw_redirect() Line1285
			//	To			Admin
			//	Title		[BLOG NAME Infomation]退会の通知
			//	Description	退会手続きが完了しました。

			$tomail = get_bloginfo('admin_email');
			$subject = "[".$blog_name." Infomation]Notification of withdrawal";
			$user_nickname = get_user_meta(get_current_user_id(),'nickname');
			$message = "Withdrawal procedure is now complete.";
			$message .= "\n"."NickName:".$user_nickname;
			break;
			//		default:

			// Any One
//		case "": //メッセージ受信
			//
			//	To			受信者
			//	Title		[BLOG NAME Infomation]ReciveMessage
			//	Description	ＸＸさんからメッセージがあります。
//			$tomail = "";
//			$subject = "[".$blog_name." Infomation]Recive New Message";
//			$message = "JobNo:". $get_job->job_no ."\nTitle:". $get_job->post_title ."\n". $get_job->post_content;
//			break;

		default:
	}

	/*	echo "Mail Content";
	 echo "<br>to"; if(!empty($tomail)) { var_dump($tomail); }
	echo "<br>Title:"; if(!empty($subject)) { echo $subject; }
	echo "<br>content:"; if(!empty($message)) { echo $message; }
	echo "<br>";*/

	/*
	 if(is_array($tomail)) {
	foreach ($tomail as $tosingle) {
	$return = wp_mail($tosingle, $subject, $message);
	}
	}else{
	$return = wp_mail($tomail, $subject, $message);
	}
	*/
	$return = wp_mail($tomail, $subject, $message, hrd_mail_header());

	return $return;
}

?>
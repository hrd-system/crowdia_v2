<?php
/*
 Plugin Name: HRD 1 Core Function
 Description: hrd Common Function. Activate First.
 Version:1.0
 Author: HRD System Works
 Author URI: http://hrd-system.com
 Text Domain: hrdcore
 Domain Path: /languages
*/

// cssの読込み
function hrd_core_css(){
	// CSSの格納パス[WP-home]/wp-content/plugins/my-plugin/myStyle.css
	//	$cssPath = WP_PLUGIN_DIR . '/crowdia-common/crowdia-common.css';
	$cssPath = WP_PLUGIN_DIR . '/hrd-core/hrd-core.css';

	// CSSファイルが存在すれば、関数呼び出しでCSSを追加する
	if(file_exists($cssPath)){
		// CSSの格納URL
		$cssUrl = plugins_url('hrd-core.css', __FILE__);
		// CSS登録
		wp_register_style('hrd', $cssUrl);
		// CSS追加
		wp_enqueue_style('hrd');
	}
}
/* アクションフック */
add_action('wp_print_styles', 'hrd_core_css');

/* 共通項目 */
// 管理バーを非表示にする（Admin以外）
function hrd_admin_bar($content) {
	return false;
}
add_filter( 'show_admin_bar' , 'hrd_admin_bar');

// 固定ページのタイトルを非表示
function hrd_title_mask($title) {
	if(!(is_admin())) {
		$title = "";
	}
	return $title;
}
//add_action('the_title', 'hrd_title_mask');

// コンテンツ上部に文字を表示する
function hrd_page_header($content) {
//	echo '<p>hoge</p>';
	if(is_login()){
		$user_data = get_userdata(get_current_user_id());

		echo '<p>'.$user_data->user_nicename.' is '.hrd_get_user_type().'</p>';
	}
	return $content;
}
//add_action('the_title', 'hrd_page_header');
add_filter('the_content', 'hrd_page_header');
//add_filter('wp_head', 'hrd_page_header');

function hrd_redirect_slug($slug) {
	$page = hrd_get_page_by_slug($slug);
//	wp_safe_redirect($page->url);
	wp_redirect($page->url);
	exit;
//	var_dump($page);
}


/* 固定ページ作成 */
function hrd_post_type_register($post_type, $post_label) {
/*
 * Create Post Type
 * @param $post_type ex. post,page,custom post type
 * @param $post_label string
 */
	register_post_type($post_type, array(
		'label' => $post_label,
		'description' => $post_label,
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'capability_type' => 'post',
		'map_meta_cap' => true,
		'hierarchical' => true,
		'rewrite' => array('slug' => $post_type, 'with_front' => true),
		'query_var' => true,
		//'has_archive' => true,
		'has_archive' => false,
		'supports' => array('title','editor','excerpt','trackbacks','custom-fields','comments','revisions','thumbnail','author','page-attributes','post-formats'),
		'labels' => array (
			'name' => $post_label,
			'singular_name' => $post_type,
			'menu_name' => $post_label,
			'add_new' => $post_label.'の追加',
			'add_new_item' => '新規の'.$post_label,
			'edit' => 'Edit',
			'edit_item' => 'Edit '.$post_type,
			'new_item' => 'New '.$post_type,
			'view' => 'View '.$post_type,
			'view_item' => 'View '.$post_type,
			'search_items' => 'Search '.$post_label,
			'not_found' => 'No '.$post_label.' Found',
			'not_found_in_trash' => 'No '.$post_label.' Found in Trash',
			'parent' => 'Parent '.$post_type,
		))
	);
}

function hrd_chack_page($post_type, $page_name) {
//echo "PostType:".$post_type.",PageName:".$page_name."<br>";
	$get_post = get_page_by_title($page_name,'OBJECT',$post_type);
//var_dump($post);
	if(is_object($get_post)) {
		return true;
	}else{
		return false;
	}
}

function hrd_get_page_url($page_slug) {
//echo "PageSlug:".$page_slug;
	$get_parent_pages = get_pages('parent=0');
	foreach ($get_parent_pages as $get_parent_page) {
		if($get_parent_page->post_name == $page_slug) {
			$permalink = get_permalink($get_parent_page->ID);
//echo "ParentURL:".$permalink."<br>";
			break;
		}
		$get_children_pages = get_page_children($get_parent_page->ID, get_pages());
		foreach ($get_children_pages as $get_children_page) {
			if($get_children_page->post_name == $page_slug) {
				$permalink = get_permalink($get_children_page->ID);
//echo "ChildURL:".$permalink."<br>";
				break;
			}
		}
	}
	return $permalink;
}

function hrd_create_page($page_args) {
	// PageCheck
	// CreatePage

	$parent_page = array(
			'post_type' 		=> $page_args['post_type'],	// 投稿タイプ
			'post_title'		=> $page_args['title'],		// ページタイトル
			'post_name'			=> $page_args['slug'],			// スラッグ
			'post_content'		=> $page_args['content'],		// 投稿の本文
		//	'post_excerpt'		=> '',							// 投稿の抜粋
		//	'post_password'		=> '',							// 閲覧のパスワード
		//	'tags_input'		=> '',							// 投稿タグ '<タグ>, <タグ>, <...>'
			'post_status'		=> 'publish',					// 公開ステータス 'draft'（下書き）,'publish'（公開済み）,'pending'（レビュー待ち）,'private'（非公開）
		//	'post_status'		=> 'private',					// 公開ステータス 'draft'（下書き）,'publish'（公開済み）,'pending'（レビュー待ち）,'private'（非公開）
			'post_author'		=> $page_args['author'],		// ユーザーID
			'comment_status'	=> 'closed',					// コメントを閉じる 'closed','open'
			'ping_status'		=> 'closed',					// ピンバック／トラックバック 'closed','open'
			'post_parent'		=> $page_args['parent'],		// 親ページID
			'menu_order'		=> $page_args['menu_order'],	// 固定ページの並び順の番号
	);
	$return = wp_insert_post($parent_page);

	// ChangeChild
	if($page_args['parent'] != "0" ){

		if($return != 0) {
			$parent_page = get_page_by_path($page_args['parent']);
			//var_dump($parent_page);
			$child_page_args = array(
					'ID'		=> $return,
					'post_status'	=> 'publish',			// 公開ステータス 'draft'（下書き）,'publish'（公開済み）,'pending'（レビュー待ち）,'private'（非公開）
					'post_parent'	=> $parent_page->ID,	// 親ページID
			);
			$return = wp_update_post($child_page_args);
		}
	}

	// meta
//var_dump($page_args);
	if(array_key_exists('meta', $page_args)){
//		echo "metaあり";
		foreach ($page_args['meta'] as $page_meta) {
//var_dump($page_meta);
			if(!add_post_meta($return, $page_meta['meta_key'], $page_meta['meta_value'])) {
				update_post_meta($return, $page_meta['meta_key'], $page_meta['meta_value']);
			}
		}
	}else{
//		echo "metaなし";
	}
	return $return;
}

function hrd_delete_page($page_args) {
	if(hrd_chack_page($page_args[post_type], $page_args[title])){
		echo "PageCheckあり<br>";
	}

	$get_post = get_page_by_title($page_args[title],'OBJECT',$page_args[post_type]);
	if(is_object($get_post)) {
		if (($get_post->ID != 0)) {
			$delete = wp_delete_post($get_post->ID,$force_delete = true);	// force_delete:ゴミ箱への移動ではなく、完全に削除する
		}
	}else{
		return false;
	}
}

function hrd_page_join($parent_post_type,$child_post_type) {
	$parent_post_args = array(
			'post_type'	=> $parent_post_type
	);
	$parent_posts = new WP_Query($parent_post_args);
	if($parent_posts->have_posts()) :
	while ($parent_posts->have_posts()) {
		$parent_posts->the_post();
		echo "Parent:".get_the_title()."<br>";
		hrd_page_child($parent_post_type,$child_post_type, get_the_title());
	}
	endif;
	wp_reset_postdata();
}
function hrd_page_child($parent_post_type,$child_post_type,$parent_title){
	$child_post_type_args = array(
			'post_type'		=> $child_post_type,
			'meta_key'		=> $parent_post_type,
			'meta_value'	=> $parent_title
	);
	$child_posts = new WP_Query($child_post_type_args);
	if($child_posts->have_posts()) :
	while($child_posts->have_posts()) :
	$child_posts->the_post();
	echo "Child:".get_the_title()."<br>";
	endwhile;
	endif;
}
function hrd_get_page_by_slug($slug) {
	$page_args = array(
			'name'	=> $slug,
			'post_type'	=> 'page',
	);
	$page = new stdClass();
	$pages_query = new WP_Query($page_args);
	if($pages_query->posts){
		$page	= $pages_query->post;
		wp_reset_postdata();
		$page->url = get_page_link($page->ID);
	}else{
//		$page->post_title = 'home';
//		$page->url = home_url();
		$page = NULL;
	}
	return $page;
}

// Metaフィールドの取得
function  hrd_get_post_meta($post_id,$meta_key = null){

	if (empty($meta_key)) {
		$get_post_metas = get_post_custom($post_id);

		foreach ($get_post_metas as $metas_key => $metas_value) {
			if(!(preg_match("/^_/", $metas_key)) && $metas_key!== '' ) {
				$meta_args[$metas_key] = $metas_value[0];
			}
		}
	} else {
		$meta_args = get_post_meta($post_id,$meta_key);
	}
	if (empty($meta_args)) {
		$meta_args = false;
	}
	return $meta_args;
}
// Inputタグ作成
function hrd_title_maker($text,$tag=null,$domain=NULL) {
	$str_text = str_replace("_"," ",$text);
	//echo $str_text;
	if(empty($domain)) {
		$domain = "hrdsystem";
	}

	if(empty($tag)){
		$str_value= '<div title="'.__($text,$domain).'" class="text-inline" >'.$str_text.'</div>';
	}else{
		if($tag == "input"){
			if(!empty($domain)){
				$str_value= '<'.$tag.' type="submit" title="'.__($text,$domain).'" class="text-inline" value='.$str_text.'>';
			}else{
				$str_value= '<'.$tag.' type="submit" title="'.__($text).'" class="text-inline" value='.$str_text.'>';
			}
		}else{
			if(!empty($domain)){
				$str_value= '<'.$tag.' title="'.__($text,$domain).'" class="text-inline" >'.$str_text.'</'.$tag.'>';
			}else{
				$str_value= '<'.$tag.' title="'.__($text).'" class="text-inline" >'.$str_text.'</'.$tag.'>';
			}
		}
	}
	if (empty($text)) { $str_value = ""; }
	return $str_value;
}

function hrd_input_meta_form ($post_id,$key,$value,$text=NULL) {
//echo "key:".$key.",value[0]".$value[0].",value[1]".$value[1]."<br>";
//echo "";
	//	$requir_mark = crowdia_const('requird_mark');

	$requir_args = '*';
	//	$requir_mark = '<div title="'.$requir_args[1].'" class="text-inline">&nbsp;'.$requir_args[0].'</div>';
	$requir_mark = hrd_title_maker($requir_args[0]);

//	$meta_fields = hrd_user_meta_client();
	$meta_fields = hrd_user_detail_args($key);

//echo $text[$key];
//	if(empty($text)){
	if(empty($text[$key])){
		$get_meta = hrd_get_post_meta($post_id,$key);
	}else{
//		$get_meta[0] = $text;
		$get_meta[0] = $text[$key];
	}
//var_dump($get_meta);
	$str_key = str_replace("_"," ",$key);
	$str_value = '<div title="'.$value[0].'"class="text-inline" >'.$str_key.'</div>';
	//var_dump($get_meta);
	//	if ($key != 'status') {
//	if (($key != 'status') AND ($key != 'evaluation')) {
		if ($value[1] == 'text') {
			echo "<tr>";
			echo "<th>";
//			echo $value[0];
			echo $str_value;
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo "</th>";
			echo '<td>';
//var_dump($get_meta);
			echo '<input type="text" name="'.$key.'"';
			if (!(empty($get_meta[0]))) {
				echo 'value="'.$get_meta[0].'"';
			}
			echo ' class="crowdia_input_text">';
			if(!empty($value[2])){
				echo $value[2];
			}
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}
			echo '</td>';
			echo "</tr>";

		} elseif ($value[1] == 'readonly') {
			echo "<tr>";
			echo '<th class="entry-vertical-list">';
			//			echo $value[0];
			echo $str_value;
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo "</th>";
			echo '<td>';
			echo '<input type="text" name="'.$key.'"';
			if (!(empty($get_meta[0]))) {
				echo 'value="'.$get_meta[0].'"';
			}
			echo ' class="crowdia_input_readonly" readonly>';
			if(!empty($value[2])){
				echo $value[2];
			}
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}
			echo '</td>';
			echo "</tr>";

		}elseif ($value[1] == 'textarea') {
			echo "<tr>";
			echo "<th>";
			//			echo $value[0];
			echo $str_value;
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo "</th>";
			echo '<td>';
			echo '<textarea name="'.$key.'" class="crowdia_input_textarea">';
			if (!(empty($get_meta[0]))) {
				echo $get_meta[0];
			}
			echo '</textarea>';
			if(!empty($value[2])){
				echo $value[2];
			}
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}
			echo '</td>';
			echo "</tr>";

		}elseif ($value[1] == 'checkbox') {
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo '<tr>';
			echo '<td></td>';
			echo '<td>';
			echo '<input type="checkbox" name="'.$key.'" value="'.$key. '"';
			if (!(empty($get_meta[0]))) {
				if ($get_meta[0] == $key) {
					echo " checked";
				}
			}
			//			echo '>'.$value[0]; // 時給相談可
			echo '>'.hrd_title_maker($key); __('price_up','crowdia');
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}
			echo '</td>';
			echo '</tr>';


		}elseif ($value[1] == 'radio') {
			$value_0 = $value[0];
			if($value[2] == "table") {
				echo '<tr><th>';
				echo '<div title="'.$str_key.'"class="text-inline" >'.$str_key.'</div>';
				//				if($value[3]){ echo $requir_mark ; } // 必須表示
				echo '</th><td>';
			}
			if($value[3]){ echo $requir_mark ; } // 必須表示
			foreach ($value_0 as $radio_key => $radio_value) {
				echo '<input type="radio" name="'.$key.'" value="'.$radio_value. '"';
				if ($get_meta[0] == $radio_value) {
					echo " checked";
				}
				echo '>'.$radio_key;
				//				echo '<br>';

			}
			if($value[2] == "table") {
				echo '</td></tr>';
			}

		}elseif ($value[1] == 'file') {
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo '<input type="file" name="'.$key.'">';
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}

		}elseif ($value[1] == 'number') {
			echo "<tr>";
			echo "<th>";
			//			echo $value[0];
			echo $str_value;
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo "</th>";
			echo '<td>';
			echo '<input type="number" name="'.$key.'" ';
			if (!(empty($get_meta[0]))) {
				echo 'value="'.$get_meta[0].'"';
			}
			echo '>';
			if(!empty($value[2])){
				echo $value[2];
			}
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}
			echo '</td>';
			echo "</tr>";

		}elseif ($value[1] == 'price') {	// 通貨表示
			echo "<tr>";
			echo "<th>";
			//			echo $value[0];
			echo $str_value;
			if(function_exists('crowdia_const')){
				echo crowdia_const('currency');
			}else{
				echo '[USD]';
			}
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo "</th>";
			echo '<td>';
			echo '<input type="number" name="'.$key.'" ';
			if (!(empty($get_meta[0]))) {
				echo 'value="'.$get_meta[0].'"';
			}
			echo '>';
			if(!empty($value[2])){
				echo $value[2];
			}
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}
			echo '</td>';
			echo "</tr>";

		}elseif ($value[1] == 'year') {	// 年数表示
			echo "<tr>";
			echo "<th>";
			//			echo $value[0];
			echo $str_value;
//			if(function_exists('crowdia_const')){
				echo "[Yesrs]";
//			}
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo "</th>";
			echo '<td>';
			echo '<input type="number" name="'.$key.'" ';
			if (!(empty($get_meta[0]))) {
				echo 'value="'.$get_meta[0].'"';
			}
			echo '>';
			if(!empty($value[2])){
				echo $value[2];
			}
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}
			echo '</td>';
			echo "</tr>";

		}elseif ($value[1] == 'date') {
			echo "<tr>";
			echo "<th>";
			//			echo $value[0];
			echo $str_value;
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo "</th>";
			echo '<td>';
			echo '<input type="date" name="'.$key.'" ';
			if (!(empty($get_meta[0]))) {
				echo 'value="'.$get_meta[0].'"';
			}else{
				echo 'value="'.date('Y-m-d').'"';
			}
			echo '>';
			if(!empty($value[2])){
				echo $value[2];
			}
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}
			echo '</td>';
			echo "</tr>";

		}elseif ($value[1] == 'select') {
			echo "<tr>";
			echo "<th>";
			//			echo $value[0];
			echo $str_value;
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo "</th>";
			echo '<td>';
			echo '<select name="'.$key.'">';
			foreach ($value[2] as $select_key => $select_value) {
				//				echo '<option value="'.$select_key.'">';
				echo '<option value="'.$select_key.'"';
				if (!(empty($get_meta[0]))) {
					if ($get_meta[0] == $select_key) {
						echo ' selected ';
					}
				}
				//				echo '>'.$select_value.'</option>';
				//				echo '>'.$select_key.'</option>';
				echo '>'.hrd_title_maker($select_key).'</option>';
				//				echo $select_value.'</option>';
			}
			echo '</select>';
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}
			echo '</td>';
			echo "<tr>";

		}elseif ($value[1] == 'space'){
			echo '<tr><td cals=2></td></tr>';

		}else{
			echo '<th class="entry-vertical-list">';
			echo $value[0];
			if($value[3]){ echo $requir_mark ; } // 必須表示
			echo "</th>";
			echo '<td>';
			echo '<input type="text" name="'.$key.'"';
			if (!(empty($get_meta[0]))) {
				echo 'value="'.$get_meta[0].'"';
			}
			echo '>';
			if(function_exists('crowdia_input_meta_help')){
				crowdia_input_meta_help($key);
			}
			echo '</td>';
		}
//	} // omit status & evaluation
}
?>
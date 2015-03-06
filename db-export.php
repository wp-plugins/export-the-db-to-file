<?php
/**
 * @package  Export the DB to file
 * @version 1.0
 */
/*
 * Plugin Name: Export the DB to file
 * Plugin URI: https://wordpress.org/plugins/
 * Description: Plug-in to export the DB tables in WordPress Admin screen as csv file or tsv file
 * WordPress管理画面でDBのテーブルをcsvファイルもしくはtsvファイルとしてエクスポートするプラグイン
 * Author: Harigae
 * Version: 1.0
 * Author URI:
*/
function add_pages() {
	add_menu_page ( 'DB Export', 'DB 出力', 'level_8', __FILE__, 'db_search', 'dashicons-upload', 26 );
}

add_action ( 'admin_menu', 'add_pages' );


// プラグインの表示
function db_search() {
	if (isset ( $_POST ["example"] ) && ! isset ( $_POST ["column_check"] ) && !isset($_POST["count"])) {

		add_action ( 'wp_loaded', 'select_db_table', 1 );
		// add_action('admin_init', 'select_db_table');
		add_action ( 'admin_init', 'test' );
		function select_db_table() {
			global $wpdb;

			$table_name = $wpdb->$_POST ["example"];

			$get_result = $wpdb->get_results ( "SELECT * FROM $table_name " );
			$table_col = $wpdb->get_col_info ();

			$index_col_name = array ();

			foreach ( $table_col as $col ) {
				$index_col_name [] = $col;
			}

			$i = 0;
			$column_check = array ();
			echo '<h4>選択テーブル : ' . $_POST ["example"] . '</h4>';
			echo '<h4>カラム選択</h4>';
			echo '<form method = "POST" action = "">';
			echo '<div>';
			foreach ( $table_col as $col ) {
				echo '<input type="checkbox"  name="column_check[]" value="' . $col . '">' . $col . '<br>';
				$i ++;
			}
			echo '</div>';
			echo '<h4>ファイル形式</h4>';
			echo '<input type = "radio" name = "separate" value="csv" checked >csvファイル';
			echo '<input type = "radio" name = "separate" value="tsv" >tsvファイル';
			echo '<input type="hidden" name = "count" value=' . $i . '>';
			echo '<input type="hidden" name = "example" value=' . $_POST ["example"] . '><br>';
			echo '<br><input type = "submit" value = "ダウンロード" /><hr size = "2px" style="color: black;">';
			echo '</form>';
		}

		select_db_table ();
	}

	if (isset ( $_POST ["column_check"] )) {
		function show_table() {
			global $wpdb;


			if(isset($_POST["separate"])){
				$sepa_name = $_POST["separate"];
				if($sepa_name === 'tsv'){
					$sepa = '	';
				}else{
					$sepa = ',';
				}
			}else{

				$sepa_name = 'csv';
				$sepa = ',';
			}

			$table_name = $wpdb->$_POST ["example"];

			$get_result = $wpdb->get_results ( "SELECT * FROM $table_name " );
			// 全データ取得
			$image_contents = $get_result;

			//ファイル名指定
			$file_name = $_POST ["example"].'.'.$sepa_name;
			$file_path = '../';
			// $file_name="sample.csv";

			//ファイル作成
			$fp = fopen ( $file_path . $file_name, 'w' );

			$index_name = $_POST["column_check"];

			// Excelで文字化けしないように文字コード変換
			mb_convert_variables ( 'SJIS', 'UTF-8', $index_name );

			// 項目名を書き込み
			fputcsv ( $fp, $index_name ,$sepa);
			$line = array();


			// htmlタグを除去する関数
			function show_content($content){

				$content = preg_replace('/(width|height|frameborder)="\d*"\s/', '', $content);
				$content = preg_replace('/src=".*"/', '', $content);
				$content = preg_replace("|(<img[^>]+>)|si",'',$content);
				$content = preg_replace("|(<a[^>]+>)|si",'',$content);
				$content = preg_replace("|(<p[^>]+>)|si",'',$content);
				$content = preg_replace("|(<h1[^>]+>)|si",'',$content);
				$content = preg_replace("|(<h2[^>]+>)|si",'',$content);
				$content = preg_replace("|(<h3[^>]+>)|si",'',$content);
				$content = preg_replace("|(<h4[^>]+>)|si",'',$content);
				$content = preg_replace("|(<h5[^>]+>)|si",'',$content);
				$content = preg_replace("|(<h6[^>]+>)|si",'',$content);
				$content = preg_replace("|(<ol[^>]+>)|si",'',$content);
				$content = preg_replace("|(<ul[^>]+>)|si",'',$content);
				$content = preg_replace("|(<dl[^>]+>)|si",'',$content);
				$content = preg_replace("|(<dt[^>]+>)|si",'',$content);
				$content = preg_replace("|(<dd[^>]+>)|si",'',$content);
				$content = preg_replace("|(<li[^>]+>)|si",'',$content);
				$content = preg_replace("|(<table[^>]+>)|si",'',$content);
				$content = preg_replace("|(<span[^>]+>)|si",'',$content);
				$content = preg_replace("|(<div[^>]+>)|si",'',$content);
				$content = preg_replace("|(<blockquote[^>]+>)|si",'',$content);
				$content = preg_replace("|(\[caption[^\]]+\])|si",'',$content);
				$content = preg_replace("|(\[gallery[^\]]+\])|si",'',$content);

				$tags = array("<p>","</p>","</a>","<div>","</div>","<h1>","</h1>","<h2>","</h2>","<h3>","</h3>","<h4>","</h4>","<h5>","</h5>","<h6>","</h6>","<blockquote>",
						"</blockquote>","<hr />","<li>","</li>","<ul>","</ul>","<ol>","</ol>","<dl>","</dl>","<dt>","</dt>","<dd>","</dd>","<strong>","</strong>","<em>","</em>",
						"<table>","</talbe>","<tr>","</tr>","<td>","</td>","<th>","</th>","[/caption]","[/gallery]","<tbody>","</tbody>","<table>","</table>",'<br>','<br/>','<br />');
				$content = str_replace($tags, '', $content);
				$tags2 = array("\n","\r","\r\n","

				");
				$content = str_replace($tags2, ' ', $content);
				return  $content;
			}
			foreach ( $get_result as $content ) {

				foreach ( $index_name as $column ){
						$line []  = show_content($content->$column);

				}

				// Excelで開けるように文字コード変換
				mb_convert_variables ( 'SJIS', 'UTF-8', $line );

				// このループ文書き込み
				fputcsv ( $fp, $line ,$sepa);

				$line = array();
			}

			flock ( $fp, LOCK_SH );
			chmod ( $file_path . $file_name, 0777 );
			chmod ( $file_path, 0777 );
			fclose ( $fp );
			// download();
			?>
<meta http-equiv="refresh"
	content="0; url=<?php  echo home_url().'/'.$file_name;?>">
<?php
		}
		show_table ();

	}elseif (isset($_POST["count"])){


	echo '<h4  style="color: red;">カラムを選択してください</h4>';

 		function select_db_table2() {
			global $wpdb;

			$table_name = $wpdb->$_POST ["example"];

			$get_result = $wpdb->get_results ( "SELECT * FROM $table_name " );
			$table_col = $wpdb->get_col_info ();

			$index_col_name = array ();

			foreach ( $table_col as $col ) {
				$index_col_name [] = $col;
			}

			$i = 0;
			$column_check = array ();
			echo '<h4>選択テーブル : ' . $_POST ["example"] . '</h4>';
			echo '<h4>カラム選択</h4>';
			echo '<form method = "POST" action = "">';
			echo '<div>';
			foreach ( $table_col as $col ) {
				echo '<input type="checkbox"  name="column_check[]" value="' . $col . '">' . $col . '<br>';
				$i ++;
			}
			echo '</div>';
			echo '<h4>ファイル形式</h4>';
			echo '<input type = "radio" name = "separate" value="csv" checked >csvファイル';
			echo '<input type = "radio" name = "separate" value="tsv" >tsvファイル';
			echo '<input type="hidden" name = "count" value=' . $i . '>';
			echo '<input type="hidden" name = "example" value=' . $_POST ["example"] . '><br>';
			echo '<br><input type = "submit" value = "ダウンロード" /><hr size = "2px" style="color: black;">';
			echo '</form>';
		}

		select_db_table2 ();
	}

	global $wpdb;
	echo '<h4>テーブル選択</h4>';
	echo '<form method = "POST" action = "">';
	echo '<div><select name="example">';
	foreach ( $wpdb->tables as $key => $value ) {
		echo '<option  value="' . $value . '">' . $value . '</option>';
	}
	echo '</select></div>';
	echo '<br><input type = "submit" value = "選択" />';
	echo '</form>';

	add_action ( 'admin_print_header_scripts', 'select_table', 21 );

	// function test(){
	// echo 'test';
	// }
}


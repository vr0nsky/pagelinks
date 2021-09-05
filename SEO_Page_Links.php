<?php
/**
 * @package SEO Page Links
 * @version 1.0
 */
/*
Plugin Name: SEO Page Links
Plugin URI: https://github.com/vr0nsky/pagelinks
Description: Useful to analyse links in your page
Author: Massimo Ivaldi
Version: 1.0
Author URI: https://massimoivaldi.com
*/
add_action('admin_menu', 'test_plugin_setup_menu');
 
function test_plugin_setup_menu(){
    add_menu_page( 'SEO Page Links', 'SEO Page Links', 'manage_options', 'SEO_Page_Links', 'SEO_Page_Links_init' );
}

function debug($string){
	echo "<pre>";
	print_r($string);
	echo "</pre>";
}
 
function SEO_Page_Links_init(){
    echo "<h3>Articles and page links</h3>";
    echo "<hr>";
    global $wpdb;
	$results = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts WHERE post_type IN ('post','page')", OBJECT );
	//debug($results);
	$pages = array();
	foreach($results as $res){
		$ID = $res->ID;
		$post_title = $res->post_title;
		$post_content = $res->post_content;
		$post_name = $res->post_name;
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		  if(preg_match_all("/$regexp/siU", $post_content, $matches)) {
		    //debug($matches);
		    $links = $matches[0];
		    $linked = $matches[2];
		    $anchors = $matches[3];
		    $count_link = count($links);
		  }
		$pages[] = array(
			'post_id' => $ID,
			'post_title' => $post_title,
			'post_name' => $post_name,
			'count_link' => $count_link,
			'links' => $links,
			'linked' => $linked,
			'anchors' => $anchors
		);
	}

	

	foreach($pages as $p){
		echo $p['post_id'].' - <strong>'.$p['post_title'].'</strong> - <i style="color:green;">Links: <strong>'.$p['count_link'].'</strong></i><br>';
		echo '<table class="table table-bordered">';
		echo '<thead><tr>';
      	echo '<th scope="col">Anchor</th>';
      	echo '<th scope="col">Link</th>';
    	echo '</tr></thead><tbody>';
    	$n=0;
    	foreach($p['linked'] as $l){
    		echo '<tr><td style="width: 40%">'.$p['anchors'][$n].'</td><td style="width: 50%">'.$p['linked'][$n].'</td></tr>';
    		$n++;
    	}
		echo '<tbody></table><br><br><br>';
	}

}

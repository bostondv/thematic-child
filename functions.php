<?php

/*  ------------------------------------------------------------
	Relative timestamps
	--------------------------------------------------------- */
if(!function_exists('how_long_ago')){
	function how_long_ago($timestamp){
		$difference = time() - $timestamp;

		if($difference >= 60*60*24*365){        // if more than a year ago
			$int = intval($difference / (60*60*24*365));
			$s = ($int > 1) ? 's' : '';
			$r = $int . ' year' . $s . ' ago';
		} elseif($difference >= 60*60*24*7*5){  // if more than five weeks ago
			$int = intval($difference / (60*60*24*30));
			$s = ($int > 1) ? 's' : '';
			$r = $int . ' month' . $s . ' ago';
		} elseif($difference >= 60*60*24*7){        // if more than a week ago
			$int = intval($difference / (60*60*24*7));
			$s = ($int > 1) ? 's' : '';
			$r = $int . ' week' . $s . ' ago';
		} elseif($difference >= 60*60*24){      // if more than a day ago
			$int = intval($difference / (60*60*24));
			$s = ($int > 1) ? 's' : '';
			$r = $int . ' day' . $s . ' ago';
		} elseif($difference >= 60*60){         // if more than an hour ago
			$int = intval($difference / (60*60));
			$s = ($int > 1) ? 's' : '';
			$r = $int . ' hour' . $s . ' ago';
		} elseif($difference >= 60){            // if more than a minute ago
			$int = intval($difference / (60));
			$s = ($int > 1) ? 's' : '';
			$r = $int . ' minute' . $s . ' ago';
		} else {                                // if less than a minute ago
			$r = 'moments ago';
		}

		return $r;
	}
}

/*  ------------------------------------------------------------
	Home page background image
	--------------------------------------------------------- */

function homepage_background() {
	//global $id, $post;
	$post_id = 47; // Home page
	if (has_post_thumbnail( $post_id ) && is_page(47)) :
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'background' );
		echo '<script>jQuery.backstretch("' . $image[0] . '");</script>';
	endif;
}
add_action('thematic_abovecontainer', 'homepage_background');

/*  ------------------------------------------------------------
	Setup image sizes
	--------------------------------------------------------- */
function setup_image_sizes() {
	add_image_size( 'background', 1280, 1024, true );
}
add_action( 'init', 'setup_image_sizes' );


/*  ------------------------------------------------------------
	Remove comments for pages
	--------------------------------------------------------- */
function remove_comments(){
	if(is_page()){
		remove_action('thematic_comments_template','thematic_include_comments',5);
	}
}
add_action('template_redirect','remove_comments');


/*  ------------------------------------------------------------
	Comment meta with human time
	--------------------------------------------------------- */
function childtheme_override_commentmeta() {
	echo '<div class="comment-meta">' . 
		sprintf( __('%1$s <a href="%2$s" title="Permalink to this comment" class="comment-permalink">Permalink</a>', 'thematic' ),
			how_long_ago(get_comment_time('U')),
		'#comment-' . get_comment_ID() ) .
		'</div>' . "\n";
}

/*  ------------------------------------------------------------
	Post header override
	--------------------------------------------------------- */
function childtheme_override_postheader_postmeta() {
	$postmeta = '<div class="entry-meta">';
	$postmeta .= thematic_postmeta_entrydate();
	//$postmeta .= '&nbsp;';
	//$postmeta .= thematic_postmeta_authorlink();				   
	$postmeta .= "</div><!-- .entry-meta -->\n";
	
	return $postmeta;
}

function childtheme_override_postmeta_entrydate() {
	$entrydate = '<span class="entry-date"><abbr class="published" title="';
	$entrydate .= get_the_time(thematic_time_title()) . '">';
	$entrydate .=  how_long_ago(get_the_time('U'));
	$entrydate .= '</abbr></span>';

	return $entrydate;
}

function childtheme_override_postmeta_editlink() {
	return FALSE;
}

function childtheme_override_postmeta_authorlink() {
	global $authordata;
	
	$authorlink = '<span class="meta-prep meta-prep-author">' . __('by ', 'thematic') . '</span>';
	$authorlink .= '<span class="author vcard">'. '<a class="url fn n" href="';
	$authorlink .= get_author_posts_url($authordata->ID, $authordata->user_nicename);
	$authorlink .= '" title="' . __('View all posts by ', 'thematic') . get_the_author_meta( 'display_name' ) . '">';
	$authorlink .= get_the_author_meta( 'display_name' );
	$authorlink .= '</a></span>';
	
	return $authorlink;
}

/*  ------------------------------------------------------------
	Post footer overrides
	--------------------------------------------------------- */
function childtheme_override_postfooter() {
	global $id, $post;
		
	if ($post->post_type == 'page') { /* For logged-out "page" search results */
		$postfooter = '';
	} else {
		$postfooter = '<div class="entry-utility">' . thematic_postfooter_postcategory() . thematic_postfooter_posttags() . thematic_postfooter_postcomments();
		$postfooter .= "</div><!-- .entry-utility -->\n";    
	}

	//echo apply_filters( 'thematic_postfooter', $postfooter );
}

function childtheme_override_postfooter_postcategory() {
	$postcategory = '<span class="cat-links">';
	$postcategory .= __('Categories: ', 'thematic') . get_the_category_list(', ');
	$postcategory .= '</span>';
	return $postcategory; 
}

function childtheme_override_postfooter_posttags() {
	$tagtext = __('Tags: ', 'thematic');
	$posttags = get_the_tag_list("<span class=\"tag-links\"> $tagtext ",', ','</span>');
	return $posttags; 
}

function childtheme_override_postfooter_posteditlink() {
	return FALSE;
}

function childtheme_override_postfooter_postcomments() {
	if (comments_open()) {
			$postcommentnumber = get_comments_number();
			if ($postcommentnumber > '1') {
				$postcomments = ' <span class="comments-link"><a href="' . apply_filters('the_permalink', get_permalink()) . '#comments" title="' . __('Comment on ', 'thematic') . the_title_attribute('echo=0') . '">';
				$postcomments .= get_comments_number() . __(' Comments', 'thematic') . '</a></span>';
			} elseif ($postcommentnumber == '1') {
				$postcomments = ' <span class="comments-link"><a href="' . apply_filters('the_permalink', get_permalink()) . '#comments" title="' . __('Comment on ', 'thematic') . the_title_attribute('echo=0') . '">';
				$postcomments .= get_comments_number() . __(' Comment', 'thematic') . '</a></span>';
			} elseif ($postcommentnumber == '0') {
				$postcomments = ' <span class="comments-link"><a href="' . apply_filters('the_permalink', get_permalink()) . '#comments" title="' . __('Comment on ', 'thematic') . the_title_attribute('echo=0') . '">';
				$postcomments .= __('Leave a comment', 'thematic') . '</a></span>';
			}
		} else {
			$postcomments = '';
		}             
		return $postcomments;
}

/*  ------------------------------------------------------------
	Footer text
	--------------------------------------------------------- */
function my_footertext() {
	echo '<a href="http://pomelodesign.com">Website by Pomelo Design</a>';
}
add_filter('thematic_footertext', 'my_footertext');

/*  ------------------------------------------------------------
	Kill the sidebar
	--------------------------------------------------------- */
function kill_sidebar(){
	return FALSE;
}
add_filter('thematic_sidebar','kill_sidebar');
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

/*  ------------------------------------------------------------
	HTML5 Doctype
	--------------------------------------------------------- */
function childtheme_create_doctype($content) {
	$content = '<!DOCTYPE html>';
	$content .= "\n";
	$content .= '<html';
	return $content;
}
add_filter('thematic_create_doctype', 'childtheme_create_doctype');

/*  ------------------------------------------------------------
	Setup css
	--------------------------------------------------------- */
function kill_default_stylesheet(){
	return FALSE;
}
add_filter('thematic_create_stylesheet','kill_default_stylesheet');

function my_load_styles() {
	if ( !is_admin() ) { 
		$theme  = get_theme( get_current_theme() );
		wp_register_style( 'normalize', get_bloginfo('stylesheet_directory') . '/library/styles/normalize.css', false, '1.0');
		wp_enqueue_style( 'normalize' );
		wp_register_style( 'default', get_bloginfo('stylesheet_directory') . '/library/styles/default.css', false, '1.0');
		wp_enqueue_style( 'default' );
		wp_register_style( 'layout', get_bloginfo('stylesheet_directory') . '/library/layouts/1c-fluid.css', false, '1.0');
		wp_enqueue_style( 'layout' );
		wp_register_style( 'theme', get_bloginfo( 'stylesheet_url' ), false, $theme['Version'] );
		wp_enqueue_style( 'theme' );
		wp_register_style( 'print', get_bloginfo('stylesheet_directory') . '/library/styles/print.css', false, '1.0', 'print');
		wp_enqueue_style( 'print' );
		wp_register_style( 'webfont', 'http://fonts.googleapis.com/css?family=Lato:300,700', false, '1.0');
		wp_enqueue_style( 'webfont' );
	}
}
add_action('init', 'my_load_styles');

/*  ------------------------------------------------------------
	Setup scripts
	--------------------------------------------------------- */
function my_load_scripts() {
	$theme  = get_theme( get_current_theme() );
	if ( !is_admin() ) {
		wp_register_script('backstretch', get_bloginfo('stylesheet_directory') . '/library/scripts/jquery.backstretch.min.js', array('jquery'), '1.2.0' );
		wp_enqueue_script('backstretch');
		wp_register_script('custom', get_bloginfo('stylesheet_directory') . '/library/scripts/jquery.scripts.js', array('jquery'), $theme['Version'] );
		wp_enqueue_script('custom');
	}
}
add_action('init', 'my_load_scripts');

/*  ------------------------------------------------------------
	Mobile viewport optimized
	--------------------------------------------------------- */
function mobile_viewport_optimized() {
	echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
}
add_action('wp_head','mobile_viewport_optimized');

/*  ------------------------------------------------------------
	Setup scripts
	--------------------------------------------------------- */
function childtheme_override_head_scripts() {
	return FALSE;
}
add_action('wp_head','childtheme_override_head_scripts');

/*  ------------------------------------------------------------
	Adds a home link to your menu
	http://codex.wordpress.org/Template_Tags/wp_page_menu
	--------------------------------------------------------- */
function childtheme_menu_args($args) {
	$args = array(
		'show_home' => 'Home',
		'sort_column' => 'menu_order',
		'menu_class' => 'menu',
		'echo' => true
	);
	return $args;
}
add_filter('wp_page_menu_args','childtheme_menu_args');

/*  ------------------------------------------------------------
	Thematic classes
	--------------------------------------------------------- */
//define('THEMATIC_COMPATIBLE_BODY_CLASS', true);
define('THEMATIC_COMPATIBLE_POST_CLASS', true);


/*  ------------------------------------------------------------
	High resolution icons
	--------------------------------------------------------- */
function my_hires_icons() {
	echo '<link rel="icon" href="' . get_bloginfo('stylesheet_directory') . '/images/icon-16.png" sizes="16x16">'."\n";
	echo '<link rel="icon" href="' . get_bloginfo('stylesheet_directory') . '/images/icon-32.png" sizes="32x32">'."\n";
	echo '<link rel="icon" href="' . get_bloginfo('stylesheet_directory') . '/images/icon-64.png" sizes="64x64">'."\n";
	echo '<link rel="icon" href="' . get_bloginfo('stylesheet_directory') . '/images/icon-128.png" sizes="128x128">'."\n";
}
add_action('wp_head', 'my_hires_icons');

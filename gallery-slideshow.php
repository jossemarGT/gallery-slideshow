<?php
/*
Plugin Name: Gallery <-> Slideshow
Description: Turn any WordPress gallery into a slideshow using the "gss" shortcode.
Version: 1.3.1
Author: Jethin
Author URI: http://s89693915.onlinehome.us/wp/?page_id=4
License: GPL2
*/

class gallery_ss{
    static function init() {
        add_shortcode( 'gss', array(__CLASS__, 'gss_shortcode') );
        add_action( 'wp_enqueue_scripts', array(__CLASS__, 'gss_enqueue_scripts') );
    }

    static function gss_shortcode($atts) { 
		extract( shortcode_atts( array( 'ids' => '', 'name' => 'gslideshow', 'style' => '', 'options' => '', 'carousel' => '', 'position' => '' ), $atts ) );
        if ( !function_exists('gss_html_output') ) {
			require 'gss_html.php';
		}
		$output = gss_html_output($ids,$name,$style,$options,$carousel,$position);
        return $output;
    }

    static function gss_enqueue_scripts() {
        wp_register_script( 'cycle2', plugins_url( 'jquery.cycle2.min.js' , __FILE__ ), array('jquery'), '2.1.3' );
		wp_register_script( 'cycle2_center', plugins_url( 'jquery.cycle2.center.min.js' , __FILE__ ), array('cycle2'), 'v20140128' );
		wp_register_script( 'cycle2_carousel', plugins_url( 'jquery.cycle2.carousel.min.js' , __FILE__ ), array('cycle2'), 'v20140114' );
		wp_register_script( 'gss_js', plugins_url( 'gss.js', __FILE__ ) );
		wp_register_style( 'gss_css', plugins_url( 'gss.css', __FILE__ ) );
		wp_enqueue_script( 'cycle2' );
		wp_enqueue_script( 'cycle2_center' );
		wp_enqueue_script( 'cycle2_carousel' );
		wp_enqueue_script( 'gss_js' );
		wp_enqueue_style( 'gss_css' );
		$custom_js = plugin_dir_path( __FILE__ ) . 'gss-custom.js';
		if ( file_exists($custom_js) ) {
			wp_register_script( 'gss-custom-js', plugins_url( 'gss-custom.js' , __FILE__ ) );
			wp_enqueue_script( 'gss-custom-js' );
		}
    }
}

gallery_ss::init();

function gss_embed_metadata( $post_id ){
	if ( wp_is_post_revision( $post_id ) ){ return; }
	$post_object = get_post( $post_id );
	$pattern = get_shortcode_regex();
    if ( preg_match_all( '/'. $pattern .'/s', $post_object->post_content, $matches )
		&& array_key_exists( 2, $matches )
		&& in_array( 'gss', $matches[2] ) ){
			foreach( $matches[3] as $atts_string ){
				$atts_string = trim( $atts_string );
				$atts = shortcode_parse_atts( $atts_string );
				$name = 'gss_' . $post_id;
				$name .= empty($atts['name']) ? '' : '_' . $atts['name'];
				// extract( shortcode_atts( array( 'ids' => '', 'name' => 'gslideshow', 'style' => '', 'options' => '', $carousel ), $atts ) );
				update_post_meta($post_id, $name, $atts_string);
			}
    }
	// if( has_shortcode( $post_object->post_content, 'gss' ) ) { }
}
add_action( 'save_post', 'gss_embed_metadata' );

?>
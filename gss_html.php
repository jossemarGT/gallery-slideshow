<?php

function gss_html_output($ids,$name,$style,$options,$carousel,$position) {
	$ids = explode( ',', $ids );
	$slide_count = count($ids);
	$options = html_entity_decode($options);
	parse_str( $options, $opts );
	if( $style != '' ){
		$style = ' style="' . $style . '"';
	}
	$longest_cap = array( 'length' => 0, 'text' => '' );
	$slides = '';
	$carousel_slides = '';
	$pager = "\n\t\t\t<div id=\"" . $name . '_pager" class="gss-pager"></div>';
	$slide_number = 1;
	
	foreach( $ids as $image_id ){
		$attachment = get_post( $image_id, 'ARRAY_A' );
		$src = wp_get_attachment_image_src( $image_id, 'full' );
		$excerpt = htmlspecialchars($attachment['post_excerpt']);
		$slides .= "\n\t\t\t<img src=\"$src[0]\" alt=\"$excerpt\" data-cycle-hash=\"img-".$slide_number."\" />\n";
		if( !empty($carousel) ){
			$carousel_slides .= "\t\t\t<div><img src=\"$src[0]\" title=\"$excerpt\" /></div>\n";
		}
		if( strlen( $attachment['post_excerpt'] ) > $longest_cap['length'] ){
			$longest_cap['length'] = strlen( $attachment['post_excerpt'] );
			$longest_cap['text'] = $attachment['post_excerpt'];
		}
		$slide_number++;
	}
	// do options
	$default_opts = array(
		'timeout' => '0',
		'prev' => '#' . $name . '_prev',
		'next' => '#' . $name . '_next',
		'pager' => '#' . $name . '_pager',
		'pager-template' => '<a href=#>&nbsp;</a>',
		'speed' => '750',
		'center-horz' => 'true'
	);
	$has_captions = !empty( $longest_cap['text'] ) ? true : false;
	if( $has_captions ){
		$default_opts['caption'] = '#' . $name . '_captions';
		$default_opts['caption-template'] = '{{alt}}';
		$no_captions_class = '';
	}
	else{
		$no_captions_class = ' no-captions';
	}
	foreach( $default_opts as $k => $v ){
		if( !array_key_exists( $k, $opts ) ){
			$opts[$k] = $v;
		}
	}
	$options_string = '';
	foreach( $opts as $k => $v ){
		$options_string .= 'data-cycle-' . $k . '="' . $v . '"' . "\n\t\t";
	}
	
	// gss info string
	$position_top = stripos($position,"top") !== false;
	
	$gss_info = '<div class="gss-info">' . "\n\t\t";
	
	if( !empty($carousel) ){
		$thumbs_to_show = $slide_count < 8 ? $slide_count : 8;
		$default_carousel_opts = array(
			'timeout' => '0',
			'carousel-visible' => $thumbs_to_show,
			'slides'=> '> div',
			'carousel-fluid' => 'true',
			'allow-wrap' => 'false'
		);
		$carousel = html_entity_decode($carousel);
		parse_str( $carousel, $carousel_opts );

		$carousel_vis_set = array_key_exists('carousel-visible', $carousel_opts) ? 'data-car-vis-set="true"' : '';
		
		foreach( $default_carousel_opts as $k => $v ){
			if( !array_key_exists( $k, $carousel_opts ) ){
				$carousel_opts[$k] = $v;
			}
		}
		$carousel_opts_string = '';
		foreach( $carousel_opts as $k => $v){
			$carousel_opts_string .= 'data-cycle-' . $k . '="' . $v . '"' . "\n\t\t";
		}
		$gss_info .= '<div class="cycle-slideshow carousel-pager"
		' . $carousel_opts_string . $carousel_vis_set . ">\n" . $carousel_slides . "\t\t</div>\n\t\t";
	}
	
	$gss_info .= 		'<div class="gss-nav"><div id="' . $name . '_prev" class="gss-prev">&lt;</div><div id="' . $name . '_next" class="gss-next">&gt;</div></div>';
	if( !$has_captions ){
		$gss_info .= $pager;
	}
	if( $has_captions ){
		$gss_info .= 		'<div class="gss-long-cap">' . $longest_cap['text'] . "</div>";
		$gss_info .= 		'<div id="' . $name . '_captions" class="gss-captions">' . "</div>";
	}
	$gss_info .= "\n\t</div>";
	
	// begin gss html assembly
	$html = "\n\n" . '<div id="' . $name . '" class="gss-container' . $no_captions_class . ' '. ($position_top? 'summary-position-top': 'summary-position-bottom' ) .'"' . $style . '>' . "\n\t";
	if ( $position_top ){
		$html .= $gss_info;
	}
	
	$html .= 	'<div class="cycle-slideshow" 
		'. $options_string . 
	'>';
	if( $has_captions ){
		$html .= $pager;
	}
	$html .= $slides . "\t</div>\n\t";
	
	if ( ! $position_top ){
		$html .= $gss_info;
	}
	$html .= "\n</div>\n\n";
	return $html;
}

?>
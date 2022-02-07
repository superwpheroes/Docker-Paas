<?php

extract( shortcode_atts( array(
    'src' => null,
	'src_author' => null,
    'id' => '',
    'caption_text' => null,
    'caption_width' => null,
    'caption_bg_color' => null,
    'caption_text_color' => null,
    'caption_text_size' => null,
    'caption_position' => null,
    'box_position' => null,
), $atts ) );

if (strpos($src, 'https:') === false) {
	$src = 'https:'.$src;
}

if (!is_null($src_author) && strpos($src_author, 'https:') === false) {
	$src_author = 'https:'.$src_author;
}


$caption_style = '';
list($i_width, $i_height) = getimagesize(str_replace(home_url(), $_SERVER['DOCUMENT_ROOT'], ''.$src));

// echo '<pre>'.print_r($width, 1).'</pre>';

if(!is_null($caption_position)){

	$c_pos        = explode(',', $caption_position);
	$c_pos_left   = isset($c_pos[0]) ? $c_pos[0] : 'auto';
	$c_pos_top    = isset($c_pos[1]) ? $c_pos[1] : 'auto';
	$c_pos_right  = isset($c_pos[2]) ? $c_pos[2] : 'auto';
	$c_pos_bottom = isset($c_pos[3]) ? $c_pos[3] : 'auto';

	$caption_style = 'position: absolute; left:'.$c_pos_left.'; top:'.$c_pos_top.'; right:'.$c_pos_right.'; bottom:'.$c_pos_bottom.';';
}

$caption_style .= !is_null($caption_width) ? 'width:'.$caption_width.';' : '';
$caption_style .= !is_null($caption_text_size) ? 'font-size:'.$caption_text_size.';' : '';
$caption_style .= !is_null($caption_text_color) ? 'color:'.$caption_text_color.';' : '';
$caption_style .= !is_null($caption_bg_color) ? 'background-color:'.$caption_bg_color.';' : '';


$box_style = '';

if(!is_null($box_position)){

	$b_pos        = explode(',', $box_position);
	$b_pos_left   = isset($b_pos[0]) ? $b_pos[0] : 'auto';
	$b_pos_top    = isset($b_pos[1]) ? $b_pos[1] : 'auto';
	$b_pos_right  = isset($b_pos[2]) ? $b_pos[2] : 'auto';
	$b_pos_bottom = isset($b_pos[3]) ? $b_pos[3] : 'auto';

	$new_i_height = preg_replace("/[^0-9-]/", "", $b_pos_top);

	// echo $new_i_height;

	// if($new_i_height < 0){
	// 	$i_height = $i_height + ($new_i_height / 4);
	// 	// echo 'aaaaaa';
	// }

	$box_style = 'style="position: absolute; left:'.$b_pos_left.'; top:'.$b_pos_top.'; right:'.$b_pos_right.'; bottom:'.$b_pos_bottom.';"';
}

?>

<div id="<?php echo $id;?>" class="ns-box" >
	<div class="ns-box__frame" <?php echo $box_style;?>>

		<?php if(!is_null($src)):
			$src = str_replace(array('http://', 'https://'), '//', $src);
		?>
			<div class="ns-box__image">
				<img src="<?php echo $src; ?>" alt="" />
			</div>
		<?php endif;?>

		<?php if(!is_null($caption_text)):?>
			<div class="ns-box__caption" style="<?php echo $caption_style;?>">
				<div class="ns-box__caption-text ns-underline">
					<?php if(!is_null($src_author)): ?>
						<img src="<?php echo $src_author; ?>" alt="" />
					<?php endif; ?>
					<?php echo $caption_text;?>
				</div>
			</div>
		<?php endif;?>

	</div>
</div>

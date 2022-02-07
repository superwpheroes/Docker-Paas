<?php

class USIN_Html{

	public static function activity_label($label, $value){
		return sprintf('<span class="usin-activity-label">%s:</span> %s', $label, $value);
	}

	public static function tag($text, $status = null){
		$css_class = 'usin-tag';
		if($status){
			$css_class .= " usin-tag-".sanitize_html_class($status);
		}
		return sprintf('<span class="%s">%s</span>', $css_class, $text);
	}

	public static function progress_tag($percentage){
		return sprintf('<span class="usin-tag usin-progress-tag">
			<span class="usin-progress usin-progress-%d usin-progress-score-%d"></span><span class="usin-progress-percentage">%d%%</span></span>', 
		self::round_percentage($percentage), floor($percentage), $percentage);
	}
	
	
	// HELPER METHODS
	
	protected static function round_percentage($percentage){
	    if($percentage > 0 && $percentage < 10){
			//set to 10 to show some progress
	        return 10;
	    }
	    if($percentage > 90 && $percentage < 100){
			//set to 90 to indicate it's still not completed
	        return 90;
	    }
	    
	    return round($percentage / 10) * 10;

	}

}
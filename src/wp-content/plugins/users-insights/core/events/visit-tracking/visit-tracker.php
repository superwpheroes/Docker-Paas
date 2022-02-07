<?php

class USIN_Visit_Tracker{

	protected static $instance;

	protected $post_id = null;
	protected $user = null;
	protected $is_archive_matched = false;

	protected function __construct(){}

	public static function init(){
		if(!self::$instance){
			self::$instance = new USIN_Visit_Tracker();
			self::$instance->run();
		}
	}

	public function run(){
		if(!is_user_logged_in() || is_admin() || !USIN_Visit_Tracking::is_active()){
			return;
		}

		$this->user = wp_get_current_user();
		$this->set_post_id();

		$should_track = $this->should_track_this_role() && $this->should_track_this_post();
		$should_track = apply_filters('usin_should_track_visit', $should_track, $this->user->ID, $this->post_id);

		if($should_track){
			USIN_Event::record(USIN_Visit_Tracking::$event_type, $this->post_id, $this->user->ID);
		}
	}

	protected function set_post_id(){
		global $post;
		if(is_singular()){
			$this->post_id = $post->ID;
		}elseif(is_home()){
			//It is the blog index page. This is technically not a page,
			//so we'll attempt to match it to the corresponding page ID
			$blog_page_id = intval(get_option('page_for_posts'));
			if($blog_page_id){
				$this->is_archive_matched = true;
				$this->post_id = $blog_page_id;
			}
		}elseif(is_post_type_archive('product')){
			//product archives are WooCommerce shop pages. Match the archive to the actual page ID.
			//we're doing an exception here to run WooCommerce related code, as
			//all the plugin modules code is only loaded in the admin - there is no point
			//in restructuring the entire plugin module functionality just for this small feature
			if(function_exists('is_woocommerce') && function_exists('is_shop') && function_exists('wc_get_page_id')){
				if(is_woocommerce() && is_shop()){
					$shop_page_id = wc_get_page_id('shop');
					if($shop_page_id){
						$this->is_archive_matched = true;
						$this->post_id = $shop_page_id;
					}
				}
			}
		}
	}

	protected function should_track_this_role(){
		$track_roles = USIN_Visit_Tracking::get_tracked_roles();
		$matched_roles = array_intersect($this->user->roles, $track_roles);
		return !empty($matched_roles);
	}

	protected function should_track_this_post(){
		$track_post_types = USIN_Visit_Tracking::get_tracked_post_types();
		if(empty($track_post_types)){
			return false;
		}
		if(is_singular($track_post_types)){
			return true;
		}
		if($this->is_archive_matched && in_array('page', $track_post_types)){
			return true;
		}
		return false;
	}
	
}
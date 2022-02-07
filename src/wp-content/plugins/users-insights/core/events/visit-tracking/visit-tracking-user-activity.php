<?php

class USIN_Visit_Tracking_User_Activity{

	protected static $instance;

	protected $module_id;

	protected function __construct(){
		$this->module_id = USIN_Visit_Tracking::$module_id;
	}

	public static function init(){
		if(!self::$instance){
			self::$instance = new USIN_Visit_Tracking_User_Activity();
			self::$instance->setup();
		}
	}

	public function setup(){
		add_filter('usin_user_activity', array($this, 'filter_user_activity'), 20, 2);
		add_filter('usin_user_all_activity_callbacks', array($this, 'register_all_activity_callback'));
		usin_register_activity_data_callback($this->module_id, array($this, 'load_full_list'));
		usin_register_activity_data_callback($this->module_id.'_most_visited', array($this, 'load_full_list_most_visited'));
	}

	public function filter_user_activity($activity, $user_id){
		$visit_activity = $this->get_visit_activity($user_id);
		if(!empty($visit_activity)){
			$activity[]= $visit_activity;
		}

		$most_visited_activity = $this->get_most_visited_activity($user_id);
		if(!empty($most_visited_activity)){
			$activity[]= $most_visited_activity;
		}
		
		return $activity;
	}

	protected function get_visit_activity($user_id){
		$pages = USIN_Visit_Tracking_Data::get($user_id, 5);

		if(!empty($pages)){
			return array(
				'type' => $this->module_id,
				'label' => __('Last Visited Pages', 'usin'),
				'dialog' => true,
				'list' => $pages,
				'icon' => $this->module_id
			);
		}
	}

	protected function get_most_visited_activity($user_id){
		$most_visited = USIN_Visit_Tracking_Data::get_most_visited($user_id, 5);

		if(empty($most_visited)){
			return;
		}

		foreach ($most_visited as &$visit_data) {
			$visit_data['title'] = sprintf('<span class="usin-tag usin-tag-light usin-tag-first">%d</span>', $visit_data['num_visits']).$visit_data['title'];
		}

		return array(
			'type' => $this->module_id.'_most_visited',
			'label' => __('Most Visited Pages', 'usin'),
			'dialog' => true,
			'list' => $most_visited,
			'icon' => $this->module_id
		);
	}

	public function load_full_list($user_id){
		return array(
			'itemProps' => array('title' => __('Title', 'usin'), 'post_type' => __('Type', 'usin')),
			'items' => USIN_Visit_Tracking_Data::get($user_id)
		);
	}

	public function load_full_list_most_visited($user_id){
		return array(
			'itemProps' => array('title' => __('Title', 'usin'), 'post_type' => __('Type', 'usin'), 'num_visits' => __('Number of visits', 'usin')),
			'items' => USIN_Visit_Tracking_Data::get_most_visited($user_id)
		);
	}


}
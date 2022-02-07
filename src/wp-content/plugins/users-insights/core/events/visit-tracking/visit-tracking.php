<?php

class USIN_Visit_Tracking{

	public static $module_id = 'visit_tracking';
	public static $event_type = 'visit';

	protected static $post_search = null;

	public static function init(){
		add_filter('usin_module_options', array(__CLASS__ , 'register_module'));
		add_filter('usin_should_activate_module_visit_tracking', array(__CLASS__, 'check_db_table_existence'));
		add_action('template_redirect', array(__CLASS__, 'init_visit_tracker'));
		add_action('admin_init', array(__CLASS__, 'init_admin_features'));
		add_filter('usin_fields', array(__CLASS__ , 'add_module_fields'));
	}

	public static function register_module($modules){
		if(is_array($modules)){
			$tracking_module = self::get_module_options();
			array_splice( $modules, 2, 0, array($tracking_module));
		}

		return $modules;
	}

	public static function is_active(){
		return usin_modules()->is_module_active(self::$module_id);
	}

	public static function init_visit_tracker(){
		if(self::is_active()){
			USIN_Visit_Tracker::init();
		}
	}

	public static function init_admin_features(){
		if(self::is_active()){
			self::get_post_search(); //just init the post search so it can register its AJAX actions
			USIN_Visit_Tracking_User_Activity::init();
			new USIN_Event_Query('has_visited', self::$event_type, USIN_Event_Query::INCLUDE_TYPE);
			new USIN_Event_Query('has_not_visited', self::$event_type, USIN_Event_Query::EXCLUDE_TYPE);
		}
	}

	protected static function get_module_options(){
		return array(
			'id' => self::$module_id,
			'name' => __('Page Visit Tracking', 'usin'),
			'desc' => __('Allows you to track which pages your users visit while logged in. ', 'usin'),
			'allow_deactivate' => true,
			'in_beta' => true,
			'buttons' => array(
				array('text'=> __('Learn More', 'usin'), 'link'=>'https://usersinsights.com/wordpress-track-user-page-views/', 'target'=>'_blank')
			),
			'settings' => array(
				'track_roles' => array(
					'name' => __('Enable tracking for user roles', 'usin'),
					'type' => USIN_Settings_Field::TYPE_CHECKBOXES,
					'options' => self::get_role_options()
				),
				'track_post_types' => array(
					'name' => __('Enable tracking for post types', 'usin'),
					'type' => USIN_Settings_Field::TYPE_CHECKBOXES,
					'options' => self::get_post_type_options()
				)
			),
			'active_by_default' => false
		);
	}

	public static function add_module_fields($fields){
		if(self::is_active()){

			$post_search = self::get_post_search();
			$fields[]= array(
				'name' => __('Has visited', 'usin'),
				'id' => 'has_visited',
				'show' => false,
				'hideOnTable' => true,
				'fieldType' => self::$module_id,
				'filter' => array(
					'type' => 'select_option',
					'options' => $post_search->get_options(),
					'searchAction' => $post_search->get_search_action()
				),
				'module' => self::$module_id
			);

			$fields[]= array(
				'name' => __('Has not visited', 'usin'),
				'id' => 'has_not_visited',
				'show' => false,
				'hideOnTable' => true,
				'fieldType' => self::$module_id,
				'filter' => array(
					'type' => 'select_option',
					'options' => $post_search->get_options(),
					'searchAction' => $post_search->get_search_action()
				),
				'module' => self::$module_id
			);
			
		}
		return $fields;
	}

	protected static function get_post_type_options(){
		$result = array();

		if(!is_admin()){
			//no need to load them on the front end
			return $result;
		}

		$post_types = get_post_types(array('public'=>true), 'objects');
		foreach ($post_types as $key => $post_type) {
			$label = $post_type->label;
			if($label == 'Posts' && $post_type->name !== 'post'){
				//It's a post type without a properly set label
				$label .= " ($post_type->name)";
			}
			$result[$key]= $label;
		}

		return $result;
	}

	protected static function get_role_options(){
		//load the roles in the admin only, as they are needed in the module options page only
		//in this way we avoid a redundant database query on the front-end
		return is_admin() ? USIN_Helper::get_roles(true) : array();
	}

	protected static function get_post_search(){
		if(self::$post_search === null){
			$track_post_types = $track_post_types = self::get_tracked_post_types();
			self::$post_search = new USIN_Post_Option_Search($track_post_types, array('post_status' => 'any'), 'usin_visit_tracking_search');
		}
		return self::$post_search;
	}

	public static function check_db_table_existence($should_activate){
		$res = USIN_Event::make_sure_db_table_created();

		if(!$res){
			return new WP_Error('usin_events_not_created', __('Error: required events database table could not be created. '));
		}

		return $should_activate;
	}

	public static function get_tracked_post_types(){
		return usin_get_module_setting(self::$module_id, 'track_post_types');
	}

	public static function get_tracked_roles(){
		return usin_get_module_setting(self::$module_id, 'track_roles');
	}

}
<?php 

class USIN_User{

	public $username = '';
	public $email = '';
	public $name = '';
	public $registered = '';
	public $ID = 0;
	public $avatar = '';
	public $activity = array();
	public $actions = array();
	public $coordinates = array();
	protected $options;
	public static $date_fields;
	protected $should_ignore_date_format = false;

	public function __construct($data, $options = array()){
		$this->options = $options;
		$data = apply_filters('usin_user_db_data', $data);
    	$this->set_user_data_from_db( $data );
	}


	public function set_profile_data(){
		$this->avatar = get_avatar( $this->ID, 200 );

		$this->coordinates = USIN_Helper::coordinates_string_to_array($this->coordinates);
		
		$this->set_activity();
		$this->set_actions();
		$this->set_user_groups();
		$this->set_role();
		
		$this->notes = USIN_Note::get_all($this->ID);
	}
	
	
	protected function set_user_data_from_db($data){
		$this->set_default_data($data);

		$this->set_avatar();
		if(isset($data->post_num)){
			$this->posts = $data->post_num;
		}

		//set comments
		if(!isset($data->comments)){
			$this->set_comments();
		}
	}


	protected function set_default_data($data){
		$date_fields = $this->get_date_fields();
		
		foreach($data as $key => $val){
			if(!empty($val) && in_array($key, $date_fields) && $key != 'last_seen' && !$this->should_ignore_date_format){
				$this->$key = USIN_Helper::format_date($val);
			}else{
				$this->$key = $val;
			}
		}
		
		if(usin_options()->is_field_visible('role')){
			$this->set_role();
		}
		$this->format_last_seen();
		if(usin_options()->is_field_visible('user_groups')){
			$this->set_user_groups();
		}
	}
	
	protected function format_last_seen(){
		if(!$this->should_ignore_date_format && isset($this->last_seen)){
			$last_seen = $this->last_seen;
			$this->last_seen = empty($last_seen) ? '' : USIN_Helper::format_date_human($last_seen);

			if($this->last_seen == 'now'){
				$this->last_seen = __('Just now', 'usin');
				$this->online = true;
			}
		}
	}
	
	protected function set_user_groups(){
		if(!isset($this->user_groups)){
			$this->user_groups = USIN_Groups::get_user_groups($this->ID);
		}
	}
	
	protected function set_avatar(){
		$this->avatar = get_avatar( $this->ID );
	}
	
	protected function set_comments(){
		if(usin_options()->is_field_visible('comments')){
			$this->comments = get_comments(array('user_id'=>$this->ID, 'count'=>true));
		}
	}
	
	protected function set_role(){
		$wp_roles = wp_roles();
		$role_names = array();
		$user_data = get_userdata( $this->ID );
		$roles = array_values($user_data->roles);
		if(!empty($roles)){
			foreach ($roles as $role ) {
				$role_names[]= $wp_roles->roles[$role]['name'];
			}
		}
		$this->role = implode(', ', $role_names);
	}

	/**
	 * Loads the fields that are from type "date". Uses a static property so that
	 * once the fields are loaded, they will be cached and reused for all of the users.
	 * @return [type] [description]
	 */
	protected function get_date_fields(){
		if(self::$date_fields === null){
			global $usin;
			self::$date_fields = $usin->manager->options->get_field_ids_by_operator_type('date');
		}

		return self::$date_fields;
	}


	protected function set_activity(){

		$user_activity = new USIN_User_Activity($this->ID);
		$this->activity = $user_activity->get();

	}


	protected function set_actions(){
		$this->actions[] = array(
			'id' => 'edit-user',
			'name' => __('Edit User'),
			'link' => get_edit_user_link($this->ID)
		);

		$this->actions = apply_filters('usin_user_actions', $this->actions, $this->ID);
	}

}

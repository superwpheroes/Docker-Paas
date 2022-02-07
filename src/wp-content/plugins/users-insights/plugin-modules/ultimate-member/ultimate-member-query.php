<?php

/**
 * Ultimate Member module query functionality.
 */
class USIN_Ultimate_Member_Query{
	
	protected $um_fields;
	protected $prefix;
	
	/**
	 * @param array $um_fields the Ultimate Member fields
	 * @param string $prefix    prefix to use for prefixing the UM fields, so
	 * they don't overwrite the default fields
	 */
	public function __construct($um_fields, $prefix){
		$this->um_fields = $um_fields;
		$this->prefix = $prefix;
	}

	/**
	 * Initializes the main functionality.
	 */
	public function init(){
		if(is_admin()){
			$this->init_meta_query();
			add_filter('usin_user_db_data', array($this, 'replace_account_status_code_with_name'));
		}
	}
	
	/**
	 * Initializes the meta query for the Ultimate Member fields.
	 */
	protected function init_meta_query(){
		foreach ($this->um_fields as $field ) {
			$meta_query = new USIN_Meta_Query($field['meta_key'], $field['filter']['type'], $this->prefix);
			$meta_query->init();
		}

		$status_query = new USIN_Meta_Query('account_status', 'text', 'um_');
		$status_query->init();
		
		//community role field
		if(USIN_Ultimate_Member::is_um_older_than_v2()){
			$role_query = new USIN_Meta_Query('role', 'select', $this->prefix);
			$role_query->init();
		}
	}

	public function replace_account_status_code_with_name($user_data){
		if(!empty($user_data->um_account_status)){
			$statuses = USIN_Ultimate_Member::get_account_statuses();
			if(isset($statuses[$user_data->um_account_status])){
				$user_data->um_account_status = $statuses[$user_data->um_account_status];
			}
		}

		return $user_data;
	}
}
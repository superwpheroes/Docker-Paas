<?php

/**
 * Contains the general plugin action hooks.
 */
class USIN_Actions{

	public static function init(){
		add_action('deleted_user', array('USIN_Actions', 'delete_user_data'));
	}

	/**
	 * Deletes the saved by Users Insights user data (such as geolocation and browser info),
	 * after a user has been deleted
	 * @param $user_id the ID of the deleted user
	 */
	public static function delete_user_data($user_id){
		global $wpdb;
		$manager = usin_manager();

		// delete the Users Insights data
		$table_name = $wpdb->prefix.$manager->user_data_db_table;
		$wpdb->delete( $table_name, array( 'user_id' => $user_id ) );

		// delete the user groups
		USIN_Groups::delete_all_user_groups($user_id);
	}
	
}


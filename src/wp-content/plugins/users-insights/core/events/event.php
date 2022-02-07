<?php

class USIN_Event{

	const DELIMETER = ',';
	const MAX_ITEMS = 300;

	public static function record($event_type, $item_id, $user_id){
		global $wpdb;

		if(!$event_type || !$item_id || !$user_id){
			return self::record_error();
		}

		$items = self::find($event_type, $user_id);
		
		if($items === null){
			//this is the first time this event is triggered, create a new event
			$items = array($item_id);
			$items_str = self::items_array_to_string($items);

			
			$res = $wpdb->insert( 
				self::get_table_name(), 
				array( 
					'event_type' => $event_type, 
					'user_id' => $user_id, 
					'items' => $items_str),
				array('%s', '%d', '%s') 
			);
		}else{
			$items[]=$item_id;
			$max_items = self::get_max_items_limit($event_type);
			if(sizeof($items) > $max_items){
				//maximum items to store limit reached, store the last n items
				$items = array_slice($items, -$max_items);
			}
			$items_str = self::items_array_to_string($items);

			//update the existing record
			$res = $wpdb->update( 
				self::get_table_name(),
				array( 
					'items' => $items_str
				), 
				array('event_type' => $event_type, 'user_id' => $user_id), 
				array('%s'), 
				array('%s', '%d') 
			);
		}

		return ($res !== false) ? true : self::record_error();
	}

	protected static function get_max_items_limit($event_type){
		$max_items = intval(apply_filters('usin_events_max_items_to_store', self::MAX_ITEMS, $event_type));
		//make sure we allow reasonable limits. Allowing a large number of items to be stored
		//could lead to memory and performance issues
		return ($max_items > 0 && $max_items < 1000) ? $max_items : self::MAX_ITEMS;
	}

	protected static function record_error(){
		return new WP_Error('usin_event_record_fail', __('Error saving event record', 'usin'));
	}

	public static function find($event_type, $user_id){
		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare (
			"SELECT * FROM ".self::get_table_name()." WHERE event_type = %s AND user_id = %d", 
			$event_type, $user_id ));

		return empty($row) ? null : self::items_string_to_array($row->items);
	}

	public static function get_latest($event_type, $user_id, $limit = null){
		$items = self::find($event_type, $user_id);
		if($items === null){
			return array();
		}
		if($limit){
			$items = array_slice($items, -$limit);
		}
		return array_reverse($items);
	}

	/**
	 * Deletes all the event data for a specific event and user.
	 *
	 * @param string $event_type the event type
	 * @param int $user_id the user ID
	 * @return boolean true if any records have been deleted and false otherwise,
	 * even if there were no existing records.
	 */
	public static function delete($event_type, $user_id){
		global $wpdb;
		$res = $wpdb->delete( 
			self::get_table_name(),
			array('event_type' => $event_type, 'user_id' => $user_id),
			array( '%s', '%d' )
		);

		return !!$res;
	}

	public static function items_string_to_array($items_str){
		if(empty($items_str) || !is_string($items_str)){
			return array();
		}

		$arr = explode(self::DELIMETER, $items_str);
		return array_map('intval', $arr);
	}

	public static function items_array_to_string($items_arr){
		if(empty($items_arr) || !is_array($items_arr)){
			return '';
		}
		return implode(self::DELIMETER, $items_arr);
	}

	public static function get_table_name(){
		global $usin, $wpdb;

		return $wpdb->prefix.$usin->manager->events_db_table;
	}

	public static function make_sure_db_table_created(){
		if(!self::db_table_created()){
			do_action('usin_schema_update_required');
			if(!self::db_table_created()){
				return false;
			}
		}

		return true;
	}

	protected static function db_table_created(){
		global $wpdb;

		$table_name = self::get_table_name();
		$res = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
		
		return $res == $table_name;
	}
}
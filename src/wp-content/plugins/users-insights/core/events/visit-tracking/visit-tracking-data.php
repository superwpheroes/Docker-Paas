<?php

class USIN_Visit_Tracking_Data{

	protected static $post_types = null;
	protected static $most_visited_min_count = 2;

	public static function get($user_id, $limit = null){
		$page_ids = USIN_Event::get_latest(USIN_Visit_Tracking::$event_type, $user_id, $limit);
		return self::page_ids_to_data($page_ids);
	}

	public static function get_most_visited($user_id, $limit = null){
		$most_visited_ids_count = self::get_most_visited_ids_with_count($user_id);
		$result = self::page_ids_to_data(array_map('intval', array_keys($most_visited_ids_count)));

		if($limit){
			$result = array_slice($result, 0, $limit);
		}

		foreach ($result as &$visit_data) {
			$visit_data['num_visits'] = $most_visited_ids_count[$visit_data['id']];
		}

		return $result;
	}

	public static function delete($user_id){
		return USIN_Event::delete(USIN_Visit_Tracking::$event_type, $user_id);
	}

	/**
	 * Retrieves the number of visited tracked pages for a user
	 *
	 * @param int $user_id
	 * @return int the number of visited tracked pages
	 */
	public static function count($user_id){
		$page_ids = USIN_Event::get_latest(USIN_Visit_Tracking::$event_type, $user_id);
		return sizeof($page_ids);
	}

	protected static function page_ids_to_data($ids){
		global $wpdb;

		if(empty($ids)) return array();

		$ids_str = implode(',', array_unique($ids));
		$posts = $wpdb->get_results("SELECT ID, post_title, post_type FROM $wpdb->posts WHERE ID IN ($ids_str)");

		if(empty($posts)) return array();

		$result = array();
		foreach ($ids as $id ) {
			$result[]=self::find_post_data($id, $posts);
		}

		return $result;
	}

	protected static function find_post_data($id, $posts){
		foreach ($posts as $post ) {
			if($post->ID == $id){
				return array(
					'title' => $post->post_title, 
					'post_type' => self::get_post_type_name($post->post_type),
					'link' => get_permalink($id),
					'id' => $id
				);
			}
		}

		return array(
			'title' => sprintf('[%s #%d]', __('Page removed', 'usin'), $id), 
			'post_type' => '',
			'id' => $id
		);
	}

	protected static function get_post_type_name($post_type){
		if(self::$post_types === null){
			//cache the post types so we'll only load them once
			self::$post_types = array();
			$post_types = get_post_types(array('public'=>true), 'objects');
			foreach ($post_types as $k => $post_type_obj ) {
				self::$post_types[$k] = $post_type_obj->labels->singular_name;
			}
		}

		return isset(self::$post_types[$post_type]) ? self::$post_types[$post_type] : $post_type;
	}

	/**
	 * Retrieves the IDs of the most visited pages with the number of times visited.
	 *
	 * @param int $user_id
	 * @return array an associative array where the key is the ID of the page and the 
	 * value is the number of times visited. It is sorted by the pages with most visits.
	 */
	protected static function get_most_visited_ids_with_count($user_id){
		$all_page_ids = USIN_Event::get_latest(USIN_Visit_Tracking::$event_type, $user_id);
		$page_ids_with_count = array_count_values($all_page_ids);
		$result = array_filter( $page_ids_with_count, array(__CLASS__, 'counts_as_most_visited') );
		uasort($result, array(__CLASS__, 'compare_count'));
		return $result;
	}

	//helper callback methods

	public static function counts_as_most_visited($number){
		return $number > self::$most_visited_min_count;
	}

	public static function compare_count($a, $b){
		return $a < $b;
	}

}
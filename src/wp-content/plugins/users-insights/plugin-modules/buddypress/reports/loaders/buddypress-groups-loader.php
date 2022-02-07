<?php

class USIN_Buddypress_Groups_Loader extends USIN_Multioption_Field_Loader{

	public function load_data(){
		$data = $this->get_group_count();
		$groups = USIN_BuddyPress::get_groups(true);

		return $this->match_ids_to_names($data, $groups);
	}

	protected function get_group_count(){
		global $wpdb;

		$prefix = USIN_BuddyPress_Query::get_prefix();

		$query = "SELECT group_id AS $this->label_col, COUNT(*) as $this->total_col FROM ".$prefix."bp_groups_members".
			" GROUP BY group_id ORDER BY $this->total_col DESC LIMIT $this->max_items";

		return $wpdb->get_results($query);
	}

}
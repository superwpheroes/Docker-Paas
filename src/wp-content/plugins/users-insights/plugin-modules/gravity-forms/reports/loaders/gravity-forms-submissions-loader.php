<?php

class USIN_Gravity_Forms_Submissions_Loader extends USIN_Standard_Report_Loader{

	protected function load_data(){
		global $wpdb;

		$table_name = USIN_Gravity_Forms::get_entries_db_table_name();

		$query = "SELECT form_id AS $this->label_col, COUNT(DISTINCT created_by) AS $this->total_col".
			" FROM $table_name GROUP BY form_id".
			"  ORDER BY $this->total_col DESC LIMIT $this->max_items";
		$data = $wpdb->get_results( $query );
		return $this->apply_form_names($data);
	}

	protected function apply_form_names($data){
		$forms = $this->get_gf_forms();

		foreach ($data as &$row ) {
			$form_id = intval($row->label);
			if(isset($forms[$form_id])){
				$row->label = $forms[$form_id];
			}
		}

		return $data;
	}

	protected function get_gf_forms(){
		
		$forms = array();

		if(method_exists('GFAPI', 'get_forms')){
			$gf_forms = GFAPI::get_forms();
			
			foreach ($gf_forms as $form ) {
				$form_id = intval($form['id']);
				$forms[$form_id] = $form['title'];
			}
		}

		return $forms;

	}
	

}
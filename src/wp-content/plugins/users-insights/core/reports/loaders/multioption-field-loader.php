<?php

class USIN_Multioption_Field_Loader extends USIN_Standard_Report_Loader{

	protected function load_data(){
		$data = $this->get_data();
		
		return $this->unify_fields($data);
	}

	protected function get_data(){
		$field_id = $this->report->get_field_id();
		return $this->load_meta_data($field_id);
	}

	protected function unify_fields($data){
		$data = $this->data_items_to_array($data);
		$vals = $this->get_total_count($data);

		$new_data = array();

		foreach ($vals as $label => $total) {
			$new_data[] = (object)array('label' => $label, 'total' => $total);
		}

		return $new_data;

	}

	protected function get_total_count($data){
		$vals = array();

		foreach ($data as $item ) {
			
			if(is_array($item->label)){
				foreach ($item->label as $label ) {
					if(isset($vals[$label])){
						$vals[$label] += $item->total;
					}else{
						$vals[$label] = $item->total;
					}
				}
			}
		}

		return $vals;

	}

	/**
	 * Unserialize the field names when they are stored in a serialized format
	 *
	 * @param array $data
	 * @return array
	 */
	protected function data_items_to_array($data){

		foreach ($data as &$item ) {
			$item->label = $this->value_to_array($item->label);
		}

		return $data;
	}

	protected function value_to_array($value){
		//if it is serialized, return the unserialized value
		return maybe_unserialize($value);
	}


}
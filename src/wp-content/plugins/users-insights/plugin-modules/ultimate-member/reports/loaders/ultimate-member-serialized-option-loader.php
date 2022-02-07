<?php

class USIN_Ultimate_Member_Serialized_Option_Loader extends USIN_Standard_Report_Loader{

	protected function load_data(){
		$field_id = $this->report->get_field_id();
		
		$data = $this->load_meta_data($field_id);
		return $this->unify_fields($data);
	}

	/**
	 * Ultimate Member radio fields can be saved as either plain text
	 * or serialized field (introduced in newer version).
	 * This function will unify fields so that values like 
	 * a:1:{i:0;s:3:"Val";} and "Val" are treated as the same value
	 *
	 * @return void
	 */
	protected function unify_fields($data){
		
		$data = $this->unserialize_data($data);
		$matchers = $this->find_matchers($data);

		foreach ($matchers as $index => $matched_index) {
			$data[$index]->total += $data[$matched_index]->total;
			unset($data[$matched_index]);
		}

		return array_values($data);
	}

	/**
	 * Unserialize the field names when they are stored in a serialized format
	 *
	 * @param array $data
	 * @return array
	 */
	protected function unserialize_data($data){

		foreach ($data as &$item ) {
			$val = maybe_unserialize($item->label);
			if(is_array($val) && isset($val[0])){
				$item->label = $val[0];
			}
		}

		return $data;
	}

	/**
	 * Finds matching items by the field value name.
	 *
	 * @param array $data
	 * @return array of the matching items' indexes. The index of the array
	 * is the index of the first occurance of the item and the value is the
	 * index of the second occurrance. 
	 * Example: array( 3 => 5 ) means that the item at index 3 has a matching
	 * item with the same name at index 5.
	 */
	protected function find_matchers($data){
		$matchers = array();
		
		for($i = 0; $i < sizeof($data) -1 ; $i++){
			for($j = $i+1; $j < sizeof($data); $j++){
				
				if(strtolower($data[$i]->label) == strtolower($data[$j]->label)){
					$matchers[$i] = $j;
					break;
				}
			}
		}

		return $matchers;
	}

}
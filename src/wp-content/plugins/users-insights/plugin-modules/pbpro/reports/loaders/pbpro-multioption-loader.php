<?php

class USIN_Pbpro_Multioption_Loader extends USIN_Multioption_Field_Loader{


	protected function value_to_array($value){
		//if it is serialized, return the unserialized value
		return array_map('trim', explode(',', $value));
	}

	/**
	 * Replaces the option values with their corresponding labels.
	 */
	protected function format_data($data){
		$data = parent::format_data($data);
		$field = USIN_Pbpro::get_form_field_by_id($this->report->id);

		if(!is_array($data) || empty($data) || empty($field)){
			return $data;
		}

		foreach ($data as &$item) {
			$item->label = $field->format_value($item->label);
		}

		return $data;
	}

}
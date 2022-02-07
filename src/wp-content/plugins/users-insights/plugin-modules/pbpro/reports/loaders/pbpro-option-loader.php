<?php 

class USIN_Pbpro_Option_Loader extends USIN_Meta_Field_Loader{

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
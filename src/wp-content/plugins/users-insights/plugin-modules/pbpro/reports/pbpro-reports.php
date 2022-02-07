<?php

class USIN_Pbpro_Reports extends USIN_Module_Reports{

	protected $group = 'pbpro';

	public function get_group(){
		
		return array(
			'id' => $this->group,
			'name' => 'Profile Builder Pro'
		);
	}

	public function get_reports(){

		$fields = USIN_Pbpro::get_form_fields();
		$reports = array();

		foreach ($fields as $field) {
			
			switch ($field->type) {
				case 'select':
					$reports[]= new USIN_Standard_Report(
						$field->id, 
						$field->name, 
						array(
							'group' => $this->group,
							'visible' => $this->get_default_report_visibility(),
							'field_id' => $field->meta_key,
							'loader_class' => 'USIN_Pbpro_Option_Loader'
						)
					);
				break;
				case 'comma_multioption':
					$reports[]= new USIN_Standard_Report(
						$field->id, 
						$field->name, 
						array(
							'group' => $this->group,
							'visible' => $this->get_default_report_visibility(),
							'field_id' => $field->meta_key,
							'loader_class' => 'USIN_Pbpro_Multioption_Loader',
							'type' => USIN_Report::BAR
						)
					);
				break;
				case 'number':

					$reports[]= new USIN_Standard_Report(
						$field->id, 
						$field->name, 
						array(
							'group' => $this->group,
							'visible' => $this->get_default_report_visibility(),
							'field_id' => $field->meta_key,
							'loader_class' => 'USIN_Numeric_Meta_Field_Loader',
							'type' => USIN_Report::BAR
						)
					);
				break;
			}
		}


		return $reports;

	}
}
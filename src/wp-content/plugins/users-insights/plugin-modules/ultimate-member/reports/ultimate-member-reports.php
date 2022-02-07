<?php

class USIN_Ultimate_Member_Reports extends USIN_Module_Reports{

	protected $group = 'ultimate_member';

	public function __construct($um){
		parent::__construct();
		$this->um = $um;
	}

	public function get_group(){
		
		return array(
			'id' => $this->group,
			'name' => 'Ultimate Member'
		);
	}

	public function get_reports(){

		$fields = $this->um->get_form_fields();
		$reports = array();

		foreach ($fields as $field) {
			
			switch ($field['filter']['type']) {
				case 'select':
					$reports[]= new USIN_Standard_Report(
						$field['id'], 
						$field['name'], 
						array(
							'group' => $this->group,
							'visible' => $this->get_default_report_visibility(),
							'field_id' => $field['meta_key'],
							'loader_class' => 'USIN_Meta_Field_Loader'
						)
					);
				break;
				case 'serialized_option':
					$reports[]= new USIN_Standard_Report(
						$field['id'], 
						$field['name'], 
						array(
							'group' => $this->group,
							'visible' => $this->get_default_report_visibility(),
							'field_id' => $field['meta_key'],
							'loader_class' => 'USIN_Ultimate_Member_Serialized_Option_Loader'
						)
					);
				break;
				case 'serialized_multioption':
					$reports[]= new USIN_Standard_Report(
						$field['id'], 
						$field['name'], 
						array(
							'group' => $this->group,
							'visible' => $this->get_default_report_visibility(),
							'field_id' => $field['meta_key'],
							'loader_class' => 'USIN_Multioption_Field_Loader',
							'type' => USIN_Report::BAR
						)
					);
				break;
				case 'number':
					if(isset($field['um_type']) && $field['um_type'] == 'rating'){
						$loader = 'USIN_Ultimate_Member_Rating_Loader';
					}else{
						$loader = 'USIN_Numeric_Meta_Field_Loader';
					}

					$reports[]= new USIN_Standard_Report(
						$field['id'], 
						$field['name'], 
						array(
							'group' => $this->group,
							'visible' => $this->get_default_report_visibility(),
							'field_id' => $field['meta_key'],
							'loader_class' => $loader,
							'type' => USIN_Report::BAR
						)
					);
				break;
			}
		}


		return $reports;

	}
}
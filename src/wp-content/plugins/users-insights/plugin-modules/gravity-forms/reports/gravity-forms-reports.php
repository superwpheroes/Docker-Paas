<?php

class USIN_Gravity_Forms_Reports extends USIN_Module_Reports{

	protected $group = 'gravity_forms';

	public function __construct($gf){
		parent::__construct();
		$this->gf = $gf;
	}

	public function get_group(){
		
		return array(
			'id' => $this->group,
			'name' => 'Gravity Forms'
		);
	}

	public function get_reports(){

		$reports = array();

		$reports[]= new USIN_Standard_Report(
			'gravity_forms_submissions', 
			'Top user submitted forms', 
			array(
				'group' => $this->group,
				'type' => USIN_Report::BAR
			)
		);

		if(empty($this->gf->gf_fields)){
			return $reports;
		}
		
		foreach ($this->gf->gf_fields as $field) {

			switch ($field['type']) {
				case 'select':
					$reports[]= new USIN_Standard_Report(
						$field['id'], 
						$field['name'], 
						array(
							'group' => $this->group,
							'visible' => $this->get_default_report_visibility(), 
							'loader_class' => 'USIN_Meta_Field_Loader'
						)
					);
				break;
				case 'multioption_text':
				$reports[]= new USIN_Standard_Report(
					$field['id'], 
					$field['name'], 
					array(
						'group' => $this->group,
						'visible' => $this->get_default_report_visibility(), 
						'loader_class' => 'USIN_Gravity_Forms_Multioption_Loader',
						'type' => USIN_Report::BAR
					)
				);
				break;
				case 'number':
				$reports[]= new USIN_Standard_Report(
					$field['id'], 
					$field['name'], 
					array(
						'group' => $this->group,
						'visible' => $this->get_default_report_visibility(), 
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
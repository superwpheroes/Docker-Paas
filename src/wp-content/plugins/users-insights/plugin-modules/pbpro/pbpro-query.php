<?php

/**
 * Profile Builder Pro module query functionality.
 */
class USIN_Pbpro_Query{
	
	protected $form_fields = array();

	/**
	 * @param array $form_fields the Profile Builder Pro fields
	 * @param string $prefix    prefix to use for prefixing the Pbpro fields, so
	 * they don't overwrite the default fields
	 */
	public function __construct($form_fields){
		$this->form_fields = $form_fields;
	}

	/**
	 * Initializes the main functionality.
	 */
	public function init(){
		$this->init_meta_query();
	}

	
	/**
	 * Initializes the meta query for the Profile Builder Pro fields.
	 */
	protected function init_meta_query(){
		foreach ($this->form_fields as $field ) {
			if($field->is_date_field()){
				$query = new USIN_Pbpro_Date_Field_Query($field->meta_key, $field->get_date_format());
			}else{
				$query = new USIN_Meta_Query($field->meta_key, $field->type, USIN_Pbpro::PREFIX);
			}
			$query->init();
		}

	}

}
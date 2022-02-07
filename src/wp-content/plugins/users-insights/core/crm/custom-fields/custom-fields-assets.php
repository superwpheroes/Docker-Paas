<?php

/**
 * Includes the assets loading and script printing functionality for the Custom Fields
 * page.
 */
class USIN_Custom_Fields_Assets extends USIN_Assets{

	protected $js_options_filter = 'usin_cf_options';

	protected $has_ui_select = true;

	protected function register_custom_assets(){
		$this->js_assets['usin_custom_fields'] = array('path' => 'js/custom-fields.min.js',
			'deps' => $this->base_js_assets);
		$this->js_assets['usin_custom_fields_templates'] = array('path' => 'views/custom-fields/templates.js', 
			'deps' => array('usin_custom_fields'));
	}

	/**
	 * Loads the required assets on the Custom Fields page/
	 */
	public function enqueue_assets(){

		$this->enqueue_base_assets();
		$this->enqueue_scripts(array('usin_custom_fields', 'usin_custom_fields_templates'));

	}


	/**
	 * Prints the initializing JavaScript code on the Custom Fields page.
	 */
	protected function get_js_options(){
		$options = array(
			'viewsURL' => plugins_url('views/custom-fields', $this->base_file),
			'fields' => USIN_Custom_Fields_Options::get_saved_fields(),
			'fieldTypes' => USIN_Custom_Fields_Options::$field_types,
			'nonce' => $this->page->ajax_nonce,
			'customTemplates' => array()
		);

		$strings = array(
			'addField' => __('Add Field', 'usin'),
			'fieldName' => __('Field Name', 'usin'),
			'fieldKey' => __('Field Key', 'usin'),
			'fieldType' => __('Field Type', 'usin'),
			'fields' => __('Fields', 'usin'),
			'fieldOptions' => __('Field Options', 'usin'),
			'fieldOptionsInfo' => __('Enter each option on a new line. For more control, you may specify both a value and label like this:<br> red : Red Color<br>blue : Blue Color', 'usin'),
			'fieldUpdateError' => __( 'Error updating fields', 'usin' ),
			'keyMessage' => __('Tip: If you would like to use existing custom user meta fields from the
			WordPress users meta table, please make sure to insert the existing meta key into the "Field Key"
			field. ', 'usin')
			
		);

		$options['strings'] = $strings;

		return $options;
	}

}
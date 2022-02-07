<?php

class USIN_Report_Loader{

	public $label_col = 'label';
	public $total_col = 'total';

	public $max_items  = 8;

	public function __construct($report, $options = array()){
		$this->report = $report;
		$this->options = $options;

		$this->setup();

		$this->max_items = apply_filters('usin_max_report_items', $this->max_items, $report);
		
	}

	public function call(){
		$data = $this->load_data();
		
		return $this->format_data($data);
	}

	protected function data_item($name, $result){
		return (object)array($this->label_col => $name, $this->total_col => $result);
	}

	/**
	 * Can be used by child classes to run additional code upon initialization.
	 *
	 */
	protected function setup(){}


	protected function getSelectedFilter(){
		return $this->options['filter'];
	}

}
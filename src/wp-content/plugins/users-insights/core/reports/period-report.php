<?php

class USIN_Period_Report extends USIN_Report {
	
	public $type = self::BAR;
	public $subtype = 'periodic';

	const PERIOD_DAILY = 'daily';
	const PERIOD_WEEKLY = 'weekly';
	const PERIOD_MONTHLY = 'monthly';
	const PERIOD_YEARLY = 'yearly';

	public $periods;


	public function __construct($id, $name, $options = array()){
		parent::__construct($id, $name, $options);

		$this->set_periods();
	}

	protected function set_periods(){
		if(!$this->filters){
			$this->filters = array(
				'options' => $this->get_default_periods(),
				'default' => self::PERIOD_DAILY	
			);
		}
	}


	protected function get_default_periods(){
		return array(
			self::PERIOD_DAILY => __('Daily', 'usin'), 
			self::PERIOD_WEEKLY => __('Weekly', 'usin'),
			self::PERIOD_MONTHLY => __('Monthly', 'usin'),
			self::PERIOD_YEARLY => __('Yearly', 'usin')
		);
	}

}
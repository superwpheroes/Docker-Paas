<?php

abstract class USIN_Module_Reports{

	abstract public function get_group();
	abstract public function get_reports();

	protected $max_cf_reports = 10;
	protected $count = 0;

	public function __construct(){
		$this->max_cf_reports = apply_filters('usin_reports_to_show', $this->max_cf_reports);
		add_filter('usin_report_groups', array($this, 'register_group'));
		add_filter('usin_report_defaults', array($this, 'register_reports'));
	}

	public function register_group($groups){
		$group = $this->get_group();
		if(is_array($groups) && !empty($group)){
			$groups[]= $group;
		}
		return $groups;
	}

	public function register_reports($reports){
		$module_reports = $this->get_reports();

		if(is_array($reports) && !empty($module_reports)){
			$reports = array_merge($reports, $module_reports);
		}

		return $reports;
	}

	protected function get_default_report_visibility(){
		$this->count++;
		return $this->count <= $this->max_cf_reports;
	}

}
<?php

class USIN_BuddyPress_Reports extends USIN_Module_Reports{

	protected $group = 'buddypress';

	public function __construct($bp){
		parent::__construct();
		$this->bp = $bp;
	}

	public function get_group(){
		
		return array(
			'id' => $this->group,
			'name' => 'BuddyPress'
		);
	}

	public function get_reports(){

		$reports = array(
			new USIN_Period_Report('buddypress_active_users', __('Active users', 'usin'), 
				array(
					'group'=>$this->group
				)
			),
		);

		if(USIN_BuddyPress::is_bp_feature_active('groups')){
			$reports[]= new USIN_Standard_Report(
				'buddypress_groups',
			__('Top Groups', 'usin'),
				array(
					'group' => $this->group,
					'type' => USIN_Report::BAR
				)
			);
		}

		if(USIN_BuddyPress::is_bp_feature_active('friends')){
			$reports[]= new USIN_Standard_Report(
				'buddypress_friends',
				__('Number of friends', 'usin'),
				array(
					'group' => $this->group,
					'field_id' => 'total_friend_count',
					'loader_class' => 'USIN_Numeric_Meta_Field_Loader',
					'type' => USIN_Report::BAR
				)
			);
		}

		$fields = $this->bp->xprofile->get_fields();

		if(empty($fields)){
			return $reports;
		}

		
		foreach ($fields as $field) {
			
			switch ($field['filter']['type']) {
				case 'select':
					$reports[]= new USIN_Standard_Report(
						$field['id'], 
						$field['name'], 
						array(
							'group' => $this->group, 
							'visible' => $this->get_default_report_visibility(),
							'field_id' => $field['bpx_id'], 
							'loader_class' => 'USIN_BuddyPress_Field_Loader'
						)
					);
				break;
				case 'multioption_text':
				case 'serialized_multioption':
					$reports[]= new USIN_Standard_Report(
						$field['id'], 
						$field['name'], 
						array(
							'group' => $this->group, 
							'visible' => $this->get_default_report_visibility(),
							'field_id' => $field['bpx_id'], 
							'loader_class' => 'USIN_BuddyPress_Multioption_Loader',
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
							'field_id' => $field['bpx_id'], 
							'loader_class' => 'USIN_BuddyPress_Numeric_Loader',
							'type' => USIN_Report::BAR
						)
					);
				break;
			}
		}

		return $reports;

	}
}
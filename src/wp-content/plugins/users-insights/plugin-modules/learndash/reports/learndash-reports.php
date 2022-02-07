<?php

class USIN_Learndash_Reports extends USIN_Module_Reports{

	protected $group = 'learndash';

	public function __construct($ld){
		parent::__construct();
		$this->ld = $ld;
	}

	public function get_group(){
		
		return array(
			'id' => $this->group,
			'name' => 'LearnDash'
		);
	}

	public function get_reports(){


		$reports = array(
			new USIN_Period_Report('learndash_active_students', __('Active students', 'usin'), 
				array(
					'group'=>'learndash'
				)
			),
			new USIN_Period_Report('learndash_course_enrolments', __('Courses started by students', 'usin'), 
				array(
					'group'=>'learndash'
				)
			),
			new USIN_Standard_Report('learndash_course_students', __('Top courses by student number', 'usin'), 
				array(
					'group'=>'learndash', 
					'type'=>USIN_Report::BAR, 
					'filters' => array(
						'options' => array(
							'all' => __('All statuses', 'usin'),
							'completed' => __('Completed', 'usin'),
							'in_progress' => __('In Progress', 'usin')
						),
						'default' => 'all'
					)
				)
			),
		);

		$groups = USIN_LearnDash::get_items(USIN_LearnDash::GROUP_POST_TYPE);
		
		if(sizeof($groups) > 0){
			$reports[]= new USIN_Standard_Report('learndash_groups', __('Top groups by student number', 'usin'), 
					array(
						'group'=>'learndash',
						'type' => USIN_Report::BAR
					)
				);
		}

		$quizzes = USIN_LearnDash::get_items(USIN_LearnDash::QUIZ_POST_TYPE, true);

		if(sizeof($quizzes) > 0){
			$quizzes['all'] = __('All quizzes', 'usin');

			$reports[]= new USIN_Standard_Report('learndash_quiz_attempts', __('Quiz attempts', 'usin'), 
					array(
						'group'=>'learndash',
						'filters' => array(
							'default' => 'all',
							'options' => $quizzes
						)
					)
				);


			$reports[]= new USIN_Standard_Report('learndash_quiz_score', __('Quiz score', 'usin'), 
				array(
					'group'=>'learndash',
					'type' => USIN_Report::BAR,
					'filters' => array(
						'default' => 'all',
						'options' => $quizzes
					)
				)
			);
		}
		

		return $reports;

	}
}
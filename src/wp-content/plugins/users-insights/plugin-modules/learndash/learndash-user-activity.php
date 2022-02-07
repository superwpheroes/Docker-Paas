<?php

/**
 * Adds the LearnDash user activity to the user profile section
 */
class USIN_LearnDash_User_Activity{
	
	protected $module_name;

	public function __construct($module_name){
		$this->module_name = $module_name;
		$this->init();
	}

	public function init(){
		add_filter('usin_user_activity', array($this, 'add_leardash_activity'), 10, 2);
	}
	
	public function add_leardash_activity($activity, $user_id){

		$ld_activities = array(
			$this->get_course_activity($user_id),
			$this->get_course_access_list($user_id),
			$this->get_quiz_activity($user_id),
			$this->get_group_activity($user_id)
		);

		foreach ($ld_activities as $ld_activity) {
			if(!empty($ld_activity)){
				$activity[]= $ld_activity;
			}
		}
	
		return $activity;
	}
	
	
	/**
	 * Adds the course activity with progress info
	 * @param  int $user_id the ID of the user
	 * @return array          The original user activity including the course activity
	 */
	protected function get_course_activity($user_id){
		$courses = $this->get_activity_courses($user_id);
		if(empty($courses)){
			return null; 
		}
			
		$list = array();
		foreach ($courses as $course ) {
			$list[]= array(
				'title' => $course->post_title . $this->get_course_progress_tag($course->ID, $user_id),
				'link' => get_permalink( $course->ID )
			);
		}

		return array(
			'type' => 'ld_courses',
			'label' => __('Course Activity', 'usin'),
			'list' => $list,
			'icon' => $this->module_name
		);
	}

	/**
	 * Returns the HTML markup of a course progress percentage.
	 */
	protected function get_course_progress_tag($course_id, $user_id){
		$result = '';
		if(!function_exists('learndash_course_progress')){
			return $result;
		}

		$progress = learndash_course_progress( array(
			'user_id'   => $user_id,
			'course_id' => $course_id,
			'array'     => true
		) );
		
		if(is_array($progress) && isset($progress['percentage'])){
			$result = USIN_Html::progress_tag($progress['percentage']);
		}

		return $result;
	}

	protected function get_course_access_list($user_id){

		if(!function_exists('ld_get_mycourses')){ 
			return null;
		}

		$courses = $this->get_courses(ld_get_mycourses($user_id));
		if(empty($courses)){ 
			return null;
		}
		
		foreach ($courses as $course ) {
			$list[]= array(
				'title' => $course->post_title,
				'link' => get_permalink( $course->ID )
			);
		}
		
		return array(
			'type' => 'ld_course_access',
			'label' => __('Course Access', 'usin'),
			'list' => $list,
			'icon' => $this->module_name
		);
	}

	protected function get_courses($ids){
		if(empty($ids) || !is_array($ids)){
			return array();
		}

		return get_posts( array(
			'posts_per_page' => -1,
			'post_type' => USIN_LearnDash::COURSE_POST_TYPE, 
			'post_status' => 'any',
			'include' => $ids
		));
	}

	protected function get_activity_courses($user_id){
		global $wpdb;

		$activity_table = $wpdb->prefix.'learndash_user_activity';

		$ids = $wpdb->get_col($wpdb->prepare(
			"SELECT DISTINCT(post_id) FROM $activity_table WHERE activity_type = 'course' AND user_id = %d",
			$user_id 
		));

		if(empty($ids)){
			return array();
		}
		return $this->get_courses(array_map('intval', $ids));

	}
	
	/**
	 * Adds the quiz activity with passed percentage info
	 * @param  int $user_id the ID of the user
	 * @return array          The original user activity including the quiz activity
	 */
	protected function get_quiz_activity($user_id){
		$activity = array();
		
		$quiz_attempts = get_user_meta( $user_id, '_sfwd-quizzes', true );
		
		if(!empty($quiz_attempts) && is_array($quiz_attempts)){
			$count = sizeof($quiz_attempts);
			$list = array();
			
			foreach ($quiz_attempts as $quiz_attempt ) {
				$quiz = get_post( $quiz_attempt['quiz'] );
				$title = $quiz->post_title;
				
				$percentage = ! empty( $quiz_attempt['percentage'] ) ? $quiz_attempt['percentage'] : ( ! empty( $quiz_attempt['count'] ) ? $quiz_attempt['score'] * 100 / $quiz_attempt['count'] : 0 );
				
				$title .= USIN_Html::progress_tag($percentage);
				
				
				$quiz_info = array(
					'title' => $title,
					'link' => get_edit_post_link( $quiz->ID, 'usin' )
				);
				
				$list[]=$quiz_info;
			}
			
			$activity = array(
				'type' => 'ld_quizes',
				'for' => 'ld_quizes',
				'label' => sprintf(_n('%s Attempt', '%s Attempts', $count, 'usin'), USIN_LearnDash::get_label('quiz')),
				'count' => $count,
				'list' => $list,
				'icon' => $this->module_name
			);
		}
		
		return $activity;
	}
	
	/**
	 * Adds the quiz activity with passed percentage info
	 * @param  int $user_id the ID of the user
	 * @return array          The original user activity including the quiz activity
	 */
	protected function get_group_activity($user_id){
		$activity = array();
		
		if(function_exists('learndash_get_users_group_ids')){
		
			$groups = learndash_get_users_group_ids($user_id);
			
			if(!empty($groups) && is_array($groups)){
				$count = sizeof($groups);
				$list = array();
				
				foreach ($groups as $group ) {
					$group = get_post( intval($group) );
					
					$group_info = array(
						'title' => $group->post_title,
						'link' => get_edit_post_link( $group->ID, 'usin' )
					);
					
					$list[]=$group_info;
				}
				
				$activity = array(
					'type' => 'ld_groups',
					'for' => 'ld_groups',
					'label' => sprintf(_n('Belongs to 1 Group', 'Belongs to %d Groups', $count, 'usin'), $count),
					'count' => $count,
					'hide_count' => true,
					'list' => $list,
					'icon' => $this->module_name
				);
			}
			
		}
		
		return $activity;
	}

	
}
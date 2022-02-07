<?php

/**
 * Retrieves the general user activity, such as posts created and comments posted
 */
class USIN_User_Activity{

	const MAX_ITEMS_TO_LOAD = 5;
	protected $user_id;

	public function __construct($user_id){
		$this->user_id = $user_id;
	}

	public function get(){
		$activity = array();

		$exclude_post_types = array('revision', 'attachment', 'nav_menu_item');
		$allowed_post_types = USIN_Helper::get_allowed_post_types();

		foreach ($allowed_post_types as $post_type_name) {
			$post_type = get_post_type_object( $post_type_name );
			if(!in_array($post_type, $exclude_post_types)){

				$post_activity = $this->get_post_activity($post_type);
				if(!empty($post_activity)){
					$activity[]=$post_activity;
				}

				$comment_activity = $this->get_comment_activity($post_type);
				if(!empty($comment_activity)){
					$activity[]=$comment_activity;
				}
			}
		}

		return apply_filters('usin_user_activity', $activity, $this->user_id);
	}

	protected function get_post_activity($post_type){
		$args = array(
			'author'=>$this->user_id, 
			'post_type'=>$post_type->name, 
			'posts_per_page'=>self::MAX_ITEMS_TO_LOAD, 
			'orderby'=>'date', 
			'order'=>'desc', 
			'post_status'=> USIN_Helper::get_allowed_post_statuses()
		);

		$suppress_filters = apply_filters("usin_suppress_filters_$post_type->name", false);
		if($suppress_filters){
			$args['suppress_filters'] = true;
		}

		$query = new WP_Query($args);

		$count = $query->found_posts;
		if($count){
			
			$list = array();

			foreach ($query->posts as $post) {
				$post_title = $post->post_title;
				if($post->post_status != 'publish'){
					$status = get_post_status_object($post->post_status);
					if(isset($status->label)){
						$post_title .= " ($status->label)";
					}
				}
				$list[]=array('title'=>$post_title, 'link'=>get_permalink($post->ID));
			}

			return array(
				'type' => 'post_type_'.$post_type->name,
				'label' => $count == 1 ? $post_type->labels->singular_name : $post_type->labels->name,
				'count' => $count,
				'link' => admin_url('edit.php?post_type='.$post_type->name.'&usin_user='.$this->user_id.'&usin_post_type='.$post_type->name),
				'list' => $list
			);

		}
	}


	protected function get_comment_activity($post_type){
		$count = get_comments(array('user_id'=>$this->user_id, 'post_type'=>$post_type->name, 'count'=>true));

		if($count){
			$label = $post_type->labels->singular_name.' ';
			$label .= $count == 1 ? __('Comment', 'usin') : __('Comments' , 'usin');

			$list = array();

			$com_args = array(
				'user_id'=>$this->user_id,
				'post_type'=>$post_type->name,
				'number'=>self::MAX_ITEMS_TO_LOAD,
				'orderby'=>'date',
				'order'=>'DESC'
			);

			$exclude_comment_types = USIN_Helper::get_exclude_comment_types();
			if(!empty($exclude_comment_types)){
				$com_args['type__not_in'] = $exclude_comment_types;
			}
			
			$comments = get_comments($com_args);
			foreach ($comments as $comment) {
				$content = wp_html_excerpt( $comment->comment_content, 40, ' [...]');
				$list[]=array('title'=>$content, 'link'=>get_permalink($comment->comment_post_ID));
			}

			return array(
				'type' => 'comment_'.$post_type->name,
				'for' => $post_type->name,
				'label' =>  $label,
				'count' => $count,
				'link' => admin_url('edit-comments.php?usin_user='.$this->user_id.'&usin_post_type='.$post_type->name),
				'list' => $list
			);
		}
	}



}
<?php


class USIN_Filters{

	public static function init(){
		add_action('pre_get_comments', array('USIN_Filters', 'admin_comments_filter'));
		add_action('pre_get_posts', array('USIN_Filters', 'admin_posts_filter'));
	}

	public static function admin_comments_filter($args){
		if( is_admin() && isset($_GET['usin_user']) && isset($_GET['usin_post_type'])) {
			$user_id = intval($_GET['usin_user']);
			if($user_id){
				 $args->query_vars['user_id'] = $user_id;

				 $args->query_vars['post_type'] = $_GET['usin_post_type'];
			}
		}

		return $args;
	}

	public static function admin_posts_filter($query){
		if( is_admin() && isset($_GET['usin_user']) && isset($_GET['usin_post_type'])
			 && $query->get('post_type') == $_GET['usin_post_type']){
			$user_id = intval($_GET['usin_user']);
			if($user_id){
				$query->set('author', $user_id);
			}
		}
	}
}



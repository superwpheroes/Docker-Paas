<?php

function usin_modules(){
	return USIN_Modules::get_instance();
}

function usin_manager(){
	return USIN_Manager::get_instance();
}

function usin_options(){
	return usin_manager()->options;
}

function usin_get_module_setting($module_id, $setting_id){
	$modules = usin_modules();
	return $modules->get_setting($module_id, $setting_id);
}

function usin_is_a_users_insights_page(){
	if (!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)){
		return false;
	}

	if(!function_exists('get_current_screen')){
		//get_current_screen() is declared at a later point
		return false;
	}

	$current_screen = get_current_screen();
	if(!$current_screen || !isset($current_screen->base)){
		return false;
	}
	
	$manager = usin_manager();
	$ui_pages = array('list_page', 'module_page', 'cf_page');

	foreach($ui_pages as $page){
		if(!empty($manager->$page) && !empty($manager->$page->slug)){
			if(strpos( $current_screen->base, $manager->$page->slug ) !== false){
				return true;
			}
		}
	}
	return false;
}

function usin_register_activity_data_callback($type, $callback){
	return USIN_List_Ajax::register_activity_data_callback($type, $callback);
}

/**
 * @deprecated 3.6.3
 * @deprecated No longer used by internal code and not recommended.
 */
function usin_module_options(){
	return usin_modules();
}
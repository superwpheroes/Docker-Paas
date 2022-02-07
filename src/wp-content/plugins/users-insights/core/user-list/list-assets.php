<?php

class USIN_List_Assets extends USIN_Assets{

	protected $has_ui_select = true;
	protected $js_options_filter = 'usin_user_list_options';

	protected function register_custom_assets(){
		$this->js_assets['usin_maps'] = array('path' => 'js/lib/leaflet/leaflet.js');
		$this->js_assets['usin_marker_clusterer'] = array('path' => 'js/lib/leaflet-marker-clusterer/leaflet.markercluster.js',
			'deps' => array('usin_maps'));

		$deps = array_merge($this->base_js_assets, array('usin_drag_drop'));
		$this->js_assets['usin_user_list'] = array('path' => 'js/user-list.min.js', 'deps' => $deps);
		$this->js_assets['usin_templates'] = array('path' => 'views/user-list/templates.js',
			'deps' => array('usin_user_list'));
		
		$this->css_assets['usin_leaflet_css'] = array('path' => 'js/lib/leaflet/leaflet.css');
		$this->css_assets['usin_marker_clusterer_css_default'] = array('path' => 'js/lib/leaflet-marker-clusterer/MarkerCluster.Default.css');
	}

	
	public function enqueue_assets(){
		$main_js_deps = array();

		$this->enqueue_base_assets();

		$this->enqueue_scripts(array('usin_ng_route', 'usin_drag_drop'));
			
		if(usin_modules()->is_module_active('geolocation')){

			$this->enqueue_scripts(array('usin_maps', 'usin_marker_clusterer'));
			$this->enqueue_styles(array('usin_leaflet_css', 'usin_marker_clusterer_css_default'));
			
			$main_js_deps[]= 'usin_maps';
		}

		$this->enqueue_script('usin_user_list', $main_js_deps);
		$this->enqueue_script('usin_templates');
	}
	
	protected function get_js_options(){
		$usin_options = usin_options();

		$options = array(
			'viewsURL' => 'views/user-list',
			'usersPerPage' => intval($usin_options->get('users_per_page', 50)),
			'orderBy' => $usin_options->get('orderby', 'registered'),
			'order' => $usin_options->get('order', 'DESC'),
			'fields' => $usin_options->set_icons($usin_options->get_ordered_fields()),
			'unorderedFields' => $usin_options->get_fields(),
			'editableFields' => $usin_options->get_editable_fields(),
			'profileSettings' => $usin_options->get('profile_settings'),
			'nonce' => $this->page->ajax_nonce,
			'months' => USIN_Helper::get_months(),
			'filterOperators' => $usin_options->get_filter_operators(),
			'optionFieldTypes' => $usin_options->get_field_types_by_type('option'),
			'textFieldTypes' => $usin_options->get_field_types_by_type('text'),
			'dateFieldTypes' => $usin_options->get_field_types_by_type('date'),
			'geolocationActive' => usin_modules()->is_module_active('geolocation'),
			'groups' => USIN_GROUPS::get_all_groups(),
			'segments' => USIN_Segments::get(),
			'customTemplates' => array(),
			'canUpdateUsers' => current_user_can(USIN_Capabilities::UPDATE_USERS),
			'canExportUsers' => current_user_can(USIN_Capabilities::EXPORT_USERS),
			'canManageSegments' => current_user_can(USIN_Capabilities::MANAGE_SEGMENTS),
			'is_ssl' => is_ssl(),
			'pageOptions' => array(10, 20, 50)
		);

		$error_tip = sprintf('%s (<a href="https://usersinsights.com/troubleshooting-user-table-loading/?ref=dash" target="_blank">%s</a>)',
			__('Tip: Try to hide all the columns from the eye-icon menu and refresh the page', 'usin'),
			__('more info', 'usin'));

		$strings = array(
			'daysAgo' => __('days ago', 'usin'),
			'day' => __('day', 'usin'),
			'month' => __('month', 'usin'),
			'year' => __('year', 'usin'),
			'loadMore' => __('Load More', 'usin'),
			'errorTip' => $error_tip,
			'addFilter' => __('Add Filter', 'usin'),
			'title' => $this->page->title,
			'activity' => __('Activity', 'usin'),
			'noActivity' => __('No activity found', 'usin'),
			'back' => __('Back to user list', 'usin'),
			'of' => __('of', 'usin'),
			'usersPerPage' => __('Users per page', 'usin'),
			'users' => __('users', 'usin'),
			'mapUsersDetected' => __('user locations detected', 'usin'),
			'online' => __('online', 'usin'),
			'export' => __('Export this list of %d users', 'usin'),
			'exportAction' => __('Export', 'usin'),
			'confirmExport' => __('Are you sure that you want to export the current list of <span class="usin-dialog-highlight">%s</span> users?'),
			'exportError' => __('Error exporting data', 'usin'),
			'groups' => __('User Groups', 'usin'),
			'groupUpdateError' => __('Error updating user groups', 'usin'),
			'notes' => __('Notes', 'usin'),
			'addNote' => __('Add Note', 'usin'),
			'by' => __('by', 'usin'),
			'noteError' => __('Error updating notes list', 'usin'),
			'fieldUpdateError' => __( 'Error updating fields', 'usin' ),
			'toggleColumns' => __('Toggle Columns', 'usin'),
			'enterMapView' => __('Enter Map View', 'usin'),
			'exitMapView' => __('Exit Map View', 'usin'),
			'usersSelected' => __('%d Users Selected', 'usin'),
			'userSelected' => __('1 User Selected', 'usin'),
			'bulkActions' => __('Bulk Actions', 'usin'),
			'segments' => __('Segments', 'usin'),
			'saveSegmentTooltip' => __('Save the current filters as a segment', 'usin'),
			'disabledSegmentTooltip' => __('Apply filters to create a segment', 'usin'),
			'newSegment' => __('Create new segment', 'usin'),
			'saveSegment' => __('Save segment', 'usin'),
			'deleteSegment' => __('Delete segment', 'usin'),
			'segmentName' => __('Segment name', 'usin'),
			'confirmDeleteSegment' => __('Are you sure that you want to delete the segment <span class="usin-dialog-highlight">%s</span>?'),
			'createSegmentError' => __( 'Error creating the segment', 'usin' ),
			'fieldNotExist' => __( 'This field does not exist anymore', 'usin' ),
			'addGroup' => __('Add to group', 'usin'),
			'addUserGroupInfo' => __('Add the selected user to the following group', 'usin'),
			'addUsersGroupInfo' => __('Add the selected %d users to the following group', 'usin'),
			'removeGroup' => __('Remove from group', 'usin'),
			'removeUserGroupInfo' => __('Remove the selected user from the following group', 'usin'),
			'removeUsersGroupInfo' => __('Remove the selected %d users from the following group', 'usin'),
			'selectAllUsers' => __('Select all users'),
			'clearSelection' => __('Clear Selection'),
			'selectGroup' => __('Select a group', 'usin'),
			'showDebugInfo' => __('Show debug info', 'usin'),
			'hideDebugInfo' => __('Hide debug info', 'usin'),
			'noGroups' => __('There are no user groups created. Go to Users Insights -> User Groups to create a new group.', 'usin'),
			'clearSelection' => __('Clear selection', 'usin'),
			'clearAll' => __('Clear all', 'usin'),
			'viewAll' => __('View all', 'usin'),
			'addSeparator' => __('Add section title', 'usin'),
			'hiddenItems' => __('more hidden items', 'usin'),
			'profileSettings' => __('Profile settings', 'usin'),
			'profileHiddenItemsInstructions' => __('Click on the Profile Settings button above to toggle field visibility')
		);

		$options['strings'] = $strings;
		return $options;
	}
}
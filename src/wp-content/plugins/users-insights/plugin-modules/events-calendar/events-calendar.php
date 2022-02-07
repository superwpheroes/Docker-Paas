<?php

if(!defined( 'ABSPATH' )){
	exit;
}

class USIN_Events_Calendar extends USIN_Plugin_Module{

	const EVENT_POST_TYPE = 'tribe_events';
	const ET_POST_TYPE = 'tribe_rsvp_attendees';
	public static $et_post_types = array('tribe_wooticket', 'tribe_eddticket', 'tribe_tpp_attendees'); //PHP 5.2 doesn't support arrays in class constants, so we can't use a constant here
	
	protected $module_name = 'events-calendar';
	protected $plugin_path = array(
		'events_calendar' => 'the-events-calendar/the-events-calendar.php',
		'tickets' => 'event-tickets/event-tickets.php', 
		'tickets_plus' => 'event-tickets-plus/event-tickets-plus.php'
	);
	protected $event_tickets_active = false;
	protected $event_tickets_plus_active = false;
	protected $tribe_commerce_enabled = false;
	protected $ticket_orders_enabled = false;

	public function init(){
		$this->set_active_plugins();

		add_filter('usin_exclude_post_types', array($this , 'exclude_post_types'));
		add_filter('usin_field_types', array($this , 'register_field_type'));

		$this->event_search = new USIN_Post_Option_Search(self::EVENT_POST_TYPE);

		if($this->event_tickets_active){
			$et_query = new USIN_Events_Calendar_Tickets_Query();
			$et_query->init();

			$et_user_activity = new USIN_Events_Calendar_Tickets_User_Activity();
			$et_user_activity->init();
		}

		if($this->ticket_orders_enabled){

			$eto_query = new USIN_Events_Calendar_Ticket_Orders_Query();
			$eto_query->init();

			$eto_user_activity = new USIN_Events_Calendar_Ticket_Orders_User_Activity();
			$eto_user_activity->init();
		}

		//events created field
		new USIN_Post_Query(USIN_Events_Calendar::EVENT_POST_TYPE);

		add_filter('usin_suppress_filters_'.self::EVENT_POST_TYPE, '__return_true');
		add_filter('usin_user_activity', array($this, 'change_events_created_label'), 10, 2);
	}

	protected function set_active_plugins(){
		$this->event_tickets_active = USIN_Helper::is_plugin_activated($this->plugin_path['tickets']);
		$this->event_tickets_plus_active = USIN_Helper::is_plugin_activated($this->plugin_path['tickets_plus']);

		if($this->event_tickets_active && function_exists('tribe_get_option')){
			$this->tribe_commerce_enabled = tribe_get_option( 'ticket-paypal-enable', false );
		}

		$this->ticket_orders_enabled = $this->event_tickets_plus_active || $this->tribe_commerce_enabled;
	}


	public function register_module(){
		return array(
			'id' => $this->module_name,
			'name' => 'Events Calendar',
			'desc' => __('Detects the user data from the Events Calendar Events Tickets, Events Tickets Plus and Community Events extensions.', 'usin'),
			'allow_deactivate' => true,
			'buttons' => array(
				array('text'=> __('Learn More', 'usin'), 'link'=>'https://usersinsights.com/events-calendar-tickets-search-filter', 'target'=>'_blank')
			),
			'active' => false
		);
	}

	public function register_fields(){
		$fields = array();

		$event_options = $this->event_search->get_options();
		$search_action = $this->event_search->get_search_action();

		if($this->event_tickets_active){
			
			$fields[]=array(
				'name' => __('RSVPs yes', 'usin'),
				'id' => 'rsvp_yes',
				'order' => 'DESC',
				'show' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'number',
					'disallow_null' => true
				),
				'module' => $this->module_name
			);

			$fields[]=array(
				'name' => __('RSVPs no', 'usin'),
				'id' => 'rsvp_no',
				'order' => 'DESC',
				'show' => false,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'number',
					'disallow_null' => true
				),
				'module' => $this->module_name
			);

			$fields[]=array(
				'name' => __("Has RSVP'd", 'usin'),
				'id' => 'has_rsvped',
				'hideOnTable' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'ec_rsvp',
					'options' => $event_options,
					'searchAction' => $search_action
				),
				'module' => $this->module_name
			);
		}

		if($this->ticket_orders_enabled){
			
			$fields[]=array(
				'name' => __('Tickets ordered', 'usin'),
				'id' => 'ec_tickets',
				'order' => 'DESC',
				'show' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'number',
					'disallow_null' => true
				),
				'module' => $this->module_name
			);

			$fields[]=array(
				'name' => __('Has ordered ticket for event', 'usin'),
				'id' => 'ec_has_ordered_ticket',
				'show' => false,
				'hideOnTable' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'select_option',
					'options' => $event_options,
					'searchAction' => $search_action
				),
				'module' => $this->module_name
			);

			$fields[]=array(
				'name' => __('Has not ordered ticket for event', 'usin'),
				'id' => 'ec_has_not_ordered_ticket',
				'show' => false,
				'hideOnTable' => true,
				'fieldType' => $this->module_name,
				'filter' => array(
					'type' => 'select_option',
					'options' => $event_options,
					'searchAction' => $search_action
				),
				'module' => $this->module_name
			);
		}

		$fields[]=array(
			'name' => __('Events created', 'usin'),
			'id' => self::EVENT_POST_TYPE.'_count',
			'order' => 'DESC',
			'show' => true,
			'fieldType' => $this->module_name,
			'filter' => array(
				'type' => 'number',
				'disallow_null' => true
			),
			'module' => $this->module_name
		);

		return $fields;
	}


	public function exclude_post_types($exclude){
		$exclude = array_merge($exclude, array('tribe_organizer', 'tribe_venue'));

		if($this->event_tickets_active){
			$exclude[]=self::ET_POST_TYPE;
		}

		if($this->ticket_orders_enabled){
			$exclude = array_merge ($exclude,  self::$et_post_types);
		}

		return $exclude;
	}

	public function register_field_type($field_types){
		if($this->event_tickets_active){
			$field_types['ec_rsvp'] = array(
				'operators' => array(
					array('key' => 'ec_is_yes' , 'val' => __('yes for', 'usin')),
					array('key' => 'ec_is_no' , 'val' => __('no for', 'usin')),
					array('key' => 'ec_is_any' , 'val' => __('any for', 'usin')),
					array('key' => 'ec_is_none' , 'val' => __('none for', 'usin'))
				),
				'type' => 'option'
			);
		}

		return $field_types;
	}

	/**
	 * Change the label of "Events" to "Events created" in the user profile
	 * section, so it is more clear
	 */
	public function change_events_created_label($activity, $user_id){

		foreach ($activity as &$item ) {
			if(isset($item['type']) && $item['type'] == self::EVENT_POST_TYPE){
				$item['label'] = _n('Event Created', 'Events Created', $item['count'], 'usin');
				$item['icon'] = $this->module_name;
			}
		}

		return $activity;
	}

}

new USIN_Events_Calendar();
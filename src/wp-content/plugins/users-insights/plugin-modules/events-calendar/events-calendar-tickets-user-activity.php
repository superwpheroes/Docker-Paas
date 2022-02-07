<?php

class USIN_Events_Calendar_Tickets_User_Activity{
	

	/**
	 * Registers the required filter and action hooks.
	 */
	public function init(){
		add_filter('usin_user_activity', array($this, 'add_rsvps_to_activity'), 10, 2);
	}
	

	public function add_rsvps_to_activity($activity, $user_id){
			$user_rsvps = $this->get_rsvps($user_id);
			$rsvps = $user_rsvps['rsvps'];
			$count = $user_rsvps['count'];
			
			if(!empty($rsvps)){
				$list = array();

				foreach ($rsvps as $rsvp ) {
					$title = sprintf('%s %s %s', $rsvp['event_name'], 
						_x('on', 'date preposition', 'usin'), USIN_Helper::format_date($rsvp['event_date']));

					if($rsvp['count']>1){
						$title .= sprintf(' <span class="usin-rsvp-count">(x%d)</span>', $rsvp['count']);
					}
					
					$title .= USIN_Html::tag($rsvp['status'], $rsvp['status']);

					$list[]=array(
						'title' => $title,
						'link' => $rsvp['event_link']
					);
				}
				
				$activity[] = array(
					'type' => 'rsvps',
					'for' => USIN_Events_Calendar::ET_POST_TYPE,
					'label' => _n('RSVP', 'RSVPs', $count, 'usin'),
					'count' => $count,
					'list' => $list,
					'icon' => 'events-calendar'
				);
			}
		
		return $activity;
	}

	protected function get_rsvps($user_id){
		$rsvps = array();
		$rsvp_posts = get_posts(array(
			'post_type' => USIN_Events_Calendar::ET_POST_TYPE,
			'posts_per_page' => -1,
			'offset' => 0,
			'author' => $user_id
		));

		foreach ($rsvp_posts as $p) {
			$event_id = get_post_meta($p->ID, '_tribe_rsvp_event', true);
			$status = get_post_meta($p->ID, '_tribe_rsvp_status', true);
			$key = $event_id.$status;

			if(!isset($rsvps[$key])){
				$event = get_post(intval($event_id));

				$link = add_query_arg(array(
					'post_type'=>USIN_Events_Calendar::EVENT_POST_TYPE, 
					'page' => 'tickets-attendees',
					'event_id' => $event->ID
				), admin_url('edit.php'));

				$rsvps[$key] = array(
					'event_name' => $event->post_title,
					'event_link' => $link,
					'event_date' => get_post_meta($event->ID, '_EventStartDate', true),
					'count' => 1,
					'status' => $status
				);
			}else{
				$rsvps[$key]['count'] += 1;
			}
		}

		return array('rsvps' => $rsvps, 'count' => sizeof($rsvp_posts));
	}
	
	
}
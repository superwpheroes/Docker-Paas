<?php

class USIN_Events_Calendar_Ticket_Orders_User_Activity{
	
	public function init(){
		add_filter('usin_user_activity', array($this, 'add_tickets_to_activity'), 10, 2);
	}
	
	public function add_tickets_to_activity($activity, $user_id){
			$user_tickets = $this->get_tickets($user_id);
			$tickets = $user_tickets['tickets'];
			$count = $user_tickets['count'];
			
			if(!empty($tickets)){
				$list = array();

				foreach ($tickets as $ticket ) {
					$title = sprintf('%s %s %s', $ticket['event_name'], 
						_x('on', 'date preposition', 'usin'), USIN_Helper::format_date($ticket['event_date']));

					if($ticket['count']>1){
						$title .= sprintf(' <span class="usin-rsvp-count">(x%d)</span>', $ticket['count']);
					}
					
					if($ticket['status']){
						$title .= USIN_Html::tag($ticket['status']['name'], $ticket['status']['key']);
					}

					$list[]=array(
						'title' => $title,
						'link' => $ticket['event_link']
					);
				}
				
				$activity[] = array(
					'type' => 'tickets',
					'for' => USIN_Events_Calendar::ET_POST_TYPE,
					'label' => _n('Ticket Ordered', 'Tickets Ordered', $count, 'usin'),
					'count' => $count,
					'list' => $list,
					'icon' => 'events-calendar'
				);
			}
		
		return $activity;
	}

	protected function get_tickets($user_id){
		$tickets = array();

		$query = new WP_Query(array(
			'post_type' => USIN_Events_Calendar::$et_post_types,
			'posts_per_page' => -1,
			'offset' => 0,
			'meta_query' => array(
				array(
					'key'     => '_tribe_tickets_attendee_user_id',
					'value'   => $user_id,
					'compare' => '=',
				)
			)
		));

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$p=$query->post;
				
				$event_id = $this->get_event_id($p);
				$order = $this->get_order($p);

				$order_id = empty($order) ? '' : $order->ID;
				$key = $event_id.$order_id;

				if(!isset($tickets[$key])){
					$event = get_post(intval($event_id));

					$tickets[$key] = array(
						'event_name' => $event->post_title,
						'event_link' => $this->get_order_list_link($p, $event),
						'event_date' => get_post_meta($event->ID, '_EventStartDate', true),
						'count' => 1,
						'status' => $this->get_status_info($order)
					);
				}else{
					$tickets[$key]['count'] += 1;
				}
			}
			wp_reset_postdata();
		}

		return array('tickets' => $tickets, 'count' => $query->post_count);
	}

	protected function get_event_id($ticket){
		if($ticket->post_type == 'tribe_tpp_attendees'){
			return get_post_meta($ticket->ID, '_tribe_tpp_event', true);
		}else{
			$prefix = '_'.$ticket->post_type;
			return get_post_meta($ticket->ID, $prefix.'_event', true);
		}
	}

	protected function get_order($ticket){
		
		if($ticket->post_type == 'tribe_tpp_attendees'){
			//Event tickets ticket
			$order_name = get_post_meta($ticket->ID, '_tribe_tpp_order', true);
			$order_id = $this->get_order_id_by_name($order_name);
		}else{
			//Event Tickets Plusg ticket
			$prefix = '_'.$ticket->post_type;
			$order_id = get_post_meta($ticket->ID, $prefix.'_order', true);
		}

		if($order_id){
			return get_post(intval($order_id));;
		}
	}

	protected function get_order_list_link($ticket, $event){
		if($ticket->post_type == 'tribe_tpp_attendees'){
			$page = 'tpp-orders';
			$event_key = 'post_id';
		}else{
			$page = 'tickets-orders';
			$event_key = 'event_id';
		}

		return add_query_arg(array(
			'post_type'=>USIN_Events_Calendar::EVENT_POST_TYPE, 
			'page' => $page,
			$event_key => $event->ID
		), admin_url('edit.php'));
	}

	protected function get_order_id_by_name($name){
		global $wpdb;
		$res = $wpdb->get_var( $wpdb->prepare( 
			"SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type='tribe_tpp_orders'", $name 
		));
		return $res;
	}


	protected function get_status_info($order){
		if(!$order){
			return null;
		}

		$status_name = $order->post_status;
		$status_key = $order->post_status;

		if($order->post_type == 'shop_order'){
			//woocoommerce
			if(class_exists('WC_Order')){
				
				$wc_order = new WC_Order($order->ID);
				if(method_exists($wc_order, 'get_status') && function_exists('wc_get_order_status_name')){
					$status_key = $wc_order->get_status();
					
					$status_name = wc_get_order_status_name($status_key);
				}
			}
		}elseif($order->post_type == 'edd_payment'){
			//edd
			if(function_exists('edd_get_payment_status')){
				$status_name = edd_get_payment_status($order, true);
				$status_key = sanitize_key($status_name);
				
			}
		}

		return array('name' => $status_name, 'key' => $status_key);

	}
	
	
}
<?php

class USIN_WC_Subscriptions_User_Activity{

	protected $post_type;

	public function __construct($post_type){
		$this->post_type = $post_type;
		$this->init();
	}

	public function init(){
		add_filter('usin_user_activity', array($this, 'add_subscriptions_to_user_activity'), 10, 2);
		add_action('pre_get_posts', array($this, 'admin_subscriptions_filter'));
	}
	
	public function add_subscriptions_to_user_activity($activity, $user_id){

		$args = array(
			'meta_key'    => '_customer_user',
			'meta_value'  => $user_id,
			'post_type'   => $this->post_type,
			'post_status' => 'any',
			'numberposts'=>-1
		);

		$all_subscriptions = get_posts($args);
		$count = sizeof($all_subscriptions);

		$args['numberposts'] = 5;
		$subscriptions = get_posts($args);


		if(!empty($subscriptions)){
			$list = array();
			foreach ($subscriptions as $subscription) {
				$title = "#$subscription->ID";
				$details = array();
				
				if(class_exists('WC_Subscription')){
					$wc_subscription = new WC_Subscription($subscription->ID);
					
					//get the date
					if(method_exists($wc_subscription, 'get_date')){
						$title .= ' '.USIN_Helper::format_date($wc_subscription->get_date('date_created'));
					}
					
					//get the status
					if(method_exists($wc_subscription, 'get_status') && function_exists('wcs_get_subscription_status_name')){
						$status = $wc_subscription->get_status();
						$title .= USIN_Html::tag(wcs_get_subscription_status_name($status), $status);
					}

					
					//get the items
					if(method_exists($wc_subscription, 'get_items')){
						$subscription_items = $wc_subscription->get_items();

						if(!empty($subscription_items) && is_array($subscription_items)){
							foreach ($subscription_items as $item ) {
								if(is_array($item) && isset($item['name'])){
									$details[]=$item['name'];
								}elseif(method_exists($item, 'get_name')){
									$details[]=$item->get_name();
								}
							}
						}
					}

					//get start date, end date and next payment date
					if(method_exists($wc_subscription, 'get_date')){
						$start_date = $wc_subscription->get_date('start');
						if($start_date){
							$details[]= USIN_Html::activity_label( __('Start date', 'usin'), USIN_Helper::format_date( $start_date ));
						}

						$end_date = $wc_subscription->get_date('end');
						if($end_date){
							$details[]= USIN_Html::activity_label( __('End date', 'usin'), USIN_Helper::format_date( $end_date ));
						}

						$next_payment_date = $wc_subscription->get_date('next_payment');
						//use post_status instead of class method for constistency with the main table query and also
						//to avoid relying to another class method that might change in the future
						if($next_payment_date && $subscription->post_status == 'wc-active'){
							$details[]= USIN_Html::activity_label( __('Next payment', 'usin'), USIN_Helper::format_date( $next_payment_date ));
						}

					}

					if(method_exists($wc_subscription, 'get_related_orders')){
						$order_num = sizeof($wc_subscription->get_related_orders());
						if($order_num>0){
							$orders_url = admin_url( 'edit.php?post_status=all&post_type=shop_order&_subscription_related_orders=' . absint( $subscription->ID ) );
							$details[]= sprintf('<a href="%s" target="_blank">%d %s</a>', $orders_url, $order_num, _n('related order', 'related orders', $order_num));
						}
					}
					
				}
				
				$subscription_info = array('title'=>$title, 'link'=>get_edit_post_link( $subscription->ID, ''));
				if(!empty($details)){
					$subscription_info['details'] = $details;
				}
				
				$list[]=$subscription_info;
			}

			$post_type_data = get_post_type_object($this->post_type);

			$activity[] = array(
				'type' => 'wc_subscriptions',
				'for' => $this->post_type,
				'label' => $count == 1 ? $post_type_data->labels->singular_name : $post_type_data->labels->name,
				'count' => $count,
				'link' => admin_url('edit.php?post_type='.$this->post_type.'&usin_customer='.$user_id),
				'list' => $list,
				'icon' => 'woocommerce'
			);
		}
		
		return $activity;
	}


	public function admin_subscriptions_filter($query){
		if( is_admin() && isset($_GET['usin_customer']) && $query->get('post_type') == $this->post_type){
			$user_id = intval($_GET['usin_customer']);

			if($user_id){
				$query->set('meta_key', '_customer_user');
				$query->set('meta_value', $user_id);
			}
		}
	}
	
}
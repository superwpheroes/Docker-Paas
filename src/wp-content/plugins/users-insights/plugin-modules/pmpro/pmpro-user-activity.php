<?php

class USIN_Pmpro_User_Activity{

	protected $module_name = 'pmpro';

	public function init(){
		add_filter('usin_user_activity', array($this, 'filter_user_activity'), 10, 2);
	}

	public function filter_user_activity($activity, $user_id){
		$membership_activity = $this->get_membership_activity($user_id);
		if(!empty($membership_activity)){
			$activity[]=$membership_activity;
		}

		$discount_codes_activity = $this->get_discount_codes_activity($user_id);
		if(!empty($discount_codes_activity)){
			$activity[]=$discount_codes_activity;
		}

		return $activity;
	}

	protected function get_membership_activity($user_id){
		$memberships = $this->get_memberships($user_id);

		if(empty($memberships)){
			return null;
		}

		$list = array();

		foreach ($memberships as $membership) {
			$title = $membership->name . USIN_Html::tag($membership->status, $membership->status);
			$link = add_query_arg(array('page'=>'pmpro-membershiplevels', 'edit'=>$membership->membership_id),
				admin_url('admin.php'));
			$info=array('title'=>$title, 'link'=>$link);

			$details = $this->get_membership_details($membership);
			if(!empty($details)){
				$info['details'] = $details;
			}
			$list[]= $info;
		}
		

		return array(
			'type' => 'pmpro_membership',
			'label' => __('Membership', 'usin'),
			'list' => $list,
			'icon' => $this->module_name
		);
	}

	protected function get_memberships($user_id){
		global $wpdb;

		if(!isset($wpdb->pmpro_memberships_users) || !isset($wpdb->pmpro_membership_levels)){
			return array();
		}

		$query = $wpdb->prepare("SELECT mu.*, ml.name FROM $wpdb->pmpro_memberships_users mu".
			" LEFT JOIN $wpdb->pmpro_membership_levels ml ON mu.membership_id = ml.id".
			" WHERE mu.user_id = %d ORDER BY mu.id DESC", $user_id);

		return $wpdb->get_results($query);
	}

	protected function get_membership_details($membership){
		$details = array();

		$details[] = USIN_Html::activity_label(__('Fee', 'usin'), $this->get_price($membership));
		$details[] = USIN_Html::activity_label(__('Start date', 'usin'), USIN_Helper::format_date($membership->startdate));
		if(!empty($membership->enddate) && $membership->enddate != '0000-00-00 00:00:00'){
			$details[] = USIN_Html::activity_label(__('End date', 'usin'), USIN_Helper::format_date($membership->enddate));
		}

		$orders = $this->get_payments_count($membership);
		
		if(!empty($orders)){
			$user = get_user_by('id', $membership->user_id);
			$args = array('page'=>'pmpro-orders', 'filter'=>'within-a-level', 'l'=>$membership->membership_id,
				's'=>$user->user_email);
			$link = esc_url(add_query_arg($args, admin_url('admin.php')));
			$details[]=sprintf('<a href="%s" target="_blank">%d %s</a>', $link, $orders, 
				_n('Payment', 'Payments', $orders, 'usin'));
		}

		return $details;
	}

	protected function get_price($membership){
		$price = '';
		if((float)$membership->initial_payment > 0) {
			$price .= $this->format_amount($membership->initial_payment);
		}
		if((float)$membership->initial_payment > 0 && (float)$membership->billing_amount > 0) {
			$price .= ' + ';
		}
		if((float)$membership->billing_amount > 0) {
			$price .= $this->format_amount($membership->billing_amount) .' / ';
			if($membership->cycle_number > 1) {
				$price .= $membership->cycle_number . " " . $membership->cycle_period . "s"; 
			} else { 
				$price .= $membership->cycle_period; 
			}
		}
		if((float)$membership->initial_payment <= 0 && (float)$membership->billing_amount <= 0) {
			$price = '-';
		}
		
		return $price;
	}

	protected function format_amount($amount){
		if(function_exists('pmpro_formatPrice')){
			return pmpro_formatPrice($amount);
		}
		return $amount;
	}


	protected function get_payments_count($membership){
		global $wpdb;

		if(!isset($wpdb->pmpro_membership_orders)){
			return 0;
		}
		$success_condition = USIN_Pmpro_Query::get_sucessful_order_condition();
		$query = $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->pmpro_membership_orders".
			" WHERE user_id = %d AND membership_id = %d AND $success_condition",
			$membership->user_id, $membership->membership_id);

		return $wpdb->get_var($query);
	}


	protected function get_discount_codes_activity($user_id){
		$codes_used = $this->get_discount_codes_used($user_id);

		if(empty($codes_used)){
			return null;
		}

		$list = array();
		$count = sizeof($codes_used);

		foreach ($codes_used as $code_info) {
			$link = add_query_arg(array('page'=>'pmpro-discountcodes', 'edit'=>$code_info->id),
				admin_url('admin.php'));
			$order_link = add_query_arg(array('page'=>'pmpro-orders', 'order'=>$code_info->order_id),
				admin_url('admin.php'));
			$details = array(sprintf('<a href="%s" target="_blank">Order #%s</a>', $order_link, $code_info->order_id));
			
			$list[]=array('title'=>$code_info->code, 'link'=>$link, 'details'=>$details);
		}
		

		return array(
			'type' => 'pmpro_discounts_used',
			'count' => $count,
			'label' => _n('Discount Code Used', 'Discount Codes Used', $count, 'usin'),
			'list' => $list,
			'icon' => $this->module_name
		);
		
	}

	protected function get_discount_codes_used($user_id){
		global $wpdb;

		if(!isset($wpdb->pmpro_discount_codes_uses) || !isset($wpdb->pmpro_discount_codes)){
			return array();
		}

		$query = $wpdb->prepare("SELECT dc.code, dc.id, dcu.order_id FROM $wpdb->pmpro_discount_codes_uses dcu".
				" INNER JOIN $wpdb->pmpro_discount_codes dc on dcu.code_id = dc.id".
				" WHERE user_id = %d", $user_id);

		return $wpdb->get_results($query);
	}

}
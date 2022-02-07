<?php

class USIN_MemberPress_User_Activity{

	protected $module_name;

	public function __construct($module_name){
		$this->module_name = $module_name;
	}
	
	public function init(){
		add_filter('usin_user_activity', array($this, 'filter_user_activity'), 10, 2);
	}


	public function filter_user_activity($activity, $user_id){

		$membership_activity = $this->get_membership_activity($user_id);
		if(!empty($membership_activity)){
			$activity[]=$membership_activity;
		}
		
		$transaction_activity = $this->get_transaction_activity($user_id);
		if(!empty($transaction_activity)){
			$activity[]=$transaction_activity;
		}

		return $activity;
	}


	protected function get_membership_activity($user_id){
		$memberships = $this->get_memberships($user_id);

		if(empty($memberships)){
			return null;
		}

		$count = sizeof($memberships);
		foreach ($memberships as $membership ) {
			$list[]=$this->get_single_membership_details($membership);
		}

		return array(
			'type' => 'mepr_memberships',
			'count' => $count,
			'label' => _n('Membership', 'Memberships', $count, 'usin'),
			'list' => $list,
			'icon' => $this->module_name
		);
	}


	/**
	 * Retrieves the transactions for a user, to be used in the user activity section.
	 *
	 * @param int $user_id the user ID
	 * @return array|null list of user transactions activity when transactions are
	 * present and null otherwise
	 */
	protected function get_transaction_activity($user_id){
		$transactions = $this->get_transactions($user_id);

		if(empty($transactions)){
			return null;
		}

		$count = sizeof($transactions);
		$transactions = array_slice($transactions, 0, 5);
		foreach ($transactions as $transaction ) {
			$list[]=$this->get_single_transaction_details($transaction);
		}

		$user = get_userdata( $user_id );
		$link = add_query_arg( array(
			'page' => 'memberpress-trans',
			'search' => $user->user_login,
			'search-field' => 'user'
		), admin_url('admin.php'));

		return array(
			'type' => 'mepr_transactions',
			'count' => $count,
			'label' => _n('Transaction', 'Transactions', $count, 'usin'),
			'list' => $list,
			'icon' => $this->module_name,
			'link' => $link
		);
	}

	protected function get_memberships($user_id){
		global $wpdb;
		$query = USIN_MemberPress_Query::get_memberships_query($user_id);
		return $wpdb->get_results($query);
	}


	/**
	 * Retrieves the transaction list for a user.
	 *
	 * @param int $user_id the user ID
	 * @return array|null array of the user's transactions when present or null otherwise
	 */
	protected function get_transactions($user_id){
		global $wpdb;
		$query = USIN_MemberPress_Query::get_transactions_query($user_id);
		return $wpdb->get_results($query);
	}


	/**
	 * Retrieves the single transaction details, to be used in the user activity
	 * section.
	 *
	 * @param object $transaction
	 * @return array transaction details, formatted as an activity item
	 */
	protected function get_single_transaction_details($transaction){
		$transaction_link = add_query_arg( array(
			'page' => 'memberpress-trans',
			'action' => 'edit',
			'id' => $transaction->id
		), admin_url('admin.php'));

		$title = sprintf("#%s %s - %s %s", $transaction->id, $this->format_date($transaction->created_at_local),
			$this->format_amount_with_currency($transaction->total),
			USIN_Html::tag($transaction->status, strtolower($transaction->status)));
		
		$details = array();

		$membership_name = $this->get_membership_name($transaction->product_id);
		if($membership_name){
			$details[]= USIN_Html::activity_label( __('Membership', 'usin'), $membership_name);
		}

		$expires = $this->format_date($transaction->expires_at_local);
		if($expires){
			$details[]= USIN_Html::activity_label( __('Expires on', 'usin'), $expires);
		}

		$coupon_id = intval($transaction->coupon_id);
		if($coupon_id){
			$coupon_name = get_the_title($coupon_id);
			if(!empty($coupon_name)){
				$details[]= USIN_Html::activity_label( __('Coupon used', 'usin'), $coupon_name);
			}
		}

		return array('title'=>$title, 'link'=>$transaction_link, 'details'=>$details);
	}



	/**
	 * Retrieves the single membership details, to be used in the user activity
	 * section.
	 *
	 * @param object $membership
	 * @return array membership details, formatted as an activity item
	 */
	protected function get_single_membership_details($membership){

		if(intval($membership->is_active) === 1){
			$status_name =  __('active', 'usin');
			$status_id = 'active';
		}else{
			$status_name =  __('inactive', 'usin');
			$status_id = 'inactive';
		}

		$title = sprintf("%s %s", $this->get_membership_name($membership->product_id),
			USIN_Html::tag($status_name, $status_id));
		
		$details = array();

		$type = intval($membership->subscription_id) === 0 ? __('Non-recurring', 'usin') : __('Recurring', 'usin');
		
		$details[]= USIN_Html::activity_label( __('Created on', 'usin'), $this->format_date($membership->created_at_local));
		$details[]= USIN_Html::activity_label( __('Expires on', 'usin'), $this->format_date($membership->expires_at_local));
		$details[]= USIN_Html::activity_label( __('Type', 'usin'), $type);

		$user = get_userdata( $membership->user_id );

		$transactions_link = add_query_arg( array(
			'page' => 'memberpress-trans',
			'search' => $user->user_login,
			'search-field' => 'user',
			'membership' => $membership->product_id,
			'status' => 'all',
			'gateway' => 'all'
		), admin_url('admin.php'));
		$details[]=sprintf('<a href="%s" target="_blank">%s</a>', $transactions_link, __('View transactions', 'usin'));

		return array('title'=>$title, 'link'=>get_edit_post_link($membership->product_id, 'usin'), 'details'=>$details);
	}


	/**
	 * Format an amount with currency based on the MemberPress currency settings.
	 *
	 * @param int $amount
	 * @return string the amount with a currency symbol
	 */
	protected function format_amount_with_currency($amount){
		$mepr_options = get_option('mepr_options', array());

		if(is_array($mepr_options) && !empty($mepr_options['currency_symbol']) && isset($mepr_options['currency_symbol_after'])){
			if ($mepr_options['currency_symbol_after'] === true){
				return sprintf("%s%s", $amount, $mepr_options['currency_symbol']);
			}else{
				return sprintf("%s%s", $mepr_options['currency_symbol'], $amount);
			}
		}

		return $amount;
	}


	/**
	 * Retrieves the name of a membership based on an ID.
	 *
	 * @param int $id the membership ID
	 * @return string the name of the membership if found, or null otherwise
	 */
	protected function get_membership_name($id){
		$post = get_post($id);
		if($post){
			return $post->post_title;
		}
		return null;
	}

	protected function format_date($date){
		if(preg_match('#^0000-00-00#', $date) || $date === 0){
			return __('Never', 'usin');
		}
		if(empty($date)){
			return $date;
		}

		return USIN_Helper::format_date($date);
	}

}
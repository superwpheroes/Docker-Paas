<?php

/**
 * Includes the User Activity functionality for the EDD module.
 */
class USIN_Woocommerce_Yith_Wishlists_User_Activity{
	

	/**
	 * Registers the required filter and action hooks.
	 */
	public function init(){
		add_filter('usin_user_activity', array($this, 'add_wishlist_products_to_activity'), 10, 2);
	}
	
	/**
	 * Adds the YITH Wishlist products to the user activity.
	 * @param array $activity the default user activity data 
	 * @param int $user_id  the ID of the user
	 * @return array the default user activity including the YITH Wishlist products
	 */
	public function add_wishlist_products_to_activity($activity, $user_id){
			$wl_products = $this->get_wl_products($user_id);
			
			if(!empty($wl_products)){
				$list = array();
				$count = sizeof($wl_products);

				foreach ($wl_products as $wl_product ) {
					$title = sprintf('%s <span class="usin-activity-dark-text">%s</span> %s', $wl_product['name'], 
						__('added on', 'usin'), USIN_Helper::format_date($wl_product['date_added']));

					$list[]=array(
						'title' => $title,
						'link' => $wl_product['link']
					);
				}
				
				$activity[] = array(
					'type' => 'yith_wishlist_products',
					'for' => 'yith_wishlist_products',
					'label' => _n('Product in Wishlist', 'Products in Wishlist', $count, 'usin'),
					'count' => $count,
					'list' => $list,
					'icon' => 'woocommerce'
				);
			}
		
		return $activity;
	}

	protected function get_wl_products($user_id){
		$wl_products = array();
		
		$rows = $this->get_wl_products_from_db($user_id);

		foreach ($rows as $row ) {
			$wl_products[]=array(
				'name' => get_the_title($row->prod_id),
				'date_added' => $row->dateadded,
				'link' => get_permalink($row->prod_id)
			);
		}

		return $wl_products;
	}

	protected function get_wl_products_from_db($user_id){
		global $wpdb;

		$table = $wpdb->prefix.'yith_wcwl';
		$query = $wpdb->prepare("SELECT * FROM $table WHERE user_id = %d", $user_id);

		return $wpdb->get_results($query);
	}
	
	
}
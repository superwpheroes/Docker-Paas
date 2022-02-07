<?php

/**
 * Includes the User Activity functionality for the EDD module.
 */
class USIN_Woocommerce_Wishlists_User_Activity{
	

	/**
	 * Registers the required filter and action hooks.
	 */
	public function init(){
		add_filter('usin_user_activity', array($this, 'add_wishlist_products_to_activity'), 10, 2);
	}
	
	/**
	 * Adds the WooCommerce Wishlist products to the user activity.
	 * @param array $activity the default user activity data 
	 * @param int $user_id  the ID of the user
	 * @return array the default user activity including the WooCommerce Wishlist products
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
					'type' => 'wc_wishlist_products',
					'for' => 'wc_wishlist_products',
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

		$query = new WP_Query(array(
			'post_type' => USIN_Woocommerce::WC_WISHLIST_POST_TYPE,
			'posts_per_page' => -1,
			'offset' => 0,
			'meta_query' => array(
				array(
					'key'     => '_wishlist_owner',
					'value'   => $user_id,
					'compare' => '=',
				)
			)
		));

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$p=$query->post;
				
				$wl_items = get_post_meta($p->ID, '_wishlist_items', true);
				
				foreach ($wl_items as $item ) {
					$wl_products[]=array(
						'name' => get_the_title($item['product_id']),
						'date_added' => date_i18n( get_option( 'date_format' ), $item['date'] ),
						'link' => get_permalink($item['product_id'])
					);
				}
			}
			wp_reset_postdata();
		}
		
		return $wl_products;
	}
	
}
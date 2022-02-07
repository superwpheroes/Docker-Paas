<?php
if ( ! class_exists( 'UM_Gallery_Pro_Privacy' ) ) :
	/**
	 * UM Gallery Privacy Class.
	 *
	 *
	 * @since 1.0.8.4.5
	 */
	class UM_Gallery_Pro_Privacy {
		/**
		 * Constructor.
		 *
		 * @since 1.0.8.4.5
		 */
		public function __construct() {
			$this->hooks();
		}

		public function hooks() {
			add_action( 'um_gallery_addon_updated', array( $this, 'setup_privacy' ), 12, 1 );
		}

		public function setup_privacy( $addon_id = '' ) {
			global $wpdb;
			$charset_collate = ! empty( $wpdb->charset ) ? "DEFAULT CHARACTER SET $wpdb->charset" : '';
			// add the type column to table
			if ( 'privacy' == $addon_id ) {
				$result = $wpdb->query( "SHOW TABLES LIKE '" . $wpdb->prefix . "um_gallery_privacy'" );
				if ( ! $result ) {
						$create_query = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}um_gallery_privacy (
						  `id` bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						  `media_id` bigint(20) NOT NULL,
						  `owner_id` bigint(20) NOT NULL,
						  `user_id` bigint(20) NOT NULL,
						  `type` varchar(100) NOT NULL,
						  `created` DATETIME NULL DEFAULT NULL
					) {$charset_collate};";
					$wpdb->query( $create_query );
				}
			}
		}
	}
endif;

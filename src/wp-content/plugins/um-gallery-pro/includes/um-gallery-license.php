<?php
function um_gallery_plugin_updater() {
	if ( !function_exists( 'um_gallery_pro_get_option' ) ) return;
	$item_key = 'um_gallery_license_key';
	$item_status = 'um_gallery_license_status';
	$product = 'Gallery for Ultimate Member';
	$license_key = trim( um_gallery_pro_get_option( $item_key ) );
	$edd_updater = new EDD_SL_Plugin_Updater( 'https://suiteplugins.com/', __FILE__, array(
			'version' 	=> '1.0.7.9.1',
			'license' 	=> $license_key,
			'item_name' => $product,
			'author' 	=> 'SuitePlugins'
		)
	);

}
add_action( 'admin_init', 'um_gallery_plugin_updater', 0 );

add_filter('um_licensed_products_settings', 'um_gallery_license_key');
function um_gallery_license_key( $array ) {
	if ( !function_exists( 'um_gallery_pro_get_option' ) ) return;
	$item_key = 'um_gallery_license_key';
	$item_status = 'um_gallery_license_status';
	$product = 'User Gallery';
	$array[] = 	array(
			'id'       		=> $item_key,
			'type'     		=> 'text',
			'title'   		=> $product . ' License Key',
			'compiler' 		=> true,
		);
	return $array;
}

add_filter('redux/options/um_options/compiler', 'um_gallery_license_status', 10, 3);
function um_gallery_license_status($options, $css, $changed_values) {
	if ( !function_exists( 'um_gallery_pro_get_option' ) ) return;
	$item_key = 'um_gallery_license_key';
	$item_status = 'um_gallery_license_status';
	$product = 'Gallery for Ultimate Member';
	if ( isset( $options[$item_key] ) && isset($changed_values[$item_key]) && $options[$item_key] != $changed_values[$item_key] ) {

		if ( $options[$item_key] == '' ) {

			$license = trim( $options[$item_key] );
			$api_params = array(
				'edd_action'=> 'deactivate_license',
				'license' 	=> $changed_values[$item_key],
				'item_name' => urlencode( $product ), // the name of our product in EDD
				'url'       => home_url()
			);

			$response = wp_remote_get( add_query_arg( $api_params, 'https://suiteplugins.com/' ), array( 'timeout' => 30, 'sslverify' => false ) );
			if ( is_wp_error( $response ) )
				return false;

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			delete_option( $item_status );

		} else {

			$license = trim( $options[$item_key] );
			$api_params = array(
				'edd_action'=> 'activate_license',
				'license' 	=> $license,
				'item_name' => urlencode( $product ), // the name of our product in EDD
				'url'       => home_url()
			);

			$response = wp_remote_get( add_query_arg( $api_params, 'https://suiteplugins.com/' ), array( 'timeout' => 30, 'sslverify' => false ) );
			if ( is_wp_error( $response ) )
				return false;

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			update_option( $item_status, $license_data->license );
		}
	}
}

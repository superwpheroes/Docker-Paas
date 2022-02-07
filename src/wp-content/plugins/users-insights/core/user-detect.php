<?php

class USIN_User_Detect{

	protected $user_id;
	protected $session_timeout = 60; //number of minutes after the session number is reset
	protected $user_info_updated = false;
	protected $geoip_url = 'https://usersinsights.com/?usinr_geoip=1';
	protected $user_data;
	protected $is_new_session = false;

	const IP_KEY = 'usin_ip';
	const GEO_KEY = '_usin_should_check_geolocation';

	protected function init_user_data($user_id){
		$this->user_id = $user_id;
		$this->user_data = new USIN_User_Data($user_id);
	}

	public function init(){
		add_action('wp_login', array($this, 'do_on_login'), 10, 2);
		add_action('set_current_user', array($this, 'detect_user'));
		add_action('wp_ajax_usin_save_user_location', array($this, 'save_location'));
	}

	protected function setup_user_data(){
		if(!is_user_logged_in()){
			return false;
		}
		$user_id = get_current_user_id();
		if(!$this->should_detect_user($user_id)){
			return false;
		}

		$this->init_user_data($user_id);
		return true;
	}


	public function detect_user(){
		if(!$this->setup_user_data()){
			return;
		}
		if($this->is_activity_active()){
			$this->set_last_seen();

			if($this->is_new_session){
				$this->do_actions_on_new_session();
			}
		}
		
		// this should be called on every request as opposed on new session, because 
		// the first time the user logs in, they might navigate away from the page before 
		// the location is detected and on the next page load it won't be a new session anymore
		$this->check_to_save_location();
	}



	public function do_on_login($user_login, $user){
		if(!$this->is_activity_active() && $this->should_detect_user($user->ID)){
			$this->init_user_data($user->ID);
			//if the activity module is not active to detect the last seen date,
			//the login hook is the alternative way to detect a new session
			$this->do_actions_on_new_session();
		}
	}


	protected function should_detect_user($user_id){
		$should_detect = false;
		if($this->is_activity_active() || 
			$this->is_devices_active() ||
			$this->is_geolocation_active()){
			$should_detect = true;
		}

		return apply_filters('usin_detect_user', $should_detect, $user_id);
	}



	protected function set_last_seen(){
		$today = current_time('mysql');
		$last_seen = $this->user_data->get('last_seen');

		if(!empty($last_seen)){
			//check if it's been a while since the last access, so that it can be
			//counted like a new session
			$last_seen_date = new DateTime($last_seen);
			$today_date = new DateTime($today);
			$minutes_diff = (USIN_Helper::get_unix_timestamp($today_date) - USIN_Helper::get_unix_timestamp($last_seen_date))/60;

			if($minutes_diff >= $this->session_timeout){
				$this->is_new_session = true;
			}
		}else{
			//the user has an old session active and doesn't have a last seen record
			//update the user data
			$this->is_new_session = true;
		}

		$this->user_data->save('last_seen', $today);
	}

	protected function do_actions_on_new_session(){
		if(!$this->user_info_updated){
			if($this->is_activity_active()){
				$this->increase_sessions_count();
			}
			if($this->is_devices_active()){
				$this->set_user_agent();
			}
			if($this->is_geolocation_active()){
				update_user_meta($this->user_id, self::GEO_KEY, true);
			}
			$this->user_info_updated = true;
		}

	}

	public function check_to_save_location(){
		if (defined('DOING_AJAX') && DOING_AJAX){
			//this is an AJAX request, no need to setup scripts for location
			return;
		}
		if($this->should_save_location()){
			$this->setup_scripts();
		}
	}

	protected function increase_sessions_count(){
		$sessions = $this->user_data->get('sessions');
		$sessions_upd = empty($sessions) ? 1 : intval($sessions) + 1;

		$this->user_data->save('sessions', $sessions_upd);
	}

	protected function set_user_agent(){
		$data = array();
		
		$browser = new USIN_Browser();
		if(method_exists($browser, 'getBrowser')){
			$browser_name = $browser->getBrowser();
			$data['browser'] = $browser_name;
		}

		if(method_exists($browser, 'getVersion')){
			$browser_version = $browser->getVersion();
			$data['browser_version'] = $browser_version;
		}

		if(method_exists($browser, 'getPlatform')){
			$platform = $browser->getPlatform();
			$data['platform'] = $platform;
		}

		if(!empty($data)){
			$this->user_data->save_array($data);
		}
	}

	protected function get_user_ip(){
		$ip = preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] );
		$ip = apply_filters('usin_client_ip', $ip);
		return $ip;
	}

	protected function should_save_location($ignore_new_session = false){
		if(!$this->is_geolocation_active()){
			return false;
		}
		$last_ip = get_user_meta( $this->user_id, self::IP_KEY, true);
		if(empty($last_ip)){
			//no data has been stored yet, check location
			return true;
		}
		
		if($last_ip == 'fail'){
			//there was a failure with checking the location the previous time,
			//now check the location only if it is a new session
			if($ignore_new_session){
				return true;
			}

			$geo_check = get_user_meta($this->user_id, self::GEO_KEY, true);
			if(!empty($geo_check)){
				return true;
			}

			return false;
		}
		
		$ip = $this->get_user_ip();
		$hashed_ip = md5($ip);
		if(!empty($ip) && $hashed_ip != $last_ip){
			return true;
		}

		$this->reset_geolocation_check();
		return false;
	}

	public function setup_scripts(){
		add_action('wp_print_scripts', array($this, 'print_scripts'));
	}

	/**
	 * Prints a JavaScript code that makes an AJAX request to save the user location
	 * In this way the location will be saved asynchronously in a separate request and won't affect
	 * the loading time of the user's page.
	 * @return [type] [description]
	 */
	public function print_scripts(){
		$url = add_query_arg('action', 'usin_save_user_location', admin_url( 'admin-ajax.php' ));

		$output = '<script type="text/javascript">';
		$output .= 'if (window.XMLHttpRequest){ var usin_xmlhttp=new XMLHttpRequest();';
		$output .= 'usin_xmlhttp.open("GET","'.$url.'",true); usin_xmlhttp.send(); }';
		$output .= '</script>';

		echo $output;
	}

	public function save_location(){
		if(!is_user_logged_in() || empty($this->user_id)){
			return;
		}
		if($this->should_save_location(true)){
			$license = usin_modules()->get_license('geolocation');
			if(empty($license)){
				//there is no license set, pause the Geolocation
				USIN_Geolocation_Status::pause();
				$this->set_geolocation_fail();
				$this->reset_geolocation_check();
				return;
			}

			$ip = $this->get_user_ip();
			$args = array(
				'ip' => $ip,
				'license' => $license,
				'url' => home_url()
			);

			$loc_data = array(
				'country' => __('unknown', 'usin'),
				'city' => __('unknown', 'usin'),
				'region' => __('unknown', 'usin'),
				'coordinates' => null
			);
			
			if(!USIN_Geolocation_Status::is_paused()){
				$res = wp_remote_get( add_query_arg($args, $this->geoip_url ),
					array('timeout'=>30));
				
				$request_error = false;

				if(!is_wp_error($res)){
					$body = wp_remote_retrieve_body( $res );
					$location = json_decode($body);
					
					if(!empty($location) && !isset($location->error)){
						if(!empty($location->country)){
							$loc_data['country'] = $location->country;
						}
						if(!empty($location->city)){
							$loc_data['city'] = $location->city;
						}
						if(!empty($location->region)){
							$loc_data['region'] = $location->region;
						}
						if(!empty($location->longitude) && !empty($location->latitude)){
							$loc_data['coordinates'] = $location->latitude.','.$location->longitude;
						}

						//the location has been added successfully, save an IP hash
						//to check against next time, so it won't retrieve the data 
						//for the same IP next time
						$this->cache_ip($ip);
					}else{
						$request_error = true;
						if(isset($location->error) && isset($location->license_status)){
							//the license is not valid, pause the geolocation lookup for a while
							USIN_Geolocation_Status::pause();
						}
					}
				}
				
				if(is_wp_error($res) || $request_error){
					$this->set_geolocation_fail();
				}
			}else{
				$this->set_geolocation_fail();
			}
			
			$this->user_data->save_array($loc_data);
			$this->reset_geolocation_check();
		}
	}

	protected function cache_ip($ip){
		$hashed_ip = md5($ip);
		update_user_meta($this->user_id, self::IP_KEY, $hashed_ip);
	}

	protected function set_geolocation_fail(){
		update_user_meta($this->user_id, self::IP_KEY, 'fail');
	}

	protected function reset_geolocation_check(){
		delete_user_meta($this->user_id, self::GEO_KEY);
	}

	protected function is_activity_active(){
		return usin_modules()->is_module_active('activity');
	}

	protected function is_geolocation_active(){
		return usin_modules()->is_module_active('geolocation');
	}

	protected function is_devices_active(){
		return usin_modules()->is_module_active('devices');
	}

	/**
	 * @deprecated 3.6.3
	 */
	public static function from_id($user_id){
		$instance = new self();
		$instance->init_user_data($user_id);

		return $instance;
	}
}

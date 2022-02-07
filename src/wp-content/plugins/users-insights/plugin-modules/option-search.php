<?php

class USIN_Option_Search extends USIN_Ajax{

	public $key;
	protected $callback;
	protected $user_capability;
	protected $nonce_key;
	protected $options_cache;

	const MAX_OPTIONS = 200;

	public function __construct($key, $callback){
		$this->key = $key;
		$this->callback = $callback;
		$this->user_capability = USIN_Capabilities::LIST_USERS;
		$this->nonce_key = USIN_Assets::$global_nonce_key;

		$this->add_actions();
	}

	public function add_actions(){
		add_action('wp_ajax_'.$this->key, array($this, 'option_search'));
	}

	public function ajax_search_enabled(){
		$options = $this->get_options();
		return sizeof($options) >= self::MAX_OPTIONS;
	}

	public function get_options(){
		if(isset($this->options_cache)){
			return $this->options_cache;
		}

		$this->options_cache = call_user_func_array($this->callback, array(self::MAX_OPTIONS, null));
		return $this->options_cache;
	}

	public function option_search(){
		$this->verify_request();
		$this->validate_required_get_params(array('search'));

		$res = call_user_func_array($this->callback, array(self::MAX_OPTIONS, $_GET['search']));
	
		$this->respond($res);
	}

	/**
	 * Retrieves the search action key
	 *
	 * @return string return the seach action key when AJAX is enabled (there are more options than
	 * the max allowed) or null when there is no need for AJAX search
	 */
	public function get_search_action(){
		return $this->ajax_search_enabled() ? $this->key : null;
	}
}
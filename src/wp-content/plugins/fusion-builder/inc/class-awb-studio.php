<?php
/**
 * Avada Studio
 *
 * @package Avada-Builder
 * @since 3.5
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

/**
 * AWB Studio class.
 *
 * @since 3.5
 */
class AWB_Studio {

	/**
	 * The one, true instance of this object.
	 *
	 * @static
	 * @access private
	 * @since 3.0
	 * @var object
	 */
	private static $instance;

	/**
	 * The studio data.
	 *
	 * @access public
	 * @var mixed
	 */
	public $data = null;

	/**
	 * The studio status.
	 *
	 * @access public
	 * @var boolean
	 */
	public static $status = null;

	/**
	 * URL to fetch from.
	 *
	 * @access private
	 * @var boolean
	 */
	private $studio_url = 'https://avada.studio';

	/**
	 * Class constructor.
	 *
	 * @since 3.0
	 * @access private
	 */
	private function __construct() {

		if ( ! self::is_studio_enabled() ) {
			return;
		}

		add_action( 'wp_ajax_fusion_builder_load_studio_elements', [ $this, 'get_ajax_data' ] );
		add_action( 'fusion_builder_load_templates', [ $this, 'builder_template' ] );
		add_action( 'fusion_builder_after', [ $this, 'builder_template' ] );

		// Requests to update server args.
		add_filter( 'http_request_args', [ $this, 'request_headers' ], 10, 2 );

		// Load admin page.
		if ( is_admin() ) {
			add_action( 'init', [ $this, 'admin_init' ] );
		}
	}

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @static
	 * @access public
	 * @since 3.0
	 */
	public static function get_instance() {

		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( null === self::$instance ) {
			self::$instance = new AWB_Studio();
		}
		return self::$instance;
	}

	/**
	 * Studio status.
	 *
	 * @static
	 * @access public
	 * @since 3.0
	 */
	public static function is_studio_enabled() {

		if ( null !== self::$status ) {
			return self::$status;
		}

		$option_name  = class_exists( 'Fusion_Settings' ) ? Fusion_Settings::get_option_name() : 'fusion_options';
		$option       = get_option( $option_name, [] );
		self::$status = apply_filters( 'fusion_load_studio', isset( $option['status_avada_studio'] ) && '0' === $option['status_avada_studio'] ? false : true );

		return self::$status;
	}

	/**
	 * Return the studio URL.
	 *
	 * @access public
	 * @since 3.0
	 * @return string
	 */
	public function get_studio_url() {
		return $this->studio_url;
	}

	/**
	 * Get the data from REST endpoint.
	 *
	 * @access public
	 * @since 3.0
	 * @return array
	 */
	public function get_data() {
		if ( null !== $this->data ) {
			return $this->data;
		}

		if ( ! FUSION_BUILDER_DEV_MODE && false !== get_transient( 'avada_studio' ) ) {
			$this->data = get_transient( 'avada_studio' );
			return $this->data;
		}

		$response = wp_remote_get( $this->studio_url . '/wp-json/studio/full', [ 'timeout' => 60 ] );

		// Exit if error.
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// Get the body.
		$resources = json_decode( wp_remote_retrieve_body( $response ), true );

		set_transient( 'avada_studio', $resources, DAY_IN_SECONDS );

		return $resources;
	}

	/**
	 * Get the data for ajax requests.
	 *
	 * @access public
	 * @since 3.0
	 * @return void
	 */
	public function get_ajax_data() {
		check_ajax_referer( 'fusion_load_nonce', 'fusion_load_nonce' );

		echo wp_json_encode( $this->get_data() );

		wp_die();
	}

	/**
	 * Template used for studio layouts.
	 *
	 * @access public
	 * @since 3.0
	 * @return void
	 */
	public function builder_template() {
		?>
		<script type="text/html" id="tmpl-fusion_studio_layout">
			<#
			var slugs       = '';
			if ( 'object' === typeof tags ) {
				_.each( tags, function( tag ) {
					slugs += tag + ' ';
				} );
				slugs.trim();
			}

			if ( 'string' === typeof element ) {
				elementType = element;
			}

			#>
			<li class="fusion-page-layout" data-layout_id="{{ ID }}" data-slug="{{ slugs }}">
				<div class="preview lazy-load">
					<img src="data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%27%20width%3D%27{{ thumbnail.width }}%27%20height%3D%27{{ thumbnail.height }}%27%20viewBox%3D%270%200%20{{ thumbnail.width }}%20{{ thumbnail.height }}%27%3E%3Crect%20width%3D%27{{ thumbnail.width }}%27%20height%3D%273{{ thumbnail.height }}%27%20fill-opacity%3D%220%22%2F%3E%3C%2Fsvg%3E" alt="" width="{{ thumbnail.width }}" height="{{ thumbnail.height }}" data-src="{{ thumbnail.url }}" data-alt="{{ post_title }}"/>
				</div>
				<div class="bar">
					<span class="fusion_module_title">{{ post_title }}</span>
					<div class="fusion-module-right">
						<button class="fusion-studio-preview" type="button" data-url="{{url}}"><span class="fusiona-search"></span></button>
					<div class="fusion-studio-load">
						<span class="fusiona-plus"></span>
						<div class="fusion-template-options">
						<ul>
							<li class="fusion-studio-import" data-load-type="replace"><?php esc_html_e( 'Replace all page content', 'fusion-builder' ); ?></li>
							<li class="fusion-studio-import" data-load-type="above"><?php esc_html_e( 'Insert above current content', 'fusion-builder' ); ?></li>
							<li class="fusion-studio-import" data-load-type="below"><?php esc_html_e( 'Insert below current content', 'fusion-builder' ); ?></li>
						</ul>
					</div>
					</div>
				</div>
				</div>
			</li>
		</script>
		<?php
	}

	/**
	 * Inits admin.
	 *
	 * @access public
	 * @since 3.5
	 * @return void
	 */
	public function admin_init() {
		require_once FUSION_BUILDER_PLUGIN_DIR . 'inc/class-awb-studio-admin.php';
		new AWB_Studio_Admin();
	}

	/**
	 * Add referrer to headers.
	 *
	 * @since 3.5
	 *
	 * @param array  $parsed_args Parsed request args.
	 * @param string $url         Request URL.
	 * @return array
	 */
	public function request_headers( $parsed_args = [], $url = '' ) {

		// If its not requesting the studio site.
		if ( false === strpos( $url, $this->studio_url ) ) {
			return $parsed_args;
		}

		$parsed_args['user-agent'] = 'avada-user-agent';

		return $parsed_args;
	}
}

/**
 * Instantiates the AWB_Studio class.
 * Make sure the class is properly set-up.
 *
 * @since object 3.0
 * @return object AWB_Studio
 */
function AWB_Studio() { // phpcs:ignore WordPress.NamingConventions
	return AWB_Studio::get_instance();
}
AWB_Studio();

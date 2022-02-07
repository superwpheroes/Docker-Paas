<?php
// WP_List_Table is not loaded automatically so we need to load it in our application
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Create a new table class that will extend the WP_List_Table.
 */
class UM_Gallery_Pro_List_Table extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( array(
			'singular' => __( 'Album', 'um-gallery-pro' ), //singular name of the listed records.
			'plural'   => __( 'Albums', 'um-gallery-pro' ), //plural name of the listed records.
			'ajax'     => false, //does this table support ajax?.
			)
		);

	}


	/**
	 * Retrieve customers data from the database.
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	 public function get_albums( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT a.*, d.file_name, COUNT(d.id) AS total_photos, d.type, u.display_name AS author FROM {$wpdb->prefix}um_gallery_album AS a LEFT JOIN {$wpdb->prefix}um_gallery AS d ON a.id=d.album_id LEFT JOIN {$wpdb->prefix}users AS u ON a.user_id=u.ID";

		$sql .= ' WHERE 1=1 ';
		if ( ! empty( $_REQUEST['s'] ) ) {
			$search_q = $wpdb->esc_like( $_REQUEST['s'] );
			$search_q = '%' . $search_q . '%';
			$sql .= $wpdb->prepare( ' AND a.album_name LIKE "%s" ', $search_q );
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}

		$sql .= ' GROUP BY a.id ';
		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
	}


	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}um_gallery_album",
			array(
				'id' => $id
			),
			array( '%d' )
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}um_gallery_album";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No albums available.', 'um-gallery-pro' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		global $album;
		$album = $item;

		switch ( $column_name ) {
			case 'album_image':
				return '<a href="' . esc_url( um_gallery_pro_admin()->album_view_url() ) . '"><img src="' . um_gallery_get_album_feature_media_url( $album['id'] ) . '" style="height: 60px;"></a>';
			break;
			case 'album_name':
				return '<a href="' . esc_url( um_gallery_pro_admin()->album_view_url() ) . '">' . $album['album_name'] . '</a>';
			break;
			case 'author':
				return $item[ $column_name ];
			break;
			case 'creation_date':
				return $item[ $column_name ];
			break;
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}

	function column_album_name( $item ) {
		global $album;
		$album = $item;
		$actions = array(
		        'edit'      => sprintf( '<a href="?page=%s&view=%s&album_id=%d">' . __('Edit', 'um-gallery-pro' ).'</a>',$_REQUEST['page'],'edit_album',$item['id'] ),
		        'delete'    => sprintf( '<a href="#" class="um-album-delete" data-type="album" data-album_id="%1s" data-nonce="%2s">' . __( 'Delete', 'um-gallery-pro' ). '</a>',$item['id'], wp_create_nonce( 'um_gallery_pro_sec' ) ),
		    );

		return sprintf( '<a href="%1$s">%2$s %3$s', esc_url( um_gallery_pro_admin()->album_view_url() ), $item['album_name'], $this->row_actions( $actions ) );
	}


	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {

		$delete_nonce = wp_create_nonce( 'sp_delete_customer' );

		$title = '<strong>' . $item['name'] . '</strong>';

		$actions = array(
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		);

		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'album_image' 		=> '',
			'album_name'    	=> __( 'Name', 'um-gallery-pro' ),
			'author'    		=> __( 'Uploaded By', 'um-gallery-pro' ),
			'creation_date'    	=> __( 'Date', 'um-gallery-pro' ),
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'album_name' => array( 'album_name', true ),
			'city' => array( 'city', false )
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return;
		/**
		 * TODO: Perform bulk actions in later update
		 */
		$actions = array(
			'bulk-delete' => __( 'Delete', 'um-gallery-pro' ),
		);

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'albums_per_page', 20 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( array(
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page,
		) );

		$this->items = self::get_albums( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_customer( absint( $_GET['customer'] ) );

						// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
						// add_query_arg() return the current url
						wp_redirect( esc_url_raw(add_query_arg()) );
				exit;
			}

		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
			 || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_customer( $id );

			}

			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
				// add_query_arg() return the current url
				wp_redirect( esc_url_raw( add_query_arg() ) );
			exit;
		}
	}

	public function search_box( $text, $input_id ) {
        //if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
           // return;

        $input_id = $input_id . '-search-input';

        if ( ! empty( $_REQUEST['orderby'] ) )
            echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
        if ( ! empty( $_REQUEST['order'] ) )
            echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
        if ( ! empty( $_REQUEST['post_mime_type'] ) )
            echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
        if ( ! empty( $_REQUEST['detached'] ) )
            echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
	?>
	<p class="search-box">
	    <label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo $text; ?>:</label>
	    <input type="search" id="<?php echo esc_attr( $input_id ); ?>" name="s" value="<?php _admin_search_query(); ?>" />
	    <?php submit_button( $text, '', '', false, array( 'id' => 'search-submit' ) ); ?>
	</p>
	<?php
    }
}

<?php
/**
 * Comments Class for UM Gallery Pro
 */
class UM_Gallery_Comments {

	private $comments_table;
	/**
	 * __construct
	 */
	public function __construct() {

		$this->hooks();
	}

	/**
	 * Set the comments table name
	 */
	public function set_table() {
		global $wdpb;
		return $wpdb->prefix . 'um_gallery_comments';
	}

	public function hooks() {
		add_action( 'wp_ajax_um_gallery_get_comments', array( $this, 'get_comments_by_photo_id' ) );
		add_action( 'wp_ajax_nopriv_um_gallery_get_comments', array( $this, 'get_comments_by_photo_id' ) );
		add_action( 'wp_ajax_um_gallery_post_comment', array( $this, 'ajax_post_comment' ) );
		add_action( 'wp_ajax_um_gallery_delete_comment', array( $this, 'ajax_delete_comment' ) );
		add_action( 'um_gallery_photo_deleted', array( $this, 'delete_associated_comments' ), 12, 1  );
	}

	/**
	 * Get comments by media id
	 * @param  integer $photo_id [description]
	 * @return [type]              [description]
	 */
	public function get_comments_by_photo_id() {
		if ( empty( $_GET['id'] ) ) {
			wp_send_json_error();
		}
		$photo_id = (int) $_GET['id'];
		$args = array(
			'photo_id' => (int) $photo_id,
		);
		wp_send_json( $this->get_comments( $args ) );
	}
	public function get_comments( $args = array(), $single = false ) {
		global $wpdb;
		$defaults = array(
			'amount'        => '20',
			'comment_id'    => 0,
			'photo_id'      => 0,
		);

		$args = wp_parse_args( $args, $defaults );
		$current_user = get_current_user_id();
		$query = "SELECT c.id, c.creation_date as created, c.parent_id as parent, c.comment as content, u.display_name AS fullname, c.user_id  FROM " . $wpdb->prefix . "um_gallery_comments AS c LEFT JOIN  " . $wpdb->users . " as u ON c.user_id=u.ID WHERE 1=1";
		if ( ! empty( $args['comment_id'] ) ) {
			$query.= " AND c.id = '" . (int) $args['comment_id'] . "' ";
		}
		if ( ! empty( $args['photo_id'] ) ) {
			$query.= " AND c.photo_id = '" . (int) $args['photo_id'] . "' ";
		}
		if (  true == $single ) {
			$results = $wpdb->get_row( $query );
			if( ! empty( $results ) ) {
				if( empty( $results->user_id ) ) {
					$results->profile_picture_url = '';
				}
				um_fetch_user( $results->user_id );
				$results->profile_picture_url = um_get_user_avatar_url();
				$results->profile_url         = um_user_profile_url();
				if ( is_user_logged_in() && $results->user_id == $current_user ) {
					$results->created_by_current_user = true;
				}else{
					$results->created_by_current_user = false;
				}
				if ( empty( $results->parent ) ) {
					$results->parent = null;
				}
				um_reset_user();
			}
		} else {
			$results = $wpdb->get_results( $query );
			if( ! empty( $results ) ) {
				foreach( $results as $k => $row ) {
					if( empty( $row->user_id ) ) {
						$results[$k]->profile_picture_url = '';
						continue;
					}
					um_fetch_user( $row->user_id );
					$results[$k]->profile_picture_url = um_get_user_avatar_url();
					$results[$k]->profile_url         = um_user_profile_url();
					if ( is_user_logged_in() && $row->user_id==$current_user ) {
						$results[$k]->created_by_current_user = true;
					}else{
						$results[$k]->created_by_current_user = false;
					}
					if ( empty( $results[$k]->parent ) ) {
						$results[$k]->parent = null;
					}
					um_reset_user();
				}
			}
		}
		return $results;
	}

	/**
	 * Post comment for insert/update
	 *
	 * @var  array $insert_array
	 * @return array
	 */
	public function post( $insert_array = array() ) {

		global $wpdb;
		$response = array();
		$id = 0;
		// check if user can post comment
		// if no $id passed then do an insert
		if (  empty( $insert_array['id'] ) ) {
			if ( ! $wpdb->insert( $wpdb->prefix . 'um_gallery_comments', $insert_array ) ) {
            	return false;
			}
			$id = (int) $wpdb->insert_id;
			do_action( 'um_gallery_new_comment', $id, $insert_array );
		} else {
			$id = (int) $insert_array['id'];
			if ( isset( $insert_array['photo_id'] ) ) {
				unset( $insert_array['photo_id'] );
			}
			$wpdb->update( $wpdb->prefix . 'um_gallery_comments', $insert_array, array( 'id' => $id ) );
			do_action( 'um_gallery_updated_comment', $id, $insert_array );
		}
		//$response['id'] = $id;
		$args = array(
			'comment_id' => (int) $id,
		);
		$response = $this->get_comments( $args, true );
		return $response;
	}

	/**
	 * Delete comment
	 *
	 * @param  integer $id
	 * @return json
	 */
	public function delete( $id = 0 ) {
		global $wpdb;
		if ( empty( $id ) ) {
			return true;
		}
		//echo 'Deleting '. $id . "\n";
		$wpdb->delete( $wpdb->prefix . 'um_gallery_comments', array( 'id' => (int) $id ) );
		if( true  == $this->delete_children( $id ) ) {
			return true;
		}
	}

	public function delete_children( $parent_id = 0 ) {
		global $wpdb;
		if ( empty( $parent_id ) ) {
			return;
		}
		$query 		= "SELECT id FROM {$wpdb->prefix}um_gallery_comments WHERE parent_id='{$parent_id}'";
		$results 	= $wpdb->get_col( $query );
		if ( ! empty( $results ) ) {
			foreach( $results as $comment_id ) {
				$this->delete( $comment_id );
			}
		} else {
			return true;
		}
	}

	public function get_comments_by_parent( $parent = 0 ) {
		global $wpdb;
		$query 		= "SELECT id FROM {$wpdb->prefix}um_gallery_comments WHERE parent_id='{$parent}'";
		$results 	= $wpdb->get_col( $query );
		echo $wpdb->last_error;
		echo $wpdb->last_query;
		if ( ! empty( $results ) ) {
			foreach( $results as $comment_id ) {
				echo $comment_id . '<br />';
			}
		}

	}
	/**
	 * Upvote comment
	 *
	 * @param  integer $id
	 * @return json
	 */
	public function upvote( $id = 0 ) {

	}

	/**
	 * Check nonce security
	 *
	 * @return boolean
	 */
	public function check_security() {
		return true;
	}

	/**
	 * AJAX callback for posting a comment
	 *
	 *
	 * @return json
	 */
	public function ajax_post_comment() {
		if( ! $this->check_security() ) {
			wp_send_json_error( array( 'message' => __( 'Error found, try again.', 'um-gallery-pro' ) ) );
		}
		$photo_id 	= 0;
		$comment_id = 0;
		if ( isset( $_POST['photo_id'] ) ) {
			$photo_id = (int) $_POST['photo_id'];
		}

		$args = array();
		$args['photo_id'] = ( ! empty( $_POST['photo_id'] ) ?  (int) $_POST['photo_id'] : 0 );
		$args['parent_id'] = ( ! empty( $_POST['parent_id'] ) ?  (int) $_POST['parent_id'] : 0 );
		$args['user_id'] = ( ! empty( $_POST['user_id'] ) ?  (int) $_POST['user_id'] : get_current_user_id() );
		$args['comment'] = ( ! empty( $_POST['content'] ) ?  esc_html( $_POST['content'] ) : '' );
		//$args['modified_date'] = ( ! empty( $_POST['modified_date'] ) ?  (int) $_POST['photo_id'] : 0 );
		$args['parent_id'] = ( ! empty( $_POST['parent'] ) ?  (int) $_POST['parent'] : 0 );

		$args['id'] = ( ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0 );
		if ( empty( $args['id'] ) ) {
			$args['creation_date'] = current_time( 'mysql' );
		}
		$result = $this->post( $args );
		wp_send_json( $result );
	}

	/**
	 * Ajax call back for deleting a comment
	 *
	 * @return void
	 */
	public function ajax_delete_comment() {
		$comment_id = ( ! empty( $_POST['id'] ) ? (int) $_POST['id'] : '' );
		echo $this->delete( $comment_id );
		exit();
	}

	/**
	 * Delete comments after a photo is deleted from album
	 *
	 * @param  integer $photo_id Photo ID
	 *
	 * @return void
	 */
	public function delete_associated_comments( $photo_id = 0 ) {
		if ( empty( $photo_id ) ) {
			return;
		}
		$args = array(
			'photo_id' => $photo_id
		);
		$comments = $this->get_comments( $args );
		if ( ! empty( $comments ) ) {
			foreach( $comments as $comment ) {
				$this->delete( $comment->id );
			}
		}
	}
}
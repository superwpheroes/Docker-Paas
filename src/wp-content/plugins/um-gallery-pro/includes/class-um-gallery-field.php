<?php
class UM_Gallery_Field {
	/*
	Use cpt
	meta field will use meta_key, display format, field type, field options
	 */
	
	public $field_post_type = 'um_gallery_field';
	public $category        = 'um_gallery_category';
	public function __construct() {
		$this->hooks();
	}

	public function hooks() {
		//add_action( 'init', array( $this, 'init_fields' ) );
		add_action( 'init', array( $this, 'init_category' ) );
		//add_action( 'admin_menu', array( $this, 'init_admin_menu' ), 55 ); 
	}

	public function init_fields() {
		$labels = array(
			'name'               => _x( 'Fields', 'post type general name', 'um-gallery-pro' ),
			'singular_name'      => _x( 'Field', 'post type singular name', 'um-gallery-pro' ),
			'menu_name'          => _x( 'Fields', 'admin menu', 'um-gallery-pro' ),
			'name_admin_bar'     => _x( 'Field', 'add new on admin bar', 'um-gallery-pro' ),
			'add_new'            => _x( 'Add New', 'field', 'um-gallery-pro' ),
			'add_new_item'       => __( 'Add New Field', 'um-gallery-pro' ),
			'new_item'           => __( 'New Field', 'um-gallery-pro' ),
			'edit_item'          => __( 'Edit Field', 'um-gallery-pro' ),
			'view_item'          => __( 'View Field', 'um-gallery-pro' ),
			'all_items'          => __( 'All Fields', 'um-gallery-pro' ),
			'search_items'       => __( 'Search Fields', 'um-gallery-pro' ),
			'parent_item_colon'  => __( 'Parent Fields:', 'um-gallery-pro' ),
			'not_found'          => __( 'No fields found.', 'um-gallery-pro' ),
			'not_found_in_trash' => __( 'No fields found in Trash.', 'um-gallery-pro' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'um-gallery-pro' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'field' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title' )
		);

		register_post_type( $this->field_post_type, $args );
	}

	public function init_category() {
		// Add new taxonomy, make it hierarchical (like categories)
		$labels = array(
			'name'              => _x( 'Categories', 'taxonomy general name', 'um-gallery-pro' ),
			'singular_name'     => _x( 'Category', 'taxonomy singular name', 'um-gallery-pro' ),
			'search_items'      => __( 'Search Categories', 'um-gallery-pro' ),
			'all_items'         => __( 'All Categories', 'um-gallery-pro' ),
			'parent_item'       => __( 'Parent Category', 'um-gallery-pro' ),
			'parent_item_colon' => __( 'Parent Category:', 'um-gallery-pro' ),
			'edit_item'         => __( 'Edit Category', 'um-gallery-pro' ),
			'update_item'       => __( 'Update Category', 'um-gallery-pro' ),
			'add_new_item'      => __( 'Add New Category', 'um-gallery-pro' ),
			'new_item_name'     => __( 'New Category Name', 'um-gallery-pro' ),
			'menu_name'         => __( 'Category', 'um-gallery-pro' ),
		);

		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'genre' ),
		);

		register_taxonomy( $this->category, null, $args );

		$labels = array(
			'name'                       => _x( 'Tags', 'taxonomy general name', 'um-gallery-pro' ),
			'singular_name'              => _x( 'Tag', 'taxonomy singular name', 'um-gallery-pro' ),
			'search_items'               => __( 'Search Tags', 'um-gallery-pro' ),
			'popular_items'              => __( 'Popular Tags', 'um-gallery-pro' ),
			'all_items'                  => __( 'All Tags', 'um-gallery-pro' ),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => __( 'Edit tags', 'um-gallery-pro' ),
			'update_item'                => __( 'Update tags', 'um-gallery-pro' ),
			'add_new_item'               => __( 'Add New tags', 'um-gallery-pro' ),
			'new_item_name'              => __( 'New Tag Name', 'um-gallery-pro' ),
			'separate_items_with_commas' => __( 'Separate tags with commas', 'um-gallery-pro' ),
			'add_or_remove_items'        => __( 'Add or remove tags', 'um-gallery-pro' ),
			'choose_from_most_used'      => __( 'Choose from the most used tags', 'um-gallery-pro' ),
			'not_found'                  => __( 'No tags found.', 'um-gallery-pro' ),
			'menu_name'                  => __( 'Tags', 'um-gallery-pro' ),
		);

		$args = array(
			'hierarchical'          => false,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'um_gallery_tag' ),
		);

		register_taxonomy( 'um_gallery_tag', null, $args );
	}

	public function init_admin_menu() {
	}
}
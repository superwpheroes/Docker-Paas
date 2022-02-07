<?php

extract( shortcode_atts( array(
    'users' => null,
    'limit' => 12,
), $atts ) );

$args = array(
	'meta_key' => 'account_status',
	'meta_value' => 'approved',
	'number' => $limit,
	// 'include' => array( 6930, 6932 )
);

if(!is_null($users)){
	$args['include'] = explode(',', $users);
	$args['orderby'] = 'include';
	$args['order'] = 'ASC';
}

// echo '<pre style="display:none;">'.print_r($args,1).'</pre>';


$user_query = new WP_User_Query( $args );

// User Loop
if ( ! empty( $user_query->results ) ) : ?>

	

	<div class="ns-collection um-form">
		<div class="row">

			<?php foreach ( $user_query->results as $user ) :
				$image = get_user_meta($user->ID, 'cover_photo', true);
				$ext = '.' . pathinfo($image, PATHINFO_EXTENSION);

				//check if cover exists
				if ( file_exists( UM()->files()->upload_basedir . $user->ID . '/'.$image ) ) {

					$resized = image_make_intermediate_size( UM()->files()->upload_basedir . $user->ID . '/'.$image, 450, 300, true );

					if( is_ssl() ){
						UM()->files()->upload_baseurl = str_replace("http://", "https://",  UM()->files()->upload_baseurl );
					}
					$base_url = UM()->files()->upload_baseurl . $user->ID . '/';

					if($resized !== false){
						$uri = $base_url .$resized['file'].'?' . current_time( 'timestamp' );
					}
				}else{
					$def_uri = um_get_default_cover_uri();
					$base_def_uri = substr($def_uri, 0, strrpos($def_uri, '/')+1);
					$path = parse_url($def_uri);
					$resize_default = isset($path['path']) ? image_make_intermediate_size( $_SERVER['DOCUMENT_ROOT'].$path['path'], 450, 300, true ) : um_get_default_cover_uri();
					$uri = $resize_default !== false ? $base_def_uri.$resize_default['file'] : '';
				}
				$permalink = new UM_Permalinks;
				$user_slug = $permalink->profile_slug($user->display_name, $user->first_name, $user->last_name);

				// echo '<pre>'.print_r($resize_default, 1).'</pre>';
				// echo $permalink->profile_slug($user->display_name, $user->first_name, $user->last_name);
				?>

				<div class="col-sm-4">
					<div class="ns-collection__box">
						<a href="<?php echo home_url();?>/photographer/<?php echo $user_slug;?>/" title="<?php echo $user->display_name;?>">
							<img src="<?php echo $uri;?>" alt="">
						</a>

						<div class="um-member-card no-photo">
							<div class="um-member-name">
								<a href="<?php echo home_url();?>/photographer/<?php echo $user_slug;?>/" title="<?php echo $user->display_name;?>"><?php echo $user->display_name;?></a>
							</div>
						</div>
					</div>
				</div>

			<?php endforeach;?>
		</div>
	</div>

<?php else: ?>
	No users found.
<?php endif;?>


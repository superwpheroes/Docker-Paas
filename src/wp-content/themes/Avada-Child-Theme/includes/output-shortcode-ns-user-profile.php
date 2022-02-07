<?php

extract( shortcode_atts( array(
), $atts ) );


$user = wp_get_current_user();
$permalink = new UM_Permalinks;
$user_slug = $permalink->profile_slug($user->display_name, $user->first_name, $user->last_name);

?>

<a href="<?php echo home_url();?>/photographer/<?php echo $user_slug;?>/">
	<?php echo home_url();?>/photographer/<?php echo $user_slug;?>
</a>

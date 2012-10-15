<?php
$style_url = get_bloginfo('stylesheet_directory');
$app_url = get_bloginfo('url');

// Viewer
global $current_user;
get_currentuserinfo();
$user_info = get_userdata($current_user->ID);

// City
global $user_city;
$user_city = get_user_meta($user_info->ID,'user_city',true);
echo $user_city;

// Author
$auth_id = $post->post_author;
$user_info = get_userdata($auth_id);
$displayname = $user_info->first_name.' '.$user_info->last_name;

// Get city info
$post_cities = wp_get_post_terms($post->ID,'nh_cities');
$term = term_exists($user_city, 'nh_cities');
foreach ($post_cities as $tmp_city) {
	$cities[] = $tmp_city->name;
}
var_dump($post_cities);
	if ($user_city AND in_array($user_city,$cities) OR !$user_city AND in_array('Any City',$cities)) {
?>
<div id="sidebar-int" class="sidebar-nh">	

sidebar
</div>

	
<?php
}
?>
</div><!--/ sidebar-->
<?php endif;?>
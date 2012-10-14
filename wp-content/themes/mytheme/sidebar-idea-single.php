<?php
$style_url = get_bloginfo('stylesheet_directory');
$app_url = get_bloginfo('url');
global $current_user;
get_currentuserinfo();
$user_info = get_userdata($current_user->ID);
global $user_city;
$user_city = get_user_meta($user_info->ID,'user_city',true);

$auth_id = $post->post_author;
$user_info = get_userdata($auth_id);
$displayname = $user_info->first_name.' '.$user_info->last_name;
?>
<div id="sidebar-int" class="sidebar-nh">	
	<div class="widget-side">
		<div class="widget-copy">
			<div class="guide-details">
				<p class="gde-avatar">
<?php
$avatar_alt = 'Photo of '.$displayname;
$avatar = get_avatar($auth_id, '48','',$avatar_alt);
echo $avatar;
?>
				</p>
				<p class="gde-byline"><span class="byline">by</span> 
<?php 
echo '<a class="nhline" href="'.$app_url.'/author/'.$user_info->user_login.'" title="See '.$displayname.'&#39;s profile">';
echo $displayname;
echo '</a>';
?><br/>								
					<span class="byline">on</span> <?php the_date();?><br/>
							<span class="byline">for</span>
<?php
// limit city name display to user city + any city
$post_cities = wp_get_post_terms($post->ID,'nh_cities');
$term = term_exists($user_city, 'nh_cities');
if ($post_cities) {
	$count = count($post_cities);
	$j = $count - 1;	
	for ($i=0; $i<=$j; $i++) {
		$city = $post_cities[$i]->name;

		if ($city == $user_city OR $city == 'Any City') {
			$city_slug = strtolower($city);
			$city_slug = str_replace(' ','-',$city_slug);
			if ($city != 'Any City') {
				$city_name = substr($city,0,-3); //remove state
				$city_string = 'City of '.$city_name;
			}
			else {
				$city_name = $city;
				$city_string = $city_name;			
			}	
			// if only one city or if only city is any city	
			if ($count == 1 OR $term == 0) {
				echo '<a href="'.$app_url.'/cities/'.$city_slug.'" title="See content for '.$city_string.'">'.$city_string.'</a>';
			}
			elseif ($count > 1 AND $i < $j) {
				echo '<a href="'.$app_url.'/cities/'.$city_slug.'" title="See content for '.$city_string.'">'.$city_string.'</a>, ';
			}			
			elseif ($count > 1 AND $i == $j) {
					echo '<a href="'.$app_url.'/cities/'.$city_slug.'" title="See content for '.$city_string.'">'.$city_string.'</a>';
			}
		}
	}
}
?>					
				</p>	
				<ul class="gde-meta">
					<li><img src="<?php echo $style_url;?>/lib/timthumb.php?src=/images/icons/thumbsup.png&h=18&zc=1&at=t" alt="Number of votes"> 
<?php 
$tmp = nh_get_vote_count($post->ID); 
echo '<span class="nh-vote-count vote-'.$post->ID.'">'.$tmp.'</span>';
?>
					</li>					
<?php 
if (have_comments()) {
	echo '<li>';
	echo '<img src="'.$style_url.'/lib/timthumb.php?src=/images/icons/comment.png&h=16&zc=1&at=t" alt="Number of comments"> ';
	comments_number( '', '1', '%' );
	echo '</li>'; 
}
?>
					<li><img src="<?php echo $style_url;?>/lib/timthumb.php?src=/images/icons/eyeball.png&h=14&zc=1&at=t" alt="Number of views"> <?php if(function_exists('the_views')) { the_views(); } ?></li>												
				</ul>
			</div><!--guide details-->
		</div><!--widget copy-->
	</div><!-- widget-side-->
	
	<div class="widget-side">						
		<div class="widget-copy">
			<div class="guide-details" style="margin-top:.5em;">		
<?php 
if (nh_user_has_voted_post($current_user->ID, $post->ID)) {
	echo '<a style="font-style:italic;font-family:Georgia,serif;line-height:200% !important;" id="votedthis" title="See your other Votes" href="'.$app_url.'/author/'.$current_user->user_login.'" class="votedthis nhline">You voted for this</a>';
}
else {
//	echo '<span style="line-height:250% !important;">';
	nh_vote_it_link();
//	echo '</span>';
}
?>
<?php 
// Turn off function when working locally - only works hosted
echo '<div class="jetpack-idea-single">';
//echo sharing_display(); 
echo '</div>';
?>
				<p class="sharing-jp"><a class="nhline" href="#leavecomment" title="Add Your Comment">Add a Comment</a></p>

			</div><!--guide details-->
		</div><!--widget copy-->
	</div><!-- widget-side-->

	<div class="widget-side">			
		<h5 class="widget-title">Explore More In</h5>			
		<div class="widget-copy">
			<div class="guide-details">				
				<ul class="gde-actions">
<?php 
$parent_cat = get_cat_ID('ideas');
$cats = array(
	'orderby' => 'name',
	'order' => 'ASC',
	'child_of' => $parent_cat
);
$subcategories = get_categories($cats);
foreach($subcategories as $subcategory) {
	echo '<li><a class="nhline" href="' . get_category_link( $subcategory->term_id ) . '" title="View all ideas in '.$subcategory->name.'">'.$subcategory->name.'</a> </li> ';
}
?>					
				</ul>
			</div><!--/guide details-->
		</div><!--widget copy-->
	</div><!-- widget-side-->	
</div><!--/ sidebar-->
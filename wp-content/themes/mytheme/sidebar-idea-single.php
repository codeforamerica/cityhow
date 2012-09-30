<?php
$style_url = get_bloginfo('stylesheet_directory');
$app_url = get_bloginfo('url');
global $current_user;
get_currentuserinfo();
$auth_id = $post->post_author;

$user_info = get_userdata($auth_id);
//$nh_author_slug = $nh_author->user_login;
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
//$nh_user_photo_url = nh_get_avatar_url($nh_avatar);
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
$idea_city = get_post_meta($post->ID,'nh_idea_city',true);
$nh_cities = get_terms('nh_cities');
$term = term_exists($idea_city, 'nh_cities');
$city = substr($idea_city,0,-3); //remove state	
// If idea city is an official city
if ($term !== 0 && $term !== null) {
	$term_id = $term['term_id'];
	$term_data = get_term_by('id',$term_id,'nh_cities');
	echo '<a href="'.$app_url.'/cities/'.$term_data->slug.'" title="See other CityHow content in '.$city.'">City of '.$city.'</a>';
}
elseif ($term == 0 && $term == null) {
	echo $city;
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
echo sharing_display(); 
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
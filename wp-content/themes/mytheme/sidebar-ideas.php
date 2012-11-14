<?php
$style_url = get_bloginfo('stylesheet_directory');
$app_url = get_bloginfo('url');
global $current_user;
get_currentuserinfo();
?>
<div id="sidebar-nh" class="sidebar-nh">
	<div class="widget-side">
		<h5 class="widget-title">Explore Ideas + Suggestions</h5>
		<div class="widget-copy">
			<ul class="bullets">
<?php
$fdbk_cat = get_cat_ID('ideas');
$args = array('child_of' => $fdbk_cat);
$categories = get_categories($args);
foreach ($categories as $cat) {
	$catcount = $cat->count;
?>				
				<li class="bullets">
<?php
if ($cat->slug == 'content-ideas' AND $catcount > 0) {
	echo '<a class="nhline" href="'.$app_url.'/ideas/'.$cat->slug.'" title="View '.$cat->name.'">Content ideas for CityHow Guides</a>';
}
elseif ($cat->slug == 'feature-ideas' AND $catcount > 0) {
	echo '<a class="nhline" href="'.$app_url.'/ideas/'.$cat->slug.'" title="View '.$cat->name.'">Ideas for Features</a>';
}
elseif ($cat->slug == 'questions' AND $catcount > 0) {
	echo '<a class="nhline" href="'.$app_url.'/ideas/'.$cat->slug.'" title="View '.$cat->name.'">Your Questions</a>';
}
?>					
</li>
<?php } ?>				
			</ul>
		</div>			
	</div><!--/ widget-->
<?php if (is_user_logged_in()) : ?>	
	<div class="widget-side">
		<h5 class="widget-title">Share Your Knowledge</h5>
		<div class="widget-copy">
			<ul class="bullets">				
				<li class="bullets">
					<a class="nhline" href="<?php echo $app_url;?>/create-guide" title="Create a CityHow Guide">Create a CityHow Guide</a>
				</li>
			</ul>
		</div>			
	</div><!--/ widget-->
<?php else: ?>
	<div class="widget-side">
		<h5 class="widget-title">Sign In to see your city's content</h5>
		<div class="widget-copy">	
			<div class="sidebar-buttons">			
				<a href="<?php echo $app_url;?>/signin" title="Sign In to CityHow"><button class="nh-btn-blue-med btn-fixed">Sign In to CityHow</button></a>
			</div>
			<div class="sidebar-buttons">
				<a href="<?php echo $app_url;?>/register" title="Create an account"><button class="nh-btn-blue-med btn-fixed">Create an Account</button></a>
			</div>
		</div><!--/ widget copy-->
	</div><!--/ widget-->			
<?php endif;?>
</div><!--/ sidebar-->
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
?>				
				<li class="bullets">
<?php
if ($cat->slug == 'content-ideas') {
	echo '<a class="nhline" href="'.$app_url.'/ideas/'.$cat->slug.'" title="View '.$cat->name.'">Content ideas for CityHow Guides</a>';
}
elseif ($cat->slug == 'feature-ideas') {
	echo '<a class="nhline" href="'.$app_url.'/ideas/'.$cat->slug.'" title="View '.$cat->name.'">Ideas for Features</a>';
}
elseif ($cat->slug == 'questions') {
	echo '<a class="nhline" href="'.$app_url.'/ideas/'.$cat->slug.'" title="View '.$cat->name.'">Your Questions</a>';
}
?>					
</li>
<?php } ?>				
			</ul>
		</div>			
	</div><!--/ widget-->
	
	<div class="widget-side">
		<h5 class="widget-title">Share Your Knowledge</h5>
		<div class="widget-copy">
			<ul class="bullets">				
				<li class="bullets">
					<a class="nhline" href="<?php echo $app_url;?>/create-guide" title="Create a CityHow Guide">Create a Guide</a>
				</li>
			</ul>
		</div>			
	</div><!--/ widget-->		

</div><!--/ sidebar-->
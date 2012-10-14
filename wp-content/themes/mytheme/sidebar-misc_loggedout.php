<?php
$style_url = get_bloginfo('stylesheet_directory');
$app_url = get_bloginfo('url');
?>
<div id="sidebar-nh" class="sidebar-nh">	

	<div class="widget-side">
		<h5 class="widget-title">Sign In to see your city's content</h5>
		<div class="widget-copy">
			
			<div class="sidebar-buttons">			
				<a href="<?php echo $app_url;?>/signin" title="Sign In now"><button class="nh-btn-blue-med btn-fixed">Sign In to CityHow</button></a>
			</div>

			<div class="sidebar-buttons">
				<a href="<?php echo $app_url;?>/register" title="Create an account"><button class="nh-btn-blue-med btn-fixed">Create an Account</button></a>
			</div>

		</div><!--/ widget copy-->
	</div><!--/ widget-->		

</div><!--/ sidebar-->

<?php
$style_url = get_bloginfo('stylesheet_directory');
$app_url = get_bloginfo('url');
?>
<div id="sidebar-nh" class="sidebar-nh">	

	<div class="widget-side">
		<h5 class="widget-title">Got your password?</h5>
		<div class="widget-copy">
			<ul class="bullets">
				<li class="bullets"><a class="nhline" href="<?php echo $app_url;?>/signin" title="Sign In to CityHow">Sign In to CityHow</a></li>
			</ul>
		</div>			
	</div><!--/ widget-->
	
	<div class="widget-side">
		<h5 class="widget-title">Create an Account</h5>
		<div class="widget-copy">
			<ul class="bullets">
				<li class="bullets"><a class="nhline" href="<?php echo $app_url;?>/register" title="Create an account">Create an Account</a></li>
			</ul>
		</div>			
	</div><!--/ widget-->	
						
<?php //include(STYLESHEETPATH.'/include_about_nhow.php');?>				

</div><!--/ sidebar-->
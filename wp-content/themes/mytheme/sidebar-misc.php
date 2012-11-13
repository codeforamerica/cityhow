<?php
$style_url = get_bloginfo('stylesheet_directory');
$app_url = get_bloginfo('url');
?>
<div id="sidebar-nh" class="sidebar-nh">	
<?php
if (is_user_logged_in()) :
?>
	<div class="widget-side">
		<h5 class="widget-title">Help Make CityHow Better</h5>
		<div class="widget-copy">
			
			<div class="sidebar-buttons">			
				<a href="<?php echo $app_url;?>/add-idea" title="Add your idea"><button class="nh-btn-blue-med btn-fixed">Add an Idea for a Guide</button></a>
				<p>Help decide what content should be part of CityHow for your city.</p>
			</div>

			<div class="sidebar-buttons">
				<a href="<?php echo $app_url;?>/create-guide" title="Create a CityHow Guide"><button class="nh-btn-blue-med btn-fixed">Create a CityHow Guide</button></a>
				<p>Share what you know about working in city government with others.</p>
			</div>

		</div><!--/ widget copy-->
	</div><!--/ widget-->	
<?php
else :
?>	
	<div class="widget-side">
		<h5 class="widget-title">Sign In to see your city's content</h5>				
		<div class="widget-copy">		
			<div class="sidebar-buttons"><a href="<?php echo $app_url;?>/signin" title="Sign In to CityHow"><button class="nh-btn-blue-med btn-fixed">Sign In to CityHow</button></a></div>
			<div class="sidebar-buttons"><a href="<?php echo $app_url;?>/register" title="Create an account"><button class="nh-btn-blue-med btn-fixed">Create an Account</button></a></div>	
		</div><!--/ widget copy-->
	</div><!--/ widget-->
<?php
endif;
?>
</div><!--/ sidebar-->

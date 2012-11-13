<?php
$style_url = get_bloginfo('stylesheet_directory');
$app_url = get_bloginfo('url');
?>
<div id="sidebar-nh" class="sidebar-nh">	

	<div class="widget-side">
		<h5 class="widget-title">About CityHow</h5>
		<div class="widget-copy">
			<ul class="bullets">
				<li class="bullets"><a class="nhline" href="<?php echo $app_url;?>/about" title="Read about CityHow">About CityHow</a></li>

				<li class="bullets"><a class="nhline" href="<?php echo $app_url;?>/contact" title="Email CityHow">Contact us</a></li>									
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
					<li class="bullets">
						<a class="nhline" href="<?php echo $app_url;?>/add-idea" title="Add an Idea for a Guide">Add an Idea for a Guide</a>
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
		
		<div class="widget-side">
			<h5 class="widget-title">Get CityHow for Your City</h5>
			<div class="widget-copy">	
				<div class="sidebar-buttons">			
					<a href="<?php echo $app_url;?>/contact" title="Contact Us"><button class="nh-btn-blue-med btn-fixed">Request CityHow</button></a>
				</div>
			</div><!--/ widget copy-->
		</div><!--/ widget-->
							
	<?php endif;?>

</div><!--/ sidebar-->

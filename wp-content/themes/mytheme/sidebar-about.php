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

<?php
if (!is_user_logged_in()) :
?>
	<div class="widget-side">
		<div class="widget-buttons" style="margin-top:.25em !important;margin-bottom:1em !important;">
		<h5 class="widget-title">Share Your Knowledge</h5>
			<p style="margin-top:1em;"><a title="Create a Guide" href="<?php echo $app_url;?>/create-guide" class="nh-btn-blue">Create a Guide</a></p>
			<p style="margin-top:1.75em;"><a title="Explore CityHow Guides" href="<?php echo $app_url;?>/guides" class="nh-btn-blue">Start Exploring</a></p>
		</div>
	</div><!--/ widget-->	
<?php endif; ?>
</div><!--/ sidebar-->

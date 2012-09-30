<?php /* Template Name: page-about */ ?>
<?php get_header(); ?>
<div class="row-fluid row-breadcrumbs">
	<div id="nhbreadcrumb">
<?php nhow_breadcrumb(); ?>
	</div>
</div>

<div class="row-fluid row-content">	
	<div class="wrapper">
		<div id="main">
			<div id="content" class="about">
				<h3 class="page-title">CityHow — making it easy to find and share information about working for the City of Philadelphia.</h3>

<p>Brought to you by <a class="nhline" title="Go to Code for America" href="http://www.codeforamerica.org" target="_blank">Code for America</a> and the <a class="nhline" title="Go to City of Philadelphia" href="http://www.phila.gov" target="_blank">City of Philadelphia</a>, CityHow is a place to collect and share knowledge about work topics like booking a conference room in City Hall or signing up for a City Zipcar account.</p>

<p>A CityHow Guide can be about anything you think would be useful to other people working for the City of Philadelphia. Short or long, <strong>if it's something you know how to do, it's probably something other people want to know how to do. So share your CityHow.</strong></p>

<p style="text-align: center;"><a style="padding: 0 2em 0 1em;" title="Go to Code for America" href="http://www.codeforamerica.org" target="_blank"><img src="<?php echo $style_url;?>/images/logo_cfa_color.png" alt="Code for America logo" /></a> <a style="padding: 0 1em 0 2em;" title="Go to City of Philadelphia" href="http://www.phila.gov" target="_blank"><img src="<?php echo $style_url;?>/images/logo_phl_color.png" alt="City of Philadelphia logo" /></a></p>
		
			</div><!--/ content-->

<?php get_sidebar('about');?>
			
		</div><!--/ main-->
	</div><!--/ wrapper-->
</div><!--/ row-content-->
<?php get_footer(); ?>
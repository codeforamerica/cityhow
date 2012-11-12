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
				<h3 class="page-title">CityHow makes it easy to find and share information about working for the City of <?php $user_city_name = substr($user_city, 0, -3);echo $user_city_name;?>.</h3>
	
<?php 
if (have_posts()) :
while (have_posts()) : 
the_post();
the_content();
endwhile;
endif;
?>

<p style="text-align: center;"><a style="padding: 0 2em 0 1em;" title="Go to Code for America" href="http://www.codeforamerica.org" target="_blank"><img src="<?php echo $style_url;?>/images/logo_cfa_color.png" alt="Code for America logo" /></a> <a style="padding: 0 1em 0 2em;" title="Go to City of Philadelphia" href="http://www.phila.gov" target="_blank"><img src="<?php echo $style_url;?>/images/logo_phl_color.png" alt="City of Philadelphia logo" /></a></p>
		
			</div><!--/ content-->

<?php get_sidebar('about');?>
			
		</div><!--/ main-->
	</div><!--/ wrapper-->
</div><!--/ row-content-->
<?php get_footer(); ?>
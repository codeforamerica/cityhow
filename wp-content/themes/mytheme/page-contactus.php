<?php /* Template Name: page-contactus */ ?>
<?php get_header(); ?>
<div class="row-fluid row-breadcrumbs">
	<div id="nhbreadcrumb">
<?php nhow_breadcrumb(); ?>
	</div>
</div>

<div class="row-fluid row-content">	
	<div class="wrapper">
		<div id="main">
			<div id="content">
				<h3 class="page-title">Contact Us</h3>
				
				<p>Send us a message by filling out the form below, and we'll get in touch shortly. Thank you.</p>
<?php
if ($_POST['frm_action'] == 'create') {}
else {
?>	

<?php
if (have_posts()) :
while (have_posts()) :
the_post();
the_content();
endwhile;
endif;
?>

<?php } ?>	

<div id="contactus"><?php echo do_shortcode('[formidable id=9]');?>
</div>				

			</div><!--/ content-->

<?php get_sidebar('about');?>
					
		</div><!--/ main-->
	</div><!--/ content-->
</div><!--/ row-content-->
<?php get_footer(); ?>
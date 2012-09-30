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
<h3 class="page-title"><?php the_title();?></h3>

<?php
$are_there_steps = get_post_meta($post->ID,'step-title-01',true);
?>				
<div class="tabbable">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab1" data-toggle="tab">Summary</a></li>
<?php if ($are_there_steps) { ?>		
		<li><a href="#tab2" data-toggle="tab">Step-by-Step</a></li>
<?php } ?>
	</ul>

	<div class="tab-content">
		<div class="tab-pane tab-pane-guide active" id="tab1">	
<?php 
if ( have_posts() ) :
while ( have_posts() ) : the_post(); 
$nh_author = get_userdata($curauth->ID);
$nhow_post_id = $post->ID;
?>			

			<div class-"guide-overview"><p>
<?php 
$tmpcontent = get_the_content();
$guide_summary = preg_replace('#\R+#', '</p><p>', $tmpcontent);
echo make_clickable($guide_summary);
?>	
			</p></div>
<?php
$img_feature_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
?>
			<div class="single-guide-img overview">
				<div class="carousel-inner"><img src="<?php echo $style_url;?>/lib/timthumb.php?src=<?php echo $img_feature_src[0];?>&h=300&q=95&zc=1&at=t" alt="Photo of <?php the_title();?>" />
				</div>
			</div>
		</div><!--/ tab 1-->

		<div class="tab-pane tab-pane-guide" id="tab2">
			<ul class="guide-steps">
<?php
// steps limited to 15 for now
$step_total = '15'; 
// display step number counter
$j = 1; 
for ($i=1;$i <= $step_total;$i++) {
	// Align w the padded number from db
	$i = str_pad($i, 2, "0", STR_PAD_LEFT);
	// Titles
	$step_t = 'step-title-'.$i;
	$step_title = get_post_meta($post->ID,$step_t,true);
	// Descriptions
	$step_d = 'step-description-'.$i;
	$step_description = get_post_meta($post->ID,$step_d,true);
	//Images
	$step_m = 'step-media-'.$i;
	$step_media_id = get_post_meta($post->ID,$step_m,true);	
	$step_media_url = wp_get_attachment_url($step_media_id);	
	$step_media_src = wp_get_attachment_image_src($step_media_id);
	
	if (!empty($step_title)) {
		echo '<li class="guide-step">';		
		echo '<p class="guide-step-number">'.$j.'</p>';	
		echo '<div class="guide-step-title"><h4>'.$step_title.'</h4>';	

		if (!empty($step_description)) {
			$step_description = preg_replace('#\R+#', '</p><p>',$step_description);
			echo '<p>'.make_clickable($step_description).'</p></div>'; 
		}
		if (!empty($step_media_id)) {
			echo '<div class="single-guide-img">';
			$mime = get_post_mime_type($step_media_id);			

			if ($mime == 'application/pdf') {
				echo '<div class="carousel-inner"><a href="'.$step_media_url.'" title="View this PDF" target="_blank"><img class="guide-attach" src="'.$style_url.'/lib/timthumb.php?src='.$style_url.'/images/icons/media/document.png&w=46&h=60&q=95" alt="PDF related to this Step" /></a></div>';
			}

			elseif ($mime == 'image/png' OR $mime == 'image/jpeg' OR $mime == 'image/gif') {
				echo '<div class="carousel-inner"><img src="'.$style_url.'/lib/timthumb.php?src='.$step_media_url.'&h=280&q=95&zc=1&at=t" alt="Photo of '.$step_title.'" /></div>';
			}

			elseif ($mime == 'application/vnd.oasis.opendocument.text' OR $mime == 'application/msword') {
				echo '<div class="carousel-inner"><a href="'.$step_media_url.'" title="Download this Word document" target="_blank"><img class="guide-attach" src="'.$style_url.'/lib/timthumb.php?src='.$style_url.'/images/icons/media/text.png&w=46&h=60&q=95" alt="Word document related to this Step" /></a></div>';
			}
// Do captions later	
?>
		</li>
<?php
		}
//		else {
//			echo '<p>Sorry, there are no steps yet for this CityHow Guide.</p>';			
//		}
		$j++;
	}
}	
?>
			</ul>
		</div><!--/ tab 2-->
		
	</div><!-- / tab content-->
</div><!-- / tabbable-->

<?php
if (!is_preview()) {
?>
<div id="leavecomment" class="nhow-comments">
<?php comments_template( '', true ); ?>
</div><!-- / comments-->				
<?php
}
?>
<?php
endwhile;
endif;
?>			

			</div><!--/ content -->
<?php 
if (!is_preview()) {
	get_sidebar('guide-single');	
}
?>			
		</div><!--/ main-->
	</div><!--/ wrapper-->
</div><!--/ row-fluid-->
<?php get_footer(); ?>
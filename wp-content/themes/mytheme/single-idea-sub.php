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
if (have_posts()) {
	while (have_posts()) { 
		the_post(); 
		$post_cities = wp_get_post_terms($post->ID,'nh_cities');
		foreach ($post_cities as $tmp_city) {
			$cities[] = $tmp_city->name;
		}

		if (is_user_logged_in()) {
			if (in_array($user_city,$cities) OR in_array('Any City',$cities)) {
?>
				<div><?php the_content();?>
<?php
				$guide_answer = get_post_meta($post->ID,'gde-answer',true);
				if ($guide_answer) {
					echo '<p class="comment-meta"><span class="answered"><a href="'.$guide_answer.'" title="Read the answer">Read the answer!</a></span></p>';
				}
?>	
				</div>
<?php
				if (!is_preview()) { 
					echo '<div id="leavecomment" class="nhow-comments">';
					echo comments_template( '', true );
					echo '</div><!-- / comments-->';				
				}
			} // if user city or any city in array
			elseif (!in_array($user_city,$cities) OR !in_array('Any City',$cities)) {
				echo '<p>Sorry ... content for this city is available only to employees of the City of ';
				foreach ($post_cities as $city) {
					$city_name = $city->name;
					$city_name = substr($city_name,0,-3);
					echo $city_name;
				}
				echo '</p>';
			}
		} // end if user logged in
		elseif (!is_user_logged_in()) {
			if (in_array('Any City',$cities)) {
?>
				<div><?php the_content();?>
<?php
				$guide_answer = get_post_meta($post->ID,'gde-answer',true);
				if ($guide_answer) {
					echo '<p class="comment-meta"><span class="answered"><a href="'.$guide_answer.'" title="Read the answer">Read the answer!</a></span></p>';
				}
?>	
				</div>
<?php
				if (!is_preview()) { 
					echo '<div id="leavecomment" class="nhow-comments">';
					echo comments_template( '', true );
					echo '</div><!-- / comments-->';				
				}
			} // end if any city in array
			elseif (!in_array('Any City',$cities)) {
				echo '<p>Sorry ... content for this city is available only to employees of the City of ';
				foreach ($post_cities as $city) {
					$city_name = $city->name;
					$city_name = substr($city_name,0,-3);
					echo $city_name;
				}
				echo '</p>';
			}
		}
?>
			</div><!--/ content -->
<?php 
		if (!is_preview()) {
			get_sidebar('idea-single2');	
		}
	} // end while posts
} // end if posts
?>			
		</div><!--/ main-->
	</div><!--/ wrapper-->
</div><!--/ row-fluid-->
<?php get_footer(); ?>
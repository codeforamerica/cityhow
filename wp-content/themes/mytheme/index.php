<?php get_header(); ?>
<div class="row-fluid row-content">
	<div id="beta"></div>
	<div class="wrapper">
		<div id="main">

<?php if (!is_user_logged_in()) : ?>	
			<div class="row-fluid home-promo">
				<div id="site-promo" class="span7">
					<h1 class="promo-copy">CityHow makes it easy to find and share information about working for city government.</h1> 
					<h2 class="promo-copy">Browse Guides on topics submitted by employees, and create your own Guides to help them.</h2>
				</div>

				<div id="site-promo-list" class="span5">
					<h4 class="loggedout">Please sign in to see content for your city</h4>
					<p><a class="promo_suggest" href="<?php echo $app_url;?>/signin" title="Sign In to CityHow"><button class="nh-btn-blue btn-fixed">Sign In to CityHow</button></a></p>

					<p><a class="promo_suggest" href="<?php echo $app_url;?>/register" title="Create a CityHow account"><button class="nh-btn-blue btn-fixed">Create an Account</button></a></p>
				</div>
			</div><!--/ row-fluid inner-->														
<?php else : ?>				
			<div class="row-fluid home-promo">	
				<div id="site-promo" class="span7">
					<h1 class="promo-copy">CityHow makes it easy to find and share information about working for the City of 
<?php 
$user_city_name = substr($user_city, 0, -3);
echo $user_city_name;
?>.</h1> 
					<h2 class="promo-copy">Browse Guides on topics submitted by employees, and create your own Guides to help them.</h2>
				</div>

				<div id="site-promo-list" class="span5">
					<h4>Share Your Knowledge with other City Employees</h4>
					<p><span>1.</span>&nbsp;&nbsp;<a class="promo_suggest" href="<?php echo $app_url;?>/add-idea" title="Tell us about the content you want, and we'll make getting it a priority."><button class="nh-btn-blue btn-fixed">Add an Idea for a Guide</button></a></p>

					<p><span>2.</span>&nbsp;&nbsp;<a class="promo_suggest" href="<?php echo $app_url;?>/create-guide" title="Share Your Knowledge -- Create a CityHow Guide and share what you know with others."><button class="nh-btn-blue btn-fixed">Create a CityHow Guide</button></a></p>
				</div>
			</div><!--/ row-fluid inner-->		
<?php endif; ?>	

			<div class="row-fluid">
				<div class="span12">
					<div class="home-featured">
						<h5 class="widget-title">
<?php
if (is_user_logged_in()) {
	echo 'Explore Guides for the City of '.$user_city_name;
}
else {
	echo 'Explore Guides for Any City&nbsp;&nbsp;&nbsp;<span class="byline" style="float:right;padding-right:1.75em;font-size:93%;text-transform:none !important;font-weight:normal !important;word-spacing:0 !important;"><a class="nhline" href="'.$app_url.'/signin">sign in</a> to see Guides for your city</span>';
}
?>
						</h5>
						<ul class="list-guides list-guides-home">
<?php
$user_city_slug = strtolower($user_city);
$user_city_slug = str_replace(' ','-',$user_city_slug);

if (is_user_logged_in()) {
	$user_city_slug = $user_city_slug;
}
elseif (!is_user_logged_in()) {
		$user_city_slug = '';
}
	// show sticky posts in groups of 4
	// that match the users city or any city
	$sticky_ids = get_option('sticky_posts');
	if(count($sticky_ids) != 0) {
		$sticky_args = array(
			'posts_per_page' => 4,
		    'orderby' => 'date',
			'order' => DESC,
	        'post__in' => $sticky_ids,
			'tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => array( 'guides' )
				),
				array(
					'taxonomy' => 'nh_cities',
					'field' => 'slug',
					'terms' => array( $user_city_slug,'any-city' )
				)	
			)
		);	
	    $sticky_query = new WP_Query($sticky_args);
		while ($sticky_query->have_posts()) : $sticky_query->the_post();
		
			$imgSrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
			$post_cities = wp_get_post_terms($post->ID,'nh_cities');
			$term = array_pop($post_cities);			
			
			echo '<li class="guides-list" id="post-'.$post->ID.'"><a rel="bookmark" title="See '.get_the_title().'" href="'.get_permalink().'">';

			echo '<img  src="'.$style_url.'/lib/timthumb.php?src='.$imgSrc[0].'&w=184&h=115&zc=1&a=tl&q=95" alt="Photo from '.get_the_title().'" />';

			echo '<div class="home-caption">';
			$pad = ' ...';
			$pic_title = trim_by_chars(get_the_title(),'50',$pad);
			echo '<p><a class="nhline link-other" rel="bookmark" title="See '.get_the_title().'" href="'.get_permalink().'">'.$pic_title.'</a></p>';
			if ($term->name) {
			echo '<p class="city-caption">'.$term->name.'</p>';	
			}
			else {
				echo '<p class="city-caption">Any City</p>';
			}
			echo '</div>';
			echo '</li>';
		endwhile;
	wp_reset_query();	
	}
	// if no sticky posts
	else {
		// show posts that match the users city or any city
		$normal_args = array(
			'posts_per_page' => 4,
		    'orderby' => 'date',
			'order' => DESC,
			'tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => array( 'guides' )
				),
				array(
					'taxonomy' => 'nh_cities',
					'field' => 'slug',
					'terms' => array( $user_city_slug,'any-city' )
				)	
			)
		);
		$normal_query = new WP_Query($normal_args);
		while ($normal_query->have_posts()) : $normal_query->the_post();
			$imgSrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
			$post_cities = wp_get_post_terms($post->ID,'nh_cities');
			$term = array_pop($post_cities);
					
			echo '<li class="guides-list" id="post-'.$post->ID.'"><a rel="bookmark" title="See '.get_the_title().'" href="'.get_permalink().'">';

			echo '<img class="guide_image_bg" src="'.$style_url.'/lib/timthumb.php?src='.$imgSrc[0].'&w=184&h=115&zc=1&a=tl&q=95" alt="Photo from '.get_the_title().'" /></a>';
		
			echo '<div class="home-caption">';
			$pad = ' ...';
			$pic_title = trim_by_chars(get_the_title(),'50',$pad);
			echo '<p><a class="nhline link-other" rel="bookmark" title="See '.get_the_title().'" href="'.get_permalink().'">'.$pic_title.'</a></p>';
			if ($term->name) {
			echo '<p class="city-caption">'.$term->name.'</p>';	
			}
			else {
				echo '<p class="city-caption">Any City</p>';
			}

			echo '</div>';
			echo '</li>';
		endwhile;
	wp_reset_query();
	}
?>

<?php
if (is_user_logged_in()) {
	echo '<div class="see_all"><a class="nhline" href="'.$app_url.'/guides" title="">See more Guides &#187;</a></div>';
}
else {
	echo '<div class="see_all"><a class="nhline" href="'.$app_url.'/guides" title="">See more Guides &#187;</a></div>';
}
?>
						</ul>
					</div>
				</div><!--/ span12-->
			</div><!--/ row-fluid inner-->
		
			<div class="row-fluid home-combo">
				<div class="span6 home-ideas">
						
<?php if (is_user_logged_in()) : ?>

<h5 class="widget-title">Latest Ideas for CityHow Guides</h5>	
<p><a id="addfdbk" title="Add Your Idea" rel="tooltip" data-placement="bottom" data-title="" class="nh-btn-blue" href="<?php echo $app_url;?>/add-idea" >Add Your Idea</a></p>

<?php else : ?>

<h5 class="widget-title">Latest Ideas for CityHow Guides</h5>
<p style="float:right;padding-top:0 !important;"><span class="byline" style="font-size:93%;text-transform:none !important;font-weight:normal !important;word-spacing:0 !important;"><a class="nhline" href="'.$app_url.'/signin">sign in</a> to see Ideas for your city</span></p>

<?php endif; ?>
						<ul class="list-ideas list-ideas-home">							
<?php
$fdbk_sub_cat = get_cat_ID('ideas');
$fdbk_sub_args = array(
	'post_status' => 'publish',
	'orderby' => 'date',
	'order' => DESC,
	'posts_per_page' => '5',
	'tax_query' => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'category',
			'field' => 'slug',
			'terms' => array( 'ideas' )
		),
		array(
			'taxonomy' => 'nh_cities',
			'field' => 'slug',
			'terms' => array( $user_city_slug,'any-city' )
		)	
	)
);
$fdbk_sub_query = new WP_Query($fdbk_sub_args);
if ($fdbk_sub_query->have_posts()) :
while($fdbk_sub_query->have_posts()) :
$fdbk_sub_query->the_post();	
?>					

<li class="ideas-list"><a class="nhline" href="<?php echo get_permalink();?>" title="See <?php echo the_title();?>"><?php echo the_title();?></a>&nbsp;&nbsp;
	
<span class="meta meta-small">

<?php
$guide_answer = get_post_meta($post->ID,'gde-answer',true);
if ($guide_answer) {
	echo '<span class="answered"><a class="nhline" href="'.$guide_answer.'" title="View this Guide">Read the answer!</a></span>';
}
else {
	echo '<span class="byline">added</span> '.get_the_date().'</span>';
}
?>		

	</span>
</li>	

<?php 
endwhile;
endif;
wp_reset_query();
?>								

<li class="ideas-list"><a class="nhline" href="<?php echo $app_url;?>/ideas" title="See all the ideas">See more Ideas &#187;</a></li>
						</ul>						
				</div><!--/ span4-->

				<div class="span6 home-about">
					<h5 class="widget-title">About CityHow</h5>
<?php
$page_id = get_ID_by_slug('about');
$post = get_post($page_id); 
$content = $post->post_content;
$content = strip_tags($content,'<p>,<a>');
$content = trim_by_words($content,'98',nh_continue_reading_link());
echo $content;
?>					
				</div>
			</div><!--/ row-fluid inner-->

<?php //endif; // end if user logged in ?>			

		</div><!--/ main-->		
	</div><!--/ wrapper-->	
</div><!--/ row-fluid-->
<?php get_footer();?>
<?php /* Template Name: page-nh_cities */ ?>
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
				<h3 class="page-title">Cities</h3>
	
				<div id="list-ideas">
					<ul class="list-ideas">			
<?php 
// limit list to User City + Any City
$guide_cat = get_category_id('guides');

$city_terms = get_terms('nh_cities');
foreach ($city_terms as $city_term) {
	$city_term = $city_term->name;
	if ($city_term == $user_city OR $city_term == 'Any City') {
		$cities[] = $city_term;
	}
}
foreach ($cities as $city) {
	if ($city != 'Any City') {
		$city_name = substr($city,0,-3); //remove state
	}
	else {
		$city_name = $city;
	}
	$city_slug = strtolower($city);
	$city_slug = str_replace(' ','-',$city_slug);
	$city_url = get_term_link($city,'nh_cities');

// get guide count per city per guide cat
$myquery = array(
	'posts_per_page' => -1,
	'post_status' => 'publish',
	'tax_query' => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'category',
			'field' => 'id',
			'terms' => array($guide_cat)
		),
		array(
			'taxonomy' => 'nh_cities',
			'field' => 'slug',
			'terms' => array($city_slug)
		)
	)
);

$city_guides = query_posts($myquery);
$count_city_guides = count($city_guides);

// get user count per city
	$users = $wpdb->get_results("SELECT * from nh_usermeta where meta_value = '".$city."' AND meta_key = 'user_city'");		
	$users_count = count($users);
	
// get idea count per city
	$ideas = $wpdb->get_results("SELECT * from nh_postmeta where meta_value = '".$city."' AND meta_key = 'nh_idea_city'");		
	$ideas_count = count($ideas);	

// show results	
	echo '<li class="nhline city-all">';
	echo '<a class="nhline" href="'.$city_url.'" title="View content for ';
	if ($city_slug != 'any-city') {
		echo 'City of ';
	}
	echo $city.'">';
	if ($city_slug != 'any-city') {
		echo 'City of ';
	}
	echo $city_name.'</a>';
	if ($posts) {
		if ($count_city_guides == '1') {
			echo '<span class="meta"><span class="byline">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$count_city_guides.'&nbsp;Guide</span></span>';
		}
		elseif ($count_city_guides > 1) {
			echo '<span class="meta"><span class="byline">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$count_city_guides.'&nbsp;Guides</span></span>';
		}
	}
	
	if ($ideas) {
		if ($ideas_count == '1') {
			echo '<span class="meta"><span class="byline">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$ideas_count.'&nbsp;Idea</span></span>';
		}
		elseif ($ideas_count > 1) {
			echo '<span class="meta"><span class="byline">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$ideas_count.'&nbsp;Ideas</span></span>';
		}
	}
		
	if ($users) {
		if ($users_count == '1') {
			echo '<span class="meta"><span class="byline">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$users_count.'&nbsp;User</span></span>';
		}
		elseif ($users_count > 1) {
			echo '<span class="meta"><span class="byline">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$users_count.'&nbsp;Users</span></span>';
		}
	}
	echo '</li>';

} // end foreach
?>
					</ul>			
				</div>
								
			</div><!--/ content-->
<?php
if (is_user_logged_in()) {
	get_sidebar('misc');
}
else {
	get_sidebar('misc_loggedout');
}
?>			
		</div><!--/ main-->
	</div><!--/ content-->
</div><!--/ row-content-->
<?php get_footer(); ?>
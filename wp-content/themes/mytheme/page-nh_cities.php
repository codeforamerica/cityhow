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
				<div class="intro-block noborder"><p>CityHow is currently available for these cities. 
<?php
if (!is_user_logged_in()) {
	echo 'If you&#39;d like CityHow for your city, <a href="<?php echo $app_url;?>/contact" title="Get CityHow for your city">contact us</a> and let us know.';
}
?>
</p></div>
	
				<div id="list-ideas">
					<ul class="list-ideas">			
<?php 
// dont allow access to cities that are not
// users city or Any City
$guide_cat = get_category_id('guides');
$idea_cat = get_category_id('ideas');
$city_terms = get_terms('nh_cities');

foreach ($city_terms as $city_term) {
	$city_term = $city_term->name;
	$cities[] = $city_term;
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
$guide_query = array(
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

// get idea count per city per idea cat
$idea_query = array(
	'posts_per_page' => -1,
	'post_status' => 'publish',
	'tax_query' => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'category',
			'field' => 'id',
			'terms' => array($idea_cat)
		),
		array(
			'taxonomy' => 'nh_cities',
			'field' => 'slug',
			'terms' => array($city_slug)
		)
	)
);

$city_guides = query_posts($guide_query);
$count_city_guides = count($city_guides);

$city_ideas = query_posts($idea_query);
$count_city_ideas = count($city_ideas);

// get user count per city
	$users = $wpdb->get_results("SELECT * from nh_usermeta where meta_value = '".$city."' AND meta_key = 'user_city'");		
	$users_count = count($users);

	echo '<li class="nhline city-all">';

// show results for any city
	if ($city != $user_city AND $city == 'Any City') {
		echo '<a class="nhline" href="'.$city_url.'" title="View content for '.$city_name.'">'.$city.'</a>';
	}
// show results for user city
	elseif ($city == $user_city AND $city != 'Any City') {
		echo '<a class="nhline" href="'.$city_url.'" title="View content for '.$city_name.'">City of '.$city.'</a>';
	}
// show results for not user city
	else {
		echo 'City of '.$city_name.' <span class="meta"><span class="byline">&nbsp;(visible to employees in '.$city_name.')</span></span>';
	}

	if ($city_guides AND $city == $user_city OR $city == 'Any City') {
		if ($count_city_guides == '1') {
			echo '<span class="meta"><span class="byline">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$count_city_guides.'&nbsp;Guide</span></span>';
		}
		elseif ($count_city_guides > 1) {
			echo '<span class="meta"><span class="byline">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$count_city_guides.'&nbsp;Guides</span></span>';
		}
	}
	
	if ($city_ideas AND $city == $user_city OR $city == 'Any City') {
		if ($count_city_ideas == '1') {
			echo '<span class="meta"><span class="byline">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$count_city_ideas.'&nbsp;Idea</span></span>';
		}
		elseif ($count_city_ideas > 1) {
			echo '<span class="meta"><span class="byline">&nbsp;&nbsp;&#8226;&nbsp;&nbsp;'.$count_city_ideas.'&nbsp;Ideas</span></span>';
		}
	}
		
	if ($users AND $city == $user_city OR $city == 'Any City') {
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
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
				
				<h3 class="page-title"><?php echo single_tag_title();?></h3>
				<div class="intro-block noborder">
<?php
if (is_user_logged_in()) {
	echo '<p>This Topic includes Guides and Ideas specific to your city government or generally applicable to any city.</p>';
}
else {
	echo '<p>Browse this Topic that CityHow users say is helpful for any city. Then <a href="<?php echo $app_url;?>/contact" title="Get CityHow for your city">contact us</a> if you&#39;d like CityHow for your city.</p>';
}
?>				
				</div>				
				<div id="list-fdbk">
					<ul class="list-fdbk">
<?php
// get all city names
/*
$city_terms = get_terms('nh_cities');
// find city names = user city or Any City
foreach ($city_terms as $city_term) {
	$city_term = $city_term->name;
	if ($city_term == $user_city OR $city_term == 'Any City') {
		$cities[] = $city_term;
	}
}
// remove state from city names
foreach ($cities as $city) {
	if ($city != 'Any City') {
		$city_name = substr($city,0,-3);
	}
	else {
		$city_name = $city;
	}	
}

$city_slug = strtolower($city);
$city_slug = str_replace(' ','-',$city_slug);
*/

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// get posts matching tag in user city and any city
$tag_args = array(
	'post_status' => 'publish',
	'orderby' => 'date',	
	'order' => DESC,
	'posts_per_page' => '20',
	'paged' => $paged,
	'tax_query' => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'post_tag',
			'field' => 'id',
			'terms' => $tag_id
		),
		array(
			'taxonomy' => 'nh_cities',
			'field' => 'slug',
			'terms' => array($user_city,'Any City')
		)
	)	
);
$tag_query = new WP_Query($tag_args);

//var_dump($tag_query);

if (!$tag_query->have_posts()) :
?>	
	<li class="fdbk-list">Sorry ... content for this Topic is available only to employees of city name here</li>

<?php else: ?>	

<?php while($tag_query->have_posts()) : $tag_query->the_post();?>	
	<li class="fdbk-list" id="post-<?php echo $post->ID; ?>"><strong><a href="<?php echo get_permalink();?>" title="See <?php echo the_title();?>"><?php echo the_title();?></a></strong>
		<div class="search-results">
<?php 
$tmp = get_the_content();
$new_content = strip_tags($tmp,'<p>');
$content_trimmed = trim_by_words($new_content,'20',nh_continue_reading_link());
echo '<p>'.$content_trimmed.'</p>';?>

<?php
$categories = get_the_category();
if ($categories) {
	echo '<p><span class="byline">in</span> ';
	foreach ($categories as $cat) {
		$cat_name = $cat->name;
		$cat_id = get_cat_ID($cat_name);
		$cat_link = get_category_link($cat_id);
		echo '<a href="'.$cat_link.'" title="See all '.$cat->name.'">';
		echo $cat->name;
		echo '</a>';
	}	
}
?>	
<?php
$post_cities = wp_get_post_terms($post->ID,'nh_cities');

// if user = logged out = no user city
$term = term_exists($user_city, 'nh_cities');

if (!empty($post_cities)) {
	$count = count($post_cities);
	echo ' + ';
	foreach ($post_cities as $city) {		
		if ($city->name == $user_city) {
			$new_city = substr($city->name,0,-3);
		}
		elseif ($city->name == 'Any City') {
			$new_city = $city->name;
		}
		
		if ($count == 1 OR $term == 0) {
			$city_string = '<a href="'.$app_url.'/cities/'.$city->slug.'" title="See content for '.$new_city.'">'.$new_city.'</a>';
		}
		elseif ($count > 1) {
			$city_string .= '<a href="'.$app_url.'/cities/'.$city->slug.'" title="See content for '.$new_city.'">'.$new_city.'</a>, ';
		}
	}
		echo rtrim($city_string, ', ');
}
?>	
			</p>
		</div>

<?php endwhile; 

$total_pages = $tag_query->max_num_pages;
if ($total_pages > 1){
  $current_page = max(1, get_query_var('paged'));
  echo paginate_links(array(
      'base' => get_pagenum_link(1) . '%_%',
      'format' => '/page/%#%',
      'current' => $current_page,
      'total' => $total_pages,
    ));
}
wp_reset_query();
endif;
?>
					</ul>
				</div>

			</div><!--/ content-->

<?php
if (is_user_logged_in()) {
	get_sidebar('misc_short');
}
else {
	get_sidebar('misc_loggedout');
}
?>
		</div><!--/ main-->
	</div><!--/ content-->
</div><!--/ row-content-->
<?php get_footer(); ?>
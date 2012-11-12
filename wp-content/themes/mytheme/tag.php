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
$user_city_name = substr($user_city,0,-3);
$user_city_slug = strtolower($user_city);
$user_city_slug = str_replace(' ','-',$user_city_slug);

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
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
			'terms' => array($user_city_slug,'any-city')
		)
	)	
);
$tag_query = new WP_Query($tag_args);
if (!$tag_query->have_posts()) :
?>	
	<li class="fdbk-list">Sorry, there is no public content matching this Topic right now.</li>

<?php else: ?>	

<?php while($tag_query->have_posts()) : $tag_query->the_post();?>	
	<li class="fdbk-list" id="post-<?php echo $post->ID; ?>"><strong><a href="<?php echo get_permalink();?>" title="View <?php echo the_title();?>"><?php echo the_title();?></a></strong>
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
		echo '<a class="nhline" href="'.$cat_link.'" title="See '.$cat->name.'">';
		echo $cat->name;
		echo '</a>';
	}
}
?>	
<?php
// get post cities
//$post_cities = wp_get_post_terms($post->ID,'nh_cities');

//find user city + get id
$user_city_terms = term_exists($user_city, 'nh_cities');
$user_city_id = $user_city_terms['term_id'];

// find Any City + get id
$any_city_terms = term_exists('Any City', 'nh_cities');
$any_city_id = $any_city_terms['term_id'];

// find post terms
$new_cities = get_the_terms($post->ID,'nh_cities');

// only keep user city and Any City
foreach($new_cities as $elementKey => $element) {
    foreach($element as $valueKey => $value) {
		if ($valueKey == 'name' AND $value != $user_city AND $value != 'Any City') {
			unset($new_cities[$elementKey]);
		}
    }
}

$new_user_city = $new_cities[$user_city_id]->name;
$user_city_slug = strtolower($new_user_city);
$new_user_city_slug = str_replace(' ','-',$user_city_slug);

$new_user_city_name = 'City of '.substr($new_user_city,0,-3);

$new_any_city = $new_cities[$any_city_id]->name;
$any_city_slug = strtolower($new_any_city);
$new_any_city_slug = str_replace(' ','-',$any_city_slug);

echo ' + ';

if ($new_any_city AND !$new_user_city) {
	$city_string = '<a href="'.$app_url.'/cities/'.$new_any_city_slug.'" title="See content for '.$new_any_city.'">'.$new_any_city.'</a>';
}

elseif ($new_any_city AND $new_user_city) {
	$city_string = '<a href="'.$app_url.'/cities/'.$new_any_city_slug.'" title="See content for '.$new_any_city.'">'.$new_any_city.'</a>, <a href="'.$app_url.'/cities/'.$new_user_city_slug.'" title="See content for '.$new_user_city_name.'">'.$new_user_city_name.'</a>';	
}

elseif (!$new_any_city AND $new_user_city) {
	$city_string = '<a href="'.$app_url.'/cities/'.$new_user_city_slug.'" title="See content for '.$new_user_city_name.'">'.$new_user_city_name.'</a>';
}
echo $city_string;
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
<?php get_sidebar('misc');?>
		</div><!--/ main-->
	</div><!--/ content-->
</div><!--/ row-content-->
<?php get_footer(); ?>
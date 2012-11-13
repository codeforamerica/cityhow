<?php //The template for displaying Search Results pages */ ?>
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
				<h3 class="page-title">
<?php
// limit search results to user city or Any City
echo 'Search Results for ';
$allsearch = &new WP_Query("s=$s&nh_cities=$user_city,Any-City&showposts=-1"); 
$key = wp_specialchars($s, 1); 
$count = $allsearch->post_count; 
echo '<span class="meta"><span class="byline">';
echo $key; 
echo ' ('.$count.')</span></span>';
wp_reset_query(); 
?> 
				</h3>				
				
				<div id="list-fdbk">
					<ul class="list-fdbk">
<?php 
if (have_posts()) : 
while (have_posts()) : 
the_post(); 
?>

<?php
//find user city + get id
$user_city_terms = term_exists($user_city, 'nh_cities');
$user_city_id = $user_city_terms['term_id'];

// find Any City + get id
$any_city_terms = term_exists('Any City', 'nh_cities');
$any_city_id = $any_city_terms['term_id'];

// find post terms + get id/name
$new_cities = get_the_terms($post->ID,'nh_cities');
foreach ($new_cities as $c) {
	$tmp_slug = strtolower($c->name);
	$tmp_slug = str_replace(' ','-',$tmp_slug);

	$other_city = get_term_by('slug',$tmp_slug,'nh_cities');	
	$other_city_id[] = $other_city->term_id;		

	$other_city_name[] = $other_city->name;
}

// if content IS user city or Any City
if (in_array($user_city_id,$other_city_id) OR in_array($any_city_id,$other_city_id)) : 
?>
	<li class="fdbk-list" id="post-<?php echo $post->ID; ?>"><strong><a href="<?php echo get_permalink();?>" title="View <?php echo the_title();?>"><?php echo the_title();?></a></strong>

	<div class="search-results">
<?php 
$tmp = get_the_content();
$new_content = strip_tags($tmp,'<p>');
$content_trimmed = trim_by_chars($new_content,'100',nh_continue_reading_link());
echo '<p>'.$content_trimmed.'</p>';

// Get post cats
$categories = get_the_category();
if ($categories) {
	echo '<p><span class="byline">in</span> ';
	foreach ($categories as $cat) {
		$cat_name = $cat->name;
		$cat_id = get_cat_ID($cat_name);
		$cat_link = get_category_link($cat_id);
		echo '<a href="'.$cat_link.'" title="View '.$cat->name.'">';
		echo $cat->name;
		echo '</a>';
	}	
}

// get post cities if user city or Any City
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

<?php 
endif;
endwhile;
else : 
?>
	<li class="fdbk-list" style="border-bottom:none;">Sorry ... nothing matched your search criteria.<br/>Please try again with some different keywords.</p><?php get_search_form(); ?></li>
					</ul>
<?php 
endif; 
?>
				</div>

			</div><!--/ content-->
<?php get_sidebar('misc');?>
		</div><!--/ main-->
	</div><!--/ content-->
</div><!--/ row-content-->
<?php get_footer(); ?>
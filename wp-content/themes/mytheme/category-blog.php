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
				<h3 class="page-title">CityHow Blog</h3>				
				
				<div id="list-fdbk">
					<ul class="list-fdbk">
<?php 
if (have_posts()) : 
while (have_posts()) : 
the_post(); 
?>
					<li class="fdbk-list" id="post-<?php echo $post->ID; ?>"><strong><a href="<?php echo get_permalink();?>" title="View <?php echo the_title();?>"><?php echo the_title();?></a></strong>

					<div class="search-results">
<?php 
$tmp = get_the_content();
$new_content = strip_tags($tmp,'<p>');
$content_trimmed = trim_by_chars($new_content,'100',nh_continue_reading_link());
echo '<p>'.$content_trimmed.'</p>';

// Get post cats
echo '<p><span class="byline">in</span> ';
$categories = get_the_category();
if ($categories) {
	foreach ($categories as $cat) {
		$cat_name = $cat->name;
		$cat_id = get_cat_ID($cat_name);
		$cat_link = get_category_link($cat_id);
		echo '<a href="'.$cat_link.'" title="View '.$cat->name.'">';
		echo $cat->name;
		echo '</a> + ';
	}	
}

// Get post cities
// find user city id
$user_city_terms = term_exists($user_city, 'nh_cities');
$user_city_id = $user_city_terms['term_id'];

// find Any City id
$any_city_terms = term_exists('Any City', 'nh_cities');
$any_city_id = $any_city_terms['term_id'];

// find post cities + get id/name
$post_cities = get_the_terms($post->ID,'nh_cities');
foreach ($post_cities as $c) {
	$tmp_slug = strtolower($c->name);
	$tmp_slug = str_replace(' ','-',$tmp_slug);

	$post_city = get_term_by('slug',$tmp_slug,'nh_cities');	
	$post_city_id[] = $post_city->term_id;		

	$post_city_name[] = $post_city->name;
}

// if user city or Any City
if (in_array($any_city_id,$post_city_id)) {
	$new_any_city = $post_cities[$any_city_id]->name;
	$any_city_slug = strtolower($new_any_city);
	$new_any_city_slug = str_replace(' ','-',$any_city_slug);
	echo '<a href="'.$app_url.'/cities/'.$new_any_city_slug.'" title="">'.$new_any_city.'</a>';
}
if (in_array($user_city_id,$post_city_id)) {
	$new_user_city = $post_cities[$user_city_id]->name;
	$user_city_slug = strtolower($new_user_city);
	$new_user_city_slug = str_replace(' ','-',$user_city_slug);
	$new_user_city_name = 'City of '.substr($new_user_city,0,-3);
	echo ' + <a href="'.$app_url.'/cities/'.$new_user_city_slug.'" title="">'.$new_user_city_name.'</a>';	
}
?>		
		</p>
	</div>

<?php 
//endif;
endwhile;
else : 
?>
	<li class="fdbk-list" style="border-bottom:none;">Sorry ... there are no CityHow blog posts right now.</li>
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
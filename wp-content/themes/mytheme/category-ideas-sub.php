<?php get_header(); ?>
<div class="row-fluid row-breadcrumbs">
	<div id="nhbreadcrumb">
<?php nhow_breadcrumb(); ?>
	</div>
</div>
<?php
$cat = get_the_category();
$cat_name = $cat[0]->name;
?>
<div class="row-fluid row-content">	
	<div class="wrapper">
		<div id="main">			
			<div id="content">
<?php
// limit list to user city + any city
/*
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
	$city_list[] = $city_name;
}
*/
$user_city_name = substr($user_city,0,-3);
$user_city_slug = strtolower($user_city);
$user_city_slug = str_replace(' ','-',$user_city_slug);
?>				
				<h3 class="page-title"><?php echo $cat_name;?> for 
<?php 
if ($user_city) {
	echo $user_city_name;	
}
else {
	echo 'Any City';
}
?>				
				</h3>
				<div class="intro-block">
<?php
if (is_user_logged_in()) {
	echo '<p>Help make CityHow better by voting on these ideas so we can understand what&#39;s most important to you. If you don&#39;t see your idea on the list, go ahead and add it!</p>';
}
else {
	echo '<p>Explore these Ideas that CityHow users say are helpful for any city. Then <a href="<?php echo $app_url;?>/contact" title="Get CityHow for your city">contact us</a> if you&#39;d like CityHow for your city.</p>';
}
?>			
				</div>
					
				<div id="list-fdbk">
<?php
if (is_user_logged_in()) {
	echo '<div class="intro-block-button"><a id="addfdbk" title="Add your Idea" class="nh-btn-blue" href="'.$app_url.'/add-idea">Add Your Idea</a></div>';
}
?>						
					<ul class="list-fdbk">	

<?php 
$fdbk_sub_cat = get_category_id($cat[0]->name);
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$vote_sub_args = array(
	'post_status' => 'publish',
	'orderby' => 'date',		
	'order' => DESC,
	'meta_key' => '_nh_vote_count',
	'posts_per_page' => '20',
	'paged' => $paged,
	'tax_query' => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'category',
			'field' => 'id',
			'terms' => array($fdbk_sub_cat)
		),
		array(
			'taxonomy' => 'nh_cities',
			'field' => 'slug',
			'terms' => array( $user_city_slug,'any-city' )
		)	
	)		
);
$fdbk_sub_query = new WP_Query($vote_sub_args);	
		
if (!$fdbk_sub_query->have_posts()) : ?>
	<li>Looks like there are no Ideas in this category yet. <?php if (is_user_logged_in()) {echo ' Add your ideas or questions!';}?></li>
<?php else: ?>
<?php while($fdbk_sub_query->have_posts()) : $fdbk_sub_query->the_post();?>
		
	<li class="fdbk-list" id="post-<?php echo $post->ID; ?>">
		<div class="vote-btn">
<?php 
if (is_user_logged_in()) {
	if (nh_user_has_voted_post($current_user->ID, $post->ID)) {
		echo '<span class="byline"><a id="votedthis" title="See your other Votes" href="'.$app_url.'/author/'.$current_user->user_login.'" class="votedthis nhline">You voted</a></span>';
	}
	else {
		nh_vote_it_link();
	}						
}	
?>
		</div>
		<div class="vote-question"><strong><a class="nhline" href="<?php the_permalink();?>" title="See <?php echo the_title();?>"><?php the_title();?></a></strong>
			<p class="comment-meta"><span class="byline"><?php comments_number( '', '1 comment', '% comments' ); ?></span></p>
			<p class="comment-meta"><span class="byline">in </span>
<?php 
// get post category
$category = get_the_category(); 
foreach ($category as $cat) {
	echo '<a class="nhline" href="'.$app_url.'/ideas/'.$cat->slug.'" title="See ideas in '.$cat->name.'">';
	echo $cat->name;
	echo '</a>';
}
// get post cities
$post_cities = wp_get_post_terms($post->ID,'nh_cities');
$term = term_exists($user_city, 'nh_cities');

if ($post_cities) {
	$count = count($post_cities);
	$j = $count - 1;	
	echo ' + ';
	for ($i=0; $i<=$j; $i++) {
		$city = $post_cities[$i]->name;

		if ($city == $user_city OR $city == 'Any City') {
			$city_slug = strtolower($city);
			$city_slug = str_replace(' ','-',$city_slug);
			if ($city != 'Any City') {
				$city_name = substr($city,0,-3); //remove state
				$city_string = 'City of '.$city_name;
			}
			else {
				$city_name = $city;
				$city_string = $city_name;			
			}	
			// if only one city or if only city is any city	
			if ($count == 1 OR $term == 0) {
				echo '<a href="'.$app_url.'/cities/'.$city_slug.'" title="See content for '.$city_string.'">'.$city_string.'</a>';
			}
			elseif ($count > 1 AND $i < $j) {
				echo '<a href="'.$app_url.'/cities/'.$city_slug.'" title="See content for '.$city_string.'">'.$city_string.'</a>, ';
			}			
			elseif ($count > 1 AND $i == $j) {
					echo '<a href="'.$app_url.'/cities/'.$city_slug.'" title="See content for '.$city_string.'">'.$city_string.'</a>';
			}
		}
	}
}
?>
			</p>
<?php
$guide_answer = get_post_meta($post->ID,'gde-answer',true);
if ($guide_answer) {
	echo '<p class="comment-meta"><span class="answered"><a class="nhline" href="'.$guide_answer.'" title="Read the answer">Read the answer!</a></span></p>';
}
?>																
		</div>
		<div class="nh-vote-count"><span class="nh-vote-count  vote-<?php echo $post->ID;?>">
<?php echo nh_get_vote_count($post->ID);?></span>
<br/><span class="small-vote">votes</span>
		</div>						
	</li>
<?php endwhile;

$total_pages = $fdbk_sub_query->max_num_pages;
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
				</div><!-- / list-feedback-->
			</div><!--/ content-->
<?php get_sidebar('ideas');?>
		</div><!--/ main-->
	</div><!--/ content-->
</div><!--/ row-content-->
<?php get_footer(); ?>
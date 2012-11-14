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
$post_cities = wp_get_post_terms($post->ID,'nh_cities');

// find user city in post cities + get id
$user_city_terms = term_exists($user_city, 'nh_cities');
$user_city_id = $user_city_terms['term_id'];

// find Any City in post cities + get id
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
	if ( have_posts() ) :
	while ( have_posts() ) : 
	the_post(); 
	$nh_author = get_userdata($curauth->ID);
	$nhow_post_id = $post->ID;	
?>			
			<div class-"guide-overview"><p>
<?php 
$tmpcontent = get_the_content();
$guide_summary = preg_replace('#\R+#', '</p><p>', $tmpcontent);
echo make_clickable($guide_summary);
?></p>
			</div>
<?php
$img_feature_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
?>
			<div class="single-guide-img overview">
				<div class="carousel-inner"><!--img src="<?php echo $style_url;?>/lib/timthumb.php?src=<?php echo $img_feature_src[0];?>&h=400&q=95&zc=2&a=t" alt="Photo of <?php the_title();?>" /-->
				</div>
			</div>
			
<?php	
	endwhile; // end while posts
	endif; // end if posts

if (!is_preview()) : ?>
<div id="leavecomment" class="nhow-comments">
<?php echo comments_template( '', true );?>
</div>
<?php
endif; // endif preview

// if content is NOT user city or Any City
else  :
	echo '<p style="padding:0 4em 0 0;">Sorry ... this content is only visible to employees of ';
	foreach ($other_city_name as $c_name) {
		$city_name = substr($c_name,0,-3);
		$new_city_name .= ' the City of '.$city_name.' + ';			
	}		
		echo rtrim($new_city_name,' + ').'</p>';
endif; // endif content is/is not user city or Any City
?>
			</div><!--/ content -->
<?php 
if (!is_preview()) :
	get_sidebar('blog-single');	
endif;
?>			
		</div><!--/ main-->
	</div><!--/ wrapper-->
</div><!--/ row-fluid-->
<?php get_footer(); ?>
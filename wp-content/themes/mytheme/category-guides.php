<?php get_header(); ?>
<div class="row-fluid row-breadcrumbs">
	<div id="nhbreadcrumb">
<?php nhow_breadcrumb(); ?>
	</div>
</div>
<div class="row-fluid row-content">	
	<div class="wrapper">
		<div id="main">
			
			<div class="row-fluid">
				<div class="span8">	
					<h3 class="page-title">CityHow Guides</h3>
					<div class="intro-block noborder"><p>A CityHow Guide can be about anything that&#39;s useful to employees working for the City of Philadelphia. Maybe that&#39;s how to book a conference room in City Hall. Or how to arrange a Zipcar through the City.</p><p>If it&#39;s something you know how to do, it&#39;s probably something other people want to know how to do. So suggest a topic for a new CityHow Guide, create your own Guide, or ask another employee to write one.</p>
					</div>
				</div>
				<div class="span4 sidebar-faux">
					<div class="sidebar-button-panel">
						<a class="btns" href="<?php echo $app_url;?>/add-idea" rel="tooltip" data-title="Tell us about the content you want, and we'll make getting it a priority."><button class="nh-btn-blue btn-fixed-small" data-placement="top">Add an Idea for a Guide</button></a>
						
						<a class="btns" href="<?php echo $app_url;?>/create-guide" rel="tooltip" data-title="Create a CityHow Guide and share what you know with others." data-placement="top"><button class="nh-btn-blue btn-fixed-small">Create a Guide</button></a>
<?php
// Turn off function when working locally - only works hosted
echo '<div class="jetpack-cat-guides">';
//echo sharing_display(); // Add sharing later
echo '</div>';
?>
						<!--br/><button class="nh-btn-blue btn-fixed-small">Tell a Friend</button-->			
					</div><!--/ widget-->	
				</div>
			</div><!--/ row-fluid-->
			
			<div id="content-full" class="row-fluid">
				<div class="span12" id="list-guides">
					<ul class="list-guides">

<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$guide_cat = get_category_id('guides');
$list_args = array(
	'post_status' => 'publish',
//	'cat' => $guide_cat,
	'posts_per_page' => 12,
	'paged' => $paged,
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
$list_query = new WP_Query($list_args);
if ($list_query->have_posts()) : 
while($list_query->have_posts()) : $list_query->the_post();
$imgSrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
?>

<li class="guides-list" id="post-<?php echo $post->ID;?>"><a class="nhline link-other" rel="bookmark" title="See <?php echo the_title();?>" href="<?php the_permalink();?>"><img src="<?php echo $style_url;?>/lib/timthumb.php?src=<?php echo $imgSrc[0];?>&w=184&h=115&zc=1&a=t" alt="Photo from <?php echo the_title();?>" />
	
	<div class="home-caption">
<?php
$pad = ' ...';
$pic_title = trim_by_chars(get_the_title(),'60',$pad);
?>
		<p><?php echo $pic_title;?></a></p>		
	</div>	
</li>

<?php endwhile; ?>	
<?php else : ?>	

<li class="guides-list" id="post-no-guides">Sorry, there are no public CityHow Guides to see at this time.</li>

<?php 
endif; 

$big = 999999999; // need an unlikely integer
echo paginate_links( array(
	'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link($big) ) ),
	'format' => '?paged=%#%',
	'current' => max(1, get_query_var('paged')),
	'total' => $wp_query->max_num_pages
));

wp_reset_query();
?>	
					</ul><!-- / list-guides-->							
				</div>
			</div><!--/ row-fluid-->
				
		</div><!--/ main-->
	</div><!--/ wrapper-->
</div><!--/ row-content-->
<?php get_footer(); ?>
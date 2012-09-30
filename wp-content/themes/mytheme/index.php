<?php get_header(); ?>
<div class="row-fluid row-content">
	<div id="beta"></div>
	<div class="wrapper">
		<div id="main">

<?php if (!is_user_logged_in()) : ?>	
			<div class="row-fluid home-promo" style="margin-bottom:2.5em;">
				<div id="site-promo" class="span7">
					<h1 class="promo-copy">CityHow makes it easy to find and share information about working for the City of Philadelphia.</h1> 
					<h2 class="promo-copy">Browse CityHow Guides on topics submitted by other employees, and create your own Guides to help them.</h2>
				</div>

				<div id="site-promo-list" class="span5">
					<h4>Please sign in to CityHow</h4>
					<p><a class="promo_suggest" href="<?php echo $app_url;?>/signin" title="Sign In now"><button class="nh-btn-blue btn-fixed">Sign In to CityHow</button></a></p>

					<p><a class="promo_suggest" href="<?php echo $app_url;?>/register" title="Create a CityHow account"><button class="nh-btn-blue btn-fixed">Create an Account</button></a></p>
				</div>
			</div><!--/ row-fluid inner-->
																		
<?php else : ?>				
			<div class="row-fluid home-promo">	
				<div id="site-promo" class="span7">
					<h1 class="promo-copy">CityHow makes it easy to find and share information about working for the City of Philadelphia.</h1> 
					<h2 class="promo-copy">Browse CityHow Guides on topics submitted by other employees, and create your own Guides to help them.</h2>
				</div>

				<div id="site-promo-list" class="span5">
					<h4>Share Your Knowledge</h4>
					<p><span>1.</span>&nbsp;&nbsp;<a class="promo_suggest" href="<?php echo $app_url;?>/add-idea" data-title="Tell us about the content you want, and we'll make getting it a priority." rel="tooltip" data-placement="top"><button class="nh-btn-blue btn-fixed">Add an Idea for a Guide</button></a></p>

					<p><span>2.</span>&nbsp;&nbsp;<a class="promo_suggest" href="<?php echo $app_url;?>/create-guide" data-title="Share Your Knowledge -- Create a CityHow Guide and share what you know with others." rel="tooltip" data-placement="top"><button class="nh-btn-blue btn-fixed">Create a CityHow Guide</button></a></p>
				</div>
			</div><!--/ row-fluid inner-->		

			<div class="row-fluid">
				<div class="span12">
					<div class="home-featured">
						<h5 class="widget-title">Explore These CityHow Guides</h5>
						<ul class="list-guides list-guides-home">
<?php
// if sticky ids show them
// must be in groups of 4 for home page
$sticky_ids = get_option('sticky_posts');
if(count($sticky_ids) != 0) {
	$sticky_args = array(
        'category_name' => 'guides',
        'posts_per_page' => 4,
        'post__in' => $sticky_ids
    );
    $sticky_query = new WP_Query($sticky_args);
	while ($sticky_query->have_posts()) : $sticky_query->the_post();
		$imgSrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
		echo '<li class="guides-list" id="post-'.$post->ID.'"><a rel="bookmark" title="See '.get_the_title().'" href="'.get_permalink().'">';

		echo '<img  src="'.$style_url.'/lib/timthumb.php?src='.$imgSrc[0].'&w=184&h=135&zc=1&at=t" alt="Photo from '.get_the_title().'" />';

		echo '<div class="home-caption">';
		$pad = ' ...';
		$pic_title = trim_by_chars(get_the_title(),'60',$pad);
		echo '<p><a class="nhline link-other" rel="bookmark" title="See '.get_the_title().'" href="'.get_permalink().'">'.$pic_title.'</a></p>';
		echo '</div></li>';
	endwhile;
wp_reset_query();	
}
else {
	$normal_args = array(
        'category_name' => 'guides',
        'posts_per_page' => 4,
        'orderby' => 'date'
    );
	$normal_query = new WP_Query($normal_args);
	while ($normal_query->have_posts()) : $normal_query->the_post();
		$imgSrc = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
		
		echo '<li class="guides-list" id="post-'.$post->ID.'"><a rel="bookmark" title="See '.get_the_title().'" href="'.get_permalink().'">';

		echo '<img class="guide_image_bg" src="'.$style_url.'/lib/timthumb.php?src='.$imgSrc[0].'&w=184&h=115&zc=1&at=t" alt="Photo from '.get_the_title().'" /></a>';
		
		echo '<div class="home-caption">';
		$pad = ' ...';
		$pic_title = trim_by_chars(get_the_title(),'60',$pad);
		echo '<p><a class="nhline link-other" rel="bookmark" title="See '.get_the_title().'" href="'.get_permalink().'">'.$pic_title.'</a></p>';
		
		echo '</div>';
		echo '</li>';
	endwhile;
wp_reset_query();
}
?>
<?php
echo '<div class="see_all"><a class="nhline" href="'.$app_url.'/guides" title="">See all CityHow Guides &#187;</a></div>';
?>
						</ul>
					</div>
				</div><!--/ span12-->
			</div><!--/ row-fluid inner-->
<?php endif; ?>	
		
			<div class="row-fluid home-combo">
				<div class="span6 home-ideas">
					<h5 class="widget-title">Latest CityHow Ideas</h5>	
<?php if (is_user_logged_in()) : ?>
<p><a id="addfdbk" title="Add Your Idea" rel="tooltip" data-placement="bottom" data-title="" class="nh-btn-blue" href="<?php echo $app_url;?>/add-idea" >Add Your Idea</a></p>
<?php else : ?>
<p style="float:left;text-align:left;"><strong><a href="<?php echo $app_url;?>/signin" title="Sign In now">Sign In</a></strong> to CityHow so you see more of the latest ideas, and add your own!</p>	
<?php endif; ?>
						<ul class="list-ideas list-ideas-home">							
<?php
$fdbk_sub_cat = get_cat_ID('ideas');
$fdbk_sub_args = array(
	'post_status' => 'publish',
	'cat' => $fdbk_sub_cat,
	'orderby' => 'date',
	'order' => DESC,
	'posts_per_page' => '5'
);
$fdbk_sub_query = new WP_Query($fdbk_sub_args);
if ($fdbk_sub_query->have_posts()) :
while($fdbk_sub_query->have_posts()) :
$fdbk_sub_query->the_post();	
?>					

<?php if (!is_user_logged_in()) : ?>
<li class="ideas-list"><?php echo the_title();?>&nbsp;&nbsp;<span class="meta meta-small"><span class="byline">added</span> <?php echo get_the_date();?></span></span></li>

<?php else : ?>
<li class="ideas-list"><a class="nhline" href="<?php echo get_permalink();?>" title="See <?php echo the_title();?>"><?php echo the_title();?></a>&nbsp;&nbsp;<span class="meta meta-small"><span class="byline">added</span> <?php echo get_the_date();?></span></span></li>	

<?php endif; ?>	
<?php 
endwhile;
endif;
wp_reset_query();
?>								

<li class="ideas-list">
<?php if (is_user_logged_in()) : ?>
<a class="nhline" href="<?php echo $app_url;?>/ideas" title="See all the ideas">See all the ideas &#187;</a>
<?php else : ?>
<a class="nhline" href="<?php echo $app_url;?>/signin" title="Sign In now">Sign in to see all the ideas</a>
<?php endif; ?>
</li>
						</ul>						
				</div><!--/ span4-->

				<div class="span6 home-about">
					<h5 class="widget-title">About CityHow</h5>
<?php
$page_id = get_ID_by_slug('about');
$post = get_post($page_id); 
$content = $post->post_content;
$content = strip_tags($content,'<p>,<a>');
$content = trim_by_words($content,'98','');
if (is_user_logged_in()) {
	$content = trim_by_words($content,'90',nh_continue_reading_link());
} 
else {
	$content = trim_by_words($content,'98');	
}
echo $content;
?>					
				</div>
			</div><!--/ row-fluid inner-->

<?php //endif; // end if user logged in ?>			

		</div><!--/ main-->		
	</div><!--/ wrapper-->	
</div><!--/ row-fluid-->
<?php get_footer();?>
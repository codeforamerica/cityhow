<?php
global $app_url;
global $style_url;
global $current_user;
$style_url = get_bloginfo('stylesheet_directory');
$app_url = get_bloginfo('url');
?>
<div class="row-fluid row-footer">
	<div class="wrapper">	
		<div id="footer">
			
			<div class="span4 odd1 clearfix partners">
				<h6>Partners</h6>
				<ul class="partnerul footer">
					<li><p class="footerp">CityHow is a collaboration between the <a target="_blank" href="http://www.phila.gov" title="Go to City of Philadelphia">City of Philadelphia</a> and <a target="_blank" href="http://www.codeforamerica.org" title="Go to Code for America">Code for America</a>.</p></li>
					<li class="partners"><a target="_blank" href="http://www.phila.gov" title="Go to City of Philadelphia"><img width="70" src="<?php echo $style_url;?>/images/logo_phl.png" alt="City of Philadelphia logo"></a></li>	
					<li class="partners cfa"><a target="_blank" href="http://www.codeforamerica.org" title="Go to Code for America"><img width="70" src="<?php echo $style_url;?>/images/logo_cfa.png" alt="Code for America logo"></a></li>														
				</ul>
			</div><!-- /span4 -->			
			
			<div class="span4 middle clearfix">
				<h6>About CityHow</h6>
				<ul class="footer">
					<li><a class="noline footer-link" title="Learn about CityHow" href="<?php echo $app_url;?>/about">Learn about CityHow</a></li>
					<!--li><a class="noline footer-link" title="Find out what we&#39;ve been up to" href="<?php echo $app_url;?>/blog">Read the Blog</a></li-->										
				</ul>
			</div><!-- /span4 -->

			<div class="span4 odd2 clearfix">
				<h6>Contact</h6>
				<ul class="footer">
<?php if (!is_user_logged_in()) :?>
	<li><p class="footerp"><a class="whitelink" title="Get in touch with us" href="<?php echo $app_url;?>/contact">Email Us</a> if you're interested in CityHow for your city.</p></li>
	
<?php else :?>
	<li><p class="footerp"><a class="footer-link" title="Get in touch with us" href="<?php echo $app_url;?>/contact">Email Us</a></p></li>	
<?php endif;?>											
					<li><p class="footerp">Find us on:&nbsp;&nbsp;<a target="_blank" title="Visit CityHow on Github" href="https://github.com/codeforamerica/cityhow"><img src="<?php echo $style_url;?>/images/icons/social/github.png" alt="Github logo" width="26" /></a></p></li>
				</ul>			
			</div><!-- /span4 -->
			
			<div class="span12 trade"><p class="trade">All CityHow code is open source and free for civic use, so <a class="whitelink" target="_blank" href="https://github.com/codeforamerica/cityhow" title="Visit CityHow on Github">visit us on Github</a>.<br/>&#169; 2012 CityHow. <!--The CityHow name and logo are trademarks of CityHow. All rights reserved.--></p>
			</div>			
		</div><!-- / footer-->
	</div><!--/ wrapper -->
</div><!--/ row-fluid -->
<?php // get stuff for JS
$city_terms = get_terms('nh_cities');
foreach ($city_terms as $city_term) {
	$city_term = $city_term->name;
	if ($city_term != 'Any City') {
		$cities[] = $city_term;
	}
}

$alltags = get_tags();
foreach ($alltags as $tag) {
	$tag_name = $tag->name;
	$tags[] = $tag_name;
}

$current_user_login = $current_user->user_login;
?>
<?php wp_footer();?>
<!-- Le javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="<?php echo $style_url; ?>/lib/js/jquery.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-collapse.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-transition.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-alert.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-modal.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-dropdown.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-scrollspy.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-tab.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-tooltip.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-popover.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-button.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-carousel.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/bootstrap-typeahead.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/application.js"></script>
<script src="<?php echo $style_url; ?>/lib/js/jquery-ui-1.8.23.custom.min.js"></script>

<script>
$(document).ready(function() {
	$('.dropdown-toggle').dropdown();
//	$('#likethis').tooltip();
//	$('.votethis').tooltip();	
//	$('#addfdbk').tooltip();
	$('.cityuser').tooltip(); // on city index page
//	$('.btns').tooltip();				
});

// Replace LikeThis btn immediately onclick
$('#likethis').click(function() {	
	var username = "<?php echo $current_user_login;?>";
	var link = '<a class="likedthis nhline" id="likedthis" title="See your other Likes" href="/author/' + username + '"';
	var txt = '>You like this</a>';
	// Hide tooltip if open after liking	
	$('.tooltip').remove();
	$('#likethis').removeAttr('rel');
	$('#likethis').replaceWith(link + txt);			
});

// Replace VoteThis btn immediately onclick
$('.vote').click(function() {	
	var username = "<?php echo $current_user_login;?>";
	var link = '<span class="byline"><a class="votedthis nhline" id="votedthis" title="See your other Votes" href="/author/' + username + '"';
	var txt = '>You voted</a></span>';
	// Hide tooltip if open after liking	
	$('.tooltip').remove();
	$(this).removeAttr('rel');
	$(this).replaceWith(link + txt);			
});

//$().click();

// Remove image from Guide or Step
function removeMyFile(id){
	jQuery("input[name='item_meta["+id+"]']").val('');
	jQuery('#frm_field_'+id+'_container img, #remove_link_'+id).fadeOut('slow');
}

// Get cities for JS Autocomplete
$(function() {
	var cities = <?php echo json_encode($cities); ?>;
	$( "#user_city" ).autocomplete({
		source: cities,
		minLength: 2
	});
	$( ".idea_city" ).autocomplete({
		source: cities,
		minLength: 2
	});
});
// Get tags for JS Autocompplete
var tags = <?php echo json_encode($tags); ?>;

function split( val ) {
	return val.split( /,\s*/ );
}
function extractLast( term ) {
	return split( term ).pop();
}

$(".guide_tag")
	.bind( "keydown", function( event ) {
		if ( event.keyCode === $.ui.keyCode.TAB &&
			$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
	.autocomplete({
		minLength: 0,
		source: function( request, response ) {
			response( $.ui.autocomplete.filter(
				tags, extractLast( request.term ) ) );
		},
		focus: function() {
			return false;
		},
		select: function( event, ui ) {
			var terms = split( this.value );
			terms.pop();
			terms.push( ui.item.value );
			terms.push( "" );
			this.value = terms.join( ", " );
			return false;
		}
	});		


</script>

<script type="text/javascript">
// Google analytics
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-32197535-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>

</body>
</html>
<?php
/**
 * The loop that displays posts.
 *
 * The loop displays the posts and the post content.  See
 * http://codex.wordpress.org/The_Loop to understand it and
 * http://codex.wordpress.org/Template_Tags to understand
 * the tags used in it.
 *
 */
 
 
global $wpdb, $searchTerms, $s; 

	// set args
	// searchTerm - term to perform search on (default: $_SERVER['REQUEST_URI'])
	// postType -  the type of posts that are shown in search results (default: page)
	// maxResults - max amount of total results displayed (default: 30)
	// resultsPerPage - amount of results to display per page (default: 7)
	// paginate - whether or not to paginate results (default: false)
	
	if ( isset($args2['searchTerm']) ) $s = $args2['searchTerm'];	else $s = preg_replace('/\?.*/', '', $_SERVER['REQUEST_URI']);
	if ( !isset($args2['postType']) ) $args2['postType'] = 'page';
	
	if (get_option(SM_SITEOP_PREFIX.'autosearch_max_results')) { $args2['maxResults'] = get_option(SM_SITEOP_PREFIX.'autosearch_max_results'); }
	else { $args2['maxResults'] = 10; }
	
	if (get_option(SM_SITEOP_PREFIX.'autosearch_per_page')) { $args2['resultsPerPage'] = get_option(SM_SITEOP_PREFIX.'autosearch_per_page'); }
	else {  $args2['resultsPerPage'] = 5; }
	
	if (get_option(SM_SITEOP_PREFIX.'autosearch_paginate')) { $args2['paginate'] = get_option(SM_SITEOP_PREFIX.'autosearch_paginate'); }
	else {  $args2['paginate'] = false; }
	
	
	// adjust length of content excerpts
	function excerpt_length( $length ) { return 25; }
	add_filter( 'excerpt_length', 'excerpt_length' ); 
	
	global $sm_more;
	$sm_more = '<span class="elipsis">&hellip;</span>';
	
	// custom elipsis
	function sm_auto_excerpt_more( $more ) { global $sm_more; return $sm_more; }
	add_filter( 'excerpt_more', 'sm_auto_excerpt_more' );
	
	// paginate
	if($args2['paginate']): 
	?>
		<!-- pagination disabled on mobile for now -->    
		<script type="text/javascript" src="<?php echo get_template_directory_uri();?>/js/jquery.pajinate.min.js"></script>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			// paginate the list
			jQuery('#smSearchResults').pajinate({
				items_per_page : <?php echo $args2['resultsPerPage']; ?>,
				item_container_id : '#resultsList',
				//nav_label_first : '<<',
				//nav_label_prev : '<',
				//nav_label_next : '>',
				//nav_label_last : '>>',
			});
		});
		</script>
	<?php endif; ?>
	<?php
	// set args to theme options if they are set
	$maxResultThemeOpt = (int)get_option(SM_SITEOP_PREFIX.'autosearch_max_results');
	if( $maxResultThemeOpt != '' && is_int($maxResultThemeOpt) )
		$args2['maxResults'] = $maxResultThemeOpt;
	$resultsPerPageThemeOpt = (int)get_option(SM_SITEOP_PREFIX.'autosearch_per_page');
	if( $resultsPerPageThemeOpt != '' && is_int($resultsPerPageThemeOpt) )
		$args2['resultsPerPage'] = $resultsPerPageThemeOpt;
	
	// clean up search term
	$s = htmlentities(strip_tags( $s ));
	$s = str_replace('/','',$s);
	$s = preg_replace('/(.*)\.(html|htm|php|asp|aspx)?$/','$1',$s);
	$s = str_replace('-',' ',$s);
	$s = str_replace('%20',' ',$s);
	
	// seperate words by spaces and store in array
	$searchTerms = explode(" ",$s);
	
	// grab ids of posts that have keywords in content or title
	$querystr = "
		SELECT DISTINCT wposts.ID 
		FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta
		WHERE wposts.ID = wpostmeta.post_id
		AND wposts.post_status = 'publish'
		AND wposts.post_type = '".$args2['postType']."' "; 
	
	$querystr .= "AND ("; 
							
	// loop through keywords in page content
	foreach ($searchTerms as $key => $searchTerm) {
		$querystr .= "wposts.post_content LIKE '%".$searchTerm."%' ";
	}
	// loop through keywords in page title
	foreach ($searchTerms as $key => $searchTerm) {
		$querystr .= "OR wposts.post_title LIKE '%".$searchTerm."%' ";
	}
	
	$querystr .= ") ";	
	$querystr .= "ORDER BY wposts.ID ASC";
	
	$pageIDs = $wpdb->get_results($querystr, ARRAY_N);
	
	// loop through pageIDs array and return a ids in a coma seperated string
	foreach ($pageIDs as $key => $data) {
		$pageIDsArray[] = $data[0];
	}
	
	// get the posts
	$qpArgs = array( 'post_type' => $args2['postType'], 'post__in' => $pageIDsArray, 'posts_per_page' => -1 );
	$posts = query_posts( $qpArgs );
	
	$searchResultsArray = array();
	
	//loop
	while ( have_posts() ) : the_post();
		if (is_search() && !($post->post_type=='post')) continue;
	
		$postTitleSingleTerm = 15;
		$postTitleWholeTerm = 175;
		$pageTitleSingleTerm = 5;
		$pageTitleWholeTerm = 80;
		$contentSingleTerm = 1;
		$contentWholeTerm = 3;
	
		// attempt to create real page title based on whats found in h1 (and sometimes h2) tags at top of content
		$fauxTitleResults = preg_match('/<h1[^>]*>(.*)<\/h1[^>]*>/', $post->post_content, $matches);
		$fauxTitle2Results = preg_match('/<\/h1[^>]*>[\r\n]+<h2[^>]*>([\s\S]*?)<\/h2[^>]*>/', $post->post_content, $matches2);
		$fauxTitle = strip_tags($matches[0]);
		$fauxTitle2 = strip_tags($matches2[0]);
		
		//if an h2 is found directly following an h1, add it to the title
		$displayTitle = $fauxTitle;
		if ($fauxTitle2Results != 0)
			$displayTitle .= " - ". $fauxTitle2;
		
		// if no h1 tag present use post_title
		if ($fauxTitleResults == 0 )
			$displayTitle = $post->post_title;
		
		//get scores
		$postTitleScore = score_search_results( array('text' => $post->post_title, 'single_weight' => $postTitleSingleTerm, 'whole_weight' => $postTitleWholeTerm) );
		$pageTitleScore = score_search_results( array('text' => $displayTitle, 'single_weight' => $pageTitleSingleTerm, 'whole_weight' => $pageTitleWholeTerm) );
		$contentScore = score_search_results( array('text' => $post->post_content, 'single_weight' => $contentSingleTerm, 'whole_weight' => $contentWholeTerm) );
		$postScore = $postTitleScore + $pageTitleScore + $contentScore;
		
		// get the excerpt
		$excerpt = get_the_excerpt();
		
		// remove $sm_more appendage before highlighting search terms
		$excerpt = str_replace($sm_more, '', $excerpt);
		$excerpt = preg_replace('/('.implode('|', $searchTerms) .')/iu', '<strong class="search-excerpt">\0</strong>', $excerpt);
		
		// re append $sm_more to highlighted excerpt
		$excerpt .= $sm_more;
		
		// get template being used by page so we can exclude those set to 404 tpl
		$theTemplate = get_post_meta($post->ID, '_wp_page_template',true );
		
		// if the post score is greater than zero and its not a faux 404 page
		if($postScore > 0 && $theTemplate != 'tpl-404.php' && $excerpt!='') {
			$item = '<li class="'.$postScore.'">';
			$item .= '<h2><a href="'.get_permalink($post->ID).'">'.$displayTitle.'</a></h2>';
			$item .=  $excerpt;
			
			if($_GET['verbose'] == 1 && current_user_can('edit_theme_options')) {
				$item .=  '<br/><a href="'.get_permalink($post->ID).'">'.get_permalink($post->ID).'</a>';
				$item .=  '<br /><br /><span class="score"><strong>WP Page Title Score: '.$postTitleScore.'</strong></span>'; 
				$item .=  '<br /><span class="score"><strong>On-Page Title Score: '.$pageTitleScore.'</strong></span>';
				$item .=  '<br /><span class="score"><strong>Content Score: '.$contentScore.'</strong></span>';
				$item .=  '<br /><span class="score"><strong>Total Score: '.$postScore.'</strong></span>';
			}
			$item .=  '</li>';
			$searchResultsArray[$postScore.".".$i] = $item;
		}//$postScore > 0 && $theTemplate != 'tpl-404.php'
	
	endwhile; // End the loop. Whew.
		
	// sort search results by score
	krsort($searchResultsArray);
	$i=1;
	
	// if we have results display them
	if(!empty($searchResultsArray)) {
		echo '<div id="smSearchResults">'; 
		// add styles needed for functionality
		echo '<style>#noSMSearchResults, #smSearchResults .first_link, #smSearchResults .last_link, #smSearchResults .paginatorTop { display:none; }</style>';
		
		echo '<p id="searchMsg" style="margin-bottom:10px;"><strong>Were you looking for one of the following pages?</strong></p>';
		echo '<div class="page_navigation paginatorTop"></div>';
		echo '<ul id="resultsList">';
		
		// output li's to page stoping at designated amount
		foreach ($searchResultsArray as $key => $result) {
			$i++;
			echo $result;
			if($i > $args2['maxResults'])
				break;
		}
		echo '</ul>';
		echo '<div class="page_navigation paginatorBottom"></div>';
		echo '</div><!-- results -->';
	}

//function: sm_list_autosearch_default
//description: outputs defualt 404 html to be displayed when no results found or autosearch is disabled
//required parameters: none
function sm_list_autosearch_default() { ?>
	<div id="noSMSearchResults">
		<?php  if ($_GET['s']) { ?>
			<p id="zeroResultsMsg">We're sorry, but your search returned 0 results. Please try searching again with different keywords.</p>
		<?php } ?>
		<?php echo stripslashes(get_option(SM_SITEOP_PREFIX.'autosearch_default_html')); ?>
	</div><!-- #noSMSearchResults -->
<?php
}

//function: score_search_results
//description: evaluates content and scores it based on keywords
//required parameters: text to score, weight to score results by ($args2['text'], $args2['single_weight'], $args2['whole_weight'] )
function score_search_results($args2 = array()) {
	global $searchTerms, $s;

	// score by searching for individual keywords
	foreach ($searchTerms as $key => $searchTerm) {
		// look in text for single term matches
		$singleResults = preg_match_all("/".strtolower($searchTerm)."/", strtolower($args2['text']), $out, PREG_SET_ORDER);
		// single score = number of matches time weight
		$singleScore += ($singleResults * $args2['single_weight']);
	}
	
	// score by searching for whole tern
	// look in text for whole term matches
	$wholeResults = preg_match_all("/".strtolower($s)."/", strtolower($args2['text']), $out, PREG_SET_ORDER);
	// whole score = number of matches time weight
	$wholeScore = $wholeResults * $args2['whole_weight'];
	
	// add single term score to whole term score to get total score
	$totalScore = $singleScore + $wholeScore;

	return $totalScore;
}
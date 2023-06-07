<?php
/***
 * Theme functions and definitions
 *
 * @package HelloElementorChild  
 */

define( 'DISQO_TH_URL', get_stylesheet_directory_uri() );
define( 'DISQO_TH_PATH', get_stylesheet_directory() );

/**
 * Load child theme css and optional scripts
 *
 * @return void
 */
function hello_elementor_child_enqueue_scripts() {
	wp_enqueue_style(
		'hello-elementor-child-style',
		DISQO_TH_URL . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		'1.0.0'
	);
	

	//Add the Select2 CSS file
	wp_enqueue_style( 'select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', array(), '4.1.0-rc.0');

	wp_enqueue_style(
		'disqo-style',
		DISQO_TH_URL . '/assets/css/disqo-style.css',
		[
			'hello-elementor-theme-style',
		],
		filemtime( DISQO_TH_PATH . '/assets/css/disqo-style.css' )
	);

	//Add the Select2 JavaScript file
	wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', 'jquery', '4.1.0-rc.0');

	wp_enqueue_script( 'disqo-custom-js', DISQO_TH_URL ."/assets/js/disqo-custom.js", array('jquery'), filemtime( DISQO_TH_PATH . '/assets/js/disqo-custom.js' ), true );
	wp_enqueue_script( 'slick-js', DISQO_TH_URL ."/assets/js/slick.js", array('jquery'), filemtime( DISQO_TH_PATH . '/assets/js/slick.js' ), true );

	if( is_search() ) :

		wp_register_script( 'disqo-search-ajax', DISQO_TH_URL ."/assets/js/disqo-search-ajax.js", array('jquery'), filemtime( DISQO_TH_PATH . '/assets/js/disqo-search-ajax.js' ), true );
		wp_enqueue_script( 'disqo-search-ajax' );
		wp_localize_script( 'disqo-search-ajax', 'disqo_search', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		));

	endif;

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts', 20 );

/**
 * Just used for testing perpose for the Developer
 *
 * @return void
 */
function pre( $args ) {
	echo '<pre>'; print_r($args); echo '</pre>';
}

/**
 * On error notify the Developer via mail
 * @param string $to email address.
 * @param string $message message.
 * 
 * @return void
 */
function error_notification_email( $to, $subject, $message ) {
	$headers= 'From: no-reply@'.$_SERVER['SERVER_NAME'];
	//wp_mail( $to, $subject, $message, $headers );
}

/*
 * HubSpot API integration: get, create and update data ************************
 */

define( 'HUB_SEARCH', get_field('hub_search','option') );
define( 'HUB_SEARCH_INDEX', get_field('hub_search_index','option') );
define( 'HUB_TAG_IMGS', array( 
	'Case Studies' => DISQO_TH_URL . '/assets/images/circle-5.png',
	'Articles' => DISQO_TH_URL . '/assets/images/circle-3.png',
	'Reports' => DISQO_TH_URL . '/assets/images/circle-4.png',
	'Podcast' => DISQO_TH_URL . '/assets/images/circle-6.png',
	'Podcast' => DISQO_TH_URL . '/assets/images/circle-6.png',
	'Press' => DISQO_TH_URL . '/assets/images/Press-tag.png',
	'News' => DISQO_TH_URL . '/assets/images/news-tag.png',
	'Event' => DISQO_TH_URL . '/assets/images/Calendar-1.png',
	'Default' => DISQO_TH_URL . '/assets/images/tag.png' )
);

/**
 * To run GET method in HusSpot API.
 *
 * @param string $endpoint Hubspot API endpoint.
 * @param array $auth Authorization.
 * @param boolean $decode Result return in decode/encode.
 *
 * @return void
 */
function hubspot_get_method( $endpoint, $auth = NULL, $decode = 1 ) {

	$ch = curl_init( $endpoint );
	$headers = [
		'Content-Type: application/json',
		'Authorization: Bearer ' . HUB_TOKEN,
	];
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ));
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	//curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'authorization: Bearer '.$auth ));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	if( $decode )
		$result = json_decode( curl_exec($ch) );
	else
		$result = curl_exec($ch);

	if( curl_errno($ch) ) {
		$err = curl_error($ch);
		$response = array( 'error' => $err );
	} else {
		$response = array( 'response' => $result );
	}

	curl_close($ch);
	return $response;
	
}

/**
 * To get all tags from HusSpot API and store each IDs.
 *
 * @return void
 */
function disqo_tags() {
	if( function_exists('hubspot_get_method') ) {

		$tags = [];
		$FilterTags = [];

		if ( false === ( $hubspot_tags = get_transient( 'hubspot_tags' ) ) || false === ( $hubspot_tags = get_transient( 'hubspot_filter_tags' ) ) ) {

			/**
			 * Get the all tags from the HubSpot.
			 *
			 */
			$results = hubspot_get_method(
				HUB_TAGS . '/?limit=300'
			);
			if(isset($_GET))

			if( isset( $results['response'] ) ) {
				if( isset( $results['response']->status ) &&  $results['response']->status == 'error' ) {
					$message = "\n\nErrors:".print_r($results['response'],true);
					error_notification_email( 'crathod@codal.com','Tags Result, Parent Hubspot API Error', $message );
				} else { 

					// SUCESS parent call

					if( !empty( $results['response']->results ) ) {
						foreach( $results['response']->results as $result ) {

							if( isset( $result->name ) ) {

								if( in_array( $result->name, array_map('trim', explode(',',get_field('default_tags','option')) ) ) ) {

										$tags['ids'][] = $result->id;
										$tags[$result->id] = $result->name;
								} 
								
								$FilterTags['ids'][] = $result->id;
								$FilterTags[$result->id] = $result->name;
								
								
							}
						}
					}

				}
			} else {
				// Error by Curl method
				$message = "\n\nErrors:".print_r($results['error'],true);
				error_notification_email( 'crathod@codal.com','Tags Result, Parent CURL error', $message );
			}

			if( !empty($tags) ) {
				set_transient( 'hubspot_tags', $tags, 2 * HOUR_IN_SECONDS );
				set_transient( 'hubspot_filter_tags', $FilterTags, 2 * HOUR_IN_SECONDS );
			}
		}

	}

	return [];
}
add_action( 'admin_init', 'disqo_tags' );

/**
 * Theme settings, when "Hubspot Default Tags" value updated then need to regenerate transient
 */
function disqo_on_acf_update_value( $value, $post_id, $field  ) {
    // only do it to certain custom fields
    if( $field['name'] == 's_default_tags' || $field['name'] == 'default_tags' ) {        
        delete_transient('hubspot_tags');
        delete_transient('hubspot_filter_tags');
        disqo_tags();
    }

    return $value;
}
add_filter('acf/update_value', 'disqo_on_acf_update_value', 10, 3);

/**
 * Custom pagination
 */
function insertPagination($base_url, $cur_page, $number_of_pages, $prev_next=false, $totalrecord = -1) {
	if( $number_of_pages < 2 ) {
		return;
	}
	$ends_count = 1;  //how many items at the ends (before and after [...])
	$middle_count = 2;  //how many items before and after current page
	$dots = false;
	?>
	<div class="prew-btn">
	<?php if ($prev_next && $cur_page && 1 < $cur_page) : ?>
		<a data-page="<?php echo $cur_page - 1 ?>" href="<?php echo str_replace( '{page_num}', $cur_page - 1, $base_url ) ?>"><i class="fa-solid fa-chevron-left fa"></i><?php _e("Previous Page") ?></a>
	<?php endif; ?>
	</div>

     <ul class="pagination" data-total-page="<?php echo $number_of_pages; ?>" data-total-records="<?php echo $totalrecord; ?>" >
     <?php
     for ($i = 1; $i <= $number_of_pages; $i++) {
          if ($i == $cur_page) {
               ?><li class="active disabled"><a><?php echo $i; ?></a></li><?php
               $dots = true;
          } else {
               if ($i <= $ends_count || ($cur_page && $i >= $cur_page - $middle_count && $i <= $cur_page + $middle_count) || $i > $number_of_pages - $ends_count) { 
                    ?><li><a data-page="<?php echo $i ?>" href="<?php echo str_replace( '{page_num}', $i, $base_url ) //printf( $base_url, $i ); ?>"><?php echo $i; ?></a></li><?php
                    $dots = true;
               } elseif ($dots) {
                    ?><li><a style="pointer-events: none" class="disabled">&hellip;</a></li><?php
                    $dots = false;
               }
          }
     }
     ?>
     </ul>
     <div class="next-btn">
	<?php if ($prev_next && $cur_page && ($cur_page < $number_of_pages || -1 == $number_of_pages)) : ?>
		<a data-page="<?php echo $cur_page + 1 ?>" href="<?php echo str_replace( '{page_num}', $cur_page + 1, $base_url ) //printf( $base_url, $cur_page+1 ); ?>"><?php _e("Next Page") ?><i class="fa-solid fa-chevron-right fa"></i></a>
	<?php endif; ?>
	</div>

     <?php
}

// Jetmenu fixed enqueue URL not being proper because of server issue, or something else
function jetmenu_url_callback( $url, $path ) {
	$url = str_replace( '/bitnami/wordpress/', home_url('/'), $url );
	return $url;
}
add_filter( 'cx_include_module_url', 'jetmenu_url_callback', 10, 2 );

// Elementor visual editor bug fixed
add_filter('user_can_richedit', function () {
	global $wp_rich_edit;

	if (get_user_option('rich_editing') == 'true' || !is_user_logged_in()) {
		if ($_SERVER['CloudFront-Is-Desktop-Viewer'] === 'true') {
			$wp_rich_edit = true;
		}

		return true;
	}

	$wp_rich_edit = false;

	return false;
}, 100);

/**
 * Change the search page to return all posts per page in results. 
 */
function search_postsperpage( $limits ) {
	if( is_search() ) {

		return 'LIMIT 0, 100';
	}
	return $limits;
}
add_filter('post_limits', 'search_postsperpage');

/**
 * replace the normal [.....] with a empty string
 */
function new_excerpt_more( $more ) {
	return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');


/**
 * Product page to contact us button page rediraction link.
 */
function custom_redirection_rule_contact( $content ) {
	$contact_us_redirect_url = get_field('contact_us_redirect_url', get_the_ID()) ?? '';
	$custom_content = $content;
	if($contact_us_redirect_url){
		$custom_content .= '<div class="contact_us_link" style="display:none;">'. $contact_us_redirect_url .'</div>';
	}
	return $custom_content;
}
add_filter( 'the_content', 'custom_redirection_rule_contact' );

/**
 * Custom email validation rule for elementor form
 */
function elementor_form_email_field_validation( $field, $record, $ajax_handler ) {
	// Validate email format
	if ( ! is_email( $field['value'] ) ) {
		$ajax_handler->add_error( $field['id'], esc_html__( 'Invalid email address, it must be in xx@xx.xx format.', 'hello-elementor-child' ) );
		return;
	}else {
		if(!preg_match("/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/", $field['value'])){
			$ajax_handler->add_error( $field['id'], esc_html__( 'Invalid email address, it must be in xx@xx.xx format.', 'hello-elementor-child' ) );
			return;
		}
	}

	// Do your validation here.
}
add_action( 'elementor_pro/forms/validation/email', 'elementor_form_email_field_validation', 10, 3 );

/**
 * Post Type: Team.
 */
function cptui_register_my_cpts_team() {

	$labels = [
		"name" => esc_html__( "Team", "hello-elementor-child" ),
		"singular_name" => esc_html__( "Team", "hello-elementor-child" ),
	];

	$args = [
		"label" => esc_html__( "Team", "hello-elementor-child" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => false,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "team", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-admin-users",
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "team", $args );
}
add_action( 'init', 'cptui_register_my_cpts_team' );

function cptui_register_my_cpts_event() {

	/**
	 * Post Type: Events.
	 */

	$labels = [
		"name" => esc_html__( "Events", "hello-elementor-child" ),
		"singular_name" => esc_html__( "Event", "hello-elementor-child" ),
	];

	$args = [
		"label" => esc_html__( "Events", "hello-elementor-child" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => false,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "event", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "event", $args );
	$labels = [
		"name" => esc_html__( "Webinars", "hello-elementor-child" ),
		"singular_name" => esc_html__( "Webinar", "hello-elementor-child" ),
	];

	$args = [
		"label" => esc_html__( "Webinars", "hello-elementor-child" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => false,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "webinar", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"show_in_graphql" => false,
	];

	register_post_type( "webinar", $args );
}
add_action( 'init', 'cptui_register_my_cpts_event' );

/**
 * ACF: Theme Settings.
 */
if( function_exists('acf_add_options_page') ) {
    
    acf_add_options_page(array(
        'page_title'    => 'Theme General Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
    
    /*acf_add_options_sub_page(array(
        'page_title'    => 'Theme Header Settings',
        'menu_title'    => 'Header',
        'parent_slug'   => 'theme-general-settings',
    ));
    
    acf_add_options_sub_page(array(
        'page_title'    => 'Theme Footer Settings',
        'menu_title'    => 'Footer',
        'parent_slug'   => 'theme-general-settings',
    ));*/
    
}

add_filter( 'body_class', function( $classes ) {
	return array_merge( $classes, array( 'sticky-header' ) );
} );

include_once( DISQO_TH_PATH .'/includes/job-list.php');

// File include for comment disable
include_once(get_stylesheet_directory() .'/includes/comment-disable.php');

function tg_exclude_pages_from_search_results( $query ) {
    if ( $query->is_main_query() && $query->is_search() && is_search() ) {
        $query->set( 'post__not_in', array( '154','1249','1992','1993','1994','1995','4107','4682','4684','4686','4688','4690','6654','6656','7171' ) );
    }    
}
add_action( 'pre_get_posts', 'tg_exclude_pages_from_search_results' );

/**
 * Search page by Ajax
 */
function ajax_disqo_search() {

	$stack = $_REQUEST['stack'];

	/**
	 * Parent call: Get the Search Data from the Hubspot.
	 *
	 */
	$results = hubspot_get_method(
		HUB_SEARCH . '?q=' . urlencode($_REQUEST['s']) . '&limit=100&language=en&type=BLOG_POST&property=title&property=description'
	);


	if( isset( $results['response'] ) ) {
		if( isset( $results['response']->status ) &&  $results['response']->status == 'error' ) {
			$message = "\n\nErrors:".print_r($results['response'],true);
			error_notification_email( 'crathod@codal.com','Search Result, Parent Hubspot API Error', $message );
		} else { 

			// SUCESS parent call

			if( !empty( $results['response']->results ) ) {
				foreach( $results['response']->results as $result ) {

					$tag = '';
					if ( ! empty( $result->tags ) ) {
						foreach( $result->tags as $_tag ) {
							if( in_array( $_tag, array_map('trim', explode(',',get_field('s_default_tags','option')) ) ) ) {
								$tag = esc_html( $_tag );
								break;
							}
						}	
					}
					if( $tag != '' ) {
						$stack[] = array(
							'id'    => $result->id,
							'title' => '',
							'url'   => esc_url( $result->url ),
							'desc'  => wp_strip_all_tags( $result->description ),
							'type'  => esc_html( $tag ),
						);
					}
				}
			}
		}
	} else {
		// Error by Curl method
		$message = "\n\nErrors:".print_r($results['error'],true);
		error_notification_email( 'crathod@codal.com','Search Result, Parent CURL error', $message );
	}

	?>

	<?php if( !empty( $stack ) ) : ?>

		<?php
		// pagination
		$page = $_REQUEST['pagenum'];
		$total = count( $stack ); //total items in array    
		$limit = 10; //per page    
		$totalPages = ceil( $total/ $limit ); //calculate total pages
		$page = max($page, 1); //get 1 page when $_GET['page'] <= 0
		$page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
		$offset = ($page - 1) * $limit;
		if( $offset < 0 ) $offset = 0;

		$stack_page = array_slice( $stack, $offset, $limit );
		?>

		<div class="search-list" data-total-page="<?php echo isset($total) ? $total : '0'; ?>">
			<?php
			/**
			 * Display the search data.
			 *
			 */
			?>
			<?php foreach( $stack_page as $item ) : ?>
				<?php 
				// when Hubspot data item, then we will update the $item array data
				if( isset($item['id']) ) {
					/**
					 * Nested API call in loop.
					 *
					 */
					$results_in = hubspot_get_method(
						HUB_SEARCH_INDEX . '/' . $item['id'] . '?type=BLOG_POST',
					);

					if( isset( $results_in['response'] ) ) {
						if( isset( $results_in['response']->status ) &&  $results_in['response']->status == 'error' ) {
							$message = "\n\nErrors:".print_r($results_in['response'],true);
							error_notification_email( 'crathod@codal.com','Search Result, Parent Hubspot API Error', $message );
						} else { 

							// SUCESS inner call

							$item['title'] = esc_html( $results_in['response']->fields->{'name_nested.en'}->value );

						}
					} else {
						// Error by Curl method
						$message = "\n\nErrors:".print_r($results_in['error'],true);
						error_notification_email( 'crathod@codal.com','Search Result, Inner CURL error', $message );
					}

				}
				?>
				<?php $item_json = json_encode($item); ?>
				<?php $item_json = str_ireplace( '\u2019', '’', $item_json ); ?>
				<?php $item_json = str_ireplace( $_REQUEST['s'], '<mark>'.$_REQUEST['s'].'</mark>', $item_json ); ?>
				<?php $item = json_decode($item_json,true); ?>
				<div class="search-item" <?php echo isset($item['id']) ? 'data-id="'.$item['id'].'"' : '' ?> >
					<label for=""><?php _e( $item['type'] ); ?></label>
					<a href="<?php echo wp_strip_all_tags($item['url']) ?>" target="<?php echo isset($item['id']) ? '_blank' : '' ?>">
						<?php _e( $item['title'] ) ?>
					</a>
					<p><?php _e( $item['desc'] ) ?></p>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if( $totalPages != 0 ) : ?>
			<?php $link = '?page=%d&s='.$_REQUEST['s']; ?>

			<div class="pagination-outer">

				<?php insertPagination('?page={page_num}&s='.$_REQUEST['s'], $page, $totalPages, true); ?>
			
			</div>
		<?php endif; ?>

	<?php else: ?>
		<p class="no-results"><?php esc_html_e( 'It seems we can\'t find what you\'re looking for.', 'hello-elementor' ); ?></p>
	<?php endif; ?>

	<script>
		jQuery('.search-total').html('<strong> '+jQuery('.search-list').data('total-page')+' results</strong>');
	</script>

	<?php
	wp_die();
}
add_action( 'wp_ajax_nopriv_ajax_disqo_search', 'ajax_disqo_search' );
add_action( 'wp_ajax_ajax_disqo_search', 'ajax_disqo_search' );


/** Add alt tags to the images */

function set_image_alt_tag( $attr, $attachment ) {
    // Check if alt tag is empty
    if ( empty( $attr['alt'] ) ) {
        // Use image title as alt tag
        $attr['alt'] = $attachment->post_title;
    }
    return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'set_image_alt_tag', 10, 2 );
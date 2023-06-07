<?php
/**
 * Plugin Name: Elementor Addon
 * Description: DISQO custom blocks/widgets for Elementor.
 * Version:     0.2
 * Author:      Disqo
 * Author URI:  https://developers.elementor.com/
 * Text Domain: elementor-addon
 */

$stack = $stack_1 = $stack_2 = [];
$all_blogs_options = $featured_stack = $featured_stackData = [];

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'HUB_PORTAL_ID', get_field('hub_portal_id','option') );

if ( ! defined( 'HUB_TOKEN' ) ) {
    define( 'HUB_TOKEN', get_field('hub_token','option') );
}
if ( ! defined( 'HUB_BLOG_POST' ) ) {
    define( 'HUB_BLOG_POST', get_field('hub_blog_post','option') );
}
if ( ! defined( 'HUB_TAGS' ) ) {
    define( 'HUB_TAGS', get_field('hub_tags','option') );
}

define( 'PLUGIN_URL_DISQO', plugin_dir_url( __FILE__ ) );
define( 'PLUGIN_PATH_DISQO', plugin_dir_path( __FILE__ ) );
define( 'HUB_ALL_TAGS', array( 'Articles' => 'Articles','Reports' => 'Reports','Case Studies' => 'Case Studies','Podcasts' => 'Podcasts','Events' => 'Events','Audience API' => 'Audience API','Audience Managed Services' => 'Audience Managed Services','Product Solutions' => 'Product Solutions','Ad Testing' => 'Ad Testing','Brand Lift' => 'Brand Lift','Outcomes Lift' => 'Outcomes Lift','Experience Suite' => 'Experience Suite','Marketers' => 'Marketers','Product Teams' => 'Product Teams','Researchers' => 'Researchers','Creative & Design' => 'Creative & Design','Brands' => 'Brands','Agencies' => 'Agencies','Media Platforms' => 'Media Platforms','Ad Measurement' => 'Ad Measurement','Behavioral Lift' => 'Behavioral Lift','A/B Testing' => 'A/B Testing','Message Testing' => 'Message Testing','User Discovery' => 'User Discovery','Early Stage Discovery' => 'Early Stage Discovery','Custom Survey' => 'Custom Survey','Concept Testing' => 'Concept Testing','Features Prioritization' => 'Features Prioritization','Comparison Testing' => 'Comparison Testing','Managed Services' => 'Managed Services','Market Research' => 'Market Research','Panel' => 'Panel','Corporate' => 'Corporate','Careers' => 'Careers','Culture' => 'Culture','Customer Experience (CX)' => 'Customer Experience (CX)','Consumer Research' => 'Consumer Research','Thought Leadership' => 'Thought Leadership','Diversity and Inclusion' => 'Diversity and Inclusion','Benchmarks' => 'Benchmarks','Awards'  => 'Awards' ) );

function register_hello_world_widget( $widgets_manager ) {

    require_once( __DIR__ . '/widgets/featured-block-widget.php' );
    require_once( __DIR__ . '/widgets/related-block-widget.php' );
    require_once( __DIR__ . '/widgets/resources-widget.php' );
    require_once( __DIR__ . '/widgets/who-we-help-widget.php' );
    require_once( __DIR__ . '/widgets/carousel-block-widget.php' );
    require_once( __DIR__ . '/widgets/event-featured-block-widget.php' );
    require_once( __DIR__ . '/widgets/events-block-widget.php' );
    require_once( __DIR__ . '/widgets/timeline-milestone-widget.php' );
    require_once( __DIR__ . '/widgets/webinar-featured-block-widget.php' );
    require_once( __DIR__ . '/widgets/webinars-block-widget.php' );

    $widgets_manager->register( new \Elementor_Featured_Block_Widget() );
    $widgets_manager->register( new \Elementor_Related_Block_Widget() );
    $widgets_manager->register( new \Elementor_Resources_Widget() );
    $widgets_manager->register( new \Elementor_Who_we_help_Widget() );
    $widgets_manager->register( new \Elementor_Carousel_Block_Widget() );
    $widgets_manager->register( new \Elementor_Event_Featured_Block_Widget() );
    $widgets_manager->register( new \Elementor_Events_Block_Widget() );
    $widgets_manager->register( new \Elementor_TimeLine_Widget() );
    $widgets_manager->register( new \Elementor_Webinar_Featured_Block_Widget() );
    $widgets_manager->register( new \Elementor_Webinars_Block_Widget() ); 

}
add_action( 'elementor/widgets/register', 'register_hello_world_widget' );

/**
 * Function to calculate the estimated reading time of the given text.
 * 
 * @param string $text The text to calculate the reading time for.
 * @param string $wpm The rate of words per minute to use.
 * @return Array
 */
function estimateReadingTime($text, $wpm = 200) {
    $totalWords = str_word_count(strip_tags($text));
    $minutes = floor($totalWords / $wpm);
    $seconds = floor($totalWords % $wpm / ($wpm / 60));
    
    return array(
        'minutes' => $minutes,
        'seconds' => $seconds
    );
}

/**
 * Taxonomy: Event Tags.
 */
function cptui_register_my_taxes_hubspot_tags() {

    $labels = [
        "name" => esc_html__( "HubSpot Tags", "hello-elementor-child" ),
        "singular_name" => esc_html__( "HubSpot Tag", "hello-elementor-child" ),
    ];

    
    $args = [
        "label" => esc_html__( "HubSpot Tags", "hello-elementor-child" ),
        "labels" => $labels,
        "public" => true,
        "publicly_queryable" => true,
        "hierarchical" => false,
        "show_ui" => true,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "query_var" => true,
        "rewrite" => [ 'slug' => 'hubspot-tags', 'with_front' => true, ],
        "show_admin_column" => true,
        "show_in_rest" => true,
        "show_tagcloud" => false,
        "rest_base" => "hubspot-tags",
        "rest_controller_class" => "WP_REST_Terms_Controller",
        "rest_namespace" => "wp/v2",
        "show_in_quick_edit" => true,
        "sort" => true,
        "show_in_graphql" => false,
    ];
    register_taxonomy( "hubspot-tags", [ "event", "webinar" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes_hubspot_tags' );

/**
 * Cron manual callback - HubSpot Tag taxonomies data sync.
 * 
 */
add_action( 'hubspot-tags_add_form_fields', '___add_form_field_term_meta_text' );
function ___add_form_field_term_meta_text() {
    // we will run cron job manually on hubspot-tags tax list view
    husbspot_term_sync_callback();
}

/**
 * Cron management - HubSpot Tag taxonomies data sync.
 * 
 */
function husbspot_term_sync( $schedules ) {
    $schedules['every_five_minutes'] = array(
        'interval'  => 3600 * 2,
        'display'   => __( 'Every 2 hours', 'hello-elementor-child' )
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'husbspot_term_sync' );
add_action( 'husbspot_term_sync', 'husbspot_term_sync_callback' );
if ( ! wp_next_scheduled( 'husbspot_term_sync' ) ) {
    wp_schedule_event( time(), 'every_five_minutes', 'husbspot_term_sync' );
}

/**
 * Cron callback - HubSpot Tag taxonomies data sync.
 * 
 */
function husbspot_term_sync_callback() {
    $hubspot_filter_tags = [];
    if ( false === ( $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' ) ) ) {
        // Store tags list in transient
        disqo_tags(); // under functions.php
        $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' );
    }
    if( is_array($hubspot_filter_tags) && !empty($hubspot_filter_tags) ) {
        foreach( $hubspot_filter_tags as $filter_k => $filter_val ) {

            if( is_array( $filter_val ) )
                continue;

            modify_term_callback( array('key'=>$filter_k,'name'=>$filter_val) );
        } 
    }
}

/**
 * Term modify callback - HubSpot Tag taxonomies data sync.
 * 
 */
function modify_term_callback( $tag ) {

    $args = array(
        'hide_empty' => false, // also retrieve terms which are not used yet
        'meta_query' => array(
            array(
            'key'       => 'hb_tag_id',
            'value'     => $tag['key'],
            'compare'   => '='
            )
        ),
        'taxonomy'  => 'hubspot-tags',
    );
    $terms = get_terms( $args );

    // insert hubspot-tags
    $term = wp_insert_term(
        $tag['name'],
        'hubspot-tags',
    );
    if( !isset($term->errors) ) {
        if( isset($term['term_id']) ) {
            update_term_meta( $term['term_id'], "hb_tag_id", $tag['key'] );
        }
    }

    // update hubspot-tags
    if( isset($term->error_data['term_exists']) ) {
        
        wp_update_term( $term->term_id, 'hubspot-tags', array(
                'name' => $tag['name'],
            ) );
        update_term_meta( $term->error_data['term_exists'], "hb_tag_id", $tag['key'] );
    }

}

/*
 * Resources from HubSpot by Ajax
 */
function disqo_blog_posts( $endpoint, $settings ) {

    global $stack;

    if( function_exists('hubspot_get_method') ) {

        /**
         * Get the blog posts data from the Hubspot.
         *
         */
        $results = hubspot_get_method(
            $endpoint
        );

        if( isset( $results['response'] ) ) {

            if( isset( $results['response']->status ) &&  $results['response']->status == 'error' ) {
                $message = "\n\nErrors:".print_r($results['response'],true);
                error_notification_email( 'crathod@codal.com','Resources block - blogs list, Hubspot API Error', $message );
            } else { 

                // SUCESS blog list call

                if( !empty( $results['response']->results ) ) {

                    if ( false === ( $hubspot_tags = get_transient( 'hubspot_tags' ) ) ) {
                        // Store tags list in transient
                        disqo_tags(); // under functions.php
                        $hubspot_tags = get_transient( 'hubspot_tags' );
                    }

                    if ( false === ( $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' ) ) ) {
                        // Store tags list in transient
                        disqo_tags(); // under functions.php
                        $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' );
                    }


                    $filter_by_tag = $exclude_tag = [];
                    foreach( $hubspot_filter_tags as $filter_k => $filter_val ) {
                        if( is_array( $filter_val ) )
                            continue;
                        if( in_array( $filter_val, $settings['resources-by-tag'] ) ) {
                            $filter_by_tag[] = $filter_k;
                        }
                        if( in_array( $filter_val, $settings['resources-exclude'] ) ) {
                            $exclude_tag[] = $filter_k;
                        }
                    }

                    foreach( $results['response']->results as $result ) {


                        if( $result->currentState != 'PUBLISHED' )
                            continue;
                        
                        if ( !empty($hubspot_tags) && !empty($hubspot_filter_tags) ) { 

                            $tag = '';
                            if ( ! empty( $result->tagIds ) ) {

                                $hub_tags = (array) $result->tagIds;

                                //exclude
                                if ( !empty($settings['resources-exclude']) 
                                    && !empty( array_intersect($hub_tags,$exclude_tag) ) ) {
                                    continue;
                                }

                                foreach( $result->tagIds as $_tagId ) {

                                    $find = isset( $_REQUEST['filter'] ) ? trim($_REQUEST['filter']) : '';

                                    // Default all tags data consider
                                    if( $find == "" && empty($settings['resources-by-tag']) ) {
                                        if( in_array( $_tagId, $hubspot_tags['ids'] ) ) {
                                            $stack[$result->id] = $result;
                                            break;
                                        }
                                    }
                                    elseif( $find == "" && !empty($settings['resources-by-tag']) ){
                                        if( in_array( $_tagId, $filter_by_tag ) ) {
                                            $stack[$result->id] = $result;
                                            break;
                                        }
                                    }

                                    // Only filter by tag data consider
                                    elseif( isset( $hubspot_filter_tags[$_tagId] )
                                        && in_array( $hubspot_filter_tags[$_tagId], explode( '|' , $_REQUEST['filter'] ) ) ) {
                                        
                                        $our_tags = $hubspot_tags['ids'];

                                        if( !empty($settings['resources-by-tag']) ) {
                                            $our_tags = $filter_by_tag;
                                        }

                                        if( !empty( array_intersect($hub_tags,$our_tags) ) ) {
                                            $stack[$result->id] = $result;
                                            break;
                                        }
                                    }
                                    
                                }   
                            }
                        }
                    }
                }

                if( isset( $results['response']->paging ) ) {
                    if( isset( $results['response']->paging->next ) ) {

                        /**if( count($stack) > 8 ) {
                            return $stack;
                            //pre($stack); exit('will not call next page');
                        }**/

                        disqo_blog_posts( HUB_BLOG_POST . '?limit=300&sort=-createdAt&after=' . $results['response']->paging->next->after, $settings );
                    }
                }
            }
        } else {

            // Error by Curl method
            $message = "\n\nErrors:".print_r($results['error'],true);
            error_notification_email( 'crathod@codal.com','Resources block - blogs list, CURL error', $message );
        }



        if( !empty($stack) ) {
            return (array) $stack;
        }
    }
}
function ajax_disqo_blog_posts() {

    //ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

    $by_tags_arr = trim($_REQUEST['by_tag']) == "" ? array() : explode( '|', $_REQUEST['by_tag'] );
    $ex_tags_arr = trim($_REQUEST['ex_tag']) == "" ? array() : explode( '|', $_REQUEST['ex_tag'] );
    $by_filter = trim($_REQUEST['filter']) == "" ? array() : explode( '|', $_REQUEST['filter'] );

    $settings = array( 
        'resources-per-page' => $_REQUEST['per_page'],
        'resources-by-tag' => $by_tags_arr,
        'resources-exclude' => $ex_tags_arr,
        'resources-title' => $_REQUEST['title'],
        'resources-filter' => $by_filter,
    );

    global $stack;
    $stack = [];
    $page_id = $_REQUEST['page_id'];
    $single_blog = [];

    // $stack storing
    disqo_blog_posts( HUB_BLOG_POST . '?limit=300&sort=-createdAt', $settings );

    $products = array( 'Audience API','Audience Managed Services','Product Solutions','Ad Testing','Brand Lift','Outcomes Lift','Customer Solutions','Experience Suite' );
    $roles = array( 'Marketers','Product Teams','Researchers','Creative & Design','Brands','Agencies','Media & Publishing' );
    $topics = array( 'Ad Measurement','Behavioral Lift','A/B Testing','Message Testing','User Discovery','Early Stage Discovery','Custom Survey','Concept Testing','Features Prioritization','Comparison Testing','Managed Services','Market Research','Panel','Corporate','Careers','Culture','Customer Experience (CX)','Consumer Research' );
    $types = array_map('trim', explode(',',get_field('default_tags','option')) );
    if( !empty( $settings['resources-by-tag'] ) && (in_array('News',$settings['resources-by-tag']) || in_array('Press',$settings['resources-by-tag'])) ) {
        $types = $settings['resources-by-tag'];
    }
    if( !empty( $settings['resources-by-tag'] ) && (!in_array('News',$settings['resources-by-tag']) && !in_array('Press',$settings['resources-by-tag'])) ) {
        $types = [];
    }
    //$default_selected = 'default="selected"';

    ?>
    <div class="resources-most-recent">
        <h3 class="resources-most-recent-title"><?php _e($settings['resources-title']) ?></h3>
        <div class="is_light_secondary">
            <a href="javascript:void(0);" class="elementor-button">
                <span><img src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/circle-2.png"><?php _e("Filters") ?></span>
            </a>
        </div>

        <div class="main-sfilter">
            <form class="inner-sfilter" action="<?php echo home_url( get_post_field( 'post_name', $page_id ) ); ?>">
                <input type="<?php echo get_post_field( 'post_name', $page_id ) == 'sample-page' ? 'text' : 'hidden' ?>" name="filter" class="filters">
                <div class="sfilter-title">
                    <h4><?php _e("Filters") ?></h4>
                    <a class="sfilter-close" href="javascript:void(0);"><i class="fa fa-close"></i></a>
                </div>
                <div class="sfilter-list">  
                    <div class="sfilter-item open-menu">
                        <a class="btn-tgl" href="javascript:void(0);"><?php _e('Products'); ?> <i class="fa fa-angle-down"></i></a>
                        <ul>
                            <?php foreach( $products as $k => $val ) : $k = $k + 1; ?>
                            <li>
                                <div class="fitem-check"><input class="fitem-ck-input" type="checkbox" id="products-<?php _e($k); ?>" noname="filter" value="<?php _e($val); ?>"><label  tabindex="<?php echo $k; ?>" for="products-<?php _e($k); ?>" class="fitem-ck-txt"><?php _e($val); ?></label></div>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="sfilter-item open-menu">
                        <a class="btn-tgl" href="javascript:void(0);"><?php _e('Roles'); ?> <i class="fa fa-angle-down"></i></a>
                        <ul>
                            <?php foreach( $roles as $k => $val ) : ?>
                            <li>
                                <div class="fitem-check"><input class="fitem-ck-input" type="checkbox" id="roles-<?php _e($k); ?>" noname="filter" value="<?php _e($val); ?>"><label for="roles-<?php _e($k); ?>" class="fitem-ck-txt"><?php _e($val); ?></label></div>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="sfilter-item open-menu">
                        <a class="btn-tgl" href="javascript:void(0);"><?php _e('Topics'); ?> <i class="fa fa-angle-down"></i></a>
                        <ul>
                            <?php foreach( $topics as $k => $val ) : ?>
                            <li>
                                <div class="fitem-check"><input class="fitem-ck-input" type="checkbox" id="topics-<?php _e($k); ?>" noname="filter" value="<?php _e($val); ?>"><label for="topics-<?php _e($k); ?>" class="fitem-ck-txt"><?php _e($val); ?></label></div>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php  if( !empty($types) ) : ?>
                    <div class="sfilter-item open-menu">
                        <a class="btn-tgl" href="javascript:void(0);"><?php _e('Types'); ?> <i class="fa fa-angle-down"></i></a>
                        <ul>
                            <?php foreach( $types as $k => $val ) : ?>
                            <li>
                                <div class="fitem-check"><input class="fitem-ck-input" type="checkbox" id="types-<?php _e($k); ?>" noname="filter" value="<?php _e($val); ?>" 
                                    <?php 
                                        if ( empty($settings['resources-filter']) ) {
                                            if ( in_array($val,$settings['resources-by-tag']) ) {
                                                echo $default_selected;
                                            }
                                        }else {
                                           if ( in_array($val,$settings['resources-filter']) ) {
                                                echo $default_selected;
                                            } 
                                        }
                                    ?> 
                                    ><label for="types-<?php _e($k); ?>" class="fitem-ck-txt"><?php _e($val); ?></label></div>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="sfilter-footer">
                    <a href="javascript:void(0);" class="clear-btn">
                    <?php _e('Clear all') ?><i class="fa fa-close"></i></a>
                    <div class="is_light_secondary">
                    <button type="submit" class="elementor-button disabled">
                    <?php _e('Apply filters') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if( isset( $_REQUEST['filter'] ) && trim($_REQUEST['filter']) != "" ) : ?>
    <div class="result-tag-list">
        <ul>
            <?php foreach( explode( '|' , $_REQUEST['filter'] ) as $k => $val ) : ?>
            <li>
                <a class="tag-btn" href="javascript:void(0);" data-tag="<?php _e($val); ?>"><?php _e(urldecode($val)); ?><i class="fa fa-close"></i></a>
            </li>
            <?php endforeach ?>
        </ul>
        <a href="javascript:void(0);" class="clear-btn">Clear all <i class="fa fa-close"></i></a>
    </div>
    <?php endif; ?>

    <?php
    
    // Avoid duplicate blog-post which are already showing in featured block
    if( ! empty($stack) ) {
        if( $_REQUEST['featured_blogid'] !="" ) {
            $featured_blogid = intval( $_REQUEST['featured_blogid'] );
            if( isset($stack[$featured_blogid]) ) {
                unset( $stack[$featured_blogid] );
            }
        }
    }

    if( ! empty($stack) ) : 

        if ( false === ( $hubspot_tags = get_transient( 'hubspot_tags' ) ) ) {
            // Store tags list in transient
            disqo_tags(); // under functions.php
            $hubspot_tags = get_transient( 'hubspot_tags' );
        }
        if ( false === ( $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' ) ) ) {
            // Store tags list in transient
            disqo_tags(); // under functions.php
            $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' );
        }

        // pagination
        $page = $_REQUEST['pagenum'];
        $total = count( $stack ); //total items in array    
        $limit = intval( $settings['resources-per-page'] ); //per page    
        $totalPages = ceil( $total/ $limit ); //calculate total pages
        $page = max($page, 1); //get 1 page when $_GET['paged'] <= 0
        $page = min($page, $totalPages); //get last page when $_GET['paged'] > $totalPages
        $offset = ($page - 1) * $limit;
        if( $offset < 0 ) $offset = 0;
        $stack_page = array_slice( $stack, $offset, $limit );

        ?>
        
        <ul class="resources-most-recent-box">

        <?php

        foreach( $stack_page as $id => $single_blog ) : 

            $tagId = ''; // current post's tagId
            foreach( $single_blog->tagIds as $key => $_tag ) {

                // when no tag selected | no tag applied in the filter
               /*if( !empty($settings['resources-by-tag']) || !empty($settings['resources-filter']) ) {
                    if( array_key_exists( $_tag, $hubspot_filter_tags ) ) {
                        $tagId = $_tag;
                        // if multiple tag selected in hubspot article, then we need to display only one tag which are selected in wp-admin && only when filter tag selected.
                        if( !empty($settings['resources-filter']) && in_array( $hubspot_filter_tags[$tagId], $settings['resources-filter'] ) ) {
                            break;
                        }elseif( !empty($settings['resources-by-tag']) && in_array( $hubspot_filter_tags[$tagId], $settings['resources-by-tag'] ) ) {
                            break;
                        }
                    }
                }else {
                    if( array_key_exists( $_tag, $hubspot_tags ) ) {
                        $tagId = $_tag;
                        break;
                    }
                }*/

                // When newsroom page else, other pages
                if( !empty( $settings['resources-by-tag'] ) && ( in_array('News',$settings['resources-by-tag']) || in_array('Press',$settings['resources-by-tag']) || in_array('Press release',$settings['resources-by-tag']) ) ) {
                    $types = $settings['resources-by-tag'];
                    
                    if( in_array( $hubspot_filter_tags[$_tag], $settings['resources-by-tag'] ) ) {
                        $tagId = $_tag;
                        break;
                    }
                }else {
                    if( array_key_exists( $_tag, $hubspot_tags ) ) {
                        $tagId = $_tag;
                        break;
                    }
                }
            } 
            ?>
        
            <li data-tag="<?php _e( $hubspot_filter_tags[$tagId] ) ?>" data-tagid="<?php echo $blog_id; ?>">
                <img class="resources-post" src="<?php echo $single_blog->featuredImage ?>">
                <div class="resources-post-detail">
                    <div class="resources-post-detail-top">
                        <ul class="resources-post-time-date">
                            <li class="active">
                                <div>
                                    <img src="<?php echo isset(HUB_TAG_IMGS[$hubspot_filter_tags[$tagId]]) ? esc_url(HUB_TAG_IMGS[$hubspot_filter_tags[$tagId]]) : HUB_TAG_IMGS['Default']  ?>">
                                    <span><?php echo $hubspot_tags[$tagId] ? $hubspot_tags[$tagId] : $hubspot_filter_tags[$tagId] ?></span>
                                </div>
                            </li>
                            <?php if( $hubspot_filter_tags[$tagId] == 'Articles' ) : ?>
                            <li>
                                <div>
                                    <img class=icon src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/Clock.png">
                                    <span><?php printf( "%d min read", estimateReadingTime($single_blog->postBody)['minutes'] ); // total time to read ?></span>
                                </div>
                            </li>
                            <?php elseif( $hubspot_filter_tags[$tagId] == 'Events' || $hubspot_filter_tags[$tagId] == 'Podcasts' ) : ?>
                            <li>
                                <div>
                                    <img class=icon src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/Calendar.png">
                                    <span><?php echo date( get_option( 'date_format' ), strtotime( $single_blog->publishDate ) ) // will display event date ?></span>
                                </div>
                            </li>
                            <?php endif ?>
                        </ul>
                        <p><?php _e( $single_blog->name ); ?></p>
                    </div>
                    <div class="resources-post-detail-anchor">
                        <div class="is_button_link">
                            <a target="_blank" href="<?php echo $single_blog->url; ?>" class="elementor-button"><?php 
                                if( $hubspot_filter_tags[$tagId] == 'Podcasts' ) { 
                                    _e( "Listen now" );
                                } else {
                                    _e( "Read more" );
                                } ?></a>
                        </div>
                    </div>
                </div>
            </li>

        <?php endforeach; ?>
        </ul>

        <?php // Pagination ?>

        <?php 
        $post = get_post( $page_id );
        $slug = $post->post_parent > 0 ? get_post_field( 'post_name', $post->post_parent ) .'/'.$post->post_name : $post->post_name;
        $link = home_url( $slug ) . '/page/{page_num}/';
        $link .= isset($_REQUEST['filter']) && trim($_REQUEST['filter']) != "" ? '?filter=' . urlencode($_REQUEST['filter']) : '';
        ?>

        <div class="pagination-outer resources-most-recent-page">

            <?php insertPagination($link, $page, $totalPages, true, $total); ?>
        
        </div>


    <?php else : ?>

        <div class="alert-card">
            <div class="alert-card-body">
                <h4><img src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/warning.png"></i><?php _e( 'Sorry, there are no resources based on the filters selected.' ); ?></h4>
                <p><?php _e( 'Try clearing filters to search more broadly.' ); ?></p>
            </div>
        </div>

    <?php endif; ?>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var data = '<?php echo isset( $_REQUEST['filter'] ) ? $_REQUEST['filter'] : null; ?>';

        if( data.indexOf('|') > -1 ) {
            var arr = data.split('|');
        }else{
            var arr = data.split('%7C');
        }

        $.each( arr, function( index, value ) {
            value = value.replace(/\+/g, ' ');
            $('input[value="'+value+'"]').prop('checked', true);
            $('input[value="'+value+'"]').attr('default', 'selected');
            if( value ) {
                $('form.inner-sfilter button').removeClass('disabled');
            }

            var filters = $('.fitem-ck-input:checkbox:checked').map( function() {
                return this.value;
            }).get().join("|");
            $('.filters').val(filters);

            console.log(value+': < value, do checked on load data:'+data);
        });

        function disabled_class() {
            console.log('disabled_class();');
            if( !$('.fitem-ck-input:checkbox:checked').length ) {
                $('.sfilter-footer .clear-btn').addClass('disabled');
                $('.sfilter-footer .is_light_secondary button').addClass('disabled');
            }else{
                $('.sfilter-footer .clear-btn').removeClass('disabled');
                $('.sfilter-footer .is_light_secondary button').removeClass('disabled');
            }
        }

        setTimeout(function(){ 
            disabled_class();
        },1000);

        $(document).on('change','.fitem-ck-input:checkbox', function(event){
            disabled_class();
        });
        $(document).on('click','.sfilter-footer .clear-btn, .resources-most-recent .is_light_secondary a', function(event){
            disabled_class();
        });

    });
    </script>

    <?php

    die();
}
add_action( 'wp_ajax_nopriv_ajax_disqo_resources', 'ajax_disqo_blog_posts' );
add_action( 'wp_ajax_ajax_disqo_resources', 'ajax_disqo_blog_posts' );

/*
 * Related from HubSpot by Ajax
 */
function disqo_related_blog_posts( $endpoint, $settings ) {

    global $stack, $stack_1, $stack_2;

    if( function_exists('hubspot_get_method') ) {

        /**
         * Get the blog posts data from the Hubspot.
         *
         */
        $results = hubspot_get_method(
            $endpoint
        );


        if( isset( $results['response'] ) ) {
            if( isset( $results['response']->status ) &&  $results['response']->status == 'error' ) {
                $message = "\n\nErrors:".print_r($results['response'],true);
                error_notification_email( 'crathod@codal.com','Resources block - blogs list, Hubspot API Error', $message );
            } else { 

                // SUCESS blog list call

                if( !empty( $results['response']->results ) ) {

                    if ( false === ( $hubspot_tags = get_transient( 'hubspot_tags' ) ) ) {
                        // Store tags list in transient
                        disqo_tags(); // under functions.php
                        $hubspot_tags = get_transient( 'hubspot_tags' );
                    }

                    if ( false === ( $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' ) ) ) {
                        // Store tags list in transient
                        disqo_tags(); // under functions.php
                        $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' );
                    }

                    $exclude_tag = [];
                    foreach( $hubspot_filter_tags as $filter_k => $filter_val ) {
                        if( is_array( $filter_val ) )
                            continue;
                        if( in_array( $filter_val, $settings['resources-exclude'] ) ) {
                            $exclude_tag[] = $filter_k;
                        }
                    }

                    foreach( $results['response']->results as $result ) {

                        // in related block we will display on 3 items
                        if( count($stack) > 2 && (empty($settings['resources-selected']) && empty($settings['resources-by-tags'])) ) {
                            break;
                        }
                        if( !empty($settings['resources-selected']) ) {
                            if( count($stack_1) > 2 ) {
                                break;
                            }
                        }
                        if( !empty($settings['resources-by-tags']) ) {
                            if( count($stack_2) > 2 ) {
                                break;
                            }
                        }

                        if( $result->currentState != 'PUBLISHED' )
                            continue;

                        if( in_array( $result->id, $settings['resources-selected'] ) ) {
                            $stack_1[$result->id] = $result;
                            continue;
                        }

                        if ( ! empty( $hubspot_filter_tags ) ) {

                            $tag = '';
                            if ( ! empty( $result->tagIds ) ) {

                                $hub_tags = (array) $result->tagIds;

                                //exclude
                                if ( !empty($settings['resources-exclude']) 
                                    && !empty( array_intersect($hub_tags,$exclude_tag) ) ) {
                                    continue;
                                }

                                foreach( $result->tagIds as $_tagId ) {

                                    // Default all tags data consider
                                    if( in_array( $_tagId, $hubspot_filter_tags['ids'] ) 
                                        && empty($settings['resources-by-tags']) ) {
                                        
                                            $stack[$result->id] = $result;
                                            break;

                                    }

                                    // Only filter by tag data consider
                                    elseif( isset( $hubspot_filter_tags[$_tagId] )
                                        && in_array( $hubspot_filter_tags[$_tagId], $settings['resources-by-tags'] ) ) {
                                            
                                            $hub_tags = (array) $result->tagIds;
                                            if( !empty( array_intersect($hub_tags,$hubspot_tags['ids']) ) ) {
                                                $stack_2[$result->id] = $result;
                                                break;
                                            }   
                                    }
                                    
                                }   
                            }
                        }

                    }
                }

                if( isset( $results['response']->paging ) ) {
                    if( isset( $results['response']->paging->next ) ) {

                        /**if( count($stack) > 8 ) {
                            return $stack;
                            //pre($stack); exit('will not call next page');
                        }**/

                        disqo_related_blog_posts( HUB_BLOG_POST . '?limit=300&sort=-createdAt&after=' . $results['response']->paging->next->after, $settings );
                    }
                }
            }
        } else {
            // Error by Curl method
            $message = "\n\nErrors:".print_r($results['error'],true);
            error_notification_email( 'crathod@codal.com','Resources block - blogs list, CURL error', $message );
        }


        if( !empty($stack) ) {
            return (array) $stack;
        }
    }
}
function ajax_disqo_related_blog_posts() {

    //ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

    $by_tags_arr = trim($_REQUEST['by_tags']) == "" ? array() : explode( '|', $_REQUEST['by_tags'] );
    $ex_tags_arr = trim($_REQUEST['ex_tags']) == "" ? array() : explode( '|', $_REQUEST['ex_tags'] );
    $selected    = trim($_REQUEST['selected']) == "" ? array() : explode( '|', $_REQUEST['selected'] );
    $settings = array( 
        'resources-by-tags' => $by_tags_arr,
        'resources-exclude' => $ex_tags_arr,
        'resources-selected' => $selected,
    );

    global $stack, $stack_1, $stack_2;
    $stack = $stack_1 = $stack_2 = [];


    
    // $stack storing
    disqo_related_blog_posts( HUB_BLOG_POST . '?limit=300&sort=-createdAt', $settings );

    //pre($stack);
    $stack_all = [];
    $stack_all = array_merge($stack_all,$stack_1);
    $stack_all = array_merge($stack_all,$stack_2);
    //$stack_all = array_merge($stack_all,$stack);

    if( ! empty($stack_all) ) : 

        if ( false === ( $hubspot_tags = get_transient( 'hubspot_tags' ) ) ) {
            // Store tags list in transient
            disqo_tags(); // under functions.php
            $hubspot_tags = get_transient( 'hubspot_tags' );
        }
        if ( false === ( $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' ) ) ) {
            // Store tags list in transient
            disqo_tags(); // under functions.php
            $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' );
        }

        // pagination
        $page = 1;
        $total = count( $stack_all ); //total items in array    
        $limit = 3; //per page    
        $totalPages = ceil( $total/ $limit ); //calculate total pages
        $page = max($page, 1); //get 1 page when $_GET['paged'] <= 0
        $page = min($page, $totalPages); //get last page when $_GET['paged'] > $totalPages
        $offset = ($page - 1) * $limit;
        if( $offset < 0 ) $offset = 0;
        $stack_page = array_slice( $stack_all, $offset, $limit );

        ?>
        
        <ul class="related-resources resources-most-recent-box">

            <?php
            foreach( $stack_page as $id => $single_blog ) : 

                $tagId = ''; // current post's tagId
                if( '' == $tagId ) {
                    foreach( $single_blog->tagIds as $key => $_tag ) {
                        if( array_key_exists( $_tag, $hubspot_tags ) ) {
                            $tagId = $_tag;
                            break;
                            // if multiple tag selected in hubspot article, then we need to display only one tag which are selected in wp-admin && only when filter tag selected.
                            /*if( in_array( $hubspot_filter_tags[$tagId], $settings['resources-by-tags'] ) ) {
                                break;
                                exit('wow');
                            }*/
                            
                        }
                    }
                }
                ?>
            
                <li data-tag="<?php _e( $hubspot_tags[$tagId] ) ?>" data-postid="<?php echo $blog_id; ?>" data-tagid="<?php echo $tagId; ?>">
                    <img class="resources-post" src="<?php echo $single_blog->featuredImage ?>">
                    <div class="related-resources resources-post-detail">
                        <div class="resources-post-detail-top">
                            <ul class="resources-post-time-date">
                                <li class="active">
                                    <div>
                                        <img src="<?php echo isset(HUB_TAG_IMGS[$hubspot_filter_tags[$tagId]]) ? esc_url(HUB_TAG_IMGS[$hubspot_filter_tags[$tagId]]) : HUB_TAG_IMGS['Default']  ?>">
                                        <span><?php echo $hubspot_tags[$tagId] ? $hubspot_tags[$tagId] : $hubspot_filter_tags[$tagId] ?></span>
                                    </div>
                                </li>
                                <?php if( $hubspot_tags[$tagId] == 'Articles' ) : ?>
                                <li>
                                    <div>
                                        <img class=icon src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/Clock.png">
                                        <span><?php printf( "%d min read", estimateReadingTime($single_blog->postBody)['minutes'] ); // total time to read ?></span>
                                    </div>
                                </li>
                                <?php elseif( $hubspot_tags[$tagId] == 'Events' || $hubspot_tags[$tagId] == 'Podcasts' ) : ?>
                                <li>
                                    <div>
                                        <img class=icon src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/Calendar.png">
                                        <span><?php echo date( get_option( 'date_format' ), strtotime( $single_blog->publishDate ) ) // will display event date ?></span>
                                    </div>
                                </li>
                                <?php endif ?>
                            </ul>
                            <p><?php _e( $single_blog->name ); ?></p>
                        </div>
                        <div class="resources-post-detail-anchor">
                            <div class="is_button_link">
                                <a target="_blank" href="<?php echo $single_blog->url; ?>" class="elementor-button"><?php 
                                if( $hubspot_tags[$tagId] == 'Podcasts' ) { 
                                    _e( "Listen now" );
                                } else {
                                    _e( "Read more" );
                                } ?></a>
                            </div>
                        </div>
                    </div>
                </li>

            <?php endforeach; ?>

        </ul>


    <?php else : ?>

        <div class="alert-card">
            <div class="alert-card-body">
                <h4><img src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/warning.png"></i><?php _e( 'Sorry, there are no related resources available.' ); ?></h4>
            </div>
        </div>

    <?php endif; ?>

    <?php

    die();
}
add_action( 'wp_ajax_nopriv_ajax_disqo_related', 'ajax_disqo_related_blog_posts' );
add_action( 'wp_ajax_ajax_disqo_related', 'ajax_disqo_related_blog_posts' );

/*
 * Event Block by Ajax
 */
function ajax_disqo_events() {

    $by_filter = trim($_REQUEST['filter']) == "" ? array() : explode( '|', $_REQUEST['filter'] );

    $settings = array( 
        'resources-per-page' => $_REQUEST['per_page'],
        'resources-title' => $_REQUEST['title'],
        'resources-filter' => $by_filter,
    );
    $page_id = $_REQUEST['page_id'];

    // Avoid duplicate blog-post which are already showing in featured block
    $exclude_ids = [];
    if( $_REQUEST['featured_blogid'] !="" ) {
        $featured_blogid = intval( $_REQUEST['featured_blogid'] );
        $exclude_ids = array( $featured_blogid );
    }

    $args = array(  
        'post_status' => 'publish',
        'posts_per_page' => -1,  
        'post_type' => 'event',
        'post__not_in' => $exclude_ids,
        'meta_key' => 'start_date',
        'meta_value' => date('Ymd'),
        'meta_compare' => '>=',
        'orderby' => 'meta_value',
        'order' => 'ASC',
    );

    if( !empty($settings['resources-filter']) ) {
        $args['tax_query'] = array(
            array (
                'taxonomy' => 'hubspot-tags',
                'field' => 'name',
                'terms' =>  $by_filter,
            )
        );
    }

    $loop = new WP_Query( $args );
    $events = [];
    while ( $loop->have_posts() ) : $loop->the_post();
        $event['title'] = get_the_title();
        $event['featuredImage'] = get_the_post_thumbnail_url();
        $event['postBody'] = get_the_content();
        $event['eventDate'] = get_field('start_date');
        $event['url'] = get_field('link');
        $event['button_title'] = get_field('button_title');
        $events[] = $event;
    endwhile;
      $args = array(
               'taxonomy' => 'hubspot-tags',
               'orderby' => 'name',
               'order'   => 'ASC'
           );

   $cats = get_categories($args);
    wp_reset_query();
    $products = array( 'Audience API','Audience Managed Services','Product Solutions','Ad Testing','Brand Lift','Outcomes Lift','Customer Solutions','Experience Suite' );
    $roles = array( 'Marketers','Product Teams','Researchers','Creative & Design','Brands','Agencies','Media & Publishing' );
    $topics = array( 'Ad Measurement','Behavioral Lift','A/B Testing','Message Testing','User Discovery','Early Stage Discovery','Custom Survey','Concept Testing','Features Prioritization','Comparison Testing','Managed Services','Market Research','Panel','Corporate','Careers','Culture','Customer Experience (CX)','Consumer Research' );
    $types = array_map('trim', explode(',',get_field('default_tags','option')) );
    if( !empty( $settings['resources-by-tag'] ) && (in_array('News',$settings['resources-by-tag']) || in_array('Press',$settings['resources-by-tag'])) ) {
        $types = $settings['resources-by-tag'];
    }
    if( !empty( $settings['resources-by-tag'] ) && (!in_array('News',$settings['resources-by-tag']) && !in_array('Press',$settings['resources-by-tag'])) ) {
        $types = [];
    }
    ?>
     <div class="resources-most-recent">
        <h3 class="resources-most-recent-title"><?php _e($settings['resources-title']) ?></h3>
        <div class="is_light_secondary">
            <a href="javascript:void(0);" class="elementor-button">
                <span><img src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/circle-2.png"><?php _e("Filters") ?></span>
            </a>
        </div>
        <div class="main-sfilter">
            <form class="inner-sfilter" action="<?php echo home_url( get_post_field( 'post_name', $page_id ) ); ?>">
                <input type="<?php echo get_post_field( 'post_name', $page_id ) == 'sample-page' ? 'text' : 'hidden' ?>" name="filter" class="filters">
                <div class="sfilter-title">
                    <h4><?php _e("Filters") ?></h4>
                    <a class="sfilter-close" href="javascript:void(0);"><i class="fa fa-close"></i></a>
                </div>
                <div class="sfilter-list">  
                    <div class="sfilter-item open-menu">
                        <a class="btn-tgl" href="javascript:void(0);"><?php _e('Products'); ?> <i class="fa fa-angle-down"></i></a>
                        <ul>
                            <?php foreach( $products as $k => $val ) : $k = $k + 1; ?>
                            <li>
                                <div class="fitem-check"><input class="fitem-ck-input" type="checkbox" id="products-<?php _e($k); ?>" noname="filter" value="<?php _e($val); ?>"><label  tabindex="<?php echo $k ?>" for="products-<?php _e($k); ?>" class="fitem-ck-txt"><?php _e($val); ?></label></div>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="sfilter-item open-menu">
                        <a class="btn-tgl" href="javascript:void(0);"><?php _e('Roles'); ?> <i class="fa fa-angle-down"></i></a>
                        <ul>
                            <?php foreach( $roles as $k => $val ) : ?>
                            <li>
                                <div class="fitem-check"><input class="fitem-ck-input" type="checkbox" id="roles-<?php _e($k); ?>" noname="filter" value="<?php _e($val); ?>"><label for="roles-<?php _e($k); ?>" class="fitem-ck-txt"><?php _e($val); ?></label></div>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="sfilter-item open-menu">
                        <a class="btn-tgl" href="javascript:void(0);"><?php _e('Topics'); ?> <i class="fa fa-angle-down"></i></a>
                        <ul>
                            <?php foreach( $topics as $k => $val ) : ?>
                            <li>
                                <div class="fitem-check"><input class="fitem-ck-input" type="checkbox" id="topics-<?php _e($k); ?>" noname="filter" value="<?php _e($val); ?>"><label for="topics-<?php _e($k); ?>" class="fitem-ck-txt"><?php _e($val); ?></label></div>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    
                </div>
                <div class="sfilter-footer">
                    <a href="javascript:void(0);" class="clear-btn">
                    <?php _e('Clear all') ?><i class="fa fa-close"></i></a>
                    <div class="is_light_secondary">
                    <button type="submit" class="elementor-button disabled">
                    <?php _e('Apply filters') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php if( isset( $_REQUEST['filter'] ) && trim($_REQUEST['filter']) != "" ) : ?>
    <div class="result-tag-list">
        <ul>
            <?php foreach( explode( '|' , $_REQUEST['filter'] ) as $k => $val ) : ?>
            <li>
                <a class="tag-btn" href="javascript:void(0);" data-tag="<?php _e($val); ?>"><?php _e(urldecode($val)); ?><i class="fa fa-close"></i></a>
            </li>
            <?php endforeach ?>
        </ul>
        <a href="javascript:void(0);" class="clear-btn">Clear all <i class="fa fa-close"></i></a>
    </div>
    <?php endif; ?>

    <?php

    if( !empty($events) ) : 

        if ( false === ( $hubspot_tags = get_transient( 'hubspot_tags' ) ) ) {
            // Store tags list in transient
            disqo_tags(); // under functions.php
            $hubspot_tags = get_transient( 'hubspot_tags' );
        }

        // pagination
        $page = $_REQUEST['pagenum'];
        $total = count( $events ); //total items in array    
        $limit = intval( $settings['resources-per-page'] ); //per page    
        $totalPages = ceil( $total/ $limit ); //calculate total pages
        $page = max($page, 1); //get 1 page when $_GET['paged'] <= 0
        $page = min($page, $totalPages); //get last page when $_GET['paged'] > $totalPages
        $offset = ($page - 1) * $limit;
        if( $offset < 0 ) $offset = 0;
        $stack_page = array_slice( $events, $offset, $limit );

        ?>
        
        <ul class="event-ul-resources resources-most-recent-box">

        <?php foreach( $stack_page as $k => $single_blog ) : ?>
        
            <li data-tag="<?php _e( 'Event' ) ?>" data-tagid="<?php echo $blog_id; ?>">
                <?php if( $single_blog['featuredImage'] ) : ?>
                    <img class="resources-post" src="<?php echo $single_blog['featuredImage'] ?>">
                <?php else : ?>
                    <div class="resources-post"></div>
                <?php endif; ?>
                <div class="event-resources-detail resources-post-detail">
                    <div class="resources-post-detail-top">
                        <ul class="resources-post-time-date">
                            <li class="active">
                                <div>
                                    <img src="<?php echo HUB_TAG_IMGS['Event'] ?>">
                                    <span><?php _e( 'Event' ) ?></span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img class=icon src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/Calendar.png">
                                    <span><?php echo date( get_option( 'date_format' ), strtotime( $single_blog['eventDate'] ) ) // will display event date ?></span>
                                </div>
                            </li>
                        </ul>
                        <p><?php _e( $single_blog['title'] ); ?></p>
                    </div>
                    <div class="resources-post-detail-anchor">
                        <div class="is_button_link">
                            <a target="_blank" href="<?php echo $single_blog['url']; ?>" class="elementor-button"><?php if(empty( $single_blog['button_title'])){
                                _e( 'Read More' );
                            }else{
                                _e(  $single_blog['button_title'] );
                            } ?></a>
                        </div>
                    </div>
                </div>
            </li>

        <?php endforeach; ?>
        </ul>

        <?php // Pagination ?>

        <?php 
        $post = get_post( $page_id );
        $slug = $post->post_parent > 0 ? get_post_field( 'post_name', $post->post_parent ) .'/'.$post->post_name : $post->post_name;
        $link = home_url( $slug ) . '/page/{page_num}/';
        $link .= isset($_REQUEST['filter']) && trim($_REQUEST['filter']) != "" ? '?filter=' . urlencode($_REQUEST['filter']) : '';
        ?>

        <div class="pagination-outer resources-most-recent-page">

            <?php insertPagination($link, $page, $totalPages, true, $total); ?>
        
        </div>


    <?php else : ?>

        <div class="alert-card">
            <div class="alert-card-body">
                <h4><img src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/warning.png"></i><?php _e( 'Sorry, there are no event based on the search applied.' ); ?></h4>
                <p><?php _e( 'Try clearing search keyword to search more broadly.' ); ?></p>
            </div>
        </div>

    <?php endif; ?>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var data = '<?php echo isset( $_REQUEST['filter'] ) ? $_REQUEST['filter'] : null; ?>';

        if( data.indexOf('|') > -1 ) {
            var arr = data.split('|');
        }else{
            var arr = data.split('%7C');
        }

        $.each( arr, function( index, value ) {
            value = value.replace(/\+/g, ' ');
            $('input[value="'+value+'"]').prop('checked', true);
            $('input[value="'+value+'"]').attr('default', 'selected');
            if( value ) {
                $('form.inner-sfilter button').removeClass('disabled');
            }

            var filters = $('.fitem-ck-input:checkbox:checked').map( function() {
                return this.value;
            }).get().join("|");
            $('.filters').val(filters);

            console.log(value+': < value, do checked on load data:'+data);
        });

        function disabled_class() {
            console.log('disabled_class();');
            if( !$('.fitem-ck-input:checkbox:checked').length ) {
                $('.sfilter-footer .clear-btn').addClass('disabled');
                $('.sfilter-footer .is_light_secondary button').addClass('disabled');
            }else{
                $('.sfilter-footer .clear-btn').removeClass('disabled');
                $('.sfilter-footer .is_light_secondary button').removeClass('disabled');
            }
        }

        setTimeout(function(){ 
            disabled_class();
        },1000);

        $(document).on('change','.fitem-ck-input:checkbox', function(event){
            disabled_class();
        });
        $(document).on('click','.sfilter-footer .clear-btn, .resources-most-recent .is_light_secondary a', function(event){
            disabled_class();
        });

    });
    </script>

    <?php

    die();
}
add_action( 'wp_ajax_nopriv_ajax_disqo_events', 'ajax_disqo_events' );
add_action( 'wp_ajax_ajax_disqo_events', 'ajax_disqo_events' );

/*
 * Webinar Block by Ajax
 */
function ajax_disqo_webinars() {
    
    $by_filter = trim($_REQUEST['filter']) == "" ? array() : explode( '|', $_REQUEST['filter'] );

    $settings = array( 
        'resources-per-page' => $_REQUEST['per_page'],
        'resources-title' => $_REQUEST['title'],
        'resources-filter' => $by_filter,
    );
    $page_id = $_REQUEST['page_id'];

    // Avoid duplicate blog-post which are already showing in featured block
    $exclude_ids = [];
    if( $_REQUEST['featured_blogid'] !="" ) {
        $featured_blogid = intval( $_REQUEST['featured_blogid'] );
        $exclude_ids = array( $featured_blogid );
    }

    $args = array(  
        'post_status' => 'publish',
        'posts_per_page' => -1,  
        'post_type' => 'webinar',
        'post__not_in' => $exclude_ids,
        'meta_key' => 'start_date',
        'meta_value' => date('Ymd'),
        'meta_compare' => '>=',
        'orderby' => 'meta_value',
        'order' => 'ASC',
    );
  
    if( !empty($settings['resources-filter']) ) {
        $args['tax_query'] = array(
            array (
                'taxonomy' => 'hubspot-tags',
                'field' => 'name',
                'terms' =>  $by_filter,
            )
        );
    }

    $a = new WP_Query( $args );

    $args1 = array(  
        'post_status' => 'publish',
        'posts_per_page' => -1,  
        'post_type' => 'webinar',
        'post__not_in' => $exclude_ids,
        'meta_key' => 'start_date',
        'meta_value' => date('Ymd'),
        'meta_compare' => '<',
        'orderby' => 'meta_value',
        'order' => 'DESC',
    );
    if( !empty($settings['resources-filter']) ) {
        $args1['tax_query'] = array(
            array (
                'taxonomy' => 'hubspot-tags',
                'field' => 'name',
                'terms' =>  $by_filter,
            )
        );
    }
    $b = new WP_Query( $args1 );
    
    //print_r($a);
    $all = new WP_Query();

    $all->posts = array_merge($a->posts, $b->posts);

    $all->post_count = count($all->posts);
        
    // if( "" != trim($settings['resources-filter']) ) {
    //     $args = array(  
    //         'post_status' => 'publish',
    //         'posts_per_page' => -1,  
    //         'post_type' => 'webinar',
    //         'post__not_in' => $exclude_ids,
    //         'meta_key' => 'start_date',
    //         's' => $settings['resources-filter'],
    //        /* 'meta_value' => date('Ymd'),
    //         'meta_compare' => '>=',*/
    //         'orderby' => 'meta_value',
    //         'order' => 'DESC',
    //     );
    // }
    // else{
    //     $args = array(  
    //         'post_status' => 'publish',
    //         'posts_per_page' => -1,  
    //         'post_type' => 'webinar',
    //         'post__not_in' => $exclude_ids,
    //         'meta_key' => 'start_date',
    //        /* 'meta_value' => date('Ymd'),
    //         'meta_compare' => '>=',*/
    //         'orderby' => 'meta_value',
    //         'order' => 'DESC',
    //     ); 
    // }
    //$loop = new WP_Query( $args );
    $events = [];
    while ( $all->have_posts() ) : $all->the_post();
        $event['title'] = get_the_title();
        $event['featuredImage'] = get_the_post_thumbnail_url();
        $event['postBody'] = get_the_content();
        $event['eventDate'] = get_field('start_date');
        $event['url'] = get_field('link');
        $event['button_title'] = get_field('button_title');
        $events[] = $event;
    endwhile;
    $products = array( 'Audience API','Audience Managed Services','Product Solutions','Ad Testing','Brand Lift','Outcomes Lift','Customer Solutions','Experience Suite' );
    $roles = array( 'Marketers','Product Teams','Researchers','Creative & Design','Brands','Agencies','Media & Publishing' );
    $topics = array( 'Ad Measurement','Behavioral Lift','A/B Testing','Message Testing','User Discovery','Early Stage Discovery','Custom Survey','Concept Testing','Features Prioritization','Comparison Testing','Managed Services','Market Research','Panel','Corporate','Careers','Culture','Customer Experience (CX)','Consumer Research' );
    $types = array_map('trim', explode(',',get_field('default_tags','option')) );
    if( !empty( $settings['resources-by-tag'] ) && (in_array('News',$settings['resources-by-tag']) || in_array('Press',$settings['resources-by-tag'])) ) {
        $types = $settings['resources-by-tag'];
    }
    if( !empty( $settings['resources-by-tag'] ) && (!in_array('News',$settings['resources-by-tag']) && !in_array('Press',$settings['resources-by-tag'])) ) {
        $types = [];
    }
    wp_reset_query();
    
    ?>
     <div class="resources-most-recent">
        <h3 class="resources-most-recent-title"><?php _e($settings['resources-title']) ?></h3>
        <div class="is_light_secondary">
            <a href="javascript:void(0);" class="elementor-button">
                <span><img src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/circle-2.png"><?php _e("Filters") ?></span>
            </a>
        </div>
        <div class="main-sfilter">
            <form class="inner-sfilter" action="<?php echo home_url( get_post_field( 'post_name', $page_id ) ); ?>">
                <input type="<?php echo get_post_field( 'post_name', $page_id ) == 'sample-page' ? 'text' : 'hidden' ?>" name="filter" class="filters">
                <div class="sfilter-title">
                    <h4><?php _e("Filters") ?></h4>
                    <a class="sfilter-close" href="javascript:void(0);"><i class="fa fa-close"></i></a>
                </div>
                <div class="sfilter-list">  
                    <div class="sfilter-item open-menu">
                        <a class="btn-tgl" href="javascript:void(0);"><?php _e('Products'); ?> <i class="fa fa-angle-down"></i></a>
                        <ul>
                            <?php foreach( $products as $k => $val ) : $k = $k + 1; ?>
                            <li>
                                <div class="fitem-check"><input class="fitem-ck-input" type="checkbox" id="products-<?php _e($k); ?>" noname="filter" value="<?php _e($val); ?>"><label  tabindex="<?php echo $k ?>" for="products-<?php _e($k); ?>" class="fitem-ck-txt"><?php _e($val); ?></label></div>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="sfilter-item open-menu">
                        <a class="btn-tgl" href="javascript:void(0);"><?php _e('Roles'); ?> <i class="fa fa-angle-down"></i></a>
                        <ul>
                            <?php foreach( $roles as $k => $val ) : ?>
                            <li>
                                <div class="fitem-check"><input class="fitem-ck-input" type="checkbox" id="roles-<?php _e($k); ?>" noname="filter" value="<?php _e($val); ?>"><label for="roles-<?php _e($k); ?>" class="fitem-ck-txt"><?php _e($val); ?></label></div>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="sfilter-item open-menu">
                        <a class="btn-tgl" href="javascript:void(0);"><?php _e('Topics'); ?> <i class="fa fa-angle-down"></i></a>
                        <ul>
                            <?php foreach( $topics as $k => $val ) : ?>
                            <li>
                                <div class="fitem-check"><input class="fitem-ck-input" type="checkbox" id="topics-<?php _e($k); ?>" noname="filter" value="<?php _e($val); ?>"><label for="topics-<?php _e($k); ?>" class="fitem-ck-txt"><?php _e($val); ?></label></div>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    
                </div>
                <div class="sfilter-footer">
                    <a href="javascript:void(0);" class="clear-btn">
                    <?php _e('Clear all') ?><i class="fa fa-close"></i></a>
                    <div class="is_light_secondary">
                    <button type="submit" class="elementor-button disabled">
                    <?php _e('Apply filters') ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if( isset( $_REQUEST['filter'] ) && trim($_REQUEST['filter']) != "" ) : ?>
    <div class="result-tag-list">
        <ul>
            <?php foreach( explode( '|' , $_REQUEST['filter'] ) as $k => $val ) : ?>
            <li>
                <a class="tag-btn" href="javascript:void(0);" data-tag="<?php _e($val); ?>"><?php _e(urldecode($val)); ?><i class="fa fa-close"></i></a>
            </li>
            <?php endforeach ?>
        </ul>
        <a href="javascript:void(0);" class="clear-btn">Clear all <i class="fa fa-close"></i></a>
    </div>
    <?php endif; ?>

    <?php

    if( !empty($events) ) : 

        if ( false === ( $hubspot_tags = get_transient( 'hubspot_tags' ) ) ) {
            // Store tags list in transient
            disqo_tags(); // under functions.php
            $hubspot_tags = get_transient( 'hubspot_tags' );
        }

        // pagination
        $page = $_REQUEST['pagenum'];
        $total = count( $events ); //total items in array    
        $limit = intval( $settings['resources-per-page'] ); //per page    
        $totalPages = ceil( $total/ $limit ); //calculate total pages
        $page = max($page, 1); //get 1 page when $_GET['paged'] <= 0
        $page = min($page, $totalPages); //get last page when $_GET['paged'] > $totalPages
        $offset = ($page - 1) * $limit;
        if( $offset < 0 ) $offset = 0;
        $stack_page = array_slice( $events, $offset, $limit );

        ?>
        
        <ul class="event-ul-resources resources-most-recent-box">

        <?php foreach( $stack_page as $k => $single_blog ) : ?>
        
            <li data-tag="<?php _e( 'Webinar' ) ?>" data-tagid="<?php echo $blog_id; ?>">
                <?php if( $single_blog['featuredImage'] ) : ?>
                    <img class="resources-post" src="<?php echo $single_blog['featuredImage'] ?>">
                <?php else : ?>
                    <div class="resources-post"></div>
                <?php endif; ?>
                <div class="event-resources-detail resources-post-detail">
                    <div class="resources-post-detail-top">
                        <ul class="resources-post-time-date">
                            <li class="active">
                                <div>
                                    <img src="<?php echo HUB_TAG_IMGS['Event'] ?>">
                                    <span><?php _e( 'Webinar' ) ?></span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <img class=icon src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/Calendar.png">
                                    <span><?php echo date( get_option( 'date_format' ), strtotime( $single_blog['eventDate'] ) ) // will display event date ?></span>
                                </div>
                            </li>
                        </ul>
                        <p><?php _e( $single_blog['title'] ); ?></p>
                    </div>
                    <div class="resources-post-detail-anchor">
                        <div class="is_button_link">
                            <a target="_blank" href="<?php echo $single_blog['url']; ?>" class="elementor-button"><?php if(empty( $single_blog['button_title'])){
                                _e( 'Read More' );
                            }else{
                                _e(  $single_blog['button_title'] );
                            } ?></a>
                        </div>
                    </div>
                </div>
            </li>

        <?php endforeach; ?>
        </ul>

        <?php // Pagination ?>

        <?php 
        $link = home_url( get_post_field( 'post_name', $page_id ) ) . '/page/{page_num}/';
        $link .= isset($_REQUEST['filter']) && trim($_REQUEST['filter']) != "" ? '?filter=' . urlencode($_REQUEST['filter']) : '';
        ?>

        <div class="pagination-outer resources-most-recent-page">

            <?php insertPagination($link, $page, $totalPages, true, $total); ?>
        
        </div>


    <?php else : ?>

        <div class="alert-card">
            <div class="alert-card-body">
                <h4><img src="<?php echo PLUGIN_URL_DISQO ?>/assets/images/warning.png"></i><?php _e( 'Sorry, there are no webinar based on the search applied.' ); ?></h4>
                <p><?php _e( 'Try clearing search keyword to search more broadly.' ); ?></p>
            </div>
        </div>

    <?php endif; ?>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var data = '<?php echo isset( $_REQUEST['filter'] ) ? $_REQUEST['filter'] : null; ?>';

        if( data.indexOf('|') > -1 ) {
            var arr = data.split('|');
        }else{
            var arr = data.split('%7C');
        }

        $.each( arr, function( index, value ) {
            value = value.replace(/\+/g, ' ');
            $('input[value="'+value+'"]').prop('checked', true);
            $('input[value="'+value+'"]').attr('default', 'selected');
            if( value ) {
                $('form.inner-sfilter button').removeClass('disabled');
            }

            var filters = $('.fitem-ck-input:checkbox:checked').map( function() {
                return this.value;
            }).get().join("|");
            $('.filters').val(filters);

            console.log(value+': < value, do checked on load data:'+data);
        });

        function disabled_class() {
            console.log('disabled_class();');
            if( !$('.fitem-ck-input:checkbox:checked').length ) {
                $('.sfilter-footer .clear-btn').addClass('disabled');
                $('.sfilter-footer .is_light_secondary button').addClass('disabled');
            }else{
                $('.sfilter-footer .clear-btn').removeClass('disabled');
                $('.sfilter-footer .is_light_secondary button').removeClass('disabled');
            }
        }

        setTimeout(function(){ 
            disabled_class();
        },1000);

        $(document).on('change','.fitem-ck-input:checkbox', function(event){
            disabled_class();
        });
        $(document).on('click','.sfilter-footer .clear-btn, .resources-most-recent .is_light_secondary a', function(event){
            disabled_class();
        });

    });
    </script>

    <?php

    die();
}
add_action( 'wp_ajax_nopriv_ajax_disqo_webinars', 'ajax_disqo_webinars' );
add_action( 'wp_ajax_ajax_disqo_webinars', 'ajax_disqo_webinars' );
<?php
class Elementor_Featured_Block_Widget extends \Elementor\Widget_Base {

	private $stack = array(); // options data
	private $stackData = array(); // all data

	public function get_name() {
		return 'featured_block_widget';
	}

	public function get_title() {
		return esc_html__( 'Featured Block', 'elementor-addon' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_keywords() {
		return [ 'resource', 'featured', 'disqo' ];
	}

	protected function register_controls() {

		global $all_blogs_options, $featured_stack, $featured_stackData;

		// Content Tab Start

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Featured Block', 'elementor-addon' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		// Store tags list in transient
		disqo_tags(); // under functions.php

		if( !empty($all_blogs_options) ) {

			$options = $all_blogs_options;
			$this->stack = $featured_stack;
			$this->stackData = $featured_stackData;

		}else {
			
			$options = $all_blogs_options = $this->disqo_blog_posts( HUB_BLOG_POST . '?limit=300&sort=-createdAt' );
			$featured_stack = $this->stack;
			$featured_stackData = $this->stackData;
		}

		$this->add_control(
			'featured_block',
			[
				'label' => esc_html__( 'Select blog from HubSpot', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $options,
			]
		);

		$alltag_option = [];
		
		if ( false === ( $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' ) ) ) {
			// Store tags list in transient
			disqo_tags(); // under functions.php
			$hubspot_filter_tags = get_transient( 'hubspot_filter_tags' );
		}
		if ( !empty($hubspot_filter_tags) ) {
			foreach ($hubspot_filter_tags as $key => $value) {
				if( $key == 'ids' ) continue;
				$alltag_option[$value] = $value;
			}
		}else {
			$alltag_option = HUB_ALL_TAGS; //static created in array
		}

		$this->add_control(
			'featured-by-tags',
			[
				'label' => esc_html__( 'By Tags', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'default' => '',
				'options' => $alltag_option,
			]
		);

		$this->end_controls_section();

		// Content Tab End

	}

	protected function render() {

		global $featured_stack, $featured_stackData;
		if( !empty($featured_stack) || !empty($featured_stackData) ) {
			$this->stack = $featured_stack;
			$this->stackData = $featured_stackData;
		}
		
		$settings = $this->get_settings_for_display();
		$single_blog = [];
		
		$blog_id = '';

		if( !is_array($settings['featured-by-tags']) ) {
			$settings['featured-by-tags'] = [];
		}

		// make sure hubspot_tags saved in the transient
		if ( false === ( $hubspot_tags = get_transient( 'hubspot_tags' ) ) ) {
			if( function_exists('disqo_tags') ) {
				disqo_tags(); // under functions.php
			}
		}
		if ( false === ( $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' ) ) ) {
            // Store tags list in transient
            disqo_tags(); // under functions.php
            $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' );
        }

		if( trim($settings['featured_block']) == "" && empty($settings['featured-by-tags']) ) {
			$blog_id = array_key_first($this->stack);
		}elseif( trim($settings['featured_block']) != "" ) {
			$blog_id = $settings['featured_block'];
		}elseif( !empty($settings['featured-by-tags']) ) {


			if( empty($this->stackData) ) {
				$blog_id = array_key_first($this->stack);
			}elseif( empty($settings['featured-by-tags']) ) {
				$blog_id = array_key_first($this->stack);
			}else {

				foreach( $this->stackData as $id => $item ) {

					foreach( $settings['featured-by-tags'] as $key => $_tag ) {

						if( in_array( $_tag, $item['tag'] ) ) {
							$blog_id = $id;
							break 2;
						}
					}
				}
			}
		}

		
		if( $blog_id ) : 

			$single_blog = $this->disqo_single_blog_post( HUB_BLOG_POST . '/' . $blog_id  );

			$tagId = ''; // current post's tagId
			foreach( $single_blog['tagIds'] as $key => $_tag ) {

				if( in_array('News',$settings['featured-by-tags']) || in_array('Press',$settings['featured-by-tags']) || in_array('Press release',$settings['featured-by-tags']) ) {
					if( array_key_exists( $_tag, $hubspot_filter_tags ) ) {
						$tagId = $_tag;
						// if multiple tag selected in hubspot article, then we need to display only one tag which are selected in wp-admin && only when filter tag selected.
	                    if( in_array( $hubspot_filter_tags[$tagId], $settings['featured-by-tags'] ) ) {
	                        break;
	                    }
					}
				}else {
					if( array_key_exists( $_tag, $hubspot_tags ) ) {
						$tagId = $_tag;
						break;
					}
				}
			}
			?>

			<div id="featured-block" class="post-section" data-tag="<?php echo esc_attr( $hubspot_tags[$tagId] ) ?>" data-tagid="<?php echo esc_attr($blog_id); ?>">
				<div class="post-img">
					<img src="<?php echo esc_url($single_blog['featuredImage']) ?>">
				</div>
				<div class="post-detail">
					<ul>
						<li class="active">
                            <div>
                                <img src="<?php echo isset(HUB_TAG_IMGS[$hubspot_tags[$tagId]]) ? esc_url(HUB_TAG_IMGS[$hubspot_tags[$tagId]]) : esc_url(HUB_TAG_IMGS['Default']) ?>">
                                <span><?php echo $hubspot_tags[$tagId] ? esc_html($hubspot_tags[$tagId]) : esc_html($hubspot_filter_tags[$tagId]) ?></span>
                            </div>
                        </li>
						<li>
							<div>
								<img class=icon src="<?php echo esc_url(PLUGIN_URL_DISQO); ?>/assets/images/star.png">
								<span><?php echo esc_html( "Featured" ) ?></span>
							</div>
						</li>
						<?php if( $hubspot_tags[$tagId] == 'Articles' ) : ?>
						<li>
							<div>
								<img class=icon src="<?php echo esc_url(PLUGIN_URL_DISQO); ?>/assets/images/Clock.png">
								<span><?php echo (int)estimateReadingTime($single_blog['postBody'])['minutes']; echo esc_html('&nbsp;min read'); // total time to read ?></span>
							</div>
						</li>
						<?php elseif( $hubspot_tags[$tagId] == 'Events' || $hubspot_tags[$tagId] == 'Podcasts' ) : ?>
						<li>
							<div>
								<img class=icon src="<?php echo esc_url(PLUGIN_URL_DISQO); ?>/assets/images/Calendar.png">
								<span><?php echo esc_html( gmdate( get_option( 'date_format' ), strtotime( $single_blog['publishDate'] ) ) ); // will display event date ?></span>
							</div>
						</li>
						<?php endif ?>
					</ul>
					<h4><?php echo esc_html( $single_blog['name'] ); ?></h4>
					<?php $postSummary = wp_strip_all_tags( $single_blog['postSummary'] ); ?>
					<?php $postSummary = trim( substr_replace( $postSummary, "", 90 ) ) . "..."; ?>
					<p><?php echo wp_kses_post( $postSummary ); ?></p>
					<div class="is_button_link">
						<a target="_blank" href="<?php echo esc_url($single_blog['url']); ?>" class="elementor-button"><?php 
						if( $hubspot_tags[$tagId] == 'Podcasts' ) { 
							echo esc_html( "Listen now" );
						} else {
							echo esc_html( "Read more" );
						} ?></a>
					</div>
				</div>
			</div>

		<?php else : ?>
			<div class="post-section">
			</div>
		<?php endif; ?>
		<?php
	}

	public function disqo_blog_posts( $endpoint ) {
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
					error_notification_email( 'crathod@codal.com','Featured block - blogs list, Hubspot API Error', $message );
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

						foreach( $results['response']->results as $result ) {

							if( $result->currentState != 'PUBLISHED' )
								continue;

							/*if ( ! empty( $hubspot_filter_tags ) ) {

								$tag = '';
								if ( ! empty( $result->tagIds ) ) {
									$_tags_arr = [];
									foreach( $result->tagIds as $_tagId ) {
										if( in_array( $_tagId, $hubspot_filter_tags['ids'] ) ) {
											
											$this->stack[$result->id] = $result->name;
											$_tags_arr[] = $hubspot_filter_tags[$_tagId];
											////break;
										}
									}
									$this->stackData[$result->id] = array( 
										'data' => $result, 
										'tag' => $_tags_arr,
									);
									
								}
								
							}
							*/
							if ( ! empty( $hubspot_tags ) ) {

								$tag = '';
								if ( ! empty( $result->tagIds ) ) {
									$_tags_arr = $__tag_arr = [];
									foreach( $result->tagIds as $_tagId ) {
										$__tag_arr[] = $hubspot_filter_tags[$_tagId];
									}
									foreach( $result->tagIds as $_tagId ) {
										if( in_array( $_tagId, $hubspot_tags['ids'] ) ) {
											
											$this->stack[$result->id] = $result->name;
											$_tags_arr = $__tag_arr;
											////break;
										}
									}
									$this->stackData[$result->id] = array( 
										'data' => $result, 
										'tag' => $_tags_arr,
									);
									
								}
							
							}
							else { // it will return all blog posts instead specific by tag
								//$this->stack[$result->id] = $result->name;
							}
						}
					}

					if( isset( $results['response']->paging ) ) {
						if( isset( $results['response']->paging->next ) ) {

							/**if( count($this->stack) > 8 ) {
								return $this->stack;
								//pre($this->stack); exit('will not call next page');
							}**/

							$this->disqo_blog_posts( HUB_BLOG_POST . '?limit=300&sort=-createdAt&after=' . $results['response']->paging->next->after );
						}
					}
				}
			} else {
				// Error by Curl method
				$message = "\n\nErrors:".print_r($results['error'],true);
				error_notification_email( 'crathod@codal.com','Featured block - blogs list, CURL error', $message );
			}

			//pre($this->stack); exit;

			if( !empty($this->stack) ) {
				return $this->stack;
			}
		}

		return [];
	}

	public function disqo_single_blog_post( $endpoint ) {
		if( function_exists('hubspot_get_method') ) {

			/**
			 * Get the single blog post data from the Hubspot.
			 *
			 */
			$results = hubspot_get_method(
				$endpoint
			);

			if( isset( $results['response'] ) ) {
				if( isset( $results['response']->status ) &&  $results['response']->status == 'error' ) {
					$message = "\n\nErrors:".print_r($results['response'],true);
					error_notification_email( 'crathod@codal.com','Featured Block - single post, Hubspot API Error', $message );
				} else { 

					// SUCESS single post call
					return (array) $results['response'];

				}
			} else {
				// Error by Curl method
				$message = "\n\nErrors:".print_r($results['error'],true);
				error_notification_email( 'crathod@codal.com','Featured Block - single post, CURL error', $message );
			}

		}
		return false;
	}

}
<?php
class Elementor_Related_Block_Widget extends \Elementor\Widget_Base {

	private $stack = array(); // options data
	private $stackData = array(); // all data

	public function get_name() {
		return 'related_block_widget';
	}

	public function get_title() {
		return esc_html__( 'Related Block', 'elementor-addon' );
	}

	public function get_icon() {
		return 'eicon-product-related';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_keywords() {
		return [ 'resource', 'related', 'disqo' ];
	}

	protected function register_controls() {

		if( ! wp_script_is( 'disqo-related-ajax' ) ) {
			wp_register_script( 'disqo-related-ajax', PLUGIN_URL_DISQO ."assets/js/disqo-related-ajax.js", array('jquery'), filemtime( PLUGIN_URL_DISQO . 'assets/js/disqo-related-ajax.js' ), true );
			wp_enqueue_script( 'disqo-related-ajax' );
			wp_localize_script( 'disqo-related-ajax', 'disqo_related', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			));
		}

		// Content Tab Start

		global $all_blogs_options;

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Related Block', 'elementor-addon' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		// Store tags list in transient
		disqo_tags(); // under functions.php

		if( !empty($all_blogs_options) ) {
			$options = $all_blogs_options;
		}else {
			$options = $all_blogs_options = $this->disqo_blog_posts( HUB_BLOG_POST . '?limit=300&sort=-createdAt' );
		}

		$this->add_control(
			'selected-related-posts',
			[
				'label' => esc_html__( 'Select blog from HubSpot', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'default' => '',
				'options' => $options,
			]
		);

		$options = [];
		
		if ( false === ( $hubspot_filter_tags = get_transient( 'hubspot_filter_tags' ) ) ) {
			// Store tags list in transient
			disqo_tags(); // under functions.php
			$hubspot_filter_tags = get_transient( 'hubspot_filter_tags' );
		}
		if ( !empty($hubspot_filter_tags) ) {
			foreach ($hubspot_filter_tags as $key => $value) {
				if( $key == 'ids' ) continue;
				$options[$value] = $value;
			}
		}else {
			$options = HUB_ALL_TAGS; //static created in array
		}

		$this->add_control(
			'resources-by-tags',
			[
				'label' => esc_html__( 'By Tags', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'default' => '',
				'options' => $options,
			]
		);

		$this->add_control(
			'resources-tag-exclude',
			[
				'label' => esc_html__( 'Exclude Tag', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $options,
			]
		);

		$this->end_controls_section();

		// Content Tab End

	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		if( is_array( $settings['resources-by-tags'] ) ) {
			$tags = implode('|', $settings['resources-by-tags'] );
		}else {
			$tags = $settings['resources-by-tags'];
		}
		if( !empty($settings['resources-tag-exclude']) && is_array($settings['resources-tag-exclude']) ) {
			$exclude = implode('|', $settings['resources-tag-exclude'] );
		}else {
			$exclude = "";
		}
		if( is_array( $settings['selected-related-posts'] ) ) {
			$selected = implode('|', $settings['selected-related-posts'] );
		}else {
			$selected = $settings['selected-related-posts'];
		}
		?>

		<div id="disqo-related-block" style="min-height: 180px;"
			data-selected="<?php echo esc_attr($selected); ?>"
			data-by-tags="<?php echo esc_attr($tags); ?>" 
			data-ex-tags="<?php echo esc_attr($exclude); ?>" >

			<div class="loader-over" style="display:none"><div class="loader"></div></div>
			<div class="related-results"></div>

		</div>
		
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

							/*if ( ! empty( $hubspot_tags ) ) {

								$tag = '';
								if ( ! empty( $result->tagIds ) ) {
									$_tags_arr = [];
									foreach( $result->tagIds as $_tagId ) {
										if( in_array( $_tagId, $hubspot_tags['ids'] ) ) {
											
											$this->stack[$result->id] = $result->name;
											$_tags_arr[] = $hubspot_tags[$_tagId];
											////break;
										}
									}
									$this->stackData[$result->id] = array( 
										'data' => $result, 
										'tag' => $_tags_arr,
									);
									
								}
								
							}*/
							if ( ! empty( $hubspot_filter_tags ) ) {

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
							else { // it will return all blog posts instead specific by tag
								$this->stack[$result->id] = $result->name;
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

}
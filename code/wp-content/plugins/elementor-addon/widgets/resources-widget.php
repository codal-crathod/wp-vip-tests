<?php
class Elementor_Resources_Widget extends \Elementor\Widget_Base {

	private $stack = array(); // Search data

	public function get_name() {
		return 'resources_widget';
	}

	public function get_title() {
		return esc_html__( 'Resources', 'elementor-addon' );
	}

	public function get_icon() {
		return 'eicon-posts-grid';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_keywords() {
		return [ 'resource', 'hubspot', 'disqo' ];
	}

	protected function register_controls() {

		if( ! wp_script_is( 'disqo-resources-ajax' ) ) {
			wp_register_script( 'disqo-resources-ajax', PLUGIN_URL_DISQO ."assets/js/disqo-resources-ajax.js", array('jquery'), filemtime( PLUGIN_PATH_DISQO . 'assets/js/disqo-resources-ajax.js' ), true );
			wp_enqueue_script( 'disqo-resources-ajax' );
			wp_localize_script( 'disqo-resources-ajax', 'disqo_resources', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			));
		}

		// Content Tab Start

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Resources', 'elementor-addon' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'resources-title',
			[
				'label' => esc_html__( 'Title', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Most recent', 'elementor-addon' ),
				'placeholder' => esc_html__( 'Title for resources', 'elementor-addon' ),
			]
		);

		$this->add_control(
			'resources-per-page',
			[
				'label' => esc_html__( 'Per Page', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '6',
				'options' => [
					'3' => esc_html__( '3 items per page', 'elementor-addon' ),
					'6' => esc_html__( '6 items per page', 'elementor-addon' ),
					'9' => esc_html__( '9 items per page', 'elementor-addon' ),
					'12' => esc_html__( '12 items per page', 'elementor-addon' ),
				],
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
			'resources-by-tag',
			[
				'label' => esc_html__( 'By Tag', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'multiple' => true,
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
		global $post;
		$settings = $this->get_settings_for_display();

		if( !empty($settings['resources-by-tag']) && is_array($settings['resources-by-tag']) ) {
			$tags = implode('|', $settings['resources-by-tag'] );
		}else {
			$tags = "";
		}

		if( !empty($settings['resources-tag-exclude']) && is_array($settings['resources-tag-exclude']) ) {
			$exclude = implode('|', $settings['resources-tag-exclude'] );
		}else {
			$exclude = "";
		}

		if ( isset($_GET['action']) && $_GET['action'] == 'elementor' ) {
			_e('Please check the results on the front');
			?><script>setTimeout(function(){ jQuery('.loader').hide(); }, 6000);</script><?php
		} 

		$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : '';
		$slug = $post->post_parent > 0 ? get_post_field( 'post_name', $post->post_parent ) .'/'.$post->post_name : $post->post_name;
		?>

		<div id="resources-page" 
			data-filter="<?php echo esc_attr($filter); ?>"
			data-current-page="<?php echo get_query_var( 'paged', 1 ) < 1 ? 1 : esc_attr( get_query_var( 'paged', 1 ) ) ?>"
			data-url="<?php echo esc_url(home_url($slug)) ?>"
			data-per-page="<?php echo esc_attr($settings['resources-per-page']) ?>" 
			data-by-tag="<?php echo esc_attr($tags); ?>" 
			data-ex-tag="<?php echo esc_attr($exclude); ?>" 
			data-title="<?php echo esc_attr($settings['resources-title']) ?>" 
			data-page-id="<?php echo get_the_ID() ?>" >
				<div class="loader-over"><div class="loader"></div></div>
				<div class="resources-results"></div>
		</div>
		<?php	
	}

}
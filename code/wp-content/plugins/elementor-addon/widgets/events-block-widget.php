<?php
class Elementor_Events_Block_Widget extends \Elementor\Widget_Base {

	private $stack = array(); // options data

	public function get_name() {
		return 'events_block_widget';
	}

	public function get_title() {
		return esc_html__( 'Events Block', 'elementor-addon' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_keywords() {
		return [ 'events', 'event', 'disqo' ];
	}

	protected function register_controls() {

		if( ! wp_script_is( 'disqo-events-ajax' ) ) {
			wp_register_script( 'disqo-events-ajax', PLUGIN_URL_DISQO ."assets/js/disqo-events-ajax.js", array('jquery'), filemtime( PLUGIN_PATH_DISQO . 'assets/js/disqo-events-ajax.js' ), true );
			wp_enqueue_script( 'disqo-events-ajax' );
			wp_localize_script( 'disqo-events-ajax', 'disqo_events', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			));
		}


		// Content Tab Start

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Events Block', 'elementor-addon' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$options = [];
		$args = array(  
			'post_status' => 'publish',
			'posts_per_page' => -1,  
			'post_type' => 'event',
			'meta_key' => 'start_date',
			'meta_value' => gmdate('Ymd'),
			'meta_compare' => '>=',
			'orderby' => 'meta_value',
			'order' => 'ASC',
		);

		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) : $loop->the_post();
			$options[get_the_ID()] = get_the_title();
		endwhile;
		wp_reset_query();
		$this->stack = $options;

		$this->add_control(
			'events-title',
			[
				'label' => esc_html__( 'Title', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Most recent', 'elementor-addon' ),
				'placeholder' => esc_html__( 'Title for events', 'elementor-addon' ),
			]
		);

		$this->add_control(
			'events-per-page',
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

		$this->end_controls_section();

		// Content Tab End

	}

	protected function render() {
		global $post;
		$settings = $this->get_settings_for_display();
		$blog_id = $settings['events_block'];
		if ( isset($_GET['action']) && $_GET['action'] == 'elementor' ) {
			_e('Please check the results on the front');
			?><script>setTimeout(function(){ jQuery('.loader').hide(); }, 6000);</script><?php
		}
		?>
		<div id="resources-page" 
			data-filter="<?php echo isset( $_REQUEST['filter'] ) ? esc_attr($_REQUEST['filter']) : null; ?>"
			data-current-page="<?php echo get_query_var( 'paged', 1 ) < 1 ? 1 : esc_attr(get_query_var( 'paged', 1 )) ?>"
			data-url="<?php echo esc_url(home_url( $post->post_name )) ?>"
			data-per-page="<?php echo (int) $settings['events-per-page'] ?>" 
			data-title="<?php echo esc_attr($settings['events-title']) ?>" 
			data-page-id="<?php echo get_the_ID() ?>" >
				<div class="loader-over"><div class="loader"></div></div>
				<div class="resources-results"></div>
		</div>
		<?php
		
	}

}
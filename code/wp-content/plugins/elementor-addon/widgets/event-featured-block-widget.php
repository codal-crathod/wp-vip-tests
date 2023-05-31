<?php
class Elementor_Event_Featured_Block_Widget extends \Elementor\Widget_Base {

	private $stack = array(); // options data

	public function get_name() {
		return 'event_featured_block_widget';
	}

	public function get_title() {
		return esc_html__( 'Event Featured Block', 'elementor-addon' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_keywords() {
		return [ 'event', 'featured', 'disqo' ];
	}

	protected function register_controls() {


		// Content Tab Start

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Event Featured Block', 'elementor-addon' ),
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
			'event_featured_block',
			[
				'label' => esc_html__( 'Please choose event to display', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $options,
			]
		);

		$this->end_controls_section();

		// Content Tab End

	}

	protected function render() {
		
		$settings = $this->get_settings_for_display();
		$single_blog = [];
		$blog_id = $settings['event_featured_block'];
		if( !$blog_id ) {
			$blog_id = array_key_first($this->stack);
		}

		if( $blog_id ) : ?>

			<?php $event = get_post($blog_id); ?>
			<?php if ( $event ) : ?>
				<div id="event-featured-block" class="post-section" data-tag="Event" data-eventID="<?php echo (int) $blog_id; ?>">
					<div class="post-img">
						<?php if ( $img = get_the_post_thumbnail_url($blog_id) ) : ?>
							<img src="<?php echo esc_url($img); ?>">
						<?php endif; ?>
					</div>
					<div class="post-detail">
						<ul>
							<li class="active">
								<div>
									<img class=icon src="<?php echo esc_url(HUB_TAG_IMGS['Event']) ?>">
									<span><?php _e( 'Events' ) ?></span>
								</div>
							</li>
							<li>
								<div>
									<img class=icon src="<?php echo esc_url( PLUGIN_URL_DISQO ) ?>/assets/images/star.png">
									<span><?php _e( "Featured" ) ?></span>
								</div>
							</li>
							<li>
								<div>
									<img class=icon src="<?php echo esc_url( PLUGIN_URL_DISQO ) ?>/assets/images/Calendar.png">
									<span><?php echo esc_html( gmdate( get_option( 'date_format' ), strtotime( get_field('start_date',$blog_id) ) ) ) // will display event date ?></span>
								</div>
							</li>
						</ul>
						<h4><?php echo esc_html( $event->post_title ); ?></h4>
						<?php $postSummary = wp_strip_all_tags( $event->post_title )	; ?>
						<?php $postSummary = trim( substr_replace( $postSummary, "", 90 ) ) . "..."; ?>
						<p><?php echo wp_kses_post( $postSummary ); ?></p>
						<div class="is_button_link">
							<a target="_blank" href="<?php echo esc_url(get_field('link',$blog_id)); ?>" class="elementor-button"><?php _e( "Read more" ); ?></a>
						</div>
					</div>
				</div>
			<?php endif; ?>

		<?php else : ?>
			<div class="post-section">
			</div>
		<?php endif; ?>

		<?php
	}

}
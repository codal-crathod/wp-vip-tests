<?php
class Elementor_Carousel_Block_Widget extends \Elementor\Widget_Base {

	private $stack = array(); // options data
	private $stackData = array(); // all data

	public function get_name() {
		return 'carousel_block_widget';
	}

	public function get_title() {
		return esc_html__( 'Carousel Block', 'elementor-addon' );
	}

	public function get_icon() {
		return 'eicon-call-to-action';
	}

	public function get_categories() {
		return [ 'basic' ];
	}

	public function get_keywords() {
		return [ 'resource', 'carousel', 'disqo' ];
	}

	protected function register_controls() {

		global $all_blogs_options, $carousel_stack, $carousel_stackData;

		// Content Tab Start

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Carousel Block', 'elementor-addon' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'select_design',
			[
				'label' => esc_html__( 'Select Design', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'label_block' => true,
				'multiple' => false,
				'options' => [
					'our_people'  => esc_html__( 'Our People', 'textdomain' ),
					'investors' => esc_html__( 'Investors', 'textdomain' ),
				],
				'default' => [ 'title', 'description' ],
			]
		);

		$this->add_control(
			'carousel_block',
			[
				'label' => esc_html__( 'Add Items', 'elementor-addon' ),
				'label_block' => true,
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'image',
						'label' => esc_html__( 'Image', 'elementor-addon' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => \Elementor\Utils::get_placeholder_image_src(),
						],
						'label_block' => true,
					],

					[
						'name' => 'embed',
						'label' => esc_html__( 'Embed Video', 'elementor-addon' ),
						'type' => \Elementor\Controls_Manager::CODE,
						'default' => esc_html__( '' , 'elementor-addon' ),
						'show_label' => true,
					],
					[
						'name' => 'name',
						'label' => esc_html__( 'Name', 'elementor-addon' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => esc_html__( '', 'elementor-addon' ),
						'placeholder' => esc_html__( 'Enter the name', 'elementor-addon' ),
					],
					[
						'name' => 'title',
						'label' => esc_html__( 'Title', 'elementor-addon' ),
						'label_block' => true,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => esc_html__( '', 'elementor-addon' ),
						'placeholder' => esc_html__( 'Enter the title', 'elementor-addon' ),
					],
					[
						'name' => 'description',
						'label' => esc_html__( 'Description', 'elementor-addon' ),
						'type' => \Elementor\Controls_Manager::TEXTAREA ,
						'default' => esc_html__( '', 'elementor-addon' ),
						'placeholder' => esc_html__( 'Type your description here', 'elementor-addon' ),
					]
				]
			]
		);

		$this->end_controls_section();

		// Content Tab End

	}

	protected function render() {
		
		$settings = $this->get_settings_for_display();
		$slide_count = 1;
		//pre($settings['carousel_block']);
		if ( $settings['select_design'] == "investors" ) {
			if ( $settings['carousel_block'] ) { ?>
			<div class="our-slick-section">
				<div class="investors-slider"><?php
					//print_r($item);
					foreach ( $settings['carousel_block'] as $item ) { ?>
				    <div class="<?php echo ($slide_count == 1) ? '' : 'slick-slide' ?>">
				        <div class="slide-card">
				            <div class="caption-area">
				                <p><?php echo $item['description']; ?></p>
				            </div>
				            <div class="investor-details">
				                <h3><?php echo $item['name']; ?>
				                    <span><?php echo $item['title']; ?></span>
				                </h3>				              
				                 <figure>
				                    <img src="<?php echo $item['image']['url']; ?>">
				                </figure>
				            </div>    
				        </div> 
				    </div> <?php 
				 	$slide_count++; }?>				    	
				</div>
				<div class="slider-controls">
					<button type="button" class="slide-m-prev">prev</button>
					<div class="slide-m-dots"></div>
					<button type="button" class="slide-m-next">next</button>
				</div> 
			</div><?php
			}
		}else {
			if ( $settings['carousel_block'] ) { ?>
				<div class="our-slick-section">
					<div class="our-people-slider"><?php
						$i =1;
						foreach ( $settings['carousel_block'] as $item ) { ?>
						    <div class="<?php echo ($slide_count == 1) ? '' : 'slick-slide' ?>">
							    <div class="our-people-item">

							    	<?php if($item['embed']) : ?>
							            <a class="people-img" href="javascript:void(0)" data-function="model" data-target="#carousel-video-popup">
							            	<div class="embed-code" style="display:none !important;opacity: 0 !important;">
							            		<?php echo $item['embed']; ?>
							            	</div>
								            <figure>
								                <img src="<?php echo $item['image']['url']; ?>">
								            </figure>
							                <i class="fa fa-youtube"></i>
							            </a>
							        <?php else: ?>
							        	<a class="people-img" href="javascript:void(0)">
								            <figure>
								                <img src="<?php echo $item['image']['url']; ?>">
								            </figure>
							            </a>
							        <?php endif; ?>
						            <div class="caption-area">
						                <h3><?php echo $item['name']; ?>
						                <span><?php echo $item['title']; ?></span>
						                </h3>
						                <p><?php echo $item['description']; ?></p>
						            </div>
						        </div>
						    </div><?php 
						$slide_count++; }?>
					</div>
					<div class="slider-controls">
						<button type="button" class="slide-m-prev">prev</button>
						<div class="slide-m-dots"></div>
						<button type="button" class="slide-m-next">next</button>
					</div>  
				</div> <?php
				$i = 1;
				//foreach ( $settings['carousel_block'] as $item ) {
					//if($item['embed']){ ?>
						<div class="model our-people-model " id="carousel-video-popup">
					        <div class="model-contain fullwidth">
					            <div class="model-header">
					                <a  href="javascript:void(0)" class="model-close"><i class="fa fa-close"></i></a>
					            </div>
					            <div class="model-body no-pad">
					            </div>
					        </div>
					    </div><?php
					    $i++;
					//}
				//}
			}
		}
		?>
		<script>
		jQuery('body').on('click','[data-function=model]',function(e){
			e.preventDefault();
        	var target = jQuery(this).data('target');
        	var embed = jQuery(this).find('.embed-code').html();
        	jQuery( target ).find('.model-body').html(embed);
		});
		</script>
		<?php
	}


	public function disqo_single_blog_post( $endpoint ) {

	}

}
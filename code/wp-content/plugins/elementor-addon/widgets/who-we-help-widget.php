<?php
class Elementor_Who_we_help_Widget extends \Elementor\Widget_Base {
	public function get_name() {
		return 'who_we_help_box';
	}
	public function get_icon() {
		return 'eicon-image-box';
	}
	public function get_title() {
		return __( 'Who we help', 'elementor-addon' );
	}
	public function get_categories() {
		return [ 'basic' ];
	}
	public function get_keywords() {
		return [ 'tabs', 'who we help', 'disqo' ];
	}
	protected function _register_controls() {
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Who We Help', 'elementor-addon' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'list_heading', [
				'label' => __( 'Tab Heading', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Tab Heading' , 'elementor-addon' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'list_image',
			[
				'label' => __( 'Media', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);
		$repeater->add_control(
			'list_title', [
				'label' => __( 'Tab Title', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Tab Title' , 'elementor-addon' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'list_content', [
				'label' => __( 'Content', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => __( 'List Content' , 'elementor-addon' ),
				'show_label' => false,
			]
		);
		$repeater->add_control(
			'list_button', [
				'label' => __( 'Button Title', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Button Title' , 'elementor-addon' ),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'list_link', [
				'label' => __( 'Link', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::URL,
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
				],
				'show_label' => true,
			]
		);
		$this->add_control(
			'list',
			[
				'label' => __( 'Repeater List', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_heading' => __( 'Tab Heading', 'elementor-addon' ),
						'list_image' => __( 'Tab Media', 'elementor-addon' ),
						'list_title' => __( 'Tab Heading', 'elementor-addon' ),
						'list_content' => __( 'Tab content. Click the edit button to change this text.', 'elementor-addon' ),
						'list_button' => __( 'Button Title', 'elementor-addon' ),
						'list_link' => [
							'url' => '#',
							'is_external' => true,
							'nofollow' => true,
						],
					],
					[
						'list_heading' => __( 'Tab Heading', 'elementor-addon' ),
						'list_image' => __( 'Tab Media', 'elementor-addon' ),
						'list_title' => __( 'Tab Heading', 'elementor-addon' ),
						'list_content' => __( 'Tab content. Click the edit button to change this text.', 'elementor-addon' ),
						'list_button' => __( 'Button Title', 'elementor-addon' ),
						'list_link' => [
							'url' => '#',
							'is_external' => true,
							'nofollow' => true,
						],
					],
				],
				'title_field' => '{{ list_heading }}',
			]
		);
		$this->end_controls_section();
	}
	protected function render() {
		$settings = $this->get_settings_for_display();
		if ( $settings['list'] ) {?>
			<div class="mtabs no-mob">
				<div class="mtabs-nav" id="example">
            		<ul class="mtabs_list"> <?php
            			$i = 0;
						foreach ( $settings['list'] as $item ) {
							$i++; ?>
	            			<li class="mtabs-item <?php if($i == 1){ echo 'active'; } ?>"><?php
	            				$tab_id = "#mtabs-".$i; ?>
			                    <a class="mtabs-link" href="<?php echo esc_url($tab_id); ?>"><?php echo esc_html($item['list_heading']); ?></a>
			                </li><?php
			            }?>
			        </ul>
				</div>
				<div class="mtabs-wrap"><?php
				$i = 0;
	            foreach ( $settings['list'] as $item ) {
	            	$i++;
	            	$tab_id_desk = "mtabs-".$i;  ?>
	            	<div class="mtabs-content <?php if($i == 1){ echo 'active'; }?>" id="<?php echo esc_attr($tab_id_desk); ?>">
		                <div class="who-we-help">
		                    <div class="elementor-tabs-contain">
		                        <div class="elementor-tabs-contain-left">
		                            <h3><?php echo esc_html($item['list_title']); ?></h3>
		                            <?php echo wp_kses_post($item['list_content']); ?>
		                            <div class="is_dark_primary"><a href="<?php echo esc_url($item['list_link']['url']); ?>" target="<?php echo esc_attr($item['list_link']['is_external']) ? "_blank" : '' ?>" rel="<?php echo ($item['list_link']['nofollow']) ? "nofollow" : '' ?>" class="elementor-button-link elementor-button elementor-size-sm"><?php echo esc_html($item['list_button']); ?></a></div>
		                        </div>
		                        <div class="elementor-tabs-contain-right"><?php
		                        	if( isset($item['list_image']['id']) && !empty( $item['list_image']['id'] ) ){?>
		                        		<img decoding="async"
		                                src="<?php echo wp_get_attachment_image( $item['list_image']['id'], 'full' ); ?>"><?php
		                            }?>
		                        </div>
		                    </div>
		                </div>
		            </div><?php
					}?>
				</div>
			</div>
			<div class="mobtabs only-mob"><?php
				$i = 0;
				foreach ( $settings['list'] as $item ) {
					$i++; ?>
					<div class="main-tabs  <?php if($i == 1) {echo 'active'; } ?>">
			            <a class="mtabs-link <?php if($i == 1) {echo 'first-tab'; } ?>" href="javascript:void(0);"><?php echo esc_html($item['list_heading']); ?></a>
			            <div class="mtabs-content active" style="<?php if($i == 1) { echo 'display: block'; } ?>">
			                <div class="who-we-help">
			                    <div class="elementor-tabs-contain">
			                        <div class="elementor-tabs-contain-left">
			                            <h3><?php echo esc_html($item['list_title']); ?></h3>
		                            	<?php echo wp_kses_post($item['list_content']); ?>
			                            <div class="is_dark_primary"><a href="<?php echo esc_url($item['list_link']['url']); ?>" target="<?php echo ($item['list_link']['is_external']) ? "_blank" : '' ?>" rel="<?php echo ($item['list_link']['nofollow']) ? "nofollow" : '' ?>" class="elementor-button-link elementor-button elementor-size-sm"><?php echo esc_html($item['list_button']); ?></a></div>
			                        </div>
			                        <div class="elementor-tabs-contain-right"><?php
			                        	if( isset($item['list_image']['id']) && !empty( $item['list_image']['id'] ) ){ ?><img decoding="async"
			                                src="<?php echo wp_get_attachment_image( $item['list_image']['id'], 'full' ); ?>"><?php
			                            }?>
			                        </div>
			                    </div>
			                </div>
			            </div>
			        </div><?php
				}?>
			</div><?php
		}
	}
}
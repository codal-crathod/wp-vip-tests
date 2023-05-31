<?php
class Elementor_TimeLine_Widget extends \Elementor\Widget_Base {
	public function get_name() {
		return 'timeline_box';
	}
	public function get_icon() {
		return 'eicon-image-box';
	}
	public function get_title() {
		return __( 'Timeline', 'elementor-addon' );
	}
	public function get_categories() {
		return [ 'basic' ];
	}
	public function get_keywords() {
		return [ 'tabs', 'timeline', 'disqo' ];
	}
	public function year_list(){
		$year_list_Array = array();
		$year_list_Array[''] = esc_html__( 'Select Year', 'elementor-addon' );
		for($i = 2015; $i <= 2035; $i++){
			$year_list_Array[$i] = esc_html__( $i, 'elementor-addon' );
		}
		return $year_list_Array;
	}
	protected function _register_controls() {

		if( ! wp_script_is( 'timeline-milestone-js' ) ) {
			wp_register_script( 'timeline-milestone-js', PLUGIN_URL_DISQO ."assets/js/timeline.js", array('jquery'), filemtime( PLUGIN_PATH_DISQO . 'assets/js/timeline.js' ), true );
			wp_enqueue_script( 'timeline-milestone-js' );
		}

		// Content Tab Start

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Timeline', 'elementor-addon' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'list_month', [
				'label' => __( 'Month', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => __( 'Select Month' , 'elementor-addon' ),
				'options' => [
					'' => esc_html__( 'Select Month', 'elementor-addon' ),
					'January' => esc_html__( 'January', 'elementor-addon' ),
					'February'  => esc_html__( 'February', 'elementor-addon' ),
					'March' => esc_html__( 'March', 'elementor-addon' ),
					'April' => esc_html__( 'April', 'elementor-addon' ),
					'May' => esc_html__( 'May', 'elementor-addon' ),
					'June' => esc_html__( 'June', 'elementor-addon' ),
					'July' => esc_html__( 'July', 'elementor-addon' ),
					'August' => esc_html__( 'August', 'elementor-addon' ),
					'September' => esc_html__( 'September', 'elementor-addon' ),
					'October' => esc_html__( 'October', 'elementor-addon' ),
					'November' => esc_html__( 'November', 'elementor-addon' ),
					'December' => esc_html__( 'December', 'elementor-addon' ),
				],
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'list_year', [
				'label' => __( 'Year', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => __( 'Select Year' , 'elementor-addon' ),
				'options' => $this->year_list(),
				'label_block' => true,
			]
		);
		$repeater->add_control(
			'list_headline', [
				'label' => __( 'Headline', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( 'Headline' , 'elementor-addon' ),
				'show_label' => true,
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
		$this->add_control(
			'list',
			[
				'label' => __( 'Repeater List', 'elementor-addon' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'list_month' => __( 'Month', 'elementor-addon' ),
						'list_year' => __( 'Year', 'elementor-addon' ),
						'list_headline' => __( 'Headline', 'elementor-addon' ),
						'list_content' => __( 'Tab content. Click the edit button to change this text.', 'elementor-addon' ),
					],
					[
						'list_month' => __( 'Month', 'elementor-addon' ),
						'list_year' => __( 'Year', 'elementor-addon' ),
						'list_headline' => __( 'Headline', 'elementor-addon' ),
						'list_content' => __( 'Tab content. Click the edit button to change this text.', 'elementor-addon' ),
					],
				],
				'title_field' => '{{ list_month }}',
			]
		);
		$this->end_controls_section();
	}
	
	protected function render() {
		$settings = $this->get_settings_for_display(); 
		if($settings['list']){
			$ItemArrayRearrange = array();
			foreach ( $settings['list'] as $item ) {
				$ItemArrayRearrange[$item['list_year']][] = $item;
			}
		}
		?>
		<div class="timeline-section">
			<div class="timeline-heading">
				<span>Timeline</span>
				<h2>Milestones</h2>
			</div>
			<div class="timeline-block">
				<ul class="timeline-year">
				<?php $TimelineYears = array_keys($ItemArrayRearrange);
					foreach($TimelineYears as $TimelineYear){ ?>
						<li class="tab <?php echo ($TimelineYear == $TimelineYears[0]) ? 'selected' : ''; ?>" id="tab-<?php echo $TimelineYear; ?>" onclick="selectTab('<?php echo $TimelineYear; ?>')">
							<a href="javascript:void(0);"><?php echo esc_html__( $TimelineYear, 'elementor-addon' ); ?></a>
						</li>
					<?php } ?>
				</ul>
				<div class="timeline-list">
					<?php 
					$SingleCardCount = 1;
					$SingleYearCount = 1;
					$TotalYears = count($ItemArrayRearrange);
					foreach($ItemArrayRearrange as $ItemArrayKey => $ItemArrayValue){ ?>
						<div class="year-data" data-year="<?php echo $ItemArrayKey; ?>">
							<?php 
							$InnerLoopCount = 1;
							$TotalCardPerYear = count($ItemArrayValue);
							foreach($ItemArrayValue as $ItemSingleValue){ ?>
								<div class="year-card <?php echo ($SingleCardCount%2 == 0) ? 'bottom-card' : 'top-card'; ?> <?php echo ($InnerLoopCount == 1) ? 'first-card' : ''; ?> <?php echo (($InnerLoopCount == 1) && ($SingleCardCount%2 == 0)) ? 'first-bottom-card' : ''; ?> <?php echo (($InnerLoopCount == 1) && ($SingleCardCount%2 != 0)) ? 'first-top-card' : ''; ?> <?php echo (($InnerLoopCount == $TotalCardPerYear) && ($SingleCardCount%2 != 0)) ? 'last-top-card' : ''; ?> <?php echo (($InnerLoopCount == $TotalCardPerYear) && ($SingleCardCount%2 == 0)) ? 'last-bottom-card' : ''; ?> <?php echo (($TotalYears == $SingleYearCount) && ($InnerLoopCount == $TotalCardPerYear)) ? 'no-more-card' : ''; ?>">
									<div class="inner-card">
										<span><?php echo $ItemSingleValue['list_month'] . ' ' . $ItemSingleValue['list_year']; ?></span>
										<h4><?php echo $ItemSingleValue['list_headline']; ?></h4>
										<p><?php echo $ItemSingleValue['list_content']; ?></p>
									</div>
								</div>
							<?php $InnerLoopCount++; $SingleCardCount++; } ?>
						</div>
					<?php $SingleYearCount++; } ?>
				</div>
      		</div>
    	</div>
	<?php }
}
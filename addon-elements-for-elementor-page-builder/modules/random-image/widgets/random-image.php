<?php
namespace WTS_EAE\Modules\RandomImage\Widgets;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Text_Shadow;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class RandomImage extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-random-image';
	}

	public function get_title() {
		return __( 'Random Image', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-coupon-code';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'random image' ,'image','random'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie'];
	}

    protected function register_controls(){

        $this->start_controls_section(
			'eae_random_code',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);

        $repeater = new Repeater();

        $repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose Image', 'wts-eae' ),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'large',
				'condition' => [
					'image[url]!' => '',
				],
			]
		);

		$repeater->add_control(
			'custom_caption',
			[
				'label'       => __( 'Custom Caption', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'label_block'	 => true,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter your caption', 'wts-eae' ),
			]
		);

		$repeater->add_control(
			'custom_link',
			[
				'label' => esc_html__( 'Custom Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
			]
		);
	
		$this->add_control(
			'images_repeater',
			[
				'label'      => __( '', 'wts-eae' ),
				'type'       => Controls_Manager::REPEATER,
				'show_label' => true,
				'default'    => [
					[
						'legend_feature_text' => __( 'Bandwidth', 'wts-eae' ),
					],
					[
						'legend_feature_text' => __( 'Space', 'wts-eae' ),
					],
					[
						'legend_feature_text' => __( 'Domain', 'wts-eae' ),
					],
				],
				'fields'     => $repeater->get_controls(),
			]
		);

		$this->add_control(
			'default_caption',
			[
				'label' => esc_html__( 'Default Caption', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
                    'caption' => __('Caption', 'wts-eae'),
					'description' => __('Description', 'wts-eae'),
				],
				'default' => 'caption',
			]
		);

		$this->add_control(
			'position',
			[
				'label' => esc_html__( 'Caption Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'column' => __('Top', 'wts-eae'),
					'column-reverse' => __('Bottom', 'wts-eae'),
				],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-content' => 'flex-direction: {{VALUE}}',						
				],
				'default' => 'column',
			]
		);


		$this->add_control(
			'enable_lightbox',
			[
				'label' => esc_html__( 'Enable Lightbox', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);


		$this->add_responsive_control(
			'image_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-wrapper' => 'align-items: {{VALUE}}',															
				],
			]
		);

        $this->end_controls_section();
		
        $this->start_controls_section(
            'eae_random_image_style',
            [
                'label' => esc_html__( 'Image', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
            'image_width',
            [
                'label' => esc_html__( 'Image Width', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' =>  ['%','px'],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
						'step'=>1,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
						'step'=>1,
                    ],
                ],
				'default' => [
                    'unit' => 'px',
                    'size' => 500,
                ],
                'selectors' => [
                  '{{WRAPPER}} .eae-random-image-wrapper img'  => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


		$this->add_control(
			'image_dimension',
			[
				'label' => esc_html__( 'Dimensions', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'auto' => __('Height Auto', 'wts-eae'),
                    'custom' => __('Custom', 'wts-eae'),
					'ratio' => __('Ratio', 'wts-eae'),
				],
				'default' => 'auto',
			]
		);

		$this->add_responsive_control(
			'max-width',
			[
				'label' => esc_html__( 'Max Width', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
					],
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vw' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'image_dimension' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-wrapper img' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'height',
			[
				'label' => esc_html__( 'Image Height', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vh', 'custom' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'image_dimension' => 'custom',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-wrapper img' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'aspect_ratio',
			[
				'label' => esc_html__( 'Aspect Ratio', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'16/9' => '16:9',
					'21/9' => '21:9',
					'4/3' => '4:3',
					'3/2' => '3:2',
					'1/1' => '1:1',
					'9/16' => '9:16',
				],
				'default' => '1/1',
				'condition' => [
					'image_dimension' => 'ratio',
				],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-wrapper img' => 'aspect-ratio:{{VALUE}};',
				]
			]
		);

		$this->add_responsive_control(
			'object-fit',
			[
				'label' => esc_html__( 'Object Fit', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'condition' => [
					'image_dimension!' => 'auto',
				],
				'options' => [
					'' => esc_html__( 'Default', 'wts-eae' ),
					'fill' => esc_html__( 'Fill', 'wts-eae' ),
					'cover' => esc_html__( 'Cover', 'wts-eae' ),
					'contain' => esc_html__( 'Contain', 'wts-eae' ),
					'scale-down' => esc_html__( 'Scale Down', 'wts-eae' ),
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-wrapper img' => 'object-fit: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'object-position',
			[
				'label' => esc_html__( 'Object Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'center center' => esc_html__( 'Center Center', 'wts-eae' ),
					'center left' => esc_html__( 'Center Left', 'wts-eae' ),
					'center right' => esc_html__( 'Center Right', 'wts-eae' ),
					'top center' => esc_html__( 'Top Center', 'wts-eae' ),
					'top left' => esc_html__( 'Top Left', 'wts-eae' ),
					'top right' => esc_html__( 'Top Right', 'wts-eae' ),
					'bottom center' => esc_html__( 'Bottom Center', 'wts-eae' ),
					'bottom left' => esc_html__( 'Bottom Left', 'wts-eae' ),
					'bottom right' => esc_html__( 'Bottom Right', 'wts-eae' ),
				],
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-wrapper img' => 'object-position: {{VALUE}};',
				],
				'condition' => [
					'image_dimension!' => 'auto',
					'object-fit' => [ 'cover', 'contain', 'scale-down' ],
				],
			]
		);

		$this->add_control(
			'separator_panel_style',
			[
				'type' => Controls_Manager::DIVIDER,
				'style' => 'thick',
			]
		);

		$this->start_controls_tabs( 'image_effects' );

		$this->start_controls_tab( 'normal',
			[
				'label' => esc_html__( 'Normal', 'wts-eae' ),
			]
		);

		$this->add_control(
			'opacity',
			[
				'label' => esc_html__( 'Opacity', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-wrapper img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .eae-random-image-wrapper img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'hover',
			[
				'label' => esc_html__( 'Hover', 'wts-eae' ),
			]
		);

		$this->add_control(
			'opacity_hover',
			[
				'label' => esc_html__( 'Opacity', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-wrapper:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .eae-random-image-wrapper:hover img',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => esc_html__( 'Transition Duration', 'wts-eae' ) . ' (s)',
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-wrapper img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->add_control(
			'hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'wts-eae' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'image_border',
				'selector' => '{{WRAPPER}} .eae-random-image-wrapper img',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .eae-random-image-wrapper img',
			]
		);

		$this->end_controls_section();
		
        $this->start_controls_section(
			'eae_random_image_caption_style',
			[
				'label' => esc_html__( 'Caption', 'wts-eae' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		
		$this->add_responsive_control(
			'caption_alignment',
			[
				'label' => esc_html__( 'Alignment', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-caption' => 'justify-content: {{VALUE}}',															
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-caption' => 'color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_control(
			'caption_background_color',
			[
				'label' => esc_html__( 'Background Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-caption' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'caption_typography',
				'selector' => '{{WRAPPER}} .eae-random-image-caption',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'caption_text_shadow',
				'selector' => '{{WRAPPER}} .eae-random-image-caption',
			]
		);

		$this->add_responsive_control(
			'caption_space',
			[
				'label' => esc_html__( 'Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
					'rem' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-random-image-content' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
    }


    public function get_link( $item ) {
		if ($item['custom_link']['url'] == '' ) {
			return [
				'url' => $item['image']['url'],
			];
		}else{
			return [
				'url' => $item['custom_link']['url'],
			];
		}
	}
    public function render(){
		$settings = $this->get_settings_for_display(); 

		if (!empty($settings['images_repeater'])) {
			$random_key = array_rand($settings['images_repeater']);
			$random_item = $settings['images_repeater'][$random_key];

			if ($random_item['custom_caption'] == "") {
				$image_post = get_post($random_item['image']['id']);
				if ($image_post) {
					if ($settings['default_caption'] == 'caption') {
						$caption = $image_post->post_excerpt; // Caption
					} else {
						$caption = $image_post->post_content; // Description
					}
				}
			} else {
				$caption = $random_item['custom_caption'];
			}

			$link = '';
			if ($random_item['custom_link']['url'] != '' || $settings['enable_lightbox'] != "") {
				$link = $this->get_link($random_item);
				if ($link) {
					$this->add_link_attributes('link_random', $link);

					$this->add_render_attribute('link_random', [
						'class' => 'wts-eae-clickable',
					]);

					if ($random_item['custom_link'] != '' && $settings['enable_lightbox'] == "yes") {
						$this->add_lightbox_data_attributes("link_random", $random_item['image']['id']);
					}
				}
			}
			?>
			<div class="eae-random-image-wrapper">
				<div class="eae-random-image-content">
					<?php if(!empty($caption)) {?>
					<div class="eae-random-image-caption"><?php echo esc_html($caption); ?></div>
					<?php } if ($link) { ?>
						<a <?php $this->print_render_attribute_string("link_random"); ?>>
					<?php } ?>
					<img class="eae-random-image elementor-animation-<?php echo esc_attr($settings['hover_animation']); ?>" 
						src="<?php echo esc_url($random_item["image"]["url"]); ?>" 
						alt="">
					<?php if ($link) { ?>
						</a>
					<?php } ?>
				</div>
			</div>
			<?php
		}
    }
        
}

        
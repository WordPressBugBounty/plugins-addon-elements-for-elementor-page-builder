<?php 

namespace WTS_EAE\Modules\Dropbar\Widgets;

use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Controls_Manager;
use WTS_EAE\Classes\Helper;
use Elementor\Plugin as EPlugin;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Dropbar extends EAE_Widget_Base {

    public function get_name() {
		return 'eae-dropbar';
	}

	public function get_title() {
		return __( 'Dropbar', 'wts-eae' );
	}

	public function get_icon() {
		return 'wpv wpv-drop-bar';
	}
    
    protected function register_controls(){

        $this->start_controls_section(
            'eae_dropbar_content',
            [
                'label'=>esc_html__('Content','wts-eae'),
            ]            
        );

     
        $this->add_control(
			'content_type',
			[
				'label'   => __( 'Type', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'plain_content' => __( 'WYSIWYG', 'wts-eae' ),
					'saved_section' => __( 'Saved Section', 'wts-eae' ),
					'saved_page'    => __( 'Saved Page', 'wts-eae' ),
					'ae_template'   => __( 'DA-Template', 'wts-eae' ),
					'saved_container' => __('Saved Container','wts-eae'),
				],
				'default' => 'plain_content',
			]
		);

        $this->add_control(
			'plain_content',
			[
				'label'     => __( 'Type', 'wts-eae' ),
				'type'      => Controls_Manager::WYSIWYG,
				'condition' => [
					'content_type' => 'plain_content',
				],
				'dynamic'   => [
					'active' => true,
				],
				'default'   => __( 'Add some nice text here.', 'wts-eae' ),
			]
		);

		$saved_container[''] = __('Select Container','wts-eae');
		$saved_container     = $saved_container + Helper::select_elementor_page( 'container' );
		$this->add_control(
			'saved_container',
			[
				'label' => esc_html__('Container','wts-eae'),
				'type' => Controls_Manager::SELECT,
				'options' => $saved_container,
				'condition' => [
					'content_type' => 'saved_container'
				]
			]
		);

		$saved_sections[''] = __( 'Select Section', 'wts-eae' );
		$saved_sections     = $saved_sections + Helper::select_elementor_page( 'section' );
		$this->add_control(
			'saved_section',
			[
				'label'     => __( 'Sections', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_sections,
				'condition' => [
					'content_type' => 'saved_section',
				],
			]
		);
		$saved_page[''] = __( 'Select Pages', 'wts-eae' );
		$saved_page     = $saved_page + Helper::select_elementor_page( 'page' );
		$this->add_control(
			'saved_pages',
			[
				'label'     => __( 'Pages', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_page,
				'condition' => [
					'content_type' => 'saved_page',
				],
			]
		);

		$saved_ae_template[''] = __( 'Select DA Template', 'wts-eae' );
		$saved_ae_template     = $saved_ae_template + Helper::select_ae_templates();
		$this->add_control(
			'ae_templates',
			[
				'label'     => __( 'DA Templates', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_ae_template,
				'condition' => [
					'content_type' => 'ae_template',
				],
			]
		);

        $this->end_controls_section();
        
        $this->start_controls_section(
            'eae_dropbar_button',
            [
                'label'=>esc_html__('Button','wts-eae'),
                ]            
            );

        $this->add_control(
            'button_content', 
            [
                'label'			 =>esc_html__( 'Text', 'wts-eae' ),
                'type'			 => Controls_Manager::TEXT,
                'dynamic'		 => [
                    'active' => true,
                ],
                // 'label_block'	 => true,
                'default'		 =>esc_html__( 'Open Dropbar', 'wts-eae' ),
            ]
        );

        Helper::eae_media_controls(
            $this,
            [
                'name'          => 'icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> true,
                'lottie'		=> true,
            ]
        );

        $this->add_control(
			'button_icon_position',
			[
				'label' => esc_html__( 'Position', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'row' =>  esc_html__( 'Before', 'wts-eae' ),
					'row-reverse' =>  esc_html__( 'After', 'wts-eae' ),
					'column' =>  esc_html__( 'Top', 'wts-eae' ),
					'column-reverse' =>  esc_html__( 'Bottom', 'wts-eae' ),
				],
				'default' => 'row',
                'selectors' => [
					'{{WRAPPER}} .eae-dropbar-text' => 'flex-direction:{{VALUE}}',						
				],
			]
		);

        $this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Align', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Stretch', 'wts-eae' ),
						'icon' => 'eicon-h-align-stretch',
					],
				],
				'prefix_class' => 'eae-drop-bar-align-',
			]
		);
    

        $this->end_controls_section();
        $this->start_controls_section(
            'eae_dropbar_settings',
            [
                'label'=>esc_html__('Settings','wts-eae'),
                ]            
            );

        $this->add_control(
            'content_mode',
            [
                'label' => esc_html__( 'Mode', 'wts-eae' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'click' =>  esc_html__( 'Click', 'wts-eae' ),
                    'hover' =>  esc_html__( 'Hover', 'wts-eae' ),
                ],
                'default' => 'hover',
                'frontend_available' => true
            ]
        );

        $this->add_control(
            'content_position',
            [
                'label' => esc_html__( 'Position', 'wts-eae' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'bottom-left'    => esc_html__( 'Bottom Left', 'wts-eae' ),
                    'bottom-center'  => esc_html__( 'Bottom Center', 'wts-eae' ),
                    'bottom-right'   => esc_html__( 'Bottom Right', 'wts-eae' ),
                    'top-left'       => esc_html__( 'Top Left', 'wts-eae' ),
                    'top-center'     => esc_html__( 'Top Center', 'wts-eae' ),
                    'top-right'      => esc_html__( 'Top Right', 'wts-eae' ),
                    'left-top'       => esc_html__( 'Left Top', 'wts-eae' ),
                    'left-center'    => esc_html__( 'Left Center', 'wts-eae' ),
                    'left-bottom'    => esc_html__( 'Left Bottom', 'wts-eae' ),
                    'right-top'      => esc_html__( 'Right Top', 'wts-eae' ),
                    'right-center'   => esc_html__( 'Right Center', 'wts-eae' ),
                    'right-bottom'   => esc_html__( 'Right Bottom', 'wts-eae' ),
                ],
                'default' => 'bottom-left',
                'frontend_available' => true
            ]
        );

        
        $this->add_responsive_control(
            'drop_width',
			[
                'label' => esc_html__('Width', 'wts-eae'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
                    'px' => [
                        'min' => 100,
						'max' => 1500,
					],
				],
                'default' => [
					'size' => 300,
					'unit' => 'px',
				],
				'selectors' => [
                    '{{WRAPPER}} .eae-drop-content' => 'width: {{SIZE}}{{UNIT}};',
				],
                ]
        );

        $this->add_control(
            'off_set',
			[
                'label' => esc_html__('Offset', 'wts-eae'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
                    'px' => [
                        'min' => 1,
						'max' => 200,
                        'step'=>1,
					],
				],
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'frontend_available' => true
            ]
        );

        $this->add_control(
            'content_animation',
            [
                'label' => esc_html__( 'Animation', 'wts-eae' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                        ''             => esc_html__('None', 'wts-eae'),
                    'animation-fade' => esc_html__('Fade', 'wts-eae'),
                    'slide-top'    => esc_html__('Slide Top', 'wts-eae'),
                    'slide-bottom' => esc_html__('Slide Bottom', 'wts-eae'),
                    'slide-left'   => esc_html__('Slide Left', 'wts-eae'),
                    'slide-right'  => esc_html__('Slide Right', 'wts-eae'),
                ],
                'default' => 'animation-fade',
                'frontend_available' => true
            ]
        );

        $this->add_control( 
			'caption_animation_out',
			[
				'label'        => __( 'Animation Out', 'wts-eae' ),
                'type'         => Controls_Manager::SWITCHER,
                'label_on'     => __( 'Yes', 'wts-eae' ),
                'label_off'    => __( 'No', 'wts-eae' ),
				'default'      =>'yes',
                'frontend_available' => true
			]
		);

        $this->add_control(
			'content_duration',
			[
				'label'     => __( 'Animation Duration', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
                    'px' => [
                        'min' => 100,
						'max' => 5000,
                        'step'=>100,
					],
				],
                'frontend_available' => true,
                'selectors' => [
                    '{{WRAPPER}} .eae-dropbar-wrapper.eae-animation .eae-drop-content' => 'transition: visibility {{SIZE}}ms ease , clip-path {{SIZE}}ms ease , opacity {{SIZE}}ms ease;',
                ],
			]
		);

        $this->add_control(
            'hide_delay',
			[
                'label' => esc_html__('Hide Delay', 'wts-eae'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
                    'px' => [
                        'min' => 100,
						'max' => 10000,
                        'step'=>100,
					],
				],
                'frontend_available' => true
            ]
        );

        $this->add_control(
            'show_delay',
			[
                'label' => esc_html__('Show Delay', 'wts-eae'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
                    'px' => [
                        'min' => 100,
						'max' => 10000,
                        'step'=>100,
					],
				],
                'frontend_available' => true
            ]
        );


        $this->end_controls_section();

       
        $this->start_controls_section(
            'eae_dropbar_button_style',
            [
                'label' => esc_html__( 'Button', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
            $this->add_responsive_control(
                'button_width',
                [
                    'label' => esc_html__( 'Width', 'wts-eae' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' =>  ['px','%'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1000,
                            'step'=>1,
                        ],
                    ],
                'selectors' => [
                        '{{WRAPPER}} .eae-dropbar-wrapper' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );


            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'typography',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                    ],
                    'selector' => '{{WRAPPER}} .eae-dropbar-text',
                ]
            );

            $this->add_group_control(
                Group_Control_Text_Shadow::get_type(),
                [
                    'name' => 'text_shadow',
                    'selector' => '{{WRAPPER}} .eae-dropbar-text',
                ]
            );

            $this->start_controls_tabs( 'tabs_button_style');

            $this->start_controls_tab(
                'tab_button_normal',
                [
                    'label' => esc_html__( 'Normal', 'wts-eae' ),
                ]
            );

            $this->add_control(
                'button_text_color',
                [
                    'label' => esc_html__( 'Text Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}} .eae-dropbar-text' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'background',
                    'types' => [ 'classic', 'gradient' ],
                    'exclude' => [ 'image' ],
                    'selector' => '{{WRAPPER}} .eae-dropbar-text',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                        'color' => [
                            'global' => [
                                'default' => Global_Colors::COLOR_ACCENT,
                            ],
                        ],
                    ],
                ]
            );

            $this->end_controls_tab();

            $this->start_controls_tab(
                'tab_button_hover',
                [
                    'label' => esc_html__( 'Hover', 'wts-eae' ),
                ]
            );

            $this->add_control(
                'hover_color',
                [
                    'label' => esc_html__( 'Text Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-dropbar-text:hover, {{WRAPPER}} .eae-dropbar-text:focus' => 'color: {{VALUE}};',
                        '{{WRAPPER}} .eae-dropbar-text:hover svg, {{WRAPPER}} .eae-dropbar-text:focus svg' => 'fill: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'button_background_hover',
                    'types' => [ 'classic', 'gradient' ],
                    'exclude' => [ 'image' ],
                    'selector' => '{{WRAPPER}} .eae-dropbar-text:hover, {{WRAPPER}} .eae-dropbar-text:focus',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                    ],
                ]
            );

            $this->add_control(
                'button_hover_border_color',
                [
                    'label' => esc_html__( 'Border Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'condition' => [
                        'border_border!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-dropbar-text:hover, {{WRAPPER}} .eae-dropbar-text:focus' => 'border-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_hover_transition_duration',
                [
                    'label' => esc_html__( 'Transition Duration', 'wts-eae' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' => [ 's', 'ms', 'custom' ],
                    'default' => [
                        'unit' => 's',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-dropbar-text' => 'transition-duration: {{SIZE}}{{UNIT}};',
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
                    'name' => 'border',
                    'selector' => '{{WRAPPER}} .eae-dropbar-text',
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-dropbar-text' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'button_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-dropbar-text',
                ]
            );

            $this->add_responsive_control(
                'text_padding',
                [
                    'label' => esc_html__( 'Padding', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-dropbar-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_responsive_control(
                'icon_space',
                [
                    'label' => esc_html__('Icon Space', 'wts-eae'),
                    'type'  => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 100,
                        ],
                    ],
                    'default' => [
                        'size' => 5,
                        'unit' => 'px',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-dropbar-text' => 'gap: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
					    'icon_graphic_type!' => 'none',
				    ],
                ]
            );

            $this->add_control(
                'icon_heading',
                [
                    'label'     => __( 'Icon', 'wts-eae' ),
                    'type'      => Controls_Manager::HEADING,
                    'separator' => 'before',
                    'condition' => [
					    'icon_graphic_type!' => 'none',
				    ],
                ]
            );
    
            Helper::global_icon_style_controls(
                $this,
                [
                    'name' => 'icon',
                    'selector' => '.eae-dropbar-icon-',
                    'is_repeater' => false,
                    'hover_selector' => '.eae-dropbar-icon:hover',
                    'conditions'     => [
					[
						'key'   => 'icon_graphic_type!',
						'value' => 'none',
					],
				]
                ]
            );

        $this->end_controls_section();


        $this->start_controls_section(
            'eae_dropbar_content_style',
            [
                'label' => esc_html__( 'Content', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

            $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'content_typography',
                    'selector' => '{{WRAPPER}} .eae-drop-content',
                    'global' => [
                        'default' => Global_Typography::TYPOGRAPHY_TEXT,
                    ],
                    'condition' => [
					    'content_type' => 'plain_content',
				    ],
                ]
            );


            $this->add_control(
                'content_color',
                [
                    'label' => esc_html__( 'Text Color', 'wts-eae' ),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .eae-drop-content' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Background::get_type(),
                [
                    'name' => 'content_background_hover',
                    'types' => [ 'classic', 'gradient' ],
                    'exclude' => [ 'image' ],
                    'selector' => '{{WRAPPER}} .eae-drop-content',
                    'fields_options' => [
                        'background' => [
                            'default' => 'classic',
                        ],
                    ],
                ]
            );

            $this->add_responsive_control(
                'text_alignment',
                [
                    'label' => esc_html__( 'Text Align', 'wts-eae' ),
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
                        '{{WRAPPER}} .eae-drop-content' => 'text-align: {{VALUE}}',															
                    ],
                    'condition' => [
					    'content_type' => 'plain_content',
				    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'content_border',
                    'selector' => '{{WRAPPER}} .eae-drop-content',
                    'separator' => 'before',
                ]
            );

            $this->add_responsive_control(
                'content_border_radius',
                [
                    'label' => esc_html__( 'Border Radius', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-drop-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

            $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'content_button_box_shadow',
                    'selector' => '{{WRAPPER}} .eae-drop-content',
                ]
            );

            $this->add_responsive_control(
                'content_text_padding',
                [
                    'label' => esc_html__( 'Padding', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-drop-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
            );

        $this->end_controls_section();

    }
        
    protected function render(){    
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('wrapper', 'class', 'eae-drop-content eae-position-'.$settings['content_position'].' eae-animation-'.$settings['content_animation']);
        $this->add_render_attribute('container', 'class', 'eae-dropbar-wrapper elementor-animation-' . $settings['hover_animation']);

       ?>
       <div  <?php $this->print_render_attribute_string('container');  ?>> 

        <span class="eae-dropbar-text">
            <?php
             Helper::render_icon_html($settings, $this,'icon','eae-dropbar-icon-', 'test');
             echo esc_html($settings['button_content']) ?>
        </span>
        <div  <?php $this->print_render_attribute_string('wrapper');  ?>> 
            <?php
            
            switch ($settings['content_type']) {
                case 'plain_content':
                    echo do_shortcode($settings['plain_content']);
                    break;
            
                case 'saved_section':
                    echo $this->get_builder_content('saved_section', $settings);
                    break;
            
                case 'saved_page':
                    echo $this->get_builder_content('saved_pages', $settings);
                    break;
            
                case 'ae_template':
                    echo $this->get_builder_content('ae_templates', $settings);
                    break;
            
                case 'saved_container':
                    echo $this->get_builder_content('saved_container', $settings);
                    break;
            
                default:
                    error_log("Invalid content type: {$settings['content_type']}");
                    break;
            }
            ?>
        </div>
       </div>
       <?php


    }

	public function get_builder_content($content_type, $settings) {
		if (empty($settings[$content_type])) {
			error_log("Missing setting: $content_type");
			return '';
		}
		$template_id = $settings[$content_type];
		if( Helper::check_template($template_id) !== ''){
			return EPlugin::instance()->frontend->get_builder_content_for_display($template_id);
		}
		return '';
	}

}
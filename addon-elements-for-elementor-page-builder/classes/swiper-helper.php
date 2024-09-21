<?php
namespace WTS_EAE\Classes;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin as EPlugin;

class Swiper_helper {

	public static function carousel_controls($widget, $args = null) {

		$widget->add_control(
			'loop',
			[
				'label'        => __( 'Loop', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
				'frontend_available' => true
			]
		);

		// Todo:: different effects management
		$widget->add_control(
			'effect',
			[
				'label'   => __( 'Effects', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'fade'      => __( 'Fade', 'wts-eae' ),
					'slide'     => __( 'Slide', 'wts-eae' ),
					'coverflow' => __( 'Coverflow', 'wts-eae' ),
					'flip'      => __( 'Flip', 'wts-eae' ),
				],
				'default' => 'slide',
				'frontend_available' => true
			]
		);
		
		$widget->add_control(
			'grid',
			[
				'label'        => __( 'Multi Row', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'On', 'wts-eae' ),
				'label_off'    => __( 'Off', 'wts-eae' ),
				'return_value' => 'yes',
				'condition'          => [
					'loop!' => 'yes',
				],
				'frontend_available' => true
			]
		);

		$widget->add_responsive_control(
			'grid_rows',
			[
				'label'              => __( 'Rows', 'wts-eae' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'max'                => 12,
				'default'            => 1,
				'tablet_default'     => 1,
				'mobile_default'     => 1,
				'condition'          => [
					'loop!' => 'yes',
					'multirow' => 'yes'
				],
				'frontend_available' => true,
			]
		);

		$desktop_default = $args['slides_per_view']['desktop'] ?? 3;
		$tablet_default = $args['slides_per_view']['tablet'] ?? 3;
		
		
		$widget->add_responsive_control(
			'slide_per_view',
			[
				'label'              => __( 'Slides Per View', 'wts-eae' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'max'                => 100,
				'default'            => $desktop_default,
				'tablet_default'     => $tablet_default,
				'mobile_default'     => 1,
				'condition'          => [
					'effect' => [ 'slide', 'coverflow' ],
				],
				'frontend_available' => true,
			]
		);

		$widget->add_responsive_control(
			'slides_per_group',
			[
				'label'              => __( 'Slides Per Group', 'wts-eae' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'max'                => 100,
				'default'            => 1,
				'tablet_default'     => 1,
				'mobile_default'     => 1,
				'condition'          => [
					'effect' => [ 'slide', 'coverflow' ],
				],
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'carousel_settings_heading',
			[
				'label'     => __( 'Setting', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$widget->add_control(
			'speed',
			[
				'label'       => __( 'Speed', 'wts-eae' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => 5000,
				],
				'description' => __( 'Duration of transition between slides (in ms)', 'wts-eae' ),
				'range'       => [
					'px' => [
						'min'  => 300,
						'max'  => 10000,
						'step' => 300,
					],
				],
				'frontend_available' => true

			]
		);

		$widget->add_control(
			'autoplay',
			[
				'label'        => __( 'Autoplay', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'On', 'wts-eae' ),
				'label_off'    => __( 'Off', 'wts-eae' ),
				'return_value' => 'yes',
				'frontend_available' => true
			]
		);

		$widget->add_control(
			'duration',
			[
				'label'       => __( 'Duration', 'wts-eae' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => 900,
				],
				'description' => __( 'Delay between transitions (in ms)', 'wts-eae' ),
				'range'       => [
					'px' => [
						'min'  => 300,
						'max'  => 10000,
						'step' => 300,
					],
				],
				'condition'   => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true
			]
		);

		$widget->add_responsive_control(
			'space',
			[
				'label'              => __( 'Space Between Slides', 'wts-eae' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'size' => 15,
				],
				'tablet_default'     => [
					'size' => 10,
				],
				'mobile_default'     => [
					'size' => 5,
				],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 5,
					],
				],
				'condition'          => [
					'effect' => [ 'slide', 'coverflow' ],
				],
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'auto_height',
			[
				'label'        => __( 'Auto Height', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
				'frontend_available' => true
			]
		);

		$widget->add_control(
			'pause_on_hover',
			[
				'label'        => __( 'Pause on Hover', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
				'condition'   => [
					'autoplay' => 'yes',
				],
				'frontend_available' => true
			]
		);

		$widget->add_control(
			'pagination_heading',
			[
				'label'     => __( 'Pagination', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$widget->add_control(
			'ptype',
			[
				'label'   => __( ' Pagination Type', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' =>
					[
						''            => __( 'None', 'wts-eae' ),
						'bullets'     => __( 'Bullets', 'wts-eae' ),
						'fraction'    => __( 'Fraction', 'wts-eae' ),
						'progressbar' => __( 'Progress Bar', 'wts-eae' ),
					],
				'default' => 'bullets',
				'frontend_available' => true
			]
		);

		$widget->add_control(
			'clickable',
			[
				'label'     => __( 'Clickable', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Yes', 'wts-eae' ),
				'label_off' => __( 'No', 'wts-eae' ),
				'condition' => [
					'ptype' => 'bullets',
				],
			]
		);

		$widget->add_control(
			'keyboard',
			[
				'label'        => __( 'Keyboard Control', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
			]
		);

		$widget->add_control(
			'scrollbar',
			[
				'label'        => __( 'Scroll bar', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
				'frontend_available' => true
			]
		);

		$widget->add_control(
			'navigation_arrow_heading',
			[
				'label'     => __( 'Prev/Next Navigation', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',

			]
		);

		$widget->add_control(
			'navigation_button',
			[
				'label'        => __( 'Enable', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
			]
		);

		$widget->add_control(
			'arrows_layout',
			[
				'label'     => __( 'Position', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inside',
				'options'   => [
					'inside'  => __( 'Inside', 'wts-eae' ),
					'outside' => __( 'Outside', 'wts-eae' ),
				],
				'condition' => [
					'navigation_button' => 'yes',
				],

			]
		);

		$widget->add_control(
			'arrow_icon_left',
			[
				'label'            => __( 'Icon Prev', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fa fa-angle-left',
					'library' => 'fa-solid',
				],
				'condition'        => [
					'navigation_button' => 'yes',
				],
			]
		);

		$widget->add_control(
			'arrow_icon_right',
			[
				'label'            => __( 'Icon Next', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fa fa-angle-right',
					'library' => 'fa-solid',
				],
				'condition'        => [
					'navigation_button' => 'yes',
				],
			]
		);

		$widget->add_responsive_control(
			'arrow_horizontal_position',
			[
				'label'       => __( 'Horizontal Position', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left' => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'toggle' => false,
				'default'     => 'center',
				'condition'   => [
					'navigation_button' => 'yes',
					'arrows_layout' => 'inside',
				],
			]
		);

		$widget->add_responsive_control(
			'arrow_vertical_position',
			[
				'label'       => __( 'Vertical Position', 'wts-eae' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'top' => [
						'title' => __( 'Top', 'wts-eae' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'wts-eae' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'wts-eae' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'toggle' => false,
				'default'     => 'middle',
				'condition'   => [
					'navigation_button' => 'yes',
					'arrows_layout' => 'inside',

				],
			]
		);
	}

	public static function  inject_carousel_controls($widget){
		$widget->start_injection( [
			'of' => 'pause_on_hover',
		] );

		$widget->add_control(
			'pause_on_interaction',
			[
				'label'     => __( 'Pause on Interaction', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Yes', 'wts-eae' ),
				'label_off' => __( 'No', 'wts-eae' ),
				'condition' => [
					'autoplay' => 'yes',
				],

			]
		);

		$widget->end_injection();
	}

	public static function carousel_style_section($widget, $args = null) {

		$widget->add_control(
			'heading_style_arrow',
			[
				'label'     => __( 'Prev/Next Navigation', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);
		$widget->start_controls_tabs( 'tabs_arrow_styles' );

		$widget->start_controls_tab(
			'tab_arrow_normal',
			[
				'label' => __( 'Normal', 'wts-eae' ),
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);

		$widget->add_control(
			'arrow_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-swiper-button-prev i' => 'color:{{VAlUE}};',
					'{{WRAPPER}} .eae-swiper-button-next i' => 'color:{{VAlUE}};',
					'{{WRAPPER}} .eae-swiper-button-prev svg' => 'fill:{{VAlUE}};',
					'{{WRAPPER}} .eae-swiper-button-next svg' => 'fill:{{VAlUE}};',
				],
				'default'   => '#444',
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);

		$widget->add_control(
			'arrow_bg_color',
			[
				'label'     => __( ' Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-swiper-button-prev' => 'background-color:{{VAlUE}};',
					'{{WRAPPER}} .eae-swiper-button-next' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);

		$widget->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'arrow_border',
				'label'     => __( 'Border', 'wts-eae' ),
				'selector'  => '{{WRAPPER}} .eae-swiper-container .eae-swiper-button-prev, {{WRAPPER}} .eae-swiper-container .eae-swiper-button-next, {{WRAPPER}} .eae-swiper-button-prev, {{WRAPPER}} .eae-swiper-button-next',
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);

		$widget->add_control(
			'arrow_border_radius',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-swiper-container .eae-swiper-button-prev, {{WRAPPER}} .eae-swiper-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
					'{{WRAPPER}} .eae-swiper-container .eae-swiper-button-next, {{WRAPPER}} .eae-swiper-button-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],
				'condition'  =>
					[
						'navigation_button' => 'yes',
					],
			]
		);
		$widget->end_controls_tab();

		$widget->start_controls_tab(
			'tab_arrow_hover',
			[
				'label' => __( 'Hover', 'wts-eae' ),
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);
		$widget->add_control(
			'arrow_color_hover',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-swiper-button-prev:hover i' => 'color:{{VAlUE}};',
					'{{WRAPPER}} .eae-swiper-button-next:hover i' => 'color:{{VAlUE}};',
					'{{WRAPPER}} .eae-swiper-button-prev:hover svg' => 'fill:{{VAlUE}};',
					'{{WRAPPER}} .eae-swiper-button-next:hover svg' => 'fill:{{VAlUE}};',
				],
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);

		$widget->add_control(
			'arrow_bg_color_hover',
			[
				'label'     => __( ' Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-swiper-button-prev:hover' => 'background-color:{{VAlUE}};',
					'{{WRAPPER}} .eae-swiper-button-next:hover' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);

		$widget->add_control(
			'arrow_border_color_hover',
			[
				'label'     => __( ' Border Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-swiper-button-prev:hover' => 'border-color:{{VAlUE}};',
					'{{WRAPPER}} .eae-swiper-button-next:hover' => 'border-color:{{VAlUE}};',
				],
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);

		$widget->add_control(
			'arrow_border_radius_hover',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-swiper-container .eae-swiper-button-prev:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
					'{{WRAPPER}} .eae-swiper-container .eae-swiper-button-next:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],
				'condition'  =>
					[
						'navigation_button' => 'yes',
					],
			]
		);

		$widget->end_controls_tab();

		$widget->end_controls_tabs();

		if(isset($args['navigation_icon_size'])){
			$icon_size = $args['navigation_icon_size'];
		}else{
			$icon_size = 50;
		}
		
		$widget->add_responsive_control(
			'arrow_size',
			[
				'label'     => __( 'Arrow Size', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => $icon_size,
					],
				'range'     =>
					[
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					],
				'selectors' => [
					'{{WRAPPER}} .eae-swiper-button-prev i' => 'font-size:{{SIZE}}px;',
					'{{WRAPPER}} .eae-swiper-button-next i' => 'font-size:{{SIZE}}px;',
					'{{WRAPPER}} .eae-swiper-button-prev svg' => 'width : {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eae-swiper-button-next svg' => 'width : {{SIZE}}{{UNIT}};',
				],
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);

		$widget->add_responsive_control(
			'horizontal_arrow_offset',
			[
				'label'          => __( 'Horizontal Offset', 'wts-eae' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ '%', 'px' ],
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range'          =>
					[
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				'selectors'      => [
					'{{WRAPPER}} .eae-hpos-left .eae-swiper-button-wrapper' => 'left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eae-hpos-right .eae-swiper-button-wrapper' => 'right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eae-hpos-center .eae-swiper-button-prev' => 'left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eae-hpos-center .eae-swiper-button-next' => 'right: {{SIZE}}{{UNIT}}',

				],
				'condition'      => [
					'navigation_button' => 'yes',
					'arrows_layout' => 'inside',
				],
			]
		);
		$widget->add_responsive_control(
			'vertical_arrow_offset',
			[
				'label'          => __( 'Vertical Offset', 'wts-eae' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ '%', 'px' ],
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range'          =>
					[
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				'selectors'      => [
					'{{WRAPPER}} .eae-vpos-top .eae-swiper-button-wrapper' => 'top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eae-vpos-bottom .eae-swiper-button-wrapper' => 'bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eae-vpos-middle .eae-swiper-button-prev' => 'top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eae-vpos-middle .eae-swiper-button-next' => 'top: {{SIZE}}{{UNIT}}',

				],
				'condition'      => [
					'navigation_button' => 'yes',
					'arrows_layout' => 'inside',
				],
			]
		);

		$widget->add_responsive_control(
			'arrow_gap',
			[
				'label'          => __( 'Arrow Gap', 'wts-eae' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ '%', 'px' ],
				'default'        => [
					'unit' => 'px',
					'size' => '25',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range'          =>
					[
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				'selectors'      => [
					'{{WRAPPER}} .eae-swiper-container'     => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .eae-swiper-outer-wrapper' => 'position: relative',
					'{{WRAPPER}} .eae-swiper-button-prev'   => 'left: 0',
					'{{WRAPPER}} .eae-swiper-button-next'   => 'right: 0',

				],
				'condition'      => [
					'navigation_button' => 'yes',
					'arrows_layout' => 'outside',
				],
			]
		);

		$widget->add_responsive_control(
			'arrow_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'separator' => 'after',
				'selectors'  => [
					'{{WRAPPER}} .eae-swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eae-swiper-button-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' =>
					[
						'navigation_button' => 'yes',
					],
			]
		);

		$widget->add_control(
			'heading_style_dots',
			[
				'label'     => __( 'Dots', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'condition' =>
					[
						'ptype' => 'bullets',
					],
			]
		);

		$widget->add_responsive_control(
			'dots_size',
			[
				'label'     => __( 'Dots Size', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 5,
					],
				'range'     =>
					[
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width:{{SIZE}}px; height:{{SIZE}}px;',
				],
				'condition' =>
					[
						'ptype' => 'bullets',
					],
			]
		);

		$widget->add_responsive_control(
			'dot_top_offset',
			[
				'label'     => __( 'Top Offset', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .eae-swiper-wrapper' => 'margin-bottom:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'ptype' => 'bullets',
				],
			]
		);

		$widget->add_control(
			'dots_color',
			[
				'label'     => __( 'Active Dot Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color:{{VAlUE}} !important;',
				],
				'condition' =>
					[
						'ptype' => 'bullets',
					],
			]
		);

		$widget->add_control(
			'inactive_dots_color',
			[
				'label'     => __( 'Inactive Dot Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						'ptype' => 'bullets',
					],
			]
		);

		$widget->add_responsive_control(
			'pagination_bullet_margin',
			[
				'label'      => __( 'Margin', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-swiper-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  =>
					[
						'ptype' => 'bullets',
					],
			]
		);

		$widget->add_control(
			'heading_style_fraction',
			[
				'label'     => __( 'Fraction', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' =>
					[
						'ptype' => 'fraction',
					],
			]
		);

		$widget->add_control(
			'fraction_bg_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						'ptype' => 'fraction',
					],
			]
		);

		$widget->add_control(
			'fraction_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color:{{VAlUE}};',
				],
				'condition' =>
					[
						'ptype' => 'fraction',
					],
			]
		);

		$widget->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'pagination_typography',
				'label'     => __( 'Typography', 'wts-eae' ),
				'selector'  => '{{WRAPPER}} .swiper-pagination-fraction',
				'condition' =>
					[
						'ptype' => 'fraction',
					],
			]
		);

		$widget->add_responsive_control(
			'fraction_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  =>
					[
						'ptype' => 'fraction',
					],
			]
		);

		$widget->add_control(
			'heading_style_scroll',
			[
				'label'     => __( 'Scrollbar', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' =>
					[
						'scrollbar' => 'yes',
					],
			]
		);
		$widget->add_control(
			'scroll_size',
			[
				'label'     => __( 'Scrollbar Size', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 5,
					],
				'range' =>
					[
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				'selectors' => [
					'{{WRAPPER}} .eae-swiper-scrollbar.swiper-scrollbar-horizontal' => 'width:{{SIZE}}px;',
					'{{WRAPPER}} .eae-swiper-scrollbar.swiper-scrollbar-horizontal' => 'height:{{SIZE}}px;',
				],
				'condition' =>
					[
						'scrollbar' => 'yes',
					],
			]
		);

		$widget->add_control(
			'scrollbar_color',
			[
				'label'     => __( 'Scrollbar Drag Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-scrollbar-drag' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						'scrollbar' => 'yes',
					],
			]
		);

		$widget->add_control(
			'scroll_color',
			[
				'label'     => __( 'Scrollbar Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-swiper-scrollbar' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						'scrollbar' => 'yes',
					],
			]
		);

		$widget->add_control(
			'heading_style_progress',
			[
				'label'     => __( 'Progress Bar', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' =>
					[
						'ptype' => 'progressbar',
					],
			]
		);
		$widget->add_control(
			'progressbar_color',
			[
				'label'     => __( 'Progress Bar Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						'ptype' => 'progressbar',
					],
			]
		);

		$widget->add_control(
			'progress_color',
			[
				'label'     => __( 'Progress Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar-fill' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						'ptype' => 'progressbar',
					],
			]
		);

		$widget->add_control(
			'progressbar_size',
			[
				'label'     => __( 'Progress Bar Size', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 5,
					],
				'range'     =>
					[
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				'selectors' => [
					'{{WRAPPER}} .swiper-container-horizontal .swiper-pagination-progressbar' => 'height:{{SIZE}}px;',
					'{{WRAPPER}} .swiper-container-vertical .swiper-pagination-progressbar' => 'width:{{SIZE}}px;',
				],
				'condition' =>
					[
						'ptype' => 'progressbar',
					],
			]
		);

		$widget->add_responsive_control(
			'pagination_progress_margin',
			[
				'label'      => __( 'Margin', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-swiper-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  =>
					[
						'ptype' => 'progressbar',
					],
			]
		);
	}

	public static function get_swiper_data($settings, $wid = null) {
		// echo $wid;
		// echo $settings['wid'];
		// die('dfa');
		// echo '<pre>';  print_r($settings); echo '</pre>';
		// die('dfadf');
		if ( $settings['speed']['size'] ) {
			$swiper_data['speed'] = $settings['speed']['size'];
		} else {
			$swiper_data['speed'] = 1000;
		}
		$swiper_data['direction'] = 'horizontal';

		if ( $settings['autoplay'] === 'yes' ) {
			$swiper_data['autoplay']['delay'] = $settings['duration']['size'];
		} else {
			$swiper_data['autoplay'] = false;
		}

		if ( $settings['pause_on_hover'] === 'yes' ) {
			$swiper_data['pause_on_hover'] = $settings['pause_on_hover'];
		}
		if(isset($settings['pause_on_interaction']) && $settings['pause_on_interaction'] == 'yes'){
			$swiper_data['pause_on_interaction'] = $settings['pause_on_interaction'];
		}

		if ( $settings['keyboard'] === 'yes' ) {
			$swiper_data['keyboard'] = $settings['keyboard'];
		}

		if( $settings['grid'] === 'yes' ){
			$swiper_data['grid']['fill'] = 'row';
			$swiper_data['grid']['rows'] = $settings['grid_rows'];
		}else{
			$swiper_data['grid'] = false;
		}

		$swiper_data['effect'] = $settings['effect'];

		$swiper_data['loop']       = $settings['loop'];
		$height                    = $settings['auto_height'];
		$swiper_data['autoHeight'] = ( $height === 'yes' ) ? true : false;
		$ele_breakpoints           = EPlugin::$instance->breakpoints->get_active_breakpoints();
		$active_devices            = EPlugin::$instance->breakpoints->get_active_devices_list();
		$active_breakpoints        = array_keys( $ele_breakpoints );
		$break_value               = [];
		foreach ( $active_devices as $active_device ) {
			$min_breakpoint                = EPlugin::$instance->breakpoints->get_device_min_breakpoint( $active_device );
			$break_value[ $active_device ] = $min_breakpoint;
		}

		if ( $settings['effect'] === 'fade' || $settings['effect'] === 'flip' ) {
			foreach ( $active_devices as $break_key => $active_device ) {
				if ( $active_device === 'desktop' ) {
					$active_device = 'default';
				}
				$swiper_data['spaceBetween'][ $active_device ] = 0;
			}
			foreach ( $active_devices as $break_key => $active_device ) {
				if ( $active_device === 'desktop' ) {
					$active_device = 'default';
				}
				$swiper_data['slidesPerView'][ $active_device ] = 1;
			}
			foreach ( $active_devices as $break_key => $active_device ) {
				if ( $active_device === 'desktop' ) {
					$active_device = 'default';
				}
				$swiper_data['slidesPerGroup'][ $active_device ] = 1;
			}
		} else {

			foreach ( $active_devices as $break_key => $active_device ) {
				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $active_device, [ 'mobile', 'tablet', 'desktop' ] ) ) {
					switch ( $active_device ) {
						case 'mobile':
							$swiper_data['spaceBetween'][ $active_device ] = intval( $settings['space_' . $active_device]['size'] !== '' ? $settings['space_' . $active_device]['size'] : 5 );
							break;
						case 'tablet':
							$swiper_data['spaceBetween'][ $active_device ] = intval( $settings['space_' . $active_device]['size'] !== '' ? $settings['space_' . $active_device]['size'] : 10 );
							break;
						case 'desktop':
							$swiper_data['spaceBetween']['default'] = intval( $settings['space']['size'] !== '' ? $settings['space']['size'] : 15 );
							break;
					}
				} else {
					$swiper_data['spaceBetween'][ $active_device ] = intval( $settings['space_' . $active_device]['size'] !== '' ? $settings['space_' . $active_device]['size'] : 15 );
				}
			}
			// SlidesPerView
			foreach ( $active_devices as $break_key => $active_device ) {
				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $active_device, [ 'mobile', 'tablet', 'desktop' ] ) ) {
					switch ( $active_device ) {
						case 'mobile':
							$swiper_data['slidesPerView'][ $active_device ] = intval( $settings['slide_per_view_' . $active_device] !== '' ? $settings['slide_per_view_' . $active_device] : 1 );
							break;
						case 'tablet':
							$swiper_data['slidesPerView'][ $active_device ] = intval( $settings['slide_per_view_' . $active_device] !== '' ? $settings['slide_per_view_' . $active_device] : 2 );
							break;
						case 'desktop':
							$swiper_data['slidesPerView']['default'] = intval( $settings['slide_per_view'] !== '' ? $settings['slide_per_view'] : 3 );
							break;
					}
				} else {
					$swiper_data['slidesPerView'][ $active_device ] = intval( $settings['slide_per_view_' . $active_device] !== '' ? $settings['slide_per_view_' . $active_device] : 2 );
				}
			}

			// SlidesPerGroup
			foreach ( $active_devices as $break_key => $active_device ) {
				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $active_device, [ 'mobile', 'tablet', 'desktop' ] ) ) {
					switch ( $active_device ) {
						case 'mobile':
							$swiper_data['slidesPerGroup'][ $active_device ] = $settings['slides_per_group_' . $active_device] !== '' ? $settings['slides_per_group_' . $active_device] : 1;
							break;
						case 'tablet':
							$swiper_data['slidesPerGroup'][ $active_device ] = $settings['slides_per_group_' . $active_device] !== '' ? $settings['slides_per_group_' . $active_device] : 1;
							break;
						case 'desktop':
							$swiper_data['slidesPerGroup']['default'] = $settings['slides_per_group'] !== '' ? $settings['slides_per_group'] : 1;
							break;
					}
				} else {
					$swiper_data['slidesPerGroup'][ $active_device ] = $settings['slides_per_group_' . $active_device] !== '' ? $settings['slides_per_group_' . $active_device] : 1;
				}
			}
			
		}

		if ( $settings['ptype'] !== '' ) {
			$swiper_data['ptype'] = $settings['ptype'];
		}
		$swiper_data['breakpoints_value'] = $break_value;
		$clickable                        = $settings['clickable'];
		$swiper_data['clickable']         = isset( $clickable ) ? $clickable : false;
		$swiper_data['navigation']        = $settings['navigation_button'];
		$swiper_data['scrollbar']         = $settings['scrollbar'];
		if(!isset($settings['wid'])){
			$settings['wid'] = null;
		}
		$swiper_data = apply_filters( "eae_swiper_data/{$settings['wid']}", $swiper_data);
		
		return $swiper_data;
	}

	public static function get_swiper_pagination($settings) {
		if ( $settings['ptype'] !== '' ) {
			?>
			<div class = "eae-swiper-pagination swiper-pagination"></div>
			<?php
		}
	}

	public static function get_swiper_scrolbar($settings) {
		if ( $settings['scrollbar'] === 'yes' ) {
			?>
			<div class = "eae-swiper-scrollbar swiper-scrollbar"></div>
			<?php
		}
	}

	public static function get_swiper_arrows($settings) {

		if ( $settings['arrow_horizontal_position'] !== 'center' && $settings['arrows_layout'] === 'inside' ) {
			?>
			<div class="eae-swiper-button-wrapper">
			<?php
		}
		?>
		<div class = "eae-swiper-button-prev swiper-button-prev">
			<?php
			if ( is_rtl() ) {
				Icons_Manager::render_icon( $settings['arrow_icon_right'], [ 'aria-hidden' => 'true' ] );
			} else {
				Icons_Manager::render_icon( $settings['arrow_icon_left'], [ 'aria-hidden' => 'true' ] );
			}
			?>
		</div>
		<div class = "eae-swiper-button-next swiper-button-next">
			<?php
			if ( is_rtl() ) {
				Icons_Manager::render_icon( $settings['arrow_icon_left'], [ 'aria-hidden' => 'true' ] );
			} else {
				Icons_Manager::render_icon( $settings['arrow_icon_right'], [ 'aria-hidden' => 'true' ] );
			}
			?>
		</div>
		<?php
		if ( $settings['arrow_horizontal_position'] !== 'center' && $settings['arrows_layout'] === 'inside' ) {
			;
			?>
			</div>
			<?php
		}
	}
}
<?php

namespace WTS_EAE\Modules\ShapeSeparator\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ShapeSeparator extends EAE_Widget_Base {


	public function get_name() {
		return 'wts-shape-separator';
	}

	public function get_title() {
		return __( 'Shape Separator', 'wts-eae' );
	}

	public function get_icon() {
		return 'wpv wpv-shape-divider';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_shape',
			[
				'label' => __( 'Shape', 'wts-eae' ),
			]
		);

		$this->add_control(
			'separator_shape',
			[
				'label'   => __( 'Shape', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'triangle-upper-left'   => __( 'Triangle Upper Left', 'wts-eae' ),
					'triangle-upper-right'  => __( 'Triangle Upper Right', 'wts-eae' ),
					'triangle-bottom-left'  => __( 'Triangle Bottom Left', 'wts-eae' ),
					'triangle-bottom-right' => __( 'Triangle Bottom Right', 'wts-eae' ),
				],
				'default' => 'triangle-upper-right',

			]
		);

		$this->add_control(
			'shape_color',
			[
				'label'     => __( 'Shape Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} svg' => 'fill:{{VALUE}}',
				],
			]
		);

		$this->add_control(
			'shape_height',
			[
				'type'        => Controls_Manager::NUMBER,
				'label'       => __( 'Shape Height (in px)', 'wts-eae' ),
				'placeholder' => __( '75', 'wts-eae' ),
				'default'     => __( '75', 'wts-eae' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();
		switch ( $settings['separator_shape'] ) {
			case 'triangle-upper-left':
				include EAE_PATH . 'modules/shape-separator/shapes/triangle-upper-left.php';
				break;
			case 'triangle-upper-right':
				include EAE_PATH . 'modules/shape-separator/shapes/triangle-upper-right.php';
				break;
			case 'triangle-bottom-left':
				include EAE_PATH . 'modules/shape-separator/shapes/triangle-bottom-left.php';
				break;
			case 'triangle-bottom-right':
				include EAE_PATH . 'modules/shape-separator/shapes/triangle-bottom-right.php';
				break;
		}
	}
}

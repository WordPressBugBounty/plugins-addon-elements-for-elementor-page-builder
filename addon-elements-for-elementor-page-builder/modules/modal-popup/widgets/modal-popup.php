<?php
namespace WTS_EAE\Modules\ModalPopup\Widgets;

use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Plugin as EPlugin;
use Elementor\Controls_Manager;
use Elementor\Utils;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use WTS_EAE\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class ModalPopup extends EAE_Widget_Base {

	public function get_name() {
		return 'wts-modal-popup';
	}

	public function get_title() {
		return __( 'Modal Popup', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-modal-popup';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	private function content_type_options() {
		$options = [
			'content'      => __( 'Content', 'wts-eae' ),
			'savedsection' => __( 'Saved Section', 'wts-eae' ),
			'savedcontainer' => __('Saved Container','wts-eae'),
			'savedpage'    => __( 'Saved Page', 'wts-eae' ),
			'aetemplate'   => __( 'AE Template', 'wts-eae' ),
		];

		return $options;
	}

	protected function get_effect_options () {
		$effects  = [
			''					=>  __('Default', 'wts-eae'),
			'zoom-in'			=>	__('Zoom In', 'wts-eae'),
			'move-horizontal'	=>	__('Move Horizontal In', 'wts-eae'),
			'newspaper'			=>	__('Newspaper', 'wts-eae'),
			'move-from-top'		=>	__('Move From Top', 'wts-eae'),
			'3d-unfold'			=>	__('3d-Unfold', 'wts-eae'),
			'zoom-out'			=>	__('Zoom Out', 'wts-eae')
		]; 

		return $effects;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);
		$this->add_control(
			'content_type',
			[
				'label'   => __( 'Content Type', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => $this->content_type_options(),
				'default' => 'content',

			]
		);
		$this->add_control(
			'preview_modal',
			[
				'label'        => __( 'Preview Modal Popup', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'modal_title',
			[
				'label'       => __( 'Title', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [
					'active' => true,
				],
				'placeholder' => __( 'Enter text', 'wts-eae' ),
				'default'     => __( 'Modal Title', 'wts-eae' ),
			]
		);

		$this->add_control(
			'modal_content',
			[
				'label'       => __( 'Content', 'wts-eae' ),
				'type'        => Controls_Manager::WYSIWYG,
				'placeholder' => __( 'Content', 'wts-eae' ),
				'dynamic'     => [
					'active' => true,
				],
				'default'     => __( 'Add some nice text here.', 'wts-eae' ),
				'condition'   => [
					'content_type' => 'content',
				],
			]
		);

		$saved_sections[''] = __( 'Select Section', 'wts-eae' );
		$saved_sections     = $saved_sections + Helper::select_elementor_page( 'section' );
		$this->add_control(
			'saved_sections',
			[
				'label'     => __( 'Select Section', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_sections,
				'condition' => [
					'content_type' => 'savedsection',
				],
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
					'content_type' => 'savedcontainer'
				]
			]
		);

		$saved_pages[''] = __( 'Select Page', 'wts-eae' );
		$saved_pages     = $saved_pages + Helper::select_elementor_page( 'page' );
		$this->add_control(
			'saved_pages',
			[
				'label'     => __( 'Select Page', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_pages,
				'condition' => [
					'content_type' => 'savedpage',
				],
			]
		);

		$saved_ae_template[''] = __( 'Select AE Template', 'wts-eae' );
		$saved_ae_template     = $saved_ae_template + Helper::select_ae_templates();
		$this->add_control(
			'saved_ae_template',
			[
				'label'     => __( 'Select AE Template', 'wts-eae' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => $saved_ae_template,
				'condition' => [
					'content_type' => 'aetemplate',
				],
			]
		);

		$this->add_control(
			'support_loop',
			[
				'label' 	=> __( 'Support Loop', 'wts_eae' ),
				'type'		=> Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'modal_setting',
			[
				'label' => __( 'Modal Setting', 'wts-eae' ),
			]
		);

		$this->add_responsive_control(
			'modal_width',
			[
				'label'     => __( 'Modal Width', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'.eae-wrap-{{ID}}.eae-popup .mfp-inline-holder .mfp-content' => 'width: {{SIZE}}%;',
				],
			]
		);
		$this->add_responsive_control(
			'modal_height',
			[
				'label'      => __( 'Modal Height', 'wts-eae' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1440,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => [ 'px', 'vh', 'vw' ],
				'selectors'  => [
					'.eae-wrap-{{ID}}.eae-popup .mfp-inline-holder .mfp-content' => 'height: {{SIZE}}{{UNIT}}; overflow-y: auto',

				],
			]
		);

		$effects = $this->get_effect_options();

		$this->add_control(
			'effect',
			[
				'label'		=>	__('Effect', 'wts-eae'),
				'type'		=>	Controls_Manager::SELECT,
				'options'	=>	$effects,
				'default'	=>	''
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => __( 'Overlay Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.75)',
				'selectors' => [
					'body .eae-popup.mfp-bg.eae-wrap-{{ID}}' => 'background-color: {{VALUE}};',
				],

			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'display_setting',
			[
				'label' => __( 'Display Setting', 'wts-eae' ),
			]
		);

		$this->add_control(
			'button_type',
			[
				'label'        => __( 'Button Type', 'wts-eae' ),
				'type'         => Controls_Manager::CHOOSE,
				'default'      => 'text',
				'options'      => [
					'image' => [
						'title' => __( 'Image', 'wts-eae' ),
						'icon'  => 'fa fa-image',
					],
					'text' => [
						'title' => __( 'Text', 'wts-eae' ),
						'icon'  => 'fa fa-font',
					],
				],
				'render_type'  => 'template',
				'prefix_class' => 'eae-pop-btn-type-',
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'     => __( 'Button Text', 'wts-eae' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Click Me',
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'button_type' => 'text',
				],

			]
		);

		$this->add_control(
			'button_image',
			[
				'label'      => __( 'Image', 'wts-eae' ),
				'type'       => Controls_Manager::MEDIA,
				'dynamic'    => [
					'active' => true,
				],
				'default'    => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'show_label' => true,
				'condition'  => [
					'button_type' => 'image',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'image',
				'exclude'   => [ 'custom' ],
				'condition' => [
					'button_type' => 'image',
				],
			]
		);

		$this->add_control(
			'button_align',
			[
				'label'        => __( 'Alignment', 'wts-eae' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [
					'left'    => [
						'title' => __( 'Left', 'wts-eae' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'  => [
						'title' => __( 'Center', 'wts-eae' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'   => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Right', 'wts-eae' ),
						'icon'  => 'fa fa-align-justify',
					],
				],
				'prefix_class' => 'eae-pop-btn-align-',
			]
		);

		$this->add_control(
			'button_icon_new',
			[
				'label'            => __( 'Icon', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'button_icon',
				'default'          => [
					'value'   => 'fas fa-star',
					'library' => 'fa-solid',
				],
			]
		);
		$this->add_control(
			'icon_position',
			[
				'label'   => __( 'Icon Position', 'wts-eae' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'before' => __( 'Before', 'wts-eae' ),
					'after'  => __( 'After', 'wts-eae' ),
				],
				'default' => 'before',
			]
		);

		$this->add_control(
			'icon_spacing',
			[
				'label'     => __( 'Icon Spacing', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 6,
				],
				'selectors' => [
					'{{WRAPPER}} .icon-position-before i , {{WRAPPER}} .icon-position-before svg' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .icon-position-after i , {{WRAPPER}} .icon-position-after svg'  => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'popup_styling',
			[
				'label' => __( 'Popup', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label' => __( 'Title', 'wts-eae' ),
				'type'  => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'.eae-popup.eae-wrap-{{ID}} .mfp-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'title_bg_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.eae-popup.eae-wrap-{{ID}} .mfp-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'.eae-popup.eae-wrap-{{ID}} .mfp-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'wts-eae' ),
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '.eae-popup.eae-wrap-{{ID}} .mfp-title',
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'item_text_shadow',
				'label'    => 'Text Shadow',
				'selector' => '.eae-popup.eae-wrap-{{ID}} .mfp-title',
			]
		);

		$this->add_control(
			'content_heading',
			[
				'label'     => __( 'Content', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'content_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,

				'global'    => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'.eae-popup.eae-wrap-{{ID}} .eae-modal-content' => 'color: {{VALUE}};',
				],
				'condition' => [
					'content_type' => 'content',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.eae-popup.eae-wrap-{{ID}} .mfp-inline-holder .mfp-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'content_typography',
				'label'     => __( 'Typography', 'wts-eae' ),
				'global'    => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector'  => '.eae-popup.eae-wrap-{{ID}} .eae-modal-content',
				'condition' => [
					'content_type' => 'content',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'      => 'content_text_shadow',
				'label'     => 'Text Shadow',
				'selector'  => '.eae-popup.eae-wrap-{{ID}} .eae-modal-content',
				'condition' => [
					'content_type' => 'content',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'.eae-popup.eae-wrap-{{ID}} .eae-modal-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'box_border',
				'label'     => __( 'Border', 'wts-eae' ),
				'separator' => 'before',
				'selector'  => '.eae-popup.eae-wrap-{{ID}} .mfp-inline-holder .mfp-content',
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'.eae-popup.eae-wrap-{{ID}} .mfp-inline-holder .mfp-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'item_box_shadow',
				'label'    => 'Box Shadow',
				'selector' => '.eae-popup.eae-wrap-{{ID}} .mfp-content',
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'button_styling',
			[
				'label' => __( 'Button', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'btn_text_typography',
				'label'     => __( 'Typography', 'wts-eae' ),
				'global'    => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector'  => '{{WRAPPER}} .eae-popup-link',
				'condition' => [
					'button_type' => 'text',
				],
			]
		);

		$this->start_controls_tabs(
			'eg_items_tab',
			[
				'condition' => [
					'button_type' => 'text',
				],
			]
		);

		$this->start_controls_tab(
			'btn_default',
			[
				'label'     => __( 'Default', 'wts-eae' ),
				'condition' => [
					'button_type' => 'text',
				],
			]
		);
		$this->add_control(
			'button_color',
			[
				'label'     => __( 'Text Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-popup-link'     => 'color: {{VALUE}};',
					'{{WRAPPER}} .eae-popup-link svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'btn_text_shadow',
				'label'    => 'Text Shadow',
				'selector' => '{{WRAPPER}} .eae-popup-link',

			]
		);

		$this->add_control(
			'btn_background_color',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .eae-popup-link' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_border_radius',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-popup-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btn_box_shadow',
				'label'    => 'Box Shadow',
				'selector' => '{{WRAPPER}} .eae-popup-link',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'btn_hover',
			[
				'label'     => __( 'Hover', 'wts-eae' ),
				'condition' => [
					'button_type' => 'text',
				],
			]
		);
		$this->add_control(
			'button_color_hover',
			[
				'label'     => __( 'Text Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-popup-link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name'     => 'btn_text_shadow_hover',
				'label'    => 'Text Shadow',
				'selector' => '{{WRAPPER}} .eae-popup-link:hover',
			]
		);

		$this->add_control(
			'btn_background_color_hover',
			[
				'label'     => __( 'Background Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#61ce70',
				'selectors' => [
					'{{WRAPPER}} .eae-popup-link:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_border_color_hover',
			[
				'label'     => __( 'Border Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-popup-link:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'btn_border_radius_hover',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-popup-link:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'btn_box_shadow_hover',
				'label'    => 'Box Shadow',
				'selector' => '{{WRAPPER}} .eae-popup-link:hover',
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __( 'Padding', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-popup-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'btn_border',
				'label'    => __( 'Border', 'wts-eae' ),
				'selector' => '{{WRAPPER}} .eae-popup-link',
			]
		);
		$this->add_control(
			'btn_border_radius_image',
			[
				'label'      => __( 'Border Radius', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-popup-link'     => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eae-popup-link img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'button_type' => 'image',
				],
			]
		);

		$this->add_control(
			'btn_border_radius_image_hover',
			[
				'label'      => __( 'Border Radius Hover', 'wts-eae' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .eae-popup-link:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .eae-popup-link:hover img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'button_type' => 'image',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'close_button_styling',
			[
				'label' => __( 'Close Button', 'wts-eae' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'btn_in_out',
			[
				'label'   => __( 'Button Inside', 'wts-eae' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);
		$this->add_control(
			'close_btn_icon_new',
			[
				'label'            => __( 'Icon', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'close_btn_icon',
				'default'          => [
					'value'   => 'fas fa-times',
					'library' => 'fa-solid',
				],
				'recommended'      => [
					'fa-solid' => [
						'times',
						'times-circle-o',
						'window-close-o',
					],
					'fa-regular' => [
						'times-circle',
						'window-close',
					],
				],

			]
		);
		$this->add_control(
			'close_btn_size',
			[
				'label'     => __( 'Size', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 60,
					],
				],
				'default'   => [
					'size' => 28,
				],
				'selectors' => [
					'.eae-popup.eae-wrap-{{ID}} .eae-close'    => 'font-size: {{SIZE}}px;height: {{SIZE}}px;width: {{SIZE}}px;',
					'.eae-popup.eae-wrap-{{ID}} svg.eae-close' => 'width: {{SIZE}}px;height: {{SIZE}}px;width: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'close_btn_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.eae-popup.eae-wrap-{{ID}}.eae-popup .eae-close' => 'color: {{VALUE}};',
					'.eae-popup.eae-wrap-{{ID}}.eae-popup svg' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'close_btn_position_top_in',
			[
				'label'      => __( 'Position Top', 'wts-eae' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default'    => [
					'size' => 10,
				],
				'selectors'  => [
					'.eae-popup.eae-wrap-{{ID}} .eae-close' => 'top:{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'btn_in_out' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'close_btn_position_right_in',
			[
				'label'      => __( 'Position Right', 'wts-eae' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'default'    => [
					'size' => 10,
				],
				'selectors'  => [
					'.eae-popup.eae-wrap-{{ID}} .eae-close' => 'right:{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'btn_in_out' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'close_btn_position_top_out',
			[
				'label'      => __( 'Position Top', 'wts-eae' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'.eae-popup.eae-wrap-{{ID}} .eae-close' => 'top:{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'btn_in_out!' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'close_btn_position_right_out',
			[
				'label'      => __( 'Position Right', 'wts-eae' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range'      => [
					'%'  => [
						'min' => 0,
						'max' => 100,
					],
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'selectors'  => [
					'.eae-popup.eae-wrap-{{ID}} .eae-close' => 'right:{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'btn_in_out!' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings  = $this->get_settings_for_display();
		$data      = $this->get_data();
		$id        = 'eae-pupup-item-' . wp_rand( 10, 2147483647 );
		$close_btn = $settings['btn_in_out'] === 'yes' ? 'true' : 'false';
		$icon_migrated           = isset( $settings['__fa4_migrated']['button_icon_new'] );
		$icon_is_new             = empty( $settings['button_icon'] );
		$close_btn_icon_migrated = isset( $settings['__fa4_migrated']['close_btn_icon_new'] );
		$close_btn_icon_is_new   = empty( $settings['close_btn_icon'] );
		$effect = Helper::validate_option_value($settings['effect'], $this->get_effect_options(), '');
		$close_button_type = isset( $settings['close_btn_icon_new']['value']['url'] ) ? 'svg' : 'icon';
		if ( $close_button_type === 'svg' ) {
			$close_button = esc_url($settings['close_btn_icon_new']['value']['url']);
		} else {
			if ( $close_btn_icon_migrated || $close_btn_icon_is_new ) :
				$close_button = $settings['close_btn_icon_new']['value'];
			else :
				$close_button = $settings['close_btn_icon'];
			endif;
		}
		// $close_button = "fas fa-times&quot;&gt;&lt;img src=x onerror=alert(1);&gt;&gt;&quot;";
		
		$this->add_render_attribute( 'eae-popup-wrapper', 'class', 'eae-popup-wrapper eae-popup-' . $id );
		$this->add_render_attribute( 'eae-popup-wrapper', 'data-id', $id );
		$this->add_render_attribute( 'eae-popup-wrapper', 'data-preview-modal', $settings['preview_modal'] );
		$this->add_render_attribute( 'eae-popup-wrapper', 'data-close-button-type', $close_button_type );
		$this->add_render_attribute( 'eae-popup-wrapper', 'data-close-btn', $close_button );
		$this->add_render_attribute( 'eae-popup-wrapper', 'data-close-in-out', $close_btn );
		$this->add_render_attribute( 'eae-popup-wrapper', 'data-effect', $effect );

		$post_id = 0;
		if($settings['support_loop'] == 'yes' ){
			global $post;
			global $wp_query;
			$old_queried_object = $wp_query->queried_object;
			$wp_query->queried_object = $post;
			$post_id = $post->ID;
		}
		?>
		<div <?php echo $this->get_render_attribute_string('eae-popup-wrapper');?>>
			<a class="eae-popup-link icon-position-<?php echo esc_attr($settings['icon_position']); ?>"
			data-id="<?php echo esc_attr($id); ?>" data-ctrl-id="<?php echo esc_attr($data['id']); ?>" href="#<?php echo esc_attr($id); ?>">
				<?php if ( $settings['button_type'] === 'text' ) { ?>
					<?php
					if ( $settings['button_icon_new'] !== '' && $settings['icon_position'] === 'before' ) {
						?>
					<span class="eae-popup-btn-icon">
						<?php
						if ( $icon_migrated || $icon_is_new ) :
							Icons_Manager::render_icon( $settings['button_icon_new'], [ 'aria-hidden' => 'true' ] );
					else :
						?>
						<i class="<?php echo esc_attr($settings['button_icon']); ?>"></i>
					<?php endif; ?>
					</span>
						<?php
					}
					?>
						<span class="eae-popup-btn-text">
							<?php echo Helper::eae_wp_kses($settings['button_text']); ?>
						</span>
					<?php if ( $settings['button_icon_new'] !== '' && $settings['icon_position'] === 'after' ) { ?>
					<span class="eae-popup-btn-icon">
						<?php
						if ( $icon_migrated || $icon_is_new ) :
							Icons_Manager::render_icon( $settings['button_icon_new'], [ 'aria-hidden' => 'true' ] );
					else :
						?>
						<i class="<?php echo esc_attr($settings['button_icon']); ?>"></i>
					<?php endif; ?>
					</span>
						<?php
					}
					?>
					<?php
				} else {
					$image_id = $settings['button_image']['id'];
					$image    = wp_get_attachment_image( $image_id, $settings['image_size'] );
					echo $image;
					?>
				<?php } ?>
			</a>
		</div>

		<div id="<?php echo esc_attr($id); ?>" class="eae-popup-<?php echo esc_attr($id); ?> mfp-hide eae-popup-container">
			<input class="eae-hidden-post-id" type="hidden" name="eae_hidden_post_id" data-id="<?php echo $id; ?>" value="<?php echo $post_id ?>" />
			<div class="eae-popup-content">
				<?php
				if ( $settings['content_type'] === 'content' ) {

					if ( $settings['modal_title'] !== '' ) {
						?>
						<div class="eae-modal-title mfp-title">
							<?php echo do_shortcode( $settings['modal_title'] ); ?>
						</div>
					<?php } ?>
					<div class="eae-modal-content">
						<?php echo do_shortcode( $settings['modal_content'] ); ?>
					</div>
					<?php
				} elseif ( $settings['content_type'] === 'savedsection' ) {
					if ( $settings['modal_title'] !== '' ) {
						?>
						<div class="eae-modal-title mfp-title">
							<?php echo Helper::eae_wp_kses($settings['modal_title']); ?>
						</div>
					<?php } ?>
					<div class="eae-modal-content">
						<?php if(Helper::check_template($settings['saved_sections']) !== ''){
							echo EPlugin::instance()->frontend->get_builder_content_for_display( $settings['saved_sections'] );
						} ?>
					</div>
					<?php
				} elseif ( $settings['content_type'] === 'savedpage' ) {
					if ( $settings['modal_title'] !== '' ) {
						?>
						<div class="eae-modal-title mfp-title">
							<?php echo Helper::eae_wp_kses($settings['modal_title']); ?>
						</div>
					<?php } ?>
					<div class="eae-modal-content">
						<?php if(Helper::check_template($settings['saved_pages']) !== ''){
							echo EPlugin::instance()->frontend->get_builder_content_for_display( $settings['saved_pages'] );
						} ?> 
					</div>
					<?php
				} elseif ( $settings['content_type'] === 'aetemplate' ) {
					if ( $settings['modal_title'] !== '' ) {
						?>
						<div class="eae-modal-title mfp-title">
							<?php echo Helper::eae_wp_kses($settings['modal_title']); ?>
						</div>
					<?php } ?>
					<div class="eae-modal-content">
						<?php if(Helper::check_template($settings['saved_ae_template']) !== ''){
							echo EPlugin::instance()->frontend->get_builder_content_for_display( $settings['saved_ae_template'] );
						} ?>
					</div>
					<?php
				} elseif ( $settings['content_type'] === 'savedcontainer' ) {
					if ( $settings['modal_title'] !== '' ) {
						?>
						<div class="eae-modal-title mfp-title">
							<?php echo Helper::eae_wp_kses($settings['modal_title']); ?>
						</div>
					<?php } ?>
					<div class="eae-modal-content">
						<?php if(Helper::check_template($settings['saved_container']) !== ''){
							echo EPlugin::instance()->frontend->get_builder_content_for_display( $settings['saved_container'] );
						} ?>
					</div>
					<?php
				}else {
					echo wp_kses_post($settings['content_type']);
				}
				?>
			</div>
		</div>
		<?php
		if($settings['support_loop'] == 'yes' ){
			$wp_query->queried_object = $old_queried_object;
		}
	}

	public function check_template($template_id){
		$post = get_posts(
			[
				'post_type' => get_post_type($template_id),
				'post__in' => [$template_id] 
			]
		);

		if(!$post){
			return '';
		}
		return $template_id;
	}
}

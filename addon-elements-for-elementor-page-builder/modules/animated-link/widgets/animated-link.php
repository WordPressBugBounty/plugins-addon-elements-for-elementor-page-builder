<?php 

namespace WTS_EAE\Modules\AnimatedLink\Widgets;

use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use WTS_EAE\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class AnimatedLink extends EAE_Widget_Base {

    public function get_name() {
		return 'eae-animated-link';
	}

	public function get_title() {
		return __( 'Animated Link', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-advance-button';
	}
    public function get_script_depends() {
		return [ 'eae-lottie' ];
	}

	
    
    protected function register_controls()
    {

        $this->start_controls_section(
            'eae_animated_link_content',
            [
                'label'=>esc_html__('Content','wts-eae'),
            ]      
        );

            $this->add_control(
                'animated_link_effect',
                [
                    'label'   => __( 'Effect', 'wts-eae' ),
                    'type'    => Controls_Manager::SELECT,
                    'options' => [
                        'effect1'  => __( 'Effect 1', 'wts-eae' ),
                        'effect2'  => __( 'Effect 2', 'wts-eae' ),
                        'effect3'  => __( 'Effect 3', 'wts-eae' ),
                        'effect4'  => __( 'Effect 4', 'wts-eae' ),
                        'effect5'  => __( 'Effect 5', 'wts-eae' ),
                        'effect6'  => __( 'Effect 6', 'wts-eae' ),
                        'effect7'  => __( 'Effect 7', 'wts-eae' ),
                        'effect8'  => __( 'Effect 8', 'wts-eae' ),
                        'effect9'  => __( 'Effect 9', 'wts-eae' ),
                        'effect10'  => __( 'Effect 10', 'wts-eae' ),
                        'effect11'  => __( 'Effect 11', 'wts-eae' ),
                        'effect12'  => __( 'Effect 12', 'wts-eae' ),
                        'effect13'  => __( 'Effect 13', 'wts-eae' ),
                        'effect14'  => __( 'Effect 14', 'wts-eae' ),
                        'effect15'  => __( 'Effect 15', 'wts-eae' ),
                    ],
                    'default' => 'effect1',
                ]
            );

            $this->add_control(
                'title',[
                    'label' => esc_html__( 'Text', 'wts-eae' ),
                    'type' => Controls_Manager::TEXT,
                    'label_block' => true,
                    'dynamic' => [
					    'active' => true,
				    ],
                    'default' => esc_html__( 'Animated Link', 'wts-eae'),
                ]
            );

            $this->add_control(
                'link',
                [
                    'label' => esc_html__( 'Link', 'wts-eae' ),
                    'type' => Controls_Manager::URL,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'default' => [
                        'url' => '#',
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
                    ],
                    'selectors'   => [
                        '{{WRAPPER}} .eae-animated-link-container' => 'text-align: {{VALUE}};',
                    ],
                ]
            );

        $this->end_controls_section();
        
        $this->start_controls_section(
            'eae_animated_link_icon',
            [
                'label'=>esc_html__('Icon','wts-eae'),
                ]      
            );

            Helper::eae_media_controls(
                $this,
                [
                    'name'          => 'before_icon',
                    'label'         => __( 'Before Icon', 'wts-eae' ),
                    'icon'			=> true,
                    'image'			=> true,
                    'lottie'		=> true,
                ]
            );

            Helper::eae_media_controls(
                $this,
                [
                    'name'          => 'after_icon',
                    'label'         => __( 'After Icon', 'wts-eae' ),
                    'icon'			=> true,
                    'image'			=> true,
                    'lottie'		=> true,
                ]
            );


            
        $this->end_controls_section();

        $this->start_controls_section(
			'eae-section_style_link',
			[
				'label'     => esc_html__('Animated Link', 'wts-eae'),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'link_text_color',
			[
				'label'     => esc_html__('Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-animated-link-wrapper' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_hover_text_color',
			[
				'label'     => esc_html__('Hover Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-animated-link-wrapper:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_style_color',
			[
				'label'     => esc_html__('Style Color', 'wts-eae'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .eae-animated-link-wrapper:not(.eae-animation-effect12)::before, {{WRAPPER}} .eae-animated-link-wrapper:not(.eae-animation-effect12)::after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect12::before, {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect12::after' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect15 span::before, {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect15 span::after' => 'background: {{VALUE}};',
					'{{WRAPPER}} .eae-animated-link-wrapper > svg, {{WRAPPER}} .eae-animated-link-wrapper > svg' => 'stroke: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'link_padding',
			[
				'label'      => esc_html__('Padding', 'wts-eae'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .eae-animated-link-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'link_typography',
				'selector' => '{{WRAPPER}} .eae-animated-link-wrapper',
			]
		);

        $this->add_responsive_control(
			'transition',
			[
				'label' => esc_html__( 'Transition Duration',  'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 4,
                        'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect1::before ' => 'transition: transform {{SIZE}}s;',
					'{{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect2::before , {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect2::after ' => 'transition: all {{SIZE}}s;',
					'{{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect2:hover::before , {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect2:hover::after ' => 'transition: all {{SIZE}}s;',
                    '{{WRAPPER}} .eae-animation-effect3:hover .eae-effect3-animation-svg path ' => 'transition-duration: {{SIZE}}s;',
                    '{{WRAPPER}} .eae-animation-effect4:before , {{WRAPPER}} .eae-animation-effect4 ' => ' transition: all {{SIZE}}s ;',
                    '{{WRAPPER}} .eae-animation-effect4:hover:before , {{WRAPPER}} .eae-animation-effect4:hover ' => ' transition: all {{SIZE}}s ;',
                    '{{WRAPPER}} .eae-animation-effect5::before , {{WRAPPER}} .eae-animation-effect5 ' => 'transition: all {{SIZE}}s ;',
                    '{{WRAPPER}} .eae-animation-effect6:before , {{WRAPPER}} .eae-animation-effect6 ' => 'transition: all {{SIZE}}s ;',
                    '{{WRAPPER}} .eae-animation-effect7:hover span ' => ' animation: effectglitchtext {{SIZE}}s linear;',
                    '{{WRAPPER}} .eae-animation-effect7:hover:before ' => 'animation: effectglitchline {{SIZE}}s steps(2, start) forwards;',
                    '{{WRAPPER}} .eae-animation-effect8:before ' => 'transition: transform {{SIZE}}s;',
                    '{{WRAPPER}} .eae-animation-effect8:after ' => 'transition: transform {{SIZE}}s;',
                    '{{WRAPPER}} .eae-animation-effect9 .eae-effect9-animation-svg ' => 'transition: transform {{SIZE}}s ;',
                    '{{WRAPPER}} .eae-animation-effect10:hover:before ' => ' animation: effect10 {{SIZE}}s ease forwards;',
                    '{{WRAPPER}} .eae-animation-effect10:after ' => ' transition: opacity {{SIZE}}s ;',
                    '{{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect11:after , {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect11:before ' => 'transition: transform {{SIZE}}s;',
                    '{{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect12::after , {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect12::before ' => ' transition: all {{SIZE}}s',
                    '{{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect13::after , {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect13::before ' => ' transition: all {{SIZE}}s',
                    '{{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect14::after , {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect14::before ' => ' transition: all {{SIZE}}s',
                    '{{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect15:before , {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect15:after , {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect15 > span:before , {{WRAPPER}} .eae-animated-link-wrapper.eae-animation-effect15 > span:after ' => ' transition: all {{SIZE}}s',
				],
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
			'eae-section_before_icon_link',
			[
				'label'     => esc_html__('Before Icon', 'wts-eae'),
				'tab'       => Controls_Manager::TAB_STYLE,
               'condition'     => [
                    'before_icon_graphic_type!'   => 'none',
                ]
			]
		);  
            $this->add_responsive_control(
                'before_space',
                [
                    'label' => esc_html__( 'Icon Gap',  'wts-eae' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' =>  ['px'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 150,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 5,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-animated-link-wrapper span .eae-animated-link-before-icon' => ' margin-right:{{SIZE}}{{UNIT}}',
                    ],
                ]
            );

            Helper::global_icon_style_controls(
                $this,
                [
                    'name' => 'before_icon',
                    'selector' => '.eae-animated-link-before-icon',
                    'hover_selector'   => '.eae-animated-link-wrapper:hover .eae-animated-link-before-icon',
                    'is_parent_hover' => true,
                ]
            );

		$this->end_controls_section();

        $this->start_controls_section(
			'eae-section_after_icon_link',
			[
				'label'     => esc_html__('After Icon', 'wts-eae'),
				'tab'       => Controls_Manager::TAB_STYLE,
               'condition'     => [
                    'after_icon_graphic_type!'   => 'none',
                ]
			]
		);

            $this->add_responsive_control(
                'after_space',
                [
                    'label' => esc_html__( 'Icon Gap',  'wts-eae' ),
                    'type' => Controls_Manager::SLIDER,
                    'size_units' =>  ['px'],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 150,
                            'step' => 1,
                        ],
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => 5,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-animated-link-wrapper span .eae-animated-link-after-icon' => ' margin-left:{{SIZE}}{{UNIT}}',
                    ],
                ]
            );

            Helper::global_icon_style_controls(
                $this,
                [
                    'name' => 'after_icon',
                    'selector' => '.eae-animated-link-after-icon',
                    'hover_selector'   => '.eae-animated-link-wrapper:hover .eae-animated-link-after-icon',
                    'is_parent_hover' => true,
                ]
            );

		$this->end_controls_section();

    }
    protected function render(){ 
        $settings = $this->get_settings_for_display();
        $this->add_render_attribute('eae-animated-link-wrapper', 'class', 'eae-animated-link-wrapper');
        $this->add_render_attribute('eae-animated-link-wrapper', 'class', 'eae-animation-'.$settings['animated_link_effect']);
        if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'eae-animated-link-wrapper', $settings['link'] );
		} 
        ?>
        <div class="eae-animated-link-container">
            <a <?php echo $this->get_render_attribute_string( 'eae-animated-link-wrapper' ); ?>>
                <span>
                    <?php  Helper::render_icon_html($settings, $this,'before_icon','eae-animated-link-before-icon', 'test');?>
                    <?php echo esc_html($settings['title']); ?>
                    <?php  Helper::render_icon_html($settings, $this,'after_icon','eae-animated-link-after-icon', 'test');?>
                </span>
                <?php if($settings['animated_link_effect'] == 'effect3') { ?>
                    <svg class="eae-effect3-animation-svg" width="100%" height="9" viewBox="0 0 101 9">
                    <path d="M0.5 2C4 1.5 16 0 20 2C23 3.5 24 5 27 6.5C30 8 34 3 38 2C45 0.5 52 6 56 4C58.5 3 60 1 66 2.5C69 3.5 72 4 75 5C80 6.5 95 -3 100 1" pathLength="1"></path>
                    </svg>
                <?php
                }?>
                <?php if($settings['animated_link_effect'] == 'effect9') { ?>
                    <svg class="eae-effect9-animation-svg" width="300%" height="100%" viewBox="0 0 1200 60" preserveAspectRatio="none">
                    <path d="M0,55c0,0,300,0,400,0C450,55,520,45,600,45c80,0,140,10,210,10c100,0,410,0,410,0"></path>
                    </svg>
                <?php
                }?>
            </a>
        </div>
        <?php
    }

    public function content_template() {
		?>
		<#
        let beforeIconName = 'before_icon';
        let afterIconName = 'after_icon';
        let renderIconHtml = function(sett, control_name, wClass = '', index='') {
            var icon_class = '';
            let imageHtml = '';
            let lottie_data = [];
            if(sett[control_name+'_graphic_type'] != 'none' ){
                icon_class += ' eae-gbl-icon eae-graphic-type-'+ sett[control_name+'_graphic_type'];
                if(wClass != ''){
                    icon_class += ' '+wClass;     
                }
                icon_class += ' eae-graphic-view-'+sett[control_name+'_view']; 
                if(sett[control_name+'_view'] != 'default'){
                    icon_class += ' eae-graphic-shape-'+sett[control_name+'_shape'];
                }
                if(sett[control_name+'_graphic_type'] == 'lottie'){
                    if( (sett[control_name+'_lottie_animation_url'] != '' ) ||  (sett[control_name+'_source_json']['url'] != '') ) {
                        icon_class += ' eae-lottie-animation eae-lottie';
                        lottie_data = {
                            'loop' : ( sett[control_name+'_lottie_animation_loop'] === 'yes' ) ? true : false,
                            'reverse' : ( sett[control_name+'_lottie_animation_reverse'] === 'yes' ) ? true : false,
                        } 
                        if(sett[control_name+'_source'] == 'media_file' && (sett[control_name+'_source_json']['url'] != '')){
                            lottie_data.url = sett[control_name+'_source_json']['url'];
                        }else{
                            lottie_data.url = sett[control_name+'_lottie_animation_url'];
                        }
                        view.addRenderAttribute('panel-icon-'+ index, 'data-lottie-settings', JSON.stringify(lottie_data));
                    }         
                }
                view.addRenderAttribute('panel-icon-'+ index, 'class', icon_class);
                if(sett[control_name+'_graphic_type'] == 'lottie'){
                    #>
                    <div {{{ view.getRenderAttributeString( 'panel-icon-'+ index ) }}}></div>
                    <#
                }else{
                    if(sett[control_name+'_graphic_type'] === 'icon'){
                        var icon = elementor.helpers.renderIcon( view, sett[control_name+'_graphic_icon'], { 'aria-hidden': true }, 'i' , 'object' );
                        imageHtml = icon.value;
                        #>
                        <div {{{ view.getRenderAttributeString( 'panel-icon-'+ index ) }}}>
                            {{{imageHtml}}}
                        </div>
                        <#
                    }else{
                        var image = {
                            id: sett[control_name+'_graphic_image']['id'],
                            url: sett[control_name+'_graphic_image']['url'],
                            size: sett[control_name+'_graphic_image_size'],
                            dimension: sett[control_name+'_graphic_image_custom_dimension'],
                            model: view.getEditModel()
                        };
                        var image_url = elementor.imagesManager.getImageUrl( image );
                        imageHtml = '<img src="' + image_url + '" class="elementor-animation-' + settings.hover_animation + '" />';
                        #>
                        <div {{{ view.getRenderAttributeString( 'panel-icon-'+ index ) }}}>
                            {{{imageHtml}}}
                        </div>
                        <#
                    }
                }
            }
            return true;
        }
		view.addRenderAttribute( 'eae-animated-link-wrapper', 'class', 'eae-animated-link-wrapper' );
		view.addRenderAttribute( 'eae-animated-link-wrapper', 'class', 'eae-animation-'+ settings.animated_link_effect );
		if(settings.link.url) {
            view.addRenderAttribute( 'eae-animated-link-wrapper', 'href', settings.link.url );
        }
		#>
		<div class="eae-animated-link-container">
            <a {{{view.getRenderAttributeString('eae-animated-link-wrapper') }}}>
                <span>
                    <#
                    renderIconHtml(settings, beforeIconName, 'eae-animated-link-before-icon', 'before');
                    #>
                    {{ settings.title }}
                    <#
                    renderIconHtml(settings, afterIconName, 'eae-animated-link-after-icon', 'after');
                    #>
                </span>
            </a>
		</div>
		<?php
	}
}

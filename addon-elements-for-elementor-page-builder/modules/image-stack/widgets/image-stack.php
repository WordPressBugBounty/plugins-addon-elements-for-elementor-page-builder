<?php

namespace WTS_EAE\Modules\ImageStack\Widgets;

use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use WTS_EAE\Classes\Helper;
use WTS_EAE\Plugin as EAE;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class ImageStack extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-image-stack';
	}

	public function get_title() {
		return __( 'Image Stack', 'wts-eae' );
	}

	public function get_icon() {
		return 'wpv wpv-image-stack';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'image stack'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie' ];
	}

    protected function register_controls(){
        $this->start_controls_section(
			'stack_content',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);

        $options = [
            'single' => __( 'Single', 'wts-eae' ),
            'repeater' => __( 'Repeater', 'wts-eae' ),
        ];

        //$options = apply_filters('eae_stack_source_options', $options);

        $this->add_control(
			'stack_source',
			[
				'label' => esc_html__( 'Source', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => $options,
				'default' => 'single',
			]
		);


        $args = [
            'name' => 'stack_source_repeater_pro_options',
            'conditions' => [
                'stack_source' => [ 'repeater' ],
            ],
        ];
        
        // $conditions = [
        //     'stack_source' => [ 'repeater' ],
        // ];

        Helper::add_eae_pro_notice_controls($this, $args);

        //  Single Data

        $this->add_control(
			'images',
			[
				'label' => esc_html__( 'Add Images', 'wta-eae' ),
				'type' => Controls_Manager::GALLERY,
				'show_label' => false,
				'dynamic' => [
					'active' => true,
				],
                'condition'=>[
                    'stack_source'=>'single',
                ]
			]
		);

        $this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'img', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
				'default' => 'large',
				'separator' => 'none',
                'condition'=>[
                    'stack_source'=>['single', 'repeater'],
                ]
			]
		);

        $this->add_control(
			'content_link_control_cus',
			[
				'label' => esc_html__( 'Link', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' => 'None',
                    'media' => 'Media File',
					'custom' => 'Custom URL',
				],
				'default' => 'none',
                'condition' => [
                    'stack_source'=>'single'
                ]
			]
		);

        $this->add_control(
			'content_custom_link_cus',
			[
				'label' => esc_html__( 'Link', 'wts-eae' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( 'https://your-link.com', 'wts-eae' ),
				'condition' => [
					'content_link_control_cus' => 'custom',
                    'stack_source'=>'single',
				],
				'show_label' => false,
			]
		);

        $this->add_control(
			'content_lightbox_control_cus',
			[
				'label' => esc_html__( 'Lightbox', 'wts-eae' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
                'condition' => [
                    'content_link_control_cus' =>'media',
                    'stack_source'=>'single'    
                ]
			]
		);


        $this->add_control(
			'tooltip_sin',
			[
                'label' => esc_html__( 'Tooltip', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
                'separator'=>'before',
				'options' => [
					'none' => 'None',
                    'title' => 'Title',
					'caption' => 'Caption',
					'custom' => 'Custom',
				],
				'default' => 'none',
                'condition'=>[
                    'stack_source'=>'single',
                ],
			]
		); 

        $this->add_control(
			'tooltip_cus',
			[
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'label' => __( 'Tooltip', 'wta-eae' ),
				'placeholder' => __( 'Type title here', 'wta-eae' ),
               
				'dynamic' => [
					'active' => true,
                ],
                'condition'=>[
                    'stack_source'=>'single',
                    'tooltip_sin'=>'custom'
                ]
			]
		);

		$this->add_control(
			'tooltip_position_sin',
			[
				'label' => __( 'Tooltip Position', 'wta-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'  => [
						'title' => __( 'Left', 'wta-eae' ),
						'icon' => 'eicon-h-align-left'
					],
					'up'  => [
						'title' => __( 'Up', 'wta-eae' ),
						'icon' => 'eicon-v-align-top'
					],
					'down'  => [
						'title' => __( 'Down', 'wta-eae' ),
						'icon' => 'eicon-v-align-bottom'
					],
					'right'  => [
						'title' => __( 'Right', 'wta-eae' ),
						'icon' => 'eicon-h-align-right'
					],
				],
                'condition'=>[
                    'stack_source'=>'single',
                    'tooltip_sin!'=>'none'
                ],
                'default'=>'up',
				'toggle' => true,
			]
		); 


        $this->end_controls_section();

        $this->start_controls_section(
            'item-style',
            [
                'label' => esc_html__( 'Item', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'item_size',
            [
                'label' => __( 'Item Size', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 60,
                ],
                'selectors' => [
                    '{{WRAPPER}} .img-stack-item.eae-is-ct-image img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .img-stack-item.eae-is-ct-lottie-animation .eae-lottie-animation' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .img-stack-item.eae-is-ct-icon i' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .img-stack-item.eae-is-ct-text .img-stack-text' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        

        $this->add_responsive_control(
            'stack_spacing',
            [
                'label' => __( 'Spacing  (px)', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .img-stack-item:not(:first-child)' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'stack_Hover_spacing',
            [
                'label' => __( 'Hover Spacing  (px)', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-image-stack:hover .img-stack-item ' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'item_background',
                'exclude'=>[' image '],
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .img-stack-item.eae-is-ct-icon, {{WRAPPER}} .img-stack-item.eae-is-ct-text,{{WRAPPER}} .img-stack-item.eae-is-ct-lottie-animation,{{WRAPPER}} .img-stack-item.eae-is-ct-image .eae-img-stack',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'img_stk_border',
                'selector' => '{{WRAPPER}} .img-stack-item.eae-is-ct-image img,{{WRAPPER}} .img-stack-item.eae-is-ct-lottie-animation,{{WRAPPER}} .img-stack-item.eae-is-ct-text,{{WRAPPER}} .img-stack-item.eae-is-ct-icon',
                'name' => 'border_image',
                'fields_options' => [
                    'border' => [
                        'default' => 'solid',
                    ],
                    'width'  => [
                        'default' => [
                            'top'    => 2,
                            'right'  => 2,
                            'bottom' => 2,
                            'left'   => 2,
                        ],
                    ],
                    'color'  => [
                        'default' => '#FFFFFF',
                    ],
                ],
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => __( 'Border Radius', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .img-stack-item.eae-is-ct-image img,{{WRAPPER}} .img-stack-item.eae-is-ct-lottie-animation,{{WRAPPER}} .img-stack-item.eae-is-ct-text,{{WRAPPER}} .img-stack-item.eae-is-ct-icon, {{WRAPPER}} .img-stack-item.eae-is-ct-icon i'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .img-stack-item.eae-is-ct-lottie-animation .eae-lottie-animation' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .img-stack-item.eae-is-ct-image img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .img-stack-item.eae-is-ct-image img,{{WRAPPER}} .img-stack-item.eae-is-ct-icon,{{WRAPPER}} .img-stack-item.eae-is-ct-lottie-animation,{{WRAPPER}} .img-stack-item.eae-is-ct-text',  
            ]
        );

        $this->add_control(
            'text_heading',
            [
                'label'     => __( 'Text', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'condition'=>[
                    'stack_source'=>'repeater',
                ]
                
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'text_typo',
                'selector' =>  '{{WRAPPER}} .img-stack-item.eae-is-ct-text .img-stack-text',
                'condition'=>[
                    'stack_source'=>'repeater',
                ]
                
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'tooltip-style',
            [
                'label' => esc_html__( 'Tooltip', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'tool_width',
            [
                'label' => esc_html__( 'Width', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .img-stack-item::after' => 'min-width: {{SIZE}}{{UNIT}};',
                    
                ]
            ]
        );
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'tool_typo',
                'selector' => '{{WRAPPER}} .img-stack-item::after',
            ]
        );

        $this->add_control(
            'tool_align',
            [
                'label'       => esc_html__( 'Text Alignment', 'wts-eae' ),
                'type'        => Controls_Manager::CHOOSE,
                'default'     => 'center',
                'toggle' => false,
                'label_block' => false,
                'options'     => [
                    'left' => [
                        'title' => __( 'Left', 'wts-eae' ),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'     => [
                        'title' => __( 'Center', 'wts-eae' ),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => __( 'Right', 'wts-eae' ),
                        'icon'  => 'eicon-text-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .img-stack-item::after' => 'text-align:{{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'tool_color',
            [
                'label' => esc_html__( 'Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .img-stack-item::after' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                    
                ],
            ]
        );

        $this->add_control(
            'tool_arrow_color',
            [
                'label' => esc_html__( 'Arrow Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    // '{{WRAPPER}} .img-stack-item::before' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                    '{{WRAPPER}} .img-stack-item[tooltip][dir^=up]:hover::before' => 'border-top-color:  {{VALUE}}',
                    '{{WRAPPER}} .img-stack-item[tooltip][dir^=down]:hover::before' => 'border-bottom-color:  {{VALUE}}',
                    '{{WRAPPER}} .img-stack-item[tooltip][dir^=left]:hover::before' => 'border-left-color:  {{VALUE}}',
                    '{{WRAPPER}} .img-stack-item[tooltip][dir^=right]:hover::before' => 'border-right-color:  {{VALUE}}',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'tool_background',               
                'types' => [ 'classic', 'gradient'],
                'exclude'=>[' image '],
                'selector' => '{{WRAPPER}} .img-stack-item::after',
            ]
        );

        $this->add_responsive_control(
            'tool_padding',
            [
                'label' => esc_html__('Padding','wts-eae'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem'],
                'selectors' => [
                    '{{WRAPPER}} .img-stack-item::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $this->add_responsive_control(
            'tool_border_radius',
            [
                'label' => esc_html__( 'Border Radius', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'default' => [
                    'top' => '3',
                    'right' => '3',
                    'bottom' => '3',
                    'left' => '3',
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .img-stack-item::after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        apply_filters('eae_image_stack_add_pro_controls', $this);    
}

    public function render(){
    
    $settings = $this->get_settings_for_display();
    //echo '<pre>';  print_r($settings); echo '</pre>';
        ?>
        <div class="eae-image-stack"><?php
            $data= $this->prepareData($settings);
            if(empty($data)){
                return;
            }
            if(!EAE::$is_pro){
                foreach($data as $key => $item){
                    $stackClasses =[];
                    $stackClasses[]= 'img-stack-item';
                    $stackClasses[]= 'eae-is-ct-'.$item['type'];
                    if($settings['stack_source'] !== 'repeater'){
                        $this->set_render_attribute('stackClasses-'.$key,'class',$stackClasses);
                        if(isset($settings['tooltip_sin']) && $settings['tooltip_sin'] !=  'none'){
                            $toolText='';
                            // echo "<pre>"; print_r($settings); echo "</pre>";
                            $toolText = $this->get_caption( $settings, $item);
                            if(!empty($toolText)){
                                $this->add_render_attribute('stackClasses-'.$key,'tooltip', $toolText);
                                $this->set_render_attribute('stackClasses-'.$key,'dir', $settings['tooltip_position_sin']);
                            }
                            
                        } 
                    }
    
                    if(empty($item['imgId']) ){
                        break;
                    }
                    $attr = [
                        'class' => 'eae-img-stack'
                    ];
    
                    if( isset($item['link_control']) && $item['link_control'] != ''){
                        $link = $this->get_link( $item); 
                        if ( $link ) {                   
                            $this->add_link_attributes( 'link_'.$key, $link );
                            $this->add_render_attribute( 'link_'.$key,[
                                'class' => 'elementor-clickable'.$key,
                            ]);
                            if ( $item['link_control'] != 'custom' ) {
                                $this->add_lightbox_data_attributes( "link_".$key, $item['imgId'] );
                            }
                        } 
                    } ?>
                    <span <?php echo $this->get_render_attribute_string( 'stackClasses-'.$key ); ?> >
                        <?php
                            if(isset($item['link_control']) && $item['link_control'] != ''){?> 
                                <a <?php $this->print_render_attribute_string( "link_".$key ); ?>> <?php
                            }
                            if(!empty($item['imgId'])){
                                echo  wp_get_attachment_image($item['imgId'], $settings['img_size'],false,$attr);
                            }
                            if(isset($item['link_control']) && $item['link_control'] != ''){?> 
                                </a> <?php
                            }?>
                    </span>
                <?php } 
            } else{
                do_action('eae_image_stack_render_pro', $this,$settings,$data);
            }?>
        </div>
        <?php
    }
    public function get_caption( $settings , $item ) {
        $tooltip_type = '';
        if($settings['stack_source'] !== 'repeater'){
            $tooltip_type = $settings['tooltip_sin'];
        }else{
            $tooltip_type = $item['tooltip'];
        }   
         
        $caption = '';        
        switch ( $tooltip_type ) {
            case 'caption':
                $caption = wp_get_attachment_caption( $item['imgId']);
                break;
            case 'title':
                $caption = get_the_title( $item['imgId'] );
                break;
            case 'custom':
                $caption = $settings['tooltip_cus'];
                break;
        }
        return $caption;
    }

    public function get_link( $item ) {

       
		if ( '' === $item['link_control'] ) {
			return false;
		}
		if ( 'custom' === $item['link_control'] ) {
            if ( empty( $item['custom_link']['url']) ) {
				return false;
			}
			return $item['custom_link'];
		}
        if ( 'media' === $item['link_control'] ) {
            return [
                'url' => $item['imgUrl'],
            ];
        }
	}

    public function prepareData($settings){ 
        $data = [];
        $type = 'image';
        if($settings['images'] && count($settings['images']) > 0){
            foreach( $settings['images'] as $key => $item){
                $data[$key]['type'] =  $type;
                $data[$key]['_id'] =   $item['_id'] ?? '';
                if($settings['content_link_control_cus'] != 'none'){
                    $data[$key]['imgUrl'] = $item['url'];
                    $data[$key]['custom_link'] = $settings['content_custom_link_cus'];
                    $data[$key]['link_control'] = $settings['content_link_control_cus']; 
                }
                $data[$key]['imgId']  = $item['id'] ?? '';
            }
        }
        
        $data = apply_filters('eae_image_stack_data', $data, $settings);
        //echo '<pre>';  print_r($data); echo '</pre>';
        return $data;
    }

}
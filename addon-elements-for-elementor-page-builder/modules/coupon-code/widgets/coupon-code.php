<?php
namespace WTS_EAE\Modules\CouponCode\Widgets;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Text_Shadow;
use WTS_EAE\Base\EAE_Widget_Base;
use Elementor\Controls_Manager;
use WTS_EAE\Classes\Helper;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use WC_Coupon;
use Elementor\Group_Control_Box_Shadow;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
class CouponCode extends EAE_Widget_Base {

	public function get_name() {
		return 'eae-coupon-code';
	}

	public function get_title() {
		return __( 'Coupon Code', 'wts-eae' );
	}

	public function get_icon() {
		return 'eae-icon eae-coupon-code';
	}

	public function get_categories() {
		return [ 'wts-eae' ];
	}

	public function get_keywords() {
		return [ 'coupon' ,'code','coupon-code'];
	}

    public function get_script_depends() {
		return [ 'eae-lottie','eae-peel', 'wts-magnific' ];
	}


     

    protected function register_controls(){
        
        $coupon_list = [
            '' => esc_html__( 'Select', 'wts-eae' ),
        ];

        if(class_exists('WooCommerce')){
            $args = array(
                'posts_per_page'   => -1,
                'orderby'          => 'title',
                'order'            => 'asc',
                'post_type'        => 'shop_coupon',
                'post_status'      => 'publish',
            );
            $coupons = get_posts( $args );
            foreach($coupons as $key => $value){
                $coupon_list[$value->ID] = $value->post_title;
            };
        }

        $this->start_controls_section(
			'eae_coupon_code',
			[
				'label' => __( 'Content', 'wts-eae' ),
			]
		);

        $this->add_control(
			'source',
			[
				'label' => esc_html__( 'Source', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'static' => 'Static', 
					'dynamic' =>  'WooCommerce',
				],
				'default' => 'static',
                'frontend_available' => true
			]
		);


        $this->add_control(
            'sta_coupon_code',
            [
                'label'			 => esc_html__( 'Coupon Code', 'wts-eae' ),
                'type'			 => Controls_Manager::TEXT,
                'dynamic'		 => [
                    'active' => true,
                ],
                'default'		 => esc_html__( 'AAA-9867-123', 'wta-eae' ),
                'condition' => [
                    'source' => 'static',
                ],
            ]
        );
         if(class_exists('WooCommerce')){
            $this->add_control(
                'dynamic_coupon',
                [
                    'label' => esc_html__( 'Coupon Type', 'wts-eae' ),
                    'type' => Controls_Manager::SELECT,
                    'options' => $coupon_list,
                    'condition' => [
                        'source' => 'dynamic'
                    ],        
                    'frontend_available' => true    
                ]
            );
         }    

        // ! ------- ---------------


        $this->add_control(
			'coupon_type',
			[
				'label' => esc_html__( 'Layout', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'standard' => 'Standard', 
					//'peel' =>  'Peel',
					'scratch' =>  'Scratch',
					'slide' =>  'Slide',
				],
				'default' => 'standard',
                'frontend_available' => true
			]
		);

        $this->add_control(
			'sta_layout',
			[
				'label' => esc_html__( 'Trigger Button', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'pop' => 'Pop Up', 
					'click' =>  'Click',
				],
                'condition' => [
                    'coupon_type' => 'standard',
                ],
				'default' => 'click',
                'frontend_available' => true
			]
		);
        
        $this->add_control(
			'sta_title',
			[
				'label'			 => esc_html__( 'Button Text', 'wts-eae' ),
				'type'			 => Controls_Manager::TEXT,
				'dynamic'		 => [
					'active' => true,
				],
				'default'		 => esc_html__( 'Coupon Code', 'wta-eae' ),
                'condition' => [
                    'coupon_type'=> 'standard'
                ],
                'frontend_available' => true
			]
		);

        $this->add_control(
            'after_copy_button',
            [
                'label'			 => esc_html__( 'After Copy', 'wts-eae' ),
                'type'			 => Controls_Manager::TEXT,
                'dynamic'		 => [
                    'active' => true,
                ],
                'default'		 => esc_html__( 'Copied!', 'wta-eae' ),
                'condition' => [
                    'coupon_type'=> 'standard'
                ],
                'frontend_available' => true
            ]
        );


        Helper::eae_media_controls(
            $this,
            [
                'name'          => 'sta_title_icon',
                'label'         => __( 'Icon', 'wts-eae' ),
                'icon'			=> true,
                'image'			=> true,
                'lottie'		=> true,
                'frontend_available' => true,
                'conditions'     => [
					[
						'key'   => 'coupon_type',
						'value' => 'standard',
					],
				]
            ]
        );

        $this->add_control(
			'sta_icon_pos',
			[
				'label' => esc_html__( 'Position', 'wta-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'row-reverse' => 'Before',
					'row' => 'After',
				],
                'condition' => [
                    'coupon_type'=> 'standard'
                ],
				'default' => 'row',
                'selectors' => [
					'{{WRAPPER}} .eae-cc-button' => 'flex-direction:{{VALUE}}',						
					'{{WRAPPER}} .eae-coupon-popup-link' => 'flex-direction:{{VALUE}}',						
				],
			]
		);

        $this->add_control(
			'preview_modal',
			[
				'label'        => __( 'Preview Modal', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'yes',
                'frontend_available' => true,
                'description'  => __('While woriking with "Popup, Slide, Scratch" it will enable back side preview which helps you to stlye your content easily','wts-eae'),
                'conditions'=>[
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'coupon_type',
                            'operator' =>'!==',
                            'value' => 'standard'
                         ],
                         [
                             'relation' => 'and',
                             'terms' => [
                                [
                                    'name' => 'coupon_type',
                                    'operator' => '===',
                                    'value' => 'standard',
                                ],
                                [
                                    'name' => 'sta_layout',
                                    'operator' => '===',
                                    'value' => 'pop',
                                ],
                             ],
                             
                         ],
                    ]
                ],
			]
		);

        $this->add_control(
            'sta_redirect_link', 
            [
           'label'			 => esc_html__( 'Redirect Link', 'wta-eae' ),
           'type'			 => Controls_Manager::URL,
           'dynamic'		 => [
               'active' => true,
           ],
           'label_block' => true,
           'placeholder' => esc_html__( 'Paste URL or type', 'wta-eae' ),
           'autocomplete' => false,
           'options' => [ 'is_external', 'nofollow', 'custom_attributes' ],
           'condition' => [
                'sta_layout'=> 'click',
                'coupon_type'=> 'standard'
            ],
            'frontend_available' => true
        ]
        );

        $this->add_control(
            'sta_exp',
            [
                'label' => esc_html__( 'Show Expire Date', 'wts-eae' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'condition' => [
                    'source' => 'static',
                ],
            ]
        );


       $this->add_control(
        'sta_expire_date',
        [
            'label'			 => esc_html__( 'Expire Date', 'wts-eae' ),
            'type'			 => Controls_Manager::DATE_TIME,
            'dynamic'		 => [
                'active' => true,
            ],
            'picker_options' => [
               'enableTime' => false,
               'dateFormate' => 'Y-m-d',
            ],

            'default'		 => esc_html__( date('Y-m-d', strtotime("+30 days")), 'wta-eae' ),
            'condition' => [
                'source' => 'static',
                'sta_exp' => 'yes'
            ],
            'frontend_available' => true
        ]
    );

       $this->add_control(
        'sta_exp_dt_msg',
        [
            'label'			 => esc_html__( 'Expire Message', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXTAREA,
            'dynamic'		 => [
                'active' => true,
            ],
            'default'		 => esc_html__( 'Expire Date : %% ExpireDate %%', 'wta-eae' ),
            'description'	 => esc_html__( 'To add expiry date between message use \'ExpireDate\' between %%_%%', 'wts-eae' ),
            'condition' => [
                // 'source' => 'static',
                // 'coupon_type'=> 'standard',
                'sta_exp' => 'yes'
            ],
            'frontend_available' => true
        ]
    );

    $this->add_control(
        'sta_speed',
        [
            'label'       => __( 'Timing (ms)', 'wts-eae' ),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 1000,
            // 'description' => __( 'Duration of transition between slides (in ms)', 'wts-eae' ),
            'range'       => [
                'px' => [
                    'min'  => 500,
                    'max'  => 10000,
                    'step' => 500,
                ],
            ],
            'frontend_available' => true,
            'condition' => [
                'coupon_type'=> 'standard'
            ],
        ]
    );

    $this->add_control(
        'sta_show_code',
        [
            'label' => esc_html__( 'Show Code', 'wts-eae' ),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => [
                'sta_layout'=> 'click',
                'coupon_type'=> 'standard'
            ],
        ]
    );

    $this->add_control(
        'sta_pop_up',
        [
            'label'     => __( 'Pop Up', 'wts-eae' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'sta_layout'=> 'pop',
                'coupon_type'=> 'standard'
            ],
        ]
    );

    $this->add_control(
        'pop_title',
        [
            'label'			 => esc_html__( 'Title', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXT,
            'dynamic'		 => [
                'active' => true,
            ],
            'label_block'	 => true,
            'default'		 => esc_html__( 'Thank you we have a special gift for you ', 'wta-eae' ),
            'condition' => [
                'sta_layout'=> 'pop',
                'coupon_type'=> 'standard'
            ],
        ]
    );

    $this->add_control(
        'pop_des',
        [
            'label'			 => esc_html__( 'Description', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXT,
            'dynamic'		 => [
                'active' => true,
            ],
            'label_block'	 => true,
            'default'		 => esc_html__( 'Here is your coupon code...', 'wta-eae' ),
            'condition' => [
                'source' => 'static',
                'sta_layout'=> 'pop',
                'coupon_type'=> 'standard'
            ],
        ]
    );

    $this->add_control(
        'pop_visit_button',
        [
            'label'			 => esc_html__( 'Visit Button', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXT,
            'dynamic'		 => [
                'active' => true,
            ],
            'default'		 => esc_html__( 'Shop Now', 'wta-eae' ),
            'condition' => [
                'sta_layout'=> 'pop',
                'coupon_type'=> 'standard'
            ],
        ]
    );

    $this->add_control(
        'pop_visit_link', 
        [
       'label'			 => esc_html__( 'Visit Link', 'wta-eae' ),
       'type'			 => Controls_Manager::URL,
       'dynamic'		 => [
           'active' => true,
       ],
       'label_block' => true,
       'placeholder' => esc_html__( 'Paste URL or type', 'wta-eae' ),
       'autocomplete' => false,
       'options' => [ 'is_external', 'nofollow', 'custom_attributes' ],
       'condition' => [
            'sta_layout'=> 'pop',
            'coupon_type'=> 'standard'
        ],
    ]
    );

    $this->add_control(
        'pop_icon',
        [
            'label' => esc_html__( 'Close Icon', 'wts-eae' ),
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'icon',
            'default' => [
                'value' => 'fas fa-times',
                'library' => 'fa-solid',
            ],
            'condition' => [
                'sta_layout'=> 'pop',
                'coupon_type'=> 'standard'
            ],
            'frontend_available' => true
        ]
    );

    $this->add_responsive_control(
        'Peel_sc_height',
        [
            'label' => esc_html__( 'Height', 'wts-eae' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 200,
                    'max' => 1000,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 300,
            ],
            'selectors' => [
                '{{WRAPPER}} .eae-peel-wrapper' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .eae-coupon-slide' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .eae-scratch-container' => 'height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .eae-coupon-canvas' => 'height: {{SIZE}}{{UNIT}};',
            ],
            'frontend_available' => true,
            'condition' => [
                'coupon_type'=> ['peel','scratch','slide']
            ],
        ]
    );

    $this->add_responsive_control(
        'Peel_scratch_width',
        [
            'label' => esc_html__( 'Width', 'wts-eae' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'px', '%' ],
            'range' => [
                'px' => [
                    'min' => 200,
                    'max' => 1000,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 500,
            ],
            'frontend_available' => true,
            'selectors' => [
                '{{WRAPPER}} .eae-coupon-slide' => 'width: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .eae-scratch-container' => 'width: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .eae-coupon-canvas' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'coupon_type'=> ['peel','scratch','slide']
            ],
        ]
    );

    $this->add_control(
        'peel_speed',
        [
            'label'       => __( 'Timing (ms)', 'wts-eae' ),
            'type'        => Controls_Manager::NUMBER,
            'default'     => 2000,
            'range'       => [
                'px' => [
                    'min'  => 500,
                    'max'  => 10000,
                    'step' => 500,
                ],
            ],
            'frontend_available' => true,
            'condition' => [
                'coupon_type!'=> 'standard'
            ],
        ]
    );

    $this->add_control(
        'peel_front_slide',
        [
            'label'     => __( 'Front Slide', 'wts-eae' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'coupon_type'=> ['peel','slide']
            ],
        ]
    );

    $this->add_control(
        'peel_front_title',
        [
            'label'			 => esc_html__( 'Title', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXT,
            'dynamic'		 => [
                'active' => true,
            ],
            'label_block'	 => true,
            'default'		 => esc_html__( 'Slide to discover coupon', 'wta-eae' ),
            'condition' => [
                'coupon_type'=> ['peel','slide']
            ],
        ]
    );

    $this->add_control(
        'peel_front_des',
        [
            'label'			 => esc_html__( 'Description', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXT,
            'dynamic'		 => [
                'active' => true,
            ],
            'label_block'	 => true,
            'default'		 => esc_html__( 'Redeem code now', 'wta-eae' ),
            'condition' => [
                'coupon_type'=> ['peel','slide'],
                'source'=> 'static'
            ],
        ]
    );

   
    $this->add_control(
        'peel_back_slide',
        [
            'label'     => __( 'Back Slide', 'wts-eae' ),
            'type'      => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'coupon_type'=> ['peel','scratch','slide'],
            ],
        ]
    );


    $this->add_control(
        'peel_title',
        [
            'label'			 => esc_html__( 'Title', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXT,
            'dynamic'		 => [
                'active' => true,
            ],
            'label_block'	 => true,
            'default'		 => esc_html__( 'Thank you! we have a special gift for you ', 'wta-eae' ),
            'condition' => [
                'coupon_type'=> ['peel','scratch','slide'],
            ],
        ]
    );

    $this->add_control(
        'peel_des',
        [
            'label'			 => esc_html__( 'Description', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXT,
            'dynamic'		 => [
                'active' => true,
            ],
            'label_block'	 => true,
            'default'		 => esc_html__( 'Here is your coupon code', 'wta-eae' ),
            'condition' => [
                'coupon_type'=> ['peel','scratch','slide'],
            ],
        ]
    );

  


    $this->add_control(
        'peel_copy_button',
        [
            'label'			 => esc_html__( 'Copy Button', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXT,
            'dynamic'		 => [
                'active' => true,
            ],
            'default'		 => esc_html__( 'Coupon Code', 'wta-eae' ),
            'condition' => [
                'coupon_type'=> ['peel','scratch','slide'],
            ],
        ]
    );

    $this->add_control(
        'peel_after_copy_button',
        [
            'label'			 => esc_html__( 'After Copy button', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXT,
            'dynamic'		 => [
                'active' => true,
            ],
            'default'		 => esc_html__( 'Copied!', 'wta-eae' ),
            'frontend_available' => true,
            'condition' => [
                'coupon_type'=> ['peel','scratch','slide'],
            ],
        ]
    );

    $this->add_control(
        'peel_visit_button',
        [
            'label'			 => esc_html__( 'Visit Button', 'wts-eae' ),
            'type'			 => Controls_Manager::TEXT,
            'dynamic'		 => [
                'active' => true,
            ],
            'default'		 => esc_html__( 'Shop Now', 'wta-eae' ),
            'condition' => [
                'coupon_type'=> ['peel','scratch','slide'],
            ],
        ]
    );

    $this->add_control(
        'peel_visit_link', 
        [
       'label'			 => esc_html__( 'Visit Link', 'wta-eae' ),
       'type'			 => Controls_Manager::URL,
       'dynamic'		 => [
           'active' => true,
       ],
       'label_block' => true,
       'placeholder' => esc_html__( 'Paste URL or type', 'wta-eae' ),
       'autocomplete' => false,
       'options' => [ 'is_external', 'nofollow', 'custom_attributes' ],
       'condition' => [
        'coupon_type'=> ['peel','scratch','slide'],
        ],
    ]

    );

        $this->end_controls_section();

        $this->start_controls_section(
			'order_',
			[
				'label' => __( 'Order', 'wts-eae' ),
                'conditions'=>[
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'coupon_type',
                            'operator' =>'!==',
                            'value' => 'standard'
                         ],
                         [
                             'relation' => 'and',
                             'terms' => [
                                [
                                    'name' => 'coupon_type',
                                    'operator' => '===',
                                    'value' => 'standard',
                                ],
                                [
                                    'name' => 'sta_layout',
                                    'operator' => '===',
                                    'value' => 'pop',
                                ],
                             ],
                             
                         ],
                    ]
                ],
			]
		);

        $repeater = new Repeater();

        $this->add_control(
            'cc_order',
            [
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'cc_heading' => esc_html__('Title','wts-eae'),
                    ],
                    [
                        'cc_heading' => esc_html__('Description','wts-eae'),
                    ],
                    [
                        'cc_heading' => esc_html__('Coupon','wts-eae'),
                    ],
                    [
                        'cc_heading' => esc_html__('Expire Date','wts-eae'),
                    ],
                    [
                        'cc_heading' => esc_html__('Visit Button','wts-eae'),
                    ],
                ],
                'item_actions' => [
                    'add' => false,
                    'duplicate' => false,
                    'remove' => false,
                    'sort' => true,
                ],
                
                'title_field' => '{{{cc_heading}}} ',
            ]
        );

        $this->end_controls_section();

        
        $this->start_controls_section(
            'popup_trigger_btn_style',
            [
                'label' => esc_html__( 'Trigger Button', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'sta_layout'=>'pop',
                    'coupon_type'=>'standard',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_trig',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                ],
                'selector' => '{{WRAPPER}} .eae-coupon-popup-link',
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
            ]
        );
    
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow_trig',
                'selector' => '{{WRAPPER}} .eae-coupon-popup-link',
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style_trig', [
            'condition'=>[
                'sta_layout'=>'pop',
            ]   
        ] );

        $this->start_controls_tab(
            'tab_btn_normal_trig',
            [
                'label' => esc_html__( 'Normal', 'wts-eae' ),
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
            ]
        );

        $this->add_control(
            'btn_color_trig',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eae-coupon-popup-link' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_trig',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-coupon-popup-link',
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
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_btn_trig_h',
            [
                'label' => esc_html__( 'Hover', 'wts-eae' ),
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
                
            ]
        );

        $this->add_control(
            'hover_color_trig',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-coupon-popup-link:hover ' => 'color: {{VALUE}};'
                ],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'btn_hover_trig',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-coupon-popup-link:hover',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
            ]
        );

        $this->add_control(
            'btn_br_trig',
            [
                'label' => esc_html__( 'Border Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-coupon-popup-link' => 'border-color: {{VALUE}};',
                ],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
            ]
        );

        
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
			'trig_min_width',
			[
				'label'     => __( 'Width', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
                'separator' => 'before',
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
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
				'selectors' => [
					'{{WRAPPER}} .eae-coupon-popup-link ' => 'min-width: {{SIZE}}{{UNIT}};', 
				],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
			]
		);

        $this->add_responsive_control(
            'pop_gap',
            [
                'label' => esc_html__( 'Gap', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-coupon-popup-link' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_trig',
                'selector' => '{{WRAPPER}} .eae-coupon-popup-link',
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
                
            ]
        );

        $this->add_responsive_control(
            'br_trig',
            [
                'label' => esc_html__( 'Border Radius', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .eae-coupon-popup-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
                
            ]
        );

        $this->add_responsive_control(
            'padding_trig',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .eae-coupon-popup-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
            ]
        );

        $this->add_control(
            'trig_icon',
            [
                'label'     => __( 'Icon', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        Helper::global_icon_style_controls(
            $this,
            [
                'name' => 'icon_trig',
                'selector' => '.eae-cc-icon',
                'is_repeater' => false,
                'hover_selector'      => '.eae-cc-icon:hover',
                'is_repeater'   => false, 
                'is_parent_hover' => true,
            ]
        );
        

        $this->end_controls_section();

        $this->start_controls_section(
            'pop_style',
            [
                'label' => esc_html__( 'Pop Up ', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'coupon_type'=>'standard',
                    'sta_layout'=>'pop',

                ]
            ]
        );

        $this->add_responsive_control(
			'pop_box_width',
			[
				'label'     => __( 'Width', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
                'range'     => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'.eae-cc-{{ID}}.eae-popup .mfp-inline-holder .mfp-content' => 'width: {{SIZE}}%;', 
				],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]  
			]
		);

		$this->add_responsive_control(
			'pop_box_height',
			[
				'label'      => __( 'Height', 'wts-eae' ),
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
					'.eae-cc-{{ID}} .white-popup' => 'height: {{SIZE}}{{UNIT}};',

				],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]  
			]
		);

        $this->add_control(
			'effect',
			[
				'label'		=>	__('Effect', 'wts-eae'),
				'type'		=>	Controls_Manager::SELECT,
				'options'	=>	[
					''					=>  __('Default', 'wts-eae'),
					'zoom-in'			=>	__('Zoom In', 'wts-eae'),
					'move-horizontal'	=>	__('Move Horizontal In', 'wts-eae'),
					'newspaper'			=>	__('Newspaper', 'wts-eae'),
					'move-from-top'		=>	__('Move From Top', 'wts-eae'),
					'3d-unfold'			=>	__('3d-Unfold', 'wts-eae'),
					'zoom-out'			=>	__('Zoom Out', 'wts-eae')
				],
                'frontend_available' => true,
				'default'	=>	'',
                'condition'=>[
                    'sta_layout'=>'pop',
                ]      
                
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => __( 'Overlay Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0,0,0,0.75)',
				'selectors' => [
					'body .eae-coupon-popup.mfp-bg.eae-cc-{{ID}}' => 'background-color: {{VALUE}};',
				],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]      
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'pop_box_background',
				'types' => [ 'classic', 'gradient' ,'image' ],  
				'selector' => '.eae-cc-{{ID}} .white-popup',
                'condition'=>[
                    'sta_layout'=>'pop',
                ] 
			]
		);

        $this->add_responsive_control(
			'pop_box_br',
			[
				'label' => esc_html__('Border Radius','wts-eae'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px','%'],
				'selectors' => [
					'.eae-cc-{{ID}} .mfp-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'=>[
                    'sta_layout'=>'pop',
                ] 
			]
		);


        $this->add_control(
            'pop_close_btn',
            [
                'label'     => __( 'Close Button', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
                    'sta_layout'=>'pop',
                ]                
            ]
        );

        $this->add_control(
			'btn_in_out',
			[
				'label'   => __( 'Button Inside', 'wts-eae' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
                'frontend_available' => true,
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   

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
					'.eae-cc-{{ID}} .eae-close'    => 'font-size: {{SIZE}}px;height: {{SIZE}}px;width: {{SIZE}}px;',
					'.eae-cc-{{ID}} svg.eae-close' => 'width: {{SIZE}}px;height: {{SIZE}}px;width: {{SIZE}}px;',
				],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
			]
		);

		$this->add_control(
			'close_btn_color',
			[
				'label'     => __( 'Color', 'wts-eae' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'.eae-cc-{{ID}}.eae-coupon-popup.eae-close-btn-in .eae-close' => 'color: {{VALUE}};',
                    '.eae-cc-{{ID}} .eae-coupon-popup.eae-close-btn-in svg' => 'background-color: {{VALUE}};',
				],
                'condition'=>[
                    'sta_layout'=>'pop',
                ]   
			]
		);
		$this->add_responsive_control(
			'cls_btn_position_top_in',
			[
				'label'      => __( 'Top', 'wts-eae' ),
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
					'.eae-cc-{{ID}} .eae-close' => 'top:{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'btn_in_out' => 'yes',
                    'sta_layout'=>'pop',
				],
               
			]
		);
		$this->add_responsive_control(
			'cls_btn_position_right_in',
			[
				'label'      => __( 'Right', 'wts-eae' ),
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
					'.eae-cc-{{ID}} .eae-close' => 'right:{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'btn_in_out' => 'yes',
                    'sta_layout'=>'pop',
				],
               
			]
		);
		$this->add_responsive_control(
			'cls_btn_position_top_out',
			[
				'label'      => __( 'Top', 'wts-eae' ),
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
					'.eae-cc-{{ID}} .eae-close' => 'top:{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'btn_in_out!' => 'yes',
                    'sta_layout'=>'pop',
				],
			]
		);
		$this->add_responsive_control(
			'cls_btn_position_right_out',
			[
				'label'      => __( 'Right', 'wts-eae' ),
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
					'.eae-cc-{{ID}} .eae-close' => 'right:{{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'btn_in_out!' => 'yes',
                    'sta_layout'=>'pop',
				],
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'Coupon_style',
            [
                'label' => esc_html__( 'Coupon', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'coupon_code_heading',
            [
                'label'     => __( 'Coupon', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_stn_code',
				'selector' => '{{WRAPPER}} .eae-coupon-wrapper .eae-code , .eae-cc-{{ID}} .eae-code',
			]
		);


        $this->add_control(
            'stn_cd_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-coupon-wrapper .eae-code, .eae-cc-{{ID}} .eae-code' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],       
            ]
        );

        $this->add_control(
            'stn_cd_color_h',
            [
                'label' => esc_html__( 'Text Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-coupon-wrapper .eae-code:hover , .eae-cc-{{ID}} .eae-code:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],       
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'stn_cd_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .eae-coupon-wrapper .eae-code , .eae-cc-{{ID}} .eae-code ',
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'stn_cd_border',
                'selector' => '{{WRAPPER}} .eae-coupon-wrapper .eae-code , .eae-cc-{{ID}} .eae-code',
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label' => esc_html__( 'Gap', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-coupon-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                    '.eae-cc-{{ID}} .eae-coupon-wrapper' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                
            ]
        );


        $this->add_control(
            'cp_btn_hd_pop',
            [
                'label'     => __( 'Button', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_pop',
                'selector' => '.eae-cc-{{ID}} .eae-cc-button , {{WRAPPER}} .eae-cc-button',
            ]
        );
    
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'text_shadow_pop',
                'selector' => '.eae-cc-{{ID}} .eae-cc-button , {{WRAPPER}} .eae-cc-button',
               
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style_pop', [
            
    
        ] );

        $this->start_controls_tab(
            'tab_button_normal_pop',
            [
                'label' => esc_html__( 'Normal', 'wts-eae' ),
            ]
        );

        $this->add_control(
            'btn_color_pop',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.eae-cc-{{ID}} .eae-cc-button ' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .eae-cc-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_pop',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '.eae-cc-{{ID}} .eae-cc-button , {{WRAPPER}} .eae-cc-button',               
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover_pop',
            [
                'label' => esc_html__( 'Hover', 'wts-eae' ),
               
            ]
        );

        $this->add_control(
            'hover_color_pop',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.eae-cc-{{ID}} .eae-cc-button:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eae-cc-button:hover' => 'color: {{VALUE}};',
                ],
              
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'btn_bck_pop_h',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '.eae-cc-{{ID}} .eae-cc-button:hover , {{WRAPPER}} .eae-cc-button:hover',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ],
               
            ]
        );

        $this->add_control(
            'hover_border_color',
            [
                'label' => esc_html__( 'Border Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '.eae-cc-{{ID}} .eae-cc-button:hover' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-cc-button:hover' => 'border-color: {{VALUE}};',
                ], 
            ]
        );

        $this->add_control(
            'btn_br_pop_h',
            [
                'label' => esc_html__( 'Border Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '.eae-cc-{{ID}} .eae-cc-button:hover ' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .eae-cc-button:hover ' => 'border-color: {{VALUE}};',
                ],
               
            ]
        );

        
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
			'pop_min_width',
			[
				'label'     => __( 'Width', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
                'separator' => 'before',
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
					'vh' => [
						'min' => 0,
						'max' => 100,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
					],
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
				],
               
                'size_units' => [ 'px', 'vh', 'vw' ],
				'selectors' => [
					'.eae-cc-{{ID}} .eae-cc-button' => 'min-width: {{SIZE}}{{UNIT}};', 
					'{{WRAPPER}} .eae-cc-button ' => 'min-width: {{SIZE}}{{UNIT}};', 
				],
			]
		);


        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_pop',
                'selector' => '.eae-cc-{{ID}} .eae-cc-button ,{{WRAPPER}} .eae-cc-button',
               
            ]
        );

        $this->add_responsive_control(
            'border_radius_pop',
            [
                'label' => esc_html__( 'Border Radius', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '.eae-cc-{{ID}} .eae-cc-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-cc-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
              
            ]
        );

        $this->add_responsive_control(
            'text_padding_pop',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '.eae-cc-{{ID}} .eae-cc-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};', 
                    '{{WRAPPER}} .eae-cc-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
              
            ]
        );

        $this->add_control(
            'sta_icon',
            [
                'label'     => __( 'Icon', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition' => [
                    'coupon_type' => 'standard',
                ],
            ]
        );

        Helper::global_icon_style_controls(
            $this,
            [
                'name' => 'icon_stan',
                'selector' => '.eae-cc-icon',
                'is_repeater' => false,
                'hover_selector'      => '.eae-cc-icon:hover',
                'is_repeater'   => false, 
                'is_parent_hover' => true,
                'conditions' => [
                    [
                        'key' => 'coupon_type',
                        'value' => 'standard',
                    ]
                ]
                
            ]
        );

        $this->add_responsive_control(
            'cli_gap',
            [
                'label' => esc_html__( 'Gap', 'wts-eae' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'rem' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-cc-button' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition'=>[
                    'sta_layout'=>'click',
                ]   
            ]
        );

        
        $this->end_controls_section();

        $this->start_controls_section(
            'front_style',
            [
                'label' => esc_html__( 'Front', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition'=>[
                    'coupon_type!'=>'standard',
                ]
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'front_background',
				'types' => [ 'classic', 'gradient','image' ],
				'selector' => '{{WRAPPER}} .eae-peel-wrapper .eae-top ,{{WRAPPER}} .eae-back ,{{WRAPPER}} .eae-slide-fr',
                'condition'=>[
                    'coupon_type!'=>['standard','scratch'],
                ]
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'item_bg',
				'label'    => __( 'Background', 'wts-eae' ),
				'types'    => [  'classic', 'gradient' , 'image' ],
                'render_type' => 'template',
				'selector' => '{{WRAPPER}} .ae-acf-repeater-inner',
				'fields_options' => [
					'image' => [
						'frontend_available' => true,
					],
					'color' => [
						'frontend_available' => true,
					],
					'color_b' => [
						'frontend_available' => true,
					],
					'background' => [
						'frontend_available' => true,
					],
					'color_stop' => [
						'frontend_available' => true,
					],
					'color_b_stop' => [
						'frontend_available' => true,
					],
					
				],
                'condition'=>[
                    'coupon_type'=>'scratch',
                ]
				
			]
		);

        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow',
                
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .eae-coupon-slide ,{{WRAPPER}} .eae-scratch-container',
			]
		);

        

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'fr_cd_border',
                'selector' => '{{WRAPPER}} .eae-coupon-slide, {{WRAPPER}} .eae-scratch-container',
            ]
        );

        $this->add_responsive_control(
			'alignment_position',
			[
				'label' => esc_html__( 'Align Items', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'wts-eae' ),
						'icon' => 'eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-justify-center-h',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'wts-eae' ),
						'icon' => 'eicon-justify-end-h',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-slide-fr' => 'align-items: {{VALUE}}',						
				],
                'condition'=>[
                    'coupon_type'=>'slide',
                ]
			]
		);
        $this->add_responsive_control(
			'position',
			[
				'label' => esc_html__( 'Justify Content', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'center',
				'options' => [
					'start' => [
						'title' => esc_html__( 'Top', 'wts-eae' ),
						'icon' => 'eicon-align-start-v',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'wts-eae' ),
						'icon' => 'eicon-align-stretch-v',
					],
					'end' => [
						'title' => esc_html__( 'Bottom', 'wts-eae' ),
						'icon' => 'eicon-align-end-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eae-slide-fr' => 'justify-content: {{VALUE}}',						
				],
                'condition'=>[
                    'coupon_type'=>'slide',
                ]
			]
		);

        $this->add_responsive_control(
                'text_padding_back',
                [
                    'label' => esc_html__( 'Padding', 'wts-eae' ),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                    'selectors' => [
                        '{{WRAPPER}} .eae-slide-fr' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'condition'=>[
                        'coupon_type'=>'slide',
                    ]
                ]
            );
		
        $this->add_control(
            'heading_overlay',
            [
                'label'     => __( 'Overlay', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'condition'=>[
                    'coupon_type'=>'slide',
                ]
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'overlay_background',
                'types' => [ 'classic', 'gradient' ],
                'exclude'=>['image'],
                'selector' => '{{WRAPPER}}  .eae-slide-fr::after',
                'condition'=>[
                    'coupon_type'=>'slide',
                ]
            ]
        );




        

        $this->add_control(
            'title_front',
            [
                'label'     => __( 'Title', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
                    'coupon_type!'=>'scratch',
                ]
            ]
        );


        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_fr_title',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .eae-fr-title ,{{WRAPPER}} .eae-slide-fr .eae-scratch-title ',
                'condition'=>[
                    'coupon_type!'=>'scratch',
                ]
			]
		);


        $this->add_control(
            'fr_title_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eae-fr-title ' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .eae-slide-fr .eae-scratch-title  ' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],       
                'condition'=>[
                    'coupon_type!'=>'scratch',
                ]
            ]
        );

        $this->add_control(
            'fr_title_hov_color',
            [
                'label' => esc_html__( 'Text Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eae-fr-title:hover ' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .eae-slide-fr .eae-scratch-title:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],     
                'condition'=>[
                    'coupon_type!'=>'scratch',
                ]  
            ]
        );


        $this->add_control(
            'des_front',
            [
                'label'     => __( 'Description', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                'condition'=>[
                    'coupon_type!'=>'scratch',
                ]
            ]
        );


        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_fr_des',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .eae-fr-des , {{WRAPPER}} .eae-slide-fr .eae-scratch-des',
                'condition'=>[
                    'coupon_type!'=>'scratch',
                ]
			]
		);


        $this->add_control(
            'fr_des_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eae-fr-des' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .eae-slide-fr .eae-scratch-des' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    
                ],       
                'condition'=>[
                    'coupon_type!'=>'scratch',
                ]
            ]
        );

        $this->add_control(
            'fr_des_hov_color',
            [
                'label' => esc_html__( 'Text Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .eae-fr-des:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '{{WRAPPER}} .eae-slide-fr .eae-scratch-des:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],    
                'condition'=>[
                    'coupon_type!'=>'scratch',
                ]   
            ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
            'back_style',
            [
                'label' => esc_html__( 'Back', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'conditions'=>[
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'coupon_type',
                            'operator' =>'!==',
                            'value' => 'standard'
                         ],
                         [
                             'relation' => 'and',
                             'terms' => [
                                [
                                    'name' => 'coupon_type',
                                    'operator' => '===',
                                    'value' => 'standard',
                                ],
                                [
                                    'name' => 'sta_layout',
                                    'operator' => '===',
                                    'value' => 'pop',
                                ],
                             ],
                             
                         ],
                    ]
                ],
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'wrap_background',
				'types' => [ 'classic', 'gradient' ,'image'],
				'selector' => '{{WRAPPER}} .eae-coupon-back ,{{WRAPPER}} .eae-back-wrapper',
                'conditions'=>[
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'coupon_type',
                            'operator' =>'!==',
                            'value' => 'standard'
                         ],
                    ]
                ],
			]
		);

        $this->add_responsive_control(
            'wrap_border_radius',
            [

                'label' => esc_html__( 'Border Radius', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'separator' => 'after',
                'selectors' => [
                    '{{WRAPPER}} .eae-coupon-back' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .eae-back-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],

                'conditions'=>[
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'coupon_type',
                            'operator' =>'!==',
                            'value' => 'standard'
                         ],
                    ]
                ],
                
            ]
        );


        
        $this->add_control(
            'back_title_heading',
            [
                'label'     => __( 'Title', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                
                
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_back_title',
				'selector' => '{{WRAPPER}} .eae-back-title , .eae-cc-{{ID}} .eae-back-title',
              
			]
		);


        $this->add_control(
            'back_title',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-back-title' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '.eae-cc-{{ID}} .eae-back-title' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],      
               
            ]
        );

        $this->add_control(
            'back_title_h',
            [
                'label' => esc_html__( 'Text Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-back-title:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '.eae-cc-{{ID}} .eae-back-title:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
                      
            ]
        );


        $this->add_control(
            'des_back_title_heading',
            [
                'label'     => __( 'Description', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
                              
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_back_des',
				'selector' => '{{WRAPPER}} .eae-back-des , .eae-cc-{{ID}} .eae-back-des',
                
			]
		);


        $this->add_control(
            'des_back',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-back-des' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '.eae-cc-{{ID}} .eae-back-des' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],      
                
            ]
        );

        $this->add_control(
            'back_des_h',
            [
                'label' => esc_html__( 'Text Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-back-des:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '.eae-cc-{{ID}} .eae-back-des:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ], 
            ]
        );

        $this->add_control(
            'back_re_btn',
            [
                'label'     => __( 'Redirect Button', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                 'separator' => 'before', 
            ]
        );


        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_re_back',
                'selector' => '{{WRAPPER}} .eae-re-btn , .eae-cc-{{ID}} .pop-visit-btn',
            ]
        );
    
        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'ts_re_back',
                'selector' => '{{WRAPPER}} .eae-re-btn , .eae-cc-{{ID}} .pop-visit-btn',
            ]
        );

        $this->start_controls_tabs( 'tabs_button_style_re_back');

        $this->start_controls_tab(
            'tab_button_normal_re_back',
            [
                'label' => esc_html__( 'Normal', 'wts-eae' ),
            ]
        );

        $this->add_control(
            'color_re_back',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-re-btn' => 'fill: {{VALUE}}; color: {{VALUE}};',
                    '.eae-cc-{{ID}} .pop-visit-btn' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],  
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_re_back',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-re-btn , .bp-popup.eae-cc-{{ID}} .pop-visit-btn',       
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_button_hover_re_back',
            [
                'label' => esc_html__( 'Hover', 'wts-eae' ),
            ]
        );

        $this->add_control(
            'color_re_back_h',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-re-btn:hover' => 'color: {{VALUE}};',
                    '.eae-cc-{{ID}} .pop-visit-btn:hover' => 'color: {{VALUE}};'
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'btn_bg_re_back_h',
                'types' => [ 'classic', 'gradient' ],
                'exclude' => [ 'image' ],
                'selector' => '{{WRAPPER}} .eae-re-btn:hover , .eae-cc-{{ID}} .pop-visit-btn:hover',
                'fields_options' => [
                    'background' => [
                        'default' => 'classic',
                    ],
                ], 
            ]
        );

        $this->add_control(
            'btn_br_re_back',
            [
                'label' => esc_html__( 'Border Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eae-re-btn' => 'border-color: {{VALUE}};',
                    '.eae-cc-{{ID}} .pop-visit-btn' => 'border-color: {{VALUE}};',
                ],  
            ]
        );

        
        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'border_re_back',
                'selector' => '{{WRAPPER}} .eae-re-btn , .eae-cc-{{ID}} .pop-visit-btn',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'br_re_back',
            [
                'label' => esc_html__( 'Border Radius', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .eae-re-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '.eae-cc-{{ID}} .pop-visit-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding_re_back',
            [
                'label' => esc_html__( 'Padding', 'wts-eae' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .eae-re-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '.eae-cc-{{ID}} .pop-visit-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ], 
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'exp_style',
            [
                'label' => esc_html__( 'Expire Date', 'wts-eae' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'cp_exp_heading',
            [
                'label'     => __( 'Expire Date', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_stn_exp',
				'selector' => '{{WRAPPER}} .eae-exp-date span , .eae-cc-{{ID}} .eae-exp-date span',
			]
		);


        $this->add_control(
            'stn_exp_text_color',
            [
                'label' => esc_html__( 'Date Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-exp-date span , .eae-cc-{{ID}} .eae-exp-date span' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],       
            ]
        );

        $this->add_control(
            'stn_exp_text_hov_color',
            [
                'label' => esc_html__( 'Date Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-exp-date span:hover , .eae-cc-{{ID}} .eae-exp-date:hover span' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],       
            ]
        );

        $this->add_control(
            'cp_exp_lbl_heading',
            [
                'label'     => __( 'Label', 'wts-eae' ),
                'type'      => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography_stn_exp_lbl',
				'selector' => '{{WRAPPER}} .eae-exp-date , .eae-cc-{{ID}} .eae-exp-date',
			]
		);


        $this->add_control(
            'stn_exp_lbl_text_color',
            [
                'label' => esc_html__( 'Text Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-exp-date , .eae-cc-{{ID}} .eae-exp-date ' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],       
            ]
        );

        $this->add_control(
            'stn_exp_lbl_color_h',
            [
                'label' => esc_html__( 'Text Hover Color', 'wts-eae' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eae-exp-date:hover , .eae-cc-{{ID}} .eae-exp-date:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],       
            ]
        );

        $this->end_controls_section();

        
    }

    public function render(){
        
        $settings = $this->get_settings_for_display();
      
        $id = wp_rand( 10, 2147483647 );
        $couponType= $settings['coupon_type'];       
      
        if($settings['source'] == 'dynamic'){
            if(class_exists('WooCommerce')){
                if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                    if(empty($settings['dynamic_coupon']) && $settings['source'] == 'dynamic'){
                        echo "<div class='eae-cc-error message'><p class='elementor-alert elementor-alert-warning'>Select Coupon</p></div>";
                        return;
                    }
                }
                $data= $this->prepareData($settings);
            } else {
                echo "<div class='eae-cc-error message'><p class='elementor-alert elementor-alert-warning'>Activate WooCommerce</p></div>";
               return;
            }
        }
        else{
            $data= $this->prepareData($settings);
        }
    
        $this->add_render_attribute('root','class',"wts-eae-coupon-code-wrapper"); 
        
        $this->add_render_attribute('pop_wrp','class',"white-popup mfp-hide eae-coupon-popup-$id");   
        $this->add_render_attribute('pop_wrp','data-id',$id);   
        $this->add_render_attribute('pop_wrp','id',$id);   
        $this->add_render_attribute('pop_container','href','#'.$id);   
        $this->add_render_attribute('pop_container','class',"eae-coupon-popup-link");   
        $this->add_render_attribute('pop_container','data-id',$id);   

        if(empty($data['couponCode'])){
            if(\Elementor\Plugin::$instance->editor->is_edit_mode()){
                echo "<div class='eae-cc-error message'><p class='elementor-alert elementor-alert-warning'>No Coupon Found</p></div>";
                return;
            }else{
                return;
            }
        }
        echo " <div {$this->get_render_attribute_string('root')} >";       
            switch ($couponType) {
                case 'standard':
                    if($settings['sta_layout']=='click'){
                        if(!empty($data['couponCode'])){
                            if(!empty($data['couponCode']) || !empty($data['title']) ){ 
                                $this->get_coupon_html($data);
                            } if(!empty($data['expDate']) && $settings['sta_exp'] == 'yes' || $settings['source'] == 'dynamic'){ ?> 
                            <div class="eae-exp-date"> 
                                <?php
                                    $heading = $this->get_expiry_date_html($data['expDate']);
                                    echo $heading; 
                                ?>
                            </div> <?php
                           }
                        } 
                    }else{
                        if(!empty($data['couponCode'])){
                            if(!empty( $settings['sta_title'])){ ?>
                                <a <?php echo $this->get_render_attribute_string('pop_container')  ?> >
                                    <?php echo Helper::eae_wp_kses($settings['sta_title']); 
                                        Helper::render_icon_html($settings,$this,'sta_title_icon','eae-cc-icon');
                                    ?>
                                </a> 
                            <?php } ?>
                                <div <?php echo $this->get_render_attribute_string('pop_wrp')  ?>>
                                    <?php  echo $this->get_prepare_html($data); ?>
                                </div>  
                            <?php
                        }   
                    }
                    break;
                case 'scratch':
                    if(!empty($data['couponCode'])){
                        $this->add_render_attribute('eae-scratch-canvas', 'class', 'eae-coupon-canvas');
                        $this->add_render_attribute('eae-scratch-canvas', 'id', 'eae-scratch-canvas');
                        $this->add_render_attribute('eae-scratch-canvas', 'width', $settings['Peel_scratch_width']['size']);
                        $this->add_render_attribute('eae-scratch-canvas', 'height', $settings['Peel_sc_height']['size']);
                        ?>
                            <div class="eae-scratch-container" id="js-container" >
                                <canvas <?php echo $this->get_render_attribute_string('eae-scratch-canvas');?>></canvas>
                                <div class="eae-back-wrapper" style= " z-index: -1;">
                                    <?php
                                    echo $this->get_prepare_html($data); ?>
                                </div>  
                            </div>
                        <?php
                    }
                    break;
                case 'slide': 
                    if(!empty($data['couponCode'])){
                    ?>
                        <div class="eae-coupon-slide">
                            <div class="eae-slide-fr">
                                <div class="eae-scratch-title"> 
                                    <?php echo Helper::eae_wp_kses($data['fr_title']); ?>
                                </div>
                                <div class="eae-scratch-des"> 
                                    <?php echo Helper::eae_wp_kses($data['fr_des']); ?>
                                </div>
                            </div>
                            <div class="eae-coupon-back">  
                            <?php echo $this->get_prepare_html($data); ?>
                            </div>
                        </div>
                    <?php
                    }   
                    break;
            }
        echo '</div>';
    }

    public function get_prepare_html($data){
        $settings = $this->get_settings_for_display();
        
        $couponType= $settings['coupon_type'];
        if($couponType == 'slide'){
            $this->add_link_attributes('btn_link', $settings['peel_visit_link']);
            $this->add_render_attribute('btn_link', 'class', 'eae-re-btn ');
        }
        if($couponType == 'scratch'){
            $this->add_link_attributes('btn_link', $settings['peel_visit_link']);
            $this->add_render_attribute('btn_link', 'class', 'eae-re-btn');
        }
        if($couponType == 'standard'){
                $this->add_link_attributes('btn_link', $settings['pop_visit_link']);
                $this->add_render_attribute('btn_link', 'class', 'pop-visit-btn');
        }

        foreach($settings['cc_order'] as $index => $item){            
            switch ($item['cc_heading']) {
                case 'Title':
                    if(!empty($data['bk_title'])){
                        ?>
                        <div class="eae-back-title">
                            <?php echo Helper::eae_wp_kses($data['bk_title']); ?>
                        </div>
                        <?php 
                    }
                    break;
                case 'Description':
                    if(!empty($data['bk_des'])) { ?> 
                        <div class="eae-back-des">
                            <?php echo Helper::eae_wp_kses($data['bk_des']); ?>
                        </div>
                    <?php }
                    break;
                case 'Coupon':
                    $this->get_coupon_html($data);
                    break;
                case 'Expire Date':
                    if(!empty($data['expDate']) && $settings['sta_exp'] == 'yes' || $settings['source'] == 'dynamic'){ ?>
                        <div class="eae-exp-date">
                        <?php
                            if(!empty($data['expDate'])){
                                $heading = $this->get_expiry_date_html($data['expDate']);
                                echo Helper::eae_wp_kses($heading);
                            }
                        ?>
                        </div>
                    <?php }
                    break;
                case 'Visit Button':
                    if(!empty($data['visit_btn'])) { ?>
                    <a <?php echo $this->get_render_attribute_string('btn_link'); ?> > 
                        <?php  
                            echo esc_html($data['visit_btn']);
                        ?> 
                    </a> <?php
                    }  
                    break;
            }
        }
    }
    public function get_coupon_html($data){
        $settings = $this->get_settings_for_display();
        $codeClass='';
        if($settings ['sta_show_code']!= 'yes' && $settings['sta_layout'] == 'click'){
           $codeClass = ' disable';
        } 

        if($settings['sta_layout'] == 'click' ){
            $this->add_link_attributes('btn_lin', $settings['sta_redirect_link']); 
        }
        $this->add_render_attribute('btn_lin', 'class', 'eae-coupon eae-cc-button');
     
        $tag= 'div';
        if($settings['coupon_type'] == 'standard'){
            $tag = 'a';
        }else{
            $tag = 'div';
        }
        ?>
        <div class="eae-coupon-wrapper">
            <?php if(!empty($data['couponCode'])){
            ?>
                <div class="eae-code<?php echo esc_attr($codeClass); ?>" data-code-value = "<?php echo esc_attr($data['couponCode']); ?>"> 
                    <?php echo Helper::eae_wp_kses($data['couponCode']);  ?>               
                </div> 
            <?php 
            }

            if (!empty($data['title'])) { ?>
            <?php
                    ob_start();
                    Helper::render_icon_html($settings,$this,'sta_title_icon','eae-cc-icon');
                    $btn_icon = ob_get_contents();
                    ob_end_clean();
                    echo sprintf('<%1$s %2$s> %3$s %4$s </%1$s>', $tag, $this->get_render_attribute_string('btn_lin'), $data['title'], $btn_icon);
            ?>
           
            <?php } ?>
        </div> 
        <?php
    }

    public function get_expiry_date_html($expDate){
        $settings = $this->get_settings_for_display();
        //echo $expDate;
        $result = str_replace("ExpireDate",$expDate,$settings['sta_exp_dt_msg']);
        $result = preg_replace_callback('/%%(.*?)%%/', function($matches) {
			return '<span>' . $matches[1] . '</span>';
		}, $result);
		return $result;
	}

    public function prepareData($settings){
        $data = [];
        switch ($settings['source']) {
            case 'static':   
                switch ($settings['coupon_type']) {
                    case 'standard':
                        if($settings['sta_layout']=='click'){
                            $data['title']=$settings['sta_title'];
                            $data['expDate'] = $settings['sta_expire_date'];
                            $data['couponCode']=$settings['sta_coupon_code'];   
                            if($settings['sta_layout'] == 'click'){
                                $data['timing'] = $settings['sta_speed'];
                            }
                        }else{
                            $data['bk_title'] =  $settings['pop_title']; 
                            $data['bk_des'] =  $settings['pop_des'];
                            $data['title'] = $settings['sta_title'];
                            $data['expDate'] = $settings['sta_expire_date'];
                            $data['couponCode'] = $settings['sta_coupon_code'];
                            $data['visit_btn'] = $settings['pop_visit_button'];
                        }
                            
                            //  ! ------------- Dynamic ----------------
                        break;
                    case 'peel':
                        $data['fr_title']=$settings['peel_front_title'];
                        $data['fr_des']=$settings['peel_front_des'];
                        $data['bk_title']=$settings['peel_title'];
                        $data['bk_des']=$settings['peel_des'];
                        $data['couponCode']=$settings['sta_coupon_code'];
                        $data['title']=$settings['peel_copy_button'];   
                        $data['visit_btn'] = $settings['peel_visit_button'];
                        $data['expDate'] = $settings['peel_expire_date'];
                        break;
                    case 'scratch':

                        $data['bk_title']=$settings['peel_title'];
                        $data['bk_des']=$settings['peel_des'];
                        $data['couponCode']=$settings['sta_coupon_code'];
                        $data['title']=$settings['peel_copy_button'];
                        $data['visit_btn'] = $settings['peel_visit_button'];     
                        $data['expDate'] = $settings['sta_expire_date'];                   
                        break;
                    case 'slide':
                        $data['fr_title']=$settings['peel_front_title'];
                        $data['fr_des']=$settings['peel_front_des'];
                        $data['bk_title']=$settings['peel_title'];
                        $data['bk_des']=$settings['peel_des'];
                        $data['couponCode']=$settings['sta_coupon_code'];
                        $data['title']=$settings['peel_copy_button'];   
                        $data['visit_btn'] = $settings['peel_visit_button'];    
                        $data['expDate'] = $settings['sta_expire_date'];                      
                        break;
                }
              break;
            case 'dynamic':

                $coupon_id = $settings['dynamic_coupon'];
                if(empty($coupon_id)){
                    return;
                }
                $coupon = get_post($coupon_id);
                $coupon_object = new WC_Coupon($coupon->ID);
                $coupon_code = $coupon_object->get_code();
                if($coupon_object->get_date_expires() != null){
                    $expiry_date = date('Y-m-d', $coupon_object->get_date_expires()->getTimestamp());
                }else{
                    $expiry_date = 'Expiry Date Not added';
                }
                $description = $coupon_object->get_description(); 
                        
                switch ($settings['coupon_type']) {
                    case 'standard':
                        if($settings['sta_layout']=='click'){
                            $data['title']=$settings['sta_title'];
                            $data['couponCode']=$coupon_code;
                            $data['expDate'] = $expiry_date;
                            if($settings['sta_layout'] == 'click'){
                                $data['timing'] = $settings['sta_speed'];
                            }
                        }else{
                            $data['bk_title'] =  $settings['pop_title']; 
                            $data['bk_des'] = $description;
                            $data['title'] = $settings['sta_title'];
                            $data['expDate'] = $expiry_date;
                            $data['couponCode'] = $coupon_code;
                            $data['visit_btn'] = $settings['pop_visit_button'];
                        }
                            
                        break;
                    case 'peel':
                        $data['fr_title']=$settings['peel_front_title'];
                        $data['fr_des']=$description;
                        $data['bk_title']=$settings['peel_title'];
                        $data['bk_des']=$settings['peel_des'];
                        $data['couponCode']=$coupon_code;
                        $data['title']=$settings['peel_copy_button'];
                        $data['visit_btn'] = $settings['peel_visit_button'];
                        $data['expDate'] = $expiry_date;
                        break;
                    case 'scratch':
                        $data['bk_title']=$settings['peel_title'];
                        $data['bk_des']=$description;
                        $data['couponCode']=$coupon_code;
                        $data['title']=$settings['peel_copy_button'];
                        $data['visit_btn'] = $settings['peel_visit_button'];    
                        $data['expDate'] = $expiry_date;
                        break;
                    case 'slide':
                        $data['fr_title']=$settings['peel_front_title'];
                        $data['fr_des']=$description;
                        $data['bk_title']=$settings['peel_title'];
                        $data['bk_des']=$settings['peel_des'];
                        $data['couponCode']=$coupon_code;
                        $data['title']=$settings['peel_copy_button'];
                        $data['visit_btn'] = $settings['peel_visit_button'];
                        $data['expDate'] = $expiry_date;
                        break;
                }
              break; 
          }
          return $data;
    }


}

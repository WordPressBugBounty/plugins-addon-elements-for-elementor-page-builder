<?php
namespace WTS_EAE\Classes;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin as EPlugin;

class Lightgallery_helper {
	public static function add_controls($widget, $media_type = ['image', 'video'], $hide_controls = []) {

		$widget->add_control(
			'lightgallery_slideshow_heading',
			[
				'label'     => __( 'Slide Show', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$widget->add_control(
			'lightgallery_loop',
			[
				'label'        => __( 'Loop', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'true',
				'default'      => 'true',
			]
		);

		$widget->add_control(
			'lightgallery_speed',
			[
				'label'     => __( 'Speed', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 500,
					],
				'range'     => [
					'px' => [
					'min' => 100,
					'max' => 10000,
					]
				],
			]
		);

		$widget->add_control(
			'lightgallery_slideDelay',
			[
				'label'     => __( 'Slide Delay', 'wts-eae' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 100,
					],
				'range'     =>[
					'px' => [
					'min' => 100,
					'max' => 10000,
					]
				],
			]
		);

		if(!in_array('lightgallery_fullscreen',$hide_controls)){
			$widget->add_control(
				'lightgallery_fullscreen',
				[
					'label'        => __( 'Full Screen', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
					'default'      => 'true',
				]
			);
		}

		if(!in_array('lightgallery_zoom',$hide_controls) && in_array('image', $media_type) ){
			$widget->add_control(
				'lightgallery_zoom',
				[
					'label'        => __( 'Zoom', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
					'default'      => 'true',
				]
			);
		}

		if(!in_array('lightgallery_counter',$hide_controls)){
			$widget->add_control(
				'lightgallery_counter',
				[
					'label'        => __( 'Counter', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
					'default'      => 'true',
				]
			);
		}

		if(!in_array('lightgallery_download',$hide_controls)){
			$widget->add_control(
				'lightgallery_download',
				[
					'label'        => __( 'Download', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
					'default'      => 'true',
				]
			);
		}

		if(!in_array('lightgallery_allowMediaOverlap',$hide_controls)){
			$widget->add_control(
				'lightgallery_allowMediaOverlap',
				[
					'label'        => __( 'Media Overlap', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => __( 'If true, toolbar, captions and thumbnails will not overlap with media element.', 'wts-eae'),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
					'default'      => 'true',
				]
			);
		}

		$widget->add_control(
			'lightgallery_closeOnTap',
			[
				'label'     => __( 'Close on Tap', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'description'  => __( 'Allows clicks on overlay to close gallery.', 'wts-eae'),
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'true',
				'default'      => 'true',
			]
		);

		$widget->add_control(
			'lightgallery_escKey',
			[
				'label'        => __( 'Esc Key', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'Whether the LightGallery could be closed by pressing the "Esc" key.', 'wts-eae'),
				'label_on'     => __( 'Yes', 'wts-eae' ),
				'label_off'    => __( 'No', 'wts-eae' ),
				'return_value' => 'true',
				'default'      => 'true',
			]
		);

    
		if (! in_array('lightgallery_hideBarsDelay', $hide_controls)) {
			$widget->add_control(
				'lightgallery_hideBarsDelay',
				[
					'label'   => __( 'Hide Bars Delay', 'wts-eae' ),
					'type'    => Controls_Manager::SLIDER,
					'default' => [
						'size' => 5000,
					],
					'range'   => [
						'px' => [
							'min' => 100,
							'max' => 10000,
						],
					],
				]
			);
		}

		$widget->add_control(
			'lightgallery_controls_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'style'     => 'thick',
			]
		);

		$widget->add_control(
			'lightgallery_video_heading',
			[
				'label'     => __( 'Video', 'wts-eae' ),
				'type'      => Controls_Manager::HEADING,
			]
		);

		if(!in_array('lightgallery_autoplayVideoOnSlide',$hide_controls)){
			$widget->add_control(
				'lightgallery_autoplayVideoOnSlide',
				[
					'label'        => __( 'Autoplay', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => __( 'Autoplay video on slide change', 'wts-eae'),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
				]
			);
		}

		$widget->add_control(
			'lightgallery_video_divider',
			[
				'type'      => Controls_Manager::DIVIDER,
				'style'     => 'thick',
			]
		);

		if(!in_array('lightgallery_navigation_popover',$hide_controls)){
			$widget->add_control(
				'lightgallery_navigation_popover',
				[
					'label' => esc_html__( 'Navigation', 'wts-eae' ),
					'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'return_value' => 'yes',
				]
			);

			$widget->start_popover();

			$widget->add_control(
				'lightgallery_controls',
				[
					'label'        => __( 'Enable Arrows', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
					'default'      => 'true',
				]
			);

			$widget->add_control(
				'lightgallery_enableDrag',
				[
					'label'        => __( 'Enable Drag', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
					'default'      => 'true',
				]
			);

			$widget->add_control(
				'lightgallery_enableSwipe',
				[
					'label'        => __( 'Enable Swipe', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
					'default'      => 'true',
				]
			);

			$widget->add_control(
				'lightgallery_keyPress',
				[
					'label'        => __( 'Keyboard', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
					'default'      => 'true',
				]
			);

			$widget->add_control(
				'lightgallery_mousewheel',
				[
					'label'        => __( 'Mouse Wheel', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
				]
			);

			

			$widget->add_control(
				'lightgallery_nextHtml',
				[
					'label'     => __( 'Next Html', 'wts-eae' ),
					'type'      => Controls_Manager::TEXT,
				]
			);

			$widget->add_control(
				'lightgallery_prevHtml',
				[
					'label'     => __( 'Prev Html', 'wts-eae' ),
					'type'      => Controls_Manager::TEXT,
				]
			);

			$widget->end_popover();
		}
		
		if ( ! in_array( 'lightgallery_rotate_popover', $hide_controls ) && in_array( 'image', $media_type ) ) {

			$widget->add_control(
				'lightgallery_rotate_popover',
				[
					'label' => esc_html__( 'Rotate', 'wts-eae' ),
					'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'return_value' => 'yes',
					//'default' => 'yes',
				]
			);

			$widget->start_popover();

			$widget->add_control(
				'lightgallery_rotateSpeed',
				[
					'label'     => __( 'Speed', 'wts-eae' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   =>
						[
							'size' => 500,
						],
					'range'     => [
						'px' => [
						'min' => 100,
						'max' => 10000,
						]
					],
				]
			);

			$widget->add_control(
				'lightgallery_rotateLeft',
				[
					'label'        => __( 'Rotate Left', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => __( 'Whether the rotate left button should be visible or not.', 'wts-eae'),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
				]
			);

			$widget->add_control(
				'lightgallery_rotateRight',
				[
					'label'        => __( 'Rotate Right', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => __( 'Whether the rotate right button should be visible or not.', 'wts-eae'),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
				]
			);

			$widget->add_control(
				'lightgallery_flipHorizontal',
				[
					'label'        => __( 'Flip Horizontal', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => __( 'Whether the flip horizontal button should be visible or not.', 'wts-eae'),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
				]
			);

			$widget->add_control(
				'lightgallery_flipVertical',
				[
					'label'        => __( 'Flip Vertical', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => __( 'Whether the flip vertical button should be visible or not.', 'wts-eae'),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
				]
			);

			$widget->end_popover();
		}

		if(!in_array('lightgallery_thumbnail_popover',$hide_controls)){
			$widget->add_control(
				'lightgallery_thumbnail_popover',
				[
					'label' => esc_html__( 'Thumbnail', 'wts-eae' ),
					'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'return_value' => 'yes',
				]
			);

			$widget->start_popover();

			$widget->add_control(
				'lightgallery_alignThumbnails',
				[
					'label'        => __( 'Alignment', 'wts-eae' ),
					'type'         => Controls_Manager::CHOOSE,
					'default'      => 'middle',
					'options'     => [
						'left' => [
							'title' => __( 'Left', 'wts-eae' ),
							'icon'  => 'eicon-h-align-left',
						],
						'middle' => [
							'title' => __( 'Middle', 'wts-eae' ),
							'icon'  => 'eicon-h-align-center',
						],
						'right' => [
							'title' => __( 'Right', 'wts-eae' ),
							'icon'  => 'eicon-h-align-right',
						],
					],
				]
			);

			$widget->add_control(
				'lightgallery_toggleThumb',
				[
					'label'        => __( 'Toggle Thumb', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => __( 'Enable toggle captions and thumbnails, not applicable if "Media Overlap" is false.', 'wts-eae'),
					'default'      => '',
				]
			);

			$widget->add_control(
				'lightgallery_thumbnail_width',
				[
					'label'        => __( 'Width', 'wts-eae' ),
					'type'         => Controls_Manager::NUMBER,
					'description'  => __( 'Width of the thumbnail in pixels.', 'wts-eae'),
					'default'      => '100',
				]
			);

			$widget->add_control(
				'lightgallery_thumbnail_height',
				[
					'label'        => __( 'Height', 'wts-eae' ),
					'type'         => Controls_Manager::NUMBER,
					'description'  => __( 'Height of the thumbnail in pixels.', 'wts-eae'),
					'default'      => '100',
				]
			);

			$widget->add_control(
				'lightgallery_thumbnail_margin',
				[
					'label'        => __( 'Margin', 'wts-eae' ),
					'type'         => Controls_Manager::NUMBER,
					'description'  => __( 'Margin of the thumbnail in pixels.', 'wts-eae'),
					'default'      => '5',
				]
			);

			$widget->end_popover();
		}

		if(!in_array('lightgallery_hash_popover',$hide_controls)){
			$widget->add_control(
				'lightgallery_hash_popover',
				[
					'label' => esc_html__( 'Hash URL', 'wts-eae' ),
					'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				]
			);

			$widget->start_popover();

			$widget->add_control(
				'lightgallery_customSlideName',
				[
					'label'        => __( 'Custom Slider Name', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'description'  => __( 'Custom slide name to use in the url when hash plugin is enabled', 'wts-eae'),
					'label_on'     => __( 'Yes', 'wts-eae' ),
					'label_off'    => __( 'No', 'wts-eae' ),
					'return_value' => 'true',
				]
			);

			$widget->add_control(
				'lightgallery_galleryId',
				[
					'label'     => __( 'Gallery ID', 'wts-eae' ),
					'type'      => Controls_Manager::TEXT,
					'default'	=> 'gallery-1',
					'condition' 	=> [
						'lightgallery_customSlideName' => 'true'
					]
				]
			);

			$widget->end_popover();

			if (!in_array('lightgallery_share_popover', $hide_controls)) {
				$widget->add_control(
					'lightgallery_share_popover',
					[
						'label' => esc_html__( 'Share', 'wts-eae' ),
						'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					]
				);

				$widget->start_popover();

				$widget->add_control(
					'lightgallery_facebook',
					[
						'label'        => __( 'Facebook', 'wts-eae' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'Yes', 'wts-eae' ),
						'label_off'    => __( 'No', 'wts-eae' ),
						'return_value' => 'true',
						'default'      => 'true',
					]
				);

				$widget->add_control(
					'lightgallery_facebookDropdownText',
					[
						'label'        => __( 'Facebook Text', 'wts-eae' ),
						'type'         => Controls_Manager::TEXT,
						'default'	=> 'Facebook',
						'condition'		=> [
							'lightgallery_facebook'	=> 'true'
						]
					]
				);

				$widget->add_control(
					'lightgallery_twitter',
					[
						'label'        => __( 'Twitter', 'wts-eae' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'Yes', 'wts-eae' ),
						'label_off'    => __( 'No', 'wts-eae' ),
						'return_value' => 'true',
						'default'      => 'true',
					]
				);

				$widget->add_control(
					'lightgallery_twitterDropdownText',
					[
						'label'        => __( 'Twitter Text', 'wts-eae' ),
						'type'         => Controls_Manager::TEXT,
						'default'	=> 'Twitter',
						'condition'		=> [
							'lightgallery_twitter'	=> 'true'
						]
					]
				);
				
				$widget->add_control(
					'lightgallery_pinterest',
					[
						'label'        => __( 'Pinterest', 'wts-eae' ),
						'type'         => Controls_Manager::SWITCHER,
						'label_on'     => __( 'Yes', 'wts-eae' ),
						'label_off'    => __( 'No', 'wts-eae' ),
						'return_value' => 'true',
						'default'      => 'true',
					]
				);

				$widget->add_control(
					'lightgallery_pinterestDropdownText',
					[
						'label'        => __( 'Pinterest Text', 'wts-eae' ),
						'type'         => Controls_Manager::TEXT,
						'default'	=> 'Pinterest',
						'condition'		=> [
							'lightgallery_pinterest'	=> 'true'
						]
					]
				);

				$widget->end_popover();
			}
		}
	}

	public static function get_lightgallery_data($settings){
	
		$lightgallery_data = array();
		$lightgallery_data['loop'] = isset($settings['lightgallery_loop']) ? $settings['lightgallery_loop'] : false;
		$lightgallery_data['allowMediaOverlap'] = isset($settings['lightgallery_allowMediaOverlap']) ? $settings['lightgallery_allowMediaOverlap'] : false;
		$lightgallery_data['speed'] = isset($settings['lightgallery_speed']) ? $settings['lightgallery_speed']['size'] : 500;
		$lightgallery_data['slideDelay'] = isset($settings['lightgallery_slideDelay']) ? $settings['lightgallery_slideDelay']['size'] : 0;
		$lightgallery_data['closeOnTap'] = isset($settings['lightgallery_closeOnTap']) ? $settings['lightgallery_closeOnTap'] : false;
		$lightgallery_data['controls'] = isset($settings['lightgallery_controls']) ? $settings['lightgallery_controls'] : true;
		$lightgallery_data['fullScreen'] = isset($settings['lightgallery_fullscreen']) ? $settings['lightgallery_fullscreen'] : true;
		$lightgallery_data['zoom'] = isset($settings['lightgallery_zoom']) ? $settings['lightgallery_zoom'] : false;
		$lightgallery_data['counter'] = isset($settings['lightgallery_counter']) ? $settings['lightgallery_counter'] : true;
		$lightgallery_data['download'] = isset($settings['lightgallery_download']) ? $settings['lightgallery_download'] : false;
		$lightgallery_data['hideBarsDelay'] = isset($settings['lightgallery_hideBarsDelay']) ? $settings['lightgallery_hideBarsDelay']['size'] : 0;
		$lightgallery_data['autoplayVideoOnSlide'] = isset($settings['lightgallery_autoplayVideoOnSlide']) ? $settings['lightgallery_autoplayVideoOnSlide'] : false;
		$lightgallery_data['enableDrag'] = isset($settings['lightgallery_enableDrag']) ? $settings['lightgallery_enableDrag'] : true;
		$lightgallery_data['enableSwipe'] = isset($settings['lightgallery_enableSwipe']) ? $settings['lightgallery_enableSwipe'] : true;
		$lightgallery_data['keyPress'] = isset($settings['lightgallery_keyPress']) ? $settings['lightgallery_keyPress'] : true;
		$lightgallery_data['mousewheel'] = isset($settings['lightgallery_mousewheel']) ? $settings['lightgallery_mousewheel'] : true;
		if(isset($settings['lightgallery_escKey'])){ 
			if($settings['lightgallery_escKey'] == 'true'){
				$lightgallery_data['escKey'] = true;
			}else{
				$lightgallery_data['escKey'] = false;
			}
		}else{
			$lightgallery_data['escKey'] = true;
		}	
		$lightgallery_data['prevHtml'] = isset($settings['lightgallery_prevHtml']) ? $settings['lightgallery_prevHtml'] : "";
		$lightgallery_data['nextHtml'] = isset($settings['lightgallery_nextHtml']) ? $settings['lightgallery_nextHtml'] : "";
		$lightgallery_data['rotate'] = isset($settings['lightgallery_rotate_popover']) ? $settings['lightgallery_rotate_popover'] : false;
		$lightgallery_data['rotateLeft'] = isset($settings['lightgallery_rotateLeft']) ? $settings['lightgallery_rotateLeft'] : true;
		$lightgallery_data['rotateRight'] = isset($settings['lightgallery_rotateRight']) ? $settings['lightgallery_rotateRight'] : true;
		$lightgallery_data['flipHorizontal'] = isset($settings['lightgallery_flipHorizontal']) ? $settings['lightgallery_flipHorizontal'] : true;
		$lightgallery_data['flipVertical'] = isset($settings['lightgallery_flipVertical']) ? $settings['lightgallery_flipVertical'] : true;
		$lightgallery_data['rotateSpeed'] = isset($settings['lightgallery_rotateSpeed']) ? $settings['lightgallery_rotateSpeed']['size'] : 400;
		$lightgallery_data['thumbnail'] = isset($settings['lightgallery_thumbnail_popover']) ? $settings['lightgallery_thumbnail_popover'] : false;
		$lightgallery_data['alignThumbnails'] = isset($settings['lightgallery_alignThumbnails']) ? $settings['lightgallery_alignThumbnails'] : "bottom";
		$lightgallery_data['thumbWidth'] = isset($settings['lightgallery_thumbnail_width']) ? $settings['lightgallery_thumbnail_width'] : 100;
		$lightgallery_data['thumbHeight'] = isset($settings['lightgallery_thumbnail_height']) ? $settings['lightgallery_thumbnail_height'] . 'px' : "80px";
		$lightgallery_data['thumbMargin'] = isset($settings['lightgallery_thumbnail_margin']) ? $settings['lightgallery_thumbnail_margin'] : 5;
		$lightgallery_data['toggleThumb'] = isset($settings['lightgallery_toggleThumb']) ? $settings['lightgallery_toggleThumb'] : "false";
		$lightgallery_data['hash'] = isset($settings['lightgallery_hash_popover']) ? $settings['lightgallery_hash_popover'] : false;
		$lightgallery_data['customSlideName'] = isset($settings['lightgallery_customSlideName']) ? $settings['lightgallery_customSlideName'] : false;
		$lightgallery_data['galleryId'] = isset($settings['lightgallery_galleryId']) ? $settings['lightgallery_galleryId'] : "1";
		$lightgallery_data['share'] = isset($settings['lightgallery_share_popover']) ? $settings['lightgallery_share_popover'] : false;
		$lightgallery_data['facebook'] = isset($settings['lightgallery_facebook']) ? $settings['lightgallery_facebook'] : true;
		$lightgallery_data['twitter'] = isset($settings['lightgallery_twitter']) ? $settings['lightgallery_twitter'] : true;
		$lightgallery_data['pinterest'] = isset($settings['lightgallery_pinterest']) ? $settings['lightgallery_pinterest'] : true;
		$lightgallery_data['facebookDropdownText'] = isset($settings['lightgallery_facebookDropdownText']) ? $settings['lightgallery_facebookDropdownText'] : "Facebook";
		$lightgallery_data['twitterDropdownText'] = isset($settings['lightgallery_twitterDropdownText']) ? $settings['lightgallery_twitterDropdownText'] : "Twitter";
		$lightgallery_data['pinterestDropdownText'] = isset($settings['lightgallery_pinterestDropdownText']) ? $settings['lightgallery_pinterestDropdownText'] : "Pinterest";
		$lightgallery_data['swipeDirection'] = is_rtl() ? 'right' : 'left';
		return $lightgallery_data;
	}

}
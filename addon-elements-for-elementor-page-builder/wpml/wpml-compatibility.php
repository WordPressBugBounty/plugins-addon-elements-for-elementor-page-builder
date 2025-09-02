<?php
namespace WTS_EAE;

class WPML_Compatibility {

	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct() {
		
		add_filter( 'wpml_elementor_widgets_to_translate', [ $this, 'wpml_widgets' ] );
	}

	public function wpml_widgets( $widgets ) {
		 
		// Free
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-animated-text.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-gmap.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-filterable-gallery.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-price-table.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-timeline.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-info-circle.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-thumbnail-slider.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-content-switcher.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-comparison-table.php';
		require_once EAE_PATH . 'wpml/modules/comparison-table/class-wpml-eae-comparison-table-plan-one.php';
		require_once EAE_PATH . 'wpml/modules/comparison-table/class-wpml-eae-comparison-table-plan-two.php';
		require_once EAE_PATH . 'wpml/modules/comparison-table/class-wpml-eae-comparison-table-plan-three.php';
		require_once EAE_PATH . 'wpml/modules/comparison-table/class-wpml-eae-comparison-table-plan-four.php';
		require_once EAE_PATH . 'wpml/modules/comparison-table/class-wpml-eae-comparison-table-plan-five.php';
		require_once EAE_PATH . 'wpml/modules/comparison-table/class-wpml-eae-comparison-table-plan-six.php';
		require_once EAE_PATH . 'wpml/modules/comparison-table/class-wpml-eae-comparison-table-plan-seven.php';
		require_once EAE_PATH . 'wpml/modules/comparison-table/class-wpml-eae-comparison-table-plan-eight.php';
		require_once EAE_PATH . 'wpml/modules/comparison-table/class-wpml-eae-comparison-table-plan-nine.php';
		require_once EAE_PATH . 'wpml/modules/comparison-table/class-wpml-eae-comparison-table-plan-ten.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-data-table-header.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-data-table-content.php';
		require_once EAE_PATH . 'wpml/modules/class-wpml-eae-chart.php';

		$widgets = $this->split_text( $widgets );
		$widgets = $this->flip_box( $widgets );
		$widgets = $this->dual_button( $widgets );
		$widgets = $this->image_compare( $widgets );
		$widgets = $this->modal_popup( $widgets );
		$widgets = $this->progress_bar( $widgets );
		$widgets = $this->text_separator( $widgets );
		$widgets = $this->twitter( $widgets );
		$widgets = $this->post_list( $widgets );
		$widgets = $this->animated_text( $widgets );
		$widgets = $this->gmap( $widgets );
		$widgets = $this->filterable_gallery( $widgets );
		$widgets = $this->price_table( $widgets );
		$widgets = $this->timeline( $widgets );
		$widgets = $this->info_circle( $widgets );
		$widgets = $this->comparison_table( $widgets );
		$widgets = $this->thumbnail_slider( $widgets );
		$widgets = $this->data_table( $widgets );
		$widgets = $this->chart( $widgets );
		$widgets = $this->content_switcher( $widgets ); 
		$widgets = $this->animated_link( $widgets ); 
		

		if(Plugin::$is_pro){
		// Pro
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-business-hours.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-business-hours-custom.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-image-accordion-custom.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-image-advance-list.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-faq.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-team-member.php';
		require_once EAE_PATH . 'wpml/modules/pro/radial-charts/class-radial-chart-dataset-one.php';
		require_once EAE_PATH . 'wpml/modules/pro/radial-charts/class-radial-chart-dataset-two.php';
		require_once EAE_PATH . 'wpml/modules/pro/radial-charts/class-radial-chart-dataset-three.php';
		require_once EAE_PATH . 'wpml/modules/pro/radial-charts/class-radial-chart-dataset-four.php';
		require_once EAE_PATH . 'wpml/modules/pro/radial-charts/class-radial-chart-dataset-five.php';
		require_once EAE_PATH . 'wpml/modules/pro/radial-charts/class-radial-chart-dataset-six.php';
		require_once EAE_PATH . 'wpml/modules/pro/radial-charts/class-radial-chart-dataset-seven.php';
		require_once EAE_PATH . 'wpml/modules/pro/radial-charts/class-radial-chart-dataset-eight.php';
		require_once EAE_PATH . 'wpml/modules/pro/radial-charts/class-radial-chart-dataset-nine.php';
		require_once EAE_PATH . 'wpml/modules/pro/radial-charts/class-radial-chart-dataset-ten.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-floating-element.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-video-gallery.php';
		require_once EAE_PATH . 'wpml/modules/pro/advance-price-table/class-advance-price-table-dataset-one.php';
		require_once EAE_PATH . 'wpml/modules/pro/advance-price-table/class-advance-price-table-dataset-two.php';
		require_once EAE_PATH . 'wpml/modules/pro/advance-price-table/class-advance-price-table-dataset-three.php';
		require_once EAE_PATH . 'wpml/modules/pro/advance-price-table/class-advance-price-table-dataset-four.php';
		require_once EAE_PATH . 'wpml/modules/pro/advance-price-table/class-advance-price-table-dataset-five.php';
		require_once EAE_PATH . 'wpml/modules/pro/advance-price-table/class-advance-price-table-dataset-six.php';
		require_once EAE_PATH . 'wpml/modules/pro/advance-price-table/class-advance-price-table-dataset-seven.php';
		require_once EAE_PATH . 'wpml/modules/pro/advance-price-table/class-advance-price-table-dataset-eight.php';
		require_once EAE_PATH . 'wpml/modules/pro/advance-price-table/class-advance-price-table-dataset-nine.php';
		require_once EAE_PATH . 'wpml/modules/pro/advance-price-table/class-advance-price-table-dataset-ten.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-image-hotspot.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-image-stack.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-info-group.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-testimonial-slider.php';
		require_once EAE_PATH . 'wpml/modules/pro/class-wpml-audio-player.php';

			$widgets = $this->advanced_heading( $widgets );
			$widgets = $this->add_to_calendar( $widgets );
			$widgets = $this->business_hours( $widgets );
			$widgets = $this->image_accordion( $widgets );
			$widgets = $this->advance_list( $widgets );
			$widgets = $this->faq( $widgets );
			$widgets = $this->team_member( $widgets );
			$widgets = $this->video_box( $widgets );
			$widgets = $this->instagram_feed( $widgets );
			$widgets = $this->radial_chart( $widgets );
			$widgets = $this->floating_element( $widgets );
			$widgets = $this->video_gallery( $widgets );
			$widgets = $this->advance_price_table( $widgets );
			$widgets = $this->call_to_action( $widgets );
			$widgets = $this->circular_progress( $widgets );
			$widgets = $this->devices( $widgets );
			$widgets = $this->image_hotspot( $widgets );
			$widgets = $this->image_stack( $widgets );
			$widgets = $this->info_group( $widgets );
			$widgets = $this->coupon_code( $widgets );
			$widgets = $this->testimonial_slider( $widgets );
			$widgets = $this->google_review( $widgets );
			$widgets = $this->woo_category( $widgets );
			$widgets = $this->audio_player( $widgets );
		}
		return $widgets;
	}

	private function radial_chart( $widgets ){
		$widgets['eae-radial-charts'] = [

			'conditions'        => [ 'widgetType' => 'eae-radial-charts' ],
			'fields'            => [],
			'integration-class' => [
				'\WTS_EAE\WPML_EAE_Radial_Chart_Dataset_One',	
				'\WTS_EAE\WPML_EAE_Radial_Chart_Dataset_Two',	
				'\WTS_EAE\WPML_EAE_Radial_Chart_Dataset_Three',	
				'\WTS_EAE\WPML_EAE_Radial_Chart_Dataset_Four',	
				'\WTS_EAE\WPML_EAE_Radial_Chart_Dataset_Five',	
				'\WTS_EAE\WPML_EAE_Radial_Chart_Dataset_Six',	
				'\WTS_EAE\WPML_EAE_Radial_Chart_Dataset_Seven',	
				'\WTS_EAE\WPML_EAE_Radial_Chart_Dataset_Eight',	
				'\WTS_EAE\WPML_EAE_Radial_Chart_Dataset_Nine',	
				'\WTS_EAE\WPML_EAE_Radial_Chart_Dataset_Ten',	
            ]
		];
		for($i = 0; $i<=10; $i++){
			$fields = [
				[
					'field'       => 'dataset_label_'.$i,
					'type'        => __( 'Radial Chart : Dateset'.$i , 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			];
			$widgets['eae-radial-charts']['fields'] = array_merge($widgets['eae-radial-charts']['fields'], $fields);
		}

		return $widgets;
	}

	private function instagram_feed( $widgets ){
		$widgets['eae-instagram-feed'] = [

			'conditions'        => [ 'widgetType' => 'eae-instagram-feed' ],
			'fields'            => [
				[
					'field'       => 'insta_profile_link_text',
					'type'        => __( 'Instagram Feed :  Profile Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $widgets;
	}

	private function video_box( $widgets ){
		$widgets['eae-video-box'] = [

			'conditions'        => [ 'widgetType' => 'eae-video-box' ],
			'fields'            => [
				[
					'field'       => 'video_display_title',
					'type'        => __( 'Video Box :  Video Detail Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'video_display_desc',
					'type'        => __( 'Video Box :  Video Detail Description', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $widgets;
	}

	private function team_member( $widgets ){
		$widgets['eae-team-member'] = [

			'conditions'        => [ 'widgetType' => 'eae-team-member' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Team_Member',
		];

		return $widgets;
	}

	private function faq( $widgets ){
		$widgets['eae-faq'] = [

			'conditions'        => [ 'widgetType' => 'eae-faq' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_FAQ',
		];

		return $widgets;
	}


	private function advance_list( $widgets ){
		$widgets['eae-advanced-list'] = [

			'conditions'        => [ 'widgetType' => 'eae-advanced-list' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Advance_List',
		];

		return $widgets;
	}

	private function image_accordion( $widgets ){
		$widgets['eae-image-accordion'] = [

			'conditions'        => [ 'widgetType' => 'eae-image-accordion' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Image_Accordion',
		];

		return $widgets;
	}

	private function advanced_heading( $widgets ){
		$widgets['eae-advanced-heading'] = [
			'conditions'        => [ 'widgetType' => 'eae-advanced-heading' ],	
			'fields'            => [
				[
					'field'       => 'eae_heading_title',
					'type'        => __( 'Advance Heading :  Heading', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'eae_heading_sub_title',
					'type'        => __( 'Advance Heading : Sub Heading', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'eae_shadow_text_content',
					'type'        => __( 'Advance Heading : Shadow Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'eae_heading_separator_with_text',
					'type'        => __( 'Advance Heading : Separator Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];
		return $widgets;
	}

	private function add_to_calendar( $widgets ){
		$widgets['eae-add-to-calendar'] = [
			'conditions'        => [ 'widgetType' => 'eae-add-to-calendar' ],	
			'fields'            => [
				[
					'field'       => 'eae_calendar_button_text',
					'type'        => __( 'Add to Calendar : Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];
		return $widgets;
	}

	private function business_hours( $widgets ){
		$widgets['eae-business-hours'] = [
			'conditions'        => [ 'widgetType' => 'eae-business-hours' ],	
			'fields'            => [
				[
					'field'       => 'eae_heading_indicators_heading',
					'type'        => __( 'Business Hours  : Indicator Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'eae_heading_indicators_opening_warning_text_enter',
					'type'        => __( 'Business Hours  : Indicator Opening Warning Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'eae_heading_indicators_closing_warning_text_enter',
					'type'        => __( 'Business Hours  : Indicator Opening Warning Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'eae_heading_indicators_label_opening_text',
					'type'        => __( 'Business Hours  : Indicator Label Opening Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'eae_heading_indicators_label_closing_text',
					'type'        => __( 'Business Hours  : Indicator Label Closing Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				
			],
			'integration-class' => [
										'\WTS_EAE\WPML_EAE_Business_Hours',	
										'\WTS_EAE\WPML_EAE_Business_Hours_Custom',	
			]
		];
		return $widgets;
	}

	private function floating_element( $widgets ) {

		$widgets['eae-floating-element'] = [

			'conditions'        => [ 'widgetType' => 'eae-floating-element' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Floating_Element',
		];

		return $widgets;
	}

	private function video_gallery( $widgets ) {

		$widgets['eae-video-gallery'] = [

			'conditions'        => [ 'widgetType' => 'eae-video-gallery' ],
			'fields'            => [
				[
					'field'       => 'vg_filter_title',
					'type'        => __( 'Video Gallery: Filter Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'vg_filterable_all_label_text',
					'type'        => __( 'Video Gallery: Filter Category', 'wts-eae' ),
					'editor_type' => 'LINE',
				],

			],
			'integration-class' => '\WTS_EAE\WPML_EAE_Video_Gallery',
		];

		return $widgets;
	}


	private function advance_price_table( $widgets ) {
		
		$widgets['eae-advanced-price-table'] = [

			'conditions'        => [ 'widgetType' => 'eae-advanced-price-table' ],
			'fields'            => [],
			'integration-class' => [
										'\WTS_EAE\WPML_EAE_Advance_Price_Table',
										'\WTS_EAE\WPML_EAE_Advance_Price_Table_One',
										'\WTS_EAE\WPML_EAE_Advance_Price_Table_Two',
										'\WTS_EAE\WPML_EAE_Advance_Price_Table_Three',
										'\WTS_EAE\WPML_EAE_Advance_Price_Table_Four',
										'\WTS_EAE\WPML_EAE_Advance_Price_Table_Five',
										'\WTS_EAE\WPML_EAE_Advance_Price_Table_Six',
										'\WTS_EAE\WPML_EAE_Advance_Price_Table_Seven',
										'\WTS_EAE\WPML_EAE_Advance_Price_Table_Eight',
										'\WTS_EAE\WPML_EAE_Advance_Price_Table_Nine',
										'\WTS_EAE\WPML_EAE_Advance_Price_Table_Ten',
			]
		];
		for($i = 0; $i<=10; $i++){
			$fields = [
				[
					'field'       => 'pt_title_'.$i ,
					'type'        => __( 'Advance Price Table: Title '. $i , 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pt_description_'.$i,
					'type'        => __( 'Advance Price Table: Description '. $i , 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pt_price_prefix_'.$i,
					'type'        => __( 'Advance Price Table: Price Prefix '. $i , 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pt_price_'.$i,
					'type'        => __( 'Advance Price Table: Price '. $i , 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pt_sale_price_'.$i,
					'type'        => __( 'Advance Price Table: Sale Price '. $i , 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pt_duration_'.$i,
					'type'        => __( 'Advance Price Table: Duration '. $i , 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pt_button_text_'.$i,
					'type'        => __( 'Advance Price Table: Button '. $i , 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pt_badge_text_'.$i,
					'type'        => __( 'Advance Price Table: Badge Text '. $i , 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			];

			$widgets['eae-advanced-price-table']['fields'] = array_merge($widgets['eae-advanced-price-table']['fields'], $fields);
		}
		return $widgets;
	}


	private function chart( $widgets ){
		$widgets['eae-chart'] = [
			'conditions'        => [ 'widgetType' => 'eae-chart' ],
			
			'fields'            => [
				[
					'field'       => 'labels',
					'type'        => __( 'Chart : Labels', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				
			],
			'integration-class' => '\WTS_EAE\WPML_EAE_Chart',
		];

		return $widgets;
	}

	private function data_table( $widgets ){
		
		$widgets['eae-data-table'] = [
			'conditions'        => [ 'widgetType' => 'eae-data-table' ],
			'fields'            => [],
			'integration-class' => [
									'\WTS_EAE\WPML_EAE_Data_Table_Header',
									'\WTS_EAE\WPML_EAE_Data_Table_Content'
								],
		];
		
		return $widgets;
	}

	private function content_switcher( $widgets ){
		
		$widgets['eae-content-switcher'] = [

			'conditions'        => [ 'widgetType' => 'eae-content-switcher' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Content_Switcher',
		];

		return $widgets;
	}

	private function thumbnail_slider( $widgets ){
		
		$widgets['eae-thumbgallery'] = [

			'conditions'        => [ 'widgetType' => 'eae-thumbgallery' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Thumbnail_Slider',
		];

		return $widgets;
	}

	private function split_text( $widgets ) {

		$widgets['wts-splittext'] = [
			'conditions' => [ 'widgetType' => 'wts-splittext' ],
			'fields'     => [
				[
					'field'       => 'text',
					'type'        => __( 'Split Text: Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $widgets;
	}

	private function flip_box( $widgets ) {

		$widgets['wts-flipbox'] = [
			'conditions' => [ 'widgetType' => 'wts-flipbox' ],
			'fields'     => [
				[
					'field'       => 'front_title',
					'type'        => __( 'Flip Box: Front Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'front-text',
					'type'        => __( 'Flip Box: Front Text', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'back_title',
					'type'        => __( 'Flip Box: Back Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'back_text',
					'type'        => __( 'Flip Box: Back Text', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'action_text',
					'type'        => __( 'Flip Box: Button Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $widgets;
	}

	private function dual_button( $widgets ) {

		$widgets['eae-dual-button'] = [
			'conditions' => [ 'widgetType' => 'eae-dual-button' ],
			'fields'     => [
				[
					'field'       => 'button1_text',
					'type'        => __( 'Dual Button: Button 1 Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button2_text',
					'type'        => __( 'Dual Button: Button 2 Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'separator_text',
					'type'        => __( 'Dual Button: Separator Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $widgets;
	}

	private function image_compare( $widgets ) {

		$widgets['wts-ab-image'] = [
			'conditions' => [ 'widgetType' => 'wts-ab-image' ],
			'fields'     => [
				[
					'field'       => 'text_before',
					'type'        => __( 'Image Compare: Before Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'text_after',
					'type'        => __( 'Image Compare: After Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $widgets;
	}

	private function modal_popup( $widgets ) {

		$widgets['wts-modal-popup'] = [
			'conditions' => [ 'widgetType' => 'wts-modal-popup' ],
			'fields'     => [
				[
					'field'       => 'modal_title',
					'type'        => __( 'Modal Popup: Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'modal_content',
					'type'        => __( 'Modal Popup: Text', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'button_text',
					'type'        => __( 'Modal Popup: Button Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $widgets;
	}

	private function progress_bar( $widgets ) {

		$widgets['eae-progress-bar'] = [
			'conditions' => [ 'widgetType' => 'eae-progress-bar' ],
			'fields'     => [
				[
					'field'       => 'progress_title',
					'type'        => __( 'Progress Bar: Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],

			],
		];

		return $widgets;
	}

	private function text_separator( $widgets ) {

		$widgets['wts-textseparator'] = [
			'conditions' => [ 'widgetType' => 'wts-textseparator' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => __( 'Text Separator: Title', 'wts-eae' ),
					'editor_type' => 'AREA',
				],

			],
		];

		return $widgets;
	}

	private function twitter( $widgets ) {

		$widgets['wts-twitter'] = [
			'conditions' => [ 'widgetType' => 'wts-twitter' ],
			'fields'     => [
				[
					'field'       => 'username',
					'type'        => __( 'Twitter: Username', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'hashtag',
					'type'        => __( 'Twitter: Hashtag', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'dm_username',
					'type'        => __( 'Twitter: Username', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'dm_prefill_text',
					'type'        => __( 'Twitter: Prefill Text', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'share_username',
					'type'        => __( 'Twitter: Share Username', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'share_prefill_text',
					'type'        => __( 'Twitter: Share Prefill Text', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'share_hashtags',
					'type'        => __( 'Twitter: Share Hashtag Text', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'prefill_text',
					'type'        => __( 'Twitter: Prefill Text', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'prefill_custom',
					'type'        => __( 'Twitter: Prefill Custom Text', 'wts-eae' ),
					'editor_type' => 'AREA',
				],

			],
		];

		return $widgets;
	}

	private function post_list( $widgets ) {

		$widgets['wts-postlist'] = [
			'conditions' => [ 'widgetType' => 'wts-postlist' ],
			'fields'     => [
				[
					'field'       => 'read_more_text',
					'type'        => __( 'Post List: Read More Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],

			],
		];

		return $widgets;
	}

	private function animated_text( $widgets ) {

		$widgets['wts-AnimatedText'] = [

			'conditions'        => [ 'widgetType' => 'wts-AnimatedText' ],
			'fields'            => [
				[
					'field'       => 'pre-text',
					'type'        => __( 'Animated Text: Pre Text', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'post-text',
					'type'        => __( 'Animated Text: Post Text', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
			],
			'integration-class' => '\WTS_EAE\WPML_EAE_Animated_Text',
		];

		return $widgets;
	}

	private function gmap( $widgets ) {

		$widgets['wts-gmap'] = [

			'conditions'        => [ 'widgetType' => 'wts-gmap' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Gmap',
		];

		return $widgets;
	}

	private function filterable_gallery( $widgets ) {

		$widgets['eae-filterableGallery'] = [

			'conditions'        => [ 'widgetType' => 'eae-filterableGallery' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Filterable_Gallery',
		];

		return $widgets;
	}

	private function price_table( $widgets ) {

		$widgets['wts-pricetable'] = [

			'conditions'        => [ 'widgetType' => 'wts-pricetable' ],
			'fields'            => [
				[
					'field'       => 'heading',
					'type'        => __( 'Price Table: Plan Heading', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sub-heading',
					'type'        => __( 'Price Table: Plan Sub Heading', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'price-box-text',
					'type'        => __( 'Price Table: Price Box Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'price-box-subtext',
					'type'        => __( 'Price Table: Price Box SubText', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'action_text',
					'type'        => __( 'Price Table: Button Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => '\WTS_EAE\WPML_EAE_Price_Table',
		];

		return $widgets;
	}

	private function timeline( $widgets ) {

		$widgets['eae-timeline'] = [

			'conditions'        => [ 'widgetType' => 'eae-timeline' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Timeline',
		];

		return $widgets;
	}

	private function info_circle( $widgets ) {

		$widgets['eae-info-circle'] = [

			'conditions'        => [ 'widgetType' => 'eae-info-circle' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Info_Circle',
		];

		return $widgets;
	}

	private function comparison_table( $widgets ) {
		
		$widgets['eae-comparisontable'] = [

			'conditions'        => [ 'widgetType' => 'eae-comparisontable' ],
			'fields'            => [
				[
					'field'       => 'feature_box_heading',
					'type'        => __( 'Comparison Table: Feature Box Heading', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_heading_text',
					'type'        => __( 'Comparison Table: Button Heading', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				// [
				// 	'field'       => 'table_price_1',
				// 	'type'        => __( 'Comparison Table: Plan 1 Price', 'wts-eae' ),
				// 	'editor_type' => 'LINE',
				// ],
			],
			'integration-class' => [
										'\WTS_EAE\WPML_EAE_Comparison_Table',
										'\WTS_EAE\WPML_EAE_Comparison_Table_Plan_One',
										'\WTS_EAE\WPML_EAE_Comparison_Table_Plan_Two',
										'\WTS_EAE\WPML_EAE_Comparison_Table_Plan_Three',
										'\WTS_EAE\WPML_EAE_Comparison_Table_Plan_Four',
										'\WTS_EAE\WPML_EAE_Comparison_Table_Plan_Five',
										'\WTS_EAE\WPML_EAE_Comparison_Table_Plan_Six',
										'\WTS_EAE\WPML_EAE_Comparison_Table_Plan_Seven',
										'\WTS_EAE\WPML_EAE_Comparison_Table_Plan_Eight',
										'\WTS_EAE\WPML_EAE_Comparison_Table_Plan_Nine',
										'\WTS_EAE\WPML_EAE_Comparison_Table_Plan_Ten',
			]
		];
		//while loop

		for($i = 0; $i<=10; $i++){
			$fields = [
				[
					'field'       => 'table_price_'.$i,
					'type'        => __( 'Comparison Table: Plan '. $i .' Price', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'table_duration_'.$i,
					'type'        => __( 'Comparison Table: Plan '. $i .' Duration', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'button_text_'.$i,
					'type'        => __( 'Comparison Table: Plan '.$i.' Button Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'table_original_price_'.$i,
					'type'        => __( 'Comparison Table: Plan '.$i.' Orignal Price', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'table_ribbon_text_'.$i,
					'type'        => __( 'Comparison Table: Plan '.$i.' Ribbon Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			];

			$widgets['eae-comparisontable']['fields'] = array_merge($widgets['eae-comparisontable']['fields'], $fields);
		}
		
		return $widgets;
	}

	private function call_to_action( $widgets ){
		$widgets['eae-call-to-action'] = [
			'conditions' => [ 'widgetType' => 'eae-call-to-action' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => __( 'Call To Action: Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'Sub_title',
					'type'        => __( 'Call To Action: Sub Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'description',
					'type'        => __( 'Call To Action: Description', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'pri_btn_title',
					'type'        => __( 'Call To Action: Primary Button', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sec_btn_title',
					'type'        => __( 'Call To Action: Secondary Button', 'wts-eae' ),
					'editor_type' => 'LINE',
				],

			],
		];

		return $widgets;
	}

	private function circular_progress( $widgets ){
		$widgets['eae-circular-progress'] = [
			'conditions' => [ 'widgetType' => 'eae-circular-progress' ],
			'fields'     => [
				[
					'field'       => 'cp_title',
					'type'        => __( 'Circular Progress: Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'cp_description',
					'type'        => __( 'Circular Progress: Description', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $widgets;
	}

	private function devices( $widgets ){
		$widgets['eae-devices'] = [
			'conditions' => [ 'widgetType' => 'eae-devices' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => __( 'Device : Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $widgets;
	}

	private function image_hotspot( $widgets ){
		$widgets['eae-image-hotspot'] = [
			'conditions' => [ 'widgetType' => 'eae-image-hotspot' ],
			'fields'     => [
				[
					'field'       => 'previous_button_text',
					'type'        => __( 'Image Hotspot : Previous Text ', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'next_button_text',
					'type'        => __( 'Image Hotspot : Next Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'end_tour_text',
					'type'        => __( 'Image Hotspot : End Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
			'integration-class' => '\WTS_EAE\WPML_EAE_Image_Hotspot',
		];

		return $widgets;
	}

	private function image_stack( $widgets ){
		$widgets['eae-image-stack'] = [
			'conditions' => [ 'widgetType' => 'eae-image-stack' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Image_Stack',
		];

		return $widgets;
	}

	private function info_group( $widgets ){
		$widgets['eae-info-group'] = [
			'conditions' => [ 'widgetType' => 'eae-info-group' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Info_Group',
		];

		return $widgets;
	}

	private function testimonial_slider( $widgets ){
		$widgets['eae-testimonial'] = [
			'conditions' => [ 'widgetType' => 'eae-testimonial' ],
			'fields'            => [],
			'integration-class' => '\WTS_EAE\WPML_EAE_Testimonial_Slider',
		];

		return $widgets;
	}

	private function coupon_code( $widgets ){
		$widgets['eae-coupon-code'] = [
			'conditions' => [ 'widgetType' => 'eae-coupon-code' ],
			'fields'     => [
				[
					'field'       => 'sta_title',
					'type'        => __( 'Coupon Code: Button Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'after_copy_button',
					'type'        => __( 'Coupon Code: After Copy', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'sta_expire_date_msg',
					'type'        => __( 'Coupon Code: Expire Message', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'pop_title',
					'type'        => __( 'Coupon Code: Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pop_des',
					'type'        => __( 'Coupon Code: Description', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'pop_visit_button',
					'type'        => __( 'Coupon Code: Visit Button', 'wts-eae' ),
					'editor_type' => 'LINE',
				],

				[
					'field'       => 'peel_title',
					'type'        => __( 'Coupon Code: Button Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'peel_after_copy_button',
					'type'        => __( 'Coupon Code: After Copy', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'peel_expire_date_msg',
					'type'        => __( 'Coupon Code: Expire Message', 'wts-eae' ),
					'editor_type' => 'AREA',
				],
				[
					'field'       => 'peel_title',
					'type'        => __( 'Coupon Code: Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'peel_des',
					'type'        => __( 'Coupon Code: Description', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'peel_visit_button',
					'type'        => __( 'Coupon Code: Visit Button', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		

		return $widgets;
	}

	private function google_review( $widgets ){
		$widgets['eae-google-reviews'] = [
			'conditions' => [ 'widgetType' => 'eae-google-reviews' ],
			'fields'     => [
				[
					'field'       => 'button_text',
					'type'        => __( 'Google Review : Button Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'heading',
					'type'        => __( 'Google Review : Header Custom Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'header_text',
					'type'        => __( 'Google Review : Header  Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'header_button_text',
					'type'        => __( 'Google Review : Header Button Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];

		return $widgets;
	}
	private function woo_category( $widgets ){
		$widgets['eae-woo-category'] = [
			'conditions' => [ 'widgetType' => 'eae-woo-category' ],
			'fields'     => [
				[
					'field'       => 'btn_text',
					'type'        => __( 'Woo Category : Button Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];
		return $widgets;
	}

	private function audio_player( $widgets ) {

		$widgets['eae-audio-player'] = [

			'conditions'        => [ 'widgetType' => 'eae-audio-player' ],
			'fields'            => [
				[
					'field'       => 'album_title',
					'type'        => __( 'Audio Player: Album Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'album_sub_title',
					'type'        => __( 'Audio player: Album Sub Title', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
				[
					'field'       => 'album_list_heading',
					'type'        => __( 'Audio player: List Heading', 'wts-eae' ),
					'editor_type' => 'LINE',
				],

			],
			'integration-class' => '\WTS_EAE\WPML_EAE_Audio_Player',
		];
    return $widgets;
	}

	private function animated_link( $widgets ){
		$widgets['eae-animated-link'] = [
			'conditions' => [ 'widgetType' => 'eae-animated-link' ],
			'fields'     => [
				[
					'field'       => 'title',
					'type'        => __( 'Animated Link : Text', 'wts-eae' ),
					'editor_type' => 'LINE',
				],
			],
		];
		return $widgets;
	}

}

WPML_Compatibility::instance();

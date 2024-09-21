<?php

namespace WTS_EAE\Classes;

use Elementor\Controls_Manager;
use Elementor\Core\Files\CSS\Global_CSS;
use Elementor\Core\Kits\Documents\Tabs\Colors_And_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use WTS_EAE\Controls\Group\Group_Control_Icon;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Elementor\Plugin as EPlugin;
use WTS_EAE\Plugin as EAE;
class Helper {

	public function __construct() {
		
		add_action( 'wp_ajax_eae_refresh_insta_cache', [ $this, 'ajax_refresh_insta_cache' ] );
		add_action( 'wp_ajax_nopriv_eae_refresh_insta_cache', [ $this, 'ajax_refresh_insta_cache' ] );
		add_action( 'wp_ajax_eae_add_to_cart', [ $this, 'ajax_wp_add_to_cart' ] );
		add_action( 'wp_ajax_nopriv_eae_add_to_cart', [ $this, 'ajax_wp_add_to_cart' ] );

	}

	
	public function ajax_wp_add_to_cart(){

		$nonce = $_POST['eae_nonce'];
		if (!wp_verify_nonce($nonce, 'eae_forntend_ajax_nonce')) {
			wp_send_json_error('Nonce is invalid');
		}
		$product_id = absint($_POST['product_id']);

		$product_qty = absint($_POST['quantity']);
		// echo "prd_id" . $product_id;
		// echo "<br/>";
		// echo "prd_qty" . $product_qty;
		// die('dfafd');
		$cart_item_key = WC()->cart->add_to_cart($product_id, $product_qty);
		// echo '<pre>';  print_r($cart_item_key); echo '</pre>';
		// die('dfadf');
		wp_send_json($cart_item_key);

		wp_die();
	}

	public function ajax_refresh_insta_cache() {
		
		// Verify the nonce
		if (wp_verify_nonce($_REQUEST['nonce'], 'wp_eae_elementor_editor_nonce') == false) {
			return wp_send_json_error('Invalid Nonce');
		}
		// Get Current Post ID
		$document = EPlugin::$instance->documents->get( $_REQUEST['post_id'] );
		if ( ! $document || ! $document->is_editable_by_current_user() ) {
			return wp_send_json_error('Invalid User, You are not allowed to edit this post');
		}
		
		// Sanitize the transient key
		$transient_key = sanitize_text_field($_REQUEST['transient_key']);
	
		// Check if the transient key starts with 'eae_insta_fetched_data_'
		if (strpos($transient_key, 'eae_insta_fetched_data_') !== 0) {
			return wp_send_json_error('Invalid Transient Key');
		}
		
		// Delete the transient
		$result = delete_transient($transient_key);
		
		// Return the result
		return wp_send_json_success($result);
	}

	
	public function eae_get_post_data( $args ) {
		$defaults = [
			'posts_per_page'   => 5,
			'offset'           => 0,
			'category'         => '',
			'category_name'    => '',
			'orderby'          => 'date',
			'order'            => 'DESC',
			'include'          => '',
			'exclude'          => '',
			'meta_key'         => '',
			'meta_value'       => '',
			'post_type'        => 'post',
			'post_mime_type'   => '',
			'post_parent'      => '',
			'author'           => '',
			'author_name'      => '',
			'post_status'      => 'publish',
			'suppress_filters' => false,
		];

		$atts = wp_parse_args( $args, $defaults );

		$posts = get_posts( $atts );

		return $posts;
	}

	public function eae_get_post_types() {
		$args = [
			'public' => true,
		];

		$skip_post_types = [ 'attachment' ];

		$post_types = get_post_types( $args );

		return $post_types;
	}

	/**
	 * @param mixed $settings
	 * 
	 * @return [type]
	 */
	public function eae_get_post_settings( $settings ) {
		$post_args['post_type'] = $settings['post_type'];

		if ( $settings['post_type'] === 'post' ) {
			$post_args['category'] = $settings['category'];
		}

		$post_args['posts_per_page'] = $settings['num_posts'];
		$post_args['offset']         = $settings['post_offset'];
		$post_args['orderby']        = $settings['orderby'];
		$post_args['order']          = $settings['order'];

		return $post_args;
	}

	
	/**
	 * @param mixed $post_id
	 * @param mixed $excerpt_length
	 * 
	 * @return [type]
	 */
	public function eae_get_excerpt_by_id( $post_id, $excerpt_length ) {
		$the_post = get_post( $post_id ); //Gets post ID

		$the_excerpt = null;
		if ( $the_post ) {
			$the_excerpt = $the_post->post_excerpt ? $the_post->post_excerpt : $the_post->post_content;
		}

		$the_excerpt = wp_strip_all_tags( strip_shortcodes( $the_excerpt ) ); //Strips tags and images
		$words       = explode( ' ', $the_excerpt, $excerpt_length + 1 );

		if ( count( $words ) > $excerpt_length ) :
			array_pop( $words );
			$the_excerpt  = implode( ' ', $words );
			$the_excerpt .= '...';  // Don't put a space before
		endif;

		return $the_excerpt;
	}

	public function eae_get_thumbnail_sizes() {
		$sizes = get_intermediate_image_sizes();
		foreach ( $sizes as $s ) {
			$ret[ $s ] = $s;
		}

		return $ret;
	}

	public function eae_get_post_orderby_options() {
		$orderby = [
			'ID'            => 'Post Id',
			'author'        => 'Post Author',
			'title'         => 'Title',
			'date'          => 'Date',
			'modified'      => 'Last Modified Date',
			'parent'        => 'Parent Id',
			'rand'          => 'Random',
			'comment_count' => 'Comment Count',
			'menu_order'    => 'Menu Order',
		];

		return $orderby;
	}

	public function get_icon_html( $settings, $control_name, $default, $all_settings ) {

		$icon_html = '';
		$skin_type = $all_settings['_skin'];
		// --------------New Work-----------------

		$view  = 'eae-icon-view-' . $default['view'];
		$shape = 'eae-icon-shape-' . $default['shape'];

		$icon_migrated = isset( $all_settings['__fa4_migrated'][ $skin_type . '_global_icon_new' ] );
		$icon_is_new   = empty( $all_settings[ $skin_type . '_global_icon' ] );

		$item_icon_migrated = isset( $settings['__fa4_migrated'][ $control_name . '_icon_new' ] );
		$item_icon_is_new   = empty( $settings[ $control_name . '_icon' ] );
		if ( ! isset( $settings[ $control_name . '_eae_icon' ] ) || $settings[ $control_name . '_eae_icon' ] === '' ) {

			switch ( $default['icon_type'] ) {

				case 'image':
					$icon_html = '<i><img src="' . esc_url($default['image']['url']) . '"/></i>';
					break;

				case 'text':
					$icon_html = '<i class="">' . esc_attr( $default['text'] ) . '</i>';
					break;
			}

			$view      = 'eae-icon-view-' . $default['view'];
			$shape     = 'eae-icon-shape-' . $default['shape'];
			$icon_type = 'eae-icon-type-' . $default['icon_type'];
			$icon_name = 'eae-icon-' . $control_name;

			if ( $default['icon_new'] !== '' ) {

				?>
				<div class="eae-icon <?php echo esc_attr($icon_name . ' ' . $view . ' ' . $shape . ' ' . $icon_type); ?>">
					<div class="eae-icon-wrap">
						<?php if ( $default['icon_type'] === 'icon' ) { ?>
							<?php
							if ( $icon_migrated || $icon_is_new ) :
								Icons_Manager::render_icon( $all_settings[ $skin_type . '_global_icon_new' ], [ 'aria-hidden' => 'true' ] );
							else :
								?>
								<i class="<?php echo esc_attr($default['icon']); ?>"></i>
							<?php endif; ?>
							<?php
						} else {
							echo $icon_html;
						}
						?>
					</div>
				</div>
				<?php
			}
		} else {
			switch ( $settings[ $control_name . '_icon_type' ] ) {

				case 'image':
					$icon_html = '<i><img src="' . esc_url($settings[ $control_name . '_image' ]['url']) . '" /></i>';
					break;

				case 'text':
					$icon_html = '<i class="">' . esc_attr( $settings[ $control_name . '_text' ] ) . '</i>';
					break;
			}

			if ( $settings[ $control_name . '_view' ] !== 'global' ) {
				$view = 'eae-icon-view-' . $settings[ $control_name . '_view' ];
			}

			if ( $settings[ $control_name . '_shape' ] !== 'global' ) {
				$shape = 'eae-icon-shape-' . $settings[ $control_name . '_shape' ];
			}

			$icon_type = 'eae-icon-type-' . $settings[ $control_name . '_icon_type' ];

			$icon_name = 'eae-icon-' . $control_name;
		}
		if ( isset( $settings[ $control_name . '_eae_icon' ] ) && $settings[ $control_name . '_eae_icon' ] !== '' ) {
			?>
			<div class="eae-icon <?php echo esc_attr($icon_name . ' ' . $view . ' ' . $shape . ' ' . $icon_type); ?>">
				<div class="eae-icon-wrap">
					<?php
					if ( $settings[ $control_name . '_icon_type' ] === 'icon' ) {
						?>
						<?php
						if ( $item_icon_migrated || $item_icon_is_new ) :
							Icons_Manager::render_icon( $settings[ $control_name . '_icon_new' ], [ 'aria-hidden' => 'true' ] );
						else :
							?>
							<i class="<?php echo esc_attr($settings[ $control_name . '_icon' ]); ?>"></i>
						<?php endif; ?>
						<?php
					} else {
						echo $icon_html;
					}
					?>
				</div>
			</div>
			<?php
		}
	}

	public function get_icon_timeline_html( $settings, $control_name ) {

		$icon_html = '';
		$icon_data = '';

		if ( $settings[ $control_name . '_eae_icon' ] === '' ) {
			$settings[ $control_name . '_icon' ]      = 'fa fa-star';
			$settings[ $control_name . '_view' ]      = 'stacked';
			$settings[ $control_name . '_shape' ]     = 'cricle';
			$settings[ $control_name . '_icon_type' ] = 'icon';
			$icon_data                                = '<i class="' . $settings[ $control_name . '_icon' ] . ' hvr-icon"></i>';
		} else {

			if ( $settings[ $control_name . '_icon_type' ] === 'icon' ) {
				if ( $settings[ $control_name . '_icon' ] === '' ) {
					$settings[ $control_name . '_icon' ]  = 'fa fa-star';
					$settings[ $control_name . '_view' ]  = 'stacked';
					$settings[ $control_name . '_shape' ] = '';
				}
				if ( $settings[ $control_name . '_icon' ] !== '' ) {
					$icon_data = '<i class="' . $settings[ $control_name . '_icon' ] . ' hvr-icon"></i>';
				}
			} elseif ( $settings[ $control_name . '_icon_type' ] === 'image' ) {
				if ( $settings[ $control_name . '_image' ]['id'] !== '' ) {
					$icon_data = wp_get_attachment_image( $settings[ $control_name . '_image' ]['id'] );
				}
			} elseif ( $settings[ $control_name . '_icon_type' ] === 'text' ) {
				if ( $settings[ $control_name . '_text' ] !== '' ) {
					$icon_data = $settings[ $control_name . '_text' ];
				}
			} else {
				$icon_data = '';
			}
		}

		if ( $icon_data !== '' ) {
			$icon_html .= '<span class="eae-icon-wrapper eae-icon-' . $control_name . '_wrapper eae-icon-view-stacked elementor-shape-' . $settings[ $control_name . '_shape' ] . ' eae-icon-type-' . $settings[ $control_name . '_icon_type' ] . '">';
			$icon_html .= '<span class="elementor-icon eae-icon elementor-animation-' . $settings[ $control_name . '_icon_hover_animation' ] . '">';
			$icon_html .= $icon_data;
			$icon_html .= '</span>';
			$icon_html .= '</span>';
		}

		return Helper::eae_wp_kses( $icon_html );
	}



	public function group_icon_styles( $widget, $args ) {

		$defaults = [
			'primary_color'         => true,
			'secondary_color'       => true,
			'hover_primary_color'   => true,
			'hover_secondary_color' => true,
			'hover_animation'       => true,
			'focus_primary_color'   => false,
			'focus_secondary_color' => false,
			'icon_size'             => true,
			'icon_padding'          => true,
			'rotate'                => true,
			'border_width'          => true,
			'border_style'          => true,
			'border_radius'         => true,
			'name'                  => 'icon',
			'tabs'                  => true,
			'custom_style_switch'   => false,
			'focus_item_class'      => '',
		];

		$args = wp_parse_args( $args, $defaults );

		$control_name = $args['name'];

		$widget->start_controls_tabs( $control_name . 'icon_colors' );

		$widget->start_controls_tab(
			$control_name . '_icon_colors_normal',
			[
				'label' => __( 'Normal', 'wts-eae' ),
			]
		);

		if ( $args['primary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_primary_color',
				[
					'label'     => __( 'Primary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'global'    => [
						'default' => Global_Colors::COLOR_PRIMARY,
					],
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-stacked' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-framed'  => 'border-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-framed i'  => 'color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-framed svg'  => 'fill: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-default i' => 'color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-default svg' => 'fill: {{VALUE}};',
					],
				]
			);
		}

		if ( $args['secondary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_secondary_color',
				[
					'label'     => __( 'Secondary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#fff',
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-framed'  => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-stacked i' => 'color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-stacked svg' => 'fill: {{VALUE}};',
					],
				]
			);
		}

		$widget->end_controls_tab();

		if ( $args['hover_primary_color'] || $args['hover_secondary_color'] ) {
			$widget->start_controls_tab(
				$control_name . '_icon_colors_hover',
				[
					'label' => __( 'Hover', 'wts-eae' ),
				]
			);
		}
		if ( $args['hover_primary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_hover_primary_color',
				[
					'label'     => __( 'Primary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-stacked:hover' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-framed:hover'  => 'border-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-framed:hover i'  => 'color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-framed:hover svg'  => 'fill: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-default:hover i' => 'color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-default:hover svg' => 'fill: {{VALUE}};',
					],
				]
			);
		}

		if ( $args['hover_secondary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_hover_secondary_color',
				[
					'label'     => __( 'Secondary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-stacked:hover i'  => 'color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-stacked:hover svg'  => 'fill: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon-view-framed:hover' => 'background-color: {{VALUE}};',
					],
				]
			);
		}

		if ( $args['hover_animation'] ) {
			$widget->add_control(
				$control_name . '_icon_hover_animation',
				[
					'label' => __( 'Hover Animation', 'wts-eae' ),
					'type'  => Controls_Manager::HOVER_ANIMATION,
				]
			);
		}

		$widget->end_controls_tab();

		if ( $args['focus_item_class'] !== '' ) {

			$widget->start_controls_tab(
				$control_name . '_icon_colors_focus',
				[
					'label' => __( 'Focus', 'wts-eae' ),
				]
			);

			if ( $args['focus_primary_color'] ) {
				$widget->add_control(
					$control_name . '_icon_focus_primary_color',
					[
						'label'     => __( 'Primary Color', 'wts-eae' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_PRIMARY,
						],
						'selectors' => [
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-stacked' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-framed'  => 'border-color: {{VALUE}};',
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-framed i'  => 'color: {{VALUE}};',
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-framed svg'  => 'fill: {{VALUE}};',
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-default i' => 'color: {{VALUE}}; border-color: {{VALUE}};',
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-default svg' => 'fill: {{VALUE}}; border-color: {{VALUE}};',
						],
					]
				);
			}

			if ( $args['focus_secondary_color'] ) {
				$widget->add_control(
					$control_name . '_icon_focus_secondary_color',
					[
						'label'     => __( 'Secondary Color', 'wts-eae' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
						'selectors' => [
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-framed'  => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-stacked i   ' => 'color: {{VALUE}};',
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-stacked svg   ' => 'fill: {{VALUE}};',
						],
					]
				);
			}

			$widget->end_controls_tab();
		}

		$widget->end_controls_tabs();

		if ( $args['icon_size'] ) {
			$widget->add_responsive_control(
				$control_name . '_icon_size',
				[
					'label'     => __( 'Size', 'wts-eae' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min' => 5,
							'max' => 100,
						],
					],
					'separator' => 'before',
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon i' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon svg' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		if ( $args['icon_padding'] ) {
			$widget->add_control(
				$control_name . '_icon_padding',
				[
					'label'     => __( 'Padding', 'wts-eae' ),
					'type'      => Controls_Manager::SLIDER,
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon' => 'padding: {{SIZE}}{{UNIT}};',
					],
					'range'     => [
						'px' => [
							'min' => 5,
							'max' => 100,
						],
					],
				]
			);
		}

		if ( $args['rotate'] ) {
			$widget->add_control(
				$control_name . '_icon_rotate',
				[
					'label'     => __( 'Rotate', 'wts-eae' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => [
						'size' => 0,
						'unit' => 'deg',
					],
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
					],
				]
			);
		}

		if ( $args['border_style'] ) {
			$widget->add_control(
				$control_name . '_border_style',
				[
					'label'     => __( 'Border Style', 'wts-eae' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						'none'   => __( 'None', 'wts-eae' ),
						'solid'  => __( 'Solid', 'wts-eae' ),
						'double' => __( 'Double', 'wts-eae' ),
						'dotted' => __( 'Dotted', 'wts-eae' ),
						'dashed' => __( 'Dashed', 'wts-eae' ),
					],
					'default'   => 'solid',
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon' => 'border-style: {{VALUE}};',
					],

				]
			);
		}

		if ( $args['border_width'] ) {
			$widget->add_control(
				$control_name . '_border_width',
				[
					'label'     => __( 'Border Width', 'wts-eae' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'default'   => [
						'value' => 20,
					],
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],

				]
			);
		}

		if ( $args['border_radius'] ) {
			$widget->add_control(
				$control_name . '_icon_border_radius',
				[
					'label'      => __( 'Border Radius', 'wts-eae' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '.eae-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		}
	}

	public function group_icon_styles_repeater( $widget, $args ) {

		$defaults = [
			'primary_color'         => true,
			'secondary_color'       => true,
			'hover_primary_color'   => true,
			'hover_secondary_color' => true,
			'focus_primary_color'   => false,
			'focus_secondary_color' => false,
			'hover_animation'       => true,
			'icon_size'             => true,
			'icon_padding'          => true,
			'rotate'                => true,
			'border_style'          => true,
			'border_width'          => true,
			'border_radius'         => true,
			'name'                  => 'icon',
			'tabs'                  => true,
			'label'                 => 'Icon',
			'custom_style_switch'   => true,
			'focus_item_class'      => '',

		];

		$args = wp_parse_args( $args, $defaults );

		$control_name  = $args['name'];
		$control_label = $args['label'];

		$widget->add_control(
			$control_name . 'custom_styles',
			[
				'label'     => __( 'Custom Icon Style', 'wts-eae' ),
				'type'      => Controls_Manager::SWITCHER,
				'label_off' => __( 'No', 'wts-eae' ),
				'label_on'  => __( 'Yes', 'wts-eae' ),
				'default'   => '',
			]
		);

		if ( $args['tabs'] ) {
			$widget->start_controls_tabs( $control_name . 'icon_colors' );

			$widget->start_controls_tab(
				$control_name . '_icon_colors_normal',
				[
					'label' => __( 'Normal', 'wts-eae' ),
				]
			);
		} else {

			$widget->add_control(
				$control_name . '_icon_colors_normal',
				[
					'label'     => __( 'Normal', 'wts-eae' ),
					'type'      => Controls_Manager::HEADING,
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}

		if ( $args['primary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_primary_color',
				[
					'label'     => __( 'Primary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-stacked' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-framed'  => 'border-color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-framed i'  => 'color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-framed svg'  => 'fill: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-default i' => 'color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-default svg' => 'fill: {{VALUE}};',
					],
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}

		if ( $args['secondary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_secondary_color',
				[
					'label'     => __( 'Secondary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'condition' => [
						$control_name . '_view!' => 'default',
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-framed'  => 'background-color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-stacked i' => 'color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-stacked svg' => 'color: {{VALUE}};',
					],
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}

		if ( $args['tabs'] ) {

			$widget->end_controls_tab();
		}

		if ( $args['tabs'] ) {
			$widget->start_controls_tab(
				$control_name . '_icon_colors_hover',
				[
					'label'     => __( 'Hover', 'wts-eae' ),
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		} else {
			if ( $args['hover_primary_color'] || $args['hover_secondary_color'] ) {
				$widget->add_control(
					$control_name . '_icon_colors_hover',
					[
						'label'     => __( 'Hover', 'wts-eae' ),
						'type'      => Controls_Manager::HEADING,
						'condition' => [
							$control_name . 'custom_styles' => 'yes',
						],
					]
				);
			}
		}

		if ( $args['hover_primary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_hover_primary_color',
				[
					'label'     => __( 'Primary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-stacked:hover' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-framed:hover'  => 'border-color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-framed:hover i'  => 'color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-framed:hover svg'  => 'fill: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-default:hover i' => 'color: {{VALUE}}; border-color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-default:hover svg' => 'fill: {{VALUE}}; border-color: {{VALUE}};',
					],
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}

		if ( $args['hover_secondary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_hover_secondary_color',
				[
					'label'     => __( 'Secondary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'condition' => [
						$control_name . '_view!' => 'default',
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-framed:hover'  => 'background-color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-stacked:hover i' => 'color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-stacked:hover svg' => 'fill: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-default:hover i' => 'color: {{VALUE}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon-view-default:hover svg' => 'fill: {{VALUE}};',
					],
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}

		if ( $args['hover_animation'] ) {
			$widget->add_control(
				$control_name . '_icon_hover_animation',
				[
					'label'     => __( 'Hover Animation', 'wts-eae' ),
					'type'      => Controls_Manager::HOVER_ANIMATION,
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}

		if ( $args['tabs'] ) {

			$widget->end_controls_tab();
		}

		if ( $args['focus_item_class'] !== '' ) {
			if ( $args['tabs'] ) {

				$widget->start_controls_tab(
					$control_name . '_icon_colors_focus',
					[
						'label' => __( 'Focus', 'wts-eae' ),
					]
				);
			} else {

				$widget->add_control(
					$control_name . '_icon_colors_focus',
					[
						'label'     => __( 'Focus', 'wts-eae' ),
						'type'      => Controls_Manager::HEADING,
						'condition' => [
							$control_name . 'custom_styles' => 'yes',
						],
					]
				);
			}

			if ( $args['focus_primary_color'] ) {
				$widget->add_control(
					$control_name . '_icon_focus_primary_color',
					[
						'label'     => __( 'Primary Color', 'wts-eae' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}}.' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-stacked' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} {{CURRENT_ITEM}}.' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-framed'  => 'border-color: {{VALUE}};',
							'{{WRAPPER}} {{CURRENT_ITEM}}.' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-framed i'  => 'color: {{VALUE}};',
							'{{WRAPPER}} {{CURRENT_ITEM}}.' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-framed svg'  => 'fill: {{VALUE}};',
							'{{WRAPPER}} {{CURRENT_ITEM}}.' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-default i' => 'color: {{VALUE}}; border-color: {{VALUE}};',
							'{{WRAPPER}} {{CURRENT_ITEM}}.' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-default svg' => 'fill: {{VALUE}}; border-color: {{VALUE}};',
						],
						'condition' => [
							$control_name . 'custom_styles' => 'yes',
						],
					]
				);
			}

			if ( $args['focus_secondary_color'] ) {
				$widget->add_control(
					$control_name . '_icon_focus_secondary_color',
					[
						'label'     => __( 'Secondary Color', 'wts-eae' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'condition' => [
							$control_name . '_view!' => 'default',
						],
						'selectors' => [
							'{{WRAPPER}} {{CURRENT_ITEM}}.' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-framed'  => 'background-color: {{VALUE}};',
							'{{WRAPPER}} {{CURRENT_ITEM}}.' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-stacked i' => 'color: {{VALUE}};',
							'{{WRAPPER}} {{CURRENT_ITEM}}.' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '.eae-icon-view-stacked svg' => 'fill: {{VALUE}};',
						],
						'condition' => [
							$control_name . 'custom_styles' => 'yes',
						],
					]
				);
			}

			if ( $args['tabs'] ) {

				$widget->end_controls_tab();
			}
		}
		if ( $args['tabs'] ) {

			$widget->end_controls_tabs();
		}

		if ( $args['icon_size'] ) {
			$widget->add_responsive_control(
				$control_name . '_icon_size',
				[
					'label'     => __( 'Size', 'wts-eae' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min' => 6,
							'max' => 300,
						],
					],
					'separator' => 'before',
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon i, {{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon' => 'font-size: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon svg' => 'width: {{SIZE}}{{UNIT}};',
					],
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}

		if ( $args['icon_padding'] ) {
			$widget->add_control(
				$control_name . '_icon_padding',
				[
					'label'     => __( 'Padding', 'wts-eae' ),
					'type'      => Controls_Manager::SLIDER,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon' => 'padding: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} {{CURRENT_ITEM}} .bpe-layout-left .bpe-timline-progress-bar' => 'left: calc({{SIZE}}{{UNIT}} / 2); right: auto;',
						'{{WRAPPER}} {{CURRENT_ITEM}} .bpe-layout-right .bpe-timline-progress-bar' => 'left: auto; right: calc({{SIZE}}{{UNIT}} / 2);',
					],
					'range'     => [
						'em' => [
							'min' => 0,
							'max' => 5,
						],
					],
					'condition' => [
						$control_name . '_view!'        => 'default',
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}

		if ( $args['rotate'] ) {
			$widget->add_control(
				$control_name . '_icon_rotate',
				[
					'label'     => __( 'Rotate', 'wts-eae' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => [
						'size' => 0,
						'unit' => 'deg',
					],
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
					],
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}
		if ( $args['border_style'] ) {
			$widget->add_control(
				$control_name . '_border_style',
				[
					'label'     => __( 'Border Style', 'wts-eae' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						'none'   => __( 'None', 'wts-eae' ),
						'solid'  => __( 'Solid', 'wts-eae' ),
						'double' => __( 'Double', 'wts-eae' ),
						'dotted' => __( 'Dotted', 'wts-eae' ),
						'dashed' => __( 'Dashed', 'wts-eae' ),
					],
					'default'   => 'solid',
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon' => 'border-style: {{VALUE}};',
					],
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],

				]
			);
		}

		if ( $args['border_width'] ) {
			$widget->add_control(
				$control_name . '_border_width',
				[
					'label'     => __( 'Border Width', 'wts-eae' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => [
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}

		if ( $args['border_radius'] ) {
			$widget->add_control(
				$control_name . '_icon_border_radius',
				[
					'label'      => __( 'Border Radius', 'wts-eae' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} {{CURRENT_ITEM}} .eae-icon-' . $control_name . '.eae-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'  => [
						$control_name . '_view!'        => 'default',
						$control_name . 'custom_styles' => 'yes',
					],
				]
			);
		}
	}

	public function group_icon_timeline_styles( $widget, $args ) {

		$defaults = [
			'primary_color'         => true,
			'secondary_color'       => true,
			'hover_primary_color'   => true,
			'hover_secondary_color' => true,
			'hover_animation'       => true,
			'focus_primary_color'   => false,
			'focus_secondary_color' => false,
			'icon_size'             => true,
			'icon_padding'          => true,
			'rotate'                => true,
			'border_width'          => true,
			'border_radius'         => true,
			'name'                  => 'icon',
			'tabs'                  => true,
			'custom_style_switch'   => false,
			'focus_item_class'      => '',
		];

		$args = wp_parse_args( $args, $defaults );

		$control_name = $args['name'];
		$widget->start_controls_tabs( $control_name . 'icon_colors' );

		$widget->start_controls_tab(
			$control_name . '_icon_colors_normal',
			[
				'label' => __( 'Normal', 'wts-eae' ),
			]
		);

		if ( $args['primary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_primary_color',
				[
					'label'     => __( 'Primary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#6ec1e4',
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.eae-icon-view-stacked' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.eae-icon-view-framed .elementor-icon'  => 'color: {{VALUE}}; border-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					],
				]
			);
		}

		if ( $args['secondary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_secondary_color',
				[
					'label'     => __( 'Secondary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '#ffffff',
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.eae-icon-view-framed'  => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.eae-icon-view-stacked .elementor-icon' => 'color: {{VALUE}};',
					],
				]
			);
		}

		$widget->end_controls_tab();

		$widget->start_controls_tab(
			$control_name . '_icon_colors_hover',
			[
				'label' => __( 'Hover', 'wts-eae' ),
			]
		);

		if ( $args['hover_primary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_hover_primary_color',
				[
					'label'     => __( 'Primary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.eae-icon-view-stacked:hover' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.eae-icon-view-framed .elementor-icon:hover'  => 'color: {{VALUE}}; border-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.elementor-view-default .elementor-icon:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					],
				]
			);
		}

		if ( $args['hover_secondary_color'] ) {
			$widget->add_control(
				$control_name . '_icon_hover_secondary_color',
				[
					'label'     => __( 'Secondary Color', 'wts-eae' ),
					'type'      => Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.eae-icon-view-framed:hover'  => 'background-color: {{VALUE}};',
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.eae-icon-view-stacked .elementor-icon:hover' => 'color: {{VALUE}};',
					],
				]
			);
		}

		if ( $args['hover_animation'] ) {
			$widget->add_control(
				$control_name . '_icon_hover_animation',
				[
					'label' => __( 'Hover Animation', 'wts-eae' ),
					'type'  => Controls_Manager::HOVER_ANIMATION,
				]
			);
		}

		$widget->end_controls_tab();

		if ( $args['focus_item_class'] !== '' ) {

			$widget->start_controls_tab(
				$control_name . '_icon_colors_focus',
				[
					'label' => __( 'Focus', 'wts-eae' ),
				]
			);

			if ( $args['focus_primary_color'] ) {
				$widget->add_control(
					$control_name . '_icon_focus_primary_color',
					[
						'label'     => __( 'Primary Color', 'wts-eae' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '_wrapper.eae-icon-view-stacked' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '_wrapper.eae-icon-view-framed'  => 'color: {{VALUE}}; border-color: {{VALUE}};',
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '_wrapper.elementor-view-default' => 'color: {{VALUE}}; border-color: {{VALUE}};',
						],
					]
				);
			}

			if ( $args['focus_secondary_color'] ) {
				$widget->add_control(
					$control_name . '_icon_focus_secondary_color',
					[
						'label'     => __( 'Secondary Color', 'wts-eae' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '_wrapper.eae-icon-view-framed'  => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .' . $args['focus_item_class'] . ' .eae-icon-' . $control_name . '_wrapper.eae-icon-view-stacked .elementor-icon' => 'color: {{VALUE}};',
						],
					]
				);
			}

			$widget->end_controls_tab();
		}

		$widget->end_controls_tabs();

		if ( $args['icon_size'] ) {
			$widget->add_responsive_control(
				$control_name . '_icon_size',
				[
					'label'     => __( 'Size', 'wts-eae' ),
					'type'      => Controls_Manager::SLIDER,
					'range'     => [
						'px' => [
							'min' => 6,
							'max' => 30,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};',
					],
				]
			);
		}

		if ( $args['icon_padding'] ) {
			$widget->add_control(
				$control_name . '_icon_padding',
				[
					'label'     => __( 'Background Size', 'wts-eae' ),
					'type'      => Controls_Manager::SLIDER,
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper.eae-icon-wrapper' => 'display: inline-block; min-height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .bpe-layout-left .bpe-timline-progress-bar' => 'left: calc({{SIZE}}{{UNIT}} / 2); right: auto;',
						'{{WRAPPER}} .bpe-layout-right .bpe-timline-progress-bar' => 'left: auto; right: calc({{SIZE}}{{UNIT}} / 2);',
					],
					'range'     => [
						'px' => [
							'min' => 30,
							'max' => 100,
						],
					],
				]
			);
		}

		if ( $args['rotate'] ) {
			$widget->add_control(
				$control_name . '_icon_rotate',
				[
					'label'     => __( 'Rotate', 'wts-eae' ),
					'type'      => Controls_Manager::SLIDER,
					'default'   => [
						'size' => 0,
						'unit' => 'deg',
					],
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper .elementor-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
					],
				]
			);
		}

		if ( $args['border_width'] ) {
			$widget->add_control(
				$control_name . '_border_width',
				[
					'label'     => __( 'Border Width', 'wts-eae' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper .elementor-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		}

		if ( $args['border_radius'] ) {
			$widget->add_control(
				$control_name . '_icon_border_radius',
				[
					'label'      => __( 'Border Radius', 'wts-eae' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .eae-icon-' . $control_name . '_wrapper .elementor-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
		}
	}


	public function box_model_controls( $widget, $args ) {

		$defaults = [
			'border'        => true,
			'border-radius' => true,
			'margin'        => true,
			'padding'       => true,
			'box-shadow'    => true,
		];

		$args = wp_parse_args( $args, $defaults );

		if ( $args['border'] ) {
			$widget->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => $args['name'] . '_border',
					'label'     => $args['label'] . ' Border',
					'selector'  => $args['selector'],
					'condition' => [
						'ribbons_badges_switcher!' => '',
					],
				]
			);
		}

		if ( $args['border-radius'] ) {
			$widget->add_control(
				$args['name'] . '_border_radius',
				[
					'label'      => __( 'Border Radius', 'wts-eae' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						$args['selector'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'  => [
						'ribbons_badges_switcher!' => '',
					],
				]
			);
		}

		if ( $args['box-shadow'] ) {
			$widget->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'      => $args['name'] . '_box_shadow',
					'label'     => __( 'Box Shadow', 'wts-eae' ),
					'selector'  => $args['selector'],
					'condition' => [
						'ribbons_badges_switcher!' => '',
					],
				]
			);
		}

		if ( $args['padding'] ) {
			$widget->add_control(
				$args['name'] . '_padding',
				[
					'label'      => __( 'Padding', 'wts-eae' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						$args['selector'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'  => [
						'ribbons_badges_switcher!' => '',
					],
				]
			);
		}

		if ( $args['margin'] ) {
			$widget->add_control(
				$args['name'] . '_margin',
				[
					'label'      => __( 'Margin', 'wts-eae' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						$args['selector'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'  => [
						'ribbons_badges_switcher!' => '',
					],
				]
			);
		}
	}

	// EAE Modules List
	public function get_eae_modules() {
		$modules = [
			'timeline' => [
				'name'    => 'Timeline',
				'enabled' => true,
				'type'    => 'widget',
			],
			'info-circle' => [
				'name'    => 'Info Circle',
				'enabled' => true,
				'type'    => 'widget',
			],
			'comparison-table' => [
				'name'    => 'Comparison Table',
				'enabled' => true,
				'type'    => 'widget',
			],
			'image-compare' => [
				'name'    => 'Image Compare',
				'enabled' => true,
				'type'    => 'widget',
			],
			'animated-text' => [
				'name'    => 'Animated Text',
				'enabled' => true,
				'type'    => 'widget',
			],
			'dual-button' => [
				'name'    => 'Dual Button',
				'enabled' => true,
				'type'    => 'widget',
			],
			'particles' => [
				'name'    => 'Particles',
				'enabled' => true,
				'type'    => 'feature',
			],
			'wrapper-links' => [
				'name'    => 'Wrapper Link',
				'enabled' => true,
				'type'    => 'feature',
			],
			'modal-popup' => [
				'name'    => 'Modal Popup',
				'enabled' => true,
				'type'    => 'widget',
			],
			'progress-bar' => [
				'name'    => 'Progress Bar',
				'enabled' => true,
				'type'    => 'widget',
			],
			'flip-box' => [
				'name'    => 'Flip Box',
				'enabled' => true,
				'type'    => 'widget',
			],
			'split-text' => [
				'name'    => 'Split Text',
				'enabled' => true,
				'type'    => 'widget',
			],
			'gmap' => [
				'name'    => 'Google Map',
				'enabled' => true,
				'type'    => 'widget',
			],
			'text-separator' => [
				'name'    => 'Text Separator',
				'enabled' => true,
				'type'    => 'widget',
			],
			'price-table' => [
				'name'    => 'Price Table',
				'enabled' => true,
				'type'    => 'widget',
			],
			'twitter' => [
				'name'    => 'Twitter',
				'enabled' => true,
				'type'    => 'widget',
			],
			'bg-slider' => [
				'name'    => 'Background Slider',
				'enabled' => true,
				'type'    => 'feature',
			],
			'animated-gradient' => [
				'name'    => 'Animated Gradient',
				'enabled' => true,
				'type'    => 'feature',
			],
			'post-list' => [
				'name'    => 'Post List',
				'enabled' => true,
				'type'    => 'widget',
			],
			'shape-separator' => [
				'name'    => 'Shape Separator',
				'enabled' => true,
				'type'    => 'widget',
			],
			'filterable-gallery' => [
				'name'    => 'Filterable Gallery',
				'enabled' => true,
				'type'    => 'widget',
			],
			'content-switcher' => [
				'name'    => 'Content Switcher',
				'enabled' => true,
				'type'    => 'widget',
			],
			'chart' => [
				'name'    => 'Chart',
				'enabled' => true,
				'type'    => 'widget',
			],
			'thumb-gallery' => [
				'name'    => 'Thumbnail Slider',
				'enabled' => true,
				'type'    => 'widget',
				'freemium' => true,
			],
			'data-table' => [
				'name'    => 'Data Table',
				'enabled' => true,
				'type'    => 'widget',
			],
			'content-ticker' => [
				'name'    => 'Content Ticker',
				'enabled' => true,
				'type'    => 'widget',
			],
      
			'advance-button' => [
				'name' => 'Advance Button',
				'enabled' => true,
				'type'	=> 'widget'
			],

			'radial-charts' => [
				'name'	=> 'Radial Charts',
				'enabled' => true, 
				'type'	=> 'widget',
				'pro'	=> true
			],

			'advanced-heading' => [
				'name'	=> 'Advanced Heading',
				'enabled' => true, 
				'type'	=> 'widget',
				'pro'	=> true
			],

			'image-accordion' => [
				'name'	=> 'Image Accordion',
				'enabled' => true, 
				'type'	=> 'widget',
				'pro'	=> true
			],

			'faq' => [
				'name'	=> 'FAQ',
				'enabled' => true, 
				'type'	=> 'widget',
				'pro'	=> true
			],
			
			'advanced-list' => [
				'name' => 'Advanced List',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true
			],

			
			'video-box' => [
				'name'	=> 'Video',
				'enabled' => true, 
				'type'	=> 'widget',
				'pro'	=> true
			],
			'business-hours'=>[
				'name'=>'Business Hours',
				'enabled'=>true,
				'type'=>'widget',
				'pro'=>true
			],

			'instagram-feed' => [
				'name'	=> 'Instagram Feed',
				'enabled' => true, 
				'type'	=> 'widget',
				'pro'	=> true
			],

			'team-member' => [
				'name' => 'Team Member',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],


			'floating-element' => [
				'name' => 'Floating Element',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],

			'advanced-price-table' => [
				'name' => 'Advanced Price Table',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],

			'image-scroll' => [
				'name' => 'Image Scroll',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],

			'video-gallery' => [
				'name' => 'Video Gallery',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],

			'info-group' => [
				'name' => 'Info Group',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],	
			'circular-progress' => [
				'name' => 'Circular Progress',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],

			'image-stack' => [
				'name' => 'Image Stack',
				'enabled' => true,
				'type' => 'widget',
				//'pro' => true,
				'freemium' => true,
			],

			'devices' => [
				'name' => 'Devices',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],
			'image-hotspot' => [
				'name' => 'Image Hotspot',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],
			'call-to-action' => [
				'name' => 'Call To Action',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],
			'table-of-content' => [
				'name' => 'Table Of Content',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],
			'coupon-code' => [
				'name' => 'Coupon Code',
				'enabled' => true,
				'type' => 'widget',
				// 'pro' => true,
			],
			'elementor-form-action' => [
				'name'    => 'Elementor Form Action',
				'enabled' => true,
				'type'    => 'feature',
				'pro' => true,
			],
			
			'google-reviews' => [
				'name' => 'Google Reviews',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],

			'testimonial' => [
				'name' => 'Testimonial',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			],

			
		];

		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$modules['woo-products'] = [
				'name' => 'Woo Products',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			];
			$modules['woo-category'] = [
				'name' => 'Woo Category',
				'enabled' => true,
				'type' => 'widget',
				'pro' => true,
			];
		}

		// check php verison 8.0
		// if ( version_compare( PHP_VERSION, '8.0.0', '>=' ) ) {
			$modules['add-to-calendar'] = [
				'name'=> 'Add To Calendar',
				'enabled'=> true,
				'type'=> 'widget',
				'pro'=> true
			];
		// }

		$modules = apply_filters('eae/register_modules', $modules);
		
		$saved_modules = get_option( 'wts_eae_elements' );

		if ( $saved_modules !== false ) {
			foreach ( $modules as $key => $module_name ) {
				if ( array_key_exists( $key, $saved_modules ) ) {
					$modules[ $key ]['enabled'] = $saved_modules[ $key ];
				} else {
					$modules[ $key ]['enabled'] = true;
				}
			}
		}

		$modules = apply_filters( 'wts_eae_active_modules', $modules );

		return $modules;
	}

	public function get_current_url_non_paged() {
		global $wp;
		$url = get_pagenum_link( 1 );

		return trailingslashit( $url );
	}


	public static function select_elementor_page( $type ) {
		$args  = [
			'tax_query'      => [
				[
					'taxonomy' => 'elementor_library_type',
					'field'    => 'slug',
					'terms'    => $type,
				],
			],
			'post_type'      => 'elementor_library',
			'posts_per_page' => -1,
		];
		$query = new \WP_Query( $args );

		$posts = $query->posts;
		foreach ( $posts as $post ) {
			$items[ $post->ID ] = $post->post_title;
		}

		if ( empty( $items ) ) {
			$items = [];
		}

		return $items;
	}

	public static function select_ae_templates() {
		$ae_id = [];
		if ( wp_verify_nonce( isset( $_GET['post'] ) ) ) {
			$ae_id = wp_verify_nonce( [ $_GET['post'] ] );
		}
		$args  = [
			'post_type'      => 'ae_global_templates',
			'meta_key'       => 'ae_render_mode',
			'meta_value'     => 'block_layout',
			'posts_per_page' => -1,
			'post__not_in'   => $ae_id,
		];
		$query = new \WP_Query( $args );

		$posts = $query->posts;
		foreach ( $posts as $post ) {
			$items[ $post->ID ] = $post->post_title;
		}

		if ( empty( $items ) ) {
			$items = [];
		}

		return $items;
	}

	public static function validate_html_tag( $tag, $allowed_tags = [], $fallback = 'div' ) {

		if ( empty( $allowed_tags ) ) {
			$allowed_tags = [
				'article',
				'aside',
				'div',
				'footer',
				'h1',
				'h2',
				'h3',
				'h4',
				'h5',
				'h6',
				'header',
				'main',
				'nav',
				'p',
				'section',
				'span',
			];
		}

		return in_array( strtolower( $tag ), $allowed_tags ) ? $tag : $fallback;
	}

	public static function eae_media_controls($widget, $args){
		// 
		$defaults = [
			'icon'      => true,
			'image' 	=> true,
			'lottie'    => true,
		];

		$args = wp_parse_args( $args, $defaults );

		$graphic_type['none'] = [
				'title' => __( 'None', 'wts-eae' ),
				'icon'  => 'eicon-ban',
			];

		foreach($args as $key => $value){
			if($key === 'icon' && $value){
				$graphic_type['icon'] = [
						'title' => __( 'Icon', 'wts-eae' ),
						'icon'  => 'eicon-star',
					];
			}

			if($key === 'image' && $value){
				$graphic_type['image'] = [
						'title' => __( 'Image', 'wts-eae' ),
						'icon'  => 'eicon-image-bold',
				];
			}

			if($key === 'lottie' && $value){
				$graphic_type['lottie'] = [
						'title' => __( 'Lottie Animation', 'wts-eae' ),
						'icon'  => 'eicon-lottie',
					];
			}
		}
		$condition = [];
		if(isset($args['conditions'])){
			foreach ($args['conditions'] as $key => $cond) {
				$condition[$cond['key']] = $cond['value'];# code...
			}
		}

        

		$graphic_type_default =  $args['defaults']['graphic_type_default'] ?? 'none';
		
		$widget->add_control(
			$args['name'] . '_graphic_type',
			[
				'label'       => __( 'Icon Type', 'wts-eae' ),
				'label_block' => false,
				'type'        => Controls_Manager::CHOOSE,
				'options'     => $graphic_type,
				'default'     => $graphic_type_default,
				'condition'   => $condition,
				'toggle'	  => false,	
			]
		);
		
		$graphic_type_icon_condition = [
			$args['name'] . '_graphic_type'         => 'icon',
		];
		if(isset($args['conditions'])){
			$graphic_type_icon_condition = array_merge($graphic_type_icon_condition, $condition);
		}
	
		$graphic_icon_default = $args['defaults']['graphic_icon_default'] ?? ['value'   => 'fas fa-star','library' => 'fa-solid']; 
         $widget->add_control(
			$args['name'] . '_graphic_icon',
			[
				'label'            => __( 'Icon', 'wts-eae' ),
				'type'             => Controls_Manager::ICONS,
				'label_block'      => true,
				'fa4compatibility' => 'head_icon',
				'default'          => $graphic_icon_default,
				'condition'        => $graphic_type_icon_condition,
			]
		);


		$graphic_type_image_condition = [
			$args['name'] . '_graphic_type'         => 'image',
		];
		if(isset($args['conditions'])){
			$graphic_type_image_condition = array_merge($graphic_type_image_condition, $condition);
		}

        $widget->add_control(
			$args['name'] . '_graphic_image',
			[
				'label'       => __( 'Image', 'wts-eae' ),
				'label_block' => true,
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => [
					'active' => true,
				],
				'default'     => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition'   => $graphic_type_image_condition
			]
		);

		$widget->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => $args['name'] . '_graphic_image',
				'label'     => __( 'Image Size', 'wts-eae' ),
				'default'   => 'full',
				'condition' => $graphic_type_image_condition,
			]
		);

		$graphic_type_lottie_condition = [
			$args['name'] . '_graphic_type'         => 'lottie',
		];
		if(isset($args['conditions'])){
			$graphic_type_lottie_condition = array_merge($graphic_type_lottie_condition, $condition);
		}

        
        $widget->add_control(
			$args['name'] . '_source',
			[
				'label' => esc_html__( 'Source', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'media_file',
				'options' => [
					'media_file' => esc_html__( 'Media File', 'elementor-pro' ),
					'external_url' => esc_html__( 'External URL', 'elementor-pro' ),
				],
				'frontend_available' => true,
                'condition'   => $graphic_type_lottie_condition,
			]
		);

		$graphic_type_lottie_au_condition = [
			$args['name'] . '_source' => 'external_url',
					$args['name'] . '_graphic_type'         => 'lottie',
		];
		if(isset($args['conditions'])){
			$graphic_type_lottie_au_condition = array_merge($graphic_type_lottie_au_condition, $condition);
		}

        $widget->add_control(
			$args['name'] . '_lottie_animation_url',
			[
				'label'       => __( 'Animation JSON URL', 'wts-eae' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'description' => 'Get JSON code URL from <a href="https://lottiefiles.com/" target="_blank">here</a>',
				'label_block' => true,
				'condition'   => $graphic_type_lottie_au_condition,
			]
		);

		$graphic_type_lottie_json_condition = [
			$args['name'] . '_source' => 'media_file',
            $args['name'] . '_graphic_type'         => 'lottie',
		];
		if(isset($args['conditions'])){
			$graphic_type_lottie_json_condition = array_merge($graphic_type_lottie_json_condition, $condition);
		}

		$widget->add_control(
			$args['name'] . '_source_json',
			[
				'label' => esc_html__( 'Upload JSON File', 'elementor-pro' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'application/json',
				'frontend_available' => true,
				'condition' => $graphic_type_lottie_json_condition,
			]
		);


		$widget->add_control(
			$args['name'] . '_lottie_animation_loop',
			[
				'label'        => __( 'Loop', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition'    => $graphic_type_lottie_condition,
			]
		);

		$widget->add_control(
			$args['name'] . '_lottie_animation_reverse',
			[
				'label'        => __( 'Reverse', 'wts-eae' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => 'Yes',
				'label_off'    => 'No',
				'return_value' => 'yes',
				'default'      => '',
				'condition'    => $graphic_type_lottie_condition,
			]
		);

		$graphic_type_icon_layout_condition = [
			$args['name'] . '_graphic_type!' => 'none',
		];
		if(isset($args['conditions'])){
			$graphic_type_icon_layout_condition = array_merge($graphic_type_icon_layout_condition, $condition);
		}
		$default_view = $args['defaults']['view_default'] ?? 'default';
        $widget->add_control(
			$args['name'] . '_view',
			[
				'label' => esc_html__( 'View', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => esc_html__( 'Default', 'wts-eae' ),
					'stacked' => esc_html__( 'Stacked', 'wts-eae' ),
					'framed' => esc_html__( 'Framed', 'wts-eae' ),
				],
				'default' => $default_view,
                'condition'    => $graphic_type_icon_layout_condition,
			]
		);

		$graphic_type_icon_shape_condition = [
			$args['name'] . '_view!' => 'default',
                    $args['name'] . '_graphic_type!' => 'none',
		];
		if(isset($args['conditions'])){
			$graphic_type_icon_shape_condition = array_merge($graphic_type_icon_shape_condition, $condition);
		}

		$widget->add_control(
			$args['name'] . '_shape',
			[
				'label' => esc_html__( 'Shape', 'wts-eae' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'circle' => esc_html__( 'Circle', 'wts-eae' ),
					'square' => esc_html__( 'Square', 'wts-eae' ),
				],
				'default' => 'circle',
				'condition' => $graphic_type_icon_shape_condition
			]
		);

    }

	/**
	 * Add global Icon Html 
	 * @since 1.0.0 pro
	 * @param array Required It may be $settings or repeater_item
	 * @param string Optional. You can add your class at icon wrap element
	 * @param object Required Object of render funciton
	 * @param string Name of Icon Control. Which you gave while creating icon control
	 */
	

	public static function render_icon_html($data,$tag, $control_name, $wClass = ''){
		
        if($data[$control_name.'_graphic_type'] != 'none'){
            $icon_class = ['eae-gbl-icon', 'eae-graphic-type-'.$data[$control_name.'_graphic_type']];

            if($wClass != ''){
                $icon_class[] = $wClass;     
            }
            $icon_class[] = 'eae-graphic-view-'.$data[$control_name.'_view']; 
            if($data[$control_name.'_view'] != 'default'){
                $icon_class[] = 'eae-graphic-shape-'.$data[$control_name.'_shape'];
            }
			if(isset($data[$control_name.'_hover_animation']) && $data[$control_name.'_hover_animation'] != 'none'){
                $icon_class[] = 'elementor-animation-'.$data[$control_name.'_hover_animation'];
            }
			
            if($data[$control_name.'_graphic_type'] == 'lottie'){   
                if(! empty( $data[$control_name.'_lottie_animation_url'] ) ||  !empty($data[$control_name.'_source_json']['url'])) {
                    $icon_class[] = 'eae-lottie-animation';
                    $icon_class[] = 'eae-lottie';
                    $lottie_data = [
                        'loop'    => ( $data[$control_name.'_lottie_animation_loop'] === 'yes' ) ? true : false,
                        'reverse' => ( $data[$control_name.'_lottie_animation_reverse'] === 'yes' ) ? true : false,
                    ];
                    if($data[$control_name.'_source'] == 'media_file' && !empty($data[$control_name.'_source_json']['url'])){
                        $lottie_data['url'] = $data[$control_name.'_source_json']['url'];
                    }else{
                        $lottie_data['url'] = $data[$control_name.'_lottie_animation_url'];
                    }                      
                    $tag->set_render_attribute('panel-icon', 'data-lottie-settings', wp_json_encode( $lottie_data ));
                }    
            }
			// add filter to add class in icon
			$icon_class = apply_filters('eae/eae-icon-class',$icon_class, $data, $control_name);
            $tag->set_render_attribute('panel-icon', 'class', $icon_class);
            if($data[$control_name.'_graphic_type'] == 'lottie') {
                if(!empty($lottie_data['url'])){
					
                    ?><span <?php echo $tag->get_render_attribute_string('panel-icon');?>></span>
					<?php
					$tag->remove_render_attribute('panel-icon');
                }
            }else{
                switch ($data[$control_name.'_graphic_type']) {
                    case 'icon':    
                                    ?>
                                    <span <?php echo $tag->get_render_attribute_string('panel-icon');?>>
                                        <?php
                                        Icons_Manager::render_icon( $data[$control_name.'_graphic_icon'], [ 'aria-hidden' => 'true' ] );
                                        ?> 
                                    </span>
                                    <?php 
                                     break;
                    case 'image':	
                                    if(!empty($data[$control_name.'_graphic_image']['id'])){
                                        $imgHtml = wp_get_attachment_image($data[$control_name.'_graphic_image']['id'], $data[$control_name.'_graphic_image_size'], false);
                                        ?><span <?php echo $tag->get_render_attribute_string('panel-icon');?>><?php echo $imgHtml; ?></span>
                                    <?php }
                                    break;
                }                    
            }
        }
    }

	public static function repeater_icon_style_controls($widget, $args){
		$wrapper_selector = '{{WRAPPER}} {{CURRENT_ITEM}} ' .$args['selector'];
	
		$hover_wrapper_selector = '{{WRAPPER}} {{CURRENT_ITEM}} ' .$args['selector'];
		$hover = ':hover';
		if(isset($args['is_parent_hover']) && $args['is_parent_hover'] == true){			
			$hover = '';
			$hover_wrapper_selector = '{{WRAPPER}} {{CURRENT_ITEM}}' .$args['hover_selector'];
		}

		$widget->add_control(
			$args['name'].'_popover_toggle',
			[
				'label' => esc_html__( 'Icon', 'wts-eae' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$widget->start_popover();
	
		$widget->add_control(
			$args['name'].'_primary_color',
			[
				'label' => esc_html__( 'Primary Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'background-color: {{VALUE}};',
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$wrapper_selector.'.eae-graphic-view-default' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$wrapper_selector.'.eae-graphic-view-default svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					$args['name'].'_popover_toggle' => 'yes'
				]
			]
		);
	
		$widget->add_control(
			$args['name'].'_secondary_color',
			[
				'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed' => 'background-color: {{VALUE}};',
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'color: {{VALUE}};',
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					$args['name'].'_popover_toggle' => 'yes'
				]
			]
		);
	
	
		$widget->add_responsive_control(
			$args['name'].'_size',
			[
				'label' => esc_html__( 'Size', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-type-icon, '.$wrapper_selector.'.eae-gbl-icon.eae-graphic-type-lottie' => 'font-size: {{SIZE}}{{UNIT}};',
					$wrapper_selector .'.eae-graphic-type-image img' => 'width : {{SIZE}}{{UNIT}}; height : {{SIZE}}{{UNIT}};'
				],
				'condition' => [
					$args['name'].'_popover_toggle' => 'yes'
				]
			]
		);
	
		$widget->add_control(
			$args['name'].'_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon:not(.eae-graphic-view-default)' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'condition' => [
					$args['name'].'_popover_toggle' => 'yes'
				]
			]
		);
	
		$widget->add_responsive_control(
			$args['name'].'_rotate',
			[
				'label' => esc_html__( 'Rotate', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon i ,'.$wrapper_selector.'.eae-gbl-icon svg,'.$wrapper_selector.'.eae-gbl-icon img' => 'transform: rotate({{SIZE}}{{UNIT}}) !important;',
				],
				'condition' => [
					$args['name'].'_popover_toggle' => 'yes'
				]
			]
		);
	
		$widget->add_control(
			$args['name'].'_border_width',
			[
				'label' => esc_html__( 'Border Width', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$args['name'].'_popover_toggle' => 'yes'
				]
			]
		);
	
		$widget->add_responsive_control(
			$args['name'].'_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon:not(.eae-graphic-view-default)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					$args['name'].'_popover_toggle' => 'yes'
				]
			]
		);

		$widget->end_popover();
		if(!isset($args['show_hover_controls'])){

		
		$widget->add_control(
			$args['name'].'_hover_popover_toggle',
			[
				'label' => esc_html__( 'Icon Hover', 'wts-eae' ),
				'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
				'return_value' => 'yes',
				'default' => '',
			]
		);

		$widget->start_popover();
	
		$widget->add_control(
			$args['name'].'_hover_primary_color',
			[
				'label' => esc_html__( 'Primary Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked'.$hover => 'background-color: {{VALUE}};',
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed'.$hover.', '.$hover_wrapper_selector.'.eae-gbl-icon.eae-graphic-view-default'.$hover => 'color: {{VALUE}}; border-color: {{VALUE}};',
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed'.$hover.','.$hover_wrapper_selector.'.eae-gbl-icon.eae-graphic-view-defult'.$hover.' svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					$args['name'].'_hover_popover_toggle' => 'yes'
				]
			]
		);
	
		$widget->add_control(
			$args['name'].'_hover_secondary_color',
			[
				'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed'.$hover => 'background-color: {{VALUE}};',
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked'.$hover => 'color: {{VALUE}};',
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked'.$hover.' svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					$args['name'].'_hover_popover_toggle' => 'yes'
				]
			]
		);
	
		$widget->add_control(
			$args['name'].'_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'wts-eae' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
				'condition' => [
					$args['name'].'_hover_popover_toggle' => 'yes'
				]
			]
		);

		$widget->end_popover();
		}
		if(isset($args['is_active_tab'])){
			$is_active_tab = $args['is_active_tab'];
			$active_selector = '{{WRAPPER}}  {{CURRENT_ITEM}} ' . $is_active_tab['selector'] . ' ' . $args['selector'];

			$widget->add_control(
				$args['name'].'_active_popover_toggle',
				[
					'label' => esc_html__( 'Icon Active', 'wts-eae' ),
					'type' => \Elementor\Controls_Manager::POPOVER_TOGGLE,
					'return_value' => 'yes',
					'default' => '',
				]
			);

			$widget->start_popover();

			$widget->add_control(
				$args['name'].'_active_primary_color',
				[
					'label' => esc_html__( 'Primary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						$active_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'background-color: {{VALUE}};',
						$active_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$active_selector.'.eae-graphic-view-default' => 'color: {{VALUE}}; border-color: {{VALUE}};',
						$active_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$active_selector.'.eae-graphic-view-default svg' => 'fill: {{VALUE}};',
					],
					'condition' => [
						$args['name'].'_active_popover_toggle' => 'yes'
					]
				]
			);

			$widget->add_control(
				$args['name'].'_active_secondary_color',
				[
					'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						$active_selector .'.eae-gbl-icon.eae-graphic-view-framed' => 'background-color: {{VALUE}};',
						$active_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'color: {{VALUE}};',
						$active_selector .'.eae-gbl-icon.eae-graphic-view-stacked svg' => 'fill: {{VALUE}};',
					],
					'condition' => [
						$args['name'].'_active_popover_toggle' => 'yes'
					]
				]
			);

			$widget->start_popover();
		}
	}


	public static function global_icon_style_controls($widget, $args){
		
		$wrapper_selector = '{{WRAPPER}} ' .$args['selector'];
		$hover_wrapper_selector = '{{WRAPPER}} ' .$args['selector'];
		$hover = ':hover';
		if(isset($args['is_parent_hover']) && $args['is_parent_hover'] == true){			
			$hover = '';
			$hover_wrapper_selector = '{{WRAPPER}} ' .$args['hover_selector'];
		}

		$condition = [];
		if(isset($args['conditions'])){
			foreach ($args['conditions'] as $key => $cond) {
				$condition[$cond['key']] = $cond['value'];# code...
			}
			//echo '<pre>';  print_r($condition); echo '</pre>';
			
		}
		
		if(!isset($args['show_hover_controls'])){
			$widget->start_controls_tabs( $args['name'].'_icon_colors' );
			$widget->start_controls_tab(
				$args['name'].'_colors_normal',
				[
					'label' => esc_html__( 'Normal', 'wts-eae' ),
					'condition' => $condition
				]
			);
		}
		if(isset($args['default']['primary_color'])){
			$p_default = $args['default']['primary_color'];
		}else{
			$p_default = Global_Colors::COLOR_PRIMARY;
		}
		if(isset($args['default']['custom_primary_color'])){
			$p_default = $args['default']['primary_color'] ?? '#ffffff';
		}

		

		if(!isset($args['default']['custom_primary_color'])){
			$widget->add_control(
				$args['name'].'_primary_color',
				[
					'label' => esc_html__( 'Primary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'background-color: {{VALUE}};',
						$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$wrapper_selector.'.eae-graphic-view-default' => 'color: {{VALUE}}; border-color: {{VALUE}};',
						$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$wrapper_selector.'.eae-graphic-view-default svg' => 'fill: {{VALUE}};',
					],
					'global' => [
						'default' => $p_default
					],
					'condition' => $condition
				]
			);
		}else{
			$widget->add_control(
				$args['name'].'_primary_color',
				[
					'label' => esc_html__( 'Primary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'background-color: {{VALUE}};',
						$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$wrapper_selector.'.eae-graphic-view-default' => 'color: {{VALUE}}; border-color: {{VALUE}};',
						$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed, '.$wrapper_selector.'.eae-graphic-view-default svg' => 'fill: {{VALUE}};',
					],
					'default' => $p_default,
					'condition' => $condition
				]
			);
		}
		
		$widget->add_control(
			$args['name'].'_secondary_color',
			[
				'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed' => 'background-color: {{VALUE}};',
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked' => 'color: {{VALUE}};',
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked svg' => 'fill: {{VALUE}};',
				],
				'condition' => $condition
			]
		);


		if(!isset($args['show_hover_controls']) ){
			$widget->end_controls_tab();

			$widget->start_controls_tab(
				$args['name'].'_colors_hover',
				[
					'label' => esc_html__( 'Hover', 'wts-eae' ),
					'condition' => $condition
				]
			);
		}

		if(!isset($args['show_hover_controls'])){

		
		$widget->add_control(
			$args['name'].'_hover_primary_color',
			[
				'label' => esc_html__( 'Primary Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked'.$hover => 'background-color: {{VALUE}};',
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed'.$hover.', '.$hover_wrapper_selector.'.eae-gbl-icon.eae-graphic-view-default'.$hover => 'color: {{VALUE}}; border-color: {{VALUE}};',
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed'.$hover.','.$hover_wrapper_selector.'.eae-gbl-icon.eae-graphic-view-defult'.$hover.' svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$widget->add_control(
			$args['name'].'_hover_secondary_color',
			[
				'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed'.$hover => 'background-color: {{VALUE}};',
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked'.$hover => 'color: {{VALUE}};',
					$hover_wrapper_selector .'.eae-gbl-icon.eae-graphic-view-stacked'.$hover.' svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$widget->add_control(
			$args['name'].'_hover_animation',
			[
				'label' => esc_html__( 'Hover Animation', 'wts-eae' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		}

		if(!isset($args['show_hover_controls']) ){
			$widget->end_controls_tab();
		}
		

		if(isset($args['is_active_tab']) ){
			
			$is_active_tab = $args['is_active_tab'];
			$active_selector = '{{WRAPPER}} ' . $is_active_tab['selector'] . ' ' . $args['selector'];
			if(!isset($args['show_hover_controls'])){
				$widget->start_controls_tab(
					$args['name'].'_colors_active',
					[
						'label' => esc_html__( $is_active_tab['label'], 'wts-eae' ),
						'condition' => $condition
					]
				);
			}
			$widget->add_control(
				$args['name'].'_active_primary_color',
				[
					'label' => esc_html__( 'Primary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						$active_selector . '.eae-gbl-icon.eae-graphic-view-stacked' => 'background-color: {{VALUE}};',
						$active_selector . '.eae-gbl-icon.eae-graphic-view-framed, ' . $active_selector . '.eae-gbl-icon.eae-graphic-view-default' => 'color: {{VALUE}}; border-color: {{VALUE}};',
						$active_selector . '.eae-gbl-icon.eae-graphic-view-framed, ' . $active_selector . '.eae-gbl-icon.eae-graphic-view-defult svg' => 'fill: {{VALUE}};',
					],
					'global'    => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
				]
			);

			$widget->add_control(
				$args['name'].'_active_secondary_color',
				[
					'label' => esc_html__( 'Secondary Color', 'wts-eae' ),
					'type' => Controls_Manager::COLOR,
					'default' => '',
					'selectors' => [
						$active_selector . '.eae-gbl-icon.eae-graphic-view-framed' => 'background-color: {{VALUE}};',
						$active_selector . '.eae-gbl-icon.eae-graphic-view-stacked' => 'color: {{VALUE}};',
						$active_selector . '.eae-gbl-icon.eae-graphic-view-stacked svg' => 'fill: {{VALUE}};',
					],
				]
			);
			if(!isset($args['show_hover_controls'])){
				$widget->end_controls_tab();
			}
			
		
		}
		if(!isset($args['show_hover_controls'])){
			$widget->end_controls_tabs();
		}
		
		$widget->add_responsive_control(
			$args['name'].'_size',
			[
				'label' => esc_html__( 'Size', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'range' => [
					'px' => [
						'min' => 6,
						'max' => 300,
					],
				],
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-type-icon, '.$wrapper_selector.'.eae-gbl-icon.eae-graphic-type-lottie' => 'font-size: {{SIZE}}{{UNIT}};',
                    $wrapper_selector .'.eae-graphic-type-image img' => 'width : {{SIZE}}{{UNIT}}; height : {{SIZE}}{{UNIT}};'
				],
				'separator' => 'before',
				'condition' => $condition
			]
		);

		$widget->add_control(
			$args['name'].'_padding',
			[
				'label' => esc_html__( 'Padding', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon:not(.eae-graphic-view-default)' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'range' => [
					'em' => [
						'min' => 0,
						'max' => 5,
					],
				],
				'condition' => $condition
			]
		);

		$widget->add_responsive_control(
			$args['name'].'_rotate',
			[
				'label' => esc_html__( 'Rotate', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'deg', 'grad', 'rad', 'turn', 'custom' ],
				'default' => [
					'unit' => 'deg',
				],
				'tablet_default' => [
					'unit' => 'deg',
				],
				'mobile_default' => [
					'unit' => 'deg',
				],
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon i ,'.$wrapper_selector.'.eae-gbl-icon svg,'.$wrapper_selector.'.eae-gbl-icon img' => 'transform: rotate({{SIZE}}{{UNIT}}) !important;',
				],
				'condition' => $condition
			]
		);

		$widget->add_control(
			$args['name'].'_border_width',
			[
				'label' => esc_html__( 'Border Width', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon.eae-graphic-view-framed' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => $condition
			]
		);

		$widget->add_responsive_control(
			$args['name'].'_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'wts-eae' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					$wrapper_selector .'.eae-gbl-icon:not(.eae-graphic-view-default)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					$wrapper_selector .'.eae-gbl-icon:not(.eae-graphic-view-default) img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => $condition
			]
		);
	}

	public static function eae_flex_controls($widget, $selector){
		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';

		$widget->add_control(
			'flex_items',
			[
				'type' => Controls_Manager::HEADING,
				'label' => esc_html__( 'Items', 'wts-eae' ),
				'separator' => 'before',
			]
		);

		$widget->add_responsive_control(
			'flex_direction', 
			[
				'label' => esc_html_x( 'Direction', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'row' => [
						'title' => esc_html_x( 'Row - horizontal', 'wts-eae' ),
						'icon' => 'eicon-arrow-' . $end,
					],
					'column' => [
						'title' => esc_html_x( 'Column - vertical', 'wts-eae' ),
						'icon' => 'eicon-arrow-down',
					],
					'row-reverse' => [
						'title' => esc_html_x( 'Row - reversed', 'wts-eae' ),
						'icon' => 'eicon-arrow-' . $start,
					],
					'column-reverse' => [
						'title' => esc_html_x( 'Column - reversed', 'wts-eae' ),
						'icon' => 'eicon-arrow-up',
					],
				],
				'default' => 'row',
				'selectors_dictionary' => [
					'row' => 'flex-direction: row; width: initial; height: 100%;',
					'column' => 'flex-direction: column; width: 100%; height: initial;',
					'row-reverse' => 'flex-direction: row-reverse; width: initial; height: 100%;',
					'column-reverse' => 'flex-direction: column-reverse; width: 100%; height: initial;',
				],
				'selectors' => [
					$selector => '{{VALUE}};',
				],
			]
		);

		$widget->add_responsive_control(
			'flex_justify_content',
			[
				'label' => esc_html_x( 'Justify Content', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => true,
				'default' => 'space-evenly',
				'options' => [
					'flex-start' => [
						'title' => esc_html_x( 'Start', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-justify-start-h',
					],
					'center' => [
						'title' => esc_html_x( 'Center', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-justify-center-h',
					],
					'flex-end' => [
						'title' => esc_html_x( 'End', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-justify-end-h',
					],
					'space-between' => [
						'title' => esc_html_x( 'Space Between', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-justify-space-between-h',
					],
					'space-around' => [
						'title' => esc_html_x( 'Space Around', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-justify-space-around-h',
					],
					'space-evenly' => [
						'title' => esc_html_x( 'Space Evenly', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-justify-space-evenly-h',
					],
				],
				'selectors' => [
					$selector => 'justify-content: {{VALUE}};',
				],
			]
		);

		$widget->add_responsive_control(
			'flex_align_items',
			[
				'label' => esc_html_x( 'Align Items', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => '',
				'options' => [
					'flex-start' => [
						'title' => esc_html_x( 'Start', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-align-start-v',
					],
					'center' => [
						'title' => esc_html_x( 'Center', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-align-center-v',
					],
					'flex-end' => [
						'title' => esc_html_x( 'End', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-align-end-v',
					],
					'stretch' => [
						'title' => esc_html_x( 'Stretch', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-align-stretch-v',
					],
				],
				'selectors' => [
					$selector => 'align-items: {{VALUE}};',
				],
			]
		);

		$widget->add_responsive_control(
			'flex_gap',
			[
				'label' => esc_html_x( 'Gap', 'Flex Item Control', 'wts-eae' ),
				'type' => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 10,
					],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
					'vw' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 50,
					],
				],

				'size_units' => [ 'px', '%', 'em', 'rem', 'vw', 'custom' ],
				'selectors' => [
					$selector => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_responsive_control(
			'flex_wrap',
			[
				'label' => esc_html_x( 'Wrap', 'wts-eae' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'nowrap' => [
						'title' => esc_html_x( 'No Wrap', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-nowrap',
					],
					'wrap' => [
						'title' => esc_html_x( 'Wrap', 'wts-eae' ),
						'icon' => 'eicon-flex eicon-wrap',
					],
				],
				'description' => esc_html_x(
					'Items within the container can stay in a single line (No wrap), or break into multiple lines (Wrap).',
					'Flex Container Control',
					'wts-eae'
				),
				'default' => 'wrap',
				'selectors' => [
					$selector => 'flex-wrap: {{VALUE}};',
				],
			]
		);

		
	}

	public static function get_share_url($settings, $share_url, $media_url, $share_text){
		if( $settings['lightgallery_facebook'] === 'yes' ){
			$facebookBaseUrl = '//www.facebook.com/sharer/sharer.php?u=';
        	return $facebookBaseUrl + $share_url;
		}

		if( $settings['lightgallery_twitter'] === 'yes' ){
			$twitterBaseUrl = '//twitter.com/intent/tweet?text=';
			$url = $share_url;
			$text = $share_text;
			return $twitterBaseUrl + $text + '&url=' + $share_url;
		}

		if( $settings['lightgallery_pinterest'] === 'yes' ){
			$pinterestBaseUrl = 'http://www.pinterest.com/pin/create/button/?url=';
			$description = $share_text;
			$media = $media_url;
			$url = $share_url;
			return $pinterestBaseUrl + $url + '&media=' + $media + '&description=' + $description;
		}
	}


	public function eae_pro_notice(){
        return '<div class="eae-pro-notice-wrapper">
            <div class="eae-pro-notice-title"><i class="fa fa-lock" aria-hidden="true"></i> Pro Feature</div>
            <div class="eae-pro-notice-info">Get our pro version to use all our widgets & features with its fullest functionality. The pro version will improve your elementor workflow.</div>
            <div class="eae-pro-notice-links">
                <a class="eae-pro-link-buy" href="https://wpvibes.link/go/eae-upgrade" target="_blank">Upgrade to Pro</a>
            </div>
        </div>';
    }

	public static function add_eae_pro_notice_controls($widget, $args){
		if(EAE::$is_pro == true){
			return;
		}else{
			$widget->add_control(
				$args['name'],
				[
					'label' => esc_html__( 'Unlock more possibilities', 'tpebl' ),
					'type' => Controls_Manager::TEXT,
					'default' => '',
					'description' => '<div class="eae-pro-notice-wrapper">
											<div class="eae-pro-notice-title"><i class="fa fa-lock" aria-hidden="true"></i> Pro Feature</div>
											<div class="eae-pro-notice-info">Get our pro version to use all our widgets & features with It\'s fullest functionallity. Pro version will improve your elementor work flow.</div>
											<div class="eae-pro-notice-links">
												<a class="eae-pro-link-buy" href="https://wpvibes.link/go/eae-upgrade" target="_blank">Upgrade to Pro</a>
											</div>	
									  </div>',
					'classes' => 'eae-pro-notice',
					'condition'    => $args['conditions'],
				]
			);
		}
	}

	/**
	 * Check if a value is present in an array, if not apply default value.
	 *
	 * @param mixed $value       The value to check.
	 * @param array $value_array The array of values to check against.
	 * @param mixed $default     The default value to apply if the value is not found in the array.
	 * @param bool  $is_assocative_array     If the array is associative or not.
	 * @return mixed The value found in the array or the default value.
	*/
	public static function validate_option_value($value, $value_array, $default, $is_assocative_array = true) {
		// Check if the value is in the array
		if($is_assocative_array){
			if (in_array($value, array_keys($value_array))) {
				return $value;
			} else {
				// If value is not found, return default value
				return $default;
			}
		}else{
			if (in_array($value, $value_array)) {
				return $value;
			} else {
				// If value is not found, return default value
				return $default;
			}
		}	
	}

	
	public static function eae_wp_kses( $text ) {
        if ( empty( $text ) ) {
            return '';
        }
		return wp_kses( $text, self::eae_allowed_html_tags(), array_merge( wp_allowed_protocols(), [ 'data' ] ) );
	}

	
	public static function eae_allowed_html_tags() {
		return apply_filters('eae_allowed_html_tags', 
			[
				'a'       => [
					'href'   => [],
					'title'  => [],
					'class'  => [],
					'rel'    => [],
					'id'     => [],
					'style'  => [],
					'target' => [],
				],
				'q'       => [
					'cite'  => [],
					'class' => [],
					'id'    => [],
				],
				'img'     => [
					'src'    => [],
					'alt'    => [],
					'height' => [],
					'width'  => [],
					'class'  => [],
					'id'     => [],
					'style'  => []
				],
				'span'    => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				'dfn'     => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				'time'    => [
					'datetime' => [],
					'class'    => [],
					'id'       => [],
					'style'    => [],
				],
				'cite'    => [
					'title' => [],
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'hr'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'b'       => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'p'       => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				'i'       => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				'u'       => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				's'       => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'br'      => [],
				'em'      => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				'code'    => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'mark'    => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'small'   => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				'abbr'    => [
					'title' => [],
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'strong'  => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				'del'     => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				'ins'     => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				'sub'     => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'sup'     => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'div'     => [
					'class' => [],
					'id'    => [],
					'style' => []
				],
				'strike'  => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'acronym' => [],
				'h1'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'h2'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'h3'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'h4'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'h5'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'h6'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'button'  => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'center'  => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'ul'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'ol'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'li'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
				],
				'table'   => [
					'class' => [],
					'id'    => [],
					'style' => [],
					'dir'   => [],
					'align' => [],
				],
				'thead'   => [
					'class' => [],
					'id'    => [],
					'style' => [],
					'align' => [],
				],
				'tbody'   => [
					'class' => [],
					'id'    => [],
					'style' => [],
					'align' => [],
				],
				'tfoot'   => [
					'class' => [],
					'id'    => [],
					'style' => [],
					'align' => [],
				],
				'th'      => [
					'class'   => [],
					'id'      => [],
					'style'   => [],
					'align'   => [],
					'colspan' => [],
					'rowspan' => [],
				],
				'tr'      => [
					'class' => [],
					'id'    => [],
					'style' => [],
					'align' => [],
				],
				'td'      => [
					'class'   => [],
					'id'      => [],
					'style'   => [],
					'align'   => [],
					'colspan' => [],
					'rowspan' => [],
				],
			]
		);
	}
}

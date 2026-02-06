<?php
namespace WTS_EAE\Modules\BgSlider;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Utils;

class Module {
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	private static $_instance = null;

	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct() {
		add_action( 'elementor/element/after_section_end', [ $this, '_add_controls' ], 10, 3 );

		add_action( 'elementor/frontend/element/before_render', [ $this, '_before_render' ], 10, 1 );

		add_action( 'elementor/frontend/column/before_render', [ $this, '_before_render' ], 10, 1 );
		add_action( 'elementor/frontend/section/before_render', [ $this, '_before_render' ], 10, 1 );
		add_action( 'elementor/frontend/container/before_render', [ $this, '_before_render' ], 10, 1 );

		add_action( 'elementor/element/print_template', [ $this, '_print_template' ], 10, 2 );
		add_action( 'elementor/section/print_template', [ $this, '_print_template' ], 10, 2 );
		add_action( 'elementor/column/print_template', [ $this, '_print_template' ], 10, 2 );
		add_action( 'elementor/container/print_template', [ $this, '_print_template' ], 10, 2 );

		add_action( 'wp_enqueue_scripts', [ $this, 'eae_add_js_css' ] );
	}

	public function eae_add_js_css() {
		wp_enqueue_style( 'vegas-css' );
		wp_enqueue_script( 'vegas' );
		wp_enqueue_script( 'wts-swiper-script' );
		wp_enqueue_style( 'wts-swiper-style' );
	}

	public function _add_controls( $element, $section_id, $args ) {
		if ( ( 'section' === $element->get_name() && 'section_background' === $section_id ) || ( 'column' === $element->get_name() && 'section_style' === $section_id ) || ( 'container' === $element->get_name() && 'section_background' === $section_id ) ) {

			$element->start_controls_section(
				'_eae_section_bg_slider',
				[
					'label' => __( 'AE - Background Slider', 'wts-eae' ),
					'tab'   => Controls_Manager::TAB_STYLE,
				]
			);

			$element->add_control(
				'eae_bg_slider_images',
				[
					'label'   => __( 'Add Images', 'wts-eae' ),
					'type'    => Controls_Manager::GALLERY,
					'default' => [],
					'dynamic' => [
						'active' => true,
					],
				]
			);

			$element->add_group_control(
				Group_Control_Image_Size::get_type(),
				[
					'name' => 'eae_thumbnail',
				]
			);

			$element->add_control(
				'eae_slider_transition',
				[
					'label'   => __( 'Transition', 'wts-eae' ),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'fade'        => __( 'Fade', 'wts-eae' ),
						'fade2'       => __( 'Fade2', 'wts-eae' ),
						'slideLeft'   => __( 'slide Left', 'wts-eae' ),
						'slideLeft2'  => __( 'Slide Left 2', 'wts-eae' ),
						'slideRight'  => __( 'Slide Right', 'wts-eae' ),
						'slideRight2' => __( 'Slide Right 2', 'wts-eae' ),
						'slideUp'     => __( 'Slide Up', 'wts-eae' ),
						'slideUp2'    => __( 'Slide Up 2', 'wts-eae' ),
						'slideDown'   => __( 'Slide Down', 'wts-eae' ),
						'slideDown2'  => __( 'Slide Down 2', 'wts-eae' ),
						'zoomIn'      => __( 'Zoom In', 'wts-eae' ),
						'zoomIn2'     => __( 'Zoom In 2', 'wts-eae' ),
						'zoomOut'     => __( 'Zoom Out', 'wts-eae' ),
						'zoomOut2'    => __( 'Zoom Out 2', 'wts-eae' ),
						'swirlLeft'   => __( 'Swirl Left', 'wts-eae' ),
						'swirlLeft2'  => __( 'Swirl Left 2', 'wts-eae' ),
						'swirlRight'  => __( 'Swirl Right', 'wts-eae' ),
						'swirlRight2' => __( 'Swirl Right 2', 'wts-eae' ),
						'burn'        => __( 'Burn', 'wts-eae' ),
						'burn2'       => __( 'Burn 2', 'wts-eae' ),
						'blur'        => __( 'Blur', 'wts-eae' ),
						'blur2'       => __( 'Blur 2', 'wts-eae' ),
						'flash'       => __( 'Flash', 'wts-eae' ),
						'flash2'      => __( 'Flash 2', 'wts-eae' ),
						'random'      => __( 'Random', 'wts-eae' ),
					],
					'default' => 'fade',
				]
			);
			$element->add_control(
				'eae_slider_animation',
				[
					'label'   => __( 'Animation', 'wts-eae' ),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'kenburns'          => __( 'Kenburns', 'wts-eae' ),
						'kenburnsUp'        => __( 'Kenburns Up', 'wts-eae' ),
						'kenburnsDown'      => __( 'Kenburns Down', 'wts-eae' ),
						'kenburnsRight'     => __( 'Kenburns Right', 'wts-eae' ),
						'kenburnsLeft'      => __( 'Kenburns Left', 'wts-eae' ),
						'kenburnsUpLeft'    => __( 'Kenburns Up Left', 'wts-eae' ),
						'kenburnsUpRight'   => __( 'Kenburns Up Right', 'wts-eae' ),
						'kenburnsDownLeft'  => __( 'Kenburns Down Left', 'wts-eae' ),
						'kenburnsDownRight' => __( 'Kenburns Down Right', 'wts-eae' ),
						'random'            => __( 'Random', 'wts-eae' ),
						''                  => __( 'None', 'wts-eae' ),
					],
					'default' => 'kenburns',
				]
			);

			$element->add_control(
				'eae_custom_overlay_switcher',
				[
					'label'        => __( 'Custom Overlay', 'wts-eae' ),
					'type'         => Controls_Manager::SWITCHER,
					'default'      => '',
					'label_on'     => __( 'Show', 'wts-eae' ),
					'label_off'    => __( 'Hide', 'wts-eae' ),
					'return_value' => 'yes',
				]
			);

			$element->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'eae_slider_custom_overlay',
					'label'     => __( 'Overlay Image', 'wts-eae' ),
					'types'     => [ 'none', 'classic', 'gradient' ],
					'selector'  => '{{WRAPPER}} .vegas-overlay',
					'condition' => [
						'eae_custom_overlay_switcher' => 'yes',
					],
				]
			);
			$element->add_control(
				'eae_slider_overlay',
				[
					'label'     => __( 'Overlay', 'wts-eae' ),
					'type'      => Controls_Manager::SELECT,
					'options'   => [
						''   => __( 'None', 'wts-eae' ),
						'01' => __( 'Style 1', 'wts-eae' ),
						'02' => __( 'Style 2', 'wts-eae' ),
						'03' => __( 'Style 3', 'wts-eae' ),
						'04' => __( 'Style 4', 'wts-eae' ),
						'05' => __( 'Style 5', 'wts-eae' ),
						'06' => __( 'Style 6', 'wts-eae' ),
						'07' => __( 'Style 7', 'wts-eae' ),
						'08' => __( 'Style 8', 'wts-eae' ),
						'09' => __( 'Style 9', 'wts-eae' ),
					],
					'default'   => '01',
					'condition' => [
						'eae_custom_overlay_switcher' => '',
					],
				]
			);
			$element->add_control(
				'eae_slider_cover',
				[
					'label'   => __( 'Cover', 'wts-eae' ),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'true'  => __( 'True', 'wts-eae' ),
						'false' => __( 'False', 'wts-eae' ),
					],
					'default' => 'true',
				]
			);
			$element->add_control(
				'eae_slider_delay',
				[
					'label'       => __( 'Delay', 'wts-eae' ),
					'type'        => Controls_Manager::TEXT,
					'label_block' => true,
					'placeholder' => __( 'Delay', 'wts-eae' ),
					'default'     => __( '5000', 'wts-eae' ),
				]
			);
			$element->add_control(
				'eae_slider_timer_bar',
				[
					'label'   => __( 'Timer', 'wts-eae' ),
					'type'    => Controls_Manager::SELECT,
					'options' => [
						'true'  => __( 'True', 'wts-eae' ),
						'false' => __( 'False', 'wts-eae' ),
					],
					'default' => 'true',
				]
			);

			$element->end_controls_section();

		}
	}

	

	public function _before_render( \Elementor\Element_Base $element ) {

		if ( $element->get_name() !== 'section' && $element->get_name() !== 'column' && $element->get_name() !== 'container' ) {
			return;
		}
		$settings = $element->get_settings_for_display();
		$eae_slider = rand( 1, 99999 );
		$element->add_render_attribute( '_wrapper', 'class', 'has_eae_slider' );
		$element->add_render_attribute( '_wrapper', 'data-eae-slider', $eae_slider );
		$element->add_render_attribute( 'eae-bs-background-slideshow-wrapper', 'class', 'eae-bs-background-slideshow-wrapper' );

		$element->add_render_attribute( 'eae-bs-backgroundslideshow', 'class', 'eae-at-backgroundslideshow' );

		$slides = [];

		if ( empty( $settings['eae_bg_slider_images'] ) ) {
			return;
		}
		foreach ( $settings['eae_bg_slider_images'] as $attachment ) {
			if ( array_key_exists( 'url', $attachment ) ) {
				$image_url = Group_Control_Image_Size::get_attachment_image_src( $attachment['id'], 'eae_thumbnail', $settings );
			} else {

				$image_url = Group_Control_Image_Size::get_attachment_image_src( $attachment['id'], 'eae_thumbnail', $settings );

			}
			$slides[] = [ 'src' => $image_url ];
		}

		if ( empty( $slides ) ) {
			return;
		}
		$animation = '';
		$transition = '';
		
		$element_id = $element->get_id(); // Only fetch once and reuse
		$slides = wp_json_encode($slides); // Properly encode slides for JavaScript
		if ( isset( $settings['eae_slider_transition'] ) && ! empty( $settings['eae_slider_transition'] ) ) {
			$transition = esc_js($settings['eae_slider_transition']);
		}		
		
		if ( isset( $settings['eae_slider_animation'] ) && ! empty( $settings['eae_slider_animation'] ) ) {
			$animation = esc_js($settings['eae_slider_animation']);
		}
		
		$cover = filter_var($settings['eae_slider_cover'], FILTER_VALIDATE_BOOLEAN);
		if(!$cover){
			$cover = 'false';
		}else{
			$cover = 'true';
		}	
		
		$delay = filter_var($settings['eae_slider_delay'], FILTER_VALIDATE_INT);
		if(!$delay){
			$delay = 5000;
		}
		$timer = filter_var($settings['eae_slider_timer_bar'], FILTER_VALIDATE_BOOLEAN);
		if(!$timer){
			$timer = 'false';
		}else{
			$timer = 'true';
		}
		// Determine the overlay URL
		$overlay_base_url = EAE_URL . '/assets/lib/vegas/overlays/';
		$overlay_file = $settings['eae_custom_overlay_switcher'] === 'yes' ? '00.png' : ($settings['eae_slider_overlay'] ? $settings['eae_slider_overlay'] . '.png' : '00.png');
		$bgoverlay = $overlay_base_url . $overlay_file;
	?>

	<script type="text/javascript">
		
		jQuery(document).ready(function ($) {
			var elementSelector = ".elementor-element-<?php echo esc_attr($element_id); ?>[data-eae-slider=<?php echo $eae_slider; ?>]";
			var $element = jQuery(elementSelector);

			// Adding the HTML structure
			$element.prepend('<div class="eae-section-bs"><div class="eae-section-bs-inner"></div></div>');

			// Initialize vegas background slider
			$element.children('.eae-section-bs').children('.eae-section-bs-inner').vegas({
				slides: <?php echo $slides; ?>,
				transition: '<?php echo $transition; ?>',
				animation: '<?php echo $animation; ?>',
				overlay: '<?php echo esc_url($bgoverlay); ?>',
				cover: <?php echo $cover; ?>,
				delay: <?php echo $delay; ?>,
				timer: <?php echo $timer; ?>
			});
			

			// Clear the overlay if custom overlay switcher is set to 'yes'
			if ('<?php echo esc_attr($settings['eae_custom_overlay_switcher']); ?>' === 'yes') {
				$element.find('.vegas-overlay').css('background-image', '');
			}
		});
	</script>
		<?php
	}

	public function _print_template( $template, $widget ) {
		if ( $widget->get_name() !== 'section' && $widget->get_name() !== 'column' && $widget->get_name() !== 'container' ) {
			return $template;
		}

		$old_template = $template;
		ob_start();
		?>
		<#

		var rand_id = Math.random().toString(36).substring(7);
		var slides_path_string = '';
		var eae_transition = settings.eae_slider_transition;
		var eae_animation = settings.eae_slider_animation;
		var eae_custom_overlay = settings.eae_custom_overlay_switcher;
		var eae_overlay = '';
		var eae_cover = settings.eae_slider_cover;
		var eae_delay = settings.eae_slider_delay;
		var eae_timer = settings.eae_slider_timer_bar;

		if(!_.isUndefined(settings.eae_bg_slider_images) && settings.eae_bg_slider_images.length){
		var slider_data = [];
		slides = settings.eae_bg_slider_images;
		for(var i in slides){
		slider_data[i]  = _.escape(slides[i].url);
		}
		slides_path_string = slider_data.join();
		}

		if(settings.eae_custom_overlay_switcher == 'yes'){
		//if(settings.eae_slider_custom_overlay_image.url){
		//eae_overlay = settings.eae_slider_custom_overlay_image.url;
		//}else{
		eae_overlay = '00.png';
		//}
		}else{
		if(settings.eae_slider_overlay){
		eae_overlay = settings.eae_slider_overlay + '.png';
		}else{
		eae_overlay = '00.png';
		}
		}
		#>

		<div class="eae-section-bs">
			<div class="eae-section-bs-inner"
				data-eae-bg-slider="{{ slides_path_string }}"
				data-eae-bg-slider-transition="{{ eae_transition }}"
				data-eae-bg-slider-animation="{{ eae_animation }}"
				data-eae-bg-custom-overlay="{{ eae_custom_overlay }}"
				data-eae-bg-slider-overlay="{{ eae_overlay }}"
				data-eae-bg-slider-cover="{{ eae_cover }}"
				data-eae-bs-slider-delay="{{ eae_delay }}"
				data-eae-bs-slider-timer="{{ eae_timer }}"
			></div>
		</div>

		<?php
		$slider_content = ob_get_contents();
		ob_end_clean();
		$template = $slider_content . $old_template;

		return $template;
	}

}


<?php

namespace WTS_EAE;

use WTS_EAE\Classes\Helper;
use WTS_EAE\Plugin;

class Admin_Ui {
    public static $instance;

	public $module_manager;
	private $screens = [];
	protected $modules = [];

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_admin_menu' ], 20 );
		add_action('in_admin_header', [$this, 'top_bar']);
		//add_action( 'wp_ajax_load_eae_modules', [ $this, 'load_eae_modules' ] );
		add_action( 'wp_ajax_eae_elements_save', [ $this, 'eae_save_elements' ] );
		add_action( 'wp_ajax_eae_save_config', [ $this, 'eae_save_config' ] );
		$this->set_screens();
		
	}
	// public function load_eae_modules(){
	// 	$helper        = new Helper();
	// 	$modules = $helper->get_eae_modules();
	// 	$items    = [];
	// 	foreach($modules as $key => $module){
	// 		$items[$key] = $module['enabled'];
	// 	}
	// 	if ( current_user_can( 'manage_options' ) ) {
	// 		update_option( 'wts_eae_elements', $items );
	// 	}
	// 	//echo "<pre>"; print_r($items); echo "</pre>";
	// 	wp_send_json([
	// 		'modules' => $items
	// 	]);
	// }

	public function eae_save_elements() {
		if(!wp_verify_nonce($_REQUEST['nonce'], 'eae_ajax_nonce')){
			wp_send_json([
				'success' => 0
			]);
		}
		$helper        = new Helper();
		//check_ajax_referer( 'eae_ajax_nonce', 'nonce' );
		$elements = $_REQUEST['moduleData'];
		if(empty($elements)){
			return;
		}
		$modules = $helper->get_eae_modules();
		$items    = [];
		$count    = count( $modules );
		foreach($elements as $element => $value){
			if($value == 'deactivate'){
				$enabled = "false";
			}else{
				$enabled = "true";
			}
			$modules[$element]['enabled'] = $enabled;
		}
		foreach($modules as $key => $module){
			$items[$key] = $module['enabled'];
		}
		if ( current_user_can( 'manage_options' ) ) {
			update_option( 'wts_eae_elements', $items );
		}
		wp_send_json([
			'modules' => $items,
			'success' => 1,
		]);
	}

	public function eae_save_config() {
		if(!wp_verify_nonce($_REQUEST['nonce'], 'eae_ajax_nonce')){
			wp_send_json([
				'success' => 0
			]);
		}
		$settings = $_REQUEST['config'];
		$gmap_api = sanitize_text_field($settings['wts_eae_gmap_key']);
		$youtube_api_key = sanitize_text_field($settings['wts_eae_youtube_api_key']);
		if ( current_user_can( 'manage_options' ) ) {
			update_option( 'wts_eae_gmap_key', $gmap_api );
			update_option('wts_eae_youtube_api_key', $youtube_api_key);
			// if($settings['eae_particle_library'] == 'tsParticle'){
			// 	$use_tsParticle = 'true';
			// }else{
			// 	$use_tsParticle = 'false';	
			// }

			// update_option( 'use_tsParticle', $use_tsParticle);
		}
		wp_send_json([
			'success' => 1
		]);
	}

	public function register_admin_menu() {
		$icon = $this->get_base64_logo();
		add_menu_page(
			__( 'Addon Elements for Elementor', 'wts-eae' ),
			__( 'Addon Elements', 'wts-eae' ),
			'manage_options',
			'eae-settings',
			[ $this, 'display_settings_page' ],
			$icon,
			99
		);
	}

	public function get_base64_logo( $color = '#ffffff' ) { //#E82A5B
		$base_logo    = '<svg width="533" height="531" viewBox="0 0 533 531" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M76.921 14.2327C82.4829 5.30144 92.3143 -0.096929 102.838 0.00194647L210.054 1.00932C220.961 1.11181 230.932 7.10123 236.101 16.6569L285.799 108.524C290.902 117.958 290.539 129.414 284.848 138.553L227.492 230.655C221.93 239.586 212.099 244.985 201.575 244.886L94.3596 243.878C83.4517 243.776 73.4816 237.787 68.3122 228.231L18.6142 136.364C13.511 126.93 13.8737 115.474 19.565 106.335L76.921 14.2327Z" fill="' . $color . '"/>
		<path d="M320.511 161.233C326.073 152.302 335.905 146.904 346.428 147.003L453.644 148.01C464.552 148.112 474.522 154.102 479.691 163.657L529.389 255.525C534.493 264.958 534.13 276.415 528.439 285.554L471.083 377.656C465.521 386.587 455.689 391.985 445.166 391.887L337.95 390.879C327.042 390.777 317.072 384.787 311.903 375.232L262.205 283.364C257.101 273.931 257.464 262.474 263.155 253.335L320.511 161.233Z" fill="' . $color . '"/>
		<path d="M61.8952 300.178C67.4571 291.247 77.2885 285.848 87.8119 285.947L195.028 286.955C205.936 287.057 215.906 293.047 221.075 302.602L270.773 394.47C275.876 403.903 275.514 415.359 269.822 424.499L212.466 516.601C206.904 525.532 197.073 530.93 186.55 530.831L79.3338 529.824C68.426 529.721 58.4558 523.732 53.2864 514.176L3.58845 422.309C-1.51479 412.875 -1.15203 401.419 4.53929 392.28L61.8952 300.178Z" fill="' . $color . '"/>
		</svg>'; //AE Hexagon logo
		
		$encoded_logo = base64_encode( $base_logo ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- The encoded version is used as data URI to use the logo in CSS.

		return 'data:image/svg+xml;base64,' . $encoded_logo;
	}

	public function display_settings_page() {
		$helper        = new Helper();
		$eae_widgets = [];
		$eae_ext = [];
		$this->modules = $helper->get_eae_modules();
		$map_key = get_option('wts_eae_gmap_key');
		$youtube_api_key = get_option('wts_eae_youtube_api_key');
		$use_tsParticle = get_option('use_tsParticle' ,false);
		$modules = apply_filters( 'wts_eae_active_modules', $this->modules );

		
		foreach($modules as $module_key => $module){
			if($module['type'] == 'widget'){
				$eae_widgets[$module_key] = $module;
			}else{
				$eae_ext[$module_key] = $module;
			}
		}

		// sort $eae_widgets by array key
		ksort($eae_widgets);

		?>
		<div class="eae-wrap">
			<div class="eae-content-wrapper">
				<div class="eae-settings-main-wrapper">
					<div class="eae-tabs tabs">
						<h3 class="eae-title eae-modules active">
							<a href="#" data-tabid="eae-module-manager">Modules</a>
						</h3>
						<h3 class="eae-title eae-config" id="eae-config">
							<a href="#" data-tabid="eae-config">Configuration</a>
						</h3>

						<?php 
							if(Plugin::$is_pro === false){
								?>
								<h3 class="eae-title eae-get-pro">
									<a href="https://wpvibes.link/go/eae-upgrade" data-tabid="eae-get-pro">Get Pro</a>
								</h3>
								<?php
							}

						?>
						
					</div>
					<div class="eae-settings-box eae-metabox">	
						<div class="eae-metabox-content">
							<form class="eae-tab-content active" id="eae-module-manager" method="post">
								<div class="eae-bulk-action eae-module-row">
									<input type="checkbox" id="eae-select-all" />
									<select name="eae-bulk-action">
										<option value="">Bulk Action</option>
										<option value="activate">Activate</option>
										<option value="deactivate">Deactivate</option>
									</select>
									<input id="eae-apply" class="button" type="button" value="<?php echo esc_attr('Apply', 'wts-eae'); ?>" />
								</div>
								<div class="eae-module-row eae-module-group eae-widgets">
									<h4 class="eae-group-title"><?php echo esc_attr('Widgets', 'aepro'); ?></h4>
								</div>
								<?php
									foreach ($eae_widgets as $module_key => $widget) {
										$pro_text = '';
										//echo "<pre>";  print_r($widget);  echo "</pre>";
										$class = 'eae-module-row';
										if ($widget['enabled'] === 'true' || $widget['enabled'] === true) {
											$class .= ' eae-enabled';
											$action_text = __('Deactivate', 'eae-wts');
											$action = 'deactivate';
										} else {
											$class .= ' eae-disabled';
											$action_text = __('Activate', 'eae-wts');
											$action = 'activate';
										}
										
										if(isset($widget['pro']) && $widget['pro'] === true){
											$pro_text = 'Pro';
										}
										
										?>
										<div class="<?php echo esc_attr($class); ?>">
											<?php 
											
											if(isset($widget['pro']) && Plugin::$is_pro === false){
												?>
												<input class="" type="checkbox" name="" disabled value="<?php echo esc_attr($module_key); ?>" />
												<?php
											}else{
												?>
												<input class="eae-module-item" type="checkbox" name="eae_modules[]" value="<?php echo esc_attr($module_key); ?>" />
												<?php
											}	
											?>
											
											<?php echo esc_html($widget['name']); ?>

											<?php
												if(!empty($pro_text)){
													echo '<span class="eae-pro-label">'.esc_html($pro_text).'</span>';
												}
											?>

											<?php 
												
												if(!empty($pro_text) && Plugin::$is_pro === false){
													?>
													<div class="eae-module-action eae-pro-missing">
														<a href="https://wpvibes.link/go/eae-upgrade/" target="_blank">Upgrade to Pro</a>
													</div>
													<?php
												}else{
													?>
														<div class="eae-module-action">
															<a data-action="<?php echo esc_attr($action); ?>" data-moduleId="<?php echo esc_attr($module_key); ?>" href="#"> <?php echo esc_html($action_text); ?> </a>
														</div>
													<?php
												}
											?>
											
										</div>
								<?php } ?>
								<div class="eae-module-row eae-module-group eae-extension">
									<h4 class="eae-group-title"><?php echo esc_html('Extensions', 'wts-eae'); ?></h4>
								</div>
								<?php
									foreach ($eae_ext as $module_key => $widget) {

										$class = 'eae-module-row';
										if ($widget['enabled'] === 'true' || $widget['enabled'] === true) {
											$class .= ' eae-enabled';
											$action_text = __('Deactivate', 'eae-wts');
											$action = 'deactivate';
										} else {
											$class .= ' eae-disabled';
											$action_text = __('Activate', 'eae-wts');
											$action = 'activate';
										}

								?>
										<div class="<?php echo esc_attr($class); ?>">
											<input class="eae-module-item" type="checkbox" name="eae_modules[]" value="<?php echo esc_attr($module_key); ?>" />
											<?php echo esc_html($widget['name']); ?>

											<div class="eae-module-action">
												<a data-action="<?php echo esc_attr($action); ?>" data-moduleId="<?php echo esc_attr($module_key); ?>" href="#"> <?php echo esc_html($action_text); ?> </a>
											</div>
										</div>
									<?php } ?>
							</form>

							<form class="eae-tab-content" id="eae-config">
								<table>
									<tr>
										<td>
											<label for="wts_eae_gmap_key"> Google Map Api Key </label>
										</td>
										<td>
											<input type="text" name="wts_eae_gmap_key" id="wts_eae_gmap_key" class="regular-text" value="<?php echo esc_html($map_key); ?>">
											<br/>
											<span class="eae-field-desc">
												<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">
													<?php echo esc_html('Click Here') ?>
												</a> to generate API KEY
											</span>
										</td>
									</tr>
									<?php  
										if(Plugin::$is_pro === true){											
									?>
										<tr>
											<td>
												<label for="wts_eae_youtube_api_key"> Youtube Api Key </label>
											</td>
											<td>
												<input type="text" name="wts_eae_youtube_api_key" id="wts_eae_youtube_api_key" class="regular-text" value="<?php echo esc_html($youtube_api_key); ?>">
												<br/>
												<span class="eae-field-desc">
													<a href="https://wpvibes.link/go/youtube-api-key/" target="_blank">
														<?php echo esc_html('Click Here') ?>
													</a> How to generate API KEY
												</span>
											</td>
										</tr>
									<?php } ?>
									<tr>
										<td colspan="2">
											<button type="button" value="Save" class="button button-primary" name="save_config" id="save-config" data-action="save-config">
												<span class="eae-action-text">Save</span>
												<span class="eae-action-loading dashicons dashicons-update-alt"></span>
											</button>		
										</td>
										
									</tr>
								</table>		

							</form>
						</div>
					</div>
				</div>
				<div class="eae-settings-sidebar-wrapper">
					<!-- Branding Box -->
					<div class="eae-sidebar-card eae-brand-box">
						<div class="eae-brand-header">
							<span class="eae-old-title"><del>Elementor Addon Elements</del></span>
							<h2 class="eae-new-title">Addon Elements for Elementor</h2>

							<div class="eae-badge">
								🎉 New Brand Identity
							</div>
						</div>

						<p class="eae-brand-desc">
							We're excited to announce our rebranding! Same great features, fresh new look and name.
						</p>

						<a href="https://qikly.ink/rebrand" target="_blank" class="eae-btn-primary">
							Read Full Story <span class="external-link"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-external-link" aria-hidden="true"><path d="M15 3h6v6"></path><path d="M10 14 21 3"></path><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path></svg>
</span>
						</a>
					</div>


					<!-- Help Box -->
					<div class="eae-sidebar-card eae-help-box">
						<h3>Need Help?</h3>

						<p>
							Check our documentation or visit the support forum.
						</p>

						<a href="https://qikly.ink/docs" target="_blank" class="eae-btn-outline">
							View Documentation
						</a>
					</div>

					<?php if(!Plugin::$is_pro){
						?>
						<div class="eae-promo-box eae-sidebar-box">
							<h3>🚀 Upgrade to <b>Addon Elements for Elementor Pro</b> Today!</h3>
							<p>
							Get meticulously crafted premium widgets to enhance your creative potential. Unleash your imagination and design captivating layouts with ease. 	
							</p>
							<ul class="eae-pro-features">
								<li>Premium Widgets</li>
								<li>Priority Support</li>
								<li>Lifetime deal available</li>
								<li>14 Days Money Back Guarantee</li>					
							</ul>
							<a href="https://wpvibes.link/go/eae-upgrade" target="_blank" class="eae-go-pro">Upgrade Now</a>	
							<em>Give it a risk free trial with our <b>14 Days No Questions Asked</b> refund policy.</em>
						</div>
						<?php
					}
					?>
					
				</div>
			</div>
		</div>
		<?php
	}


	protected function set_screens()
	{

		$this->screens = [
			'toplevel_page_eae-settings',
		];
	}



    public function top_bar(){

		$nav_links = [
			'toplevel_page_eae-settings' => [
				'label' => __('Home', 'wts-eae'),
				'link'  => 'admin.php?page=eae-settings'
			],
			'doc' => [
				'label' => __('Documentation', 'wts-eae'),
				'link'  => 'https://wpvibes.link/go/ea-docs/'
			],
			'support' => [
				'label' => __('Get Support', 'wts-eae'),
				'link'  => 'https://wpvibes.link/go/ea-support/'
			]
		];


		$current_screen = get_current_screen();
		//print_r( $current_screen->id);

		if (!in_array($current_screen->id, $this->screens)) {
			return;
		}


?>

		<div class="eae-admin-topbar">
			<div class="eae-branding">
				<svg viewBox="0 0 1908 1891" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M954 0C1050.4 0 1143.46 14.172 1231.18 40.5273L1180.96 119.803C1108.66 100.373 1032.57 90 954 90C476.065 90 90 473.778 90 945.5C90 1417.22 476.065 1801 954 1801C1431.94 1801 1818 1417.22 1818 945.5C1818 725.547 1734.06 524.715 1596.02 372.993L1645.79 294.43C1808.29 463.974 1908 693.196 1908 945.5C1908 1467.69 1480.88 1891 954 1891C427.12 1891 0 1467.69 0 945.5C0 423.315 427.12 0 954 0Z" fill="#183A59"/>
					<path d="M1250.81 55.846C1256.37 46.9127 1266.2 41.5092 1276.72 41.6013L1359.58 42.3269C1370.5 42.4226 1380.49 48.4263 1385.66 58.0047L1423.95 128.978C1429.03 138.401 1428.67 149.836 1422.99 158.963L1378.48 230.516C1372.93 239.45 1363.1 244.853 1352.58 244.761L1269.72 244.036C1258.8 243.94 1248.81 237.936 1243.64 228.358L1205.35 157.384C1200.27 147.961 1200.63 136.526 1206.31 127.399L1250.81 55.846Z" fill="#E82A5B"/>
					<path d="M1452.55 177.73C1458.1 168.797 1467.93 163.394 1478.45 163.486L1561.31 164.211C1572.24 164.307 1582.22 170.311 1587.39 179.889L1625.68 250.863C1630.76 260.286 1630.4 271.721 1624.72 280.848L1580.22 352.401C1574.66 361.334 1564.83 366.738 1554.31 366.646L1471.46 365.92C1460.53 365.824 1450.54 359.821 1445.37 350.242L1407.08 279.269C1402 269.845 1402.36 258.411 1408.04 249.284L1452.55 177.73Z" fill="#E82A5B"/>
					<path d="M1238.21 293.201C1243.76 284.268 1253.59 278.864 1264.11 278.957L1346.97 279.682C1357.9 279.778 1367.88 285.782 1373.05 295.36L1411.34 366.333C1416.42 375.757 1416.06 387.192 1410.38 396.318L1365.88 467.872C1360.32 476.805 1350.49 482.209 1339.97 482.116L1257.12 481.391C1246.19 481.295 1236.2 475.291 1231.03 465.713L1192.74 394.74C1187.66 385.316 1188.02 373.881 1193.7 364.755L1238.21 293.201Z" fill="#E82A5B"/>
					<path d="M1341.12 1209.21L1333.97 1268.77L1331.93 1285.81L1314.78 1286.38L1190.52 1290.49L1152.5 1208.3L1318.72 1186.98L1344.18 1183.72L1341.12 1209.21ZM1316.12 683.145L1308.97 742.711L1306.57 762.646L1286.65 760.175L1113.55 738.697C1097.18 739.32 1082.19 741.201 1068.54 744.26L1068.18 744.34L1067.82 744.407C1057.44 746.334 1046.41 748.858 1034.71 751.992L1041.32 931.298L1228.69 925.101L1248.6 924.441L1249.34 944.353L1250.58 977.962L1251.32 998.023L1231.25 998.688L1058.16 1004.41L905.141 673.686L921.182 673.156L1295.6 660.771L1318.9 660L1316.12 683.145ZM1093.07 720.091C1092.54 720.146 1092.01 720.202 1091.48 720.26C1092.54 720.144 1093.6 720.034 1094.67 719.93L1093.07 720.091Z" fill="#E82A5B"/>
					<path d="M857.64 670.903L860.33 676.684L1134.74 1266.36L1141.35 1280.58H1018.45L1015.82 1274.63L949.157 1124.03H687.807L622.056 1274.58L619.437 1280.58H547.316L553.934 1266.36L828.339 676.684L831.028 670.903H857.64ZM710.438 1070.98H925.66L818.857 824.203L710.438 1070.98Z" fill="#183A59" stroke="#183A59" stroke-width="20"/>
				</svg>


				<h1>Addon Elements for Elementor <em style="font-size: 16px;">(formerly Elementor Addon Elements)</em></h1>
				<span class="eae-version"><?php echo esc_html(EAE_VERSION); ?></span>
			</div>


			<nav class="eae-nav">
				<ul>
					<?php
					if (isset($nav_links) && count($nav_links)) {
						foreach ($nav_links as $id => $link) {
							
							
							$active = ($current_screen->id === $id) ? 'eae-nav-active' : '';

							$target = '';
							if ($id === 'doc' || $id === 'support') {
								$target = 'target="_blank"';
							}
					?>
							<li class="<?php echo esc_attr($active); ?>">
								<a <?php echo esc_attr($target); ?> href="<?php echo esc_attr($link['link']); ?>"><?php echo esc_attr($link['label']); ?></a>
							</li>
					<?php
						}
					}
					?>
				</ul>
			</nav>
		</div>

	<?php
	}
    
}
new Admin_Ui();
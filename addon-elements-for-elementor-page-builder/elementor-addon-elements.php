<?php

/**
 * Plugin Name: Elementor Addon Elements
 * Description: Elementor Addon Elements comes with 40+ widgets and extensions to extend the power of Elementor Page Builder.
 * Plugin URI: https://www.elementoraddons.com/elements-addon-elements/
 * Author: WPVibes
 * Version: 1.14.1
 * Author URI: https://wpvibes.com/
 * Elementor tested up to: 3.29
 * Elementor Pro tested up to: 3.29
 * Text Domain: wts-eae
 * @package WTS_EAE
 */

if(! defined('EAE_FILE')){
	define( 'EAE_FILE', __FILE__ );
	define( 'EAE_PLUGIN_BASE', plugin_basename( EAE_FILE ) );
	define( 'EAE_URL', plugins_url( '/', __FILE__ ) );
	define( 'EAE_PATH', plugin_dir_path( __FILE__ ) );
	define( 'EAE_SCRIPT_SUFFIX', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' );
	define( 'EAE_VERSION', '1.14.1' );
}


if ( ! function_exists( '_is_elementor_installed' ) ) {

	function _is_elementor_installed() {
		$file_path         = 'elementor/elementor.php';
		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $file_path ] );
	}
}

if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

/**
 * Handles plugin activation actions.
 *
 * @since 1.0
 */
if(!function_exists('eae_activate')){
	
	function eae_activate() {
		
		if ( ! is_plugin_active( 'elementor/elementor.php' ) ) {
			add_action( 'admin_notices', function(){
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php esc_html_e( 'Elementor Addon Elements requires Elementor plugin to be active.', 'wts-eae' ); ?></p>
				</div>
				<?php
			});
			return;
		}else{

		}
		\Elementor\Plugin::$instance->files_manager->clear_cache();
	}
}

register_activation_hook( EAE_FILE, 'eae_activate' );

// get current file path
$current_file_path = __FILE__;
$is_pro = false;
$active_plugins = get_option( 'active_plugins' );
// Old Free Plugin
if(strpos($current_file_path, 'elementor-addon-elements/elementor-addon-elements.php') !== false){
	$is_pro = true;
	if($is_pro){
		// free is also active, unset it so that it won't be loaded
		if(in_array('addon-elements-for-elementor-page-builder/elementor-addon-elements.php', $active_plugins)){
			deactivate_plugins('addon-elements-for-elementor-page-builder/elementor-addon-elements.php');
			add_action( 'admin_notices', function(){
				?>
				<div class="notice notice-error is-dismissible">
					<p><?php esc_html_e( 'Elementor Addon Elements (Free) cannot be activated along with Pro version', 'wts-eae' ); ?></p>
				</div>
				<?php
			});
		}
	}
}



if(!function_exists('wts_eae_pro_fail_load')){
	function wts_eae_pro_fail_load() {
		$plugin = 'elementor/elementor.php';
	
		if ( _is_elementor_installed() ) {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
			$message      = esc_html__( 'Elementor Addon Elements is not working because you need to activate the Elementor plugin.', 'wts-eae' );
			$action_url   = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
			$button_label = __( 'Activate Elementor', 'wts-eae' );
		} else {
			if ( ! current_user_can( 'install_plugins' ) ) {
				return;
			}
			$message      = esc_html__( 'Elementor Addon Elements is not working because you need to install the Elementor plugin.', 'wts-eae' );
			$action_url   = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
			$button_label = __( 'Install Elementor', 'wts-eae' );
		}
	
		$button = '<p><a href="' . $action_url . '" class="button-primary">' . $button_label . '</a></p><p></p>';
	
		//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		printf( '<div class="%1$s"><p>%2$s</p>%3$s</div>', 'notice notice-error', $message, $button );
	}
}

add_action( 'plugins_loaded', function(){

		if ( ! did_action( 'elementor/loaded' ) ) {
			/* TO DO */
			add_action( 'admin_notices', 'wts_eae_pro_fail_load' );
			return;
		}else{
			$active_plugins = get_option( 'active_plugins' );
			$current_file_path = __FILE__;
			$is_pro = false;
			$php_version = phpversion();
			
			if(strpos($current_file_path, 'elementor-addon-elements/elementor-addon-elements.php') !== false){
				// free is also active, unset it so that it won't be loaded
				$is_pro = true;
				
				require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
				
				require_once plugin_dir_path( __FILE__ ) . 'inc/bootstrap.php';
			}else{
				if(in_array('elementor-addon-elements/elementor-addon-elements.php', $active_plugins)){
					
				}else{
					//require_once EAE_PATH . 'vendor/autoload.php';
					require_once plugin_dir_path( __FILE__ ) . 'inc/bootstrap.php';
				}
			}
		}
		// check is elementor installed and activated			
		
});
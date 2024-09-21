<?php
namespace WTS_EAE\Managers;

use WTS_EAE\Classes\Helper;
use WTS_EAE\Plugin as EAE;
class Module_Manager {
	protected $modules = [];
	public function __construct() {
		$helper        = new Helper();
		$this->modules = $helper->get_eae_modules();

		$this->modules = apply_filters( 'wts_eae_active_modules', $this->modules );
		// Todo:: apply filter for modules that depends on third party plugins
		foreach ( $this->modules as $key => $module_name ) {
			
			if ( $module_name['enabled'] == 'true' || trim( $module_name['enabled'] ) === '' || $module_name['enabled'] === null ) {

				$class_name                            = str_replace( '-', ' ', $key );
				$class_name                            = str_replace( ' ', '', ucwords( $class_name ) );
				
				if(isset($module_name['pro']) && $module_name['pro'] === true ){
					if(!EAE::$is_pro == true){
						continue;
					}else{
						$class_name	= 'WTS_EAE\\Pro\\Modules\\' . $class_name . '\Module';
					}
				}else{
					$class_name = 'WTS_EAE\\Modules\\' . $class_name . '\Module';
				}
	
				$this->modules[ $module_name['name'] ] = $class_name::instance();
				if(EAE::$is_pro){
					if(isset($module_name['freemium']) && $module_name['freemium']){
						$name                            = str_replace( '-', ' ', $key );
						$name                            = str_replace( ' ', '', ucwords( $name ) );
						$name = 'WTS_EAE\\Pro\\Modules\\' . $name . '\Module';
						
						// check if class exists
						if(class_exists($name) && EAE::$is_pro == true){
							$this->modules[ $module_name['name'].'_pro' ] = $name::instance();
						}	
					}
				}
				
			}
		}
	}
}

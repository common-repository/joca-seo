<?php 

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Model Class
 *
 * Handles generic plugin functionality.
 *
 * @package Joca SEO
 * @since 1.0.0
 */
class Wp_jocaseo_Model {

	public function __construct() {
	
	}

	/**
	 * Escape Tags & Slashes
	 *
	 * Handles escapping the slashes and tags
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_escape_attr($data){
		return esc_attr(stripslashes($data));
	}
	
	/**
	 * Strip Slashes From Array
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_escape_slashes_deep($data = array(),$flag=false){
			
		if($flag != true) {
			$data = $this->jocaseo_nohtml_kses($data);
		}
		$data = stripslashes_deep($data);
		return $data;
	}
	
	/**
	 * Strip Html Tags 
	 * 
	 * It will sanitize text input (strip html tags, and escape characters)
	 * 
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_nohtml_kses($data = array()) {
		
		if ( is_array($data) ) {
			
			$data = array_map(array($this,'jocaseo_nohtml_kses'), $data);
			
		} elseif ( is_string( $data ) ) {
			
			$data = wp_filter_nohtml_kses($data);
		}
		
		return $data;
	}

} //End of Class
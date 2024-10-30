<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Scripts Class
 *
 * Handles all scripts functionality
 *
 * @package Joca SEO
 * @since 1.0.0
 */
class Wp_jocaseo_Scripts {

	public $model;

	public function __construct() {

		global $jocaseo_model;
		$this->model 	= $jocaseo_model;
	}

	/**
	 * Handles admin side scripts/styles
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_admin_scripts() {

		// Enqueue styles
		wp_register_style( 'jocaseo_admin', JOCASEO_URL.'css/jocaseo-admin.css' );
		wp_enqueue_style( 'jocaseo_admin' );

		// Enqueue scripts
		wp_register_script( 'jocaseo_admin', JOCASEO_URL.'js/jocaseo-admin.js' );
		wp_enqueue_script( 'jocaseo_admin' );
	}

	/**
	 * Adding Hooks
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function add_hooks() {

		// add admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'jocaseo_admin_scripts' ) );
	}
}

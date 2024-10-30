<?php
/**
 * Plugin Name: Joca SEO
 * Plugin URI: http://www.jocawp.com/joca-seo/
 * Description: Joca SEO allows you to add meta title and description in WordPress posts, pages & categories plus Woocommerce products and product categories to boost your positions in the search engines. Only 30 kb fast and optimized code.
 * Version: 1.0.0
 * Author: Joca
 * Author URI: http://www.jocawp.com
 * Text Domain: joca-seo
 * Domain Path: languages
 */

if ( !defined( 'ABSPATH' ) ) exit;

global $wpdb;

if( !defined( 'JOCASEO_DIR' ) ) {
	define( 'JOCASEO_DIR', dirname( __FILE__ ) );
}
if( !defined( 'JOCASEO_URL' ) ) {
	define( 'JOCASEO_URL', plugin_dir_url( __FILE__ ) );
}
if( !defined( 'JOCASEO_ADMIN_DIR' ) ) {
	define( 'JOCASEO_ADMIN_DIR', JOCASEO_DIR . '/includes/admin' );
}
if( !defined( 'JOCASEO_META_PREFIX' ) ) {
	define( 'JOCASEO_META_PREFIX', '_jocaseo_' );
}

function joca_seo_load_plugin_textdomain() {
    $loaded = load_plugin_textdomain( 'joca-seo', FALSE, basename(dirname(__FILE__)).'/languages/');
}
add_action( 'plugins_loaded', 'joca_seo_load_plugin_textdomain' );

register_activation_hook( __FILE__, 'jocaseo_install' );
function jocaseo_install() {
	global $wpdb, $jocaseo_options;

}

global $jocaseo_model, $jocaseo_public, $jocaseo_admin, $jocaseo_scripts, $jocaseo_options;
$jocaseo_options = get_option('jocaseo_options');

require_once( JOCASEO_DIR . '/includes/class-jns-jocaseo-scripts.php' );
$jocaseo_scripts = new Wp_jocaseo_Scripts();
$jocaseo_scripts->add_hooks();

require_once( JOCASEO_DIR . '/includes/class-jns-jocaseo-model.php' );
$jocaseo_model = new Wp_jocaseo_Model();

require_once( JOCASEO_ADMIN_DIR . '/class-jns-jocaseo-admin.php' );
$jocaseo_admin = new Wp_jocaseo_Admin();
$jocaseo_admin->add_hooks();

require_once( JOCASEO_DIR . '/includes/class-jns-jocaseo-public.php' );
$jocaseo_public = new Wp_jocaseo_Public();
$jocaseo_public->add_hooks();

function jocaseo_start() {
		$jocaseo_is_admin=current_user_can('manage_options');
		if ($jocaseo_is_admin && is_admin()) {
				add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'jocaseo_add_action_links');
				function jocaseo_add_action_links ( $links ) {
						$mylinks = array(
						'<a href="' . admin_url( 'options-general.php?page=jocaseo-settings' ) . '">' . __("Settings", 'joca-seo') . '</a>',
						'<a href="http://www.jocawp.com/joca-seo/" target="_blank">' . __("Get Pro", 'joca-seo') . '</a>'
						);
						return array_merge( $links, $mylinks );
				}
		}
}
add_action ('init', 'jocaseo_start');

<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Public Pages Class
 *
 * Handles all the different features and functions
 * for the front end pages.
 *
 * @package Joca SEO
 * @since 1.0.0
 */
class Wp_jocaseo_Public {
	
	public $model;
	
	public function __construct() {

		global $jocaseo_model;

		$this->model 	= $jocaseo_model;
	}

	/**
	 * Main title function.
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_page_title( $title, $separator = '' ) {

		global $post, $jocaseo_options;

		// Get the prefix
		$prefix = JOCASEO_META_PREFIX;

		if( is_home() || is_front_page() ) {

			$title = isset($jocaseo_options['home_title']) ? $jocaseo_options['home_title'] : get_bloginfo( 'name' );
			
		} elseif ( is_singular() ) {

			if ( !empty($post->ID) ) {
				$page_title = get_post_meta( $post->ID, $prefix.'meta_title', true );
			}

		} elseif ( is_category() || is_tag() || is_tax() ) {

			$term = $GLOBALS['wp_query']->get_queried_object();
			if( !empty($term->term_id) ) {

				$term_id = $term->term_id;
				$term_meta = get_option( "jocaseo_tax_$term_id" );
				$page_title = isset($term_meta['meta_title']) ? $term_meta['meta_title'] : '';
			}
		}

		$title = !empty($page_title) ? $page_title : $title;
		return $title;
	}

	/**
	 * Adding Description meta tag in head section
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_add_description_tag() {

		global $post, $jocaseo_options;

		// Get the prefix
		$prefix = JOCASEO_META_PREFIX;

		if( is_home() || is_front_page() ) {
			
			$desc_meta = isset($jocaseo_options['home_desc']) ? $jocaseo_options['home_desc'] : '';

		} elseif ( is_singular() ) {

			if ( !empty($post->ID) ) {
				$desc_meta = get_post_meta( $post->ID, $prefix.'meta_desc', true );
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {

			$term = $GLOBALS['wp_query']->get_queried_object();
			if( !empty($term->term_id) ) {

				$term_id = $term->term_id;
				$term_meta = get_option( "jocaseo_tax_$term_id" );
				$desc_meta = isset($term_meta['meta_desc']) ? $term_meta['meta_desc'] : '';
			}
		}

		if( !empty( $desc_meta ) ) { ?>
			<meta name="description" content="<?php echo $desc_meta; ?>" />
		<?php }
	}

	/**
	 * Adding Hooks
	 *
	 * Adding proper hoocks for the public pages.
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function add_hooks() {

		// Filter for change title tag
		add_filter( 'wp_title', array( $this, 'jocaseo_page_title' ), 99, 2 );
		add_filter('pre_get_document_title', array( $this, 'jocaseo_page_title' ));
		// Add meta description tag
		add_action( 'wp_head', array( $this, 'jocaseo_add_description_tag'), 0 );
	}
} // End of public class
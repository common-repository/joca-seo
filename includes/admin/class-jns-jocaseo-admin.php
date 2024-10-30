<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Admin Class
 *
 * Handles generic Admin functionality and AJAX requests.
 *
 * @package Joca SEO
 * @since 1.0.0
 */
class Wp_jocaseo_Admin {

	public $model;

	public function __construct() {

		global $jocaseo_model;

		$this->model 	= $jocaseo_model;
	}

	/**
	 * Adding metabox to each post types
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_add_posts_type_metabox() {

		$screens = array( 'post', 'page', 'product' );

		foreach ( $screens as $screen ) {

			add_meta_box(
				'jocaseo_posts_metas',
				__( 'Joca SEO', 'jocaseo' ),
				array($this, 'jocaseo_posts_metaboxes_fields'),
				$screen
			);
		} // End of foreach
	}

	/**
	 * Adding metabox fields
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_posts_metaboxes_fields( $post ) {

		// Check for post id
		$post_id = isset($post->ID) ? $post->ID : '';
		if( empty($post_id) ) return;

		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'jocaseo_posts_metaboxes', 'jocaseo_posts_metabox_nonce' );

		// Get the prefix
		$prefix = JOCASEO_META_PREFIX;

		// Get the variables
		$title 	 = get_post_meta( $post_id, $prefix.'meta_title', true );
		$desc 	 = get_post_meta( $post_id, $prefix.'meta_desc', true );

		// Check if empty fields
		$title 		= !empty($title) ? $title : '';
		$h2title	= !empty($title) ? $title : get_the_title( $post_id );
		//$desc		= !empty($desc) ? $desc : 'Enter description!';

		$autofill_class = '';
		if( $h2title == 'Auto Draft' ) {
			$h2title = __('Enter title', 'jocaseo');
			$autofill_class = 'jocaseo-autofill-title';
		} ?>

		<input type="hidden" name="jocaseo_inline_edit" />
		<table class="form-table jocaseo-form-table">
			<tr>
				<th>
					<label for="jocaseo_meta_preview"><?php _e('Edit Search Result', 'jocaseo'); ?></label>
				</th>
				<? if ( $post_id == get_option( 'page_on_front' ) ) {?>
				<td class="jocaseo-form-fields">
				Start page meta is edited through <a href="<?php echo admin_url('options-general.php?page=jocaseo-settings')?>"><?php echo __("Settings", 'joca-seo') . " - Joca SEO"?></a>
				</td>
				<?} else { ?>
				<td class="jocaseo-form-fields">
					<h2 onclick="joca_showInput(this, 'jocaseo_meta_title');" class="jocaseo_title <?php echo $autofill_class; ?>"><?php esc_attr_e($title?$title:'Enter title');  ?></h2>
					<input onkeydown="joca_checkEnter(event, this);" maxlength="60" onblur="joca_disableInput(this, 'jocaseo_title');" placeholder="Enter title" class="jocaseo_meta_title large-text" name="_jocaseo_meta_title" style="display: none;" value="<?php esc_attr_e($title); ?>" type="text" />

					<p onclick="joca_showInput(this, 'jocaseo_meta_desc');" class="jocaseo_desc" ><?php esc_attr_e($desc?$desc:'Enter description'); ?></p>
					<textarea onkeydown="joca_checkLen(event, this);" onpaste="joca_checkLen(event, this);" onblur="joca_disableInput(this, 'jocaseo_desc');" placeholder="Enter description" class="jocaseo_meta_desc large-text" name="_jocaseo_meta_desc"  maxlength=160><?php esc_attr_e($desc); ?></textarea>
				</td>
				<?}?>
			</tr>
		</table>
	<?php
	}

	/**
	 * Save metabox fields
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_save_posts_type_metabox( $post_id ) {

		// Check post id
		if( empty($post_id) ) return;

		// Check if our nonce is set.
		if ( !isset( $_POST['jocaseo_posts_metabox_nonce'] ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Get the prefix
		$prefix = JOCASEO_META_PREFIX;

		// Get data of metabox
		$title = isset( $_POST[$prefix.'meta_title'] ) ? $this->model->jocaseo_escape_slashes_deep($_POST[$prefix.'meta_title']) : '';
		$desc  = isset( $_POST[$prefix.'meta_desc'] ) ? $this->model->jocaseo_escape_slashes_deep($_POST[$prefix.'meta_desc']) : '';

		// Update metaboxes
		update_post_meta( $post_id, $prefix.'meta_title', $title );
		update_post_meta( $post_id, $prefix.'meta_desc', $desc );
	}

	/**
	 * Add texonomy fields
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_category_edit_meta_field( $term ) {

		// Put the term ID into a variable.
		$term_id = isset($term->term_id) ? $term->term_id : '';

		// Return if no term id
		if( empty($term_id) ) return;

		// Retrieve the existing value(s) for this meta field. This returns an array.
		$term_meta = get_option( "jocaseo_tax_$term_id" );

		// Get valuse
		$title = isset($term_meta['meta_title']) ? $term_meta['meta_title'] : '';
		$desc = isset($term_meta['meta_desc']) ? $term_meta['meta_desc'] : '';

		// Check if empty fields
		$title 	= !empty($title) ? $title : $term->name;
		//$desc 	= !empty($desc) ? $desc : 'Enter description!'; //description

		ob_start(); ?>
			<tr class="jocaseo_cat_table">
				<th colspan="2">
					<h2 class="title"><?php _e('Joca SEO', 'jocaseo'); ?></h2>

					<table class="form-table jocaseo_cat_table_content">
						<tr>
							<th>
								<label for="jocaseo_meta_preview"><?php _e('Edit Search Result', 'jocaseo'); ?></label>
							</th>
							<td class="jocaseo-form-fields">
								<h2 onclick="joca_showInput(this, 'jocaseo_meta_title');" class="jocaseo_title"><?php esc_attr_e($title?$title:'Enter title'); ?></h2>
								<input onkeydown="joca_checkEnter(event, this);" maxlength="60" onblur="joca_disableInput(this, 'jocaseo_title');" placeholder="Enter title" class="jocaseo_meta_title large-text" name="jocaseo_tax[meta_title]" style="display: none;" value="<?php esc_attr_e($title); ?>" type="text" />

								<p onclick="joca_showInput(this, 'jocaseo_meta_desc');" class="jocaseo_desc" ><?php esc_attr_e($desc?$desc:'Enter description'); ?></p>
								<textarea onkeydown="joca_checkLen(event, this);" onpaste="joca_checkLen(event, this);" onblur="joca_disableInput(this, 'jocaseo_desc');" placeholder="Enter description" class="jocaseo_meta_desc large-text" name="jocaseo_tax[meta_desc]"  maxlength=160><?php esc_attr_e($desc); ?></textarea>
							</td>
						</tr>
					</table>
				</th>
			</tr>
		<?php ob_end_flush();
	}

	/**
	 * Save texonomy meta data
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_category_save_meta_field( $term_id ) {

		// Check term id is not empty
		if( empty($term_id) ) return;

		// Check isset tax
		if ( isset( $_POST['jocaseo_tax'] ) ) {

			$term_meta = get_option( "jocaseo_tax_$term_id" );
			$cat_keys = array_keys( $_POST['jocaseo_tax'] );

			foreach ( $cat_keys as $key ) {

				if ( isset($_POST['jocaseo_tax'][$key]) ) {
					$term_meta[$key] = $this->model->jocaseo_escape_slashes_deep($_POST['jocaseo_tax'][$key]);
				}
			}

			// Save the option array.
			update_option( "jocaseo_tax_$term_id", $term_meta );
		}
	}

	/**
	 * Manage SEO column
	 * posts, page and product post type
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_manage_seo_column( $columns ) {

		$columns['joca_seo'] = __( 'Joca SEO', 'jocaseo' );
		return $columns;
	}

	/**
	 * Manage SEO column value
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_manage_seo_column_value( $column, $post_id ) {

		// Get the prefix
		$prefix = JOCASEO_META_PREFIX;

		switch ( $column ) {
			case 'joca_seo':

				$meta_desc = get_post_meta( $post_id, $prefix.'meta_desc', true );
				if( !empty($meta_desc) ) {
					echo "<img src='".JOCASEO_URL."images/joca-yes-meta.png' alt='Right' />";
				} else {
					echo "<img src='".JOCASEO_URL."images/joca-no-meta.png' alt='Wrong' />";
				}
			break;
		}
	}

	/**
	 * Manage SEO column value for texonomy
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_manage_texo_seo_column( $content, $column, $term_id ) {

		$term_meta = get_option( "jocaseo_tax_$term_id" );
		switch ( $column ) {
			case 'joca_seo':

				if( !empty($term_meta['meta_desc']) ) {
					echo "<img src='".JOCASEO_URL."images/joca-yes-meta.png' alt='Right' />";
				} else {
					echo "<img src='".JOCASEO_URL."images/joca-no-meta.png' alt='Wrong' />";
				}
			break;
		}
	}

	/**
	 * Adding admin menu page
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_add_settings_menu_page() {
		add_options_page( __('Joca SEO'), __('Joca SEO'), 'manage_options', 'jocaseo-settings', array($this, 'jocaseo_settings_page') );
	}

	/**
	 * Add setings page
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function jocaseo_settings_page() {
		include_once( JOCASEO_DIR . '/includes/admin/forms/jns-jocaseo-settings-form.php' );
	}

	/**
	 * Adding Hooks
	 *
	 * @package Joca SEO
	 * @since 1.0.0
	 */
	public function add_hooks() {

		// Add metabox for post types
		add_action( 'add_meta_boxes', array($this, 'jocaseo_add_posts_type_metabox'), 15);

		// Save post metabox
		add_action( 'save_post', array($this, 'jocaseo_save_posts_type_metabox') );

		// Add texonomy metabox
		add_action( 'category_edit_form_fields', array($this, 'jocaseo_category_edit_meta_field'), 5, 1 );
		add_action( 'post_tag_edit_form_fields', array($this, 'jocaseo_category_edit_meta_field'), 5, 1 );
		add_action( 'product_cat_edit_form_fields', array($this, 'jocaseo_category_edit_meta_field'), 5, 1 );
		add_action( 'product_tag_edit_form_fields', array($this, 'jocaseo_category_edit_meta_field'), 5, 1 );

		// Save texonomy data
		add_action( 'edited_category', array($this, 'jocaseo_category_save_meta_field'), 10, 1 );
		add_action( 'edited_post_tag', array($this, 'jocaseo_category_save_meta_field'), 10, 1 );
		add_action( 'edited_product_cat', array($this, 'jocaseo_category_save_meta_field'), 10, 1 );
		add_action( 'edited_product_tag', array($this, 'jocaseo_category_save_meta_field'), 10, 1 );

		// Manage columns on Posts, Pages and Product page
		add_filter( 'manage_posts_columns', array($this, 'jocaseo_manage_seo_column') );
		add_filter( 'manage_page_posts_columns', array($this, 'jocaseo_manage_seo_column') );
		add_filter( 'manage_edit-product_columns', array($this, 'jocaseo_manage_seo_column') );

		// Manage value of columns
		add_action( 'manage_posts_custom_column', array($this, 'jocaseo_manage_seo_column_value'), 10, 2 );
		add_action( 'manage_page_posts_custom_column', array($this, 'jocaseo_manage_seo_column_value'), 10, 2 );
		//add_action( 'manage_product_posts_custom_column', array($this, 'jocaseo_manage_seo_column_value'), 10, 2 );

		// Add menu page for joca form settings page
		add_action( 'admin_menu', array($this, 'jocaseo_add_settings_menu_page') );

		// Add texonomy page meta
		add_filter( 'manage_edit-category_columns', array($this, 'jocaseo_manage_seo_column') );
		add_filter( 'manage_edit-post_tag_columns', array($this, 'jocaseo_manage_seo_column') );
		add_filter( 'manage_edit-product_cat_columns', array($this, 'jocaseo_manage_seo_column') );
		add_filter( 'manage_edit-product_tag_columns', array($this, 'jocaseo_manage_seo_column') );

		add_filter( 'manage_category_custom_column', array($this, 'jocaseo_manage_texo_seo_column'), 10, 3 );
		add_filter( 'manage_post_tag_custom_column', array($this, 'jocaseo_manage_texo_seo_column'), 10, 3 );
		add_filter( 'manage_product_cat_custom_column', array($this, 'jocaseo_manage_texo_seo_column'), 10, 3 );
		add_filter( 'manage_product_tag_custom_column', array($this, 'jocaseo_manage_texo_seo_column'), 10, 3 );

	}
} // End of Admin Class


add_action( 'wp_ajax_joca_import', 'joca_import' );

function joca_import()
{
	$prefix = JOCASEO_META_PREFIX;
		if ($_POST['type'] == 'post'){
			$args = array(
						'posts_per_page'	=> intval($_POST['posts_per_page']),//$posts_per_page,
						'offset'            => intval($_POST['offset']),//$offset,
						'post_type'			=> 'any',
						'post_status'		=> 'publish',
						'fields'			=> 'ids'
					);

			// Get all the post ids
			$post_ids = get_posts( $args );

			if( !empty($post_ids) ) {
				foreach($post_ids as $post_id) {

					// Get meta details
					$title 	= get_post_meta( $post_id, '_yoast_wpseo_title', true );
					$desc 	= get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );

					// Check details is available
					if( !empty($title) || !empty($desc) ) {

						// update details
						update_post_meta( $post_id, $prefix.'meta_title', $title );
						update_post_meta( $post_id, $prefix.'meta_desc', $desc );
					}
				}
			} // End of meta import
		}
		if ($_POST['type'] == 'taxo'){
			$wpseo_titles = get_option('wpseo_titles');
			if( isset($wpseo_titles['title-home-wpseo']) ) {
				$jocaseo_options = get_option('jocaseo_options');
				if( empty($jocaseo_options) ) {
					$jocaseo_options['home_title'] 	= !empty($wpseo_titles['title-home-wpseo']) ? $wpseo_titles['title-home-wpseo'] : get_bloginfo( 'name' );
					$jocaseo_options['home_desc']	= !empty($wpseo_titles['metadesc-home-wpseo']) ? $wpseo_titles['metadesc-home-wpseo'] :get_bloginfo('description');
					update_option( 'jocaseo_options', $jocaseo_options );
				}
			}
			// Import taxonomy meta
			$yoast_taxo_meta = get_option('wpseo_taxonomy_meta');

			// Check for post categories
			if( !empty($yoast_taxo_meta['category']) ) {
				foreach ($yoast_taxo_meta['category'] as $key => $value) {

					$term_meta = array();
					$term_meta['meta_title'] = isset($value['wpseo_title']) ? $value['wpseo_title'] : '';
					$term_meta['meta_desc']	 = isset($value['wpseo_desc']) ? $value['wpseo_desc'] : '';

					update_option( "jocaseo_tax_$key", $term_meta );
				}
			}

			// Check for product categories
			if( !empty($yoast_taxo_meta['product_cat']) ) {
				foreach ($yoast_taxo_meta['product_cat'] as $key => $value) {

					$term_meta = array();
					$term_meta['meta_title'] = isset($value['wpseo_title']) ? $value['wpseo_title'] : '';
					$term_meta['meta_desc']	 = isset($value['wpseo_desc']) ? $value['wpseo_desc'] : '';

					update_option( "jocaseo_tax_$key", $term_meta );
				}
			}
		}
		echo 'ok';
		wp_die();
}

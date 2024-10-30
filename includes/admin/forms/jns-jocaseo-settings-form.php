<?php
/**
 * Joca SEO Settings fields
 *
 * @package Joca SEO
 * @since 1.0.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

global $jocaseo_options;
?>

<div class="wrap">
	<br />
	<h2>Joca SEO by <a href="http://www.jocawp.com" target="_blank">www.jocawp.com</a></h2>

	<?php

	// save options on form submition
	if( isset($_POST['jocaseo_save_settings']) &&
		$_POST['jocaseo_save_settings'] == 'Save Settings' ) {

			$jocaseo_options = isset($_POST['jocaseo_options']) ? $_POST['jocaseo_options'] : array();

			// Update options
			update_option( 'jocaseo_options', $jocaseo_options );
			_e( 'Settings saved.', 'jocaseo' ) . '<br />';
	}

	// Get options
	$jocaseo_options = get_option('jocaseo_options');

	// Get title and description
	$title 	= isset($jocaseo_options['home_title']) ? $jocaseo_options['home_title'] : get_bloginfo( 'name' );
	$desc 	= isset($jocaseo_options['home_desc']) ? $jocaseo_options['home_desc'] : get_bloginfo('description');
	?>

	<!-- Beginning of the settings meta box -->
	<div id="jocaseo-settings" class="post-box-container">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div id="options" class="postbox">
					<div class="handlediv" title="<?php _e( 'Click to toggle', 'jocaseo' ); ?>"><br /></div>

					<!-- settings box title -->
					<h3 class="hndle">
						<span style='vertical-align: top;'><?php _e( 'Settings', 'jocaseo' ); ?></span>
					</h3>

					<div class="inside">
						<form action=" " method="post">
							<table class="form-table">
								<tbody>
									<tr>
										<th>
											<label for="jocaseo_meta_preview"><?php _e('Edit Home Page', 'jocaseo'); ?></label>
										</th>
										<td class="jocaseo-form-fields">
											<h2 onclick="joca_showInput(this, 'jocaseo_meta_title');" class="jocaseo_title"><?php esc_attr_e($title?$title:'Enter title'); ?></h2>
											<input onkeydown="joca_checkEnter(event, this);" maxlength="60" onblur="joca_disableInput(this, 'jocaseo_title');" placeholder="Enter title" class="jocaseo_meta_title large-text" name="jocaseo_options[home_title]" style="display: none;" value="<?php esc_attr_e($title); ?>" type="text" />

											<p onclick="joca_showInput(this, 'jocaseo_meta_desc');" class="jocaseo_desc" ><?php esc_attr_e($desc?$desc:'Enter description'); ?></p>
											<textarea onkeydown="joca_checkLen(event, this);" onpaste="joca_checkLen(event, this);" onblur="joca_disableInput(this, 'jocaseo_desc');" placeholder="Enter description" class="jocaseo_meta_desc large-text" name="jocaseo_options[home_desc]" maxlength=160><?php esc_attr_e($desc); ?></textarea>
										</td>
									</tr>

									<tr>
										<td colspan="2">
											<input type="submit" class="button-primary" name="jocaseo_save_settings" value="Save Settings" />
										</td>
									</tr>
								</tbody>
							</table>
						</form>
					</div><!-- .inside -->
				</div><!-- #options -->
			</div><!-- .meta-box-sortables ui-sortable -->
		</div><!-- .metabox-holder -->
	</div><!-- #jocaseo-settings -->

</div> <!-- .wrap -->

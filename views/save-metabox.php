<?php
/**
 * Save notification metabox
 *
 * @package notification
 */

?>

<div class="misc-pub-section">
	<label class="row-label" for="onoffswitch"><strong><?php esc_html_e( 'Enable' ); ?></strong></label>
	<div class="onoffswitch">
	    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" value="1" id="onoffswitch" <?php checked( $this->get_var( 'enabled' ), true ); ?>>
	    <label class="onoffswitch-label" for="onoffswitch">
	        <span class="onoffswitch-inner"></span>
	        <span class="onoffswitch-switch"></span>
	    </label>
	</div>
</div>

<div id="major-publishing-actions">
	<div id="publishing-action">
		<input type="submit" value="<?php esc_attr_e( 'Save' ); ?>" class="button button-primary button-large" id="publish" name="publish">
	</div>
	<div class="clear"></div>
</div>

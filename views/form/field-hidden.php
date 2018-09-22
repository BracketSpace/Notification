<?php
/**
 * Hidden field template
 *
 * @package notification
 */

echo $this->get_var( 'current_field' )->field();  // WPCS: XSS ok.

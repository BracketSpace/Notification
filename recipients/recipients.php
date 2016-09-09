<?php
/**
 * Register default recipients
 */

use Notification\Recipients\Core;

/**
 * Custom email recipient
 */
new Core\Email();

if ( apply_filters( 'notification/recipients/core/administrator', true ) ) {
	new Core\Administrator();
}

if ( apply_filters( 'notification/recipients/core/user', true ) ) {
	new Core\User();
}

if ( apply_filters( 'notification/recipients/core/merge_tag', true ) ) {
	new Core\MergeTag();
}

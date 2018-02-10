<?php
/**
 * Email merge tag class
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag;

use underDEV\Notification\Abstracts\MergeTag;

/**
 * Email merge tag class
 */
class EmailTag extends MergeTag {

    /**
     * Check the merge tag value type
     *
     * @param  mixed $value value.
     * @return boolean
     */
    public function validate( $value ) {
    	return filter_var( $value, FILTER_VALIDATE_EMAIL ) !== false;
    }

    /**
     * Sanitizes the merge tag value
     *
     * @param  mixed $value value.
     * @return mixed
     */
    public function sanitize( $value ) {
    	return sanitize_email( $value );
    }

}

<?php
/**
 * Float merge tag class
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag;

use underDEV\Notification\Abstracts\MergeTag;

/**
 * Float merge tag class
 */
class FloatTag extends MergeTag {

    /**
     * Check the merge tag value type
     *
     * @param  mixed $value value.
     * @return boolean
     */
    public function validate( $value ) {
    	return filter_var( $value, FILTER_VALIDATE_FLOAT ) !== false;
    }

    /**
     * Sanitizes the merge tag value
     *
     * @param  mixed $value value.
     * @return mixed
     */
    public function sanitize( $value ) {
    	return floatval( $value );
    }

}

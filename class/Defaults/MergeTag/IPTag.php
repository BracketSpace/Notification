<?php
/**
 * IP merge tag class
 *
 * @package notification
 */

namespace underDEV\Notification\Defaults\MergeTag;

use underDEV\Notification\Abstracts\MergeTag;

class IPTag extends MergeTag {

    public function __construct( $params = array() ) {

    	parent::__construct( $params );

    }

    /**
     * Check the merge tag value type
     *
     * @param  mixed $value value
     * @return boolean
     */
    public function validate( $value ) {
    	return filter_var( $value, FILTER_VALIDATE_IP ) !== false;
    }

    /**
     * Sanitizes the merge tag value
     *
     * @param  mixed $value value
     * @return mixed
     */
    public function sanitize( $value ) {
    	return $value;
    }

}

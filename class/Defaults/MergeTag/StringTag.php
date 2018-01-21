<?php

namespace underDEV\Notification\Defaults\MergeTag;
use underDEV\Notification\Abstracts\MergeTag;

class StringTag extends MergeTag {

    public function __construct( $params = array() ) {

    	parent::__construct( $params );

    }

    /**
     * Check the merge tag value type
     * @param  mixed   $value value
     * @return boolean
     */
    public function validate( $value ) {
    	return ! is_array( $value ) && ! is_object( $value );
    }

    /**
     * Sanitizes the merge tag value
     * @param  mixed $value value
     * @return mixed
     */
    public function sanitize( $value ) {
    	return (string) sanitize_text_field( $value );
    }

}

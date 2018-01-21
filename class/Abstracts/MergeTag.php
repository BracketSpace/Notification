<?php

namespace underDEV\Notification\Abstracts;
use underDEV\Notification\Interfaces;

abstract class MergeTag extends Common implements Interfaces\Taggable {

    /**
     * MergeTag resolved value
     * @var mixed
     */
    protected $value;

    /**
     * Short description
     * No html tags allowed. Keep it tweet-short.
     * @var string
     */
    protected $description = '';

    /**
     * Function which resolve the merge tag value
     * @var callable
     */
    protected $resolver;

    /**
     * Resolving status
     * @var boolean
     */
    protected $resolved = false;

    public function __construct( $params = array() ) {

    	if ( ! isset( $params['slug'], $params['name'], $params['resolver'] ) ) {
    		trigger_error( 'Merge tag requires slug, name and resolver', E_USER_ERROR );
    	}

    	if ( ! is_callable( $params['resolver'] ) ) {
    		trigger_error( 'Merge tag resolver has to be callable', E_USER_ERROR );
    	}

		$this->slug     = $params['slug'];
		$this->name     = $params['name'];
		$this->resolver = $params['resolver'];

		if ( isset( $params['description'] ) ) {
			$this->description = sanitize_text_field( $params['description'] );
		}

    }

    /**
     * Checks if the value is the correct type
     * @param  mixed   $value tag value
     * @return boolean
     */
    abstract public function validate( $value );

    /**
     * Sanitizes the merge tag value
     * @param  mixed $value tag value
     * @return mixed        sanitized value
     */
    abstract public function sanitize( $value );

    /**
     * Checks the merge tag reqirements
     * ie. if there's a property set
     * @return boolean default always true
     */
    public function check_requirements() {
    	return true;
    }

    /**
     * Gets description
     * @return string description
     */
    public function get_description() {
    	return $this->description;
    }

    /**
	 * Resolves the merge tag value
	 * It also check if the value is correct type
	 * and sanitizes it
	 * @param  mixed $value value
	 * @return void
	 */
    public function resolve() {
    	$value = call_user_func( $this->resolver );

    	if ( ! $this->validate( $value ) ) {
    		trigger_error( 'Resolved value is a wrong type', E_USER_ERROR );
    	}

    	$this->resolved = true;

    	$this->value = $this->sanitize( $value );
    }

    /**
	 * Checks if merge tag is already resolved
	 * @return boolean
	 */
    public function is_resolved() {
    	return $this->resolved;
    }

    /**
	 * Gets merge tag resolved value
	 * @return mixed
	 */
    public function get_value() {
    	return apply_filters( 'notification/merge_tag/' . $this->get_slug() . '/value', $this->value, $this );
    }

}

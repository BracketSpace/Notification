<?php

namespace underDEV\Notification\Interfaces;
use underDEV\Notification\Interfaces\Nameable;

interface Taggable extends Nameable {

	/**
	 * Resolves the merge tag value
	 * @return void
	 */
    public function resolve();

	/**
	 * Gets merge tag resolved value
	 * @return mixed
	 */
    public function get_value();

	/**
	 * Checks if merge tag is already resolved
	 * @return boolean
	 */
    public function is_resolved();

}

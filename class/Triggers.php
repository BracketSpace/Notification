<?php
/**
 * Triggers
 */

namespace underDEV\Notification;

class Triggers {

	/**
	 * Gets all registered triggers
	 * @return array
	 */
	public function get() {
		return apply_filters( 'notification/triggers', array() );
	}

	/**
	 * Gets single trigger by its slug
	 * @param  string $slug trigger slug
	 * @return mixed        trigger object or false
	 */
	public function get_single( $slug ) {
		$trigers = $this->get();
		return isset( $trigers[ $slug ] ) ? $trigers[ $slug ] : false;
	}

	/**
	 * Get formatted triggers array
	 * where the top key is group
	 * @return array triggers
	 */
	public function get_grouped_array() {

		$return = array();

		foreach ( $this->get() as $trigger ) {

			if ( ! isset( $return[ $trigger->get_group() ] ) ) {
				$return[ $trigger->get_group() ] = array();
			}

			$return[ $trigger->get_group() ][ $trigger->get_slug() ] = array(
				'name'        => $trigger->get_name(),
				'description' => $trigger->get_description()
			);

		}

		return $return;

	}

}

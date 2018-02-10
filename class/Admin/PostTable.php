<?php
/**
 * Handles Notification post table
 */

namespace underDEV\Notification\Admin;

class PostTable {

	/**
	 * Adds custom table columns
     *
	 * @param  array $columns current columns
	 * @return array          filtered columns
	 */
	public function table_columns( $columns ) {

		$date_column = $columns['date'];
		unset( $columns['date'] );

		// Custom columns
		$columns['trigger'] = __( 'Trigger', 'notification' );
		$columns['date']    = $date_column;

		return $columns;

	}

	/**
	 * Content for custom columns
     *
	 * @param  string  $column  column slug
	 * @param  integer $post_id post ID
	 * @return void
	 */
	public function table_column_content( $column, $post_id ) {

		switch ( $column ) {
			case 'trigger':
				$trigger_slug = get_post_meta( $post_id, '_trigger', true );

				if ( empty( $trigger_slug ) ) {
					_e( 'No trigger selected', 'notification' );
				} else {
					try {
						echo $trigger_slug;
					} catch ( \Exception $e ) {
						echo $e->getMessage();
					}
				}
				break;
		}

	}

	/**
	 * Remove quick edit from post inline actions
     *
	 * @param  array  $row_actions array with action links
	 * @param  object $post        WP_Post object
	 * @return array               filtered actions
	 */
	public function remove_quick_edit( $row_actions, $post ) {

		if ( $post->post_type == 'notification' ) {
			if ( isset( $row_actions['inline hide-if-no-js'] ) ) {
				unset( $row_actions['inline hide-if-no-js'] );
			}
			if ( isset( $row_actions['inline'] ) ) {
				unset( $row_actions['inline'] );
			}
		}

		return $row_actions;

	}

}

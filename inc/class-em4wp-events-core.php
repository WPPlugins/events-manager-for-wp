<?php

/**
 * Event Calendar Core.
 */
class EM4WP_Events_Core {

	public $slug = 'events-manager-for-wp';
	public $event_post_type = 'event';

	/**
	 * Internal system for accessing plugin settings.
	 */
	public function get_option( $option ) {
		$options = get_option( $this->slug );

		// Add defaults if they don't already exist
		if ( '' == $options ) {
			$defaults = array(
				'permalink-slug'		=> 'event',
				'permalink-archive'		=> 'archive',
				'permalink-taxonomy'	=> 'event-type',
				'permalink-landing'		=> 'events',
				'hide-post-info'		=> 0,
			);
			add_option( $this->slug, $defaults );
		}

		if ( isset( $options[$option] ) ) {
			return $options[$option];
		} else {
			return false;
		}

	}

}

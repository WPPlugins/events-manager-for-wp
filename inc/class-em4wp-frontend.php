<?php

/**
 * Frontend view of event.
 */
class EM4WP_Frontend extends EM4WP_Events_Core {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'init',					array( $this, 'init' ) );
		add_action( 'wp_enqueue_scripts',	array( $this, 'css' ) );
	}

	/**
	 * init.
	 */
	public function init() {
		if ( function_exists( 'genesis' ) ) {
			add_action( 'genesis_entry_content', array( $this, 'genesis_content' ), 29 );

			add_filter( 'genesis_attr_entry-author', array( $this, 'attr_entry_author' ), 99, 2 );
			add_filter( 'genesis_attr_entry-time', array( $this, 'remove_attr_itemprop' ), 99, 2 );
			add_filter( 'genesis_attr_entry-author-name', array( $this, 'remove_attr_itemprop' ), 99, 2 );
			add_filter( 'genesis_attr_entry-author-link', array( $this, 'remove_attr_itemprop' ), 99, 2 );
			add_filter( 'genesis_post_info', array( $this, 'post_info' ), 99, 1 );
			
		} else {
			add_filter( 'the_content',    array( $this, 'the_content' ), 29 );
			add_filter( 'the_content',    array( $this, 'the_content_wrapper' ), 50 );
			add_filter( 'the_content',    array( $this, 'the_content_description_wrapper' ), 10 );
		}
	}

	/**
	 * Adding ending of content area for Genesis.
	 * We use a hook here for Genesis instead of the normal the_content() filter.
	 */
	public function genesis_wrapper_end() {

		// Bail out now if not on event post-type
		if ( 'event' != get_post_type() ) {
			return;
		}

		echo '</div>';
	}

	/**
	 * Adding content differently for Genesis.
	 * We use a hook here for Genesis instead of the normal the_content() filter.
	 */
	public function genesis_content() {

		// Bail out now if not on event post-type
		if ( 'event' != get_post_type() ) {
			return;
		}

		$content = $this->the_content( '' );
		echo $content;
	}

	/**
	 * the_content() filter.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function the_content( $content ) {

		// Bail out now if not on event post-type
		if ( 'event' != get_post_type() ) {
			return $content;
		}

		$start = get_post_meta( get_the_ID(), '_event_start', true );
		$end = get_post_meta( get_the_ID(), '_event_end', true );
		$allday = get_post_meta( get_the_ID(), '_event_allday', true );

		// Show the start date/time
		$content .= '
		<div class="em4wp-one-half">
			<h3>' . __( 'Date', 'events-manager-for-wp' ) . '</h3>
			<ul><li>' . __( 'Start: ', 'events-manager-for-wp' ) . '
			<time itemprop="startDate" content="' . date( 'c', $start ) . '">' . date( get_option( 'date_format' ), $start );

		if ( 1 != $allday ) {
			$content .= ', ' . __( 'at ', 'events-manager-for-wp' ) . date( 'H:i', $start );
		}

		$content .= '</time></li>';

		// Show the end date/time
		if ( '' != $end ) {
			$content .= '
			<li>' . __( 'End: ', 'events-manager-for-wp' ) . '
			<time itemprop="endDate" content="' . date( 'c', $end ) . '">' . date( get_option( 'date_format' ), $end );

			if ( 1 != $allday ) {
				$content .= ', ' . __( 'at ', 'events-manager-for-wp' ) . date( 'H:i', $end );
			}

			$content .= '</time></li></ul>';
		}

		$content .= '
		</div>';

		return $content;
	}

	/**
	 * the_content() wrapper filter.
	 * Wraps the content in a schema.org markup div.
	 * This will break some sites, but users may unhook this filter if required.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function the_content_description_wrapper( $content ) {

		// Bail out now if not on event post-type
		if ( 'event' != get_post_type() ) {
			return $content;
		}

		$content = '<div itemprop="description">' . $content . '</div>';

		return $content;
	}

	/**
	 * the_content() wrapper filter.
	 * Wraps the content in a schema.org markup div.
	 * This will break some sites, but users may unhook this filter if required.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function the_content_wrapper( $content ) {

		// Bail out now if not on event post-type
		if ( 'event' != get_post_type() ) {
			return $content;
		}

		$content = '<div itemscope itemtype="http://schema.org/Event">' . $content . '</div>';

		return $content;
	}

	/**
	 * Adding CSS for event posts.
	 */
	public function css() {

		// Bail out if not on event post-type
		if ( 'event' != get_post_type() ) {
			return;
		}

		$css_url = plugins_url( 'css/events-single.css', dirname(__FILE__) );
		wp_enqueue_style( 'em4wp-css', $css_url );

	}

	/**
	 * Remove all schema.org attributes from the entry_author
	 */
	public function attr_entry_author( $attributes, $context ) {

		// Bail out now if not on event post-type
		if ( 'event' != get_post_type() ) {
			return $attributes;
		}

		//unset schema.org/Person here ( since it is not a part of http://schema.org/Event )
		unset( $attributes['itemprop'] );
		unset( $attributes['itemscope'] );
		unset( $attributes['itemtype'] );

		return $attributes;
	}

	/**
	 * Remove the itemprop schema.org attribute from an element
	 */
	public function remove_attr_itemprop( $attributes, $context ) {

		// Bail out now if not on event post-type
		if ( 'event' != get_post_type() ) {
			return $attributes;
		}

		//unset itemprop ( =url [from: schema.org/Person] , because this is not a part of http://schema.org/Event )
		unset( $attributes['itemprop'] );

		return $attributes;
	}

	/**
	 * Empty the post info when our settings says so
	 */
	public function post_info( $post_info ) {

		// Bail out now if not on event post-type
		if ( 'event' != get_post_type() ) {
			return $post_info;
		}		

		if ( $this->get_option( 'hide-post-info' ) ) {
			$post_info = '';
		}

		return $post_info;

	}

}

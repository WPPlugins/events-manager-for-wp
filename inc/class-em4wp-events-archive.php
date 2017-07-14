<?php

/**
 * Event Archive.
 */
class EM4WP_Events_Archive extends EM4WP_Events_Core {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'rewrite_endpoint' ) );
		add_action( 'template_redirect', array( $this, 'redirect_endpoint' ) );

		add_filter(
			'query_vars',
			function( $vars ) {
				$vars[] = $this->get_option( 'permalink-archive' );
				return $vars;
			}
		);

		add_action( 'pre_get_posts', array( $this, 'modify_archive_query' ), 11 );

	}

	/**
	 * If end point is present, then perform redirect.
	 */
	public function redirect_endpoint() {

		$url_bits = explode( '/', get_option( 'siteurl' ) );

		// Bail out if no end point present
		if ( ! isset( $url_bits[3] ) ) {
			return;
		}

		$site_path = $url_bits[3];

		$path = '/';
		if ( ! empty( $site_path ) ) {
			$path .= $site_path . '/';
		}
		$path .= $this->get_option( 'permalink-slug' ) . '/';

		// On primary archive URL
		if ( $path == $_SERVER['REQUEST_URI'] ) {
			$url = home_url() . '/' . $this->get_option( 'permalink-landing' ) . '/';
			wp_redirect( esc_url( $url ), 302 );
			exit;
		}

	}

	/**
	 * Rewriting the archive URLs.
	 */
	public function rewrite_endpoint() {

		add_rewrite_rule(
			$this->get_option( 'permalink-landing' ) . '/' . $this->get_option( 'permalink-archive' ) . '/?$',
			'index.php?post_type=event&' . $this->get_option( 'permalink-archive' ) . '=1',
			'top'
		);

		add_rewrite_rule(
			$this->get_option( 'permalink-landing' ) . '/' . $this->get_option( 'permalink-archive' ) . '/page/([0-9]+)/?',
			'index.php?post_type=event&' . $this->get_option( 'permalink-archive' ) . '=$matches[1]',
			'top'
		);

		add_rewrite_rule(
			$this->get_option( 'permalink-landing' ) . '/?$',
			'index.php?post_type=event',
			'top'
		);
	}

	/**
	 * Modify the archive query.
	 *
	 * @param  object  $query  The main page query
	 * @return object  The modified main page query
	 */
	public function modify_archive_query( $query ) {

		if ( '' != get_query_var( $this->get_option( 'permalink-archive' ) ) && $query->is_main_query() && ! is_admin() && ( is_post_type_archive( 'event' ) || is_tax( 'event-category' ) ) ) {

			$meta_query = array(
				array(
					'key' => '_event_end',
					'value' => (int) current_time( 'timestamp' ),
					'compare' => '<'
				)
			);

			if ( '1' != get_query_var( $this->get_option( 'permalink-archive' ) ) ) {
				$paged = get_query_var( $this->get_option( 'permalink-archive' ) );
			} else {
				$paged = 1;
			}
			$query->set( 'offset', ( $paged - 1 ) * get_option( 'posts_per_page' ) );
			$query->set( 'paged', $paged );

			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
			$query->set( 'meta_query', $meta_query );
			$query->set( 'meta_key', '_event_start' );
		}

		return $query;
	}

}

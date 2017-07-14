<?php

/**
 * Read more meta.
 */
class EM4WP_Read_More {

	/*
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ), 12 );
		add_action( 'save_post',      array( $this, 'meta_boxes_save' ), 10, 2 );
		add_filter( 'the_content',    array( $this, 'the_content' ), 27 );
	}

	/**
	 * Add admin metabox.
	 */
	public function add_metabox() {
		add_meta_box(
			'read_more', // ID
			__( 'External Event Information', 'events-manager-for-wp' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			'event', 
			'side',
			'high'
		);
	}

	/**
	 * Output the meta box.
	 */
	public function meta_box() {

		?>

		<p>
			<label for="_read_more_text"><strong><?php _e( 'Link Text', 'events-manager-for-wp' ); ?></strong></label>
			<br />
			<input type="text" name="_read_more_text" id="_read_more_text" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_read_more_text', true ) ); ?>" />
		</p>

		<p>
			<label for="_read_more_url"><strong><?php _e( 'URL', 'events-manager-for-wp' ); ?></strong></label>
			<br />
			<input type="text" name="_read_more_url" id="_read_more_url" value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_read_more_url', true ) ); ?>" />
		</p>

		<input type="hidden" id="read-more-nonce" name="read-more-nonce" value="<?php echo esc_attr( wp_create_nonce( __FILE__ ) ); ?>"><?php
	}

	/**
	 * Save opening times meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_boxes_save( $post_id, $post ) {

		// Do nonce security check
		if ( ! isset( $_POST['read-more-nonce'] ) || ! wp_verify_nonce( $_POST['read-more-nonce'], __FILE__ ) ) {
			return;
		}

		// Only save if correct post data sent
		if ( isset( $_POST['_read_more_url'] ) ) {
			$_read_more_url = esc_url( $_POST['_read_more_url'] );
			update_post_meta( $post_id, '_read_more_url', $_read_more_url );
		}

		// Only save if correct post data sent
		if ( isset( $_POST['_read_more_text'] ) ) {
			$_read_more_text = wp_kses_post( $_POST['_read_more_text'] );
			update_post_meta( $post_id, '_read_more_text', $_read_more_text );
		}

		return $post_id;
	}

	/**
	 * the_content() filter.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function the_content( $content ) {

		if ( 'event' != get_post_type() ) {
			return $content;
		}

		$text = get_post_meta( get_the_ID(), '_read_more_text', true );
		$url = get_post_meta( get_the_ID(), '_read_more_url', true );

		if ( '' != $url && '' != $text ) {
			$content .= '
			<div class="em4wp-one-half">
				<h3>' . __( 'More information', 'events-manager-for-wp' ) . '</h3>
				<a  itemprop="url" class="button" href="' . esc_url( $url ) . '">' . $text . '</a>
			</div>';
		} else {
			$internal_url = get_permalink();
			$content .= '
			<div class="em4wp-one-half">
				<meta itemprop="url" content="' . esc_url( $internal_url ) . '" />
			</div>';

		}

		return $content;
	}

}

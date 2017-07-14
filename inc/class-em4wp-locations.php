<?php

/**
 * Locations.
 */
class EM4WP_Locations extends EM4WP_Events_Core {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post',      array( $this, 'meta_boxes_save' ), 10, 2 );
		add_action( 'init',           array( $this, 'init' ) );
	}

	/**
	 * init.
	 */
	public function init() {
		if ( function_exists( 'genesis' ) ) {
			add_action( 'genesis_entry_content', array( $this, 'genesis_name_content' ), 20 );
			add_action( 'genesis_entry_content', array( $this, 'genesis_map_content' ), 30 );
		} else {
			add_filter( 'the_content',    array( $this, 'the_name_content' ), 20 );
			add_filter( 'the_content',    array( $this, 'the_map_content' ), 30 );
		}
	}

	/**
	 * Adding the name content differently for Genesis.
	 * We use a hook here for Genesis instead of the normal the_content() filter.
	 */
	public function genesis_name_content() {

		// Bail out now if not on event post-type
		if ( 'event' != get_post_type() ) {
			return;
		}

		$content = $this->the_name_content( '' );
		echo $content;
	}	

	/**
	 * Adding the map content differently for Genesis.
	 * We use a hook here for Genesis instead of the normal the_content() filter.
	 */
	public function genesis_map_content() {

		// Bail out now if not on event post-type
		if ( 'event' != get_post_type() ) {
			return;
		}

		$content = $this->the_map_content( '' );
		echo $content;
	}

	/**
	 * Add admin metabox.
	 */
	public function add_metabox() {
		add_meta_box(
			'location', // ID
			__( 'Location', 'events-manager-for-wp' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			$this->event_post_type, // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the example meta box.
	 */
	public function meta_box() {

		$location = get_post_meta( get_the_ID(), '_location', true );
		if ( isset( $location['display'] ) ) {
			$display = $location['display'];
		} else {
			$display = '';
		}
		if ( isset( $location['latitude'] ) ) {
			$latitude = $location['latitude'];
		} else {
			$latitude = '29.9753';
		}
		if ( isset( $location['longitude'] ) ) {
			$longitude = $location['longitude'];
		} else {
			$longitude = '31.1376';
		}
		if ( isset( $location['width'] ) ) {
			$width = $location['width'];
		} else {
			$width = '640';
		}
		if ( isset( $location['height'] ) ) {
			$height = $location['height'];
		} else {
			$height = '480';
		}
		if ( isset( $location['name'] ) ) {
			$name = $location['name'];
		} else {
			$name = '';
		}

		if ( '' != $latitude && '' != $longitude ) {
			$embed_url = 'https://maps.google.com/maps?q=' . $latitude . ',' . $longitude . '&z=14&output=embed&iwloc=0';
		} else {
			$embed_url = '';
		}

		?>
		<style>
		.em4wp-location {
			display: inline-block;
		}
		table.em4wp-location {
			width: 39%;
		}
		iframe.em4wp-location {
			width: 59%;
			height: 300px;
		}
		</style>

		<table class="em4wp-location">
			<tr>
				<td><label for="name"><strong><?php _e( 'Location name', 'events-manager-for-wp' ); ?></strong></label></td>
				<td>
					<input type="text" name="location[name]" id="name" value="<?php echo esc_attr( $name ); ?>" />
				</td>
			</tr>			
			<tr>
				<td><label for="display"><strong><?php _e( 'Display map?', 'events-manager-for-wp' ); ?></strong></label></td>
				<td>
					<input type="checkbox" name="location[display]" id="display" <?php checked( $display, 1 ); ?> value="1" />
				</td>
			</tr>
			<tr>
				<td><label for="latitude"><strong><?php _e( 'Latitude', 'events-manager-for-wp' ); ?></strong></label></td>
				<td>
					<input type="text" name="location[latitude]" id="latitude" value="<?php echo esc_attr( $latitude ); ?>" />
				</td>
			</tr>
			<tr>
				<td><label for="longitude"><strong><?php _e( 'Longitude', 'events-manager-for-wp' ); ?></strong></label></td>
				<td>
					<input type="text" name="location[longitude]" id="longitude" value="<?php echo esc_attr( $longitude ); ?>" />
				</td>
			</tr>
			<tr>
				<td><label for="width"><strong><?php _e( 'Width', 'events-manager-for-wp' ); ?></strong></label></td>
				<td>
					<input type="text" name="location[width]" id="width" value="<?php echo esc_attr( $width ); ?>" />
				</td>
			</tr>
			<tr>
				<td><label for="height"><strong><?php _e( 'Height', 'events-manager-for-wp' ); ?></strong></label></td>
				<td>
					<input type="text" name="location[height]" id="height" value="<?php echo esc_attr( $height ); ?>" />
				</td>
			</tr>
		</table>

		<iframe id="em4wp-map" class="em4wp-location" src="<?php echo esc_url( $embed_url ); ?>" frameborder="0" allowfullscreen></iframe>

		<script>

			var latitude = document.getElementById("latitude");
			latitude.addEventListener("change", set_map_location);

			var longitude = document.getElementById("longitude");
			longitude.addEventListener("change", set_map_location);

			function set_map_location() {
				var embed_url = 'https://maps.google.com/maps?q='+latitude.value+','+longitude.value+'&z=14&output=embed&iwloc=0';
				var map = document.getElementById("em4wp-map");
				map.src = embed_url;
			}
			set_map_location();

		</script>

		<input type="hidden" id="location-nonce" name="location-nonce" value="<?php echo esc_attr( wp_create_nonce( __FILE__ ) ); ?>"><?php
	}

	/**
	 * Save opening times meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_boxes_save( $post_id, $post ) {

		// Only save if correct post data sent
		if ( isset( $_POST['location'] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST['location-nonce'], __FILE__ ) ) {
				return;
			}

			// Sanitize and store the data
			foreach ( $_POST['location'] as $key => $value ) {
				if (
					in_array( $key, array( 'longitude', 'latitude', 'width', 'height', 'display' ) )
					&&
					is_numeric( $value )
				) {
					$location[$key] = $value;
				}

				if ( in_array( $key, array( 'name' ) ) ) {
					$location[$key] = sanitize_text_field( $value );
				}

			}

			update_post_meta( $post_id, '_location', $location );

		}

	}

	/**
	 * Add the name to the content.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 * @since  1.2.4
	 */
	public function the_name_content( $content ) {

		// Bail out now if we are not on an event
		if( $this->event_post_type != get_post_type() ) {
			return $content;
		}

		$location	= get_post_meta( get_the_ID(), '_location', true );

		$content .= '<div class="em4wp-full" itemprop="location" itemscope itemtype="http://schema.org/Place">';
		if ( isset( $location['name'] ) && ( ! empty( $location['name'] ) ) ) {
			$content .= '<p><strong>' . __( 'Location:', 'events-manager-for-wp' ) . '</strong> <span itemprop="name">' . $location['name'] . '</span></p>';
		} else {
			$content .= '<meta itemprop="name" content="' . esc_attr( 'No Location Available', 'events-manager-for-wp' ) . '" />';
		}
		$content .= '<meta itemprop="address" content="not set"/></div>';

		return $content;
	}	

	/**
	 * Add the map to the content.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function the_map_content( $content ) {

		// Bail out now if we are not on an event
		if( $this->event_post_type != get_post_type() ) {
			return $content;
		}

		$location	= get_post_meta( get_the_ID(), '_location', true );

		if ( isset( $location['display'] ) && 1 == $location['display'] && isset( $location['latitude'] ) && isset( $location['longitude'] ) ) {
			
			$latitude	= $location['latitude'];
			$longitude	= $location['longitude'];

			if ( '' != $latitude && '' != $longitude ) {

				$content .= '<div class="em4wp-full">';

				$embed_url = 'https://maps.google.com/maps?q=' . $latitude . ',' . $longitude . '&z=14&output=embed&iwloc=0';
				$content .= '
					<iframe src="' . esc_url( $embed_url ) . '" ';

				if ( isset( $location['width'] ) ) {
					$content .= ' width="' . esc_attr( $location['width'] ) . '"';
				}
				if ( isset( $location['height'] ) ) {
					$content .= ' height="' . esc_attr( $location['height'] ) . '"';
				}

				$content .= 'frameborder="0" allowfullscreen></iframe>';

				$content .= '</div>';

			}
		}

		return $content;
	}

}

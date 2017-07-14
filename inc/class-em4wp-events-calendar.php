<?php

/**
 * Event Calendar Base.
 */
class EM4WP_Events_Calendar extends EM4WP_Events_Core {

	/**
	 * class constructor.
	 */
	public function __construct() {

		// Fire on activation
		register_activation_hook( EM4WP_EVENTS_CALENDAR_PLUGIN_FILE, array( $this, 'activation' ) );

		// Load the plugin base
		add_action( 'plugins_loaded', array( $this, 'init' ) );	
	}

	/**
	 * Flush the WordPress permalink rewrite rules on activation.
	 */
	public function activation() {
		$this->post_type();
		flush_rewrite_rules();
	}

	/**
	 * Loads the plugin base into WordPress.
	 */
	public function init() {

		// Create Post Type
		add_action( 'init', array( $this, 'post_type' ) );

		// Post Type columns
		add_filter( 'manage_edit-events_columns', array( $this, 'edit_event_columns' ) ) ;
		add_action( 'manage_events_posts_custom_column', array( $this, 'manage_event_columns' ), 10, 2 );

		// Post Type sorting
		add_filter( 'manage_edit-events_sortable_columns', array( $this, 'event_sortable_columns' ) );
		add_action( 'load-edit.php', array( $this, 'edit_event_load' ) );

		// Post Type title placeholder
		add_action( 'gettext',  array( $this, 'title_placeholder' ) );

		// Create Taxonomy
		add_action( 'init', array( $this, 'taxonomies' ) );

		// Create Metabox
		$metabox = apply_filters( 'em4wp_events_manager_metabox_override', false );
		if ( false === $metabox ) {
			add_action( 'add_meta_boxes', array( $this, 'metabox_register' ) );
			add_action( 'save_post', array( $this, 'metabox_save' ),  1, 2  );
		}
	
		// Modify Event Listings query
		add_action( 'pre_get_posts', array( $this, 'event_query' ) );
	}

	/** 
	 * Register Post Type.
	 */
	public function post_type() {

		$labels = array(
			'name'               => __( 'Events', 'events-manager-for-wp' ),
			'singular_name'      => __( 'Event', 'events-manager-for-wp' ),
			'add_new'            => __( 'Add New', 'events-manager-for-wp' ),
			'add_new_item'       => __( 'Add New Event', 'events-manager-for-wp' ),
			'edit_item'          => __( 'Edit Event', 'events-manager-for-wp' ),
			'new_item'           => __( 'New Event', 'events-manager-for-wp' ),
			'view_item'          => __( 'View Event', 'events-manager-for-wp' ),
			'search_items'       => __( 'Search Events', 'events-manager-for-wp' ),
			'not_found'          => __( 'No Events found', 'events-manager-for-wp' ),
			'not_found_in_trash' => __( 'No Events found in trash', 'events-manager-for-wp' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Events Manager', 'events-manager-for-wp' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true, 
			'show_in_menu'       => true, 
			'query_var'          => true,
			'rewrite'            => array( 'slug' => $this->get_option( 'permalink-slug' ), 'with_front' => false ),
			'capability_type'    => 'post',
			'has_archive'        => true, 
			'hierarchical'       => false,
			'menu_position'      => null,
				'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'genesis-layouts', 'genesis-seo', 'genesis-simple-sidebars', 'genesis-cpt-archives-settings' ),
			'menu_icon'          => 'dashicons-calendar',
		); 

		register_post_type( 'event', $args );	
	}

	/**
	 * Edit Column Titles.
	 *
	 * @link http://devpress.com/blog/custom-columns-for-custom-post-types/
	 * @param array $columns
	 * @return array
	 */
	public function edit_event_columns( $columns ) {

		$columns = array(
			'cb'          => '<input type="checkbox" />',
			'title'       => 'Event',
			'event_start' => 'Starts',
			'event_end'   => 'Ends',
			'date'        => 'Published Date',
		);

		return $columns;
	}

	/**
	 * Edit Column Content
	 *
	 * @link http://devpress.com/blog/custom-columns-for-custom-post-types/
	 * @param string $column
	 * @param int $post_id
	 */
	public function manage_event_columns( $column, $post_id ) {
		global $post;

		switch( $column ) {

			/* If displaying the 'duration' column. */
			case 'event_start' :

				/* Get the post meta. */
				$allday = get_post_meta( $post_id, '_event_allday', true );
				$date_format = $allday ? 'M j, Y' : 'M j, Y g:i A';
				$start = esc_attr( date( $date_format, get_post_meta( $post_id, '_event_start', true ) ) );

				/* If no duration is found, output a default message. */
				if ( empty( $start ) )
					echo __( 'Unknown', 'events-manager-for-wp' );

				/* If there is a duration, append 'minutes' to the text string. */
				else
					echo $start;

				break;

			/* If displaying the 'genre' column. */
			case 'event_end' :

				/* Get the post meta. */
				$allday = get_post_meta( $post_id, '_event_allday', true );
				$date_format = $allday ? 'M j, Y' : 'M j, Y g:i A';
				$end = esc_attr( date( $date_format, get_post_meta( $post_id, '_event_end', true ) ) );

				/* If no duration is found, output a default message. */
				if ( empty( $end ) )
					echo __( 'Unknown' );

				/* If there is a duration, append 'minutes' to the text string. */
				else
					echo $end;

				break;

			/* Just break out of the switch statement for everything else. */
			default :
				break;
		}
	}	 

	/**
	 * Make Columns Sortable.
	 *
	 * @link http://devpress.com/blog/custom-columns-for-custom-post-types/
	 * @param array $columns
	 * @return array
	 */
	public function event_sortable_columns( $columns ) {

		$columns['event_start'] = 'event_start';
		$columns['event_end']   = 'event_end';

		return $columns;
	}

	/**
	 * Check for load request.
	 */
	public function edit_event_load() {
		add_filter( 'request', array( $this, 'sort_events' ) );
	}

	/**
	 * Sort events on load request
	 *
	 * @param array $vars
	 * @return array
	 */
	public function sort_events( $vars ) {

		/* Check if we're viewing the 'event' post type. */
		if ( isset( $vars['post_type'] ) && 'event' == $vars['post_type'] ) {

			/* Check if 'orderby' is set to 'start_date'. */
			if ( isset( $vars['orderby'] ) && 'event_start' == $vars['orderby'] ) {

				/* Merge the query vars with our custom variables. */
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => '_event_start',
						'orderby' => 'meta_value_num'
					)
				);
			}

			/* Check if 'orderby' is set to 'end_date'. */
			if ( isset( $vars['orderby'] ) && 'event_end' == $vars['orderby'] ) {

				/* Merge the query vars with our custom variables. */
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => '_event_end',
						'orderby' => 'meta_value_num'
					)
				);
			}

		}

		return $vars;
	}

	/**
	 * Change the default title placeholder text.
	 *
	 * @global array $post
	 * @param string $translation
	 * @return string Customized translation for title
	 */
	public function title_placeholder( $translation ) {
		global $post;

		if ( isset( $post ) && 'event' == $post->post_type && 'Enter title here' == $translation ) {
			$translation = __( 'Enter Event Name Here', 'events-manager-for-wp' );
		}
		return $translation;
	}

	/**
	 * Create Taxonomies.
	 */
	public function taxonomies() {

		$supports = get_theme_support( 'em4wp-events-calendar' );
		if ( !is_array( $supports ) || !in_array( 'event-category', $supports[0] ) ) {
			return;
		}

		$post_types = in_array( 'recurring-events', $supports[0] ) ? array( 'event', 'recurring-events' ) : array( 'event' );

		$labels = array(
			'name'              => __( 'Categories', 'events-manager-for-wp' ),
			'singular_name'     => __( 'Category', 'events-manager-for-wp' ),
			'search_items'      => __( 'Search Categories', 'events-manager-for-wp' ),
			'all_items'         => __( 'All Categories', 'events-manager-for-wp' ),
			'parent_item'       => __( 'Parent Category', 'events-manager-for-wp' ),
			'parent_item_colon' => __( 'Parent Category:', 'events-manager-for-wp' ),
			'edit_item'         => __( 'Edit Category', 'events-manager-for-wp' ),
			'update_item'       => __( 'Update Category', 'events-manager-for-wp' ),
			'add_new_item'      => __( 'Add New Category', 'events-manager-for-wp' ),
			'new_item_name'     => __( 'New Category Name', 'events-manager-for-wp' ),
			'menu_name'         => __( 'Category', 'events-manager-for-wp' ),
		); 	
	
		register_taxonomy( 'event-category', $post_types, array(
			'hierarchical' => true,
			'labels'       => $labels,
			'show_ui'      => true,
			'query_var'    => true,
			'rewrite'      => array( 'slug' => 'event-category' ),
		));
	}

	/**
	 * Initialize the metabox.
	 */
	public function metabox_register() {
		add_meta_box(
			'em4wp-events-calendar-date-time',
			__( 'Date and Time Details', 'events-manager-for-wp' ),
			array( $this, 'render_metabox' ),
			'event',
			'side',
			'high'
		);
	}

	/**
	 * Render the metabox.
	 */
	public function render_metabox() {

		$start  = get_post_meta( get_the_ID() , '_event_start', true );
		$end    = get_post_meta( get_the_ID() , '_event_end',   true );
		$allday = get_post_meta( get_the_ID(), '_event_allday', true );

		// Convert unix time stamp to human readable formats
		if ( ! empty( $start ) ) {
			$start_date = date( 'Y-m-d', $start );
			$start_time = date( 'H:i',  $start );
		}

		// Convert unix time stamp to human readable formats
		if ( ! empty( $end ) ) {
			$end_date   = date( 'Y-m-d', $end   );
			$end_time   = date( 'H:i',  $end   );
		}

		wp_nonce_field( 'em4wp_events_calendar_date_time', 'em4wp_events_calendar_date_time_nonce' );
		?>

		<div class="section" style="min-height:0;">
			<label for="em4wp-events-calendar-allday">All Day event?</label>
			<input name="em4wp-events-calendar-allday" type="checkbox" id="em4wp-events-calendar-allday" value="1" <?php checked( '1', $allday ); ?>>
		</div>

		<div class="section">
			<label for="em4wp-events-calendar-start">Start date and time:</label> 
			<input name="em4wp-events-calendar-start" type="date"  id="em4wp-events-calendar-start" class="em4wp-events-calendar-date" value="<?php echo !empty( $start ) ? $start_date : ''; ?>" />
			<input name="em4wp-events-calendar-start-time" type="time"  id="em4wp-events-calendar-start-time" class="em4wp-events-calendar-time" value="<?php echo !empty( $start ) ? $start_time : ''; ?>">
		</div>

		<div class="section">
			<label for="em4wp-events-calendar-end">End date and time:</label> 
			<input name="em4wp-events-calendar-end" type="date"  id="em4wp-events-calendar-end" class="em4wp-events-calendar-date" value="<?php echo !empty( $end ) ? $end_date : ''; ?>" />
			<input name="em4wp-events-calendar-end-time" type="time"  id="em4wp-events-calendar-end-time" class="em4wp-events-calendar-time" value="<?php echo !empty( $end ) ? $end_time : ''; ?>">
		</div><?php
	}

	/**
	 * Save metabox contents.
	 *
	 * @param int $post_id
	 * @param array $post
	 */
	public function metabox_save( $post_id, $post ) {

		// Security check
		if ( ! isset( $_POST['em4wp_events_calendar_date_time_nonce'] ) || ! wp_verify_nonce( $_POST['em4wp_events_calendar_date_time_nonce'], 'em4wp_events_calendar_date_time' ) ) {
			return;
		}

		// Bail out if running an autosave, ajax, cron, or revision.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		 // Bail out if the user doesn't have the correct permissions to update the slider.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Make sure the event start/end dates were not left blank before we run the save
		if (
			isset( $_POST['em4wp-events-calendar-start'] )
			&&
			isset( $_POST['em4wp-events-calendar-end'] )
			&&
			isset( $_POST['em4wp-events-calendar-start-time'] )
			&&
			isset( $_POST['em4wp-events-calendar-end-time'] )
		) {

			$start      = $_POST['em4wp-events-calendar-start'] . ' ' . $_POST['em4wp-events-calendar-start-time'];
			$end        = $_POST['em4wp-events-calendar-end'] . ' ' . $_POST['em4wp-events-calendar-end-time'];
			if ( ' ' == $end ) {
				$end = $start;
			}
			if ( ' ' == $start ) {
				$start = $end;
			}
			$allday     = ( isset( $_POST['em4wp-events-calendar-allday'] ) ? '1' : '0' );

				$start_unix = absint( strtotime( $start ) );
				update_post_meta( $post_id, '_event_start',  $start_unix );
			if ( ' ' != $start ) {
			} else {
//				delete_post_meta( $post_id, '_event_start' );
			}

			if ( ' ' != $end ) {
				$end_unix   = absint( strtotime( $end ) );
				update_post_meta( $post_id, '_event_end',    $end_unix   );
			} else {
//				delete_post_meta( $post_id, '_event_end' );
			}
//echo $start_unix.": ".$end_unix." - ";
//echo get_post_meta( $post_id, '_event_start', true );
//echo ' ... xxx';die;
			update_post_meta( $post_id, '_event_allday', $allday     );
		}
	}

	/**
	 * Modify WordPress query where needed for event listings.
	 *
	 * @param object $query
	 */
	public function event_query( $query ) {

		// If you don't want the plugin to mess with the query, use this filter to override it
		$override = apply_filters( 'em4wp_events_manager_query_override', false );
		if ( $override ) {
			return;
		}

		if ( $query->is_main_query() && !is_admin() && ( is_post_type_archive( 'event' ) || is_tax( 'event-category' ) ) ) {	
			$meta_query = array(
				array(
					'key' => '_event_end',
					'value' => (int) current_time( 'timestamp' ),
					'compare' => '>'
				)
			);
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', 'ASC' );
			$query->set( 'meta_query', $meta_query );
			$query->set( 'meta_key', '_event_start' );
		}
	}

}

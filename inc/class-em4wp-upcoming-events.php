<?php

/**
 * Upcoming Events Widget.
 */
class EM4WP_Upcoming_Events extends WP_Widget {

	/**
	 * Class constructor.
	 **/
	public function __construct() {
		$widget_ops = array( 'classname' => 'widget_upcoming_events', 'description' => '' );
		parent::__construct( 'upcoming-events-widget', __( 'Upcoming Events Widget' ), $widget_ops );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array  An array of standard parameters for widgets in this theme 
	 * @param array  An array of settings for this widget instance 
	 * @return void Echoes it's output
	 **/
	public function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		
		$count = esc_attr( $instance['count'] );
		$count = 0 < $count && $count < 10 ? $count : 2;
		$loop = new WP_Query( array( 
			'post_type'      => 'event',
			'posts_per_page' => $count,
			'order'          => 'ASC',
			'orderby'        => 'meta_value_num',
			'meta_key'       => '_event_start',
			'meta_query'     => array(
				array(
					'key'     => '_event_end',
					'value'   => time(),
					'compare' => '>',
				)
			)
		) );
		if ( $loop->have_posts() ):

			echo $before_widget;

			if ( $instance['title'] ) {
				echo $before_title . apply_filters( 'widget_title', $instance['title'] ) . $after_title;
			}

			echo '<ul>';

			while( $loop->have_posts() ): $loop->the_post();
				global $post;
				$output = '<span class="meta">' . date( get_option( 'date_format' ), get_post_meta( get_the_ID(), '_event_start', true ) ) . '</span> <a href="' . get_permalink() . '">' . get_the_title() . '</a>';

				$read_more = apply_filters( 'em4wp_events_manager_upcoming_widget_output', $output, $post );
				if ( $read_more ) {
					echo '<li>' . $read_more . '</li>'; 
				}

			endwhile;

			if ( $instance['more_text'] ) {
				echo '<li><a href="' . get_post_type_archive_link( 'event' ) . '">' . esc_attr( $instance['more_text'] ) . '</a></li>';
			}
			echo '</ul>';

			echo $after_widget;

		endif;
		wp_reset_postdata();
	}

	/**
	 * Deals with the settings when they are saved by the admin. Here is
	 * where any validation should be dealt with.
	 *
	 * @param array  An array of new settings as submitted by the admin
	 * @param array  An array of the previous settings 
	 * @return array The validated and (if necessary) amended settings
	 **/
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = wp_kses_post( $new_instance['title'] );
		$instance['count'] = (int) esc_attr( $new_instance['count'] );
		$instance['more_text'] = esc_attr( $new_instance['more_text'] );

		return $instance;
	}
	
	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array  An array of the current settings for this widget
	 **/
	public function form( $instance ) {

		$defaults = array( 
			'title' 	=> 'Upcoming Events', 
			'count' 	=> 2, 
			'more_text' => 'View All Event Information (leave blank for no link).' 
			);

		$instance = wp_parse_args( (array) $instance, $defaults ); 

		echo '<p><label for="' . $this->get_field_id( 'title' ) . '">Title: <input class="widefat" id="' . $this->get_field_id( 'title' ) .'" name="' . $this->get_field_name( 'title' ) . '" value="' . esc_attr( $instance['title'] ) . '" /></label></p>';
		echo '<p><label for="' . $this->get_field_id( 'count' ) . '">How Many: <input class="widefat" id="' . $this->get_field_id( 'count' ) .'" name="' . $this->get_field_name( 'count' ) . '" value="' . esc_attr( $instance['count'] ) . '" /></label></p>';
		echo '<p><label for="' . $this->get_field_id( 'more_text' ) . '">More Text: <input class="widefat" id="' . $this->get_field_id( 'more_text' ) .'" name="' . $this->get_field_name( 'more_text' ) . '" value="' . esc_attr( $instance['more_text'] ) . '" /></label></p>';

	}
}

add_action( 'widgets_init', 'em4wp_register_upcoming_events_widget' );
/**
 * Regster the Upcoming Events Widget
 */
function em4wp_register_upcoming_events_widget() {
	register_widget( 'EM4WP_Upcoming_Events' );
}


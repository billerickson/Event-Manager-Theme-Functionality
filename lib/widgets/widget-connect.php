<?php
/* Connect Widget */

/** Add our function to the widgets_init hook. **/
function sc_load_connect_widget() {
	register_widget( 'Connect_Widget' );
}
add_action( 'widgets_init', 'sc_load_connect_widget' );

/** Define the Widget as an extension of WP_Widget **/
class Connect_Widget extends WP_Widget {
	function Connect_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_connect', 'description' => __( 'Displays icons for Email, RSS, Twitter, Facebook and Flickr', 'social-coup-functionality' ) );

		/* Widget control settings. */
		$control_ops = array( 'id_base' => 'connect-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'connect-widget', __( 'Connect Widget', 'social-coup-functionality' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		
		echo '<p>';
		if( !empty( $instance['email'] ) ) echo '<a href="mailto:' .  $instance['email'] . '" class="btn-email">Email</a> ';
		if( !empty( $instance['rss'] ) ) echo '<a href="' . $instance['rss'] . '" class="btn-rss">RSS</a> ';
		if (!empty( $instance['twitter'] ) ) echo '<a href="' . $instance['twitter'] . '" class="btn-twitter">Twitter</a> ';
		if (!empty( $instance['facebook'] ) ) echo '<a href="' . $instance['facebook'] . '" class="btn-facebook">Facebook</a> ';
		if (!empty( $instance['flickr'] ) ) echo '<a href="' . $instance['flickr'] . '" class="btn-flickr">Flickr</a> ';
		echo '</p>';
		
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['email'] = is_email( $new_instance['email'] ) ? $new_instance['email'] : $old_instance['email'];
		$instance['twitter'] = esc_url( $new_instance['twitter'] );
		$instance['facebook'] = esc_url( $new_instance['facebook'] );
		$instance['flickr'] = esc_url( $new_instance['flickr'] );
		$instance['rss'] = esc_url( $new_instance['rss'] );
		return $instance;
}

	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __( 'Connect With Us', 'social-coup-functionality' ),  'email' => '', 'rss' => '', 'twitter' => '', 'facebook' => '', 'flickr' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		 
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , 'social-coup-functionality' );?></label>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'email' ); ?>"><?php _e( 'Email:' , 'social-coup-functionality' );?></label>
		<input id="<?php echo $this->get_field_id( 'email' ); ?>" name="<?php echo $this->get_field_name( 'email' ); ?>" value="<?php echo $instance['email']; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'rss' ); ?>"><?php _e( 'RSS URL:' , 'social-coup-functionality' );?></label>
		<input id="<?php echo $this->get_field_id( 'rss' ); ?>" name="<?php echo $this->get_field_name( 'rss' ); ?>" value="<?php echo $instance['rss']; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php _e( 'Twitter URL:' , 'social-coup-functionality' );?></label>
		<input id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" value="<?php echo $instance['twitter']; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php _e( 'Facebook URL:' , 'social-coup-functionality' );?></label>
		<input id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" value="<?php echo $instance['facebook']; ?>" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'flickr' ); ?>"><?php _e( 'Flickr URL:' , 'social-coup-functionality' );?></label>
		<input id="<?php echo $this->get_field_id( 'flickr' ); ?>" name="<?php echo $this->get_field_name( 'flickr' ); ?>" value="<?php echo $instance['flickr']; ?>" />
		</p>
		<?php
	}
}
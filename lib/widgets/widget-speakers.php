<?php
/* Speakers Widget */

/** Add image size **/
function sc_speakers_widget_image_size() {
	add_image_size( 'sc_speakers_widget', 73, 72, true );
}
add_action( 'init', 'sc_speakers_widget_image_size' );


/** Add our function to the widgets_init hook. **/
function sc_load_speakers_widget() {
	register_widget( 'Speakers_Widget' );
}
add_action( 'widgets_init', 'sc_load_speakers_widget' );

/** Define the Widget as an extension of WP_Widget **/
class Speakers_Widget extends WP_Widget {
	function Speakers_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'widget_speakers', 'description' => __( 'Lists the current speakers', 'social-coup-functionality' ) );

		/* Widget control settings. */
		$control_ops = array( 'id_base' => 'speakers-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'speakers-widget', __( 'Speakers Widget', 'social-coup-functionality' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		
		$args = array(
			'post_type' => 'sc-speakers',
			'posts_per_page' => '-1',
			'orderby' => 'menu_order',
			'order' => 'ASC'
		);
		
		$loop = new WP_Query( $args );
		if( $loop->have_posts() ): while( $loop->have_posts() ): $loop->the_post(); global $post;
			echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail( $post->ID, 'sc_speakers_widget' ) . '</a>';
		endwhile; endif; wp_reset_query();

		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
}

	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __( 'Speakers', 'social-coup-functionality' ) );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		 
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' , 'social-coup-functionality' );?></label>
		<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<?php
	}
}
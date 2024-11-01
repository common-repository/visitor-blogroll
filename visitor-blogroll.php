<?php
/**
 * Plugin Name: Visitor Blogroll Widget
 * Plugin URI: http://www.makers-online.co.uk/projects/visitor_blogroll
 * Description: Let your visitor share their blogs with you and your readers. Great way to network.
 * Version: 0.45
 * Author: Luke Saunders
 * Author URI: http://www.makers-online.co.uk/
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */
add_action( 'widgets_init', 'visitorblogroll_load_widgets' );

/**
 * Register our widget.
 * 'Visitorblogroll_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function visitorblogroll_load_widgets() {
	register_widget( 'Visitorblogroll_Widget' );
}

/**
 * Visitorblogroll Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Visitorblogroll_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Visitorblogroll_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'visitorblogroll', 'description' => __('A widget which allows visitors to list their own blogs, a good way to discover your readers\' blogs.', 'visitorblogroll') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'visitorblogroll-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'visitorblogroll-widget', __('Visitor Blogroll Widget', 'visitorblogroll'), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$apikey = $instance['apikey'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div id="visitor_blogroll_container">';
                echo '<script type="text/javascript">';
                echo 'window.visitorBlogrollKey = \'' . $apikey . '\';';
                echo '</script>';
		echo '<script type="text/javascript" src="wp-content/plugins/visitor-blogroll/js/yui-min.js"></script>';
		echo '<script type="text/javascript" src="wp-content/plugins/visitor-blogroll/js/visitor_blogroll.js"></script>';
		echo '<ul class="blogroll" id="visitor_blogroll_list">';
		echo '<li>Loading...</li>';
		echo '</ul>';
		echo '<form method="POST" action="" id="visitor_blogroll_form">';
		echo '<p style="font-size: 10px;">Got a blog? Put your RSS feed URL here</p>';
		echo '<p class="vbr_error vbr_hidden" id="vbr_error"></p>';
		echo '<input type="text" name="rss" />';
		echo '<input type="submit" value="submit" />';
		echo '</form>';
		echo '<p style="-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=50); filter: alpha(opacity=50); opacity: .5; font-size: 10px;">The <a href="http://www.makers-online.co.uk/projects/visitor_blogroll">Visitor Blogroll Widget</a> is a <a href="http://christmas-gifts.makers-online.co.uk">christmas gifts</a> project</p>';
		echo '</div>';
		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['apikey'] = strip_tags( $new_instance['apikey'] );

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Visitor Blogroll', 'visitorblogroll') );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
	<?php
	}
}

?>
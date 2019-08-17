<?php
/*
Plugin Name: WP Category Post Widget
Plugin URI: https://profiles.wordpress.org/rafiul17/#content-plugins
Description: A simple Posts Contrubutors plugin for rtCamp.
Version: 1.0
Author: B.M. Rafiul Alam
Author URI: https://profiles.wordpress.org/rafiul17/#content-plugins
Text Domain: rrr-plug
Domain Path: /languages
*/

class Category_Posts_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'category_posts_widget', // Base ID
			esc_html__( 'Category Posts', 'rrr-plug' ), // Name
			array( 'description' => esc_html__( 'Category Posts', 'rrr-plug' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		
		//========Viwe ================
		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}
		$cat_name = $instance['cat'];
		$q_args = array(
		'category_name' => $cat_name,
		'posts_per_page' => $number,
		);
		$query = new WP_Query( $q_args );
		?>
		<ul>
		<?php
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				echo '<li><a href="'.get_the_permalink().'">' . get_the_title() .'</a></li>';
				// Post data goes here.
			}?>
			<ul>
		<?php
		}
		// Reset the `$post` data to the current post in main query.
		wp_reset_postdata();
		?>
		<?php 
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'rrr-plug' );
		$cat = $instance['cat'];
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'rrr-plug' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cat' ); ?>"><?php _e( 'Select Category:' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'cat' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'cat' ) ); ?>">
			<?php 
			$categories = get_categories( array(
				'orderby' => 'name',
				'parent'  => 0
			) );
			foreach ( $categories as $category ) {?>
				<option value="<?php echo esc_attr( $category->slug ); ?>" id="<?php echo esc_attr( $category->slug ); ?>" <?php if($cat==$category->slug){ echo "selected";}?>>
					<?php echo esc_html( $category->name );?>
				</option>
			<?php  } ?>
			</select>
		</p>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php _e( 'Number of posts to show:','rrr-plug' ); ?></label>
		<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr($this->get_field_name( 'number' ) ); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3" /></p>
		<?php 
	}
	
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['cat'] = ( ! empty( $new_instance['cat'] ) ) ? sanitize_text_field( $new_instance['cat'] ) : '';
		$instance['number']    = (int) $new_instance['number'];

		return $instance;
	}

} // class Foo_Widget
// register Foo_Widget widget
function register_category_post_widget() {
    register_widget( 'Category_Posts_Widget' );
}
add_action( 'widgets_init', 'register_category_post_widget' );
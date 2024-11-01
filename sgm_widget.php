<?php
/**
 * Adds Stylish_google_map widget.
 */
class Stylish_google_map extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'Stylish_google_map', // Base ID
			__('Stylish Google Map', 'text_domain'), // Name
			array('description' => __( 'Stylish google map plugin.', 'text_domain' ),) // Args
		);

		//wp_enqueue_script( 'google-map-api', 'http://maps.google.com/maps/api/js?sensor=true&key='.$instance['gm_api_key'], array(), '1.0.0', true );
		
		add_action( 'sidebar_admin_setup', array( $this, 'admin_setup' ) );

	}
	
	function admin_setup(){

		wp_enqueue_media();
		wp_register_script('sgm-admin-js', plugins_url('sgm_admin.js', __FILE__), array( 'jquery', 'media-upload', 'media-views' ) );
		wp_enqueue_script('sgm-admin-js');
		wp_enqueue_style('sgm-admin', plugins_url('sgm_admin.css', __FILE__) );

	}		
			
	
	/**
	 * Front-end display of widgeta.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$options = get_option('sgm_options');
		$gm_api_key = $options['sgm_field_gm_api_key'];
	
		wp_enqueue_script( 'google-map-api', 'http://maps.google.com/maps/api/js?sensor=true&key='.$gm_api_key, array(), '1.0.0', true );
		// use a template for the output so that it can easily be overridden by theme
		
		// check for template in active theme
		$template = locate_template(array('sgm_widget_template.php'));
		
		// if none found use the default template
		if ( $template == '' ) $template = 'sgm_widget_template.php';
				
		include ( $template );
			
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		//$gm_api_key = ( isset( $instance['gm_api_key'] ) ) ? $instance['gm_api_key'] : '';
		$map_style = ( isset( $instance['map_style'] ) ) ? $instance['map_style'] : '';
		$lattitude = ( isset( $instance['lattitude'] ) ) ? $instance['lattitude'] : '';
		$longitude = ( isset( $instance['longitude'] ) ) ? $instance['longitude'] : '';
		$zoom_level = ( isset( $instance['zoom_level'] ) ) ? $instance['zoom_level'] : '';
		$map_height = ( isset( $instance['map_height'] ) ) ? $instance['map_height'] : '';

	?>	
		
		<div class="sgm_widget">
		
			<h3>Map</h3>
			<p>
				<div class="widget_input">
					<label for="<?php echo $this->get_field_id( 'map_style' ); ?>"><?php _e( 'Google Map Style :' ); ?></label>
					<div>
						<select id="<?php echo $this->get_field_id( 'map_style' ); ?>" name="<?php echo $this->get_field_name( 'map_style' ); ?>" >
			        <option value="" <?php echo isset($map_style) ? (selected($map_style, '', false)) : (''); ?>>
			            <?= esc_html('Default Style', 'sgm'); ?>
			        </option>
			        <option value="style-1" <?php echo isset($map_style) ? (selected($map_style, 'style-1', false)) : (''); ?>>
			            <?= esc_html('Style 1', 'sgm'); ?>
			        </option>
			        <option value="style-2" <?php echo isset($map_style) ? (selected($map_style, 'style-2', false)) : (''); ?>>
			            <?= esc_html('Style 2', 'sgm'); ?>
			        </option>
			        <option value="style-3" <?php echo isset($map_style) ? (selected($map_style, 'style-3', false)) : (''); ?>>
			            <?= esc_html('Style 3', 'sgm'); ?>
			        </option>
			        <option value="style-4" <?php echo isset($map_style) ? (selected($map_style, 'style-4', false)) : (''); ?>>
			            <?= esc_html('Style 4', 'sgm'); ?>
			        </option>
			        <option value="style-5" <?php echo isset($map_style) ? (selected($map_style, 'style-5', false)) : (''); ?>>
			            <?= esc_html('Style 5', 'sgm'); ?>
			        </option>		        
				    </select>
			    </div>
				</div>
				<div class="widget_input">
					<label for="<?php echo $this->get_field_id( 'lattitude' ); ?>"><?php _e( 'Lattitude :' ); ?></label>
					<input class="lattitude" id="<?php echo $this->get_field_id( 'lattitude' ); ?>" name="<?php echo $this->get_field_name( 'lattitude' ); ?>" value="<?php echo $lattitude; ?>" type="text"><br/>
				</div>
				<div class="widget_input">
					<label for="<?php echo $this->get_field_id( 'longitude' ); ?>"><?php _e( 'Longitude :' ); ?></label>
					<input class="longitude" id="<?php echo $this->get_field_id( 'longitude' ); ?>" name="<?php echo $this->get_field_name( 'longitude' ); ?>" value="<?php echo $longitude ?>" type="text"><br/>
				</div>
				<div class="widget_input">
					<label for="<?php echo $this->get_field_id( 'zoom_level' ); ?>"><?php _e( 'Zoom Level :' ); ?></label>
					<input class="zoom_level" id="<?php echo $this->get_field_id( 'zoom_level' ); ?>" name="<?php echo $this->get_field_name( 'zoom_level' ); ?>" value="<?php echo $zoom_level ?>" type="text"><br/>
				</div>
				<div class="widget_input">
					<label for="<?php echo $this->get_field_id( 'map_height' ); ?>"><?php _e( 'Map Height :' ); ?></label>
					<input class="map_height" id="<?php echo $this->get_field_id( 'map_height' ); ?>" name="<?php echo $this->get_field_name( 'map_height' ); ?>" value="<?php echo $map_height ?>" type="text"><br/>
				</div>
			</p>
			
		</div>
	
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

		$instance['map_style'] = ( ! empty( $new_instance['map_style'] ) ) ? strip_tags( $new_instance['map_style'] ) : '';
		$instance['lattitude'] = ( ! empty( $new_instance['lattitude'] ) ) ? strip_tags( $new_instance['lattitude'] ) : '';
		$instance['longitude'] = ( ! empty( $new_instance['longitude'] ) ) ? strip_tags( $new_instance['longitude'] ) : '';
		$instance['zoom_level'] = ( ! empty( $new_instance['zoom_level'] ) ) ? strip_tags( $new_instance['zoom_level'] ) : '';
		$instance['map_height'] = ( ! empty( $new_instance['map_height'] ) ) ? strip_tags( $new_instance['map_height'] ) : '';
		
		return $instance;
	}

} // class My_Widget
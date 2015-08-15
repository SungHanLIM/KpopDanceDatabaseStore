<?php
	
	/*
	*
	*	nx Image Widget
	*	------------------------------------------------
	*	TemplatesNext
	* 	Copyright TemplatesNext 2014 - http://www.TemplatesNext.org
	*/
	
	/*
	*	Plugin Name: NX Image Widget
	*	Plugin URI: http://www.TemplatesNext.org
	*	Description: NX Image Widget
	*	Author: templatesNext
	*	Version: 1.0
	*	Author URI: http://www.TemplatesNext.org
	*/		

	class nx_image_widget extends WP_Widget {
		
		function nx_image_widget() {			
			$widget_ops = array( 'classname' => 'widget-nx-image', 'description' => 'Simple image widget' );
			$control_ops = array( 'width' => 250, 'height' => 200, 'id_base' => 'nx-image-widget' ); //default width = 250
			$this->WP_Widget( 'nx-image-widget', 'NX Image Widget', $widget_ops, $control_ops );
		}
	
		function form($instance) {
		$defaults = array( 'title' => '', 'image_1' => '', 'image_1_url' => '');
		$instance = wp_parse_args( (array) $instance, $defaults );
	
	?>
	<div class="widget-content">		
		<p>
			<label><?php _e('Title', 'nx-admin');?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label><?php _e('Image 1 URL', 'nx-admin');?>:</label>
			<input id="<?php echo $this->get_field_id( 'image_1' ); ?>" name="<?php echo $this->get_field_name( 'image_1' ); ?>" value="<?php echo $instance['image_1']; ?>" class="widefat" type="text"/>
		</p>
		<p>
			<label><?php _e('Image 1 Link', 'nx-admin');?>:</label>
			<input id="<?php echo $this->get_field_id( 'image_1_url' ); ?>" name="<?php echo $this->get_field_name( 'image_1_url' ); ?>" value="<?php echo $instance['image_1_url']; ?>" class="widefat" type="text"/>
		</p>
	</div>		
	<?php	
		}
	
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['image_1'] = strip_tags( $new_instance['image_1'] );
			$instance['image_1_url'] = strip_tags( $new_instance['image_1_url'] );
			return $instance;
		}
		
		function widget($args, $instance) {
			
			extract( $args );
	
			$title = apply_filters('widget_title', $instance['title'] );
			$image_1 = $instance['image_1'];
			$image_1_url = $instance['image_1_url'];
	
			$output = '';
			
			echo $before_widget;
			if ( $title ) echo $before_title . $title . $after_title; 
			
			$output .= '<div class="sidebar-ad-grid"><ul class="clearfix">';
			
			if ($image_1 != "") {
				$output .= '<li>';
				if ($image_1_url != "") {
					$output .= '<a href="'.$image_1_url.'" target="_blank">';
					$output .= '<img src="'.$image_1.'" alt="advert" />';
					$output .= '</a>';
				} else {
					$output .= '<img src="'.$image_1.'" alt="advert" />';
				}
				$output .= '</li>';
			}
		
			$output .= '</ul></div>';
			
			echo $output;
								
			echo $after_widget;
	
		}
			
	}
	
	add_action( 'widgets_init', 'nx_load_image_widget' );
	
	function nx_load_image_widget() {
		register_widget('nx_image_widget');
	}

?>

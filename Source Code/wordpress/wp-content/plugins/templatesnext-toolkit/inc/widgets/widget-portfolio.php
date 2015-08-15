<?php

	/*
	*	nx Portfolio Widget
	*	------------------------------------------------
	*	TemplatesNext
	* 	Copyright TemplatesNext 2014 - http://www.TemplatesNext.org
	*/
	/*
	*	Plugin Name: NX Portfolio List
	*	Plugin URI: http://www.TemplatesNext.org
	*	Description: NX Portfolio List
	*	Author: templatesNext
	*	Version: 1.0
	*	Author URI: http://www.TemplatesNext.org
	*/
	
	// Register widget
	add_action( 'widgets_init', 'init_nx_recent_portfolio' );
	function init_nx_recent_portfolio() { return register_widget('nx_recent_portfolio'); }
	
	class nx_recent_portfolio extends WP_Widget {
		function nx_recent_portfolio() {
			parent::WP_Widget( 'nx_recent_custom_portfolio', $name = 'NX Portfolio List', array( 'description' => __( 'NX Widget for Portofolio in list format', 'tx' ) ) );
		}
	
		function widget( $args, $instance ) {
			global $post;
			extract($args);
	
			// Widget Options
			$title 	 = apply_filters('widget_title', $instance['title'] ); // Title		
			$number	 = $instance['number']; // Number of posts to show
			
			echo $before_widget;
			
		    if ( $title ) echo $before_title . $title . $after_title;
				
			$recent_portfolio = new WP_Query(
				array(
					'post_type' => 'portfolio',
					'posts_per_page' => $number
					)
			);
			
			if( $recent_portfolio->have_posts() ) : 
			
			?>
			
			<ul class="recent-posts-list portfolio-list">
				
				<?php while( $recent_portfolio->have_posts()) : $recent_portfolio->the_post();
				
				$post_title = get_the_title();
				$post_permalink = get_permalink();
				$thumb_image = get_post_thumbnail_id();
				$thumb_img_url = wp_get_attachment_url( $thumb_image, 'widget-image' );
				$image = tx_image_resize( $thumb_img_url, 94, 75, true, false);
				?>
				<li>
					<a href="<?php echo $post_permalink; ?>" class="recent-post-image">
						<?php if ($image) { ?>
						<img src="<?php echo $image['url']; ?>" width="<?php echo $image[1]; ?>" height="<?php echo $image[2]; ?>" alt=" " />
						<?php } ?>
					</a>
					<div class="recent-post-details">
						<a class="recent-post-title" href="<?php echo $post_permalink; ?>" title="<?php echo $post_title; ?>"><?php echo $post_title; ?></a>
					</div>
				</li>
				
				<?php wp_reset_query(); endwhile; ?>
			</ul>
				
			<?php endif; ?>			
			
			<?php
			
			echo $after_widget;
		}
	
		/* Widget control update */
		function update( $new_instance, $old_instance ) {
			$instance    = $old_instance;
				
			$instance['title']  = strip_tags( $new_instance['title'] );
			$instance['number'] = strip_tags( $new_instance['number'] );
			return $instance;
		}
		
		/* Widget settings */
		function form( $instance ) {	
		
			    // Set defaults if instance doesn't already exist
			    if ( $instance ) {
					$title  = $instance['title'];
			        $number = $instance['number'];
			    } else {
				    // Defaults
					$title  = '';
			        $number = '5';
			    }
				
				// The widget form
				?>
                <div class="widget-content">
                    <p>
                        <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __( 'Title:', 'nx-admin' ); ?></label>
                        <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
                    </p>
                    <p>
                        <label for="<?php echo $this->get_field_id('number'); ?>"><?php echo __( 'Number of items to show:', 'nx-admin' ); ?></label>
                        <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
                    </p>
                </div>
		<?php 
		}
	
	}

?>
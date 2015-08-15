<?php

	/*
	*
	*	nx Posts Widget
	*	------------------------------------------------
	*	TemplatesNext
	* 	Copyright TemplatesNext 2014 - http://www.TemplatesNext.org
	*/
	/*
	*	Plugin Name: NX Recent posts
	*	Plugin URI: http://www.TemplatesNext.org
	*	Description: NX Widget for Recent Posts
	*	Author: templatesNext
	*	Version: 1.0
	*	Author URI: http://www.TemplatesNext.org
	*/			
	
	// Register widget
	add_action( 'widgets_init', 'init_nx_recent_posts' );
	function init_nx_recent_posts() { return register_widget('nx_recent_posts'); }
	
	class nx_recent_posts extends WP_Widget {
		function nx_recent_posts() {
			parent::WP_Widget( 'nx_recent_custom_posts', $name = 'NX Recent Posts', array( 'description' => __( 'NX Widget for Recent Posts', 'tx' ) ) );
		}
	
		function widget( $args, $instance ) {
			global $post;
			extract($args);
						
			// Widget Options
			$title 	 = apply_filters('widget_title', $instance['title'] ); // Title		
			$number	 = $instance['number']; // Number of posts to show
			
			echo $before_widget;
			
		    if ( $title ) echo $before_title . $title . $after_title;
			
			$atts['posts_per_page'] = $number;
			$atts['post_type'] = 'post';
			$atts['ignore_sticky_posts'] = 1;
			
			$recent_posts = new WP_Query($atts);
			
			/*
			$recent_posts = new WP_Query(
				array(
					'post_type' => 'post',
					'posts_per_page' => 5,
					'post_count' => 5,
					'post_status' => 'publish'
				)
			);
				*/
			
			if( $recent_posts->have_posts() ) : 
			
			?>
			
			<ul class="recent-posts-list">
				
				<?php while( $recent_posts->have_posts()) : $recent_posts->the_post();
				
				$thumb_stat = "";
				$post_title = get_the_title();
				$post_author = get_the_author_link();
				$post_date = get_the_date('M j, Y');
				$post_categories = get_the_category_list();
				$post_comments = get_comments_number();
				$post_permalink = get_permalink();

				$thumb_image = get_post_thumbnail_id();
				$thumb_img_url = wp_get_attachment_url( $thumb_image, 'small' );
				$image = tx_image_resize( $thumb_img_url, 96, 96, true, false);
				
				if ($image) {
					$thumb_stat = "thumbyes";
				} else
				{
					$thumb_stat = "thumbno";
				}
				?>
				<li class="clearfix nx-recent-post">
                
					<a href="<?php echo $post_permalink; ?>" class="recent-post-image">
						<?php if ($image) { ?>
						<img src="<?php echo $image['url']; ?>" alt=" " />
						<?php } ?>
					</a>
					<div class="recent-post-details <?php echo $thumb_stat; ?>">
						<a class="recent-post-title" href="<?php echo $post_permalink; ?>" title="<?php echo $post_title; ?>"><?php echo $post_title; ?></a>
						<span class="post-meta"><?php printf(__('By %1$s on %2$s', 'tx'), $post_author, $post_date); ?></span>
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
				<p>
					<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __( 'Title:', 'nx-admin' ); ?></label>
					<input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" class="widefat" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('number'); ?>"><?php echo __( 'Number of posts to show:', 'nx-admin'); ?></label>
					<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" />
				</p>
		<?php 
		}
	
	}

?>
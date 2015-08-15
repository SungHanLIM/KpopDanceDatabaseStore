<?php 
/*-----------------------------------------------------------------------------------*/
/* Social icons																		*/
/*-----------------------------------------------------------------------------------*/
function icraft_social_icons () {
	
	$socio_list = '';
	$siciocount = 0;
    $services = array ('facebook','twitter','youtube','flickr','feed','instagram','googleplus');
    
		$socio_list .= '<ul class="social">';	
		foreach ( $services as $service ) :
			
			$active[$service] = esc_url( of_get_option ('itrans_social_'.$service) );
			if ($active[$service]) { 
				$socio_list .= '<li><a href="'.$active[$service].'" title="'.$service.'" target="_blank"><i class="genericon socico genericon-'.$service.'"></i></a></li>';
				$siciocount++;
			}
			
		endforeach;
		$socio_list .= '</ul>';
		
		if($siciocount>0)
		{	
			return $socio_list;
		} else
		{
			return;
		}
}

/*-----------------------------------------------------------------------------------*/
/* ibanner Slider																		*/
/*-----------------------------------------------------------------------------------*/
function icraft_ibanner_slider () {    
	$arrslidestxt = array();
	$template_dir = get_template_directory_uri();
	
	$upload_dir = wp_upload_dir();
	$upload_base_dir = $upload_dir['basedir'];
	$upload_base_url = $upload_dir['baseurl'];	
	
    for($slideno=1;$slideno<=4;$slideno++){
			$strret = '';
			$slide_title = esc_attr(of_get_option ('itrans_slide'.$slideno.'_title', 'Exclusive WooCommerce Features'));
			$slide_desc = esc_attr(of_get_option ('itrans_slide'.$slideno.'_desc', 'To start setting up i-craft go to appearance &gt; Theme Options. Make sure you have installed recommended plugin &#34;TemplatesNext Toolkit&#34; by going appearance > install plugin.'));
			$slide_linktext = esc_attr(of_get_option ('itrans_slide'.$slideno.'_linktext', 'Know More'));
			$slide_linkurl = esc_url(of_get_option ('itrans_slide'.$slideno.'_linkurl', '#'));
			$slide_image = of_get_option ('itrans_slide'.$slideno.'_image', get_template_directory_uri() . '/images/slide'.$slideno.'.jpg');
			
			$slider_image_id = icraft_get_attachment_id_from_url( $slide_image );			
			$slider_resized_image = wp_get_attachment_image( $slider_image_id, "icraft-slider-thumb" );
			
			if (!$slide_linktext)
			{
				$slide_linktext="Read more";
			}			
			
			if ($slide_title) {

				if( $slide_image!='' ){
					if( file_exists( str_replace($upload_base_url,$upload_base_dir,$slide_image) ) ){
						$strret .= '<div class="da-img">' . $slider_resized_image .'</div>';
					}
					else
					{
						$slide_image = $template_dir.'/images/slide'.$slideno.'.jpg';
						$strret .= '<div class="da-img noslide-image"><img src="'.$slide_image.'" alt="'.$slide_title.'" /></div>';					
					}
				}
				else
				{					
					$slide_image = $template_dir.'/images/no-image.png';
					$strret .= '<div class="da-img noslide-image"><img src="'.$slide_image.'" alt="'.$slide_title.'" /></div>';
				}
				
				$strret .= '<div class="slider-content-wrap"><div class="nx-slider-container">';
				$strret .= '<h2>'.$slide_title.'</h2>';
				$strret .= '<p>'.$slide_desc.'</p>';
				$strret .= '<a href="'.$slide_linkurl.'" class="da-link">'.$slide_linktext.'</a>';
				$strret .= '</div></div>';
			}
			if ($strret !=''){
				$arrslidestxt[$slideno] = $strret;
			}
					
	}
	
	$sliderscpeed = "6000";
	if(of_get_option('itrans_sliderspeed'))
	{
		$sliderscpeed = esc_attr(of_get_option('itrans_sliderspeed'));
	}	
	
	if(count($arrslidestxt)>0){
		echo '<div class="ibanner">';
		echo '	<div id="da-slider" class="da-slider" role="banner" data-slider-speed="'.$sliderscpeed.'">';
			
		foreach ( $arrslidestxt as $slidetxt ) :
			echo '<div class="nx-slider">';	
			echo	$slidetxt;
			echo '</div>';

		endforeach;
		echo '	</div>';
		echo '</div>';
	} else
	{
        echo '<div class="iheader front">';
        echo '    <div class="titlebar">';
        echo '        <h1>';
		
		if (of_get_option('itrans_slogan')) {
						//bloginfo( 'name' );
			echo of_get_option('itrans_slogan');
		} 
		
        echo '        </h1>';
		echo ' 		  <h2>';
			    		//bloginfo( 'description' );
						//echo of_get_option('itrans_sub_slogan');
		echo '		</h2>';
        echo '    </div>';
        echo '</div>';
	}
    
}

/*-----------------------------------------------------------------------------------*/
/* find attachment id from url																	*/
/*-----------------------------------------------------------------------------------*/
function icraft_get_attachment_id_from_url( $attachment_url = '' ) {

    global $wpdb;
    $attachment_id = false;

    // If there is no url, return.
    if ( '' == $attachment_url )
        return;

    // Get the upload directory paths
    $upload_dir_paths = wp_upload_dir();

    // Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
    if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

        // If this is the URL of an auto-generated thumbnail, get the URL of the original image
        $attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

        // Remove the upload path base directory from the attachment URL
        $attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

        // Finally, run a custom database query to get the attachment ID from the modified attachment URL
        $attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );

    }

    return $attachment_id;
}



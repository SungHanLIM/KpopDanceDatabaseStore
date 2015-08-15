<?php
/**
 * woothemes_metabox_create()
 *
 * Create the markup for the meta box.
 *
 * @access public
 * @param object $post
 * @param array $callback
 * @return void
 */
function woothemes_metabox_create( $post, $callback ) {
	_deprecated_function( 'woothemes_metabox_create', '6.0.0', __( 'an instance of the WF_Fields_Meta class', 'woothemes' ) );
    global $post;

    // Allow child themes/plugins to act here.
    do_action( 'woothemes_metabox_create', $post, $callback );

    $template_to_show = $callback['args'];

    $woo_metaboxes = get_option( 'woo_custom_template', array() );

    // Array sanity check.
    if ( ! is_array( $woo_metaboxes ) ) { $woo_metaboxes = array(); }

    // Determine whether or not to display general fields.
    $display_general_fields = true;
    if ( count( $woo_metaboxes ) <= 0 ) {
        $display_general_fields = false;
    }

    $output = '';

    // Add nonce for custom fields.
    $output .= wp_nonce_field( 'wooframework-custom-fields', 'wooframework-custom-fields-nonce', true, false );

    if ( $callback['id'] == 'woothemes-settings' ) {
        // Add tabs.
        $output .= '<div class="wooframework-tabs">' . "\n";

        $output .= '<ul class="tabber hide-if-no-js">' . "\n";
            if ( $display_general_fields ) {
                $output .= '<li class="wf-tab-general"><a href="#wf-tab-general">' . __( 'General Settings', 'woothemes' ) . '</a></li>' . "\n";
            }

            // Allow themes/plugins to add tabs to WooFramework custom fields.
            $output .= apply_filters( 'wooframework_custom_field_tab_headings', '' );
        $output .= '</ul>' . "\n";
    }

    if ( $display_general_fields ) {
        $output .= woothemes_metabox_create_fields( $woo_metaboxes, $callback, 'general' );

    }

    // Allow themes/plugins to add tabs to WooFramework custom fields.
    $output = apply_filters( 'wooframework_custom_field_tab_content', $output );

    $output .= '</div>' . "\n";

    echo $output;
} // End woothemes_metabox_create()

/**
 * woothemes_metabox_create_fields()
 *
 * Create markup for custom fields based on the given arguments.
 *
 * @access public
 * @since 5.3.0
 * @param array $metaboxes
 * @param array $callback
 * @param string $token (default: 'general')
 * @return string $output
 */
function woothemes_metabox_create_fields ( $metaboxes, $callback, $token = 'general' ) {
	_deprecated_function( 'woothemes_metabox_create_fields', '6.0.0', __( 'an instance of the WF_Fields_Meta class', 'woothemes' ) );
    global $post;

    if ( ! is_array( $metaboxes ) ) { return; }

    // $template_to_show = $callback['args'];
    $template_to_show = $token;

    $output = '';

    $output .= '<div id="wf-tab-' . esc_attr( $token ) . '">' . "\n";
    $output .= '<table class="woo_metaboxes_table">'."\n";
    foreach ( $metaboxes as $k => $woo_metabox ) {

        // Setup CSS classes to be added to each table row.
        $row_css_class = 'woo-custom-field';
        if ( ( $k + 1 ) == count( $metaboxes ) ) { $row_css_class .= ' last'; }

        $woo_id = 'woothemes_' . $woo_metabox['name'];
        $woo_name = $woo_metabox['name'];

        if ( function_exists( 'woothemes_content_builder_menu' ) ) {
            $metabox_post_type_restriction = $woo_metabox['cpt'][$post->post_type];
        } else {
            $metabox_post_type_restriction = 'undefined';
        }

        if ( ( $metabox_post_type_restriction != '' ) && ( $metabox_post_type_restriction == 'true' ) ) {
            $type_selector = true;
        } elseif ( $metabox_post_type_restriction == 'undefined' ) {
            $type_selector = true;
        } else {
            $type_selector = false;
        }

        $woo_metaboxvalue = '';

        if ( $type_selector ) {

            if( isset( $woo_metabox['type'] ) && ( in_array( $woo_metabox['type'], woothemes_metabox_fieldtypes() ) ) ) {

                    $woo_metaboxvalue = get_post_meta($post->ID,$woo_name,true);

                }

                // Make sure slashes are stripped before output.
                foreach ( array( 'label', 'desc', 'std' ) as $k ) {
                    if ( isset( $woo_metabox[$k] ) && ( $woo_metabox[$k] != '' ) ) {
                        $woo_metabox[$k] = stripslashes( $woo_metabox[$k] );
                    }
                }

                if ( $woo_metaboxvalue == '' && isset( $woo_metabox['std'] ) ) {

                    $woo_metaboxvalue = $woo_metabox['std'];
                }

                // Add a dynamic CSS class to each row in the table.
                $row_css_class .= ' woo-field-type-' . strtolower( $woo_metabox['type'] );

                if( $woo_metabox['type'] == 'info' ) {

                    $output .= "\t".'<tr class="' . $row_css_class . '" style="background:#f8f8f8; font-size:11px; line-height:1.5em;">';
                    $output .= "\t\t".'<th class="woo_metabox_names"><label for="'. esc_attr( $woo_id ) .'">'.$woo_metabox['label'].'</label></th>'."\n";
                    $output .= "\t\t".'<td style="font-size:11px;">'.$woo_metabox['desc'].'</td>'."\n";
                    $output .= "\t".'</tr>'."\n";

                }
                elseif( $woo_metabox['type'] == 'text' ) {

                    $add_class = ''; $add_counter = '';
                    if($template_to_show == 'seo'){$add_class = 'words-count'; $add_counter = '<span class="counter">0 characters, 0 words</span>';}
                    $output .= "\t".'<tr class="' . $row_css_class . '">';
                    $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
                    $output .= "\t\t".'<td><input class="woo_input_text '.$add_class.'" type="'.$woo_metabox['type'].'" value="'.esc_attr( $woo_metaboxvalue ).'" name="'.$woo_name.'" id="'.esc_attr( $woo_id ).'"/>';
                    $output .= '<span class="woo_metabox_desc">'.$woo_metabox['desc'] .' '. $add_counter .'</span></td>'."\n";
                    $output .= "\t".'</tr>'."\n";

                }

                elseif ( $woo_metabox['type'] == 'textarea' ) {

                    $add_class = ''; $add_counter = '';
                    if( $template_to_show == 'seo' ){ $add_class = 'words-count'; $add_counter = '<span class="counter">0 characters, 0 words</span>'; }
                    $output .= "\t".'<tr class="' . $row_css_class . '">';
                    $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.$woo_metabox.'">'.$woo_metabox['label'].'</label></th>'."\n";
                    $output .= "\t\t".'<td><textarea class="woo_input_textarea '.$add_class.'" name="'.$woo_name.'" id="'.esc_attr( $woo_id ).'">' . esc_textarea(stripslashes($woo_metaboxvalue)) . '</textarea>';
                    $output .= '<span class="woo_metabox_desc">'.$woo_metabox['desc'] .' '. $add_counter.'</span></td>'."\n";
                    $output .= "\t".'</tr>'."\n";

                }

                elseif ( $woo_metabox['type'] == 'calendar' ) {

                    $output .= "\t".'<tr class="' . $row_css_class . '">';
                    $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.$woo_metabox.'">'.$woo_metabox['label'].'</label></th>'."\n";
                    $output .= "\t\t".'<td><input class="woo_input_calendar" type="text" name="'.$woo_name.'" id="'.esc_attr( $woo_id ).'" value="'.esc_attr( $woo_metaboxvalue ).'">';
                    $output .= "\t\t" . '<input type="hidden" name="datepicker-image" value="' . get_template_directory_uri() . '/functions/images/calendar.gif" />';
                    $output .= '<span class="woo_metabox_desc">'.$woo_metabox['desc'].'</span></td>'."\n";
                    $output .= "\t".'</tr>'."\n";

                }

                elseif ( $woo_metabox['type'] == 'time' ) {

                    $output .= "\t".'<tr>';
                    $output .= "\t\t".'<th class="woo_metabox_names"><label for="' . esc_attr( $woo_id ) . '">' . $woo_metabox['label'] . '</label></th>'."\n";
                    $output .= "\t\t".'<td><input class="woo_input_time" type="' . $woo_metabox['type'] . '" value="' . esc_attr( $woo_metaboxvalue ) . '" name="' . $woo_name . '" id="' . esc_attr( $woo_id ) . '"/>';
                    $output .= '<span class="woo_metabox_desc">' . $woo_metabox['desc'] . '</span></td>'."\n";
                    $output .= "\t".'</tr>'."\n";

                }

                elseif ( $woo_metabox['type'] == 'time_masked' ) {

                    $output .= "\t".'<tr>';
                    $output .= "\t\t".'<th class="woo_metabox_names"><label for="' . esc_attr( $woo_id ) . '">' . $woo_metabox['label'] . '</label></th>'."\n";
                    $output .= "\t\t".'<td><input class="woo_input_time_masked" type="' . $woo_metabox['type'] . '" value="' . esc_attr( $woo_metaboxvalue ) . '" name="' . $woo_name . '" id="' . esc_attr( $woo_id ) . '"/>';
                    $output .= '<span class="woo_metabox_desc">' . $woo_metabox['desc'] . '</span></td>'."\n";
                    $output .= "\t".'</tr>'."\n";

                }

                elseif ( $woo_metabox['type'] == 'select' ) {

                    $output .= "\t".'<tr class="' . $row_css_class . '">';
                    $output .= "\t\t".'<th class="woo_metabox_names"><label for="' . esc_attr( $woo_id ) . '">' . $woo_metabox['label'] . '</label></th>'."\n";
                    $output .= "\t\t".'<td><select class="woo_input_select" id="' . esc_attr( $woo_id ) . '" name="' . esc_attr( $woo_name ) . '">';
                    $output .= '<option value="">Select to return to default</option>';

                    $array = $woo_metabox['options'];

                    if( $array ) {

                        foreach ( $array as $id => $option ) {
                            $selected = '';

                            if( isset( $woo_metabox['default'] ) )  {
                                if( $woo_metabox['default'] == $option && empty( $woo_metaboxvalue ) ) { $selected = 'selected="selected"'; }
                                else  { $selected = ''; }
                            }

                            if( $woo_metaboxvalue == $option ){ $selected = 'selected="selected"'; }
                            else  { $selected = ''; }

                            $output .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . $option . '</option>';
                        }
                    }

                    $output .= '</select><span class="woo_metabox_desc">' . $woo_metabox['desc'] . '</span></td>'."\n";
                    $output .= "\t".'</tr>'."\n";
                }
                elseif ( $woo_metabox['type'] == 'select2' ) {

                    $output .= "\t".'<tr class="' . $row_css_class . '">';
                    $output .= "\t\t".'<th class="woo_metabox_names"><label for="' . esc_attr( $woo_id ) . '">' . $woo_metabox['label'] . '</label></th>'."\n";
                    $output .= "\t\t".'<td><select class="woo_input_select" id="' . esc_attr( $woo_id ) . '" name="' . esc_attr( $woo_name ) . '">';
                    $output .= '<option value="">Select to return to default</option>';

                    $array = $woo_metabox['options'];

                    if( $array ) {

                        foreach ( $array as $id => $option ) {
                            $selected = '';

                            if( isset( $woo_metabox['default'] ) )  {
                                if( $woo_metabox['default'] == $id && empty( $woo_metaboxvalue ) ) { $selected = 'selected="selected"'; }
                                else  { $selected = ''; }
                            }

                            if( $woo_metaboxvalue == $id ) { $selected = 'selected="selected"'; }
                            else  {$selected = '';}

                            $output .= '<option value="'. esc_attr( $id ) .'" '. $selected .'>' . $option . '</option>';
                        }
                    }

                    $output .= '</select><span class="woo_metabox_desc">'.$woo_metabox['desc'].'</span></td>'."\n";
                    $output .= "\t".'</tr>'."\n";
                }

                elseif ( $woo_metabox['type'] == 'checkbox' ){

                    if( $woo_metaboxvalue == 'true' ) { $checked = ' checked="checked"'; } else { $checked=''; }

                    $output .= "\t".'<tr class="' . $row_css_class . '">';
                    $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
                    $output .= "\t\t".'<td><input type="checkbox" '.$checked.' class="woo_input_checkbox" value="true"  id="'.esc_attr( $woo_id ).'" name="'. esc_attr( $woo_name ) .'" />';
                    $output .= '<span class="woo_metabox_desc" style="display:inline">'.$woo_metabox['desc'].'</span></td>'."\n";
                    $output .= "\t".'</tr>'."\n";
                }

                elseif ( $woo_metabox['type'] == 'radio' ) {

                $array = $woo_metabox['options'];

                if( $array ) {

                $output .= "\t".'<tr class="' . $row_css_class . '">';
                $output .= "\t\t".'<th class="woo_metabox_names"><label for="' . esc_attr( $woo_id ) . '">' . $woo_metabox['label'] . '</label></th>'."\n";
                $output .= "\t\t".'<td>';

                    foreach ( $array as $id => $option ) {
                        if($woo_metaboxvalue == $id) { $checked = ' checked'; } else { $checked=''; }

                            $output .= '<input type="radio" '.$checked.' value="' . $id . '" class="woo_input_radio"  name="'. esc_attr( $woo_name ) .'" />';
                            $output .= '<span class="woo_input_radio_desc" style="display:inline">'. $option .'</span><div class="woo_spacer"></div>';
                        }
                        $output .= "\t".'</tr>'."\n";
                     }
                } elseif ( $woo_metabox['type'] == 'images' ) {

                $i = 0;
                $select_value = '';
                $layout = '';

                foreach ( $woo_metabox['options'] as $key => $option ) {
                     $i++;

                     $checked = '';
                     $selected = '';
                     if( $woo_metaboxvalue != '' ) {
                        if ( $woo_metaboxvalue == $key ) { $checked = ' checked'; $selected = 'woo-meta-radio-img-selected'; }
                     }
                     else {
                        if ( isset( $option['std'] ) && $key == $option['std'] ) { $checked = ' checked'; }
                        elseif ( $i == 1 ) { $checked = ' checked'; $selected = 'woo-meta-radio-img-selected'; }
                        else { $checked = ''; }

                     }

                        $layout .= '<div class="woo-meta-radio-img-label">';
                        $layout .= '<input type="radio" id="woo-meta-radio-img-' . $woo_name . $i . '" class="checkbox woo-meta-radio-img-radio" value="' . esc_attr($key) . '" name="' . $woo_name . '" ' . $checked . ' />';
                        $layout .= '&nbsp;' . esc_html($key) . '<div class="woo_spacer"></div></div>';
                        $layout .= '<img src="' . esc_url( $option ) . '" alt="" class="woo-meta-radio-img-img '. $selected .'" onClick="document.getElementById(\'woo-meta-radio-img-'. esc_js( $woo_metabox["name"] . $i ) . '\').checked = true;" />';
                    }

                $output .= "\t".'<tr class="' . $row_css_class . '">';
                $output .= "\t\t".'<th class="woo_metabox_names"><label for="' . esc_attr( $woo_id ) . '">' . $woo_metabox['label'] . '</label></th>'."\n";
                $output .= "\t\t".'<td class="woo_metabox_fields">';
                $output .= $layout;
                $output .= '<span class="woo_metabox_desc">' . $woo_metabox['desc'] . '</span></td>'."\n";
                $output .= "\t".'</tr>'."\n";

                }

                elseif( $woo_metabox['type'] == 'upload' )
                {
                    if( isset( $woo_metabox['default'] ) ) $default = $woo_metabox['default'];
                    else $default = '';

                    // Add support for the WooThemes Media Library-driven Uploader Module // 2010-11-09.
                    if ( function_exists( 'woothemes_medialibrary_uploader' ) ) {

                        $_value = $default;

                        $_value = get_post_meta( $post->ID, $woo_metabox['name'], true );

                        $output .= "\t".'<tr class="' . $row_css_class . '">';
                        $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.$woo_metabox['name'].'">'.$woo_metabox['label'].'</label></th>'."\n";
                        $output .= "\t\t".'<td class="woo_metabox_fields">'. woothemes_medialibrary_uploader( $woo_metabox['name'], $_value, 'postmeta', $woo_metabox['desc'], $post->ID );
                        $output .= '</td>'."\n";
                        $output .= "\t".'</tr>'."\n";

                    } else {

                        $output .= "\t".'<tr class="' . $row_css_class . '">';
                        $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.esc_attr( $woo_id ).'">'.$woo_metabox['label'].'</label></th>'."\n";
                        $output .= "\t\t".'<td class="woo_metabox_fields">'. woothemes_uploader_custom_fields( $post->ID, $woo_name, $default, $woo_metabox['desc'] );
                        $output .= '</td>'."\n";
                        $output .= "\t".'</tr>'."\n";

                    }
                }

                // Timestamp field.
                elseif ( $woo_metabox['type'] == 'timestamp' ) {
                    $woo_metaboxvalue = get_post_meta($post->ID,$woo_name,true);

                    // Default to current UNIX timestamp.
                    if ( $woo_metaboxvalue == '' ) {
                        $woo_metaboxvalue = time();
                    }

                    $output .= "\t".'<tr class="' . $row_css_class . '">';
                    $output .= "\t\t".'<th class="woo_metabox_names"><label for="'.$woo_metabox.'">'.$woo_metabox['label'].'</label></th>'."\n";
                    $output .= "\t\t".'<td><input type="hidden" name="datepicker-image" value="' . admin_url( 'images/date-button.gif' ) . '" /><input class="woo_input_calendar" type="text" name="'.$woo_name.'[date]" id="'.esc_attr( $woo_id ).'" value="' . esc_attr( date( 'm/d/Y', $woo_metaboxvalue ) ) . '">';

                    $output .= ' <span class="woo-timestamp-at">' . __( '@', 'woothemes' ) . '</span> ';

                    $output .= '<select name="' . $woo_name . '[hour]" class="woo-select-timestamp">' . "\n";
                        for ( $i = 0; $i <= 23; $i++ ) {

                            $j = $i;
                            if ( $i < 10 ) {
                                $j = '0' . $i;
                            }

                            $output .= '<option value="' . $i . '"' . selected( date( 'H', $woo_metaboxvalue ), $j, false ) . '>' . $j . '</option>' . "\n";
                        }
                    $output .= '</select>' . "\n";

                    $output .= '<select name="' . $woo_name . '[minute]" class="woo-select-timestamp">' . "\n";
                        for ( $i = 0; $i <= 59; $i++ ) {

                            $j = $i;
                            if ( $i < 10 ) {
                                $j = '0' . $i;
                            }

                            $output .= '<option value="' . $i . '"' . selected( date( 'i', $woo_metaboxvalue ), $j, false ) .'>' . $j . '</option>' . "\n";
                        }
                    $output .= '</select>' . "\n";
                    /*
                    $output .= '<select name="' . $woo_name . '[second]" class="woo-select-timestamp">' . "\n";
                        for ( $i = 0; $i <= 59; $i++ ) {

                            $j = $i;
                            if ( $i < 10 ) {
                                $j = '0' . $i;
                            }

                            $output .= '<option value="' . $i . '"' . selected( date( 's', $woo_metaboxvalue ), $j, false ) . '>' . $j . '</option>' . "\n";
                        }
                    $output .= '</select>' . "\n";
                    */
                    $output .= '<span class="woo_metabox_desc">'.$woo_metabox['desc'].'</span></td>'."\n";
                    $output .= "\t".'</tr>'."\n";

                }
        } // End IF Statement
    }

    $output .= '</table>'."\n\n";
    $output .= '</div><!--/#wf-tab-' . $token . '-->' . "\n\n";

    return $output;
} // End woothemes_metabox_create_fields()

/**
 * woothemes_metabox_handle()
 *
 * Handle the saving of the custom fields.
 *
 * @access public
 * @param int $post_id
 * @return void
 */
function woothemes_metabox_handle( $post_id ) {
	_deprecated_function( 'woothemes_metabox_handle', '6.0.0', __( 'an instance of the WF_Fields_Meta class', 'woothemes' ) );
    $pID = '';
    global $globals, $post;

    if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
    }

    $woo_metaboxes = get_option( 'woo_custom_template', array() );

    // Sanitize post ID.
    if( isset( $_POST['post_ID'] ) ) {
        $pID = intval( $_POST['post_ID'] );
    }

    // Don't continue if we don't have a valid post ID.
    if ( $pID == 0 ) return;

    $upload_tracking = array();

    if ( isset( $_POST['action'] ) && $_POST['action'] == 'editpost' ) {
        if ( ( get_post_type() != '' ) && ( get_post_type() != 'nav_menu_item' ) && wp_verify_nonce( $_POST['wooframework-custom-fields-nonce'], 'wooframework-custom-fields' ) ) {
            foreach ( $woo_metaboxes as $k => $woo_metabox ) { // On Save.. this gets looped in the header response and saves the values submitted
                if( isset( $woo_metabox['type'] ) && ( in_array( $woo_metabox['type'], woothemes_metabox_fieldtypes() ) ) ) {
                    $var = $woo_metabox['name'];

                    // Get the current value for checking in the script.
                    $current_value = '';
                    $current_value = get_post_meta( $pID, $var, true );

                    if ( isset( $_POST[$var] ) ) {
                        // Sanitize the input.
                        $posted_value = '';
                        $posted_value = $_POST[$var];

                         // If it doesn't exist, add the post meta.
                        if(get_post_meta( $pID, $var ) == "") {
                            add_post_meta( $pID, $var, $posted_value, true );
                        }
                        // Otherwise, if it's different, update the post meta.
                        elseif( $posted_value != get_post_meta( $pID, $var, true ) ) {
                            update_post_meta( $pID, $var, $posted_value );
                        }
                        // Otherwise, if no value is set, delete the post meta.
                        elseif($posted_value == "") {
                            delete_post_meta( $pID, $var, get_post_meta( $pID, $var, true ) );
                        } // End IF Statement
                    } elseif ( ! isset( $_POST[$var] ) && $woo_metabox['type'] == 'checkbox' ) {
                        update_post_meta( $pID, $var, 'false' );
                    } else {
                        delete_post_meta( $pID, $var, $current_value ); // Deletes check boxes OR no $_POST
                    } // End IF Statement

                } else if ( $woo_metabox['type'] == 'timestamp' ) {
                    // Timestamp save logic.

                    // It is assumed that the data comes back in the following format:
                    // date: month/day/year
                    // hour: int(2)
                    // minute: int(2)
                    // second: int(2)

                    $var = $woo_metabox['name'];

                    // Format the data into a timestamp.
                    $date = $_POST[$var]['date'];

                    $hour = $_POST[$var]['hour'];
                    $minute = $_POST[$var]['minute'];
                    // $second = $_POST[$var]['second'];
                    $second = '00';

                    $day = substr( $date, 3, 2 );
                    $month = substr( $date, 0, 2 );
                    $year = substr( $date, 6, 4 );

                    $timestamp = mktime( $hour, $minute, $second, $month, $day, $year );

                    update_post_meta( $pID, $var, $timestamp );
                } elseif( isset( $woo_metabox['type'] ) && $woo_metabox['type'] == 'upload' ) { // So, the upload inputs will do this rather
                    $id = $woo_metabox['name'];
                    $override['action'] = 'editpost';

                    if(!empty($_FILES['attachement_'.$id]['name'])){ //New upload
                    $_FILES['attachement_'.$id]['name'] = preg_replace( '/[^a-zA-Z0-9._\-]/', '', $_FILES['attachement_'.$id]['name']);
                           $uploaded_file = wp_handle_upload($_FILES['attachement_' . $id ],$override);
                           $uploaded_file['option_name']  = $woo_metabox['label'];
                           $upload_tracking[] = $uploaded_file;
                           update_post_meta( $pID, $id, $uploaded_file['url'] );
                    } elseif ( empty( $_FILES['attachement_'.$id]['name'] ) && isset( $_POST[ $id ] ) ) {
                        // Sanitize the input.
                        $posted_value = '';
                        $posted_value = $_POST[$id];

                        update_post_meta($pID, $id, $posted_value);
                    } elseif ( $_POST[ $id ] == '' )  {
                        delete_post_meta( $pID, $id, get_post_meta( $pID, $id, true ) );
                    } // End IF Statement

                } // End IF Statement

                   // Error Tracking - File upload was not an Image
                   update_option( 'woo_custom_upload_tracking', $upload_tracking );
                } // End FOREACH Loop
            }
        }
} // End woothemes_metabox_handle()

/**
 * woothemes_metabox_add()
 *
 * Add meta boxes for the WooFramework's custom fields.
 *
 * @access public
 * @since 1.0.0
 * @return void
 */
function woothemes_metabox_add () {
	_deprecated_function( 'woothemes_metabox_add', '6.0.0', __( 'an instance of the WF_Fields_Meta class', 'woothemes' ) );
    $woo_metaboxes = get_option( 'woo_custom_template', array() );
    if ( function_exists( 'add_meta_box' ) ) {
        if ( function_exists( 'get_post_types' ) ) {
            $custom_post_list = get_post_types();
            // Get the theme name for use in multiple meta boxes.
            $theme_name = get_option( 'woo_themename' );

            foreach ( $custom_post_list as $type ) {

                $settings = array(
                                    'id' => 'woothemes-settings',
                                    'title' => sprintf( __( '%s Custom Settings', 'woothemes' ), $theme_name ),
                                    'callback' => 'woothemes_metabox_create',
                                    'page' => $type,
                                    'priority' => 'normal',
                                    'callback_args' => ''
                                );

                // Allow child themes/plugins to filter these settings.
                $settings = apply_filters( 'woothemes_metabox_settings', $settings, $type, $settings['id'] );
                add_meta_box( $settings['id'], $settings['title'], $settings['callback'], $settings['page'], $settings['priority'], $settings['callback_args'] );
                // if(!empty($woo_metaboxes)) Temporarily Removed
            }
        } else {
            add_meta_box( 'woothemes-settings', sprintf( __( '%s Custom Settings', 'woothemes' ), $theme_name ), 'woothemes_metabox_create', 'post', 'normal' );
            add_meta_box( 'woothemes-settings', sprintf( __( '%s Custom Settings', 'woothemes' ), $theme_name ), 'woothemes_metabox_create', 'page', 'normal' );
        }
    }
} // End woothemes_metabox_add()

/**
 * woothemes_metabox_fieldtypes()
 *
 * Return a filterable array of supported field types.
 *
 * @access public
 * @author Matty
 * @return void
 */
function woothemes_metabox_fieldtypes() {
	_deprecated_function( 'woothemes_metabox_fieldtypes', '6.0.0', __( 'an instance of the WF_Fields_Meta class', 'woothemes' ) );
    return apply_filters( 'woothemes_metabox_fieldtypes', array( 'text', 'calendar', 'time', 'time_masked', 'select', 'select2', 'radio', 'checkbox', 'textarea', 'images' ) );
} // End woothemes_metabox_fieldtypes()

/**
 * woothemes_uploader_custom_fields()
 *
 * Create markup for outputting the custom upload field as a custom field.
 *
 * @access public
 * @param int $pID
 * @param string $id
 * @param string $std
 * @param string $desc
 * @return void
 */
function woothemes_uploader_custom_fields( $pID, $id, $std, $desc ) {
	_deprecated_function( 'woothemes_uploader_custom_fields', '6.0.0', __( 'an instance of the WF_Fields_Meta class', 'woothemes' ) );
    $upload = get_post_meta( $pID, $id, true );
    $href = cleanSource( $upload );
    $uploader = '';
    $uploader .= '<input class="woo_input_text" name="' . $id . '" type="text" value="' . esc_attr( $upload ) . '" />';
    $uploader .= '<div class="clear"></div>'."\n";
    $uploader .= '<input type="file" name="attachement_' . $id . '" />';
    $uploader .= '<input type="submit" class="button button-highlighted" value="Save" name="save"/>';
    if ( $href )
        $uploader .= '<span class="woo_metabox_desc">' . $desc . '</span></td>' . "\n" . '<td class="woo_metabox_image"><a href="' . $upload . '"><img src="' . get_template_directory_uri() . '/functions/thumb.php?src=' . $href . '&w=150&h=80&zc=1" alt="" /></a>';

return $uploader;
} // End woothemes_uploader_custom_fields()

if ( ! function_exists( 'woo_custom_enqueue' ) ) {
/**
 * woo_custom_enqueue()
 *
 * Enqueue JavaScript files used with the custom fields.
 *
 * @access public
 * @param string $hook
 * @since 2.6.0
 * @return void
 */
function woo_custom_enqueue ( $hook ) {
	_deprecated_function( 'woo_custom_enqueue', '6.0.0', __( 'an instance of the WF_Fields_Meta class', 'woothemes' ) );
    wp_register_script( 'jquery-ui-datepicker', get_template_directory_uri() . '/functions/js/ui.datepicker.js', array( 'jquery-ui-core' ) );
    wp_register_script( 'jquery-input-mask', get_template_directory_uri() . '/functions/js/jquery.maskedinput.js', array( 'jquery' ), '1.3' );
    wp_register_script( 'woo-custom-fields', get_template_directory_uri() . '/functions/js/woo-custom-fields.js', array( 'jquery', 'jquery-ui-tabs' ) );

    if ( in_array( $hook, array( 'post.php', 'post-new.php', 'page-new.php', 'page.php' ) ) ) {
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-input-mask' );
        wp_enqueue_script( 'woo-custom-fields' );
    }
} // End woo_custom_enqueue()
}

if ( ! function_exists( 'woo_custom_enqueue_css' ) ) {
/**
 * woo_custom_enqueue_css()
 *
 * Enqueue CSS files used with the custom fields.
 *
 * @access public
 * @author Matty
 * @since 4.8.0
 * @return void
 */
function woo_custom_enqueue_css () {
	_deprecated_function( 'woo_custom_enqueue_css', '6.0.0', __( 'an instance of the WF_Fields_Meta class', 'woothemes' ) );
    global $pagenow;
    wp_register_style( 'woo-custom-fields', get_template_directory_uri() . '/functions/css/woo-custom-fields.css' );
    wp_register_style( 'jquery-ui-datepicker', get_template_directory_uri() . '/functions/css/jquery-ui-datepicker.css' );

    if ( in_array( $pagenow, array( 'post.php', 'post-new.php', 'page-new.php', 'page.php' ) ) ) {
        wp_enqueue_style( 'woo-custom-fields' );
        wp_enqueue_style( 'jquery-ui-datepicker' );
    }
} // End woo_custom_enqueue_css()
}

/**
 * Specify action hooks for the functions above.
 *
 * @access public
 * @since 1.0.0
 * @return void
 */
// add_action( 'admin_enqueue_scripts', 'woo_custom_enqueue', 10, 1 );
// add_action( 'admin_print_styles', 'woo_custom_enqueue_css', 10 );
// add_action( 'edit_post', 'woothemes_metabox_handle', 10 );
// add_action( 'admin_menu', 'woothemes_metabox_add', 10 ); // Triggers woothemes_metabox_create()

/*-----------------------------------------------------------------------------------*/
/* Generates The Options - woothemes_machine */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woothemes_uploader_function' ) ) {
	function woothemes_uploader_function( $id, $std, $mod ) {
		_deprecated_function( 'woothemes_uploader_function', '6.0.0', __( 'an instance of the WF_Fields class', 'woothemes' ) );
		return woothemes_medialibrary_uploader( $id, $std, $mod );
	} // End woothemes_uploader_function()
}

if ( ! function_exists( 'woothemes_machine' ) ) {
	function woothemes_machine( $options ) {
		_deprecated_function( 'woothemes_machine', '6.0.0', __( 'an instance of the WF_Fields class', 'woothemes' ) );
		$counter = 0;
		$menu = '';
		$output = '';

		// Create an array of menu items - multi-dimensional, to accommodate sub-headings.
		$menu_items = array();
		$headings = array();

		foreach ( $options as $k => $v ) {
			if ( $v['type'] == 'heading' || $v['type'] == 'subheading' ) {
				$headings[] = $v;
			}
		}

		$prev_heading_key = 0;

		foreach ( $headings as $k => $v ) {
			$token = 'woo-option-' . preg_replace( '/[^a-zA-Z0-9\s]/', '', strtolower( trim( str_replace( ' ', '', $v['name'] ) ) ) );

			// Capture the token.
			$v['token'] = $token;

			if ( $v['type'] == 'heading' ) {
				$menu_items[$token] = $v;
				$prev_heading_key = $token;
			}

			if ( $v['type'] == 'subheading' ) {
				$menu_items[$prev_heading_key]['children'][] = $v;
			}
		}

		// Loop through the options.
		foreach ( $options as $k => $value ) {

			$counter++;
			$val = '';
			//Start Heading
			if ( $value['type'] != 'heading' && $value['type'] != 'subheading' ) {
				$class = ''; if( isset( $value['class'] ) ) { $class = ' ' . $value['class']; }
				$output .= '<div class="section section-' . esc_attr( $value['type'] ) . esc_attr( $class ) .'">'."\n";
				$output .= '<h3 class="heading">'. esc_html( $value['name'] ) .'</h3>'."\n";
				$output .= '<div class="option">'."\n" . '<div class="controls">'."\n";

			}
			//End Heading

			$select_value = '';
			switch ( $value['type'] ) {

			case 'text':
				$val = $value['std'];
				$std = esc_html( get_option( $value['id'] ) );
				if ( $std != "" ) { $val = $std; }
				$val = stripslashes( $val ); // Strip out unwanted slashes.
				$output .= '<input class="woo-input" name="'. esc_attr( $value['id'] ) .'" id="'. esc_attr( $value['id'] ) .'" type="'. esc_attr( $value['type'] ) .'" value="'. esc_attr( $val ) .'" />';
				break;

			case 'select':
				$output .= '<div class="select_wrapper"><select class="woo-input" name="'. esc_attr( $value['id'] ) .'" id="'. esc_attr( $value['id'] ) .'">';

				$select_value = stripslashes( get_option( $value['id'] ) );

				foreach ( $value['options'] as $option ) {

					$selected = '';

					if( $select_value != '' ) {
						if ( $select_value == $option ) { $selected = ' selected="selected"';}
					} else {
						if ( isset( $value['std'] ) )
							if ( $value['std'] == $option ) { $selected = ' selected="selected"'; }
					}

					$output .= '<option'. $selected .'>';
					$output .= esc_html( $option );
					$output .= '</option>';

				}
				$output .= '</select></div>';

				break;

			case 'select2':
				$output .= '<div class="select_wrapper">' . "\n";

				if ( is_array( $value['options'] ) ) {
					$output .= '<select class="woo-input" name="'. esc_attr( $value['id'] ) .'" id="'. esc_attr( $value['id'] ) .'">';

					$select_value = stripslashes( get_option( $value['id'] ) );


					foreach ( $value['options'] as $option => $name ) {

						$selected = '';

						if( $select_value != '' ) {
							if ( $select_value == $option ) { $selected = ' selected="selected"';}
						} else {
							if ( isset( $value['std'] ) )
								if ( $value['std'] == $option ) { $selected = ' selected="selected"'; }
						}

						$output .= '<option'. $selected .' value="'.esc_attr( $option ).'">';
						$output .= esc_html( $name );
						$output .= '</option>';

					}
					$output .= '</select>' . "\n";
				}

				$output .= '</div>';

				break;

			case 'calendar':
				$val = $value['std'];
				$std = get_option( $value['id'] );
				if ( $std != "" ) { $val = $std; }
				$output .= '<input class="woo-input-calendar" type="text" name="'.esc_attr( $value['id'] ).'" id="'.esc_attr( $value['id']).'" value="'.esc_attr( $val ).'">';
				$output .= '<input type="hidden" name="datepicker-image" value="' . get_template_directory_uri() . '/functions/images/calendar.gif" />';

				break;

			case 'time':
				$val = $value['std'];
				$std = get_option( $value['id'] );
				if ( $std != "" ) { $val = $std; }
				$output .= '<input class="woo-input-time" name="'. esc_attr( $value['id'] ) .'" id="'. esc_attr( $value['id'] ) .'" type="text" value="'. esc_attr( $val ) .'" />';
				break;

			case 'time_masked':
				$val = $value['std'];
				$std = get_option( $value['id'] );
				if ( $std != "" ) { $val = $std; }
				$output .= '<input class="woo-input-time-masked" name="'. esc_attr( $value['id'] ) .'" id="'. esc_attr( $value['id'] ) .'" type="text" value="'. esc_attr( $val ) .'" />';
				break;

			case 'textarea':
				$cols = '8';
				$ta_value = '';

				if( isset( $value['std'] ) ) {

					$ta_value = $value['std'];

					if( isset( $value['options'] ) ) {
						$ta_options = $value['options'];
						if( isset( $ta_options['cols'] ) ) {
							$cols = $ta_options['cols'];
						} else { $cols = '8'; }
					}

				}
				$std = get_option( $value['id'] );
				if( $std != "" ) { $ta_value = stripslashes( $std ); }
				$output .= '<textarea ' . ( ! current_user_can( 'unfiltered_html' ) && in_array( $value['id'], woo_disabled_if_not_unfiltered_html_option_keys() ) ? 'disabled="disabled" ' : '' ) . 'class="woo-input" name="'. esc_attr( $value['id'] ) .'" id="'. esc_attr( $value['id'] ) .'" cols="'. esc_attr( $cols ) .'" rows="8">'.esc_textarea( $ta_value ).'</textarea>';


				break;

			case "radio":
				$select_value = get_option( $value['id'] );

				if ( is_array( $value['options'] ) ) {
					foreach ( $value['options'] as $key => $option ) {

						$checked = '';
						if( $select_value != '' ) {
							if ( $select_value == $key ) { $checked = ' checked'; }
						} else {
							if ( $value['std'] == $key ) { $checked = ' checked'; }
						}
						$output .= '<div class="radio-wrapper"><input class="woo-input woo-radio" type="radio" name="'. esc_attr( $value['id'] ) .'" value="'. esc_attr( $key ) .'" '. $checked .' /><label>' . esc_html( $option ) .'</label></div>';

					}
				}

				break;

			case "checkbox":
				$std = $value['std'];

				$saved_std = get_option( $value['id'] );

				$checked = '';

				if( ! empty( $saved_std ) ) {
					if( $saved_std == 'true' ) {
						$checked = 'checked="checked"';
					} else {
						$checked = '';
					}
				}
				elseif( $std == 'true' ) {
					$checked = 'checked="checked"';
				}
				else {
					$checked = '';
				}
				$output .= '<input type="checkbox" class="checkbox woo-input" name="'.  esc_attr( $value['id'] ) .'" id="'. esc_attr( $value['id'] ) .'" value="true" '. $checked .' />';

				break;

			case "multicheck":
				$std =  $value['std'];

				if ( is_array( $value['options'] ) ) {
					foreach ( $value['options'] as $key => $option ) {

						$woo_key = $value['id'] . '_' . $key;
						$saved_std = get_option( $woo_key );

						if ( ! empty( $saved_std ) ) {
							if ( $saved_std == 'true' ) {
								$checked = 'checked="checked"';
							} else {
								$checked = '';
							}
						} elseif ( $std == $key ) {
							$checked = 'checked="checked"';
						} else {
							$checked = '';
						}
						$output .= '<input type="checkbox" class="checkbox woo-input" name="'. esc_attr( $woo_key ) .'" id="'. esc_attr( $woo_key ) .'" value="true" '. $checked .' /><label for="'. esc_attr( $woo_key ) .'">'. esc_html( $option ) .'</label><br />';

					}
				}
				break;

			case "multicheck2":
				$std =  explode( ',', $value['std'] );

				if ( is_array( $value['options'] ) ) {
					foreach ( $value['options'] as $key => $option ) {

						$woo_key = $value['id'] . '_' . $key;
						$saved_std = get_option( $woo_key );

						if( ! empty( $saved_std ) )
						{
							if( $saved_std == 'true' ) {
								$checked = 'checked="checked"';
							} else {
								$checked = '';
							}
						}
						elseif ( in_array( $key, $std ) ) {
							$checked = 'checked="checked"';
						} else {
							$checked = '';
						}
						$output .= '<input type="checkbox" class="checkbox woo-input" name="'. esc_attr( $woo_key ) .'" id="'. esc_attr( $woo_key ) .'" value="true" '. $checked .' /><label for="'. esc_attr( $woo_key ) .'">'. esc_html( $option ) .'</label><br />';

					}
				}
				break;

			case "upload":
				$output .= woothemes_medialibrary_uploader( $value['id'], $value['std'], null ); // New AJAX Uploader using Media Library
				break;

			case "upload_min":
				$output .= woothemes_medialibrary_uploader( $value['id'], $value['std'], 'min' ); // New AJAX Uploader using Media Library
				break;

			case "color":
				$val = $value['std'];
				$stored  = get_option( $value['id'] );
				if ( $stored != "" ) { $val = $stored; }
				$output .= '<div id="' . esc_attr( $value['id'] ) . '_picker" class="colorSelector"><div></div></div>';
				$output .= '<input class="woo-color" name="'. esc_attr( $value['id'] ) .'" id="'. esc_attr( $value['id'] ) .'" type="text" value="'. esc_attr( $val ) .'" />';
				break;

			case "typography":
				$default = $value['std'];
				$typography_stored = get_option( $value['id'] );

				if ( ! is_array( $typography_stored ) || empty( $typography_stored ) ) {
					$typography_stored = $default;
				}

				/* Font Size */
				$val = $default['size'];
				if ( $typography_stored['size'] != '' ) {
					$val = $typography_stored['size'];
				}
				if ( $typography_stored['unit'] == 'px' ) {
					$show_px = '';
					$show_em = ' style="display:none" ';
					$name_px = ' name="'. esc_attr( $value['id'].'_size') . '" ';
					$name_em = '';
				} else if ( $typography_stored['unit'] == 'em' ) {
					$show_em = '';
					$show_px = 'style="display:none"';
					$name_em = ' name="'. esc_attr( $value['id'].'_size') . '" ';
					$name_px = '';
				} else {
					$show_px = '';
					$show_em = ' style="display:none" ';
					$name_px = ' name="'. esc_attr( $value['id'].'_size') . '" ';
					$name_em = '';
				}
				$output .= '<select class="woo-typography woo-typography-size woo-typography-size-px"  id="'. esc_attr( $value['id'].'_size_px') . '" '. $name_px . $show_px .'>';
				for ( $i = 9; $i < 71; $i++ ) {
					if( $val == strval( $i ) ) { $active = 'selected="selected"'; } else { $active = ''; }
					$output .= '<option value="'. esc_attr( $i ) .'" ' . $active . '>'. esc_html( $i ) .'</option>'; }
				$output .= '</select>';

				$output .= '<select class="woo-typography woo-typography-size woo-typography-size-em" id="'. esc_attr( $value['id'].'_size_em' ) . '" '. $name_em . $show_em.'>';
				$em = 0.5;
				for ( $i = 0; $i < 39; $i++ ) {
					if ( $i <= 24 )   // up to 2.0em in 0.1 increments
						$em = $em + 0.1;
					elseif ( $i >= 14 && $i <= 24 )  // Above 2.0em to 3.0em in 0.2 increments
						$em = $em + 0.2;
					elseif ( $i >= 24 )  // Above 3.0em in 0.5 increments
						$em = $em + 0.5;
					if( $val == strval( $em ) ) { $active = 'selected="selected"'; } else { $active = ''; }
					//echo ' '. $value['id'] .' val:'.floatval($val). ' -> ' . floatval($em) . ' $<br />' ;
					$output .= '<option value="'. esc_attr( $em ) .'" ' . $active . '>'. esc_html( $em ) .'</option>'; }
				$output .= '</select>';

				/* Font Unit */
				$val = $default['unit'];
				if ( $typography_stored['unit'] != '' ) { $val = $typography_stored['unit']; }
				$em = ''; $px = '';
				if( $val == 'em' ) { $em = 'selected="selected"'; }
				if( $val == 'px' ) { $px = 'selected="selected"'; }
				$output .= '<select class="woo-typography woo-typography-unit" name="'. esc_attr( $value['id'] ) .'_unit" id="'. esc_attr( $value['id'].'_unit' ) . '">';
				$output .= '<option value="px" '. $px .'">px</option>';
				$output .= '<option value="em" '. $em .'>em</option>';
				$output .= '</select>';

				/* Font Face */
				$val = $default['face'];
				if ( $typography_stored['face'] != "" )
					$val = $typography_stored['face'];

				$font01 = '';
				$font02 = '';
				$font03 = '';
				$font04 = '';
				$font05 = '';
				$font06 = '';
				$font07 = '';
				$font08 = '';
				$font09 = '';
				$font10 = '';
				$font11 = '';
				$font12 = '';
				$font13 = '';
				$font14 = '';
				$font15 = '';
				$font16 = '';
				$font17 = '';

				if ( strpos( $val, 'Arial, sans-serif' ) !== false ) { $font01 = 'selected="selected"'; }
				if ( strpos( $val, 'Verdana, Geneva' ) !== false ) { $font02 = 'selected="selected"'; }
				if ( strpos( $val, 'Trebuchet' ) !== false ) { $font03 = 'selected="selected"'; }
				if ( strpos( $val, 'Georgia' ) !== false ) { $font04 = 'selected="selected"'; }
				if ( strpos( $val, 'Times New Roman' ) !== false ) { $font05 = 'selected="selected"'; }
				if ( strpos( $val, 'Tahoma, Geneva' ) !== false ) { $font06 = 'selected="selected"'; }
				if ( strpos( $val, 'Palatino' ) !== false ) { $font07 = 'selected="selected"'; }
				if ( strpos( $val, 'Helvetica' ) !== false ) { $font08 = 'selected="selected"'; }
				if ( strpos( $val, 'Calibri' ) !== false ) { $font09 = 'selected="selected"'; }
				if ( strpos( $val, 'Myriad' ) !== false ) { $font10 = 'selected="selected"'; }
				if ( strpos( $val, 'Lucida' ) !== false ) { $font11 = 'selected="selected"'; }
				if ( strpos( $val, 'Arial Black' ) !== false ) { $font12 = 'selected="selected"'; }
				if ( strpos( $val, 'Gill' ) !== false ) { $font13 = 'selected="selected"'; }
				if ( strpos( $val, 'Geneva, Tahoma' ) !== false ) { $font14 = 'selected="selected"'; }
				if ( strpos( $val, 'Impact' ) !== false ) { $font15 = 'selected="selected"'; }
				if ( strpos( $val, 'Courier' ) !== false ) { $font16 = 'selected="selected"'; }
				if ( strpos( $val, 'Century Gothic' ) !== false ) { $font17 = 'selected="selected"'; }

				$output .= '<select class="woo-typography woo-typography-face" name="'. esc_attr( $value['id'].'_face' ) . '" id="'. esc_attr( $value['id'].'_face') . '">';
				$output .= '<option value="Arial, sans-serif" '. $font01 .'>Arial</option>';
				$output .= '<option value="Verdana, Geneva, sans-serif" '. $font02 .'>Verdana</option>';
				$output .= '<option value="&quot;Trebuchet MS&quot;, Tahoma, sans-serif"'. $font03 .'>Trebuchet</option>';
				$output .= '<option value="Georgia, serif" '. $font04 .'>Georgia</option>';
				$output .= '<option value="&quot;Times New Roman&quot;, serif"'. $font05 .'>Times New Roman</option>';
				$output .= '<option value="Tahoma, Geneva, Verdana, sans-serif"'. $font06 .'>Tahoma</option>';
				$output .= '<option value="Palatino, &quot;Palatino Linotype&quot;, serif"'. $font07 .'>Palatino</option>';
				$output .= '<option value="&quot;Helvetica Neue&quot;, Helvetica, sans-serif" '. $font08 .'>Helvetica*</option>';
				$output .= '<option value="Calibri, Candara, Segoe, Optima, sans-serif"'. $font09 .'>Calibri*</option>';
				$output .= '<option value="&quot;Myriad Pro&quot;, Myriad, sans-serif"'. $font10 .'>Myriad Pro*</option>';
				$output .= '<option value="&quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, &quot;Lucida Sans&quot;, sans-serif"'. $font11 .'>Lucida</option>';
				$output .= '<option value="&quot;Arial Black&quot;, sans-serif" '. $font12 .'>Arial Black</option>';
				$output .= '<option value="&quot;Gill Sans&quot;, &quot;Gill Sans MT&quot;, Calibri, sans-serif" '. $font13 .'>Gill Sans*</option>';
				$output .= '<option value="Geneva, Tahoma, Verdana, sans-serif" '. $font14 .'>Geneva*</option>';
				$output .= '<option value="Impact, Charcoal, sans-serif" '. $font15 .'>Impact</option>';
				$output .= '<option value="Courier, &quot;Courier New&quot;, monospace" '. $font16 .'>Courier</option>';
				$output .= '<option value="&quot;Century Gothic&quot;, sans-serif" '. $font17 .'>Century Gothic</option>';

				// Google webfonts
				global $google_fonts;
				sort( $google_fonts );

				$output .= '<option value="">-- Google Fonts --</option>';
				foreach ( $google_fonts as $key => $gfont ) :
					$font[$key] = '';
				if ( $val == $gfont['name'] ) { $font[$key] = 'selected="selected"'; }
				$name = $gfont['name'];
				$output .= '<option value="'.esc_attr( $name ).'" '. $font[$key] .'>'.esc_html( $name ).'</option>';
				endforeach;

				// Custom Font stack
				$new_stacks = get_option( 'framework_woo_font_stack' );
				if( !empty( $new_stacks ) ) {
					$output .= '<option value="">-- Custom Font Stacks --</option>';
					foreach( $new_stacks as $name => $stack ) {
						if ( strpos( $val, $stack ) !== false ) { $fontstack = 'selected="selected"'; } else { $fontstack = ''; }
						$output .= '<option value="'. stripslashes( htmlentities( $stack ) ) .'" '.$fontstack.'>'. str_replace( '_', ' ', $name ).'</option>';
					}
				}

				$output .= '</select>';

				/* Font Weight */
				$val = $default['style'];
				if ( $typography_stored['style'] != "" ) { $val = $typography_stored['style']; }
				$thin = ''; $thinitalic = ''; $normal = ''; $italic = ''; $bold = ''; $bolditalic = '';
				if( $val == '300' ) { $thin = 'selected="selected"'; }
				if( $val == '300 italic' ) { $thinitalic = 'selected="selected"'; }
				if( $val == 'normal' ) { $normal = 'selected="selected"'; }
				if( $val == 'italic' ) { $italic = 'selected="selected"'; }
				if( $val == 'bold' ) { $bold = 'selected="selected"'; }
				if( $val == 'bold italic' ) { $bolditalic = 'selected="selected"'; }

				$output .= '<select class="woo-typography woo-typography-style" name="'. esc_attr( $value['id'].'_style' ) . '" id="'. esc_attr( $value['id'].'_style' ) . '">';
				$output .= '<option value="300" '. $thin .'>Thin</option>';
				$output .= '<option value="300 italic" '. $thinitalic .'>Thin/Italic</option>';
				$output .= '<option value="normal" '. $normal .'>Normal</option>';
				$output .= '<option value="italic" '. $italic .'>Italic</option>';
				$output .= '<option value="bold" '. $bold .'>Bold</option>';
				$output .= '<option value="bold italic" '. $bolditalic .'>Bold/Italic</option>';
				$output .= '</select>';

				/* Font Color */
				$val = $default['color'];
				if ( $typography_stored['color'] != "" ) { $val = $typography_stored['color']; }
				$output .= '<div id="' . esc_attr( $value['id'] . '_color_picker' ) .'" class="colorSelector"><div></div></div>';
				$output .= '<input class="woo-color woo-typography woo-typography-color" name="'. esc_attr( $value['id'] .'_color' ) . '" id="'. esc_attr( $value['id'] .'_color' ) . '" type="text" value="'. esc_attr( $val ) .'" />';

				break;

			case "border":
				$default = $value['std'];
				$border_stored = get_option( $value['id'] );

				/* Border Width */
				$val = $default['width'];
				if ( $border_stored['width'] != "" ) { $val = $border_stored['width']; }
				$output .= '<select class="woo-border woo-border-width" name="'. esc_attr( $value['id'].'_width' ) . '" id="'. esc_attr( $value['id'].'_width' ) . '">';
				for ( $i = 0; $i < 21; $i++ ) {
					if( $val == $i ) { $active = 'selected="selected"'; } else { $active = ''; }
					$output .= '<option value="'. esc_attr( $i ) .'" ' . $active . '>'. esc_html( $i ) .'px</option>'; }
				$output .= '</select>';

				/* Border Style */
				$val = $default['style'];
				if ( $border_stored['style'] != "" ) { $val = $border_stored['style']; }
				$solid = ''; $dashed = ''; $dotted = '';
				if( $val == 'solid' ) { $solid = 'selected="selected"'; }
				if( $val == 'dashed' ) { $dashed = 'selected="selected"'; }
				if( $val == 'dotted' ) { $dotted = 'selected="selected"'; }

				$output .= '<select class="woo-border woo-border-style" name="'. esc_attr( $value['id'].'_style' ) . '" id="'. esc_attr( $value['id'].'_style' ) . '">';
				$output .= '<option value="solid" '. $solid .'>Solid</option>';
				$output .= '<option value="dashed" '. $dashed .'>Dashed</option>';
				$output .= '<option value="dotted" '. $dotted .'>Dotted</option>';
				$output .= '</select>';

				/* Border Color */
				$val = $default['color'];
				if ( $border_stored['color'] != "" ) { $val = $border_stored['color']; }
				$output .= '<div id="' . esc_attr( $value['id'] . '_color_picker' ) . '" class="colorSelector"><div></div></div>';
				$output .= '<input class="woo-color woo-border woo-border-color" name="'. esc_attr( $value['id'] .'_color' ) . '" id="'. esc_attr( $value['id'] .'_color' ) . '" type="text" value="'. esc_attr( $val ) .'" />';

				break;

			case "images":
				$i = 0;
				$select_value = get_option( $value['id'] );

				foreach ( $value['options'] as $key => $option ) {
					$i++;

					$checked = '';
					$selected = '';
					if( $select_value != '' ) {
						if ( $select_value == $key ) { $checked = ' checked'; $selected = 'woo-radio-img-selected'; }
					} else {
						if ( $value['std'] == $key ) { $checked = ' checked'; $selected = 'woo-radio-img-selected'; }
						elseif ( $i == 1  && !isset( $select_value ) ) { $checked = ' checked'; $selected = 'woo-radio-img-selected'; }
						elseif ( $i == 1  && $value['std'] == '' ) { $checked = ' checked'; $selected = 'woo-radio-img-selected'; }
						else { $checked = ''; }
					}

					$output .= '<span>';
					$output .= '<input type="radio" id="woo-radio-img-' . $value['id'] . $i . '" class="checkbox woo-radio-img-radio" value="'. esc_attr( $key ) .'" name="'. esc_attr( $value['id'] ).'" '.$checked.' />';
					$output .= '<span class="woo-radio-img-label">'. esc_html( $key ) .'</span>';
					$output .= '<img src="'.esc_attr( $option ).'" alt="" class="woo-radio-img-img '. $selected .'" onClick="document.getElementById(\'woo-radio-img-'. $value['id'] . $i.'\').checked = true;" />';
					$output .= '</span>';

				}

				break;

			case "info":
				$default = $value['std'];
				$output .= $default;
				break;

			// Timestamp field.
			case 'timestamp':
				$val = get_option( $value['id'] );

				if ( $val == '' ) {
					$val = time();
				}

				$output .= '<input type="hidden" name="datepicker-image" value="' . admin_url( 'images/date-button.gif' ) . '" />' . "\n";

				$output .= '<span class="time-selectors">' . "\n";
				$output .= ' <span class="woo-timestamp-at">' . __( '@', 'woothemes' ) . '</span> ';

				$output .= '<select name="' . esc_attr( $value['id'] . '[hour]' ) . '" class="woo-select-timestamp">' . "\n";
					for ( $i = 0; $i <= 23; $i++ ) {

						$j = $i;
						if ( $i < 10 ) {
							$j = '0' . $i;
						}

						$output .= '<option value="' . esc_attr( $i ) . '"' . selected( date( 'H', $val ), $j, false ) . '>' . esc_html( $j ) . '</option>' . "\n";
					}
				$output .= '</select>' . "\n";

				$output .= '<select name="' . $value['id'] . '[minute]" class="woo-select-timestamp">' . "\n";
					for ( $i = 0; $i <= 59; $i++ ) {

						$j = $i;
						if ( $i < 10 ) {
							$j = '0' . $i;
						}

						$output .= '<option value="' . esc_attr( $i ) . '"' . selected( date( 'i', $val ), $j, false ) .'>' . esc_html( $j ) . '</option>' . "\n";
					}
				$output .= '</select>' . "\n";
				/*
				$output .= '<select name="' . $value['id'] . '[second]" class="woo-select-timestamp">' . "\n";
					for ( $i = 0; $i <= 59; $i++ ) {

						$j = $i;
						if ( $i < 10 ) {
							$j = '0' . $i;
						}

						$output .= '<option value="' . $i . '"' . selected( date( 's', $val ), $j, false ) . '>' . $j . '</option>' . "\n";
					}
				$output .= '</select>' . "\n";
				*/

				$output .= '</span><!--/.time-selectors-->' . "\n";

				$output .= '<input class="woo-input-calendar" type="text" name="' . esc_attr( $value['id'] . '[date]' ) . '" id="'.esc_attr( $value['id'] ).'" value="' . esc_attr( date( 'm/d/Y', $val ) ) . '">';
			break;

			case 'slider':
				$val = $value['std'];
				$std = get_option( $value['id'] );
				if ( $std != "" ) { $val = $std; }
				$val = stripslashes( $val ); // Strip out unwanted slashes.
				$output .= '<div class="ui-slide" id="'. esc_attr( $value['id'] .'_div' ) . '" min="'. esc_attr( $value['min'] ) .'" max="'. esc_attr( $value['max'] ) .'" inc="'. esc_attr( $value['increment'] ) .'"></div>';
				$output .= '<input readonly="readonly" class="woo-input" name="'. esc_attr( $value['id'] ) .'" id="'. esc_attr( $value['id'] ) .'" type="'. esc_attr( $value['type'] ) .'" value="'. esc_attr( $val ) .'" />';
			break;

			case "heading":
				if( $counter >= 2 ) {
					$output .= '</div>'."\n";
				}
				$jquery_click_hook = preg_replace( '/[^a-zA-Z0-9\s]/', '', strtolower( $value['name'] ) );
				// $jquery_click_hook = preg_replace( '/[^\p{L}\p{N}]/u', '', strtolower( $value['name'] ) ); // Regex for UTF-8 languages.
				$jquery_click_hook = str_replace( ' ', '', $jquery_click_hook );

				$jquery_click_hook = "woo-option-" . $jquery_click_hook;
				$menu .= '<li class="'.esc_attr( $value['icon'] ).'"><a title="'. esc_attr( $value['name'] ) .'" href="#'.  $jquery_click_hook  .'">'.  esc_html( $value['name'] ) .'</a></li>';
				$output .= '<div class="group" id="'. esc_attr( $jquery_click_hook ) .'"><h1 class="subtitle">'. esc_html( $value['name'] ) .'</h1>'."\n";
				break;

			case "subheading":
				if( $counter >= 2 ) {
					$output .= '</div>'."\n";
				}
				$jquery_click_hook = preg_replace( '/[^a-zA-Z0-9\s]/', '', strtolower( $value['name'] ) );
				// $jquery_click_hook = preg_replace( '/[^\p{L}\p{N}]/u', '', strtolower( $value['name'] ) ); // Regex for UTF-8 languages.
				$jquery_click_hook = str_replace( ' ', '', $jquery_click_hook );

				$jquery_click_hook = "woo-option-" . $jquery_click_hook;
				$menu .= '<li><a title="' . esc_attr( $value['name'] ) . '" href="#' . $jquery_click_hook . '">' . esc_html( $value['name'] ) . '</a></li>';
				$output .= '<div class="group" id="'. esc_attr( $jquery_click_hook ) .'"><h1 class="subtitle">'. esc_html( $value['name'] ).'</h1>'."\n";
				break;
			}

			// if TYPE is an array, formatted into smaller inputs... ie smaller values
			if ( is_array( $value['type'] ) ) {
				foreach( $value['type'] as $array ) {

					$id = $array['id'];
					$std = $array['std'];
					$saved_std = get_option( $id );
					if( $saved_std != $std ) {$std = $saved_std;}
					$meta = $array['meta'];

					if( $array['type'] == 'text' ) { // Only text at this point

						$output .= '<input class="input-text-small woo-input" name="'. esc_attr( $id ) .'" id="'. esc_attr( $id ) .'" type="text" value="'. esc_attr( $std ) .'" />';
						$output .= '<span class="meta-two">'. esc_html( $meta ) .'</span>';
					}
				}
			}
			if ( $value['type'] != "heading" && $value['type'] != "subheading" ) {
				if ( $value['type'] != "checkbox" )
				{
					$output .= '<br/>';
				}
				$explain_value = ( isset( $value['desc'] ) ) ? $value['desc'] : '';
				if ( !current_user_can( 'unfiltered_html' ) && isset( $value['id'] ) && in_array( $value['id'], woo_disabled_if_not_unfiltered_html_option_keys() ) )
					$explain_value .= '<br /><br /><b>' . esc_html( __( 'You are not able to update this option because you lack the <code>unfiltered_html</code> capability.', 'woothemes' ) ) . '</b>';
				$output .= '</div><div class="explain">'. $explain_value .'</div>'."\n";
				$output .= '<div class="clear"> </div></div></div>'."\n";
			}

		}

		//Checks if is not the Content Builder page
		if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] != 'woothemes_content_builder' ) {
			$output .= '</div>';
		}

		// Override the menu with a new multi-level menu.
		if ( count( $menu_items ) > 0 ) {
			$menu = '';
			foreach ( $menu_items as $k => $v ) {
				$class = '';
				if ( isset( $v['icon'] ) && ( $v['icon'] != '' ) ) {
					$class = $v['icon'];
				}

				if ( isset( $v['children'] ) && ( count( $v['children'] ) > 0 ) ) {
					$class .= ' has-children';
				}

				$menu .= '<li class="top-level ' . $class . '">' . "\n" . '<div class="arrow"><div></div></div>';
				if ( isset( $v['icon'] ) && ( $v['icon'] != '' ) )
					$menu .= '<span class="icon"></span>';
				$menu .= '<a title="' . esc_attr( $v['name'] ) . '" href="#' . $v['token'] . '">' . esc_html( $v['name'] ) . '</a>' . "\n";

				if ( isset( $v['children'] ) && ( count( $v['children'] ) > 0 ) ) {
					$menu .= '<ul class="sub-menu">' . "\n";
						foreach ( $v['children'] as $i => $j ) {
							$menu .= '<li class="icon">' . "\n" . '<a title="' . esc_attr( $j['name'] ) . '" href="#' . $j['token'] . '">' . esc_html( $j['name'] ) . '</a></li>' . "\n";
						}
					$menu .= '</ul>' . "\n";
				}
				$menu .= '</li>' . "\n";

			}
		}

		return array( $output, $menu, $menu_items );
	} // End woothemes_machine()
}
?>
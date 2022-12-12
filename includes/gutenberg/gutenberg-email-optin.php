<?php

/**
 * Gutenberg Integration: Register Email Optin block in Editor (backend + frontend)
 * 
 * @since 1.1.0
 */

function gb_sf4wp_gutenberg_email_optin_enqueue()
{
	// styles

	wp_enqueue_style(

		'sf4wp-gutenberg-email-optin-block',

	    plugin_dir_url( __FILE__ ) . 'block.min.css',

	    array(),

	    GB_SF4WP_VER,

	    'all'

	);

	// editor js

	wp_enqueue_script(
	    
	    'sf4wp-gutenberg-email-optin-block',

	    plugin_dir_url( __FILE__ ) . 'block.min.js',

	    array(
	    	'wp-blocks',
	    	'wp-editor', 
	    	'wp-element', 
	    	'wp-i18n',
	    	'wp-edit-post'
	    ),

	    GB_SF4WP_VER,

	    TRUE

	);

	$localized = array(

		'url' => admin_url( 'admin-ajax.php' ),

		'block_description' => __( 'A customizable email optin block that allows to let people subscribe to your SendFox lists', 'sf4wp' ),

		'block_default_heading' => __( 'Your Title Goes Here...', 'sf4wp' ),

		'block_default_content' => __( 'Your content goes here...', 'sf4wp' ),

		'block_default_button_text' => __( 'Subscribe', 'sf4wp' ),

		'error_retrieve_lists' => __( 'Can not retrieve lists from SendFox. Please, check your API key.', 'sf4wp' ),

		'panel_form_settings' => __( 'Form Settings', 'sf4wp' ),

		'panel_button_settings' => __( 'Button Settings', 'sf4wp' ),

		'label_heading_align' => __( 'Align Heading', 'sf4wp' ),

		'label_subheading_align' => __( 'Align Subheading', 'sf4wp' ),		

		'label_select_list' => __( 'Select SendFox List', 'sf4wp' ),

		'label_button_label' => __( 'Button Label', 'sf4wp' ),

		'placeholder_button_label' => __( 'Enter button label...', 'sf4wp' ),

		'label_button_label_color' => __( 'Label Color', 'sf4wp' ),

		'label_button_bg_color' => __( 'Background Color', 'sf4wp' ),

		'label_form_width' => __( 'Form Width', 'sf4wp' ),

		'label_form_width_hint' => __( 'enter number or leave empty for full width', 'sf4wp' ),

		'label_form_border_style' => __( 'Border Style', 'sf4wp' ),

		'label_form_border_width' => __( 'Border Width', 'sf4wp' ),

		'label_form_border_radius' => __( 'Border Radius', 'sf4wp' ),

		'label_form_border_color' => __( 'Border Color', 'sf4wp' ),

		'label_form_bg_color' => __( 'Background Color', 'sf4wp' ),

		'label_form_bg_image' => __( 'Background Image', 'sf4wp' ),

		'label_button_border_style' => __( 'Border Style', 'sf4wp' ),

		'label_button_border_width' => __( 'Border Width', 'sf4wp' ),

		'label_button_border_radius' => __( 'Border Radius', 'sf4wp' ),

		'label_button_border_color' => __( 'Border Color', 'sf4wp' ),

		'button_upload_image' => __( 'Upload Image', 'sf4wp' ),

		'button_remove_image' => __( 'Remove Image', 'sf4wp' ),

		'placeholder_first_name' => __( 'First name', 'sf4wp' ),

		'placeholder_last_name' => __( 'Last name', 'sf4wp' ),

		'placeholder_email' => __( 'Email', 'sf4wp' ),
	);

	wp_localize_script( 
		'sf4wp-gutenberg-email-optin-block',
		'sf4wp_gutenberg',
		$localized
	);

	// front js

	if( !is_admin() )
	{
		wp_enqueue_script(
		    
		    'sf4wp-gutenberg-email-optin-front',

		    plugin_dir_url( __FILE__ ) . 'front.min.js',

		    array(
		    	'jquery'
		    ),

		    GB_SF4WP_VER,

		    TRUE

		);

		$localized = array(

			'url' => admin_url( 'admin-ajax.php' ),

			'fields_required' => __( 'All fields are required.', 'sf4wp' ),

			'invalid_email' => __( 'You have entered an invalid email address.', 'sf4wp' ),

			'request_error' => __( 'Unable to process request. Please, contact support.', 'sf4wp' ),

			'msg_thanks' => __( 'Thanks for subscribing!', 'sf4wp' ),
		);

		wp_localize_script( 
			'sf4wp-gutenberg-email-optin-front',
			'sf4wp_gutenberg',
			$localized
		);
	}
}
add_action( 'enqueue_block_assets', 'gb_sf4wp_gutenberg_email_optin_enqueue' );

/**
 * Gutenberg Integration: Get SendFox lists
 * 
 * @since 1.1.0
 */

function gb_sf4wp_gutenberg_email_optin_get_lists()
{
	$lists = gb_sf4wp_get_lists();

	$lists_json = json_encode( $lists );

	wp_send_json( $lists_json, 200 );
}
add_action( 'wp_ajax_sf4wp_gutenberg_get_lists', 'gb_sf4wp_gutenberg_email_optin_get_lists' );
add_action( 'wp_ajax_nopriv_sf4wp_gutenberg_get_lists', 'gb_sf4wp_gutenberg_email_optin_get_lists' );

/**
 * Gutenberg Integration: Include jQuery if not included
 * 
 * @since 1.1.0
 */

function gb_sf4wp_gutenberg_email_optin_jquery()
{
	if( ! wp_script_is( 'jquery' ) )
	{
		wp_register_script( 'jquery', '//cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js', FALSE, NULL );

		wp_enqueue_script( 'jquery' );
	}
}
add_action( 'wp_enqueue_scripts', 'gb_sf4wp_gutenberg_email_optin_jquery' );

/**
 * Gutenberg Integration: Handle email optin submission
 * 
 * @since 1.1.0
 */

function gb_sf4wp_gutenberg_email_optin_handle_subscribe()
{
	$first_name = isset( $_POST['first_name'] ) ? $_POST['first_name'] : '';
    $last_name = isset( $_POST['last_name'] ) ? $_POST['last_name'] : '';
    $email = isset( $_POST['email'] ) ? $_POST['email'] : '';
    $list_id = isset( $_POST['list_id'] ) ? $_POST['list_id'] : '';

	$result = gb_sf4wp_add_contact(
		$data = array(
			'first_name' => $first_name,
			'last_name' => $last_name,
			'email' => $email,
			'lists' => array( 
			    $list_id
			),
		)
	);

	wp_send_json( $result, 200 );
}
add_action( 'wp_ajax_sf4wp_gutenberg_subscribe', 'gb_sf4wp_gutenberg_email_optin_handle_subscribe' );
add_action( 'wp_ajax_nopriv_sf4wp_gutenberg_subscribe', 'gb_sf4wp_gutenberg_email_optin_handle_subscribe' );

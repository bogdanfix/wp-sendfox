<?php
/*
Plugin Name: WP SendFox
Plugin URI: https://wordpress.org/plugins/wp-sendfox/
Description: Capture emails and add them to your SendFox list via comments, registration, WooCommerce checkout, Gutenberg page or Divi Builder page. Export your WP users and WooCommerce customers to your list.
Author: BogdanFix
Author URI: https://bogdanfix.com/
Version: 1.2.0
Text Domain: sf4wp
Domain Path: /lang
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WC requires at least: 3.0.0
WC tested up to: 7.2.0
*/

define( 'GB_SF4WP_NAME', 'SendFox for WordPress' );
define( 'GB_SF4WP_VER', '1.2.0' );
define( 'GB_SF4WP_ID', 'wp-sendfox' );

define( 'GB_SF4WP_CORE_FILE', __FILE__ );

define( 'GB_SF4WP_USERS_PER_STEP', 5 );
define( 'GB_SF4WP_STEP_TIMEOUT', 250 );

/**
 * Init and hook most of the subscribe forms
 *
 * @since 1.0.0
 */

function gb_sf4wp_init()
{
    // comment form

    add_action( 'comment_form_after_fields', 'gb_sf4wp_comment_form' );

    // registration form

    add_action( 'register_form', 'gb_sf4wp_registration_form' );

    // checkout form

    $options = get_option( 'gb_sf4wp_options' );

    if( !empty( $options['woocommerce-checkout'] ) )
    {
        $form = $options['woocommerce-checkout'];

        if( empty( $form['position'] ) )
        {
            $form['position'] = 'after_notes';
        }

        if( !empty( $form['position'] ) )
        {
            // if( $form['position'] == 'after_email' )

            if( $form['position'] == 'after_billing' )
            {
                add_action( 'woocommerce_after_checkout_billing_form', 'gb_sf4wp_wc_checkout_form' );
            }
            elseif( $form['position'] == 'after_shipping' )
            {   
                add_action( 'woocommerce_after_checkout_shipping_form', 'gb_sf4wp_wc_checkout_form' );
            }
            elseif( $form['position'] == 'after_customer' )
            {   
                add_action( 'woocommerce_checkout_after_customer_details', 'gb_sf4wp_wc_checkout_form' );
            }
            elseif( $form['position'] == 'before_submit' )
            {
                add_action( 'woocommerce_review_order_before_submit', 'gb_sf4wp_wc_checkout_form' );
            }
            elseif( $form['position'] == 'after_notes' )
            {
                add_action( 'woocommerce_after_order_notes', 'gb_sf4wp_wc_checkout_form' );
            }
        }
    }
}
add_action( 'init', 'gb_sf4wp_init' );

/**
 * Load plugin's textdomain
 *
 * @since 1.0.0
 */

function gb_sf4wp_load_textdomain()
{
    load_plugin_textdomain( 'sf4wp', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang' ); 
}
add_action( 'plugins_loaded', 'gb_sf4wp_load_textdomain' );

/**
 * Registers plugin's admin page
 *
 * @since 1.0.0
 */

function gb_sf4wp_add_page()
{
    add_menu_page( 
        GB_SF4WP_NAME,
        'SendFox',
        'manage_options',
        GB_SF4WP_ID,
        'gb_sf4wp_do_page',
        plugins_url( 'assets/img/sendfox-icon.svg', __FILE__ ),
        99
    );
}
add_action( 'admin_menu', 'gb_sf4wp_add_page' );

/**
 * Displays plugin's admin page
 *
 * @since 1.0.0
 */

function gb_sf4wp_do_page()
{
    if( !current_user_can( 'manage_options' ) )
    {
        wp_die( __( 'Oops, you can\'t access this page.', 'sf4wp' ) );
    }

    include_once 'wp-sendfox-admin.php';
}

/**
 * Registers plugins admin page options
 *
 * @param string $option_group A settings group name. Must exist prior to the register_setting call.
 * This must match the group name in settings_fields()
 * @param string $option_name The name of an option to sanitize and save.
 *
 * @since 1.0.0
 */

function gb_sf4wp_admin_init()
{
    register_setting( 'gb_sf4wp_options', 'gb_sf4wp_options' );
}
add_action( 'admin_init', 'gb_sf4wp_admin_init' );

/**
 * Redirect to the settings page on the first activation
 *
 * @since 1.0.0
 */

function gb_sf4wp_plugin_install( $plugin )
{
    if( $plugin == plugin_basename( __FILE__ ) )
    {
        $options = get_option( 'gb_sf4wp_options' );

        if( empty( $options ) )
        {
            if( wp_redirect( admin_url( 'admin.php?page=' . GB_SF4WP_ID ) ) )
            {
                exit;
            }
        }
    }
}
add_action( 'activated_plugin', 'gb_sf4wp_plugin_install' );

/**
 * API Request: Get lists
 *
 * @since 1.0.0
 */

function gb_sf4wp_get_lists()
{
    $response = array();

    $lists_cache = get_site_transient( 'gb_sf4wp_api_lists' );

    if( $lists_cache === FALSE )
    {
        $response = gb_sf4wp_api_request( 'lists' );

        $response = gb_sf4wp_api_response( $response );

        $response_data = array();

        // process multiple pages

        if( 
            $response['status'] === 'success' && 
            !empty( $response['result'] ) && 
            !empty( $response['result']['data'] ) && 
            !empty( $response['result']['total'] ) 
        )
        {
            // preserve first page of lists

            $response_data = $response['result']['data'];

            // count total pages

            $lists_total = absint( $response['result']['total'] );
            $list_per_page = absint( $response['result']['per_page'] );

            $pagination_needed = absint( $lists_total / $list_per_page ) + 1;

            if( $pagination_needed >= 2 )
            {
                // request pages >=2 and merge lists

                $response_pages = array();

                for( $i = 2; $i <= $pagination_needed; $i++ )
                {
                    $response_pages = gb_sf4wp_api_request( 'lists?page=' . $i );

                    $response_pages = gb_sf4wp_api_response( $response_pages );

                    if( 
                        $response_pages['status'] === 'success' && 
                        !empty( $response_pages['result'] ) && 
                        !empty( $response_pages['result']['data'] )
                    )
                    {
                        $response_data = array_merge( $response_data, $response_pages['result']['data'] );
                    }
                }
            }

            // update data in the final response

            $response['result']['data'] = $response_data;
        }

        // save cache to transient

        set_site_transient( 'gb_sf4wp_api_lists', $response, 86400 );
    }
    else
    {
        $response = $lists_cache;
    }

    return $response;
}

/**
 * API Request: Add contact
 *
 * @since 1.0.0
 */

function gb_sf4wp_add_contact( $contact = array() )
{
    $response = gb_sf4wp_api_request( 'contacts', $contact, 'POST' );

    return gb_sf4wp_api_response( $response );
}

/**
 * API Request Wrapper
 *
 * @since 1.0.0
 */

function gb_sf4wp_api_request( $endpoint = 'me', $data = array(), $method = 'GET' )
{
    $result = FALSE;

    $base = 'https://api.sendfox.com/';

    $options = get_option( 'gb_sf4wp_options' );

    if( empty( $options['api_key'] ) )
    {
        $result = array(
            'status'     => 'error',
            'error'      => 'empty_api_key',
            'error_text' => __( 'API Key is not set.', 'sf4wp' ),
        );

        return $result;
    }

    // access for 3rd parties

    $args = apply_filters( 'gb_sf4wp_request_args', $data, $endpoint, $method );

    // prepare request args

    $args = array( 
        'body' => $args,        
    );

    $args['headers'] = array(
        'Authorization' => 'Bearer ' . $options['api_key'],
    );

    $args['method']  = $method;
    $args['timeout'] = 30;

    // make request

    $result = wp_remote_request( $base . $endpoint, $args );

    gb_sf4wp_log(
        array(
            '_time' => date( 'H:i:s d.m.Y' ),
            'event' => 'API_REQUEST',
            'endpoint' => $base . $endpoint,
            'args' => $args,
            'response_raw' => $result,
        )
    );

    if( 
        !is_wp_error( $result ) && 
        ( $result['response']['code'] == 200 || $result['response']['code'] == 201 )
    )
    {
        $result = wp_remote_retrieve_body( $result );

        $result = json_decode( $result, TRUE );

        if( !empty( $result ) )
        {
            $result = array(
                'status'     => 'success',
                'result'     => $result,
            );
        }
        else
        {
            $result = array(
                'status'     => 'error',
                'error'      => 'json_parse_error',
                'error_text' => __( 'JSON Parse', 'sf4wp' ),
            );
        }
    }
    else // if WP_Error happened
    {
        if( is_object( $result ) )
        {
            $result = array(
                'status'     => 'error',
                'error'      => 'request_error',
                'error_text' => $result->get_error_message(),
            );
        }
        else
        {
            $result = wp_remote_retrieve_body( $result );

            $result = array(
                'status'     => 'error',
                'error'      => 'request_error',
                'error_text' => $result,
            );
        }
    }

    return $result;
}

/**
 * API Response Wrapper
 *
 * @since 1.0.0
 */

function gb_sf4wp_api_response( $response = array() )
{
    $result = array(
        'status'     => 'error',
        'error'      => 'status_error',
        'error_text' => __( 'Error: Response Status', 'sf4wp' ),
    );

    if( !empty( $response['status'] ) )
    {
        $result = $response;
    }

    return $result;
}

/**
 * Display menu icon CSS in admin header
 *
 * @since 1.0.0
 */

function gb_sf4wp_admin_header()
{
    echo '<style type="text/css">
            #adminmenu .toplevel_page_wp-sendfox .wp-menu-image img { width: 18px; padding: 6px 0 0 0; }
            #adminmenu .toplevel_page_wp-sendfox.current .wp-menu-image img { opacity: 1; }
    </style>';
}
add_action( 'admin_head', 'gb_sf4wp_admin_header' );

/**
 * Pre-update plugin settings filter
 *  
 * @since 1.0.0
 */

function gb_sf4wp_pre_update_option( $new_value = '', $old_value = '' )
{
    if( !empty( $old_value ) )
    {
        // update existing settings with new value (only changed ones)

        foreach( $old_value as $k => $v )
        {
            if( isset( $new_value[ $k ] ) )
            {
                $old_value[ $k ] = $new_value[ $k ];
            }
        }

        // add new ones, that don't exist in existing settings yet

        foreach( $new_value as $k => $v )
        {
            if( ! isset( $old_value[ $k ] ) )
            {
                $old_value[ $k ] = $new_value[ $k ];
            }
        }
    }
    else
    {
        // if old value doesn't exist, just save the new value (ex. first install)

        $old_value = $new_value;
    }

    return $old_value;
}
add_filter( 'pre_update_option_gb_sf4wp_options', 'gb_sf4wp_pre_update_option', 10, 2 );

/**
 * Comment Form: add checkbox to comment form
 *
 * @since 1.0.0
 */

function gb_sf4wp_comment_form( $post_id )
{
    $options = get_option( 'gb_sf4wp_options' );

    if( !empty( $options['wp-comment-form'] ) )
    {
        $form = $options['wp-comment-form'];

        if( !empty( $form['enabled'] ) )
        {
            if( !empty( $form['implicit'] ) )
            {
                $form['implicit'] = 'style="display: none !important;"';

                $form['prechecked'] = 1;
            }
            else
            {
                $form['implicit'] = '';
            }

            if( !empty( $form['prechecked'] ) )
            {
                $form['prechecked'] = 'checked="checked"';
            }
            else
            {
                $form['prechecked'] = '';
            }

            if( empty( $form['label'] ) )
            {
                $form['label'] = 'subscribe to our newsletter';
            }
            else
            {
                $form['label'] = strip_tags( $form['label'], '<strong><em><a>' );
            }

            if( !empty( $form['css'] ) )
            {
                echo '<style type="text/css">.comment-form-sf4wp-subscribe { display: block; clear: both; float: none; margin: 1em 0; padding: 0; }</style>';
            }

            echo '<p class="comment-form-sf4wp-subscribe" ' . $form['implicit'] . '>
                    <label for="sf4wp-subscribe">' . 
                        '<input type="checkbox" id="sf4wp-subscribe" name="sf4wp-subscribe" value="1" ' . $form['prechecked'] . ' />&nbsp;' . 
                        $form['label'] . 
                    '</label>
                </p>';
        }
    }
}

/**
 * Comment Form: process checkbox
 *
 * @since 1.0.0
 */

function gb_sf4wp_comment_post( $comment_id, $comment_approved )
{
    if( !empty( $_POST['sf4wp-subscribe'] ) )
    {
        $comment = get_comment( $comment_id );

        if( !empty( $comment ) && !empty( $comment->comment_author_email ) )
        {
            $options = get_option( 'gb_sf4wp_options' );

            // either process email from auto-approved comment or 
            // from manually approved one, when status transition happens

            if( $comment_approved == 0 )
            {
                // mark comment data to be sent to SendFox only after approval

                if( !empty( $options['wp-comment-form']['approved_check'] ) )
                {
                    add_comment_meta( $comment_id, 'sf4wp_send', 1, TRUE );

                    return;
                }
                else
                {
                    return;
                }
            }

            // if comment data is submitted instantly

            if( 
                !empty( $options['wp-comment-form'] ) && 
                !empty( $options['wp-comment-form']['list'] )
            )
            {
                $contact = array(
                    'email' => $comment->comment_author_email,
                    'lists' => array( 
                        intval( $options['wp-comment-form']['list'] ) 
                    ),
                );

                if( !empty( $comment->comment_author ) )
                {
                    $contact['first_name'] = $comment->comment_author;
                }

                $contact = apply_filters( 'sf4wp_before_add_contact', $contact, 'comment', $comment );

                $result = gb_sf4wp_add_contact( $contact );
            }
        }
    }
}
add_action( 'comment_post', 'gb_sf4wp_comment_post', 10, 2 );

/**
 * Comment Form: process manual comment approval
 *
 * @since 1.1.0
 */

function gb_sf4wp_comment_approved( $new_status, $old_status, $comment )
{
    if( $old_status === $new_status )
    {
        return;
    }

    if( $new_status !== 'approved' )
    {
        return;
    }

    if(
        !empty( $comment ) && 
        !empty( $comment->comment_ID ) && 
        !empty( $comment->comment_author_email )
    )
    {
        $options = get_option( 'gb_sf4wp_options' );

        // if comment data is marked to be sent to SendFox after approval

        if( get_comment_meta( $comment->comment_ID, 'sf4wp_send', TRUE ) )
        {
            if(
                !empty( $options['wp-comment-form'] ) && 
                !empty( $options['wp-comment-form']['list'] )
            )
            {
                $contact = array(
                    'email' => $comment->comment_author_email,
                    'lists' => array( 
                        intval( $options['wp-comment-form']['list'] ),
                    ),
                );

                if( !empty( $comment->comment_author ) )
                {
                    $contact['first_name'] = $comment->comment_author;
                }

                $contact = apply_filters( 'sf4wp_before_add_contact', $contact, 'comment', $comment );

                $result = gb_sf4wp_add_contact( $contact );
            }
        }
    }
}
add_action( 'transition_comment_status', 'gb_sf4wp_comment_approved', 10, 3 );

/**
 * Registration Form: add checkbox to registration form
 *
 * @since 1.0.0
 */

function gb_sf4wp_registration_form()
{
    $options = get_option( 'gb_sf4wp_options' );

    if( !empty( $options['wp-registration-form'] ) )
    {
        $form = $options['wp-registration-form'];

        if( !empty( $form['enabled'] ) )
        {
            if( !empty( $form['implicit'] ) )
            {
                $form['implicit'] = 'style="display: none !important;"';

                $form['prechecked'] = 1;
            }
            else
            {
                $form['implicit'] = '';
            }

            if( !empty( $form['prechecked'] ) )
            {
                $form['prechecked'] = 'checked="checked"';
            }
            else
            {
                $form['prechecked'] = '';
            }

            if( empty( $form['label'] ) )
            {
                $form['label'] = 'subscribe to our newsletter';
            }
            else
            {
                $form['label'] = strip_tags( $form['label'], '<strong><em><a>' );
            }

            if( !empty( $form['css'] ) )
            {
                echo '<style type="text/css">
                    #login form p.registration-form-sf4wp-subscribe { display: block; clear: both; float: none; margin: 0.5em 0; padding: 0; }
                    #login form p.registration-form-sf4wp-subscribe label { font-size: 13px; }
                </style>';
            }

            echo '<p class="registration-form-sf4wp-subscribe" ' . $form['implicit'] . '>
                    <label for="sf4wp-subscribe">' . 
                        '<input type="checkbox" id="sf4wp-subscribe" name="sf4wp-subscribe" value="1" ' . $form['prechecked'] . ' />&nbsp;' . 
                        $form['label'] . 
                    '</label>
                </p>';
        }
    }
}
 
/**
 * Registration Form: process checkbox
 *
 * @since 1.0.0
 */

function gb_sf4wp_user_register( $user_id )
{
    if( !empty( $_POST['sf4wp-subscribe'] ) )
    {
        $user = get_user_by( 'id', $user_id );

        if( !empty( $user ) && !empty( $user->user_email ) )
        {
            $options = get_option( 'gb_sf4wp_options' );

            if( 
                !empty( $options['wp-registration-form'] ) && 
                !empty( $options['wp-registration-form']['list'] )
            )
            {
                $contact = array(
                    'email' => $user->user_email,
                    'lists' => array( 
                        intval( $options['wp-registration-form']['list'] ) 
                    ),
                );

                if( !empty( $user->user_nicename ) )
                {
                    $contact['first_name'] = $user->user_nicename;
                }

                $contact = apply_filters( 'sf4wp_before_add_contact', $contact, 'registration', $user );

                $result = gb_sf4wp_add_contact( $contact ); 
            }
        }
    }
}
add_action( 'user_register', 'gb_sf4wp_user_register' );

/**
 * WooCommerce Checkout: add checkbox to checkout form
 *
 * @since 1.0.0
 */

function gb_sf4wp_wc_checkout_form()
{
    $options = get_option( 'gb_sf4wp_options' );

    if( !empty( $options['woocommerce-checkout'] ) )
    {
        $form = $options['woocommerce-checkout'];

        if( !empty( $form['enabled'] ) )
        {
            if( !empty( $form['implicit'] ) )
            {
                $form['implicit'] = ' style="display: none !important;"';

                $form['prechecked'] = 1;
            }
            else
            {
                $form['implicit'] = '';
            }

            if( !empty( $form['prechecked'] ) )
            {
                $form['prechecked'] = 'checked="checked"';
            }
            else
            {
                $form['prechecked'] = '';
            }

            if( empty( $form['label'] ) )
            {
                $form['label'] = 'subscribe to our newsletter';
            }
            else
            {
                $form['label'] = strip_tags( $form['label'], '<strong><em><a>' );
            }

            if( !empty( $form['position'] ) && $form['position'] == 'after_customer' )
            {
                $clear_css = 'clear: none;';
            }
            else
            {
                $clear_css = 'clear: both;';
            }

            if( !empty( $form['css'] ) )
            {
                echo '<style type="text/css">
                        .wc-checkout-sf4wp-subscribe { display: block; ' . $clear_css . ' float: none; margin: 1em 0; padding: 0; }
                        .wc-checkout-sf4wp-subscribe input[type="checkbox"] { margin-right: 0.3342343017em; }
                    </style>';
            }

            echo '<p class="wc-checkout-sf4wp-subscribe" ' . $form['implicit'] . '>
                    <label for="sf4wp-subscribe">' . 
                        '<input type="checkbox" id="sf4wp-subscribe" name="sf4wp-subscribe" value="1" ' . $form['prechecked'] . ' />&nbsp;' . 
                        $form['label'] . 
                    '</label>
                </p>';
        }
    }
}

/**
 * WooCommerce Checkout: process checkbox
 *
 * @since 1.0.0
 */

function gb_sf4wp_order_processed( $order_id )
{
    if( !empty( $_POST['sf4wp-subscribe'] ) )
    {
        if( !empty( $_POST['billing_email'] ) )
        {
            $options = get_option( 'gb_sf4wp_options' );

            if( 
                !empty( $options['woocommerce-checkout'] ) && 
                !empty( $options['woocommerce-checkout']['list'] )
            )
            {
                $contact = array(
                    'email' => sanitize_email( $_POST['billing_email'] ),
                    'lists' => array( 
                        intval( $options['woocommerce-checkout']['list'] ) 
                    ),
                );

                if( !empty( $_POST['billing_first_name'] ) )
                {
                    $contact['first_name'] = sanitize_text_field( $_POST['billing_first_name'] );
                }

                if( !empty( $_POST['billing_last_name'] ) )
                {
                    $contact['last_name'] = sanitize_text_field( $_POST['billing_last_name'] );
                }

                $contact = apply_filters( 'sf4wp_before_add_contact', $contact, 'wc-checkout', $order_id );

                $result = gb_sf4wp_add_contact( $contact ); 
            }
        }
    }
}
add_action( 'woocommerce_checkout_order_processed', 'gb_sf4wp_order_processed' );

/**
 * Process synchronization
 *
 * @since 1.0.0
 */

function gb_sf4wp_process_sync()
{
    $result = array( 'result' => 'error' );

    if( 
        !empty( $_POST['stage'] ) &&
        !empty( $_POST['list'] ) &&
        !empty( $_POST['mode'] )
    )
    {
        $stage = intval( $_POST['stage'] );
        $list = intval( $_POST['list'] );
        $mode = sanitize_text_field( $_POST['mode'] );

        if( $stage === 1 )
        {
            // count total emails

            if( $mode == 'wp-users' )
            {
                $users = count_users();

                if( !empty( $users['total_users'] ) )
                {
                    $result['result'] = 'success';
                    $result['stage'] = 1;
                    $result['total'] = intval( $users['total_users'] );
                    $result['total_steps'] = ceil( $users['total_users'] / GB_SF4WP_USERS_PER_STEP );
                }
                else
                {
                    $response['error_text'] = 'count users error';
                }
            }
            elseif( $mode == 'wc-customers' )
            {
                global $wpdb;

                $users = $wpdb->get_var(

                    "SELECT COUNT(DISTINCT meta_value) 
                    FROM {$wpdb->postmeta} 
                    WHERE meta_key = '_billing_email' AND meta_value <> '' ;" 

                );

                if( !empty( $users ) )
                {
                    $result['result'] = 'success';
                    $result['stage'] = 1;
                    $result['total'] = intval( $users );
                    $result['total_steps'] = ceil( $users / GB_SF4WP_USERS_PER_STEP );
                }
                else
                {
                    $response['error_text'] = 'count customers error';
                }
            }
            else
            {
                $response['error_text'] = 'mode error';
            }
        }
        elseif( $stage === 2 )
        {
            $step = 0;
            $total_steps = 0;

            if( isset( $_POST['step'] ) )
            {
                $step = intval( $_POST['step'] );
            }

            if( isset( $_POST['total_steps'] ) )
            {
                $total_steps = intval( $_POST['total_steps'] );
            }

            // pull emails and subscribe

            if( $mode == 'wp-users' )
            {
                $users = get_users( 
                    array( 
                        'fields' => array( 'ID', 'user_email' ),
                        'number' => GB_SF4WP_USERS_PER_STEP,
                        'paged' => $step,
                    )
                );

                if( !empty( $users ) )
                {
                    $contact = $contact_info = array();

                    $import_success = $import_fail = array();

                    $import_success_count = $import_fail_count = 0;

                    foreach( $users as $u )
                    {
                        $u_data = get_userdata( $u->ID );

                        $contact = array(
                            'email' => $u->user_email,
                            'first_name' => ( !empty( $u_data->first_name ) ? $u_data->first_name : '' ),
                            'last_name' => ( !empty( $u_data->last_name ) ? $u_data->last_name : '' ),
                            'lists' => array( 
                                $list
                            ),
                        );

                        $contact = apply_filters( 'sf4wp_before_export_contact', $contact, $mode );

                        $request = gb_sf4wp_add_contact( $contact );

                        if( 
                            !empty( $request['status'] ) && 
                            $request['status'] === 'success' && 

                            !empty( $request['result'] ) && 
                            !empty( $request['result']['id'] ) &&
                            empty( $request['result']['invalid_at'] )
                        )
                        {
                            ++$import_success_count;
                        }
                        else
                        {
                            ++$import_fail_count;

                            $import_fail[] = $contact['email'];
                        }
                    }

                    $result['result'] = 'success';
                    $result['stage'] = 2;
                    $result['import_success_count'] = $import_success_count;
                    $result['import_fail_count'] = $import_fail_count;
                    $result['import_fail_emails'] = $import_fail;
                }
                else
                {
                    $response['error_text'] = 'get users error';
                }
            }
            elseif( $mode == 'wc-customers' )
            {
                global $wpdb;

                $offset = intval( GB_SF4WP_USERS_PER_STEP * ( $step - 1 ) );
                $limit = intval( GB_SF4WP_USERS_PER_STEP );

                $customers = $wpdb->get_results( 

                    "SELECT DISTINCT meta_value as billing_email 
                    FROM {$wpdb->postmeta} 
                    WHERE meta_key = '_billing_email' AND meta_value <> '' 
                    LIMIT {$offset}, {$limit};" 
                );

                if( !empty( $customers ) )
                {
                    $contact = array();

                    $import_success = $import_fail = array();

                    $import_success_count = $import_fail_count = 0;

                    foreach( $customers as $c )
                    {
                        $contact = array(
                            'email' => $c->billing_email,
                            // 'first_name' => ( !empty( $c->first_name ) ? $c->first_name : '' ),
                            // 'last_name' => ( !empty( $c->last_name ) ? $c->last_name : '' ),
                            'lists' => array( 
                                $list
                            ),
                        );

                        $contact = apply_filters( 'sf4wp_before_export_contact', $contact, $mode );

                        $request = gb_sf4wp_add_contact( $contact );

                        if(
                            !empty( $request['status'] ) && 
                            $request['status'] === 'success' && 

                            !empty( $request['result'] ) && 
                            !empty( $request['result']['id'] ) &&
                            empty( $request['result']['invalid_at'] )
                        )
                        {
                            ++$import_success_count;
                        }
                        else
                        {
                            ++$import_fail_count;

                            $import_fail[] = $contact['email'];
                        }
                    }

                    $result['result'] = 'success';
                    $result['stage'] = 2;
                    $result['import_success_count'] = $import_success_count;
                    $result['import_fail_count'] = $import_fail_count;
                    $result['import_fail_emails'] = $import_fail;
                }
                else
                {
                    $response['error_text'] = 'get customers error';
                }
            }
            else
            {
                $response['error_text'] = 'mode error';
            }
        }
        else
        {
            $response['error_text'] = 'stage error';
        }
    }
    else
    {
        $response['error_text'] = 'request error';
    }

    echo json_encode( $result );

    wp_die();
}
add_action( 'wp_ajax_sf4wp_process_sync', 'gb_sf4wp_process_sync' );

/**
 * Integration: Replace pluggable Divi Builder's method to 
 * register 3rd-party components
 *
 * @since 1.1.0
 */

if ( ! function_exists( 'et_core_get_third_party_components' ) ):

function et_core_get_third_party_components( $group = '' )
{
    $third_party_components = apply_filters( 'et_core_get_third_party_components', array(), $group );

    return $third_party_components;
}

endif;

/**
 * Integration: Add SendFox email provider to Divi Builder opt-ins
 *
 * @since 1.1.0
 */

function gb_sf4wp_add_divi_provider( $third_party_components, $group )
{
    if( class_exists( 'ET_Core_API_Email_Provider' ) )
    {
        if( $group === 'api' || $group === 'api/email' )
        {
            $options = get_option( 'gb_sf4wp_options' );

            if( !empty( $options['divi'] ) && !empty( $options['divi']['enabled'] ) )
            {
                require_once plugin_dir_path( __FILE__ ) . 'includes/divi-builder/divi-email-provider.php';

                $third_party_components['sendfox'] = new ET_Core_API_Email_SendFox( 'builder', 'default', '' );
            }
        }
    }

    return $third_party_components;
}
add_filter( 'et_core_get_third_party_components', 'gb_sf4wp_add_divi_provider', 1, 2 );

/**
 * Integration: Add SendFox email provider to Gutenberg Email Optin
 *
 * @since 1.0.0
 */

function gb_sf4wp_add_gutenberg_email_optin()
{
    if( function_exists( 'has_blocks' ) )
    {
        $options = get_option( 'gb_sf4wp_options' );

        if( !empty( $options['gutenberg'] ) && !empty( $options['gutenberg']['enabled'] ) )
        {
            require_once plugin_dir_path( __FILE__ ) . 'includes/gutenberg/gutenberg-email-optin.php';
        }
    }
}
add_action( 'after_setup_theme', 'gb_sf4wp_add_gutenberg_email_optin' );

/**
 * Helper: Simple logging function, useful for debugging
 *
 * @since 1.0.0
 */

function gb_sf4wp_log( $data = array(), $file = 'debug.log', $force = FALSE )
{
    if( empty( $file ) )
    {
        $file = 'debug.log';
    }

    if( !empty( $data ) )
    {
        $options = get_option( 'gb_sf4wp_options' );

        if( empty( $options['enable_log'] ) && $force === FALSE )
        {
            return;
        }

        if( empty( $data['_time'] ) )
        {
            $data[ '_time' ] = date( 'H:i:s d.m.y' );
        }

        $data = json_encode( $data );

        // remove api_key from logs

        if( !empty( $options['api_key'] ) )
        {
            $data = str_replace( $options['api_key'], '###_API_KEY_REMOVED_###', $data );
        }

        $data = $data . PHP_EOL . PHP_EOL;

        return file_put_contents( dirname( __FILE__ ) . '/' . $file, $data, FILE_APPEND );
    }
}


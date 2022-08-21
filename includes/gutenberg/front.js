/**
 * Handle Gutenberg related stuff (frontend)
 * 
 * @since 1.1.0
 */

jQuery(document).ready( function($) {

	$('.gb-sf4wp-gutenberg-email-optin-submit').click(function()
	{
		var first_name = $('.gb-sf4wp-gutenberg-email-optin-first-name').val();

		var last_name = $('.gb-sf4wp-gutenberg-email-optin-last-name').val();

		var email_address = $('.gb-sf4wp-gutenberg-email-optin-email-address').val();

		var list_id = $('.gb-sf4wp-gutenberg-email-optin-list').val();

		var url = sf4wp_gutenberg.url + '?action=sf4wp_gutenberg_subscribe';

		if( first_name.length == 0 || last_name.length == 0 || email_address.length == 0 )
		{
            $('.gb-sf4wp-gutenberg-email-optin-error-msg').show();

            $('.gb-sf4wp-gutenberg-email-optin-error-msg').html(
            	'<h3>' + sf4wp_gutenberg.fields_required + '</h3>'
            );

            $('.gb-sf4wp-gutenberg-email-optin-success-msg').hide();
        } 
        else if( ! gb_sf4wp_gutenberg_email_optin_is_valid_email( email_address ) )
        {
            $('.gb-sf4wp-gutenberg-email-optin-error-msg').show();

            $('.gb-sf4wp-gutenberg-email-optin-error-msg').html(
            	'<h3>' + sf4wp_gutenberg.invalid_email + '</h3>'
            );

            $('.gb-sf4wp-gutenberg-email-optin-success-msg').hide();
        } 
        else 
        {
        	$.post( url,
            {
                first_name: first_name,
                last_name: last_name,
                email: email_address,
                list_id: list_id

            }, function( response ){	                    	

            	if( response.status === 'error' )
            	{
            		$('.gb-sf4wp-gutenberg-email-optin-error-msg').show();

                    $('.gb-sf4wp-gutenberg-email-optin-error-msg').html(
                        '<h3>' + sf4wp_gutenberg.request_error + '</h3>'
                    );

                    $('.gb-sf4wp-gutenberg-email-optin-success-msg').hide();
            	}
            	else
            	{
            		$('.gb-sf4wp-gutenberg-email-optin-success-msg').show();

                    $('.gb-sf4wp-gutenberg-email-optin-success-msg').html(
                    	'<h3>' + sf4wp_gutenberg.msg_thanks + '</h3>'
                    );

                    $('.gb-sf4wp-gutenberg-email-optin-error-msg').hide();

                    $('.gb-sf4wp-gutenberg-email-optin-first-name').val('');

                    $('.gb-sf4wp-gutenberg-email-optin-last-name').val('');

                    $('.gb-sf4wp-gutenberg-email-optin-email-address').val('');
            	}
        	});
        }
	});

	function gb_sf4wp_gutenberg_email_optin_is_valid_email( email )
	{
		var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;

	    return regex.test( email );
	}
});
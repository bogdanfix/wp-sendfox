/**
 * Handle Gutenberg related stuff (backend)
 * 
 * @since 1.1.0
 */

(function gb_sf4wp_gutenberg_email_optin( blocks, element, blockEditor, component ) 
{
	var el = element.createElement;
    var Fragment = element.Fragment;
    var BlockControls = blockEditor.BlockControls;
    var RichText = blockEditor.RichText;
    var InspectorControls = blockEditor.InspectorControls;
    var TextControl = component.TextControl;
    var TextareaControl = component.TextareaControl;
    var SelectControl = component.SelectControl;
    var ColorPicker = component.ColorPicker;
    var MediaUpload = blockEditor.MediaUpload;
    var Button = component.Button;
    var useBlockProps = blockEditor.useBlockProps;

    // styles

    const labelStyles = {
    	marginBottom: '10px',
    	display: 'block'
    };

    // manipulate the promise

    var lists = gb_sf4wp_gutenberg_email_optin_get_lists();

    var optionsArray = [];
    var sendfoxEnabled = true;

    lists.then(
    	(promise) => {
    		if( promise )
    		{
    			promise = JSON.parse( promise );

    			promise.result.data.map( (r) => {
	                optionsArray.push(
	                    { value: r.id, label: r.name }
	                );
	            });
    		}
    		else
    		{
    			optionsArray.push(
	               { value: '-', label: '-' }
	            );

	            sendfoxEnabled = false;

	            alert( sf4wp_gutenberg.error_retrieve_lists );
    		}   		
    	}
    );

    wp.blocks.registerBlockType( 'gb-sf4wp/gutenberg-email-optin', {

		title: 'Gutenberg Email Optin',
		description: sf4wp_gutenberg.block_description,
		category: 'widgets',
		icon: 'feedback',

		attributes: {
			formHeading: {
				type: 'string',
	           	default: 'Your Title Goes Here'
			},
			formHeadingStyle: {
				type: 'string',
				source: 'attribute',
				selector: 'h2',
				attribute: 'style'
	        },
	        formHeadingTextColor: {
	        	type: 'string',
	            default: '#FFFFFF'
	        },
	        formDescription: {
	            type: 'string',
	            default: 'Your content goes here. Edit or remove this text in the block settings.'
	        },
    		formDescriptionStyle: {
    			type: 'string',
    			source: 'attribute',
    			selector: 'p',
    			attribute: 'style'
            },
            formDescriptionTextColor: {
				type: 'string',
				default: '#FFFFFF'
            },
	        formBtnText: {
	        	type: 'string',
	        	default: 'Subscribe'
	        },
	        formBtnBgColor: {
	        	type: 'string',
	            default: '#005C7A'
	        },
	        formBtnTextColor:{
	            type: 'string',
	            default: '#FFFFFF'
	        },
	        formBtnStyle:{
	            type: 'string',
	            source: 'attribute',
	            selector: 'input',
	            attribute: 'style'
	        },
	        formBgColor:{
	            type: 'string',
	            default: '#7EBEC5'
	        },
	        formBgImg:{
	            type: 'string'
	        },
	        formStyle:{
	            type: 'string',
	            source: 'attribute',
	            selector: 'div',
	            attribute: 'style'
	        },
	        formList:{
	            type: 'string'
	        }
		},

		// backend

		edit: function({ attributes, setAttributes }) {

			if( ! sendfoxEnabled )
		    {
		    	setAttributes( { formList: '' } );
		    }

			const onChangeFormHeading = ( heading ) => {
				
		        setAttributes( { formHeading: heading } );

		    };

		    const onChangeFormDescription = ( description ) => {

		    	setAttributes( { formDescription: description } );

		    };

		    const onChangeList = ( list ) => {

		    	setAttributes( { formList: list } );

		    };

		    const onChangeFormHeadingTextColor = ( headingTextColor ) => {

		    	setAttributes( { formHeadingTextColor: headingTextColor } );

		    };

		    const onChangeFormDescriptionTextColor = ( descriptionTextColor ) => {

		    	setAttributes( { formDescriptionTextColor: descriptionTextColor } );

		    };

		    const onChangeButtonText = ( btnText ) => {

		    	setAttributes( { formBtnText: btnText } );

		    };

		    const onChangeButtonTextColor = ( btnTextColor ) => {

		    	setAttributes( { formBtnTextColor: btnTextColor } );

		    };

		    const onChangeButtonBackgroundColor = ( btnBgColor ) => {

		    	setAttributes( { formBtnBgColor: btnBgColor } );

		    };

		    const onChangeFormBackgroundColor = ( formBgColor ) => {

		    	setAttributes( { formBgColor: formBgColor } );

		    };

		    const onChangeFormBackgroundImage = ( media ) => {

		    	setAttributes( { formBgImg: media.url } );

		    };

		    const onClickRemoveBgImg = ( e ) => {

		    	setAttributes( { formBgImg: '' } );

		    }; 

		    // STYLES

		    // heading style

		    var formHeadingStyle = attributes.formHeadingStyle;
		    var formHeadingTextColor = attributes.formHeadingTextColor;

		    formHeadingStyle = {
		    	color: formHeadingTextColor
		    }

		    // description style

		    var formDescriptionStyle = attributes.formDescriptionStyle;
		    var formDescriptionTextColor = attributes.formDescriptionTextColor;

		    formDescriptionStyle = {
		    	color: formDescriptionTextColor
		    }

		    // button background, text color and style

		    var btnStyle = attributes.formBtnStyle;
		    var btnBg = attributes.formBtnBgColor;
		    var btnTextColor = attributes.formBtnTextColor;

		    btnStyle = {
		        backgroundColor: btnBg,
		        color: btnTextColor,
		        maxWidth: '100%',
		        minWidth: '100%',
		        height: '45px'
		    };

		    // form style

		    var formStyle = attributes.formStyle;
		    var formBgColor = attributes.formBgColor;
		    var formBgImg = attributes.formBgImg;

		    if( typeof formBgImg === 'undefined' )
		    {
		    	formBgImg = '';
		    }

		    formStyle = {
		        backgroundColor: formBgColor,
		        backgroundImage: 'url(' + formBgImg + ')',
		        backgroundSize: 'cover',
		        backgroundPosition: 'center',
		        backgroundRepeat: 'no-repeat'
		    };

		    var inputStyle = {
		    	padding: '12px'
		    };

		    var removeBtnVisibility = 'is-button is-destructive';

		    removeBtnVisibility = formBgImg ? 
		    	removeBtnVisibility + ' sf4wp-gutenberg-visible' : 
		    	removeBtnVisibility + ' sf4wp-gutenberg-invisible';

			return(
				el( Fragment, {},
					el( InspectorControls,{},
						el(
							'div', { className: 'components-panel__body is-opened gb-sf4wp-gutenberg-email-optin-form-controls' },
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_form_heading ),
							
							el( TextControl, { placeholder: sf4wp_gutenberg.placeholder_form_heading, onChange: onChangeFormHeading, value: attributes.formHeading }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_form_description ),
							
							el( TextareaControl, { placeholder: sf4wp_gutenberg.placeholder_form_description, onChange: onChangeFormDescription, value: attributes.formDescription }),
							
							// el( 'label', { style: labelStyles }, 'Email Provider' ),
							
							// el( TextControl, { type: 'hidden', value: sendfoxEnabled ? 'SendFox' : '', className: 'gb-sf4wp-gutenberg-email-optin-form-controls-provider' }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_select_list ),
							
							el( SelectControl, { options: optionsArray, value: attributes.formList, onChange: onChangeList, className: 'gb-sf4wp-gutenberg-email-optin-form-controls-lists' }),

							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_button_label ),
							
							el( TextControl, { placeholder: sf4wp_gutenberg.placeholder_button_label, onChange: onChangeButtonText, value: attributes.formBtnText }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_heading_text_color ),
							
							el( ColorPicker, { onChange: onChangeFormHeadingTextColor }),

							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_description_text_color ),
							
							el( ColorPicker, { onChange: onChangeFormDescriptionTextColor }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_button_label_color ),
							
							el( ColorPicker, { onChange: onChangeButtonTextColor }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_button_bg_color ),
							
							el( ColorPicker, { onChange: onChangeButtonBackgroundColor }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_form_bg_color ),
							
							el( ColorPicker, { onChange: onChangeFormBackgroundColor }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_form_bg_image ),

							el( 'div', { className: 'components-button-group' },
							
								el( MediaUpload, {
									onSelect: onChangeFormBackgroundImage,
									accept: 'image/*',
									render: function( openFileDialog ) {
										return(
											el( Button, { onClick: openFileDialog.open, className: 'is-button is-secondary' }, sf4wp_gutenberg.button_upload_image )
										);
									}
								}),

								el( Button, { onClick: onClickRemoveBgImg, className: removeBtnVisibility }, sf4wp_gutenberg.button_remove_image )

							)
						)
					),

					el( 'div', { className: 'gb-sf4wp-gutenberg-email-optin-form-wrapper', style: formStyle },

						el( RichText.Content, { tagName: 'h2', value: attributes.formHeading, style: formHeadingStyle }, attributes.formHeading ),

						el( RichText.Content, { tagName: 'p', value: attributes.formDescription, style: formDescriptionStyle }, attributes.formDescription ),

						el( TextControl,{ type: 'text', placeholder: sf4wp_gutenberg.placeholder_first_name, style: inputStyle }),

						el( TextControl,{ type: 'text', placeholder: sf4wp_gutenberg.placeholder_last_name, style: inputStyle }),

						el( TextControl,{ type: 'email', placeholder: sf4wp_gutenberg.placeholder_email, style: inputStyle }),

						el( Button,{ value: attributes.formBtnText, style: btnStyle }, attributes.formBtnText )

					)					
				)
			);
		},

		// frontend

		save: function({ attributes }) {

			const blockProps = useBlockProps.save();

			// saved data

			var formHeading = attributes.formHeading;
			var formDescription = attributes.formDescription;
			var formList = attributes.formList;

			var formHeadingStyle = attributes.formHeadingStyle;
			var formHeadingTextColor = attributes.formHeadingTextColor;

			formHeadingStyle = {
				color: formHeadingTextColor
			}

			var formDescriptionStyle = attributes.formDescriptionStyle;
			var formDescriptionTextColor = attributes.formDescriptionTextColor;

			formDescriptionStyle = {
				color: formDescriptionTextColor
			}

			// button background, text color and style

		    var btnStyle = attributes.formBtnStyle;
		    var btnBg = attributes.formBtnBgColor;
		    var btnTextColor = attributes.formBtnTextColor;

		    btnStyle = {
		        backgroundColor: btnBg,
		        color: btnTextColor,
		        padding: '15px',
		        border: '0',
		        cursor: 'pointer'
		    };

		    // style

			var formStyle = attributes.formStyle;
		    var formBgColor = attributes.formBgColor;
		    var formBgImg = attributes.formBgImg;

		    if( typeof formBgImg === 'undefined' )
		    {
		    	formBgImg = '';
		    }

		    formStyle = {
		        backgroundColor: formBgColor,
		        backgroundImage: 'url(' + formBgImg + ')',
		        backgroundSize: 'cover',
		        backgroundPosition: 'center',
		        backgroundRepeat: 'no-repeat'
		    };

		    var inputStyle = {
		    	padding: '12px'
		    };

			return(
				el(
					'div', { className: 'gb-sf4wp-gutenberg-email-optin-form-wrapper', style: formStyle },

					el( RichText.Content, { tagName: 'h2', value: formHeading, style: formHeadingStyle }, formHeading ),

					el( RichText.Content, { tagName: 'p', value: formDescription, style: formDescriptionStyle }, formDescription ),

					el( 'div', { className: 'gb-sf4wp-gutenberg-email-optin-error-msg' }, '' ),

					el( 'div', { className: 'gb-sf4wp-gutenberg-email-optin-success-msg' }, '' ),

					el( 'div', { className: 'components-base-control__field' },
						el( 'input', { type: 'text', placeholder: 'First name', className: 'components-text-control__input gb-sf4wp-gutenberg-email-optin-first-name', style: inputStyle })
					),

					el( 'div', { className: 'components-base-control__field' },
						el( 'input', { type: 'text', placeholder: 'Last name', className: 'components-text-control__input gb-sf4wp-gutenberg-email-optin-last-name', style: inputStyle })
					),

					el( 'div', { className: 'components-base-control__field' },
						el( 'input', { type: 'email', placeholder: 'Email', className: 'components-text-control__input gb-sf4wp-gutenberg-email-optin-email-address', style: inputStyle })
					),

					el( 'div', { className: 'components-base-control__field' },
						el( 'input', { type: 'submit', style: btnStyle, value: attributes.formBtnText, className: 'gb-sf4wp-gutenberg-email-optin-submit' })
					),

					el( 'div', { className: 'components-base-control__field' },
						el( 'input', { type: 'hidden', className: 'components-text-control__input gb-sf4wp-gutenberg-email-optin-list', value: formList })
					)
				)
			);
		}
	});
}(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor,
	window.wp.components
));

/**
 * Get lists for the dropdown in Gutenberg Editor
 * 
 * @since 1.1.0
 */

function gb_sf4wp_gutenberg_email_optin_get_lists()
{
	var url = sf4wp_gutenberg.url + '?action=sf4wp_gutenberg_get_lists';

    const getResults = fetch(
        url, 
        { method: 'GET' }
    ).then( 
    	res => res.json()
    ).then( 
    	(result) => {
            return result;
        }
    ).catch( 
    	(err) => {
            console.log( err );
        }
    );
    
    return getResults;
}
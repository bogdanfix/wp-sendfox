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
    var PanelBody = component.PanelBody;
    var TextControl = component.TextControl;
    var NumberControl = component.__experimentalNumberControl;
    var TextareaControl = component.TextareaControl;
    var SelectControl = component.SelectControl;
    var ColorPicker = component.ColorPicker;
    var MediaUpload = blockEditor.MediaUpload;
    var Button = component.Button;
    var useBlockProps = blockEditor.useBlockProps;

    // styles

    const labelStyles = {
    	display: 'block',
    	marginTop: '10px'
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
				source: 'html',
				selector: 'h2',
	           	default: sf4wp_gutenberg.block_default_heading
			},
			formDescription: {
				type: 'string',
				source: 'html',
				selector: 'p',
			   	default: sf4wp_gutenberg.block_default_content
			},
			formHeadingTextAlign: {
				type: 'string',
				default: 'left'
			},
			formDescriptionTextAlign: {
				type: 'string',
				default: 'left'
			},
	        formBtnText: {
	        	type: 'string',
	        	default: sf4wp_gutenberg.block_default_button_text
	        },
	        formBtnBgColor: {
	        	type: 'string',
	            default: '#005C7A'
	        },
	        formBtnTextColor:{
	            type: 'string',
	            default: '#FFFFFF'
	        },
	        formBtnBorderStyle: {
	        	type: 'string',
	        	default: 'hidden'
	        },
	        formBtnBorderWidth: {
	        	type: 'number',
	        	default: 1
	        },
	        formBtnBorderColor: {
	        	type: 'string',
	        	default: '#000'
	        },
	        formBtnBorderRadius: {
	        	type: 'number',
	        	default: 0
	        },
	        formMaxWidth: {
	        	type: 'number',
	        	default: ''
	        },
	        formBorderStyle: {
	        	type: 'string',
	        	default: 'hidden'
	        },
	        formBorderWidth: {
	        	type: 'number',
	        	default: 1
	        },
	        formBorderColor: {
	        	type: 'string',
	        	default: '#000'
	        },
	        formBorderRadius: {
	        	type: 'number',
	        	default: 0
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

		    var blockProps = wp.blockEditor.useBlockProps();

		    const onChangeList = ( list ) => {

		    	setAttributes( { formList: list } );

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

		    // button background, text color and style

		    var btnStyle = attributes.formBtnStyle;
		    var btnBg = attributes.formBtnBgColor;
		    var btnTextColor = attributes.formBtnTextColor;

		    btnStyle = {
		        backgroundColor: btnBg,
		        color: btnTextColor,
		        maxWidth: '100%',
		        minWidth: '100%',
		        height: '45px',
				borderStyle: attributes.formBtnBorderStyle,
				borderWidth: attributes.formBtnBorderWidth + 'px',
				borderColor: attributes.formBtnBorderColor,
				borderRadius: attributes.formBtnBorderRadius + 'px'
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
		        backgroundRepeat: 'no-repeat',
				borderStyle: attributes.formBorderStyle,
				borderWidth: attributes.formBorderWidth + 'px',
				borderColor: attributes.formBorderColor,
				maxWidth: attributes.formMaxWidth ? attributes.formMaxWidth + 'px' : '',
				borderRadius: attributes.formBorderRadius + 'px'
		    };

		    var inputStyle = {
		    	padding: '12px'
		    };

		    var removeBgImgBtnVisibility = 'is-button is-destructive';

		    removeBgImgBtnVisibility = formBgImg ? 
		    	removeBgImgBtnVisibility + ' gb-sf4wp-gutenberg-visible' : 
		    	removeBgImgBtnVisibility + ' gb-sf4wp-gutenberg-invisible';

			return(
				el( Fragment, {},

					el( InspectorControls, {},

						el( PanelBody, { title: sf4wp_gutenberg.label_select_list }, 

							el( SelectControl, { options: optionsArray, value: attributes.formList, onChange: onChangeList, className: 'gb-sf4wp-gutenberg-email-optin-form-controls-lists' }),
						),

						el( PanelBody, { title: sf4wp_gutenberg.panel_form_settings, initialOpen: false },

							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_heading_align ),

							el( SelectControl, { options: [
									{ label: 'left', value: 'left' },
									{ label: 'right', value: 'right' },
									{ label: 'center', value: 'center' },
									{ label: 'justify', value: 'justify' },
								],
								value: attributes.formHeadingTextAlign, onChange: function( text_align ){
									setAttributes( { formHeadingTextAlign: text_align } );
								}
							}),

							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_subheading_align ),

							el( SelectControl, { options: [
									{ label: 'left', value: 'left' },
									{ label: 'right', value: 'right' },
									{ label: 'center', value: 'center' },
									{ label: 'justify', value: 'justify' },
								],
								value: attributes.formDescriptionTextAlign, onChange: function( text_align ){
									setAttributes( { formDescriptionTextAlign: text_align } );
								}
							}),

							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_form_width ),

							el( 'label', { 
									style: { display: 'block', color: '#777777', fontSize: '12px', lineHeight: 1.2 } 
								}, 
								sf4wp_gutenberg.label_form_width_hint
							),

							el( NumberControl, { placeholder: '100%', onChange: function( value ){ setAttributes({ formMaxWidth: value }) }, value: attributes.formMaxWidth }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_form_border_style ),

							el( SelectControl, { options: [
									{ label: 'none', value: 'none' },
									{ label: 'solid', value: 'solid' },
									{ label: 'dotted', value: 'dotted' },
									{ label: 'dashed', value: 'dashed' },
									{ label: 'double', value: 'double' },
								], 
								value: attributes.formBorderStyle, onChange: function( value ){
				                	setAttributes( { formBorderStyle: value } );
								}
							}),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_form_border_width ),

							el( NumberControl, { onChange: function( value ){ setAttributes({ formBorderWidth: value })}, value: attributes.formBorderWidth }),
						
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_form_border_radius ),

							el( NumberControl, { onChange: function( value ){ setAttributes({ formBorderRadius: value })}, value: attributes.formBorderRadius }),

							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_form_border_color ),

							el( ColorPicker, { onChange: function( value ){ setAttributes({ formBorderColor: value })}}),

							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_form_bg_color ),
							
							el( ColorPicker, { onChange: onChangeFormBackgroundColor, enableAlpha: true }),
							
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

								el( Button, { onClick: onClickRemoveBgImg, className: removeBgImgBtnVisibility }, sf4wp_gutenberg.button_remove_image )

							)
						),

						el( PanelBody, { title: sf4wp_gutenberg.panel_button_settings, initialOpen: false },
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_button_border_style ),

							el( SelectControl, { options: [
									{ label: 'none', value: 'none' },
									{ label: 'solid', value: 'solid' },
									{ label: 'dotted', value: 'dotted' },
									{ label: 'dashed', value: 'dashed' },
									{ label: 'double', value: 'double' },
								], 
								value: attributes.formBtnBorderStyle, onChange: function( value ){
									setAttributes( { formBtnBorderStyle: value } );
								}
							}),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_button_border_width ),

							el( NumberControl, { onChange: function( value ){ setAttributes({ formBtnBorderWidth: value })}, value: attributes.formBtnBorderWidth }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_button_border_radius ),

							el( NumberControl, { onChange: function( value ){ setAttributes({ formBtnBorderRadius: value })}, value: attributes.formBtnBorderRadius }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_button_border_color ),

							el( ColorPicker, { onChange: function( value ){ setAttributes({ formBtnBorderColor: value })}}),

							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_button_label ),
							
							el( TextControl, { placeholder: sf4wp_gutenberg.placeholder_button_label, onChange: onChangeButtonText, value: attributes.formBtnText }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_button_label_color ),
							
							el( ColorPicker, { onChange: onChangeButtonTextColor }),
							
							el( 'label', { style: labelStyles }, sf4wp_gutenberg.label_button_bg_color ),
							
							el( ColorPicker, { onChange: onChangeButtonBackgroundColor }),
						)
					),

					el( 'div', { className: 'gb-sf4wp-gutenberg-email-optin-form-wrapper', style: formStyle },

						el( RichText, Object.assign( blockProps, {
							style: {
								'text-align': attributes.formHeadingTextAlign
							},
							tagName: 'h2',
							value: attributes.formHeading,
							onChange: function( content ) {
								setAttributes( { formHeading: content } );
							},
							placeholder: sf4wp_gutenberg.block_default_heading,
						})),

						el( RichText, Object.assign( blockProps, {
							style: {
								'text-align': attributes.formDescriptionTextAlign
							},
							tagName: 'p',
							value: attributes.formDescription,
							onChange: function( content ) {
								setAttributes( { formDescription: content } );
							},
							placeholder: sf4wp_gutenberg.block_default_content,
						})),

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

			var formList = attributes.formList;

			// button background, text color and style

		    var btnStyle = attributes.formBtnStyle;
		    var btnBg = attributes.formBtnBgColor;
		    var btnTextColor = attributes.formBtnTextColor;

		    btnStyle = {
		        backgroundColor: btnBg,
		        color: btnTextColor,
		        padding: '15px',
		        border: '0',
		        cursor: 'pointer',
				borderStyle: attributes.formBtnBorderStyle,
				borderWidth: attributes.formBtnBorderWidth + 'px',
				borderColor: attributes.formBtnBorderColor,
				borderRadius: attributes.formBtnBorderRadius + 'px'
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
		        backgroundRepeat: 'no-repeat',
				borderStyle: attributes.formBorderStyle,
				borderWidth: attributes.formBorderWidth + 'px',
				borderColor: attributes.formBorderColor,
				maxWidth: attributes.formMaxWidth ? attributes.formMaxWidth + 'px' : '',
				borderRadius: attributes.formBorderRadius + 'px'
		    };

		    var inputStyle = {
		    	padding: '12px'
		    };

			return(
				el(
					'div', { className: 'gb-sf4wp-gutenberg-email-optin-form-wrapper', style: formStyle },

					el( wp.blockEditor.RichText.Content, Object.assign( blockProps, {
						style: {
							'text-align': attributes.formHeadingTextAlign
						},
						tagName: 'h2', value: attributes.formHeading
					})),

					el( wp.blockEditor.RichText.Content, Object.assign( blockProps, {
						style: {
							'text-align': attributes.formDescriptionTextAlign
						},
						tagName: 'p', value: attributes.formDescription
					})),

					el( 'div', { className: 'gb-sf4wp-gutenberg-email-optin-error-msg' }, '' ),

					el( 'div', { className: 'gb-sf4wp-gutenberg-email-optin-success-msg' }, '' ),

					el( 'div', { className: 'components-base-control__field' },
						el( 'input', { type: 'text', placeholder: sf4wp_gutenberg.placeholder_first_name, className: 'components-text-control__input gb-sf4wp-gutenberg-email-optin-first-name', style: inputStyle })
					),

					el( 'div', { className: 'components-base-control__field' },
						el( 'input', { type: 'text', placeholder: sf4wp_gutenberg.placeholder_last_name, className: 'components-text-control__input gb-sf4wp-gutenberg-email-optin-last-name', style: inputStyle })
					),

					el( 'div', { className: 'components-base-control__field' },
						el( 'input', { type: 'email', placeholder: sf4wp_gutenberg.placeholder_email, className: 'components-text-control__input gb-sf4wp-gutenberg-email-optin-email-address', style: inputStyle })
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
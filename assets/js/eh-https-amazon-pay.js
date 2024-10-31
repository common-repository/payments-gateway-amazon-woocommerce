jQuery( function( $ ) {
    var amazonButton,authRequest=false;
    function amazon_button_load()
    {
        if(amazonButton)
            return;
        if ( 0 !== $( '#eh_pay_with_amazon' ).length && (amazon_https_js.current_page === 'cart' || amazon_https_js.current_page === 'checkout'))
        {
            var amazonButtonArgs = {
                    type : amazon_https_js.text,
                    color: amazon_https_js.color,
                    size : amazon_https_js.size,
                    language : amazon_https_js.language,
                    authorization: function() {
                            loginOptions = {
                                scope: 'payments:widget payments:shipping_address payments:billing_address',popup:true
                            };
                            authRequest = amazon.Login.authorize( loginOptions, amazon_https_js.redirect );
                    },
                    onError      : function( error ) {
                        console.log('Code : ' + error.getErrorCode() + '. Message : ' + error.getErrorMessage());
                    }
            };
            OffAmazonPayments.Button( 'eh_pay_with_amazon', amazon_https_js.id, amazonButtonArgs );
            amazonButton = true;
        }
    }
    amazon_button_load();
    if ( $( '#amazon_address_widget_section' ).length > 0 )
    {
        add_address_widget();
    }
    if ( $( '#amazon_payment_widget_section' ).length > 0 )
    {
        add_payment_widget();
    }
    function add_address_widget()
    {
        new OffAmazonPayments.Widgets.AddressBook({
            sellerId: amazon_https_js.id,
            onOrderReferenceCreate: function (orderReference) {
               orderReferenceId = orderReference.getAmazonOrderReferenceId();
               $( 'input.eh-amazon-ref-id' ).remove();
		var ref_html = '<input class="eh-amazon-ref-id" type="hidden" name="amazon_reference_id" value="' + orderReferenceId + '" />';
		$( 'form.checkout' ).append( ref_html );
		$( 'form#order_review' ).append( ref_html );
            },
            onAddressSelect: function (e) {
                $( 'body' ).trigger( 'update_checkout' );
            },
            design: {
                designMode: 'responsive'
            },
            onError: function (error) {
                console.log('Code : ' + error.getErrorCode() + '. Message : ' + error.getErrorMessage());
            }
        }).bind("amazon_address_widget_section");
    }
    function add_payment_widget()
    {
        if ( $( '#amazon_address_widget_section' ).length > 0 )
        {
            new OffAmazonPayments.Widgets.Wallet({
                sellerId: amazon_https_js.id,
                design: {
                    designMode: 'responsive'
                },
                onError: function (error) {
                    console.log('Code : ' + error.getErrorCode() + '. Message : ' + error.getErrorMessage());
                }
            }).bind("amazon_payment_widget_section");
        }
        else
        {
            new OffAmazonPayments.Widgets.Wallet({
                sellerId: amazon_https_js.id,
                onOrderReferenceCreate: function (orderReference) {
                   orderReferenceId = orderReference.getAmazonOrderReferenceId();
                   $( 'input.eh-amazon-ref-id' ).remove();
                    var ref_html = '<input class="eh-amazon-ref-id" type="hidden" name="amazon_reference_id" value="' + orderReferenceId + '" />';
                    $( 'form.checkout' ).append( ref_html );
                    $( 'form#order_review' ).append( ref_html );
                },
                design: {
                    designMode: 'responsive'
                },
                onError: function (error) {
                    console.log('Code : ' + error.getErrorCode() + '. Message : ' + error.getErrorMessage());
                }
            }).bind("amazon_payment_widget_section");
        }
        
    }
    $( 'form.checkout' ).on( 'checkout_place_order', function() {

		var fieldSelectors = [
			':input[name=billing_email]',
			':input[name=billing_first_name]',
			':input[name=billing_last_name]',
			':input[name=account_password]'
		].join( ',' );

		$( this ).find( fieldSelectors ).each( function() {
			var $input = $( this );
			if ( '' === $input.val() && $input.is(':hidden') ) {
				$input.attr( 'disabled', 'disabled' );
			}
		} );

	} );
});
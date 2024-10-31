jQuery( function( $ ) {
    var amazonButton=false;
    function amazon_button_load()
    {
        if(amazonButton)
            return;
        if ( 0 !== $( '#eh_pay_with_amazon' ).length && (amazon_http_js.current_page === 'cart' || amazon_http_js.current_page === 'checkout'))
        {
            new OffAmazonPayments.Widgets.Button( {
                        sellerId            : amazon_http_js.id,
			useAmazonAddressBook: ((amazon_http_js.use_address === 'yes')? true:false),
			onSignIn            : function ( orderReference ) {
				amazonOrderReferenceId = orderReference.getAmazonOrderReferenceId();
				window.location = amazon_http_js.redirect + '&amazon_reference_id=' + amazonOrderReferenceId;
			}
		} ).bind( 'eh_pay_with_amazon' ); 
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
    $( 'form.checkout' ).on( 'checkout_place_order', function() {

        if ( ! $( ':checkbox[name=createaccount]' ).is( ':checked' ) ) {
                return;
        }

        $( this ).find( ':input[name=billing_email],:input[name=account_password]' ).each( function() {
                var $input = $( this );
                if ( '' === $input.val() && $input.is(':hidden') ) {
                        $input.attr( 'disabled', 'disabled' );
                }
        } );

    } );
    function add_address_widget()
    {
        new OffAmazonPayments.Widgets.AddressBook( {
			sellerId              : amazon_http_js.id,
			amazonOrderReferenceId: amazon_http_js.ref_id,
			onAddressSelect       : function ( ) {
				$( 'body' ).trigger( 'update_checkout' );
			},
			design                : {
				designMode: 'responsive'
			}
		} ).bind( 'amazon_address_widget_section' );
    }
    function add_payment_widget()
    {
        new OffAmazonPayments.Widgets.Wallet( {
			sellerId              : amazon_http_js.id,
			amazonOrderReferenceId: amazon_http_js.ref_id,
			design                : {
				designMode: 'responsive'
			}
		} ).bind( 'amazon_payment_widget_section' );
    }
});
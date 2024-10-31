<?php
if (!defined('ABSPATH')) {
    exit;
}

class Eh_Amazon_Pay_Payment extends WC_Payment_Gateway {

    protected $request;
    protected $acc_token;
    protected $ref_id;
    protected $eh_amazon;

    public function __construct() {
        $this->id = 'eh_amazon_pay';
        $this->method_title = __('Amazon Payments', 'eh-amazon-payments');
        $this->method_description = sprintf(__("Use Amazon Payments to Checkout faster.", 'eh-amazon-payments'));
        $this->has_fields = true;
        $this->supports = array(
            'products'
        );
        $this->init_form_fields();
        $this->init_settings();
        $this->enabled = $this->get_option('enabled');
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->ssl_mode = $this->get_option('ssl_mode');
        $this->environment = $this->get_option('environment');
        $this->merchant_id = $this->get_option('merchant_id');
        $this->access_keys = $this->get_option('access_keys');
        $this->secret_keys = $this->get_option('secret_keys');
        $this->client_id = $this->get_option('client_id');
        $this->client_secret = $this->get_option('client_secret');
        $this->payment_action = $this->get_option('payment_action');
        $this->pay_with_amazon_description = $this->get_option('pay_with_amazon_description');
        $this->amazon_address_on_checkout_page ='bill_ship';// $this->get_option('amazon_address_on_checkout_page');
        
        $this->checkout_display = $this->get_option('amazon_on_checkout_page');
        $this->button_text = $this->get_option('button_text');
        $this->button_color = $this->get_option('button_color');
        $this->button_size = $this->get_option('button_size');
        //$this->amazon_locale = $this->get_option('amazon_locale');
       // $this->amazon_language = $this->get_option('amazon_language');
        $this->banner_display = 'light_logo_check';
        $this->policy_notes = $this->get_option('policy_notes');
        $this->eh_amazon = Eh_Amazon_Static::get_enable_amazon();
        $this->acc_token = Eh_Amazon_Static::get_acc_token($this->ssl_mode);
        $this->ref_id = Eh_Amazon_Static::get_ref_id();
        if (is_admin()) {
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }
        if ($this->enabled === 'yes' && $this->checkout_display) {
            add_action('wp_enqueue_scripts', array($this, 'payment_scripts'),99);
        }
    }
    public function init_form_fields() {
        $this->form_fields = include( 'eh-amazon-payments-settings-page.php' );
    }
    
    function is_available() {
        if ($this->enabled === 'yes') {
            if (((!empty($this->ref_id) && $this->ssl_mode === 'http') || ((!empty($this->acc_token) && $this->ssl_mode === 'https'))) && $this->eh_amazon) {
                return true;
            }
        }
        return false;
    }

    public function admin_options() {
        include_once("market.php");
        $plugin_name = 'amazonpaymentgateway';
        $ssl = is_ssl();
        $site_url = '';
        $site_alert = '';
        if ($ssl) {
            $site_url = get_site_url();
            $site_alert = "onclick=\"window.prompt(\'Copy to Clipboard: Ctrl+C, Enter\',jQuery( \'#amazon_allowed_url\' ).html());\"";
        } else {
            $site_url = 'Please Enable HTTPS for Showing SSL Site URL';
        }
        $locale = (in_array(get_locale(), array('en_US', 'en_GB', 'de_DE', 'fr_FR', 'it_IT', 'es_ES'))) ? true : false;
        if ($locale) {
            $locale_msg = __('Your Store Language supports Amazon Supported Languages', 'eh-amazon-payments');
        } else {
            $locale_msg = __('Your Store Language does\'nt supports Amazon Supported Languages. Amazon Language will be English ( US )', 'eh-amazon-payments');
        }
        wc_enqueue_js("
            jQuery( function( $ ) {
                $('.description').css({'font-style':'normal'});
                var eh_amazon_pay_client            = jQuery ( '#woocommerce_eh_amazon_pay_client_id, #woocommerce_eh_amazon_pay_client_secret').closest( 'tr' );
                var eh_amazon_pay_style             = jQuery ( '#woocommerce_eh_amazon_pay_button_size, #woocommerce_eh_amazon_pay_button_color, #woocommerce_eh_amazon_pay_button_text, #woocommerce_eh_amazon_pay_amazon_locale, #woocommerce_eh_amazon_pay_amazon_language').closest( 'tr' );
                var eh_amazon_locale_check          = jQuery ( '#woocommerce_eh_amazon_pay_amazon_language').closest( 'tr' );
                $( '#woocommerce_eh_amazon_pay_ssl_mode' ).change(function(){
                    if('https' === $( this ).val())
                    {
                        var allow_js_url='" . $site_url . "';
                        var amazon_links='Under your <a href=\'https://sellercentral.amazon.com\' target=\'_blank\'>Login with Amazon</a> app  -> Web Settings [\" Allowed JavaScript Origins \"]<br><br>Paste this URL - <b><span id=\'amazon_allowed_url\' style=\'cursor:pointer;\' " . $site_alert . ">'+allow_js_url+'</span></b>';
                        $('.links_for_amazon').html(amazon_links);
                        $( eh_amazon_pay_client ).show();
                        $( eh_amazon_pay_style  ).show();
                        if ( $( '#woocommerce_eh_amazon_pay_amazon_locale' ).is( ':checked' ) )
                        {
                            $( eh_amazon_locale_check  ).hide();
                        }
                        else
                        {
                            $( eh_amazon_locale_check ).show();
                        }
                    }
                    else
                    {
                        $('.links_for_amazon').empty();
                        $( eh_amazon_pay_client ).hide();
                        $( eh_amazon_pay_style ).hide();
                    }
                }).change();
                $( '#woocommerce_eh_amazon_pay_amazon_locale' ).change(function(){
                    if ( $( this ).is( ':checked' ) ) {
                            $('.amazon_lanuguage_desc').html(\"" . $locale_msg . "\");
                            $( eh_amazon_locale_check  ).hide();
                    } else {
                            $('.amazon_lanuguage_desc').empty();
                            $( eh_amazon_locale_check ).show();
                        }
                }).change();
            });
        ");
        parent::admin_options();
    }

    public function payment_scripts() {
        wp_register_script('eh-amazon-script', EH_AMAZON_MAIN_URL . 'assets/js/eh-amazon-script.js');
        wp_enqueue_script('eh-amazon-script');
        if (empty($this->ref_id) && empty($this->acc_token)) {
            wp_register_style('eh-amazon-style', EH_AMAZON_MAIN_URL . 'assets/css/eh-pwa-style.css');
            wp_enqueue_style('eh-amazon-style');
            wp_enqueue_script('eh-amazon-widget', Eh_Amazon_Static::get_widget_js($this->environment, $this->ssl_mode, $this->merchant_id), array(), '1.0', true);
            if ('https' === $this->ssl_mode) {
                wp_register_script('eh-amazon-https', EH_AMAZON_MAIN_URL . 'assets/js/eh-https-amazon-pay.js');
                wp_enqueue_script('eh-amazon-https');
                wp_localize_script('eh-amazon-https', 'amazon_https_js', array
                    (
                        'text' => $this->button_text,
                        'color' => $this->button_color,
                        'size' => $this->button_size,
                       // 'language' => Eh_Amazon_Static::get_lang($this->amazon_locale, $this->amazon_language),
                        'id' => $this->merchant_id,
                        'current_page' => 'checkout',
                        'redirect' => esc_url(add_query_arg('eh_amazon_payments', 'true', wc_get_checkout_url()))
                    )
                );
            }
            else
            {
                wp_register_script('eh-amazon-http', EH_AMAZON_MAIN_URL . 'assets/js/eh-http-amazon-pay.js');
                wp_enqueue_script('eh-amazon-http');
                wp_localize_script('eh-amazon-http', 'amazon_http_js', array
                    (
                        'id' => $this->merchant_id,
                        'current_page' => 'checkout',
                        'use_address'   => ((isset($this->amazon_address_on_checkout_page) && $this->amazon_address_on_checkout_page !='')?"yes":'no'),
                        'redirect' => esc_url(add_query_arg('eh_amazon_payments', 'true', wc_get_checkout_url()))
                    )
                );
            }
        } else {
            wp_register_style('eh-amazon-style', EH_AMAZON_MAIN_URL . 'assets/css/eh-pwa-style.css');
            wp_enqueue_style('eh-amazon-style');
            wp_enqueue_script('eh-amazon-widget', Eh_Amazon_Static::get_widget_js($this->environment, $this->ssl_mode, $this->merchant_id), array(), '1.0', true);
            if ('https' === $this->ssl_mode) {
                wp_register_script('eh-amazon-https', EH_AMAZON_MAIN_URL . 'assets/js/eh-https-amazon-pay.js');
                wp_enqueue_script('eh-amazon-https');
                wp_localize_script('eh-amazon-https', 'amazon_https_js', array
                    (
                        'id' => $this->merchant_id,
                        'current_page' => 'checkout'
                    )
                );
            }
            else
            {
                wp_register_script('eh-amazon-http', EH_AMAZON_MAIN_URL . 'assets/js/eh-http-amazon-pay.js');
                wp_enqueue_script('eh-amazon-http');
                wp_localize_script('eh-amazon-http', 'amazon_http_js', array
                    (
                        'id' => $this->merchant_id,
                        'current_page' => 'checkout',
                        'ref_id'    => $this->ref_id
                    )
                );
            }
        }
    }

    public function process_payment($order_id) {
        $wc_order=  wc_get_order($order_id);
        $ref_id=  isset($_REQUEST['amazon_reference_id'])?sanitize_text_field($_REQUEST['amazon_reference_id']):'';
     
        try {
                if($ref_id === '')
                {
                    wc_add_notice(__('Select the Amazon Payments.','eh-amazon-payments'), 'error');
                    return;
                }
                else
                {
                    $args=array
                            (
                                'amazon_order_reference_id' => $ref_id,
                                'amount'                    => (WC()->version < '2.7.0') ? $wc_order->order_total : $wc_order->get_total(),
                                'currency_code'             => (WC()->version < '2.7.0') ? $wc_order->order_currency : $wc_order->get_currency(),
                                'seller_order_id'           => $order_id,
                                'seller_note'               => $this->policy_notes,
                                'store_name'                => get_bloginfo('name'),
                                'custom_information'        => (WC()->version < '2.7.0') ? $wc_order->billing_email : $wc_order->get_billing_email()
                            );
                    if( $this->ssl_mode === 'https' )
                    {
                        $args['mws_auth_token'] = $this->acc_token;
                    }

                    $amazon_req = new Eh_Amazon_Request_Response();
                    $amazon_req->request_amazon('SetOrderReferenceDetails',$args);
                    $amazon_req->request_amazon('ConfirmOrderReference',array('amazon_order_reference_id'=>$ref_id));
                   
                    if( isset($this->amazon_address_on_checkout_page) ) {
                        $data_object = $amazon_req->request_amazon('GetOrderReferenceDetails',array('amazon_order_reference_id'=>$ref_id));
                        if(isset( $data_object->GetOrderReferenceDetailsResult->OrderReferenceDetails )){
                            $data = $data_object->GetOrderReferenceDetailsResult->OrderReferenceDetails;
                            $address= (array) $data->Destination->PhysicalDestination;
                            $formatted_address=$this->get_address($address);
                            if($this->amazon_address_on_checkout_page == 'yes' && isset($_POST['ship_to_different_address']) && $_POST['ship_to_different_address'] == 1)
                            {
                                $wc_order->set_address($formatted_address, 'shipping');
                            }
                            elseif($this->amazon_address_on_checkout_page === 'bill')
                            {                       
                                $wc_order->set_address($formatted_address, 'billing');
                            }
                            elseif($this->amazon_address_on_checkout_page === 'bill_ship')
                            {
                                $wc_order->set_address($formatted_address, 'billing');
                                $wc_order->set_address($formatted_address, 'shipping');
                            }
                        }
                        else {
                            $address = $wc_order->get_address('billing');
                            $wc_order->set_address($address, 'shipping');
                        }
                    }
                    else {
                        $address = $wc_order->get_address('billing');
                        $wc_order->set_address($address, 'shipping');
                    }
                
                    update_post_meta( $order_id, 'eh_amazon_ref_id', $ref_id );
                    update_post_meta( $order_id, '_transaction_id', $ref_id );
                    if($this->payment_action === 'authorize')
                    {
                        $args=array
                                (
                                    'amazon_order_reference_id' => $ref_id,
                                    'authorization_amount'      => (WC()->version < '2.7.0') ? $wc_order->order_total : $wc_order->get_total(),
                                    'currency_code'             => (WC()->version < '2.7.0') ? $wc_order->order_currency : $wc_order->get_currency(),
                                    'capture_now'               => false,
                                    'authorization_reference_id'=>'Auth-' . $order_id . '-' . current_time( 'timestamp', true ),
                                );
                        $data_object=$amazon_req->request_amazon('Authorize', $args);
                        if($data_object->ResponseStatus === '200')
                        {
                            $wc_order->update_status('on-hold');
                            $wc_order->add_order_note(__( "Authorize for Amazon Payment Success. Capture the Payment Before 7 days.",'eh-amazon-payments'));
                            if((WC()->version < '2.7.0'))
                            {
                                $wc_order->reduce_order_stock();
                            }
                            else
                            {
                                wc_reduce_stock_levels($order_id);
                            }
                            update_post_meta( $order_id, 'eh_amazon_auth_id', $data_object->AuthorizeResult->AuthorizationDetails->AmazonAuthorizationId );
                            update_post_meta( $order_id, 'eh_amazon_status', 'Authorization' );
                            update_post_meta( $order_id, 'eh_amazon_environment', $this->environment );
                        }
                        else
                        {
                            $wc_order->update_status('failed');
                            $wc_order->add_order_note(__( "Authorize for Amazon Payment Failed.",'eh-amazon-payments'));
                        }
                    }
                    else
                    {
                        $args=array
                                (
                                    'amazon_order_reference_id' => $ref_id,
                                    'authorization_amount'      => (WC()->version < '2.7.0') ? $wc_order->order_total : $wc_order->get_total(),
                                    'currency_code'             => (WC()->version < '2.7.0') ? $wc_order->order_currency : $wc_order->get_currency(),
                                    'capture_now'               => true,
                                    'authorization_reference_id'=>'Cap-' . $order_id . '-' . current_time( 'timestamp', true ),
                                );
                        $data_object=$amazon_req->request_amazon('Authorize', $args);
                        if($data_object->ResponseStatus === '200')
                        {
                            $wc_order->payment_complete();
                            $wc_order->add_order_note(__( "Capture for Amazon Payment Success.",'eh-amazon-payments'));
                            update_post_meta( $order_id, 'eh_amazon_auth_id', $data_object->AuthorizeResult->AuthorizationDetails->AmazonAuthorizationId );
                            update_post_meta( $order_id, 'eh_amazon_status', 'Capture' );
                            update_post_meta( $order_id, 'eh_amazon_environment', $this->environment );
                            $amazon_req->request_amazon('closeOrderReference', array('amazon_order_reference_id'=>$ref_id));
                        }
                        else
                        {
                            $wc_order->update_status('failed');
                            $wc_order->add_order_note(__( "Capture for Amazon Payment Failed.",'eh-amazon-payments'));
                        }
                    }
                    WC()->cart->empty_cart();

                    return array(
                            'result' 	=> 'success',
                            'redirect'	=> $this->get_return_url( $wc_order )
                    );
                }
        } catch (Exception $exc) {
            wc_add_notice( __( 'Error:', 'eh-amazon-payments' ) . ' ' . $e->getMessage(), 'error' );
            return;
        }
    }
    public function get_address($address)
    {
        $format = array();
        $name = explode( ' ', (string) $address['Name'] );
        $format['first_name']   = ! empty( $name[0] ) ? $name[0] : '';
        $format['last_name']    = ! empty( $name[1] ) ? $name[1] : '';
        $format['company']      = '';
        $format['address_1']    = ! empty( $address['AddressLine1'] ) ? $address['AddressLine1'] : '';
        $format['address_2']    = ! empty( $address['AddressLine2'] ) ? $address['AddressLine2'] : '';
        $format['city']         = ! empty( $address['City'] ) ? $address['City'] : '';
        $format['state']        = ! empty( $address['StateOrRegion'] ) ? $address['StateOrRegion'] : '';
        $format['postcode']     = ! empty( $address['PostalCode'] ) ? $address['PostalCode'] : '';
        $format['country']      = ! empty( $address['CountryCode'] ) ? $address['CountryCode'] : '';
        return $format;
    }
    public function file_size($link) {
        $bytes = is_file($link) ? filesize($link) : 0;
        $result = 0;
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

        foreach ($arBytes as $arItem) {
            if ($bytes >= $arItem["VALUE"]) {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", ".", strval(round($result, 2))) . " " . $arItem["UNIT"];
                break;
            }
        }
        return $result;
    }

    public function get_icon() {
        $image_path = EH_AMAZON_MAIN_URL."assets/img/amazon.png";
        $icon = "<img src=\"$image_path\"/>";
        return $icon;
    }

}
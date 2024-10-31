<?php
if (!defined('ABSPATH')) {
    exit;
}
class Eh_Amazon_Request_Response
{
    protected $keys;
    public function __construct() {
        $settings =  get_option('woocommerce_eh_amazon_pay_settings');
        $this->keys = array
                ( 
                    'merchant_id'   => $settings['merchant_id'],
                    'access_key'    => $settings['access_keys'],
                    'secret_key'    => $settings['secret_keys'],
                    'sandbox'       => ($settings['environment'] === 'sandbox')?true:false,
                    'region'        =>  Eh_Amazon_Static::get_base_location()
                );
        if( $settings['ssl_mode'] === 'https' )
        {
            $this->keys['client_id'] = $settings['client_id'];
        }
        $this->request_amazon("Request", $this->keys);
    }
    public function request_amazon($method,$args)
    {        
        $client=new PayWithAmazon\Client($this->keys);
        $response = '';
        switch ($method) {
            case 'Request':
                    Eh_Amazon_Log::log_update($args,"Request");
                break;
            case 'GetOrderReferenceDetails':
                    $data = $client->getOrderReferenceDetails($args);
                    $response = json_decode($data->toJson());
                    Eh_Amazon_Log::log_update($response,"Getting Order Details - ".$args['amazon_order_reference_id']);
                    return $response;
                break;
            case 'SetOrderReferenceDetails':
                    $data = $client->setOrderReferenceDetails($args);
                    $response = json_decode($data->toJson());
                    Eh_Amazon_Log::log_update($response,"Setting Order Details - ".$args['amazon_order_reference_id']);
                break;
            case 'ConfirmOrderReference':
                    $client->confirmOrderReference($args);
                break;
            case 'Authorize':
                    $data=$client->authorize($args);
                    $response = json_decode($data->toJson());
                    Eh_Amazon_Log::log_update($response,"Processing Authorize Payment - ".$args['amazon_order_reference_id']);
                    return $response;
                break;

            case 'getAuthorizationDetails':
                    $data=$client->getAuthorizationDetails($args);
                    $response = json_decode($data->toJson());
                    Eh_Amazon_Log::log_update($response,"Getting Authorization Details - ".$args['amazon_authorization_id']);
                    return $response;
                break;
            case 'closeOrderReference':
                    $data=$client->closeOrderReference($args);
                 break;
            default:
                break;
        }
        
    }
}
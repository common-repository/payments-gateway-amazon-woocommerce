<?php
if (!defined('ABSPATH')) {
    exit;
}

class Eh_Amazon_Pay_Hooks {

    protected $eh_amazon_pay_options;
    protected $id;
    protected $token;
    protected $eh_amazon;
    

    public function __construct() {
        $this->eh_amazon_pay_options = get_option('woocommerce_eh_amazon_pay_settings');
        if ($this->eh_amazon_pay_options['enabled'] === 'yes') {
            $this->eh_amazon = Eh_Amazon_Static::get_enable_amazon();
            $this->token = Eh_Amazon_Static::get_acc_token($this->eh_amazon_pay_options['ssl_mode']);
            $this->id = Eh_Amazon_Static::get_ref_id();
            $priority = 20;
            
            
            if(isset($this->eh_amazon_pay_options['position_button']))
            {
                $priority = ($this->eh_amazon_pay_options['position_button'] === 'below') ? 20 : 0;
            }
            
           // add_action('woocommerce_proceed_to_checkout', array($this, 'eh_pay_with_amazon_hook'), $priority);
            add_action('wp_head', array($this, 'login_app_init'));
            add_action('wp', array($this, 'unset_amazon'));
           // add_action('wp_enqueue_scripts', array($this, 'eh_payment_scripts'));
//            add_action('woocommerce_before_cart', array($this, 'banner_display'));
//            add_action('woocommerce_before_checkout_form', array($this, 'banner_display'));
            
            $this->eh_amazon_pay_options['position_button_checkout']='above';
            if(isset($this->eh_amazon_pay_options['position_button_checkout']))
            {
                if($this->eh_amazon_pay_options['position_button_checkout'] === 'above')
                {
                    add_action('woocommerce_before_checkout_form', array($this, 'add_amazon_pay_button'));
                }
                if($this->eh_amazon_pay_options['position_button_checkout'] === 'above_rew')
                {
                    add_action('woocommerce_checkout_after_customer_details', array($this, 'add_amazon_pay_button'));
                }
                if($this->eh_amazon_pay_options['position_button_checkout'] === 'above_pay')
                {
                    add_action('woocommerce_review_order_before_payment', array($this, 'add_amazon_pay_button'));
                }
                
                if($this->eh_amazon_pay_options['position_button_checkout'] === 'below')
                {
                    add_action('woocommerce_after_checkout_form', array($this, 'add_amazon_pay_button'));
                }
                
                

            }else
            {   
                add_action('woocommerce_before_checkout_form', array($this, 'add_amazon_pay_button'));
            }
            $this->eh_amazon_pay_options['amazon_address_on_checkout_page']='bill_ship';
            $amazon_address_on_checkout_page = isset($this->eh_amazon_pay_options['amazon_address_on_checkout_page'])?$this->eh_amazon_pay_options['amazon_address_on_checkout_page']:'';
            
            if(isset($this->eh_amazon_pay_options['amazon_address_on_checkout_page']) && $this->eh_amazon_pay_options['amazon_address_on_checkout_page'] == 'yes')
            {
                add_action('woocommerce_after_checkout_shipping_form', array($this, 'add_amazon_address_widget_section'));   
            }elseif(!empty($amazon_address_on_checkout_page) && $this->eh_amazon_pay_options['amazon_address_on_checkout_page'] == 'bill' || $this->eh_amazon_pay_options['amazon_address_on_checkout_page'] == 'bill_ship' )
            {
                add_action('woocommerce_before_checkout_billing_form', array($this, 'add_amazon_address_widget_section'));  
            }

            add_action('woocommerce_review_order_before_payment', array($this, 'add_amazon_payment_widget_section'));
            add_action('woocommerce_available_payment_gateways', array($this, 'gateways_hide_on_review'));
            add_action('woocommerce_review_order_after_payment', array($this, 'add_policy_notes'));
            add_action('woocommerce_review_order_after_submit', array($this, 'add_cancel_order_elements'));
            add_action('woocommerce_checkout_update_order_review', array($this, 'update_check_info'));
            add_filter( 'woocommerce_checkout_fields' , array( $this,'custom_override_default_address_fields'),10 );
            add_action('add_meta_boxes', array($this, 'add_amazon_action_metabox'));
            
        }
    }
    function custom_override_default_address_fields( $address_fields ) {
         if ($this->eh_amazon && ($this->id !== '' || $this->token !== '' ) && isset($this->eh_amazon_pay_options['amazon_address_on_checkout_page']) && ($this->eh_amazon_pay_options['amazon_address_on_checkout_page'] == 'yes' || $this->eh_amazon_pay_options['amazon_address_on_checkout_page'] == 'bill_ship')) {
            if( $this->eh_amazon_pay_options['amazon_address_on_checkout_page'] == 'bill_ship' ) {
                unset($address_fields['billing']['billing_first_name']);
                unset($address_fields['billing']['billing_last_name']);
                unset($address_fields['billing']['billing_company']);
                unset($address_fields['billing']['billing_address_1']);
                unset($address_fields['billing']['billing_address_2']);
                unset($address_fields['billing']['billing_city']);
                unset($address_fields['billing']['billing_postcode']);
                unset($address_fields['billing']['billing_country']);
                unset($address_fields['billing']['billing_state']); 
            }
            unset($address_fields['shipping']['shipping_first_name']);
            unset($address_fields['shipping']['shipping_last_name']);
            unset($address_fields['shipping']['shipping_company']);
            unset($address_fields['shipping']['shipping_address_1']);
            unset($address_fields['shipping']['shipping_address_2']);
            unset($address_fields['shipping']['shipping_city']);
            unset($address_fields['shipping']['shipping_postcode']);
            unset($address_fields['shipping']['shipping_country']);
            unset($address_fields['shipping']['shipping_state']);
        }
        return $address_fields;
    }
    
 


    public function eh_amazon_register_styles_scripts() {
        $page = (isset($_GET['page'])) ? esc_attr($_GET['page']) : false;
        if ('eh-amazon-payments' != $page)
            return;
        wp_nonce_field('ajax-eh-pwa-nonce', '_ajax_eh_pwa_nonce');
        global $woocommerce;
        $woocommerce_version = function_exists('WC') ? WC()->version : $woocommerce->version;
        wp_enqueue_style('woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css', array(), $woocommerce_version);
        wp_register_style('eh-boot-style', EH_AMAZON_MAIN_URL . 'assets/css/boot.css');
        wp_enqueue_style('eh-boot-style');
        wp_register_style('eh-style-style', EH_AMAZON_MAIN_URL . 'assets/css/style.css');
        wp_enqueue_style('eh-style-style');
        //Date Picker Scripts and CSS
        wp_register_style('eh-daterangepicker-style', EH_AMAZON_MAIN_URL . 'assets/css/daterangepicker.css');
        wp_enqueue_style('eh-daterangepicker-style');
        wp_register_script('eh-daterangepicker-script', EH_AMAZON_MAIN_URL . 'assets/js/daterangepicker.js');
        wp_enqueue_script('eh-daterangepicker-script');

        wp_register_style('eh-datepicker-style', EH_AMAZON_MAIN_URL . 'assets/css/datepicker.css');
        wp_enqueue_style('eh-datepicker-style');
        wp_register_script('eh-datepicker-script', EH_AMAZON_MAIN_URL . 'assets/js/datepicker.js');
        wp_enqueue_script('eh-datepicker-script');


        //xchart includes
        wp_register_style('eh-xcharts.min-style', EH_AMAZON_MAIN_URL . 'assets/css/xcharts.min.css');
        wp_enqueue_style('eh-xcharts.min-style');
        wp_register_script('eh-xcharts.min', EH_AMAZON_MAIN_URL . 'assets/js/xcharts.min.js');
        wp_enqueue_script('eh-xcharts.min');
        wp_register_script('eh-sugar.min', EH_AMAZON_MAIN_URL . 'assets/js/sugar.min.js');
        wp_enqueue_script('eh-sugar.min');
        wp_register_script('eh-xhart-lib-script', '//cdnjs.cloudflare.com/ajax/libs/d3/2.10.0/d3.v2.js');
        wp_enqueue_script('eh-xhart-lib-script');

        // our chart init file        
        wp_register_script('eh-custom-chart', EH_AMAZON_MAIN_URL . 'assets/js/script.js');
        wp_enqueue_script('eh-custom-chart');
        wp_register_script('eh-custom', EH_AMAZON_MAIN_URL . 'assets/js/eh-amazon-overview.js');
        wp_enqueue_script('eh-custom');

        //sweetalert
        wp_register_style('eh-alert-style', EH_AMAZON_MAIN_URL . '/assets/css/sweetalert2.css');
        wp_enqueue_style('eh-alert-style');
        wp_register_script('eh-alert-jquery', EH_AMAZON_MAIN_URL . '/assets/js/sweetalert2.min.js');
        wp_enqueue_script('eh-alert-jquery');
    }

    public function eh_amazon_template_display() {
        include (EH_AMAZON_MAIN_PATH . "templates/template-frontend-main.php");
    }

    public function add_amazon_action_metabox() {
        global $post;
        $order = wc_get_order($post->ID);
        if ($order) {
            if (((WC()->version < '2.7.0')?$order->payment_method:$order->get_payment_method()) === 'eh_amazon_pay' && (in_array($order->get_status(), array('on-hold', 'processing', 'completed')))) {
                wp_register_style('eh-pwa-meta', EH_AMAZON_MAIN_URL . 'assets/css/eh-pwa-meta.css');
                wp_enqueue_style('eh-pwa-meta');
                wp_register_script('eh-pwa-meta', EH_AMAZON_MAIN_URL . 'assets/js/eh-pwa-meta.js');
                wp_enqueue_script('eh-pwa-meta');
                wp_register_style('eh-alert-style', EH_AMAZON_MAIN_URL . '/assets/css/sweetalert2.css');
                wp_enqueue_style('eh-alert-style');
                wp_register_script('eh-alert-jquery', EH_AMAZON_MAIN_URL . '/assets/js/sweetalert2.min.js');
                wp_enqueue_script('eh-alert-jquery');
                add_meta_box
                        (
                        'eh_amazon_action_box', __('Amazon Payments Actions', 'eh-amazon-payments'), array
                    (
                    $this,
                    'amazon_metabox_html'
                        ), 'shop_order', 'side', 'high'
                );
            }
        }
    }

    public function amazon_metabox_html() {
        global $post;
        $order = wc_get_order($post->ID);
        ?>
        <input type="hidden" value="<?php echo ((WC()->version < '2.7.0')?$order->id:$order->get_id()); ?>" id="eh_pwa_order_id">
        <div class="loader"></div>
        <table class="eh_pwa_meta_table">
            <?php
            $status = get_post_meta($post->ID, 'eh_amazon_status', true);
            if ($status === 'Authorization') {
                ?>
                <tr>
                    <td>
                        <?php _e('Capture Actions', 'eh-amazon-payments'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <select class="wc-enhanced-select" id="eh_pwa_meta_capture_select" style="width:100%">
                            <option value="full">
                                <?php _e('Full Capture', 'eh-amazon-payments'); ?>
                            </option>
                        </select>
                        <div  class="div_eh_pwa_capture_meta">
                            <span class="button button-primary" id="eh_pwa_meta_capture_button">
                                <?php _e('Capture', 'eh-amazon-payments'); ?>
                            </span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <hr>
                    </td>
                </tr>
                <?php
            } elseif ($status === 'Capture') {
                ?>
                <tr>
                    <td>
                        <?php _e('Refund Actions', 'eh-amazon-payments'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <select class="wc-enhanced-select" id="eh_pwa_meta_refund_select" style="width:100%">
                            <option value="partial">
                                <?php _e('Partial Refund', 'eh-amazon-payments'); ?>
                            </option>
                            <option value="full">
                                <?php _e('Full Refund', 'eh-amazon-payments'); ?>
                            </option>
                        </select>
                        <div  class="div_eh_pwa_refund_meta">
                            <input type="text" class="text" placeholder="Amount" id="eh_pwa_meta_refund_amount">
                            <span class="button button-primary" id="eh_pwa_meta_refund_button">
                                <?php _e('Refund', 'eh-amazon-payments'); ?>
                            </span>
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>

        </table>
        <?php
    }
    

    public function login_app_init() {
        if ('https' === $this->eh_amazon_pay_options['ssl_mode']) {
            ?>
            <script>
                window.onAmazonLoginReady = function () {
                    amazon.Login.setClientId("<?php echo esc_js($this->eh_amazon_pay_options['client_id']); ?>");
                    amazon.Login.setUseCookie(true);
                };
            </script>
            <?php
        }
    }

    public function unset_amazon() {
        if (( isset($_REQUEST['cancel_amazon_checkout']) && ($_REQUEST['cancel_amazon_checkout'] === 'true'))) {
            if (isset($_COOKIE["eh_amazon_access_token"])) {
                unset($_COOKIE["eh_amazon_access_token"]);
                setcookie("eh_amazon_access_token", '', time() - 3600);
            }
        }
    }

   

    public function add_amazon_pay_button() {
        if ($this->id === '' && $this->token === '' && !$this->eh_amazon && $this->eh_amazon_pay_options['amazon_on_checkout_page'] === 'yes') {
            $pwa_button_output = '<center><div class="eh_pay_with_amazon_button"><div class="eh_pay_with_amazon_description" ><small>-- ' . $this->eh_amazon_pay_options['pay_with_amazon_description'] . ' --</small></div>';
            $pwa_button_output .= "<div id='eh_pay_with_amazon' style='padding-bottom:10px;'></div>";
            $pwa_button_output .= "</div></center>";
            //$pwa_button_output .= "<div><small>-- or --</small></div></div></center>";
            echo $pwa_button_output;
        }
    }

    public function add_amazon_address_widget_section() {
        if ($this->eh_amazon && ($this->id !== '' || $this->token !== '' )) {
            ?>
                    <div id="amazon_address_widget_section"></div>
                    <br/>
                    <?php if (!empty($this->id)) { ?>
                        <input type="hidden" name="eh_amazon_payments_reference_id" value="<?php echo esc_attr($this->id); ?>" />
                    <?php } elseif (!empty($this->token)) { ?>
                        <input type="hidden" name="eh_amazon_payments_access_token" value="<?php echo esc_attr($this->token); ?>" />
                    <?php } ?>
                <?php
            }
        }

        public function add_amazon_payment_widget_section() {
            if ($this->eh_amazon && ($this->id !== '' || $this->token !== '' )) {
                ?>
                    <h3><?php _e('Payment Method', 'eh-amazon-payments'); ?></h3>
                    <div id="amazon_payment_widget_section"></div>
                    <br/>
                    <?php if (!empty($this->id)) { ?>
                        <input type="hidden" name="eh_amazon_payments_reference_id" value="<?php echo esc_attr($this->id); ?>" />
                    <?php } elseif (!empty($this->token)) { ?>
                        <input type="hidden" name="eh_amazon_payments_access_token" value="<?php echo esc_attr($this->token); ?>" />
                    <?php } ?>
                <?php 
                    $checkout = wc()->checkout;
                    if ( ! is_user_logged_in() && $checkout->enable_signup ) 
                    { 
                        if ( $checkout->enable_guest_checkout ) 
                        { ?>
                            <p class="form-row form-row-wide create-account">
                                <input class="input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true ) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'eh-amazon-payments' ); ?></label>
                            </p><?php 
                        }
                        do_action( 'woocommerce_before_checkout_registration_form', $checkout );
                        if ( ! empty( $checkout->checkout_fields['account'] ) ) 
                        { ?>
                            <div class="create-account">
                                    <h3><?php _e( 'Create Account', 'eh-amazon-payments' ); ?></h3>
                                    <p><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'eh-amazon-payments' ); ?></p>
                                    <?php foreach ( $checkout->checkout_fields['account'] as $key => $field ) {
                                            woocommerce_form_field( $key, $field, $checkout->get_value( $key ) );
                                    } ?>
                                    <div class="clear"></div>
                            </div><?php 
                        }
                        do_action( 'woocommerce_after_checkout_registration_form', $checkout );
                    } 
        }
    }

    public function gateways_hide_on_review($gateways) {
        if ($this->eh_amazon && ($this->id !== '' || $this->token !== '' )) {
            foreach ($gateways as $id => $name) {
                if ($id !== 'eh_amazon_pay') {
                    unset($gateways[$id]);
                }
            }
            return $gateways;
        }
        return $gateways;
    }

    public function add_policy_notes() {
        if ($this->eh_amazon && ($this->id !== '' || $this->token !== '' ) && $this->eh_amazon_pay_options['policy_notes'] !== '') {
            echo
            '
                <div class="eh_amazon_seller_policy">
                    <div class="form-row eh_amazon_seller_policy_content">
                        <h3>Seller Policy</h3>
                        <div>' . $this->eh_amazon_pay_options['policy_notes'] . '</div>
                    </div>
                </div>
            ';
        }
    }

    public function add_cancel_order_elements() {
        if ($this->eh_amazon && ($this->id !== '' || $this->token !== '' )) {
            printf('<a href="' . add_query_arg('cancel_amazon_checkout', 'true', wc_get_checkout_url()) . '" class="button alt " id="cancel_amazon_order" style="background-color: crimson;text-align: center;">Cancel Order</a>');
        }
    }

    public function update_check_info($array) {
        parse_str($array, $output);
        if ($this->eh_amazon && isset($this->eh_amazon_pay_options['amazon_address_on_checkout_page']) && (($this->eh_amazon_pay_options['amazon_address_on_checkout_page'] == 'yes' && isset($output['ship_to_different_address']) && $output['ship_to_different_address']==1) || $this->eh_amazon_pay_options['amazon_address_on_checkout_page'] == 'bill_ship' || $this->eh_amazon_pay_options['amazon_address_on_checkout_page'] == 'bill' )) {
            if (!$this->id) {
                return;
            }
            $args = array();
            $args['amazon_order_reference_id'] = $this->id;
            if ($this->eh_amazon_pay_options['ssl_mode'] === 'https') {
                $args['mws_auth_token'] = $this->token;
            }
            $amazon_req = new Eh_Amazon_Request_Response();
            $data_object = $amazon_req->request_amazon('GetOrderReferenceDetails', $args);
            if ($data_object->ResponseStatus === '200') {
                $data = $data_object->GetOrderReferenceDetailsResult->OrderReferenceDetails;
                if (!$data || !isset($data->Destination->PhysicalDestination)) {
                    return;
                }
                $customer_address = (array) $data->Destination->PhysicalDestination;
                if (!empty($customer_address['CountryCode'])) {
                    WC()->customer->set_shipping_country($customer_address['CountryCode']);
                }

                if (!empty($customer_address['StateOrRegion'])) {
                    WC()->customer->set_shipping_state($customer_address['StateOrRegion']);
                }

                if (!empty($customer_address['PostalCode'])) {
                    WC()->customer->set_shipping_postcode($customer_address['PostalCode']);
                }

                if (!empty($customer_address['City'])) {
                    WC()->customer->set_shipping_city($customer_address['City']);
                }

                 WC()->session->eh_amazon_checkout = array(
                            'City'         => $customer_address['City'],
                            'PostalCode'      => $customer_address['PostalCode'],
                            'StateOrRegion'    => $customer_address['StateOrRegion'],
                            'CountryCode'      => $customer_address['CountryCode'],
            );
            }
        }
    }

    
}
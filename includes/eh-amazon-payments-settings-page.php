<?php
if (!defined('ABSPATH')) {
    exit;
}
return array(
    'enabled' => array(
        'title' => __('Amazon Payment Gateway', 'eh-amazon-payments'),
        'label' => __('Enable', 'eh-amazon-payments'),
        'type' => 'checkbox',
        'description' => __('This option will enable Amazon Payments method in checkout page.', 'eh-amazon-payments'),
        'default' => 'no',
        'desc_tip' => true
    ),                                    
    'overview' => array(
        'title' => __('Amazon Payment Overview<span style="vertical-align: super;color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments'),
        'label' => __('Enable', 'eh-amazon-payments'),
        'type' => 'checkbox',
        'description' => sprintf(' <a target=\'_blank\' href=\'https://ps.w.org/payments-gateway-amazon-woocommerce/assets/screenshot-4.png?rev=1825962/\'>'.__('Amazon Payments Overview', 'eh-amazon-payments').'</a> '),
        'default' => 'no',
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'title' => array(
        'title' => __('Title', 'eh-amazon-payments'),
        'type' => 'text',
        'description' => __('Enter the title of the checkout which the user can see.', 'eh-amazon-payments'),
        'default' => __('Amazon Payments', 'eh-amazon-payments'),
        'desc_tip' => true
    ),
    'description' => array(
        'title' => __('Regular Description', 'eh-amazon-payments'),
        'type' => 'textarea',
        'css' => 'width:25em',
        'description' => __('Description which the user sees during checkout.', 'eh-amazon-payments'),
        'default' => __('Pay for your order via Amazon Payments Secure Server.', 'eh-amazon-payments'),
        'desc_tip' => true
    ),
    'ssl_mode' => array(
        'title' => __('SSL Mode of your Store', 'eh-amazon-payments'),
        'type' => 'select',
        'class'       => 'wc-enhanced-select',
        'options' => array(
            'https' => __('HTTPS', 'eh-amazon-payments'),
            'http' => __('HTTP', 'eh-amazon-payments')
        ),
        'description' => sprintf(__('If your site is SSL enabled, Pay with Amazon button customization is applicable.<br>You need to geneate Login with App Client ID in Seller Portal<br><div class="links_for_amazon"/>', 'eh-amazon-payments')),
        'default' => 'https'
    ),
    'credentials_title' => array(
        'title' => sprintf('<span style="text-decoration: underline;color:brown;">'.__('Amazon Payments Credentials','eh-amazon-payments').'<span>'),
        'type' => 'title'
    ),
    'environment' => array(
        'title' => __('Environment', 'eh-amazon-payments'),
        'type' => 'select',
        'class'       => 'wc-enhanced-select',
        'options' => array(
            'sandbox' => __('Sandbox Mode', 'eh-amazon-payments'),
            'live' => __('Live Mode', 'eh-amazon-payments')
        ),
        'description' => sprintf(__('Obtain your','eh-amazon-payments').' <a target=\'_blank\' href=\'https://sellercentral.amazon.com/\'>'.__('API credentials', 'eh-amazon-payments').'</a> '. __('from Amazon Seller Central.','eh-amazon-payments')),
        'default' => 'sandbox',
        
    ),
    'merchant_id' => array(
        'title' => __('Merchant ID', 'eh-amazon-payments'),
        'type' => 'text',
        'default' => ''
    ),
    'access_keys' => array(
        'title' => __('Access Keys', 'eh-amazon-payments'),
        'type' => 'text',
        'default' => ''
    ),
    'secret_keys' => array(
        'title' => __('Secret Keys', 'eh-amazon-payments'),
        'type' => 'password',
        'default' => ''
    ),
    'client_id' => array(
        'title' => __('Amazon Client ID', 'eh-amazon-payments'),
        'type' => 'text',
        'default' => ''
    ),
    'client_secret' => array(
        'title' => __('Amazon Client Secret', 'eh-amazon-payments'),
        'type' => 'password',
        'default' => ''
    ),
    'amazon_title' => array(
        'title' => sprintf('<span style="text-decoration: underline;color:brown;">'.__('Pay with Amazon Abilities', 'eh-amazon-payments').'<span>'),
        'type' => 'title'
    ),
    'amazon_on_cart_page' => array(
        'title' => __('Pay with Amazon <span style="vertical-align: super; color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments'),
        'type' => 'checkbox',
        'label' => __('Enable on Cart Page', 'eh-amazon-payments'),
        'default' => 'no',
        'description' => __('Allows customers to checkout using Amazon Payments directly from the cart page.', 'eh-amazon-payments'),
        'desc_tip' => true,
        'custom_attributes'=>array('disabled'=>'disabled')        
    ),
    'amazon_on_checkout_page' => array(
        'title' => __('Pay with Amazon', 'eh-amazon-payments'),
        'type' => 'checkbox',
        'label' => __('Enable on Checkout Page', 'eh-amazon-payments'),
        'default' => 'no',
        'description' => __('Allows customers to checkout using Amazon Payments directly from the Checkout page.', 'eh-amazon-payments'),
        'desc_tip' => true
    ),
    'payment_action' => array(
        'title' => __('Payment Action <span style="vertical-align: super; color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments'),
        'type' => 'select',
        'class'       => 'wc-enhanced-select',
        'options' => array(
            '' => __('Capture', 'eh-amazon-payments'),
            'authorize' => __('Authorize', 'eh-amazon-payments')
        ),
        'description' => __('Select whether you want to capture the payment or not.', 'eh-amazon-payments'),
        'default' => '',
        'desc_tip' =>true,
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'appearance_title' => array(
        'title' => sprintf('<span style="text-decoration: underline;color:brown;">'.__('Pay with Amazon Customization', 'eh-amazon-payments').'<span>'),
        'type' => 'title'
    ),
    'amazon_address_on_checkout_page' => array(
        'title' => __('Use Amazon Address Widget <span style="vertical-align: super;color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments'),
        'type' => 'select',
        'class' => 'wc-enhanced-select',
        'default' => 'bill_ship',
        'description' => __('During checkout, the default billing/shipping address will be replaced by the address provided in customers Amazon account.', 'eh-amazon-payments'),
        'desc_tip' => true,
        'options'   => array(
                'bill_ship' => __( 'Billing & Shipping Address', 'eh-amazon-payments' ),
                'bill' => __( 'Billing Address', 'eh-amazon-payments' ),
                'yes'  => __( 'Shipping Address', 'eh-amazon-payments' ),
               // ''  => __( 'None', 'eh-amazon-payments' ),
                ),
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'button_size' => array(
        'title'       => __( 'Button Size    <span style="vertical-align: super;color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments' ),
        'type'        => 'select',
        'class'       => 'wc-enhanced-select',
        'description' => __( 'Select the Button size that fits your shop theme.', 'eh-amazon-payments' ),
        'default'     => 'medium',
        'desc_tip'    => true,
        'options'     => array(
                'small'  => __( 'Small', 'eh-amazon-payments' ),
                'medium' => __( 'Medium', 'eh-amazon-payments' ),
                'large'  => __( 'Large', 'eh-amazon-payments' ),
                'x-large' => __('X-Large','eh-amazon-payments')
        ),
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'button_color' => array(
        'title'       => __( 'Button Color    <span style="vertical-align: super;color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments' ),
        'type'        => 'select',
        'class'       => 'wc-enhanced-select',
        'description' => __( 'Select the Button color that fits your shop theme.', 'eh-amazon-payments' ),
        'default'     => 'Gold',
        'desc_tip'    => true,
        'options'     => array(
                'Gold'  => __( 'Gold', 'eh-amazon-payments' ),
                'LightGray' => __( 'Light Gray', 'eh-amazon-payments' ),
                'DarkGray'  => __( 'Dark Gray', 'eh-amazon-payments' )
        ),
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'button_text' => array(
        'title'       => __( 'Button Text    <span style="vertical-align: super;color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments' ),
        'type'        => 'select',
        'class'       => 'wc-enhanced-select',
        'description' => __( 'Select the Button text that Checkout button will have.', 'eh-amazon-payments' ),
        'default'     => 'PwA',
        'desc_tip'    => true,
        'options'     => array(
                'PwA' => __( 'Pay With Amazon', 'eh-amazon-payments' ),
                'Pay' => __( 'Pay', 'eh-amazon-payments' ),
                'A'   => __( 'Amazon Logo', 'eh-amazon-payments' )
        ),
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'amazon_locale' => array(
        'title'     => __('Amazon Language  <span style="vertical-align: super;color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments'),
        'type'      => 'checkbox',
        'label'     => __('Use Store Locale', 'eh-amazon-payments'),
        'default'   => 'yes',
        'description' => __('Check to set your Store language to Amazon Language.', 'eh-amazon-payments')."<br><strong><div class='amazon_lanuguage_desc'/></strong>",
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'amazon_language' => array(
        'title'       => __( 'Custom Language  <span style="vertical-align: super;color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments' ),
        'type'        => 'select',
        'class'       => 'wc-enhanced-select',
        'description' => __( 'Select the Button Language for the Checkout Pay with Amazon Payments.', 'eh-amazon-payments' ),
        'default'     => 'en-GB',
        'desc_tip'    => true,
        'options'     => array(
                'en-GB'  => __( 'English (UK)', 'eh-amazon-payments' ),
                'de-DE' => __( 'German (Germany)', 'eh-amazon-payments' ),
                'fr-FR'  => __( 'French (France)', 'eh-amazon-payments' ),
                'it-IT' => __('Italian (Italy)','eh-amazon-payments'),
                'es-ES' => __('Spanish (Spain)','eh-amazon-payments')
        ),
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'position_button' => array(
        'title' => __('Position of Button - Cart <span style="vertical-align: super;color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments'),
        'type' => 'select',
        'class'       => 'wc-enhanced-select',
        'options' => array(
            'above' => __('Above', 'eh-amazon-payments'),
            'below' => __('Below', 'eh-amazon-payments')
        ),
        'description' => __('Select where you want to display the Checkout button.', 'eh-amazon-payments'),
        'default' => 'below',
        'desc_tip' =>true,
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'position_button_checkout' => array(
        'title' => __('Position of Button - Checkout <span style="vertical-align: super;color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments'),
        'type' => 'select',
        'class'       => 'wc-enhanced-select',
        'options' => array(
            'above' => __('Above', 'eh-amazon-payments'),
            'above_rew' => __('Above Order Review', 'eh-amazon-payments'),
            'above_pay' => __('Above Order Payment', 'eh-amazon-payments'),
            'below' => __('Below', 'eh-amazon-payments')
        ),
        'description' => __('Select where you want to display the Checkout button.', 'eh-amazon-payments'),
        'default' => 'above',
        'desc_tip' =>true,
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'banner_display' => array(
        'title' => __('Amazon Display Banners <span style="vertical-align: super;color:green;font-size:12px">[Premium]</span>', 'eh-amazon-payments'),
        'type' => 'select',
        'class'       => 'wc-enhanced-select',
        'default' => 'dark_basic',
        'options' => array(
           // '' => __('No Amazon Banners', 'eh-amazon-payments'),
            'light_logo_check' => __('Light : Logo & Checkout Banners', 'eh-amazon-payments'),
            'dark_basic' => __('Dark : Basic Checkout Banners', 'eh-amazon-payments'),
            'light_basic' => __('Light : Basic Checkout Banners', 'eh-amazon-payments'),
            'dark_logo_check' => __('Dark : Logo & Checkout Banners', 'eh-amazon-payments'),
            'dark_desc_but' => __('Dark : Description & Button Banners', 'eh-amazon-payments'),
            'light_desc_but' => __('Light : Description & Button Banners', 'eh-amazon-payments'),
            'dark_logo_desc' => __('Dark : Logo & Description Banners', 'eh-amazon-payments'),
            'light_logo_desc' => __('Light : Logo & Description Banners', 'eh-amazon-payments')
        ),
        'description' => __('Banners on your homepage and throughout the shopping experience drive awareness of the benefits of completing a purchase using Pay with Amazon.','eh-paypal-express'). sprintf('<br><br><img src="%s" width="800px" height="30px" style="cursor:pointer" title="Amazon Display Banner">',('' === $this->get_option('banner_display'))?EH_AMAZON_MAIN_URL.'assets/banner/light_logo_check.jpg':EH_AMAZON_MAIN_URL.'assets/banner/'.$this->get_option('banner_display').'.jpg'),
        
        'custom_attributes'=>array('disabled'=>'disabled')
    ),
    'pay_with_amazon_description' => array(
        'title' => __('Pay with Amazon Description', 'eh-amazon-payments'),
        'type' => 'textarea',
        'css' => 'width:25em',
        'description' => __('Description which the user sees during Pay with Amazon.', 'eh-amazon-payments'),
        'default' => __('Reduce multiple click by clicking on Pay with Amazon', 'eh-amazon-payments'),
        'desc_tip' => true
    ),       
    'policy_notes' => array(
        'title' => __('Seller Policy', 'eh-amazon-payments'),
        'type' => 'textarea',
        'css' => 'width:25em',
        'description' => __('Enter the seller protection policy or customized text which will be displayed in order review page.', 'eh-amazon-payments'),
        'default' => __('You are Protected by '.get_bloginfo( 'name', 'display').' Policy', 'eh-amazon-payments'),
        'desc_tip' => true
    ),
    'log_title' => array(
        'title' => sprintf('<span style="text-decoration: underline;color:brown;">'.__('Developer Settings','eh-amazon-payments').'<span>'),
        'type' => 'title',
        'description' => sprintf(__('Enable Logging to save Amazon Payments logs into log file.','eh-amazon-payments').' <a href="'.admin_url("admin.php?page=wc-status&tab=logs").'" target="_blank">'.__(' Check Now','eh-amazon-payments').' </a>')
    ),
    'amazon_logging' => array(
        'title' => __('Logging', 'eh-amazon-payments'),
        'label' => __('Enable', 'eh-amazon-payments'),
        'type' => 'checkbox',
        'description' => sprintf('<span style="color:green">'.__('Log File','eh-amazon-payments').'</span>: ' . strstr(wc_get_log_file_path('eh_amazon_pay_log'), 'eh_amazon_pay_log') . ' ( ' . $this->file_size(wc_get_log_file_path('eh_amazon_pay_log')) . ' ) '),
        'default' => 'yes'
    )
    );


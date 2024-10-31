<?php
/*
Plugin Name: Amazon Payment Gateway for WooCommerce ( Basic )
Plugin URI: www.xadapter.com/product/amazon-payments-gateway-woocommerce/
Description: Login and Pay with Amazon for your shop orders.
Version: 1.3.2
WC requires at least: 2.6.0
WC tested up to: 3.4
Author: AdaptXY
Author URI: https://adaptxy.com/
*/

if (!defined('ABSPATH')) {
    exit;
}
if (!defined('EH_AMAZON_MAIN_PATH')) {
    define('EH_AMAZON_MAIN_PATH', plugin_dir_path(__FILE__));
}
if (!defined('EH_AMAZON_MAIN_URL')) {
    define('EH_AMAZON_MAIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('EH_AMAZON_VERSION')) {
    define('EH_AMAZON_VERSION', '1.1.0');
}

require_once(ABSPATH . "wp-admin/includes/plugin.php");
// Change the Pack IF BASIC  mention switch('BASIC') ELSE mention switch('PREMIUM')
switch('BASIC')
{
    case 'PREMIUM':
        $conflict   = 'basic';
        $base       = 'premium';
        break;
    case 'BASIC':
        $conflict   = 'premium';
        $base       = 'basic';
        break;
}
// Enter your plugin unique option name below $option_name variable
$option_name='eh_amazon_pay_pack';

if(get_option($option_name)==$conflict)
{
    add_action('admin_notices','eh_wc_admin_notices', 99);
    deactivate_plugins(plugin_basename(__FILE__));
    function eh_wc_admin_notices()
    {
        is_admin() && add_filter('gettext', function($translated_text, $untranslated_text, $domain)
        {
            $old = array(
                "Plugin <strong>activated</strong>.",
                "Selected plugins <strong>activated</strong>."
            );
            $error_text='';
            // Change the Pack IF BASIC  mention switch('BASIC') ELSE mention switch('PREMIUM')
            switch('BASIC')
            {
                case 'PREMIUM':
                    $error_text="BASIC Version of this Plugin Installed. Please uninstall the BASIC Version before activating PREMIUM.";
                    break;
                case 'BASIC':
                    $error_text="PREMIUM Version of this Plugin Installed. Please uninstall the PREMIUM Version before activating BASIC.";
                    break;
            }
            $new = "<span style='color:red'>".$error_text."</span>";
            if (in_array($untranslated_text, $old, true)) {
                $translated_text = $new;
            }
            return $translated_text;
        }, 99, 3);
    }
    return;
}
else
{
    update_option($option_name, $base);
    register_deactivation_hook(__FILE__, 'eh_amazon_pay_deactivate_work');
    // Enter your plugin unique option name below update_option function
    function eh_amazon_pay_deactivate_work()
    {
        update_option('eh_amazon_pay_pack', '');
    }    
    if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
    {
        register_activation_hook(__FILE__, 'eh_amazon_pay_init_log');
        include(EH_AMAZON_MAIN_PATH . "includes/log.php");
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'eh_amazon_pay_plugin_action_links');
        add_action( 'init', 'eh_amazon_lang_loader' );
        function eh_amazon_lang_loader() {
            load_plugin_textdomain( 'eh-amazon-payments', false, dirname( plugin_basename( __FILE__ ) ) . '/lang');
        }
        function eh_amazon_pay_plugin_action_links($links)
        {
            $setting_link = admin_url('admin.php?page=wc-settings&tab=checkout&section=eh_amazon_pay');
            $plugin_links = array(
                '<a href="' . $setting_link . '">' . __('Settings', 'eh-paypal-express') . '</a>',
                '<a href="https://www.xadapter.com/product/amazon-payments-gateway-woocommerce/" target="_blank">' . __( 'Premium Upgrade', 'eh-paypal-express' ) . '</a>',
                '<a href="https://www.xadapter.com/category/documentation/amazon-payments-gateway-for-woocommerce/" target="_blank">' . __( 'Documentation', 'eh-paypal-express' ) . '</a>',
                '<a href="https://wordpress.org/support/plugin/payments-gateway-amazon-woocommerce/" target="_blank">' . __('Support', 'eh-paypal-express') . '</a>'
            );
            return array_merge($plugin_links, $links);
        }
        function eh_amazon_pay_init_log()
        {
            if(WC()->version >= '2.7.0')
            {
                $log      = wc_get_logger();
                $init_msg = Eh_Amazon_Log::init_log();
                $context = array( 'source' => 'eh_amazon_pay_log' );
                $log->log("debug", $init_msg,$context);
            }
            else
            {
                $log      = new WC_Logger();
                $init_msg = Eh_Amazon_Log::init_log();
                $log->add("eh_amazon_pay_log", $init_msg);
            }
        }
        function eh_amazon_pay_run()
        {
            add_filter('woocommerce_ajax_get_endpoint','add_query_parameters_to_ajax_requests_after_3_2_0_rc1',2,99);
            static $eh_amazon_plugin;
            if(!isset($eh_amazon_plugin))
            {
                require_once (EH_AMAZON_MAIN_PATH . "includes/class-eh-amazon-init-handler.php");
                $eh_amazon_plugin=new Eh_Amazon_Pay_Handlers();
            }
            return $eh_amazon_plugin;
        }
        function add_query_parameters_to_ajax_requests_after_3_2_0_rc1($url,$request)
        {
                return add_query_arg( 'wc-ajax', $request, remove_query_arg( array( 'remove_item', 'add-to-cart', 'added-to-cart' ), home_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
        }
        eh_amazon_pay_run()->express_run();
    }
    else
    {
        add_action('admin_notices', 'eh_amazon_pay_wc_admin_notices', 99);
        deactivate_plugins(plugin_basename(__FILE__));
    }
    function eh_amazon_pay_wc_admin_notices()
    {
        is_admin() && add_filter('gettext', function($translated_text, $untranslated_text, $domain)
        {
            $old = array(
                "Plugin <strong>activated</strong>.",
                "Selected plugins <strong>activated</strong>."
            );
            $new = "<span style='color:red'>Amazon Payment Gateway for Woocommerce (Basic) - </span> Plugin Needs WooCommerce to Work.";
            if (in_array($untranslated_text, $old, true)) {
                $translated_text = $new;
            }
            return $translated_text;
        }, 99, 3);
    }
}

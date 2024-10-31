<?php
if (!defined('ABSPATH')) {
    exit;
}
class Eh_Amazon_Pay_Handlers {
    public function express_run()
    {
        add_action('plugins_loaded',array($this, 'check_dependencies'), 99);
        add_filter('woocommerce_payment_gateways',array($this, 'add_payment_gateway'));
    }
    public function add_payment_gateway($methods)
    {
        $methods[] = 'Eh_Amazon_Pay_Payment';
        return $methods;
    }
    public function check_dependencies()
    {
        if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $this->amazon_pay_init();
        } else {
            add_action('admin_notices', 'eh_amazon_pay_wc_admin_notices', 99);
            deactivate_plugins(plugin_basename(__FILE__));
        }
    }
    public function amazon_pay_init()
    {
        if (!class_exists('Eh_Amazon_Pay_Payment')) {
            require_once (EH_AMAZON_MAIN_PATH . "includes/class-amazon-pay-api.php");
        }
        $this->run_dependencies_hook();
    }
    public function run_dependencies_hook()
    {   
       // require_once (EH_AMAZON_MAIN_PATH . "includes/include-ajax-functions.php");
        require_once (EH_AMAZON_MAIN_PATH . "includes/class-amazon-pay-static.php");
        require_once (EH_AMAZON_MAIN_PATH . "includes/class-amazon-pay-hook.php");
       // require_once (EH_AMAZON_MAIN_PATH . "includes/class-amazon-pay-overview-table-data.php");
        $this->hook_include=new Eh_Amazon_Pay_Hooks();
        require_once (EH_AMAZON_MAIN_PATH . "includes/class-amazon-pay-request-response.php");
        require_once (EH_AMAZON_MAIN_PATH . "lib/pwa/Client.php");
    }
}
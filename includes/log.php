<?php
if (!defined('ABSPATH')) {
    exit;
}
class Eh_Amazon_Log 
{
    public static function init_log()
    {
        $content="<------------------- Amazon Payments Log File ( ".EH_AMAZON_VERSION." ) ------------------->\n";
        return $content;
    }
    public static function log_update($msg,$title)
    {
        $check=  get_option('woocommerce_eh_amazon_pay_settings');
        if('yes' === $check['amazon_logging'])
        {
            if(WC()->version >= '2.7.0')
            {
                $log= wc_get_logger();
                $head="<------------------- Amazon Payments ( ".$title." ) ------------------->\n";
                $log_text=$head.print_r((object)$msg,true);
                $context = array( 'source' => 'eh_amazon_pay_log' );
                $log->log("debug", $log_text,$context);
            }
            else
            {
                $log=new WC_Logger();
                $head="<------------------- Amazon Payments ( ".$title." ) ------------------->\n";
                $log_text=$head.print_r((object)$msg,true);
                $log->add("eh_amazon_pay_log",$log_text);
            }
        }
    }
}

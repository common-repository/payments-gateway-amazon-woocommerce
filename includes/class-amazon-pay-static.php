<?php
if (!defined('ABSPATH')) {
    exit;
}
class Eh_Amazon_Static {

    public static function get_base_location() {
        return in_array(WC()->countries->get_base_country(), array('US', 'GB', 'DE', 'JP')) ? WC()->countries->get_base_country() : 'US';
    }
     public static function get_enable_amazon() {
        if (isset($_REQUEST['eh_amazon_payments']) && $_REQUEST['eh_amazon_payments'] === 'true') {
            return true;
        }
        return false;
    }

    public static function get_acc_token($ssl) {
        $token = '';
        if ($ssl === 'https') {
            if (isset($_REQUEST['access_token'])) {
                $token = sanitize_text_field($_REQUEST['access_token']);
            }
        }
        return $token;
    }

    public static function get_ref_id() {
        $id = '';
        if (isset($_REQUEST['amazon_reference_id'])) {
            $id = sanitize_text_field($_REQUEST['amazon_reference_id']);
            return $id;
        }
        if ( isset( $_POST['post_data'] ) ) {
            parse_str( sanitize_text_field($_POST['post_data']), $post_data );
            if ( isset( $post_data['amazon_reference_id'] ) ) {
                    $id = $post_data['amazon_reference_id'];
            }
        }
        return $id;
    }

    public static function get_widget_js($environment, $ssl_mode, $id = '') {
        $base_location = self::get_base_location();
        $env = ($environment === 'sandbox') ? 'sandbox/' : '';
        $login_app = ($ssl_mode === 'https') ? 'lpa/' : '';
        switch ($base_location) {
            case 'US':
                $url = 'https://static-na.payments-amazon.com/OffAmazonPayments/us/' . $env . 'js/Widgets.js';
                break;
            case 'GB':
                $url = 'https://static-eu.payments-amazon.com/OffAmazonPayments/uk/' . $env . $login_app . 'js/Widgets.js';
                break;
            case 'DE':
                $url = 'https://static-eu.payments-amazon.com/OffAmazonPayments/de/' . $env . $login_app . 'js/Widgets.js';
                break;
            case 'JP':
                $url = 'https://static-fe.payments-amazon.com/OffAmazonPayments/jp/' . $env . $login_app . 'js/Widgets.js';
                break;
        }
        return $url . '?sellerId=' . $id;
    }

    public static function get_lang($locale, $language = '') {
        if ($locale === 'yes') {
            return ( in_array(get_locale(), array('en_US', 'en_GB', 'de_DE', 'fr_FR', 'it_IT', 'es_ES'))) ? str_replace("_", "-", get_locale()) : 'en-US';
        } else {
            return $language;
        }
    }
}
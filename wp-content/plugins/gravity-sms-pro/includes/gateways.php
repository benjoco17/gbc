<?php if (!defined('ABSPATH')) exit;

if (!class_exists('GFHANNANSMS_Pro_WebServices')) {

    class GFHANNANSMS_Pro_WebServices
    {

        public static function get()
        {
            return apply_filters('gf_sms_gateways', array('no' => __('Select a Gateway', 'GF_SMS')));
        }

        public static function action($settings, $action, $from, $to, $messages)
        {

            $gateway = isset($settings["ws"]) ? $settings["ws"] : '';

            if (empty($gateway) || $gateway == 'no')
                return __('No Gateway found.', 'GF_SMS');

            $GATEWAY = strtoupper($gateway);
            $Gateway = 'GFHANNANSMS_Pro_' . $GATEWAY;

            if (!class_exists($Gateway) && file_exists(GF_SMS_GATEWAY . strtolower($gateway) . '.php')) {
                require_once(GF_SMS_GATEWAY . strtolower($gateway) . '.php');
            }

            if (class_exists($Gateway) && method_exists($Gateway, 'process')) {
                $options = get_option("gf_hannansms_" . $GATEWAY);
                return $Gateway::process($options, $action, $from, $to, $messages);
            }

            return __('No Gateway found.', 'GF_SMS');
        }
    }
}
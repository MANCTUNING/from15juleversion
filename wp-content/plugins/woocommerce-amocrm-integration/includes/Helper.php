<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Includes;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Helper
{
    public static $log;

    private static $logRequestID;

    public static function log($message, $data = [], $type = 'info')
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (
            !isset($settings['enabled_logging']) ||
            (int) $settings['enabled_logging'] !== 1
        ) {
            return;
        }

        try {
            if (empty(self::$log)) {
                self::$log = new Logger('wcamo');
                self::$log->pushHandler(
                    new StreamHandler(WC_AMOCRM_PLUGIN_LOG_FILE, Logger::INFO)
                );
            }

            if (empty(self::$logRequestID)) {
                self::$logRequestID = uniqid();
            }

            self::$log->$type('[' . self::$logRequestID . '] ' . $message, (array) $data);
        } catch (\Exception $exception) {
            if (is_super_admin()) {
                wp_die(
                    sprintf(
                        esc_html__(
                            'Error code (%s): %s.',
                            'wc-amocrm-integration'
                        ),
                        $exception->getCode(),
                        $exception->getMessage()
                    ),
                    esc_html__(
                        'An error occurred while writing the log file.',
                        'wc-amocrm-integration'
                    ),
                    [
                        'back_link' => true
                    ]
                );
                // escape ok
            }
        }
    }

    public static function getRedirectUrl()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (!empty($settings['redirect-url'])) {
            return $settings['redirect-url'];
        }

        return admin_url() . 'admin.php?page=wc-amocrm-integration-settings';
    }

    public static function isVerify()
    {
        $value = get_site_option(Bootstrap::PURCHASE_CODE_OPTIONS_KEY);

        if (!empty($value)) {
            return true;
        }

        return false;
    }

    public static function hasToken()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);
        $tokenData = get_option(Bootstrap::TOKEN_DATA_KEY, []);

        if (
            !empty($settings['domain']) &&
            !empty($settings['client-id']) &&
            !empty($settings['client-secret']) &&
            !empty($tokenData['refresh_token'])
        ) {
            return true;
        }

        return false;
    }

    public static function nonVerifyText()
    {
        return esc_html__(
            'Please verify the purchase code on the plugin integration settings page - ',
            'wc-amocrm-integration'
            )
            . admin_url()
            . 'admin.php?page=wc-amocrm-integration-settings#wcamo-license-verify';
    }

    public static function resolveNextResponsible($list)
    {
        $list = explode(',', $list);

        if (count($list) === 1) {
            return $list[0];
        }

        $last = get_option('_wc_amocrm_last_responsible');
        $lastKey = array_search($last, $list);

        if (empty($last) || $lastKey === false || ($lastKey + 1) >= count($list)) {
            update_option('_wc_amocrm_last_responsible', $list[0]);

            return $list[0];
        }

        update_option('_wc_amocrm_last_responsible', $list[$lastKey + 1]);

        return $list[$lastKey + 1];
    }

    public static function isJson($string)
    {
        json_decode($string);

        $noError = json_last_error() === JSON_ERROR_NONE;

        return $noError;
    }

    public static function resolveSelectMultiSelect($fields, $resolvedFields)
    {
        foreach ($fields as $field) {
            if (!empty($resolvedFields[$field['id']])
                && !in_array($field['code'], ['PHONE', 'EMAIL', 'IM'])
                && !empty($field['enums'])
            ) {
                $ids = array_keys($field['enums']);
                $labels = array_values($field['enums']);

                $explodedField = explode(', ', $resolvedFields[$field['id']]);
                $resolveValues = [];

                foreach ($explodedField as $explodeValue) {
                    if (array_search($explodeValue, $ids) !== false) {
                        $resolveValues[] = $explodeValue;
                    } elseif (array_search($explodeValue, $labels) !== false) {
                        $resolveValues[] = $ids[array_search($explodeValue, $labels)];
                    }
                }

                if ($resolveValues) {
                    // type_id = 5 - multiselect
                    $resolvedFields[$field['id']]
                        = (int) $field['type_id'] === 5
                        ? $resolveValues
                        : $resolveValues[0];
                }
            }
        }

        return $resolvedFields;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}

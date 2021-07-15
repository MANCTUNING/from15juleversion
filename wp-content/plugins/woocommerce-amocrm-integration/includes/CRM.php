<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Includes;

use AmoCRM\ClientOauthWc;

class CRM
{
    public static function updateInformation()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        try {
            $amo = new ClientOauthWc($settings['domain'], 'empty', $settings);

            $account = $amo->account->apiCurrent();

            update_option(Bootstrap::OPTIONS_CUSTOM_FIELDS, $account['custom_fields']);
            update_option(Bootstrap::OPTIONS_USERS, $account['users']);

            $pipelines = $amo->pipelines->apiList();
            update_option(Bootstrap::OPTIONS_PIPELINES, $pipelines);
        } catch (\Exception $e) {
            Helper::log('error when update info', $e, 'error');

            if (defined('WP_DEBUG') && WP_DEBUG === true) {
                printf(
                    'Error (%d): %s' . "\n",
                    (int) $e->getCode(),
                    esc_html($e->getMessage())
                );
            }
        }
    }

    public static function checkConnection()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        Helper::log('check connection');

        try {
            $response = wp_remote_post(
                'https://' . $settings['domain'] . '/oauth2/access_token',
                [
                    'body' => [
                        'grant_type' => 'authorization_code',
                        'client_id' => $settings['client-id'],
                        'client_secret' => $settings['client-secret'],
                        'redirect_uri' => Helper::getRedirectUrl(),
                        'code' => $settings['authorization-code']
                    ],
                    'timeout' => 20
                ]
            );

            if (is_wp_error($response)) {
                throw new \Exception(
                    $response->get_error_message(),
                    (int) $response->get_error_code()
                );
            }

            $body = $response['body'];
            $result = json_decode($body, true);

            if (isset($result['hint'])) {
                throw new \Exception(
                    $result['hint'] . ' | ' . $result['detail'],
                    (int) $result['status']
                );
            }

            if (empty($result['refresh_token']) && isset($result['title'])) {
                throw new \Exception(
                    $result['title'] . ' | ' . $result['detail'],
                    (int) $result['status']
                );
            }

            unset($settings['authorization-code']);

            update_option(Bootstrap::OPTIONS_KEY, $settings);

            if (!empty($result['refresh_token'])) {
                update_option(
                    Bootstrap::TOKEN_DATA_KEY,
                    [
                        'access_token' => $result['access_token'],
                        'refresh_token' => $result['refresh_token'],
                        'expires_in' => time() + (int) $result['expires_in'],
                    ]
                );
            } else {
                update_option(Bootstrap::TOKEN_DATA_KEY, []);
            }

            Helper::log('check connection result - success', $result);
        } catch (\Exception $e) {
            Helper::log('error when check connection', $e, 'error');

            $settings['domain'] = '';
            $settings['client-id'] = '';
            $settings['client-secret'] = '';
            $settings['authorization-code'] = '';

            // Clean failed information
            update_option(Bootstrap::OPTIONS_KEY, $settings);
            update_option(Bootstrap::TOKEN_DATA_KEY, []);

            return sprintf(
                '<div data-ui-component="wcamonotice" class="error notice notice-error">'
                . '<p><strong>Error (%d)</strong>: %s</p></div>',
                (int) $e->getCode(),
                esc_html($e->getMessage())
            );
            // Escape ok
        }

        return '';
    }

    private function __construct()
    {
        // Nothing
    }

    private function __clone()
    {
        // Nothing
    }
}

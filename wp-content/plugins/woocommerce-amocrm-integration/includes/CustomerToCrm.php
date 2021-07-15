<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Includes;

use AmoCRM\ClientOauthWc;

class CustomerToCrm
{
    private static $instance = false;

    protected function __construct()
    {
        if ($this->isEnabled()) {
            add_action('woocommerce_created_customer', [$this, 'customerSendCrm'], 10);
            add_action('profile_update', [$this, 'customerSendCrm'], 10);
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function customerSendCrm($customerID)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY, []);

        // maybe empty contact fields setting
        if (empty($settings['contact'])) {
            return;
        }

        $crmFields = new CrmFields();
        $additionalFields = get_option(Bootstrap::OPTIONS_CUSTOM_FIELDS);

        $prepareAdditionalContactFields = [];

        foreach ($additionalFields['contacts'] as $field) {
            $prepareAdditionalContactFields[$field['id']] = $field['code'];
        }

        $additionalFieldIds = [];

        foreach ($additionalFields['contacts'] as $field) {
            $additionalFieldIds[] = $field['id'];
        }

        $contactData = $this->prepareContactData(get_user_meta($customerID), $customerID);
        $contactFields = $this->prepareFields($settings['contact'], $contactData);

        $name = !empty($contactData['billing_first_name']) ? $contactData['billing_first_name'] : '';
        $name .= !empty($contactData['billing_last_name']) ? (' ' . $contactData['billing_last_name']) : '';

        if (!$name) {
            $name = get_user_meta($customerID, 'first_name', true);
        }

        if (!$name) {
            $name = get_user_meta($customerID, 'nickname', true);
        }

        $contactFields['name'] = $name;

        try {
            $amo = new ClientOauthWc($settings['domain'], 'empty', $settings);

            $entity = $amo->contact;

            $searchContactEmail = '';
            $searchContactPhone = '';

            foreach ($contactFields as $key => $value) {
                if (in_array($key, array_keys($crmFields->contacts))) {
                    $entity[$key] = $value;
                } elseif (in_array($key, $additionalFieldIds) && !empty($value)) {
                    if ($prepareAdditionalContactFields[$key] == 'EMAIL') {
                        $searchContactEmail = $value;
                    } elseif ($prepareAdditionalContactFields[$key] == 'PHONE') {
                        $searchContactPhone = str_replace(' ', '', $value);
                    }

                    $entity->addCustomField(
                        $key,
                        $value,
                        in_array($prepareAdditionalContactFields[$key], ['EMAIL', 'PHONE'], true) ? 'WORK' : null
                    );
                }
            }

            $existsContact = false;

            if ($searchContactEmail) {
                $existsContact = $amo->contact->apiList(['query' => $searchContactEmail]);
            }

            if (!$existsContact && $searchContactPhone) {
                $existsContact = $amo->contact->apiList(['query' => $searchContactPhone]);
            }

            // Exists contact is found
            if ($existsContact) {
                $existsContact = current($existsContact);
                $leadIds = [];

                if (!empty($existsContact['linked_leads_id'])) {
                    $leadIds = array_merge($existsContact['linked_leads_id'], $leadIds);
                }

                // if not enable do no update - just connect a new lead
                if (!isset($settings['do_not_update_contact']) || $settings['do_not_update_contact'] != '1') {
                    $entity->setLinkedLeadsId($leadIds);
                    $entity->apiUpdate($existsContact['id']);
                }
            } else {
                $entity->apiAdd();
            }
        } catch (\Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG === true) {
                printf(
                    'Error (%d): %s' . "\n",
                    (int) $e->getCode(),
                    esc_html($e->getMessage())
                );
            }
        }
    }

    private function prepareContactData($data, $userID)
    {
        $returnData = [];

        foreach ($data as $key => $value) {
            if (empty($value) || empty($value[0])) {
                continue;
            }

            if (mb_strpos($key, 'billing') === false && mb_strpos($key, 'shipping') === false) {
                continue;
            }

            $returnData[$key] = $value[0];
        }

        $user  = get_userdata($userID);

        if (empty($returnData['billing_email'])) {
            $returnData['billing_email'] = $user->user_email;
        }

        $utmFields = $this->parseUtmCookie();

        $returnData['utm_source'] = isset($utmFields['utm_source'])
            ? rawurldecode(wp_unslash($utmFields['utm_source']))
            : '';
        $returnData['utm_medium'] = isset($utmFields['utm_medium'])
            ? rawurldecode(wp_unslash($utmFields['utm_medium']))
            : '';
        $returnData['utm_campaign'] = isset($utmFields['utm_campaign'])
            ? rawurldecode(wp_unslash($utmFields['utm_campaign']))
            : '';
        $returnData['utm_term'] = isset($utmFields['utm_term'])
            ? rawurldecode(wp_unslash($utmFields['utm_term']))
            : '';
        $returnData['utm_content'] = isset($utmFields['utm_content'])
            ? rawurldecode(wp_unslash($utmFields['utm_content']))
            : '';

        $returnData['roistat_visit'] = isset($_COOKIE['roistat_visit'])
            ? $_COOKIE['roistat_visit']
            : '';

        return $returnData;
    }

    private function prepareFields($fields, $orderData)
    {
        $returnFields = [];

        $keys = array_map(function ($key) {
            return '[' . $key . ']';
        }, array_keys($orderData));
        $values = array_values($orderData);
        array_walk($values, function (&$value) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            }
        });

        foreach ($fields as $keyField => $fieldValue) {
            $value = trim(str_replace($keys, $values, $fieldValue));

            if ($value && mb_strpos($value, '[') === false) {
                $returnFields[$keyField] = $value;
            }
        }

        return $returnFields;
    }

    private function parseUtmCookie()
    {
        if (!empty($_COOKIE[Bootstrap::UTM_COOKIE])) {
            return json_decode(wp_unslash($_COOKIE[Bootstrap::UTM_COOKIE]), true);
        }

        return [];
    }

    private function isEnabled()
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);

        return isset($settings['enabled'])
            && $settings['enabled'] == '1'
            && isset($settings['enabled_contact'])
            && $settings['enabled_contact'] == '1'
            && Helper::hasToken();
    }

    private function __clone()
    {
    }
}

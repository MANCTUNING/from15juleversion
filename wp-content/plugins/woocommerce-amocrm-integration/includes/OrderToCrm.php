<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Includes;

use AmoCRM\ClientOauthWc;
use AmoCRM\Models\Note;
use AmoCRM\Models\Task;

class OrderToCrm
{
    private static $instance = false;

    public $order = null;

    protected function __construct()
    {
        if (!$this->isEnabled()) {
            return;
        }

        add_action('woocommerce_checkout_order_processed', [$this, 'actionProcessing'], 11, 1);
        add_action('woocommerce_resume_order', [$this, 'actionProcessing'], 10, 1);
        add_action('woocommerce_order_status_changed', [$this, 'actionProcessing']);

        // compatible with Woocommerce Subscriptions - renewal order
        add_filter('wcs_renewal_order_created', [$this, 'renewalOrder'], 10, 2);

        add_action('woocommerce_after_order_object_save', [$this, 'afterSaveOrder']);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function renewalOrder($renewalOrder, $subscription)
    {
        // if is order number
        if (is_numeric($renewalOrder)) {
            $this->actionProcessing($renewalOrder);
            // if is order object
        } elseif (is_a($renewalOrder, 'WC_Order')) {
            $this->actionProcessing($renewalOrder->get_id());
        }

        return $renewalOrder;
    }

    public function afterSaveOrder($order)
    {
        $orderID = false;

        // if is order number
        if (is_numeric($order)) {
            $orderID = $order;
            // if is order object
        } elseif (is_a($order, 'WC_Order')) {
            $orderID = $order->get_id();
        }

        if ($orderID && !wp_next_scheduled(Bootstrap::CRON_TASK_SEND, [$orderID])) {
            Helper::log('register task order save event - ' . $orderID);
            wp_schedule_single_event(time() + 15, Bootstrap::CRON_TASK_SEND, [$orderID]);
        }
    }

    public function actionProcessing($orderID)
    {
        Helper::log('action processing - ' . $orderID);

        $settings = get_option(Bootstrap::OPTIONS_KEY);

        if (!empty($settings['send_type']) && $settings['send_type'] === 'wp_cron') {
            if ($orderID && !wp_next_scheduled(Bootstrap::CRON_TASK_SEND, [$orderID])) {
                Helper::log('register task send - ' . $orderID);
                wp_schedule_single_event(time() + 15, Bootstrap::CRON_TASK_SEND, [$orderID]);
            }
        } else {
            $this->orderSendCrm($orderID);
        }
    }

    public function orderSendCrm($orderID)
    {
        // Stop duplicate
        if (get_post_meta($orderID, '_wc_amo_lead_id', true)) {
            Helper::log('update lead event - ' . $orderID);
            $this->leadUpdate($orderID);
        } else {
            Helper::log('create lead event - ' . $orderID);
            $this->leadProcessing($orderID);
        }
    }

    private function leadProcessing($orderID)
    {
        try {
            $settings = get_option(Bootstrap::OPTIONS_KEY);

            $leadStatuses = isset($settings['lead_statuses']) ? $settings['lead_statuses'] : '';

            if (empty($leadStatuses)) {
                Helper::log('empty all statuses - ' . $orderID);

                return;
            }

            $order = wc_get_order($orderID);
            $this->order = $order;
            $status = $order->get_status();

            if (empty($leadStatuses[$status])) {
                Helper::log('empty status - ' . $status);

                return;
            }

            $crmFields = new CrmFields();
            $additionalFields = get_option(Bootstrap::OPTIONS_CUSTOM_FIELDS);

            // prepare contact fields for search
            $prepareAdditionalContactFields = [];

            foreach ($additionalFields['contacts'] as $field) {
                $prepareAdditionalContactFields[$field['id']] = $field['code'];
            }

            // prepare company fields for search
            $prepareAdditionalCompanyFields = [];

            foreach ($additionalFields['companies'] as $field) {
                $prepareAdditionalCompanyFields[$field['id']] = $field['code'];
            }

            $additionalFieldIds = [];

            foreach ($additionalFields['leads'] as $field) {
                $additionalFieldIds[] = $field['id'];
            }

            foreach ($additionalFields['contacts'] as $field) {
                $additionalFieldIds[] = $field['id'];
            }

            $additionalFieldCompaniesIds = [];

            foreach ($additionalFields['companies'] as $field) {
                $additionalFieldCompaniesIds[] = $field['id'];
            }

            Helper::log('start prepareOrderData - ' . $orderID);
            $data = $order->get_data();
            $orderData = $this->prepareOrderData($data, $order);
            Helper::log('end prepareOrderData - ' . $orderID);

            Helper::log('start prepareFields lead - ' . $orderID);
            $leadFields = $this->prepareFields($settings['lead'], $orderData);
            Helper::log('end prepareFields lead - ' . $orderID);

            if (empty($leadFields['name'])) {
                $leadFields['name'] = esc_html__('Order', 'wc-amocrm-integration')
                    . ' '
                    . $order->get_order_number();
            }

            $leadFields['sale'] = $data['total'];

            Helper::log('start prepareFields contact - ' . $orderID);
            $contactFields = $this->prepareFields($settings['contact'], $orderData);
            Helper::log('end prepareFields contact - ' . $orderID);

            $companyFields = [];

            if (!empty($settings['company'])) {
                Helper::log('start prepareFields company - ' . $orderID);
                $companyFields = $this->prepareFields($settings['company'], $orderData);
                Helper::log('end prepareFields company - ' . $orderID);
            }

            $taskFields = [];

            if (!empty($settings['task'])
                && isset($settings['create_task_to_deal'])
                && $settings['create_task_to_deal'] === 'true'
            ) {
                Helper::log('start prepareFields task - ' . $orderID);
                $taskFields = $this->prepareFields($settings['task'], $orderData);
                Helper::log('end prepareFields task - ' . $orderID);
            }

            Helper::log('start connection amo - ' . $orderID);
            $amo = new ClientOauthWc($settings['domain'], 'empty', $settings);

            $entity = $amo->lead;

            $leadFields['created_at'] = strtotime($order->get_date_created()->date_i18n('c'));
            $leadFields['updated_at'] = strtotime($order->get_date_modified()->date_i18n('c'));

            // Set pipeline id
            $explodeCurrentStatus = explode('.', $leadStatuses[$status]);

            if (count($explodeCurrentStatus) > 1) {
                $leadFields['pipeline_id'] = $explodeCurrentStatus[0];
                $leadFields['status_id'] = $explodeCurrentStatus[1];
            } else {
                $leadFields['status_id'] = $leadStatuses[$status];
            }

            $leadFields = Helper::resolveSelectMultiSelect($additionalFields['leads'] , $leadFields);
            $leadFields = apply_filters('itglx_wcamo_lead_fields', $leadFields, $order);
            $leadResponsible = 0;

            foreach ($leadFields as $key => $value) {
                if (in_array($key, array_keys($crmFields->leads)) || $key === 'sale') {
                    if ($key == 'responsible_user_id' && !empty($value)) {
                        $value = Helper::resolveNextResponsible($value);
                        $leadResponsible = $value;
                    }

                    $entity[$key] = $value;
                } elseif (in_array($key, $additionalFieldIds) && !empty($value)) {
                    if (is_array($value) && !is_numeric(reset(array_keys($value)))) {
                        $resultValue = [];

                        foreach ($value as $subKey => $subValue) {
                            $resultValue[] = [
                                'value' => $subValue,
                                'subtype' => $subKey
                            ];
                        }

                        $entity->addCustomField($key, $resultValue);
                    } else {
                        $entity->addCustomField($key, $value);
                    }
                }
            }

            if (!empty($leadFields['created_at'])) {
                $entity['created_at'] = $leadFields['created_at'];
            }

            if (!empty($leadFields['updated_at'])) {
                $entity['updated_at'] = $leadFields['updated_at'];
            }

            Helper::log('lead entity', $entity);

            $leadID = $entity->apiAdd();

            if ($leadID) {
                Helper::log('created amo deal - ' . $leadID);

                $productCatalogStatus = $amo->account->productCatalogStatus();

                if (!empty($productCatalogStatus['is_enabled'])
                    && !empty($productCatalogStatus['catalog_id'])
                ) {
                    Helper::log('start set amo `Products`');

                    $productRows = $this->generateProductRows($order, $amo, $productCatalogStatus['catalog_id']);

                    if ($productRows) {
                        $links = [];

                        foreach ($productRows as $productID => $quantity) {
                            $link = $amo->links;
                            $link['from'] = 'leads';
                            $link['from_id'] = $leadID;
                            $link['quantity'] = $quantity;
                            $link['to'] = 'catalog_elements';
                            $link['to_catalog_id'] = $productCatalogStatus['catalog_id'];
                            $link['to_id'] = $productID;

                            $links[] = $link;
                        }

                        $amo->links->apiLink($links);
                    }

                    $entity = $amo->lead;
                    $entity['sale'] = $data['total'];
                    $entity->apiUpdate($leadID);

                    Helper::log('end set amo `Products`');
                } else {
                    Helper::log('amo `Products` not enabled');
                }

                update_post_meta($data['id'], '_wc_amo_lead_id', $leadID);
                $order->add_order_note(esc_html__('Added lead in CRM #', 'wc-amocrm-integration') . $leadID);

                // create task to deal
                if (!empty($taskFields) && !empty($taskFields['text'])) {
                    $task = $amo->task;

                    $task['element_id'] = $leadID;
                    $task['element_type'] = Task::TYPE_LEAD;
                    $task['task_type'] = !empty($taskFields['type']) ? $taskFields['type'] : 1; //Звонок

                    if (!empty($leadResponsible)) {
                        $task['responsible_user_id'] = $leadResponsible;
                    } elseif (!empty($taskFields['responsible_user_id'])) {
                        $task['responsible_user_id'] = $taskFields['responsible_user_id'];
                    }

                    $completeCorrect = 3600;

                    if (!empty($taskFields['complete_till_at'])) {
                        $completeCorrect = (int) $taskFields['complete_till_at'] * 60;
                    }

                    $task->setCompleteTill(date('Y-m-d H:i', strtotime('+' . $completeCorrect . ' seconds')));
                    $task['text'] = $taskFields['text'];

                    $task->apiAdd();
                }
            }

            $note = $amo->note;
            $note['text'] = $this->generateNote($order);

            if (!Helper::isVerify()) {
                if ($note['text']) {
                    $note['text'] = Helper::nonVerifyText()
                        . "\n"
                        . $note['text'];
                } else {
                    $note['text'] = Helper::nonVerifyText();
                }
            }

            $note['note_type'] = Note::COMMON;
            $note['element_type'] = Note::TYPE_LEAD;
            $note['element_id'] = $leadID;

            $note->apiAdd();

            if ($order->get_customer_note()) {
                $note = $amo->note;
                $note['text'] = $order->get_customer_note();

                $note['note_type'] = Note::COMMON;
                $note['element_type'] = Note::TYPE_LEAD;
                $note['element_id'] = $leadID;

                $note->apiAdd();
            }

            $additionalNote = isset($settings['additional_lead_note'])
                ? self::prepareFields(['note_content' => $settings['additional_lead_note']], $orderData)
                : [];

            if (!empty($additionalNote['note_content'])) {
                $note = $amo->note;
                $note['text'] = $additionalNote['note_content'];

                $note['note_type'] = Note::COMMON;
                $note['element_type'] = Note::TYPE_LEAD;
                $note['element_id'] = $leadID;

                $note->apiAdd();
            }

            $entity = $amo->contact;

            // set specified contact id
            if (!empty($leadFields['contact_id'])) {
                Helper::log('specified constant contact id - ' . $leadFields['contact_id']);

                $existsContact = $amo->contact->apiList(['id' => $leadFields['contact_id']]);

                if ($existsContact) {
                    $existsContact = current($existsContact);
                    Helper::log('specified contact found by id');

                    $leadIds = [$leadID];

                    if (!empty($existsContact['linked_leads_id'])) {
                        $leadIds = array_merge($existsContact['linked_leads_id'], $leadIds);
                    }

                    $entity = $amo->contact;

                    $entity->setLinkedLeadsId($leadIds);
                    $entity->apiUpdate($existsContact['id']);
                } else {
                    Helper::log('specified contact not found by id - lead was left without contact');
                }

                return true;
            }

            // create/find company
            if (!empty($companyFields)) {
                $entityCompany = $amo->company;

                $searchCompanyEmail = '';
                $searchCompanyPhone = '';

                foreach ($companyFields as $key => $value) {
                    if (in_array($key, array_keys($crmFields->companies))) {
                        $entityCompany[$key] = $value;
                    } elseif (in_array($key, $additionalFieldCompaniesIds) && !empty($value)) {
                        if ($prepareAdditionalCompanyFields[$key] == 'EMAIL') {
                            $searchCompanyEmail = $value;
                        } elseif ($prepareAdditionalCompanyFields[$key] == 'PHONE') {
                            $searchCompanyPhone = $value;
                        }

                        if (is_array($value) && !is_numeric(reset(array_keys($value)))) {
                            $resultValue = [];

                            foreach ($value as $subKey => $subValue) {
                                $resultValue[] = [
                                    'value' => $subValue,
                                    'subtype' => $subKey
                                ];
                            }

                            $entityCompany->addCustomField($key, $resultValue);
                        } else {
                            $entityCompany->addCustomField($key, $value, in_array($prepareAdditionalCompanyFields[$key], ['EMAIL', 'PHONE']) ? 'WORK' : null);
                        }
                    }
                }

                $existsCompany = false;

                if ($searchCompanyEmail) {
                    $existsCompany = $amo->company->apiList(['query' => $searchCompanyEmail]);
                }

                if (!$existsCompany && $searchCompanyPhone) {
                    $existsCompany = $amo->company->apiList(['query' => $searchCompanyPhone]);
                }

                if (!$existsCompany && !empty($companyFields['name'])) {
                    $existsCompany = $amo->company->apiList(['query' => $companyFields['name']]);
                }

                // Exists company is found
                if ($existsCompany) {
                    $existsCompany = current($existsCompany);
                    $entity['linked_company_id'] = $existsCompany['id'];
                } elseif (!empty($entityCompany['name'])) {
                    $companyId = $entityCompany->apiAdd();

                    if ($companyId) {
                        $entity['linked_company_id'] = $companyId;
                    }
                }
            }

            $searchContactEmail = '';
            $searchContactPhone = '';

            foreach ($contactFields as $key => $value) {
                if (in_array($key, array_keys($crmFields->contacts))) {
                    if ($key == 'responsible_user_id' && !empty($leadResponsible)) {
                        $value = $leadResponsible;
                    }

                    $entity[$key] = $value;
                } elseif (in_array($key, $additionalFieldIds) && !empty($value)) {
                    if ($prepareAdditionalContactFields[$key] == 'EMAIL') {
                        $searchContactEmail = $value;
                    } elseif ($prepareAdditionalContactFields[$key] == 'PHONE') {
                        $searchContactPhone = str_replace(' ', '', $value);
                    }

                    if (is_array($value) && !is_numeric(reset(array_keys($value)))) {
                        $resultValue = [];

                        foreach ($value as $subKey => $subValue) {
                            $resultValue[] = [
                                'value' => $subValue,
                                'subtype' => $subKey
                            ];
                        }

                        $entity->addCustomField($key, $resultValue);
                    } else {
                        $entity->addCustomField($key, $value, in_array($prepareAdditionalContactFields[$key], ['EMAIL', 'PHONE']) ? 'WORK' : null);
                    }
                }
            }

            $existsContact = false;

            if ($searchContactEmail) {
                Helper::log('trying to find a contact by email', $searchContactEmail);

                $existsContact = $amo->contact->apiList(['query' => $searchContactEmail]);

                if ($existsContact) {
                    Helper::log('contact found', current($existsContact)['id']);
                } else {
                    Helper::log('contact not found');
                }
            } else {
                Helper::log('contact data without mail');
            }

            if (!$existsContact && $searchContactPhone) {
                Helper::log('trying to find a contact by phone', $searchContactPhone);

                $existsContact = $amo->contact->apiList(['query' => $searchContactPhone]);

                if ($existsContact) {
                    Helper::log('contact found', current($existsContact)['id']);
                } else {
                    Helper::log('contact not found');
                }
            }

            // Exists contact is found
            if ($existsContact) {
                $existsContact = current($existsContact);
                $leadIds = [$leadID];

                if (!empty($existsContact['linked_leads_id'])) {
                    $leadIds = array_merge($existsContact['linked_leads_id'], $leadIds);
                }

                // if enable do no update - just connect a new lead
                if (
                    isset($settings['do_not_update_contact']) &&
                    (int) $settings['do_not_update_contact'] === 1
                ) {
                    Helper::log('do_not_update_contact is enabled');

                    $entity = $amo->contact;
                }

                $entity->setLinkedLeadsId($leadIds);
                $entity->apiUpdate($existsContact['id']);

                Helper::log('update contact', $existsContact['id']);
            } else {
                $entity->setLinkedLeadsId($leadID);
                $contactID = $entity->apiAdd();
                Helper::log('created new contact', $contactID);
            }
        } catch (\Exception $e) {
            Helper::log('error when lead create', $e, 'error');

            if (defined('WP_DEBUG') && WP_DEBUG === true) {
                printf(
                    'Error (%d): %s' . "\n",
                    (int) $e->getCode(),
                    esc_html($e->getMessage())
                );
            }
        }
    }

    private function leadUpdate($orderID)
    {
        $leadID = get_post_meta($orderID, '_wc_amo_lead_id', true);

        if (!$leadID) {
            Helper::log('empty lead by order ID - ' . $orderID);

            return;
        }

        $settings = get_option(Bootstrap::OPTIONS_KEY);
        $crmFields = new CrmFields();
        $additionalFields = get_option(Bootstrap::OPTIONS_CUSTOM_FIELDS);
        $order = wc_get_order($orderID);
        $this->order = $order;

        Helper::log('start prepareOrderData - ' . $orderID);
        $data = $order->get_data();
        $orderData = $this->prepareOrderData($data, $order);
        Helper::log('end prepareOrderData - ' . $orderID);

        Helper::log('start prepareFields lead - ' . $orderID);
        $leadFields = $this->prepareFields($settings['lead'], $orderData);
        $leadFields = Helper::resolveSelectMultiSelect($additionalFields['leads'] , $leadFields);
        $leadFields = apply_filters('itglx_wcamo_lead_fields', $leadFields, $order);
        Helper::log('end prepareFields lead - ' . $orderID);

        $updateLeadFields = [];

        if (
            (!isset($settings['do_not_post_status_changes']) || $settings['do_not_post_status_changes'] !== 'true') &&
            !empty($settings['lead_statuses'])
        ) {
            $status = $order->get_status();

            if (!empty($settings['lead_statuses'][$status])) {
                $explodeCurrentStatus = explode('.', $settings['lead_statuses'][$status]);

                if (count($explodeCurrentStatus) > 1) {
                    $updateLeadFields['pipeline_id'] = $explodeCurrentStatus[0];
                    $updateLeadFields['status_id'] = $explodeCurrentStatus[1];
                } else {
                    $updateLeadFields['status_id'] = $settings['lead_statuses'][$status];
                }
            }
        }

        if (!empty($settings['lead']['update'])) {
            foreach (array_keys($settings['lead']['update']) as $fieldID) {
                if (!isset($leadFields[$fieldID]) || $leadFields[$fieldID] === '') {
                    continue;
                }

                $updateLeadFields[$fieldID] = $leadFields[$fieldID];
            }
        }

        if (
            empty($updateLeadFields) &&
            (!isset($settings['resend_product_list']) || $settings['resend_product_list'] !== 'true')
        ) {
            return;
        }

        try {
            $amo = new ClientOauthWc($settings['domain'], 'empty', $settings);

            if (isset($settings['resend_product_list']) && $settings['resend_product_list'] === 'true') {
                $productCatalogStatus = $amo->account->productCatalogStatus();

                if (
                    !empty($productCatalogStatus['is_enabled']) &&
                    !empty($productCatalogStatus['catalog_id'])
                ) {
                    $productRows = $this->generateProductRows($order, $amo, $productCatalogStatus['catalog_id']);

                    if ($productRows) {
                        $currentLinks = $amoProduct = $amo->links->apiList([
                            'from' => 'leads',
                            'from_id' => $leadID,
                            'to' => 'catalog_elements',
                            'to_catalog_id' => $productCatalogStatus['catalog_id']
                        ]);

                        if ($currentLinks) {
                            $unlinkList = [];

                            foreach ($currentLinks as $currentLink) {
                                $link = $amo->links;
                                $link['from'] = 'leads';
                                $link['from_id'] = $leadID;
                                $link['to'] = 'catalog_elements';
                                $link['to_catalog_id'] = $currentLink['to_catalog_id'];
                                $link['to_id'] = $currentLink['to_id'];

                                $unlinkList[] = $link;
                            }

                            if ($unlinkList) {
                                $amo->links->apiUnlink($unlinkList);
                            }
                        }

                        $links = [];

                        foreach ($productRows as $productID => $quantity) {
                            $link = $amo->links;
                            $link['from'] = 'leads';
                            $link['from_id'] = $leadID;
                            $link['quantity'] = $quantity;
                            $link['to'] = 'catalog_elements';
                            $link['to_catalog_id'] = $productCatalogStatus['catalog_id'];
                            $link['to_id'] = $productID;

                            $links[] = $link;
                        }

                        $amo->links->apiLink($links);
                    }
                }

                $updateLeadFields['sale'] = $data['total'];
            }

            if (empty($updateLeadFields) ) {
                return;
            }

            $entity = $amo->lead;
            $additionalFieldIds = [];

            foreach ($additionalFields['leads'] as $field) {
                $additionalFieldIds[] = $field['id'];
            }

            if (isset($updateLeadFields['pipeline_id'])) {
                $entity['pipeline_id'] = $updateLeadFields['pipeline_id'];
                $entity['status_id'] = $updateLeadFields['status_id'];
            } else {
                $entity['status_id'] = $updateLeadFields['status_id'];
            }

            foreach ($updateLeadFields as $key => $value) {
                if (in_array($key, array_keys($crmFields->leads)) || $key === 'sale') {
                    if ($key == 'responsible_user_id') {
                        continue;
                    }

                    $entity[$key] = $value;
                } elseif (in_array($key, $additionalFieldIds) && !empty($value)) {
                    if (is_array($value) && !is_numeric(reset(array_keys($value)))) {
                        $resultValue = [];

                        foreach ($value as $subKey => $subValue) {
                            $resultValue[] = [
                                'value' => $subValue,
                                'subtype' => $subKey
                            ];
                        }

                        $entity->addCustomField($key, $resultValue);
                    } else {
                        $entity->addCustomField($key, $value);
                    }
                }
            }

            Helper::log('lead entity', $entity);

            $response = $entity->apiUpdate($leadID);

            if (!empty($response)) {
                Helper::log('update lead error - ' . $leadID . ', order - ' . $orderID, $response, 'error');
            } else {
                Helper::log('update lead success - ' . $leadID . ', order - ' . $orderID, $response);
            }
        } catch (\Exception $e) {
            Helper::log('error when lead update', $e, 'error');

            if (defined('WP_DEBUG') && WP_DEBUG === true) {
                printf(
                    'Error (%d): %s' . "\n",
                    (int) $e->getCode(),
                    esc_html($e->getMessage())
                );
            }
        }
    }

    private function prepareOrderData($data, $order)
    {
        $returnData = [];

        foreach ($data['billing'] as $key => $value) {
            $returnData['billing_' . $key] = $value;
        }

        foreach ($data['shipping'] as $key => $value) {
            $returnData['shipping_' . $key] = $value;
        }

        if (!empty($data['meta_data'])) {
            foreach ($data['meta_data'] as $orderMeta) {
                if (!method_exists($orderMeta, 'get_data')) {
                    continue;
                }

                $metaData = $orderMeta->get_data();

                // ignore meta data is object or is array
                if (
                    isset($metaData['value']) &&
                    (
                        is_object($metaData['value']) ||
                        is_array($metaData['value'])
                    )
                ) {
                    continue;
                }

                if (!empty($metaData['value']) && Helper::isJson($metaData['value'])) {
                    $jsonValue = json_decode($metaData['value'], true);

                    if (!empty($jsonValue['url'])) {
                        $metaData['value'] = $jsonValue['url'];
                    }

                    unset($jsonValue);
                }

                // Supports `Booster for WooCommerce` custom checkout fields
                // Supports `WooCommerce Checkout Field Editor` custom checkout fields
                if ($metaData['key'][0] == '_') {
                    $returnData[mb_substr($metaData['key'], 1)] = $metaData['value'];
                } else {
                    $returnData[$metaData['key']] = $metaData['value'];
                }
            }
        }


        // Supports `WooCommerce Checkout Add-Ons` custom checkout fields
        if (!empty($data['fee_lines'])) {
            foreach ($data['fee_lines'] as $fee) {
                $feeData = $fee->get_data();

                if (!empty($feeData['meta_data'])) {
                    $preparedFeeMeta = [];

                    foreach ($feeData['meta_data'] as $feeMeta) {
                        $metaData = $feeMeta->get_data();

                        $preparedFeeMeta[$metaData['key']] = $metaData['value'];
                    }

                    if (isset($preparedFeeMeta['_wc_checkout_add_on_id'])) {
                        $returnData['wc_checkout_add_on_' . $preparedFeeMeta['_wc_checkout_add_on_id']]
                            = isset($preparedFeeMeta['_wc_checkout_add_on_label'])
                            ? $preparedFeeMeta['_wc_checkout_add_on_label']
                            : '';
                    }
                }
            }
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

        // Set ga client id
        $returnData['gaClientID'] = '';

        if (!empty($_COOKIE['_ga'])) {
            $clientId = explode('.', wp_unslash($_COOKIE['_ga']));
            $returnData['gaClientID'] = $clientId[2] . '.' . $clientId[3];
        }

        // Set yandex client id
        $returnData['yandexClientID'] = '';

        if (!empty($_COOKIE['_ym_uid'])) {
            $returnData['yandexClientID'] = wp_unslash($_COOKIE['_ym_uid']);
        }

        $returnData['order_number'] = $order->get_order_number();
        $returnData['order_create_date'] = $order->get_date_created()->date_i18n('d.m.Y');

        // support use payment method title in fields
        if (wc_get_payment_gateway_by_order($order)) {
            $returnData['payment_method_title'] = wc_get_payment_gateway_by_order($order)->title;
            $returnData['payment_method_id'] = wc_get_payment_gateway_by_order($order)->id;
        } else {
            $returnData['payment_method_title'] = '';
            $returnData['payment_method_id'] = '';
        }

        // support use shipping method title in fields
        if ($order->get_shipping_method()) {
            $returnData['shipping_method_title'] = $order->get_shipping_method();
            $returnData['shipping_method_id'] = current($order->get_shipping_methods())->get_method_id();
        } else {
            $returnData['shipping_method_title'] = '';
            $returnData['shipping_method_id'] = '';
        }

        // support use shipping price in fields
        if ($order->get_shipping_method() && $order->get_shipping_total()) {
            $returnData['shipping_price'] = $order->get_shipping_total();
        } else {
            $returnData['shipping_price'] = '';
        }

        $returnData['order_admin_edit_link'] = admin_url() . 'post.php?post=' . $order->get_id() . '&action=edit';

        if (!empty($order->get_items())) {
            $items = $order->get_items();
            $firstItem = array_shift($items);

            $returnData['first_product_title'] = $firstItem->get_name();
        } else {
            $returnData['first_product_title'] = '';
        }

        // support Order Delivery Date Pro for WooCommerce Lite
        if (class_exists('order_delivery_date_lite')) {
            $date = get_post_meta($order->get_id(), '_orddd_lite_timestamp', true);

            if ($date) {
                $returnData['order_delivery_date_lite'] = date_i18n('d.m.Y', $date);
            } else {
                $returnData['order_delivery_date_lite'] = '';
            }
        }

        // support Order Delivery Date Pro for WooCommerce
        if (class_exists('order_delivery_date')) {
            $date = get_post_meta($order->get_id(), '_orddd_timestamp', true);

            if ($date) {
                $time = date('H:i', $date);

                if ($time !== '00:00' && $time !== '00:01') {
                    $returnData['order_delivery_date'] = date_i18n('d.m.Y H:i', $date);
                } else {
                    $returnData['order_delivery_date'] = date_i18n('d.m.Y', $date);
                }

                if (class_exists('orddd_common') && \orddd_common::orddd_get_order_timeslot($order->get_id())) {
                    $returnData['order_delivery_date'] .= ' '
                        . \orddd_common::orddd_get_order_timeslot($order->get_id());
                }
            } else {
                $returnData['order_delivery_date'] = '';
            }
        }

        $skuList = [];
        $titlesList = [];
        $productCatTitles = [];
        $totalWeight = 0;

        foreach ($order->get_items() as $item) {
            if (version_compare(WC_VERSION, '4.4', '<')) {
                $product = $order->get_product_from_item($item);
            } else {
                $product = $item->get_product();
            }

            $titlesList[] = $item->get_name();

            if ($product instanceof \WC_Product) {
                if ($product->get_weight() > 0) {
                    $totalWeight += $item->get_quantity() * $product->get_weight();
                }

                if ($product->get_sku()) {
                    $skuList[] = $product->get_sku();
                }

                $terms = wp_get_object_terms($product->get_id(), 'product_cat', ['fields' => 'names']);

                if ($terms) {
                    foreach ($terms as $term) {
                        $productCatTitles[] = $term;
                    }
                }
            }
        }

        if ($totalWeight) {
            $returnData['order_total_weight'] = $totalWeight;
        } else {
            $returnData['order_total_weight'] = '';
        }

        $returnData['order_currency'] = $data['currency'];

        if ($skuList) {
            $returnData['order_product_sku_list'] = implode(', ', $skuList);
        } else {
            $returnData['order_product_sku_list'] = '';
        }

        if ($titlesList) {
            $returnData['order_product_titles_list'] = implode(', ', $titlesList);
        } else {
            $returnData['order_product_titles_list'] = '';
        }

        if ($productCatTitles) {
            $productCatTitles = array_unique($productCatTitles);
            $returnData['order_product_cat_name_list'] = implode(', ', $productCatTitles);
        } else {
            $returnData['order_product_cat_name_list'] = '';
        }

        // Supports used order status
        $returnData['order_status_id'] = $order->get_status();
        $returnData['order_status_title'] = \wc_get_order_status_name($order->get_status());

        // Supports used coupon list
        $returnData['order_coupon_list'] = '';

        // method WC 3.7+
        if (method_exists($order, 'get_coupon_codes')) {
            if (!empty($order->get_coupon_codes())) {
                $returnData['order_coupon_list'] = implode(', ', $order->get_coupon_codes());
            }
        // method before WC 3.7+
        } elseif (!empty($order->get_used_coupons())) {
            $returnData['order_coupon_list'] = implode(', ', $order->get_used_coupons());
        }

        $returnData['order_comments'] = isset($data['customer_note']) ? $data['customer_note'] : '';
        $returnData['order_total_without_shipping_and_tax'] = $order->get_subtotal();

        foreach ($data as $key => $value) {
            // no rewrite
            if (isset($returnData[$key])) {
                continue;
            }

            // no complex data
            if (is_object($value) || is_array($value)) {
                continue;
            }

            $returnData[$key] = (string) $value;
        }

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
            if (is_object($fieldValue)) {
                continue;
            }

            if (is_array($fieldValue)) {
                foreach ($fieldValue as $keySubField => $fieldSubValue) {
                    if (is_object($fieldSubValue)) {
                        continue;
                    }

                    $value = trim(str_replace($keys, $values, $fieldSubValue));
                    $value = $this->productTitlesByProductCatProcess($value);
                    $value = $this->metaKeysProcess($value);

                    if ($value) {
                        // Remove not exists shortcodes
                        $value = trim(preg_replace('/\[.*?\]/is', '', $value));

                        if ($value) {
                            $returnFields[$keyField][$keySubField] = $value;
                        }
                    }
                }
            } else {
                $value = trim(str_replace($keys, $values, $fieldValue));
                $value = $this->productTitlesByProductCatProcess($value);
                $value = $this->metaKeysProcess($value);

                if ($value) {
                    // Remove not exists shortcodes
                    $value = trim(preg_replace('/\[.*?\]/is', '', $value));

                    if ($value) {
                        $returnFields[$keyField] = $value;
                    }
                }
            }
        }

        return $returnFields;
    }

    private function productTitlesByProductCatProcess($value)
    {
        preg_match_all('/\[(order_product_titles_by_product_cat_.+?)\]/', $value, $matches);

        if (!empty($matches[1])) {
            $products = [];

            foreach ($this->order->get_items() as $item) {
                if (version_compare(WC_VERSION, '4.4', '<')) {
                    $product = $this->order->get_product_from_item($item);
                } else {
                    $product = $item->get_product();
                }

                if ($product instanceof \WC_Product) {
                    $products[] = $product;
                }
            }

            foreach ($matches[1] as $key) {
                $term = str_replace('order_product_titles_by_product_cat_', '', $key);
                $resultValue = [];

                foreach ($products as $product) {
                    $terms = wp_get_object_terms($product->get_id(), 'product_cat', ['fields' => 'ids']);

                    if ($terms && in_array($term, $terms)) {
                        $resultValue[] = $product->get_title();
                    }
                }

                $value = trim(str_replace('[' . $key . ']', implode(', ', $resultValue), $value));
            }
        }

        return $value;
    }


    private function generateNote($order)
    {
        $settings = get_option(Bootstrap::OPTIONS_KEY);
        $orderData = $order->get_data();

        $productRows = '';

        // add admin order link if enable
        if (isset($settings['add_admin_order_link_note'])
            && $settings['add_admin_order_link_note'] === 'true'
        ) {
            $productRows = admin_url()
                . 'post.php?post='
                . $order->get_id()
                . '&action=edit'
                . "\n";
        }

        if (class_exists('order_delivery_date_lite')) {
            $date = get_post_meta($order->get_id(), '_orddd_lite_timestamp', true);

            if ($date) {
                $productRows .= esc_html__('Delivery Date', 'order-delivery-date')
                    . ': '
                    . date_i18n('d.m.Y', $date)
                    . "\n";
            }
        }

        if (class_exists('order_delivery_date')) {
            $date = get_post_meta($order->get_id(), '_orddd_timestamp', true);

            if ($date) {
                $time = date('H:i', $date);

                if ($time !== '00:00' && $time !== '00:01') {
                    $showDate = date_i18n('d.m.Y H:i', $date);
                } else {
                    $showDate = date_i18n('d.m.Y', $date);
                }

                if (class_exists('orddd_common') && \orddd_common::orddd_get_order_timeslot($order->get_id())) {
                    $showDate .= ' '
                        . \orddd_common::orddd_get_order_timeslot($order->get_id());
                }

                $productRows .= esc_html__('Delivery Date', 'order-delivery-date')
                    . ': '
                    . $showDate
                    . "\n";
            }
        }

        $hiddenOrderItemMeta = apply_filters(
            'woocommerce_hidden_order_itemmeta', [
                '_qty',
                '_tax_class',
                '_product_id',
                '_variation_id',
                '_line_subtotal',
                '_line_subtotal_tax',
                '_line_total',
                '_line_tax',
                'method_id',
                'cost',
                '_reduced_stock'
            ]
        );

        foreach ($order->get_items() as $item) {
            if (version_compare(WC_VERSION, '4.4', '<')) {
                $product = $order->get_product_from_item($item);
            } else {
                $product = $item->get_product();
            }

            $skuString = '';

            if ($product instanceof \WC_Product && $product->get_sku()) {
                $skuString .= ' / '
                    . esc_html__('SKU', 'wc-amocrm-integration')
                    . ': '
                    . $product->get_sku();
            }

            $productRows .= $item->get_name()
                . $skuString
                . ', '
                . esc_html__('Quantity', 'wc-amocrm-integration')
                . ': '
                . $item->get_quantity()
                . ', '
                . esc_html__('Summ', 'wc-amocrm-integration')
                . ': '
                . $item->get_total()
                . "\n";

            // show item metadata
            $metaData = $item->get_formatted_meta_data('');

            if ($metaData) {
                foreach ($metaData as $meta) {
                    if (in_array($meta->key, $hiddenOrderItemMeta, true)) {
                        continue;
                    }

                    $productRows .= wp_kses_post($meta->display_key . ' (' . $meta->key . ')')
                        . ': '
                        . wp_kses_post(wp_strip_all_tags($meta->display_value))
                        . "\n";
                }
            }

            $productRows .= "\n";
        }

        // Supports shipping method
        if ($order->get_shipping_method()) {
            $localPickupPlusData = [];

            if (function_exists('\\wc_local_pickup_plus')) {
                $localPickupPlus = \wc_local_pickup_plus();
                $localPickupPlusOrders = $localPickupPlus->get_orders_instance();
                $localPickupPlusData = $localPickupPlusOrders->get_order_pickup_data($order);
            }

            foreach($order->get_items('shipping') as $shippingItem){
                $productRows .= esc_html__('Shipping', 'wc-amocrm-integration')
                    . ': '
                    . $shippingItem->get_name()
                    . ', '
                    . esc_html__('Summ', 'wc-amocrm-integration')
                    . ': '
                    . $shippingItem->get_total()
                    . "\n";

                // show item metadata
                $metaData = $shippingItem->get_formatted_meta_data('');

                if ($metaData) {
                    foreach ($metaData as $meta) {
                        if (in_array($meta->key, $hiddenOrderItemMeta, true)) {
                            continue;
                        }

                        $productRows .= wp_kses_post($meta->display_key)
                            . ': '
                            . wp_kses_post(wp_strip_all_tags($meta->display_value))
                            . "\n";
                    }
                }

                if (!empty($localPickupPlusData[$shippingItem->get_id()])) {
                    foreach ($localPickupPlusData[$shippingItem->get_id()] as $label => $value) {
                        $productRows .= wp_kses_post($label)
                            . ': '
                            . wp_kses_post(wp_strip_all_tags($value))
                            . "\n";
                    }
                }

                $productRows .= "\n";
            }
        }

        // Supports payment method
        if (\wc_get_payment_gateway_by_order($order)) {
            $productRows .= esc_html__('Payment', 'wc-amocrm-integration')
                . ': '
                . \wc_get_payment_gateway_by_order($order)->title
                . "\n"
                . "\n";
        }

        foreach (WC()->countries->get_address_fields(WC()->countries->get_base_country(), 'billing' . '_') as $value => $field) {
            if (!empty($orderData['billing'][str_replace('billing_', '', $value)])) {
                $productRows .= $field['label']
                    . ': '
                    . $orderData['billing'][str_replace('billing_', '', $value)]
                    . "\n";
            }
        }

        return $productRows;
    }

    private function generateProductRows($order, $amo, $catalogID)
    {
        Helper::log('start `generateProductRows`, order - ' . $order->get_id());
        $productRows = [];

        $priceFieldID = 0;
        $customFields = get_option(Bootstrap::OPTIONS_CUSTOM_FIELDS);

        if (empty($customFields[$catalogID])) {
            return [];
        }

        foreach ($customFields[$catalogID] as $field) {
            if ($field['code'] === 'PRICE') {
                $priceFieldID = $field['id'];
            }
        }

        foreach ($order->get_items(['line_item']) as $item) {
            $itemName = $item->get_name();

            if (version_compare(WC_VERSION, '4.4', '<')) {
                $product = $order->get_product_from_item($item);
            } else {
                $product = $item->get_product();
            }

            if ($product instanceof \WC_Product && $product->get_sku()) {
                $itemName .= ' / '
                    . esc_html__('SKU', 'wc-bitrix24-integration')
                    . ': '
                    . $product->get_sku();
            }

            $amoProduct = $amo->catalog_element->apiList([
                'catalog_id' => $catalogID,
                'term' => $itemName
            ]);

            if (!$amoProduct) {
                $element = $amo->catalog_element;
                $element['catalog_id'] = $catalogID;
                $element['name'] = $itemName;
                $element->addCustomField($priceFieldID, round($item->get_total() / $item->get_quantity()));

                $amoProduct = $element->apiAdd();
            } else {
                $amoProduct = current($amoProduct)['id'];
            }

            $productRows[$amoProduct] = $item->get_quantity();
        }

        // Supports shipping method
        if ($order->get_shipping_method()) {
            $amoProduct = $amo->catalog_element->apiList([
                'catalog_id' => $catalogID,
                'term' => esc_html__('Shipping', 'wc-bitrix24-integration')
                    . ' - '
                    . $order->get_shipping_method()
            ]);

            if (!$amoProduct) {
                $element = $amo->catalog_element;
                $element['catalog_id'] = $catalogID;
                $element['name'] = esc_html__('Shipping', 'wc-bitrix24-integration')
                    . ' - '
                    . $order->get_shipping_method();
                $element->addCustomField($priceFieldID, round($order->get_shipping_total()));

                $amoProduct = $element->apiAdd();
            } else {
                $amoProduct = current($amoProduct)['id'];
            }

            $productRows[$amoProduct] = 1;
        }

        Helper::log('prepared product list', $productRows);
        Helper::log('end `generateProductRows`, order - ' . $order->get_id());

        return $productRows;
    }

    private function metaKeysProcess($value)
    {
        preg_match_all('/\[(meta_key_.+?)\]/', $value, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $metaKey) {
                $metaValue = get_post_meta($this->order->get_id(), str_replace('meta_key_', '', $metaKey), true);

                $value = trim(str_replace('[' . $metaKey . ']', $metaValue, $value));
            }
        }

        return $value;
    }

    private function dropEmptyShortcode($value)
    {
        preg_match_all('/\[(.+?)\]/', $value, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $metaKey) {
                $value = trim(str_replace('[' . $metaKey . ']', '', $value));
            }
        }

        return $value;
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
            && Helper::hasToken();
    }

    private function __clone()
    {
    }
}

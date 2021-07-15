<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Includes;

class CrmFields
{
    public static $breakFields = [
        'lead' => [
            'price',
            'sale',
            'status_id'
        ],
        'contact' => [
        ],
        'company' => [
        ],
        'task' => [
        ]
    ];

    public $leads;

    public $contacts;

    public function __construct()
    {
        $this->leads = [
            'name' => [
                'required' => true,
                'type' => esc_html__('String', 'wc-amocrm-integration'),
                'name' => esc_html__('Lead name', 'wc-amocrm-integration')
            ],
            // 'date_create'
            // 'last_modified'
            'status_id' => [
                'required' => false,
                'type' => esc_html__('Numeric', 'wc-amocrm-integration'),
                'name' => esc_html__('Pipeline / Status', 'wc-amocrm-integration')
            ],
            // 'pipeline_id'
            'price' => [
                'required' => false,
                'type' => esc_html__('Numeric', 'wc-amocrm-integration'),
                'name' => esc_html__('Deal budget', 'wc-amocrm-integration')
            ],
            'responsible_user_id' => [
                'required' => false,
                'type' => esc_html__('Numeric', 'wc-amocrm-integration'),
                'name' => esc_html__('Responsible', 'wc-amocrm-integration'),
                'description' => esc_html__(
                    'you can specify several, separated by commas, then the requests will be distributed sequentially',
                    'wc-amocrm-integration'
                )
            ],
            'contact_id' => [
                'required' => false,
                'type' => esc_html__('Numeric', 'wc-amocrm-integration'),
                'name' => esc_html__('Contact ID', 'wc-amocrm-integration'),
                'description' => esc_html__(
                    'you can specify the contact id, which will always be assigned to the lead',
                    'wc-amocrm-integration'
                )
            ],
            // 'request_id'
            // 'linked_company_id'
            'tags' => [
                'required' => false,
                'type' => esc_html__('String', 'wc-amocrm-integration'),
                'name' => esc_html__('Tags', 'wc-amocrm-integration')
            ]
            // 'visitor_uid'
        ];

        $this->contacts = [
            'name' => [
                'required' => true,
                'type' => esc_html__('String', 'wc-amocrm-integration'),
                'name' => esc_html__('Contact name', 'wc-amocrm-integration')
            ],
            // 'request_id'
            // 'date_create'
            // 'last_modified'
            'responsible_user_id' => [
                'required' => false,
                'type' => esc_html__('Numeric', 'wc-amocrm-integration'),
                'name' => esc_html__('Responsible', 'wc-amocrm-integration')
            ],
            // 'linked_leads_id'
            // 'linked_company_id'
            'tags' => [
                'required' => false,
                'type' => esc_html__('String', 'wc-amocrm-integration'),
                'name' => esc_html__('Tags', 'wc-amocrm-integration')
            ]
        ];

        $this->companies = [
            'name' => [
                'required' => true,
                'type' => esc_html__('String', 'wc-amocrm-integration'),
                'name' => esc_html__('Company name', 'wc-amocrm-integration')
            ],
            // 'request_id'
            // 'date_create'
            // 'last_modified'
            'responsible_user_id' => [
                'required' => false,
                'type' => esc_html__('Numeric', 'wc-amocrm-integration'),
                'name' => esc_html__('Responsible', 'wc-amocrm-integration')
            ],
            // 'linked_leads_id'
            // 'linked_company_id'
            'tags' => [
                'required' => false,
                'type' => esc_html__('String', 'wc-amocrm-integration'),
                'name' => esc_html__('Tags', 'wc-amocrm-integration')
            ]
        ];

        $this->task = [
            'text' => [
                'required' => true,
                'type' => esc_html__('String', 'wc-amocrm-integration'),
                'name' => esc_html__('Text', 'wc-amocrm-integration')
            ],
            // 'request_id'
            // 'date_create'
            // 'last_modified'
            'responsible_user_id' => [
                'required' => false,
                'type' => esc_html__('Numeric', 'wc-amocrm-integration'),
                'name' => esc_html__('Responsible', 'wc-amocrm-integration')
            ],
            // 'linked_leads_id'
            // 'linked_company_id'
            'type' => [
                'required' => false,
                'type' => esc_html__('String', 'wc-amocrm-integration'),
                'name' => esc_html__('Type', 'wc-amocrm-integration'),
                'items' => [
                    1 => esc_html__('Call', 'wc-amocrm-integration'),
                    2 => esc_html__('Meeting', 'wc-amocrm-integration')
                ]
            ],
            'complete_till_at' => [
                'required' => false,
                'type' => esc_html__('Numeric', 'wc-amocrm-integration'),
                'name' => esc_html__(
                    'Number of minutes for deadline',
                    'wc-amocrm-integration'
                ),
                'description' => esc_html__(
                    'after how many minutes after creation the task should be completed',
                    'wc-amocrm-integration'
                )
            ]
        ];
    }

    private function __clone()
    {
    }
}

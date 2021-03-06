<?php

require_once __DIR__ . '/../../vendor/autoload.php';

try {
    $amo = new \AmoCRM\Client(getenv('DOMAIN'), getenv('LOGIN'), getenv('HASH'));

    // Добавление неразобранных заявок
    // Метод позволяет добавлять неразобранные заявки по одной или пакетно

    $unsorted = $amo->unsorted;
    $unsorted['source'] = 'www.my-awesome-site.com';
    $unsorted['source_uid'] = null;

    // Данные заявки (зависят от категории)
    $unsorted['source_data'] = [
        'data' => [
            'name_1' => [
                'type' => 'text',
                'id' => 'name',
                'element_type' => '1',
                'name' => 'Name',
                'value' => 'Some name',
            ]
        ],
        'form_id' => 318,
        'form_type' => 1,
        'origin' => [
            'ip' => '10.4.4.43',
            'datetime' => 'Tue Nov 03 2015 13:02:24 GMT+0300 (Russia Standard Time)',
            'referer' => '',
        ],
        'date' => 1446544971,
        'from' => 'some-url.com',
        'form_name' => 'My name for form',
    ];

    // Добавление контакта или компании которая будет создана после одобрения заявки.
    $contact = $amo->contact;
    $contact['name'] = 'Create contact from this data';

    // Примечания, которые появятся в контакте после принятия неразобранного
    $note = $amo->note;
    $note['element_type'] = \AmoCRM\Models\Note::TYPE_CONTACT; // 1 - contact, 2 - lead
    $note['note_type'] = \AmoCRM\Models\Note::COMMON; // @see https://developers.amocrm.ru/rest_api/notes_type.php
    $note['text'] = 'Примечания, которые появятся в контакте после принятия неразобранного';
    $contact['notes'] = $note;

    // Присоединение контакта к неразобранному
    $unsorted->addDataContact($contact);

    // Добавление неразобранной заявки с типом FORMS
    $unsortedId = $unsorted->apiAddForms();
    print_r($unsortedId);

} catch (\AmoCRM\Exception $e) {
    printf('Error (%d): %s' . PHP_EOL, $e->getCode(), $e->getMessage());
}

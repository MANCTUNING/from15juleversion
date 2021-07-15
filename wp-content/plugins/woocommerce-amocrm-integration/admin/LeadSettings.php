<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Admin;

use Itgalaxy\Wc\AmoCrm\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\AmoCrm\Integration\Includes\CrmFields;
use Itgalaxy\Wc\AmoCrm\Integration\Includes\CRM;

class LeadSettings
{
    private static $instance = false;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct()
    {

    }

    public function render($meta)
    {
        $additionalFields = get_option(Bootstrap::OPTIONS_CUSTOM_FIELDS);
        $renderFields = new RenderFields('lead', true);
        $currentValues = isset($meta['lead']) ? $meta['lead'] : [];
        ?>
        <table class="form-table">
            <?php
            $crmFields = new CrmFields();

            foreach ($crmFields->leads as $key => $field) {
                // Not show fields
                if (in_array($key, CrmFields::$breakFields['lead'])) {
                    continue;
                }
                ?>
                <tr>
                    <th>
                        <?php echo esc_html($field['name']); ?>
                        <?php
                        echo isset($field['required']) && $field['required'] === true
                            ? '<span style="color:red;"> * </span>'
                            : '';
                        ?>
                    </th>
                    <td>
                        <?php
                        $currentValue = isset($currentValues[$key]) ? $currentValues[$key] : '';

                        $renderFields->inputTextField(
                            $key,
                            $field['name'],
                            $currentValue,
                            $field,
                            (isset($currentValues['update']) ? $currentValues['update'] : [])
                        );

                        if ($key === 'responsible_user_id') {
                            $users = get_option(Bootstrap::OPTIONS_USERS);

                            if (empty($users)) {
                                CRM::updateInformation();

                                $users = get_option(Bootstrap::OPTIONS_USERS);
                            }

                            $showUsers = [];

                            foreach ($users as $user) {
                                $showUsers[] = $user['id'] . ' - ' . $user['login'];
                            }

                            echo '<br>'
                                . implode(', ', $showUsers);
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }

            if (!empty($additionalFields['leads']) && is_array($additionalFields['leads'])) {
                foreach ($additionalFields['leads'] as $field) {
                    ?>
                    <tr>
                        <th>
                            <?php echo esc_html($field['name']); ?>
                            <?php
                            echo isset($field['required']) && $field['required'] === true
                                ? '<span style="color:red;"> * </span>'
                                : '';
                            ?>
                        </th>
                        <td>
                            <?php
                            $key = $field['id'];

                            $currentValue = isset($currentValues[$key]) ? $currentValues[$key] : '';

                            // 9 - text area type
                            if ((int) $field['type_id'] === 9) {
                                $renderFields->textareaField(
                                    $key,
                                    $field['name'],
                                    $currentValue
                                );
                            } else {
                                if (!empty($field['subtypes'])) {
                                    foreach ($field['subtypes'] as $subType) {
                                        $currentSubValue = isset($currentValue[$subType['name']]) ? $currentValue[$subType['name']] : '';

                                        $renderFields->inputTextFieldSimple(
                                            $key . '][' . $subType['name'],
                                            $subType['title'],
                                            $currentSubValue
                                        );

                                        echo '<br><br>';
                                    }
                                } else {
                                    $renderFields->inputTextField(
                                        $key,
                                        $field['name'],
                                        $currentValue,
                                        [],
                                        (isset($currentValues['update']) ? $currentValues['update'] : [])
                                    );
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <?php
    }
}

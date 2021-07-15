<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Admin;

use Itgalaxy\Wc\AmoCrm\Integration\Includes\Bootstrap;
use Itgalaxy\Wc\AmoCrm\Integration\Includes\CrmFields;

class CompanySettings
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

        $renderFields = new RenderFields('company');
        $currentValues = isset($meta['company']) ? $meta['company'] : [];
        ?>
        <table class="form-table">
            <?php
            $crmFields = new CrmFields();

            foreach ($crmFields->companies as $key => $field) {
                // Not show fields
                if (in_array($key, CrmFields::$breakFields['company'])) {
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
                            $currentValue
                        );
                        ?>
                    </td>
                </tr>
                <?php
            }

            if (!empty($additionalFields['companies']) && is_array($additionalFields['companies'])) {
                foreach ($additionalFields['companies'] as $field) {
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

                                        $renderFields->inputTextField(
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
                                        $currentValue
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

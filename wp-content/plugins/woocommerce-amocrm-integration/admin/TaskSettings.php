<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Admin;

use Itgalaxy\Wc\AmoCrm\Integration\Includes\CrmFields;

class TaskSettings
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
        $renderFields = new RenderFields('task');
        $currentValues = isset($meta['task']) ? $meta['task'] : [];
        ?>
        <table class="form-table">
            <?php
            $crmFields = new CrmFields();

            foreach ($crmFields->task as $key => $field) {
                // Not show fields
                if (in_array($key, CrmFields::$breakFields['task'])) {
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

                        if (!empty($field['items'])) {
                            $renderFields->selectField(
                                $field['items'],
                                $key,
                                $field['name'],
                                $currentValue
                            );
                        } else {
                            $renderFields->inputTextField(
                                $key,
                                $field['name'],
                                $currentValue,
                                $field
                            );
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
        <?php
    }
}

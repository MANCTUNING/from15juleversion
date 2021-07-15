<?php
namespace Itgalaxy\Wc\AmoCrm\Integration\Admin;

use Itgalaxy\Wc\AmoCrm\Integration\Includes\Bootstrap;

class RenderFields
{
    public $fieldNameStart = '';
    public $withUpdate = false;

    public function __construct($type = '', $withUpdate = false)
    {
        $this->fieldNameStart .= $type . '[';
        $this->withUpdate = $withUpdate;
    }

    public function selectField($list, $name, $title, $currentValue)
    {
        ?>
        <select id="__<?php echo esc_attr($name); ?>"
            title="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]">
            <option value=""><?php esc_html_e('Not chosen', 'wc-amocrm-integration'); ?></option>
            <?php
            foreach ((array) $list as $value => $name) {
                echo '<option value="'
                    . esc_attr($value)
                    . '"'
                    . ($currentValue == $value ? ' selected' : '')
                    . '>'
                    . esc_html($value . ' - ' . $name)
                    . '</option>';
            }
            ?>
        </select>
        <?php
    }

    public function statusField($name, $currentValue)
    {
        ?>
        <select id="__<?php echo esc_attr($name); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]">
            <?php
            $pipelines = get_option(Bootstrap::OPTIONS_PIPELINES);

            foreach ($pipelines as $pipelineID => $pipeline) {
                if (empty($pipeline['statuses'])) {
                    continue;
                }

                echo '<optgroup label="' . esc_attr($pipeline['label']) . '">';

                foreach ($pipeline['statuses'] as $statusID => $status) {
                    $statusValue = $pipelineID . '.' . $statusID;
                    ?>
                    <option value="<?php echo esc_attr($statusValue); ?>"
                        <?php selected($currentValue, $statusValue); ?>>
                        <?php echo esc_attr($status['name']); ?>
                    </option>
                    <?php
                }

                echo '</optgroup>';
            }
            ?>
        </select>
        <?php
    }

    public function inputTextField($name, $title, $currentValue, $field = [], $update = [])
    {
        ?>
        <input id="__<?php echo esc_attr($name); ?>"
            type="text"
            class="large-text code"
            title="<?php echo esc_attr($title); ?>"
            placeholder="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
            <?php echo $this->withUpdate ? ' style="width: 95%;"' : ''; ?>
            value="<?php echo esc_attr($currentValue); ?>">
        <?php if ($this->withUpdate) { ?>
            <input id="__<?php echo esc_attr($name); ?>"
                   type="checkbox"
                   title="<?php echo esc_html__('Update when send status change event or order changed', 'wc-amocrm-integration'); ?>"
                   name="<?php echo esc_attr($this->fieldNameStart . 'update][' . $name); ?>]"
                   value="true"
                <?php echo in_array($name, array_keys($update)) ? 'checked' : ''; ?>>
        <?php } ?>
        <?php
        if (isset($field['description'])) { ?>
            <br><small><?php echo esc_html($field['description']); ?></small>
            <?php
        }
        ?>
        <?php
    }

    public function inputTextFieldSimple($name, $title, $currentValue, $field = [])
    {
        ?>
        <input id="__<?php echo esc_attr($name); ?>"
               type="text"
               class="large-text code"
               title="<?php echo esc_attr($title); ?>"
               placeholder="<?php echo esc_attr($title); ?>"
               name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
               value="<?php echo esc_attr($currentValue); ?>">
        <?php
        if (isset($field['description'])) { ?>
            <br><small><?php echo esc_html($field['description']); ?></small>
            <?php
        }
        ?>
        <?php
    }

    public function inputCheckboxField($name, $title, $currentValue)
    {
        ?>
        <input type="hidden"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
            value="N">
        <input id="__<?php echo esc_attr($name); ?>"
            type="checkbox"
            title="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
            value="Y"
            <?php echo $currentValue === 'Y' ? 'checked' : ''; ?>>
        <?php
    }

    public function textareaField($name, $title, $currentValue, $update = [])
    {
        ?>
        <textarea
            id="__<?php echo esc_attr($name); ?>"
            class="large-text code"
            title="<?php echo esc_attr($title); ?>"
            name="<?php echo esc_attr($this->fieldNameStart . $name); ?>]"
            <?php echo $this->withUpdate ? ' style="width: 95%;" ' : ''; ?>
            rows="4"><?php echo esc_attr($currentValue); ?></textarea>
        <?php if ($this->withUpdate) { ?>
            <input id="__<?php echo esc_attr($name); ?>"
                   type="checkbox"
                   title="<?php echo esc_html__('Update when send status change event or order changed', 'wc-amocrm-integration'); ?>"
                   name="<?php echo esc_attr($this->fieldNameStart . 'update][' . $name); ?>]"
                   value="true"
                <?php echo in_array($name, array_keys($update)) ? 'checked' : ''; ?>>
        <?php } ?>
        <?php
    }
}

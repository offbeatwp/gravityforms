<?php
namespace OffbeatWP\GravityForms\Hooks;

final class FilterButtonClass
{
    public static function buttonClass(array $formSettings, array $form): array
    {
        $input = $form['button']['class'] ?? null;
        $val = is_string($input) ? esc_attr($input) : '';

        $formSettings['Form Button']['button_class'] = '
            <tr id="form_button_text_setting" class="child_setting_row">
                <th>
                    ' . __('Button Class', 'gravityforms') . ' ' . gform_tooltip('form_button_class', '', true) . '
                </th>
                <td>
                    <input type="text" id="form_button_text_class" name="form_button_text_class" class="fieldwidth-3" value="' . $val . '" />
                </td>
            </tr>';

        return $formSettings;
    }

    public static function buttonClassProcess(array $updatedForm): array
    {
        $class = filter_input(INPUT_POST, 'form_button_text_class');
        $updatedForm['button']['class'] = ($class) ? stripslashes_deep((string)$class) : '';

        return $updatedForm;
    }
}

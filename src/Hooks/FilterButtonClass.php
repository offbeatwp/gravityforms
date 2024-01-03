<?php
namespace OffbeatWP\GravityForms\Hooks;

final class FilterButtonClass
{
    public static function buttonClass(array $formSettings, array $form): array
    {
        $styles = config('button.styles');

        if (is_iterable($styles)) {
            $options = [];
            $current = $form['button']['class'] ?? null;

            foreach ($styles as $value => $label) {
                $options[] = '<option value="' . esc_attr($value) .'"'. ($current === $value ? ' selected="selected"' : '') .'>'. htmlentities($label) .'</option>';
            }

            $formSettings['Form Button']['button_class'] = '
            <tr id="form_button_text_setting" class="child_setting_row">
                <th>
                    ' . __('Button Style', 'gravityforms') . ' ' . gform_tooltip('form_button_class', '', true) . '
                </th>
                <td>
                    <select id="form_button_text_class" name="form_button_text_class">' . implode($options) . '</select>
                </td>
            </tr>';
        }

        return $formSettings;
    }

    public static function buttonClassProcess(array $updatedForm): array
    {
        $class = filter_input(INPUT_POST, 'form_button_text_class');
        $updatedForm['button']['class'] = ($class) ? stripslashes_deep((string)$class) : '';
        return $updatedForm;
    }
}

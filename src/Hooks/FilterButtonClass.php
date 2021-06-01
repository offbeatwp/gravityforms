<?php
namespace OffbeatWP\GravityForms\Hooks;

class FilterButtonClass
{
    public function buttonClass(array $formSettings, array $form): array
    {
        $inputVal = esc_attr(rgars($form, 'button/class'));

        $formSettings["Form Button"]["button_class"] = '
            <tr id="form_button_text_setting" class="child_setting_row">
                <th>
                    ' . __('Button Class', 'gravityforms') . ' ' . gform_tooltip('form_button_class', '', true) . '
                </th>
                <td>
                    <input type="text" id="form_button_text_class" name="form_button_text_class" class="fieldwidth-3" value="' . $inputVal . '" />
                </td>
            </tr>';

        return $formSettings;
    }

    public function buttonClassProcess(array $updatedForm): array
    {
        $updatedForm['button']['class'] = rgpost('form_button_text_class');
        return $updatedForm;
    }
}
<?php

namespace OffbeatWP\GravityForms;

use OffbeatWP\GravityForms\Components\GravityForm;
use OffbeatWP\GravityForms\Integrations\AcfFieldGravityForms;
use OffbeatWP\Services\AbstractService;
use OffbeatWP\Contracts\View;

final class Service extends AbstractService
{
    public function register(View $view): void
    {
        add_filter('gform_form_tag', [$this, 'formActionOnAjax']);
        add_filter('gform_cdata_open', [$this, 'wrapJqueryScriptStart']);
        add_filter('gform_cdata_close', [$this, 'wrapJqueryScriptEnd']);

        if (is_admin()) {
            add_filter('gform_enable_field_label_visibility_settings', '__return_true');
        } else {
            add_filter('gform_init_scripts_footer', '__return_true');
        }

        if (apply_filters('offbeatwp/gravityforms/register_component', true)) {
            offbeat('components')->register('gravityform', GravityForm::class);
        }

        $view->registerGlobal('gf', new Helpers\View());

        if (class_exists('GFAPI')) {
            add_action('acf/include_field_types', [$this, 'addACFGravityFormsFieldType']);
        }

        add_action('gform_field_standard_settings', function (int $position) {
            if ($position !== 25) {
                return;
            }

            $styles = config('button.styles');

            if (is_iterable($styles)) { ?>
                <li id="field_vg_button_style_container" class="vg_button_style_setting field_setting">
                    <label for="field_vg_button_style_input">
                        <?= __('Button Type', 'gravityforms') ?>
                    </label>

                    <select id="field_vg_button_style_input" onchange="window.vollegrondGformButtonStyle = this.value;">
                        <?php foreach ($styles as $value => $label) { ?>
                            <option value="<?= esc_attr($value) ?>">
                                <?= esc_html($label) ?>
                            </option>
                        <?php } ?>
                    </select>
                </li>
            <?php } ?>

            <li class="vg_button_class_setting field_setting">
                <input type="checkbox" id="field_vg_button_enable_custom_class_input" onclick="changeButtonStyleDisplay(this.checked); window.enableCustomClass = this.checked;"/>
                <label for="field_vg_button_enable_custom_class_input" class="inline">
                    <?= esc_html__('Enable custom classes', 'gravityforms') ?>
                </label>
                <br/>
                <div id="field_vg_button_class_container" style="display:none; padding-top:10px;">
                    <div class="vg_button_class_setting field_setting">
                        <label for="field_vg_button_class_input">
                            <?= esc_html__('Button Class', 'gravityforms') ?>
                        </label>
                        <input type="text" id="field_vg_button_class_input" onchange="window.vollegrondGformButtonClass = this.value;">
                    </div>
                </div>
            </li>

            <?php });

        add_action('gform_editor_js', function() {
            ?>
            <script type="text/javascript">
                fieldSettings.submit += ', .vg_button_style_setting, .vg_button_class_setting';

                jQuery(document).on("gform_load_field_settings", function (event, field, form) {
                    const { button } = form;

                    const buttonStyleInput = document.getElementById('field_vg_button_style_input');
                    const buttonCustomClassCheckbox = document.getElementById('field_vg_button_enable_custom_class_input');
                    const buttonClassInput = document.getElementById('field_vg_button_class_input');

                    if (buttonStyleInput) {
                        buttonStyleInput.value = button.class;
                    }

                    if (buttonCustomClassCheckbox) {
                        buttonCustomClassCheckbox.checked = button.customClassEnabled;
                    }

                    if (buttonClassInput) {
                        buttonClassInput.value = button.class;
                    }

                    if (button.customClassEnabled) {
                        changeButtonStyleDisplay(true);
                    }
                });

                gform.addFilter('gform_pre_form_editor_save', function (form) {
                    const { button } = form;

                    button.class = window.vollegrondGformButtonStyle || button.class;
                    button.customClassEnabled = window.enableCustomClass ?? button.customClassEnabled;

                    if (button.customClassEnabled) {
                        button.class = window.vollegrondGformButtonClass || button.class;
                    }

                    return form;
                });

                function changeButtonStyleDisplay(state) {
                    const buttonClassInputContainer = document.getElementById('field_vg_button_class_container');
                    const buttonStyleInputContainer = document.getElementById('field_vg_button_style_container');

                    if (buttonClassInputContainer) {
                        buttonClassInputContainer.style.display = state ? '' : 'none';
                    }

                    if (buttonStyleInputContainer) {
                        buttonStyleInputContainer.style.display = state ? 'none' : '';
                    }
                }
            </script>
            <?php
        });
    }

    public function formActionOnAjax(string $formTag): ?string
    {
        if (wp_doing_ajax() || isset($_POST['gform_ajax'])) {
            $formTag = preg_replace("/action='(.+)(#[^']+)'/", 'action="$2"', $formTag);
        }

        return $formTag;
    }

    public static function wrapJqueryScriptStart(string $content = ''): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4);

        if (wp_doing_ajax() || isset($_POST['gform_ajax']) || $backtrace[3]['function'] !== 'get_form') {
            return $content;
        }

        return 'document.addEventListener("DOMContentLoaded", function() { ';
    }

    public static function wrapJqueryScriptEnd(string $content = ''): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4);

        if (wp_doing_ajax() || isset($_POST['gform_ajax']) || $backtrace[3]['function'] !== 'get_form') {
            return $content;
        }

        return ' }, false);';
    }

    public function addACFGravityFormsFieldType(): void
    {
        new AcfFieldGravityForms();
    }
}

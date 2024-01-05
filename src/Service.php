<?php

namespace OffbeatWP\GravityForms;

use OffbeatWP\GravityForms\Components\GravityForm;
use OffbeatWP\GravityForms\Integrations\AcfFieldGravityForms;
use OffbeatWP\Services\AbstractService;
use OffbeatWP\Contracts\View;

final class Service extends AbstractService
{
    public function register(View $view)
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
            $styles = config('button.styles');

            if ($position === 25 && is_iterable($styles)) { ?>
                <li class="vg_button_style_setting field_setting">
                    <label for="field_vg_button_style_value">
                        <?= __('Button Class', 'gravityforms') ?>
                    </label>

                    <select id="field_vg_button_style_input" onchange="window.vollegrondGformButtonStyle = this.value;">
                        <?php foreach ($styles as $value => $label) { ?>
                            <option value="<?= esc_attr($value) ?>">
                                <?= esc_html($label) ?>
                            </option>
                        <?php } ?>
                    </select>
                </li>
            <?php }
        });

        add_action('gform_editor_js', function() {
            ?>
            <script type="text/javascript">
                fieldSettings.submit += ', .vg_button_style_setting';

                jQuery(document).on("gform_load_field_settings", (event, field, form) => {
                    if (form.button.class) {
                        document.querySelector('#field_vg_button_style_input').value = form.button.class;
                    }
                });

                gform.addFilter('gform_pre_form_editor_save', function (form) {
                    if (window.vollegrondGformButtonStyle) {
                        form.button.class = window.vollegrondGformButtonStyle;
                    }

                    return form;
                });
            </script>
            <?php
        });
    }

    public function formActionOnAjax(string $formTag): ?string
    {
        if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax'])) {
            $formTag = preg_replace("/action='(.+)(#[^']+)'/", 'action="$2"', $formTag);
        }

        return $formTag;
    }

    public static function wrapJqueryScriptStart(string $content = ''): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4);

        if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax']) || $backtrace[3]['function'] !== 'get_form') {
            return $content;
        }

        return 'document.addEventListener("DOMContentLoaded", function() { ';
    }

    public static function wrapJqueryScriptEnd(string $content = ''): string
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4);

        if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax']) || $backtrace[3]['function'] !== 'get_form') {
            return $content;
        }

        return ' }, false);';
    }

    public function addACFGravityFormsFieldType(): void
    {
        new AcfFieldGravityForms();
    }
}

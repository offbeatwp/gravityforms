<?php

namespace OffbeatWP\GravityForms;

use OffbeatWP\GravityForms\Components\GravityForm;
use OffbeatWP\GravityForms\Hooks\FilterButtonClass;
use OffbeatWP\GravityForms\Integrations\AcfFieldGravityForms;
use OffbeatWP\Services\AbstractService;
use OffbeatWP\Contracts\View;

class Service extends AbstractService
{
    public function register(View $view)
    {
        add_filter('gform_form_tag', [$this, 'formActionOnAjax'], 10, 2);

        add_filter('gform_cdata_open', [$this, 'wrapJqueryScriptStart']);
        add_filter('gform_cdata_close', [$this, 'wrapJqueryScriptEnd']);

        if (is_admin()) {
            add_filter('gform_form_settings', [FilterButtonClass::class, 'buttonClass'], 10, 2);
            add_filter('gform_pre_form_settings_save', [FilterButtonClass::class, 'buttonClassProcess']);
            add_filter('gform_enable_field_label_visibility_settings', '__return_true');
        }

        if (apply_filters('offbeatwp/gravityforms/register_component', true)) {
            offbeat('components')->register('gravityform', GravityForm::class);
        }

        $view->registerGlobal('gf', new Helpers\View());

        if (class_exists('GFAPI')) {
            add_action('acf/include_field_types', [$this, 'addACFGravityFormsFieldType']);
        }
    }

    public function formActionOnAjax(string $formTag)
    {
        if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax'])) {
            preg_match("/action='(.+)(#[^']+)'/", $formTag, $matches);

            $formTag = str_replace($matches[0], 'action="' . $matches[2] . '"', $formTag);
        }

        return $formTag;
    }

    public static function wrapJqueryScriptStart(string $content = ''): string
    {
        $backtrace = debug_backtrace();

        if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax']) || $backtrace[3]['function'] != 'get_form') {
            return $content;
        }

        return 'document.addEventListener("DOMContentLoaded", function() { ';
    }

    public static function wrapJqueryScriptEnd(string $content = ''): string
    {
        $backtrace = debug_backtrace();

        if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax']) || $backtrace[3]['function'] != 'get_form') {
            return $content;
        }

        return ' }, false);';
    }

    public function addACFGravityFormsFieldType()
    {
        new AcfFieldGravityForms();
    }
}

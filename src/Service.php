<?php
namespace OffbeatWP\GravityForms;

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
            add_filter('gform_form_settings', [ Hooks\FilterButtonClass::class, 'buttonClass' ], 10, 2);
            add_filter('gform_pre_form_settings_save', [ Hooks\FilterButtonClass::class, 'buttonClassProcess' ], 10, 1);
            add_filter('gform_enable_field_label_visibility_settings', '__return_true');
        } else {
            add_filter('gform_init_scripts_footer', '__return_true');
        }

        offbeat('components')->register('gravityform', Components\GravityForm::class);

        $view->registerGlobal('gf', new Helpers\View());

        add_action('acf/include_field_types', [$this, 'addACFGravityFormsFieldType']);
    }

    public function formActionOnAjax($formTag, $form)
    {
        if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax'])) {
            preg_match("/action='(.+)(#[^']+)'/", $formTag, $matches);

            $formTag = str_replace($matches[0], 'action="' . $matches[2] . '"', $formTag);
        }

        return $formTag;
    }

    public static function wrapJqueryScriptStart($content = '')
    {
        $backtrace = debug_backtrace();

        if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax']) || $backtrace[3]['function'] != 'get_form') {
            return $content;
        }

        $content = 'document.addEventListener("DOMContentLoaded", function() { ';
        return $content;
    }

    public static function wrapJqueryScriptEnd($content = '')
    {
        $backtrace = debug_backtrace();

        if ((defined('DOING_AJAX') && DOING_AJAX) || isset($_POST['gform_ajax']) || $backtrace[3]['function'] != 'get_form') {
            return $content;
        }

        $content = ' }, false);';
        return $content;
    }

    public function addACFGravityFormsFieldType() {
        new Integrations\AcfFieldGravityForms();
    }
}
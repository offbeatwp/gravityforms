<?php
namespace OffbeatWP\GravityForms\Components;

use \OffbeatWP\Components\AbstractComponent;
use \OffbeatWP\Form\Form;
use OffbeatWP\GravityForms\Fields\GravityFormsField;

class GravityForm extends AbstractComponent
{
    public static function settings() {
        return [
            'name'      => __('Gravity Form', 'offbeatwp'),
            'slug'      => 'gravityform',
            'category'  => __('Basic Modules', 'offbeatwp'),
            'supports'  => ['widget', 'pagebuilder'],
            'form'      => self::form(),
        ];
    }

    public function render($settings)
    {

        if (!empty($settings->form) && is_array($settings->form)) {
            $settings->form = (object)$settings->form;
        }

        if (!is_object($settings->form) || !isset($settings->form->id)) {
            return 'No valid form';
        }

        if (!isset($settings->displayTitle)) {
            $settings->displayTitle = false;
        }

        if (!isset($settings->displayDescription)) {
            $settings->displayDescription = false;
        }

        return gravity_form($settings->form->id, $settings->displayTitle, $settings->displayDescription, false, null, true, 1, false);
    }

    public static function form() {

        $form = new Form();

        $form->addTab('general', 'General')
                ->addSection('general', 'General')
                    ->addField(GravityFormsField::make('form', 'Form'));

        return $form;
    }
}

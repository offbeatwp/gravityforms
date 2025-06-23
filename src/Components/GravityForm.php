<?php

namespace OffbeatWP\GravityForms\Components;

use OffbeatWP\Components\AbstractComponent;
use OffbeatWP\Components\ComponentSettings;
use OffbeatWP\Form\Form;
use OffbeatWP\GravityForms\Fields\GravityFormsField;

final class GravityForm extends AbstractComponent
{
    public static function settings(): array
    {
        return [
            'name' => esc_html__('Gravity Form', 'offbeatwp'),
            'slug' => 'gravityform',
            'category' => esc_html__('Basic Modules', 'offbeatwp'),
            'supports' => ['widget', 'pagebuilder'],
            'form' => self::form()
        ];
    }

    public function render(ComponentSettings $settings): string
    {
        $form = $settings->get('form');

        if (is_array($form) && isset($form['form'])) {
            $formId = $form['id'];
        } elseif (is_object($form) && isset($form->id)) {
            $formId = $form->id;
        } else {
            return 'No valid form';
        }

        /** @var string Always returns a string since 'echo' is set to <i>false</i> */
        return gravity_form(
            $formId,
            $settings->getBool('displayTitle'),
            $settings->getBool('displayDescription'),
            false,
            null,
            true,
            1,
            false
        );
    }

    public static function form(): Form
    {

        $form = new Form();

        $form->addTab('general', 'General')
            ->addSection('general', 'General')
            ->addField(GravityFormsField::make('form', 'Form'));

        return $form;
    }
}

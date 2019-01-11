<?php
namespace OffbeatWP\GravityForms\Components;

use \OffbeatWP\Components\AbstractComponent;

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
        return gravity_form($settings->formId, $settings->displayTitle, $settings->displayDescription, false, null, true, 1, false);
    }

    public static function form() {
        return [[
            'id'  => 'general',
            'title'  => __('General', 'offbeatwp'),
            'sections' => [[
                'id' => 'general',
                'title'  => __('Form', 'offbeatwp'),
                'fields' => [
                    [
                        'name' => 'form',
                        'label' => __('Form', 'offbeatwp'),
                        'type' => 'gravityforms',
                    ]
                ]
            ]]
        ]];
    }
}
<?php
namespace OffbeatWP\GravityForms\Fields;

use OffbeatWP\Form\Fields\AbstractField;

final class GravityFormsField extends AbstractField {
    public const FIELD_TYPE = 'gravityforms';

    public function getFieldType(): string
    {
        return self::FIELD_TYPE;
    }
}
<?php
namespace OffbeatWP\GravityForms\Helpers;

class View {
    public function form($id, bool $displayTitle = true, bool $displayDescription = true, bool $displayInactive = false, ?array $fieldValues = null, bool $ajax = false, int $tabindex = 1)
    {
        return gravity_form($id, $displayTitle, $displayDescription, $displayInactive, $fieldValues, $ajax, $tabindex, false);
    }
}
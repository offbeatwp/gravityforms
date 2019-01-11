<?php
namespace OffbeatWP\GravityForms\Helpers;

class View {
    public function form($id, $displayTitle = true, $displayDescription = true, $displayInactive = false, $fieldValues = null, $ajax = false, $tabindex = 1)
    {
        return gravity_form( $id, $displayTitle, $displayDescription, $displayInactive, $fieldValues, $ajax, $tabindex, false );
    }
}
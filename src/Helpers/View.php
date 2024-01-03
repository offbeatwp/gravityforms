<?php
namespace OffbeatWP\GravityForms\Helpers;

final class View {
    /**
     * @param string|int $id The id or title of the form to be embedded.
     * @param bool $displayTitle Whether or not to display the form title.
     * @param bool $displayDescription Whether or not to display the form description.
     * @param bool $displayInactive Whether or not to display the form even if it is inactive.
     * @param mixed[]|null $fieldValues Pass an array of dynamic population parameter keys with their corresponding values to be populated.
     * @param bool $ajax Whether or not to use AJAX for form submission.
     * @param int $tabindex Specify the starting tab index for the fields of this form.
     * @return string
     */
    public function form($id, bool $displayTitle = true, bool $displayDescription = true, bool $displayInactive = false, ?array $fieldValues = null, bool $ajax = false, int $tabindex = 1)
    {
        return gravity_form($id, $displayTitle, $displayDescription, $displayInactive, $fieldValues, $ajax, $tabindex, false);
    }
}
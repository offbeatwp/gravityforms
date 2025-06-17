<?php

namespace OffbeatWP\GravityForms\Models;

use GFAPI;
use WP_Error;

final class GfEntryModel
{
    /** @var mixed[] */
    private array $entry;
    public readonly GfFormModel $form;
    /** @var array<string, string|null> */
    private array $fieldsMapping = [];

    /** @param mixed[] $entry */
    public function __construct(array $entry, GfFormModel $form)
    {
        $this->entry = $entry;
        $this->form = $form;
    }

    /** @return mixed[] */
    public function getEntry(): array
    {
        return $this->entry;
    }

    public function getId(): int
    {
        return (int)$this->entry['id'];
    }

    public function getFormId(): int
    {
        return (int)$this->entry['form_id'];
    }

    public function getSourceUrl(): string
    {
        return $this->entry['source_url'];
    }

    public function getDateCreated(): string
    {
        return $this->entry['date_created'];
    }

    public function getRequestId(): ?int
    {
        return $this->entry['request_id'] ?? null;
    }

    private function findValueByInputName(string $inputName): ?string
    {
        $fieldKey = $this->form->getFieldKeyByInputName($inputName);

        if ($fieldKey) {
            foreach ($this->entry as $entryKey => $entryValue) {
                if ($entryKey === $fieldKey) {
                    return $entryValue;
                }
            }
        }

        return null;
    }

    public function getValueByInputName(string $inputName): mixed
    {
        if (!array_key_exists($inputName, $this->fieldsMapping)) {
            $this->fieldsMapping[$inputName] = $this->findValueByInputName($inputName);
        }

        return $this->fieldsMapping[$inputName];
    }

    public function getMeta(string|int $key): mixed
    {
        return gform_get_meta($this->entry['id'], $key);
    }

    public function delete(): bool|WP_Error
    {
        return GFAPI::delete_entry($this->entry['id']);
    }

    public static function find(int $entryId, ?GfFormModel $form = null): ?static
    {
        if ($entryId > 0) {
            $entry = GFAPI::get_entry($entryId);

            if (is_array($entry) && !empty($entry['form_id'])) {
                $formId = filter_var($entry['form_id'], FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

                if ($formId) {
                    $form = $form ?? GfFormModel::find($formId);

                    if ($form && $form->getId() === $formId) {
                        return new static($entry, $form);
                    }
                }
            }
        }

        return null;
    }
}

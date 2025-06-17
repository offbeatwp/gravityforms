<?php

namespace OffbeatWP\GravityForms\Models;

use GF_Field;
use GFAPI;

final class GfFormModel
{
    /** @var mixed[] */
    private readonly array $form;
    /** @var array<string, array{key: ?int, field: ?GF_Field}> */
    private array $mappedFields;

    /** @param mixed[] $form */
    public function __construct(array $form)
    {
        $this->form = $form;
        $this->mappedFields = [];
    }

    public function getId(): int
    {
        return $this->form['id'];
    }

    public function getTitle(): string
    {
        return $this->form['title'];
    }

    public function getDescription(): string
    {
        return $this->form['description'];
    }

    /** @return GF_Field[] */
    public function getFields(): array
    {
        return $this->form['fields'];
    }

    /** @return array{key: ?int, field: ?GF_Field} */
    private function findByInputName(string $inputName): array
    {
        if (!array_key_exists($inputName, $this->mappedFields)) {
            $this->mappedFields[$inputName] = ['key' => null, 'field' => null];

            /** @var GF_Field $field */
            foreach ($this->form['fields'] as $field) {
                if ($field->inputName === $inputName) {
                    $this->mappedFields[$inputName] = ['key' => (int)$field->id, 'field' => $field];
                    break;
                }
            }
        }

        return $this->mappedFields[$inputName];
    }

    public function getFieldKeyByInputName(string $inputName): ?int
    {
        return $this->findByInputName($inputName)['key'];
    }

    public function getFieldByInputName(string $inputName): ?GF_Field
    {
        return $this->findByInputName($inputName)['field'];
    }

    public function getAttribute(string $key): mixed
    {
        return $this->form[$key] ?? null;
    }

    public static function find(int $formId): ?static
    {
        if ($formId > 0) {
            $form = GFAPI::get_form($formId);

            if (is_array($form) && isset($form['id']) && is_int($form['id'])) {
                return new static($form);
            }
        }

        return null;
    }
}

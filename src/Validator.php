<?php

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, string $label) : void
    {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field] = "{$label}は必須です";
        }
    }

    public function maxLength( string $field, string $label, int $max) : void
    {
        if ($max < mb_strlen($this->data[$field])) {
            $this->errors[$field] = "{$label}文字数オーバー";
        }
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

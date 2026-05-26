<?php

class Validator {
    private array $data;
    private array $errors = [];

public function __construct($data) {
    $this->data = $data;
}

public function required($field, $label) {
    if(empty($this->data[$field])) {
        $this->errors[$field] = "{$label}は必須です";
    }
}

public function hasErrors() :bool {
    return !empty($this->errors);
}

public function getErrors() :array {
    return $this->errors;
}
}




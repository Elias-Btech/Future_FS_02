<?php
// ============================================================
// Validator.php — Input validation
// ============================================================
namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data   = [];

    public function __construct(array $data)
    {
        // Trim all input values on construction
        $this->data = array_map('trim', $data);
    }

    // required — field must not be empty
    public function required(string $field, string $label): self
    {
        if (empty($this->data[$field])) {
            $this->errors[$field] = "$label is required.";
        }
        return $this;
    }

    // email — must be valid email format
    public function email(string $field, string $label): self
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "$label must be a valid email address.";
        }
        return $this;
    }

    // min — minimum string length
    public function min(string $field, int $length, string $label): self
    {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = "$label must be at least $length characters.";
        }
        return $this;
    }

    // max — maximum string length
    public function max(string $field, int $length, string $label): self
    {
        if (!empty($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = "$label must not exceed $length characters.";
        }
        return $this;
    }

    // in — value must be in allowed list
    public function in(string $field, array $allowed, string $label): self
    {
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $allowed, true)) {
            $this->errors[$field] = "$label contains an invalid value.";
        }
        return $this;
    }

    // Returns true if no errors
    public function passes(): bool { return empty($this->errors); }
    public function fails():  bool { return !empty($this->errors); }

    // Get all errors
    public function errors(): array { return $this->errors; }

    // Get first error for a field
    public function error(string $field): ?string { return $this->errors[$field] ?? null; }

    // Get sanitized value
    public function get(string $field, mixed $default = ''): mixed
    {
        return $this->data[$field] ?? $default;
    }

    // Get all sanitized data
    public function all(): array { return $this->data; }
}

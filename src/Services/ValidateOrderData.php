<?php

namespace App\Services;

class ValidateOrderData
{
    public function validate(array $data): array
    {
        $errors = [];
        $sanitized = [];

        // 1. ФИО
        $sanitized['full_name'] = $this->sanitize($data['full_name'] ?? '');
        if ($sanitized['full_name'] === '') {
            $errors[] = 'ФИО не должно быть пустым';
        } elseif (strlen($sanitized['full_name']) <= 3) {
            $errors[] = 'ФИО должно содержать более 3 символов';
        }

        // 2. Адрес
        $sanitized['address'] = $this->sanitize($data['address'] ?? '');
        if ($sanitized['address'] === '') {
            $errors[] = 'Адрес не должен быть пустым';
        } elseif (strlen($sanitized['address']) <= 10) {
            $errors[] = 'Адрес должен содержать более 10 символов';
        } elseif (strlen($sanitized['address']) >= 200) {
            $errors[] = 'Адрес должен содержать менее 200 символов';
        }

        // 3. Телефон (удаляем всё кроме + и цифр через filter_var + preg_replace для точности)
        $rawPhone = $data['phone'] ?? '';
        $sanitized['phone'] = preg_replace('/[^0-9+]/', '', filter_var($rawPhone, FILTER_SANITIZE_NUMBER_INT));
        if ($sanitized['phone'] === '') {
            $errors[] = 'Телефон не должен быть пустым';
        }

        // 4. Email
        $sanitized['email'] = $this->sanitize($data['email'] ?? '');
        if ($sanitized['email'] === '') {
            $errors[] = 'Email не должен быть пустым';
        } elseif (!filter_var($sanitized['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email указан некорректно';
        }

        return [
            'success' => empty($errors),
            'errors'  => $errors,
            'data'    => $sanitized
        ];
    }

    private function sanitize(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return $value;
    }
}
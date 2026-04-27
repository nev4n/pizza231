<?php

namespace App\Services;

class ValidateRegisterData
{
    /** @var array Список уже существующих email в БД (для примера) */
    private array $existingEmails;

    /**
     * В реальном проекте здесь будет внедрение репозитория/сервиса пользователей
     */
    public function __construct(array $existingEmails = [])
    {
        $this->existingEmails = array_map('strtolower', $existingEmails);
    }

    public function validate(array $data): array
    {
        $errors = [];
        $sanitized = [];

        // 1. Имя пользователя
        $sanitized['username'] = $this->sanitize($data['username'] ?? '');
        if ($sanitized['username'] === '') {
            $errors[] = 'Имя пользователя не должно быть пустым';
        }

        // 2. Email
        $sanitized['email'] = $this->sanitize($data['email'] ?? '');
        if ($sanitized['email'] === '') {
            $errors[] = 'Email не должен быть пустым';
        } elseif (!filter_var($sanitized['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email указан некорректно';
        } elseif (in_array(strtolower($sanitized['email']), $this->existingEmails, true)) {
            $errors[] = 'Этот email уже зарегистрирован';
        }

        // 3. Пароль (пароль не санитизируем htmlspecialchars/strip_tags, так как это бинарные данные для хеширования)
        $password = $data['password'] ?? '';
        if ($password === '') {
            $errors[] = 'Пароль не должен быть пустым';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Пароль должен быть не менее 6 символов';
        }

        // 4. Подтверждение пароля
        $passwordConfirm = $data['password_confirm'] ?? '';
        if ($password !== $passwordConfirm) {
            $errors[] = 'Пароли не совпадают';
        }

        $sanitized['password'] = $password; // В реальном проекте здесь будет password_hash()

        return [
            'success' => empty($errors),
            'errors'  => $errors,
            'data'    => $sanitized
        ];
    }

    /**
     * Базовая санитизация от XSS и лишних пробелов
     */
    private function sanitize(string $value): string
    {
        $value = trim($value);
        $value = strip_tags($value);
        $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        return $value;
    }
}
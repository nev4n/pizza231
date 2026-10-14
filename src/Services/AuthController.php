<?php
// src/Controllers/AuthController.php
namespace App\Controllers;

use App\Services\ValidateRegisterData;

class AuthController
{
    public function register(array $requestData, array $dbExistingEmails): array
    {
        $validator = new ValidateRegisterData($dbExistingEmails);
        $result = $validator->validate($requestData);

        if (!$result['success']) {
            // Возвращаем ошибки в представление или API-ответ
            return ['status' => 'error', 'messages' => $result['errors']];
        }

        // ✅ Данные валидны и санитизированы. Можно сохранять в БД.
        // В реальном проекте: $this->userRepository->create($result['data']);
        return ['status' => 'success', 'user' => $result['data']];
    }
}
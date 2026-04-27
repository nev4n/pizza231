<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ValidateRegisterData;

class ValidateRegisterDataTest extends TestCase
{
    private ValidateRegisterData $validator;

    protected function setUp(): void
    {
        // Имитируем БД: email 'user@example.com' уже занят
        $this->validator = new ValidateRegisterData(['user@example.com']);
    }

    public function testValidDataPasses(): void
    {
        $data = [
            'username' => '  ValidUser  ',
            'email' => 'new@example.com',
            'password' => 'secure123',
            'password_confirm' => 'secure123'
        ];

        $result = $this->validator->validate($data);

        $this->assertTrue($result['success']);
        $this->assertEmpty($result['errors']);
        $this->assertSame('ValidUser', $result['data']['username']); // trim сработал
    }

    public function testEmptyUsername(): void
    {
        $result = $this->validator->validate([
            'username' => '', 'email' => 'a@b.c', 'password' => '123456', 'password_confirm' => '123456'
        ]);

        $this->assertFalse($result['success']);
        $this->assertContains('Имя пользователя не должно быть пустым', $result['errors']);
    }

    public function testInvalidEmail(): void
    {
        $result = $this->validator->validate([
            'username' => 'User', 'email' => 'invalid-email', 'password' => '123456', 'password_confirm' => '123456'
        ]);

        $this->assertContains('Email указан некорректно', $result['errors']);
    }

    public function testDuplicateEmail(): void
    {
        $result = $this->validator->validate([
            'username' => 'User', 'email' => 'USER@EXAMPLE.COM', 'password' => '123456', 'password_confirm' => '123456'
        ]);

        $this->assertContains('Этот email уже зарегистрирован', $result['errors']);
    }

    public function testShortPassword(): void
    {
        $result = $this->validator->validate([
            'username' => 'User', 'email' => 'ok@ok.ru', 'password' => '123', 'password_confirm' => '123'
        ]);

        $this->assertContains('Пароль должен быть не менее 6 символов', $result['errors']);
    }

    public function testPasswordMismatch(): void
    {
        $result = $this->validator->validate([
            'username' => 'User', 'email' => 'ok@ok.ru', 'password' => '123456', 'password_confirm' => '654321'
        ]);

        $this->assertContains('Пароли не совпадают', $result['errors']);
    }

    public function testXssSanitization(): void
    {
        $result = $this->validator->validate([
            'username' => '<script>alert("xss")</script>  Test  ',
            'email' => 'clean@test.com',
            'password' => '123456',
            'password_confirm' => '123456'
        ]);

        $this->assertTrue($result['success']);
        $this->assertStringNotContainsString('<script>', $result['data']['username']);
        $this->assertStringContainsString('Test', $result['data']['username']);
        $this->assertStringContainsString('&lt;', $result['data']['username']); // htmlspecialchars экранировал
    }
}
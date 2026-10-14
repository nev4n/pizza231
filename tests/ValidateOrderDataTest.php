<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ValidateOrderData;

class ValidateOrderDataTest extends TestCase
{
    private ValidateOrderData $validator;

    protected function setUp(): void
    {
        $this->validator = new ValidateOrderData();
    }

    public function testValidOrderData(): void
    {
        $data = [
            'full_name' => 'Иванов Иван Иванович',
            'address'   => 'г. Москва, ул. Ленина, д. 10, кв. 5',
            'phone'     => '+7 (999) 123-45-67',
            'email'     => 'ivanov@mail.ru'
        ];

        $result = $this->validator->validate($data);

        $this->assertTrue($result['success']);
        $this->assertSame('+79991234567', $result['data']['phone']); // Остались только + и цифры
    }

    public function testShortName(): void
    {
        $result = $this->validator->validate([
            'full_name' => 'Ив', 'address' => 'Длинный адрес больше 10 символов', 'phone' => '+79000000000', 'email' => 'a@b.c'
        ]);

        $this->assertContains('ФИО должно содержать более 3 символов', $result['errors']);
    }

    public function testAddressTooShortAndTooLong(): void
    {
        // Слишком короткий
        $r1 = $this->validator->validate([
            'full_name' => 'Test', 'address' => 'Short', 'phone' => '+79000000000', 'email' => 'a@b.c'
        ]);
        $this->assertContains('Адрес должен содержать более 10 символов', $r1['errors']);

        // Слишком длинный
        $longAddress = str_repeat('A', 205);
        $r2 = $this->validator->validate([
            'full_name' => 'Test', 'address' => $longAddress, 'phone' => '+79000000000', 'email' => 'a@b.c'
        ]);
        $this->assertContains('Адрес должен содержать менее 200 символов', $r2['errors']);
    }

    public function testPhoneSanitizationAndValidation(): void
    {
        $result = $this->validator->validate([
            'full_name' => 'Test Name', 'address' => 'Valid Address 123', 'phone' => 'ab+7(999)-111-22-33cd', 'email' => 'a@b.c'
        ]);

        $this->assertTrue($result['success']);
        $this->assertSame('+79991112233', $result['data']['phone']);
    }

    public function testInvalidEmailInOrder(): void
    {
        $result = $this->validator->validate([
            'full_name' => 'Test Name', 'address' => 'Valid Address 123', 'phone' => '+79991112233', 'email' => 'no-at-sign'
        ]);

        $this->assertContains('Email указан некорректно', $result['errors']);
    }
}
<?php

namespace Test;

use PHPUnit\Framework\TestCase;
use App\Controller\OrderController;

class OrderControllerTest extends TestCase
{
    private string $testFile;
    private OrderController $controller;

    protected function setUp(): void
    {
        $this->testFile = sys_get_temp_dir() . '/test_orders_' . uniqid() . '.json';
        $this->controller = new OrderController($this->testFile);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testCreateOrderSuccessfully()
    {
        $postData = [
            'name' => 'Тест Пользователь',
            'email' => 'test@example.com',
            'phone' => '+79991234567'
        ];
        
        $basketData = [
            ['id' => 1, 'qty' => 2, 'price' => 350],
            ['id' => 2, 'qty' => 1, 'price' => 500]
        ];
        
        $result = $this->controller->create($postData, $basketData);
        
        $this->assertTrue($result['success']);
        $this->assertEquals(1200, $result['all_sum']);
        $this->assertStringStartsWith('ORD-', $result['order_id']);
        $this->assertEquals(201, $result['code']);
    }

    public function testCreateOrderWithEmptyCart()
    {
        $postData = ['name' => 'Test', 'email' => 't@t.com', 'phone' => '+79991234567'];
        $result = $this->controller->create($postData, []);
        
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Корзина пуста', $result['error']);
    }
}
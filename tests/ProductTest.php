<?php
// tests/ProductTest.php
namespace Test;

use PHPUnit\Framework\TestCase;
use App\Model\Product;

class ProductTest extends TestCase
{
    /**
     * Тест-1: Проверка бизнес-логики (правильность вычислений)
     * 
     * Заказ: 2 товара по 350 руб. + 1 товар по 500 руб. = 1200 + 100 = 1300 руб.
     */
    public function testPrepareDataCalculatesTotalCorrectly()
    {
        $productModel = new Product();
        
        // Данные формы (минимальные, валидные)
        $form_data = [
            'name' => 'Иван Иванов',
            'email' => 'ivan@example.com',
            'phone' => '+7 (999) 123-45-67',
            'address' => 'г. Москва, ул. Примерная, д. 1'
        ];
        
        // Данные корзины: 2×350 + 1×500 = 1200 + 100 = 1300
        $basket_data = [
            ['id' => 1, 'qty' => 2, 'price' => 350],
            ['id' => 2, 'qty' => 1, 'price' => 500]
        ];
        
        $result = $productModel->prepareData($form_data, $basket_data);
        
        // Проверяем итоговую сумму
        $this->assertEquals(1300.00, $result['all_sum'], 'Итоговая сумма заказа должна быть 1300 руб.');
        
        // Дополнительные проверки
        $this->assertCount(2, $result['items'], 'В заказе должно быть 2 позиции');
        $this->assertEquals(700.00, $result['items'][0]['total'], 'Сумма первой позиции: 2×350=700');
        $this->assertEquals(500.00, $result['items'][1]['total'], 'Сумма второй позиции: 1×500=500');
        $this->assertArrayHasKey('created_at', $result, 'Должна быть дата создания');
    }
    
    /**
     * Тест-2: Проверка защиты от XSS-инъекций (безопасность)
     */
    public function testPrepareDataSanitizesUserInput()
    {
        $productModel = new Product();
        
        // Попытка внедрить вредоносный код
        $form_data = [
            'name' => '<script>alert("XSS")</script>Иван',
            'email' => 'test@example.com<script>',
            'address' => '"><img src=x onerror=alert(1)>'
        ];
        
        $basket_data = [['id' => 1, 'qty' => 1, 'price' => 100]];
        
        $result = $productModel->prepareData($form_data, $basket_data);
        
        // Проверяем, что скрипты экранированы
        $this->assertStringNotContainsString('<script>', $result['customer_name'], 'Имя должно быть экранировано от XSS');
        $this->assertStringContainsString('&lt;script&gt;', $result['customer_name'], 'Теги должны быть преобразованы в HTML-сущности');
        $this->assertMatchesRegularExpression('/^[\w@.\-]+$/', $result['customer_email'], 'Email должен быть валидным');
    }
    
    /**
     * Тест-заглушка для быстрой проверки настройки
     */
    public function testProbe()
    {
        $this->assertEquals(4, 2 + 2);
    }
}
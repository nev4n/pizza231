<?php
// tests/ProductTest.php
namespace Test;

use PHPUnit\Framework\TestCase;
use App\Models\Product;

class ProductTest extends TestCase
{
    private Product $product;

    protected function setUp(): void
    {
        $this->product = new Product();
    }

    /**
     * Тест-1: Загрузка данных возвращает непустой массив
     */
    public function testLoadDataReturnsArray(): void
    {
        $result = $this->product->loadData();
        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result), 'Должен быть хотя бы 1 товар');
    }

    /**
     * Тест-2: findById возвращает товар с полями name и price
     */
    public function testFindByIdReturnsProductForExistingId(): void
    {
        $product = $this->product->findById(1);
        $this->assertIsArray($product);
        $this->assertArrayHasKey('name', $product);
        $this->assertArrayHasKey('price', $product);
    }

    /**
     * Тест-3: findById возвращает null для несуществующего ID
     */
    public function testFindByIdReturnsNullForUnknownId(): void
    {
        $this->assertNull($this->product->findById(999999));
    }

    /**
     * Тест-4: getCategories возвращает массив строк
     */
    public function testGetCategoriesReturnsArrayOfStrings(): void
    {
        $categories = $this->product->getCategories();
        $this->assertIsArray($categories);
        $this->assertNotEmpty($categories);
        foreach ($categories as $cat) {
            $this->assertIsString($cat);
        }
    }

    /**
     * Тест-5: search находит существующий товар
     */
    public function testSearchFindsProductByName(): void
    {
        $results = $this->product->search('Аватар');
        $this->assertIsArray($results);
        $this->assertNotEmpty($results, 'Должен найти хотя бы 1 товар');
    }

    /**
     * Тест-6: search возвращает пустой массив для несуществующего запроса
     */
    public function testSearchReturnsEmptyForUnknownQuery(): void
    {
        $results = $this->product->search('zzzz_not_exist_zzzz');
        $this->assertIsArray($results);
        $this->assertCount(0, $results);
    }
}

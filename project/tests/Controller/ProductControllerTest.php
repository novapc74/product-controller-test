<?php

namespace App\Tests\Controller;

use App\Service\ProductService;
use LogicException;
use PHPUnit\Event\NoPreviousThrowableException;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class ProductControllerTest extends WebTestCase
{

    /**
     * @throws NoPreviousThrowableException
     * @throws Exception
     * @throws \PHPUnit\Framework\InvalidArgumentException
     */
    private function getMockService(): MockObject
    {
        return $this->createMock(ProductService::class);
    }


    /**
     * @throws InvalidArgumentException
     * @throws NoPreviousThrowableException
     * @throws Exception
     * @throws LogicException
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testShowProductSuccess(): void
    {
        $client = ProductControllerTest::createClient();
        $mockService = $this->getMockService();

        $mockService->expects($this->once())
            ->method('getProductById')
            ->with(123)
            ->willReturn(['id' => 123, 'name' => 'Тестовый товар']);

        ProductControllerTest::getContainer()->set(ProductService::class, $mockService);

        $client->request('GET', '/product/123');

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(
            json_encode([ 'product' => ['id' => 123, 'name' => 'Тестовый товар']]),
            $client->getResponse()->getContent()
        );
    }

    /**
     * @throws LogicException
     */
    public function testShowProductInvalidId(): void
    {
        $client = ProductControllerTest::createClient();

        $client->request('GET', '/product/abc');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    /**
     * @throws InvalidArgumentException
     * @throws NoPreviousThrowableException
     * @throws LogicException
     * @throws Exception
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testIndexSuccess(): void
    {
        $client = ProductControllerTest::createClient();

        // 1. Создаем "двойника" сервиса
        $serviceMock = $this->getMockService();

        // 2. Настраиваем его: при вызове getAllProducts вернуть пустой массив
        $serviceMock->method('getAllProducts')
            ->willReturn([]);

        // 3. Заменяем реальный сервис в контейнере Symfony на наш Mock
        ProductControllerTest::getContainer()->set(ProductService::class, $serviceMock);

        // 4. Выполняем запрос
        $client->request('GET', '/product');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertEquals('[]', $client->getResponse()->getContent());
    }


    /**
     * @throws InvalidArgumentException
     * @throws NoPreviousThrowableException
     * @throws LogicException
     * @throws Exception
     * @throws \PHPUnit\Framework\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function testCreateProductSuccess(): void
    {
        $client = ProductControllerTest::createClient();

        $mock = $this->getMockService();
        $productData = ['id' => 1, 'name' => 'Новый товар'];

        $mock->expects($this->once())
            ->method('createNewProduct')
            ->willReturn($productData);

        // Важно: берем контейнер именно так
        ProductControllerTest::getContainer()->set(ProductService::class, $mock);

        $client->request(
            'POST',
            '/product/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json'],
            json_encode([
                'name' => 'Новый товар',
                'price' => 100,
                'status' => true
            ])
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(
            json_encode(['product' => $productData]),
            $client->getResponse()->getContent()
        );
    }
}

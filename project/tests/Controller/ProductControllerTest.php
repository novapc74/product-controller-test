<?php

namespace App\Tests\Controller;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ProductControllerTest extends WebTestCase
{
    private function getMockService()
    {
        return $this->createMock(ProductService::class);
    }

    public function testShowProductSuccess(): void
    {
        $client = static::createClient();
        $mockService = $this->getMockService();

        $mockService->expects($this->once())
            ->method('getProductById')
            ->with(123)
            ->willReturn(['id' => 123, 'name' => 'Тестовый товар']);

        static::getContainer()->set(ProductService::class, $mockService);

        $client->request('GET', '/product/123');

        $this->assertResponseIsSuccessful();
        $this->assertJsonStringEqualsJsonString(
            json_encode(['id' => 123, 'name' => 'Тестовый товар']),
            $client->getResponse()->getContent()
        );
    }

    public function testShowProductInvalidId(): void
    {
        $client = static::createClient();

        $client->request('GET', '/product/abc');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function testIndexSuccess(): void
    {
        $client = static::createClient();
        $mock = $this->createMock(ProductService::class);
        $mock->method('getAllProducts')->willReturn([
            ['id' => 1]
            ,
            ['id' => 2]
        ]);

        $client->getContainer()->set(ProductService::class, $mock);

        $client->request('GET', '/product');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testCreateProductSuccess(): void
    {
        $client = static::createClient();

        $mock = $this->createMock(ProductService::class);

        $productData = ['id' => 1, 'name' => 'Новый товар'];

        $mock->expects($this->once())
            ->method('createNewProduct')
            ->willReturn($productData);

        $client->getContainer()->set(ProductService::class, $mock);

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

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonStringEqualsJsonString(
            json_encode($productData),
            $client->getResponse()->getContent()
        );
    }

}

<?php

namespace App\Tests\Controller;

use Generator;
use LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
class SmokeTest extends WebTestCase
{
    /**
     * @throws LogicException
     */
    #[DataProvider('urlProvider')]
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public static function urlProvider(): Generator
    {
        yield ['/product'];
        yield ['/product/1'];
    }
}

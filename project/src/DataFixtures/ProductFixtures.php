<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;

class ProductFixtures extends AppFixtures
{
    protected function loadData(ObjectManager $manager): void
    {
        $this->createEntity(Product::class, 10, function (Product $product, $count) {
            $product
                ->setName("Product $count")
                ->setPrice(rand(100000, 1000000))
                ->setStatus($count % 2 === 0);
        });

        $manager->flush();
    }
}

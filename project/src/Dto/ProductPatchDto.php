<?php

namespace App\Dto;

use App\Validator\UniqueProductName;
use Symfony\Component\Validator\Constraints as Assert;

class ProductPatchDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[UniqueProductName]
        public string $name,

        public ?int $price,

        #[Assert\Choice([false, true])]
        public bool $status,

    ) {}
}
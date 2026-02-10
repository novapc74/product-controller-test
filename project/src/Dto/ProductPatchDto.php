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

        #[Assert\PositiveOrZero(message: "Значение должно быть позитивным или 0")]
        public ?int  $price,

        #[Assert\Choice([false, true])]
        public bool $status,

    ) {}
}
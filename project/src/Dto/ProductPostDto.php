<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
class ProductPostDto
{
    public function __construct(
        #[Assert\NotBlank(message: "Название не может быть пустым")]
        #[Assert\Length(min: 3, max: 255)]
        public string $name,

        #[Assert\PositiveOrZero(message: "Значение должно быть позитивным или 0")]
        public ?int  $price,

        #[Assert\Choice([false, true])]
        public bool $status,

    ) {}
}
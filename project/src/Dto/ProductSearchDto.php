<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ProductSearchDto
{
    public function __construct(
        #[Assert\Type('string', 'Ожидаемый тип - строка.')]
        public ?string $search = null,

        #[Assert\Choice(['ASC', 'DESC'])]
        public string  $sort_direction = 'ASC',
    )
    {
        $this->search = $this->search ? mb_strtolower($this->search) : null;
        $this->sort_direction = strtoupper($this->sort_direction);
    }
}

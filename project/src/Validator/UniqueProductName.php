<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueProductName extends Constraint
{
    public string $message = 'Имя "{{ value }}" уже занято другим продуктом.';
}

<?php

namespace App\Validator;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueProductNameValidator extends ConstraintValidator
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack
    ) {}

    public function validate($value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) return;

        $currentRequest = $this->requestStack->getCurrentRequest();
        $productId = $currentRequest->attributes->get('id');

        $repository = $this->entityManager->getRepository(Product::class);
        $existingProduct = $repository->findOneBy(['name' => $value]);

        if ($existingProduct && (string)$existingProduct->getId() !== (string)$productId) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}

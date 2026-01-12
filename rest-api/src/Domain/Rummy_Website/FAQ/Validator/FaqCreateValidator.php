<?php

namespace App\Domain\Rummy_Website\FAQ\Validator;

use App\Domain\Rummy_Website\FAQ\Repository\FaqRepository;

use App\Factory\ConstraintFactory;
use DomainException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class FaqCreateValidator
{
    private FaqRepository $repository;

    public function __construct(FaqRepository $repository)
    {
        $this->repository = $repository;
    }

    public function validateCreateData(array $data): void
    {
        $this->validateData($data);
    }

    public function validateData(array $data): void
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($data, $this->createConstraints());

        if ($violations->count()) {
            throw new ValidationFailedException('Please check your input', $violations);
        }
    }

    private function createConstraints(): Constraint
    {
        $constraint = new ConstraintFactory();

        return $constraint->collection(
            [
                
                'title' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                ),
                'answer' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 500),
                    ]
                )
            ]
        );
    }
}

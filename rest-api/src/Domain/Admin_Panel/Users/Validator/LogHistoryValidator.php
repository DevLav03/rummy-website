<?php

namespace App\Domain\Admin_Panel\Users\Validator;

use App\Factory\ConstraintFactory;
use DomainException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class LogHistoryValidator
{
  
    public function __construct()
    {
        
    }

    public function validateGetData(array $data): void
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
                'start_date' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                ),
                'end_date' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                ),
                'search_val' => $constraint->required(
                    [
                        //$constraint->notBlank(),
                        $constraint->length(null, 60),
                    ]
                ),
                'offset' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 60),
                    ]
                ),
                'limit' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 60),
                    ]
                )
            ]
        );
    }
}

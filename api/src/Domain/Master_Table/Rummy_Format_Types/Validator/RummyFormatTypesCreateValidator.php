<?php

namespace App\Domain\Master_Table\Rummy_Format_Types\Validator;

use App\Factory\ConstraintFactory;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class RummyFormatTypesCreateValidator
{

    public function __construct()
    {
    
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
                
                'name' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                ),
                'description' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 1000),
                    ]
                ),
                'format_id' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 11),
                    ]
                )
            ]
        );
    }
}

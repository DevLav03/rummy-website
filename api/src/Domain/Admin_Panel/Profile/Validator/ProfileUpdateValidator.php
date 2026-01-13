<?php

namespace App\Domain\Admin_Panel\Profile\Validator;

use App\Factory\ConstraintFactory;
use DomainException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class ProfileUpdateValidator
{

    public function __construct()
    {

    }

    public function validateUpdateData(array $data): void
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
                        $constraint->length(null, 120),
                    ]
                ),
                'username' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 120),
                    ]
                ),
                'email' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                ),
                'phone_no' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 10),
                    ]
                )
            ]
        );
    }
}

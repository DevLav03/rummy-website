<?php

namespace App\Domain\Admin_Panel\Rummy_Game_Table\Validator;

use App\Factory\ConstraintFactory;
use DomainException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class RummyTableValidator
{

    public function __construct()
    {
       
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
                
                'game' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 50),
                    ]
                ),
                'game_type_id' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 50),
                    ]
                ),
                'table_name' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 200),
                    ]
                ),
                'table_no' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                ),
                'bet_value' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 10),
                    ]
                    ),
                'point_value' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 10),
                    ]
                ),
                'sitting_capacity' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 10),
                    ]
                ),
                'game_deck' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 10),
                    ]
                ),
                'table_status' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 10),
                    ]
                )
            ]
        );
    }
}

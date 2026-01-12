<?php

namespace App\Domain\Rummy_Game\Gameroom_Cash\Validator;

use App\Factory\ConstraintFactory;
use DomainException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class GameRoomUpdateValidator
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
                'game_table_id' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 50),
                    ]
                ),
                'joker_type' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 50),
                    ]
                ),
                'deck' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 50),
                    ]
                )
                
            ]
        );
    }
}

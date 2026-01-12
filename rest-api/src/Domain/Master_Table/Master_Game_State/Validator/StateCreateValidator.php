<?php

namespace App\Domain\Master_Table\Master_Game_State\Validator;

use App\Domain\Master_Table\Master_Game_State\Repository\StateRepository;

use App\Factory\ConstraintFactory;
use DomainException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class StateCreateValidator
{
    private StateRepository $repository;

    public function __construct(StateRepository $repository)
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
                
                'state_name' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 50),
                    ]
                )
            ]
        );
    }
}

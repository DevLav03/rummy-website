<?php

namespace App\Domain\Master_Table\Admin_Menu\Validator;

use App\Domain\Master_Table\Admin_Menu\Repository\AdminMenuRepository;

use App\Factory\ConstraintFactory;
use DomainException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class AdminMenuCreateValidator
{
    private AdminMenuRepository $repository;

    public function __construct(AdminMenuRepository $repository)
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
                
                'menu_name' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 50),
                    ]
                ),
                'menu_key' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 50),
                    ]
                ),
                'menu_link' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 200),
                    ]
                ),
                'icons' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 200),
                    ]
                ),
                'parent_id' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 200),
                    ]
                ),
                'order_id' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 200),
                    ]
                ),
                // 'status' => $constraint->required(
                //     [
                //         $constraint->notBlank(),
                //         $constraint->length(null, 255),
                //     ]
                // )
            ]
        );
    }
}

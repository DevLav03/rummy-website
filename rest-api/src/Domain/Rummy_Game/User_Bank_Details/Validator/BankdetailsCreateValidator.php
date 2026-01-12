<?php

namespace App\Domain\Rummy_Game\User_Bank_Details\Validator;

use App\Domain\Rummy_Game\User_Bank_Details\Repository\BankdetailsRepository;

use App\Factory\ConstraintFactory;
use DomainException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

final class BankdetailsCreateValidator
{
    private BankdetailsRepository $repository;

    public function __construct(BankdetailsRepository $repository)
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
                
                'user_id' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 50),
                    ]
                ),
                'customer_name' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                ),
                'bank_name' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                ),
                'account_no' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                ),
                'ifsc_code' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                ),
                'ac_type' => $constraint->required(
                    [
                        $constraint->notBlank(),
                        $constraint->length(null, 255),
                    ]
                )
            ]
        );
    }
}

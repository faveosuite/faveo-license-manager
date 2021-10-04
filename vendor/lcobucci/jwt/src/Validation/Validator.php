<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Validation;

use Lcobucci\JWT\Token;

final class Validator implements \Lcobucci\JWT\Validator
{
<<<<<<< HEAD
<<<<<<< HEAD
    public function assert(Token $token, Constraint ...$constraints)
=======
    public function assert(Token $token, Constraint ...$constraints): void
>>>>>>> 22c0e54 (table changes)
=======
    public function assert(Token $token, Constraint ...$constraints)
>>>>>>> f330c64 (optimization in progress)
    {
        if ($constraints === []) {
            throw new NoConstraintsGiven('No constraint given.');
        }

        $violations = [];

        foreach ($constraints as $constraint) {
            $this->checkConstraint($constraint, $token, $violations);
        }

        if ($violations) {
            throw RequiredConstraintsViolated::fromViolations(...$violations);
        }
    }

    /** @param ConstraintViolation[] $violations */
    private function checkConstraint(
        Constraint $constraint,
        Token $token,
        array &$violations
<<<<<<< HEAD
<<<<<<< HEAD
    ) {
=======
    ): void {
>>>>>>> 22c0e54 (table changes)
=======
    ) {
>>>>>>> f330c64 (optimization in progress)
        try {
            $constraint->assert($token);
        } catch (ConstraintViolation $e) {
            $violations[] = $e;
        }
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function validate(Token $token, Constraint ...$constraints)
=======
    public function validate(Token $token, Constraint ...$constraints): bool
>>>>>>> 22c0e54 (table changes)
=======
    public function validate(Token $token, Constraint ...$constraints)
>>>>>>> f330c64 (optimization in progress)
    {
        if ($constraints === []) {
            throw new NoConstraintsGiven('No constraint given.');
        }

        try {
            foreach ($constraints as $constraint) {
                $constraint->assert($token);
            }

            return true;
        } catch (ConstraintViolation $e) {
            return false;
        }
    }
}

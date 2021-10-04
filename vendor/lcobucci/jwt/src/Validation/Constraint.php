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

interface Constraint
{
    /** @throws ConstraintViolation */
<<<<<<< HEAD
<<<<<<< HEAD
    public function assert(Token $token);
=======
    public function assert(Token $token): void;
>>>>>>> 22c0e54 (table changes)
=======
    public function assert(Token $token);
>>>>>>> f330c64 (optimization in progress)
}

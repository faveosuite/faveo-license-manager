<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Validation\Constraint;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

final class IdentifiedBy implements Constraint
{
<<<<<<< HEAD
<<<<<<< HEAD
    /** @var string */
    private $id;

    /** @param string $id */
    public function __construct($id)
=======
    private string $id;

    public function __construct(string $id)
>>>>>>> 22c0e54 (table changes)
=======
    /** @var string */
    private $id;

    /** @param string $id */
    public function __construct($id)
>>>>>>> f330c64 (optimization in progress)
    {
        $this->id = $id;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function assert(Token $token)
=======
    public function assert(Token $token): void
>>>>>>> 22c0e54 (table changes)
=======
    public function assert(Token $token)
>>>>>>> f330c64 (optimization in progress)
    {
        if (! $token->isIdentifiedBy($this->id)) {
            throw new ConstraintViolation(
                'The token is not identified with the expected ID'
            );
        }
    }
}

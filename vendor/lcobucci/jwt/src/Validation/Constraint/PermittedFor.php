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

final class PermittedFor implements Constraint
{
<<<<<<< HEAD
<<<<<<< HEAD
    /** @var string  */
    private $audience;

    public function __construct($audience)
=======
    private string $audience;

    public function __construct(string $audience)
>>>>>>> 22c0e54 (table changes)
=======
    /** @var string  */
    private $audience;

    public function __construct($audience)
>>>>>>> f330c64 (optimization in progress)
    {
        $this->audience = $audience;
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
        if (! $token->isPermittedFor($this->audience)) {
            throw new ConstraintViolation(
                'The token is not allowed to be used by this audience'
            );
        }
    }
}

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

final class IssuedBy implements Constraint
{
    /** @var string[] */
<<<<<<< HEAD
<<<<<<< HEAD
    private $issuers;

    /** @param list<string> $issuers */
    public function __construct(...$issuers)
=======
    private array $issuers;

    public function __construct(string ...$issuers)
>>>>>>> 22c0e54 (table changes)
=======
    private $issuers;

    /** @param list<string> $issuers */
    public function __construct(...$issuers)
>>>>>>> f330c64 (optimization in progress)
    {
        $this->issuers = $issuers;
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
        if (! $token->hasBeenIssuedBy(...$this->issuers)) {
            throw new ConstraintViolation(
                'The token was not issued by the given issuers'
            );
        }
    }
}

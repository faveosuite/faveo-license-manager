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

final class RelatedTo implements Constraint
{
<<<<<<< HEAD
<<<<<<< HEAD
    /** @var string */
    private $subject;

    public function __construct($subject)
=======
    private string $subject;

    public function __construct(string $subject)
>>>>>>> 22c0e54 (table changes)
=======
    /** @var string */
    private $subject;

    public function __construct($subject)
>>>>>>> f330c64 (optimization in progress)
    {
        $this->subject = $subject;
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
        if (! $token->isRelatedTo($this->subject)) {
            throw new ConstraintViolation(
                'The token is not related to the expected subject'
            );
        }
    }
}

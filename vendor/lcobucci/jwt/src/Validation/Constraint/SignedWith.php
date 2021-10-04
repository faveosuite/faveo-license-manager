<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Validation\Constraint;

use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

final class SignedWith implements Constraint
{
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /** @var Signer */
    private $signer;

    /** @var Signer\Key */
    private $key;
<<<<<<< HEAD
=======
    private Signer $signer;
    private Signer\Key $key;
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

    public function __construct(Signer $signer, Signer\Key $key)
    {
        $this->signer = $signer;
        $this->key    = $key;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    public function assert(Token $token)
    {
        if ($token->headers()->get('alg') !== $this->signer->getAlgorithmId()) {
            throw new ConstraintViolation('Token signer mismatch');
        }

        if (! $this->signer->verify((string) $token->signature(), $token->getPayload(), $this->key)) {
=======
    public function assert(Token $token): void
=======
    public function assert(Token $token)
>>>>>>> f330c64 (optimization in progress)
    {
        if ($token->headers()->get('alg') !== $this->signer->getAlgorithmId()) {
            throw new ConstraintViolation('Token signer mismatch');
        }

<<<<<<< HEAD
        if (! $this->signer->verify($token->signature()->hash(), $token->payload(), $this->key)) {
>>>>>>> 22c0e54 (table changes)
=======
        if (! $this->signer->verify((string) $token->signature(), $token->getPayload(), $this->key)) {
>>>>>>> f330c64 (optimization in progress)
            throw new ConstraintViolation('Token signature mismatch');
        }
    }
}

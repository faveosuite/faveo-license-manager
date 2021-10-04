<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Validation\Constraint;

use DateInterval;
use DateTimeInterface;
use Lcobucci\Clock\Clock;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Validation\Constraint;
use Lcobucci\JWT\Validation\ConstraintViolation;

final class ValidAt implements Constraint
{
<<<<<<< HEAD
<<<<<<< HEAD
    /** @var Clock */
    private $clock;

    /** @var DateInterval */
    private $leeway;

    public function __construct(Clock $clock, DateInterval $leeway = null)
=======
    private Clock $clock;
    private DateInterval $leeway;

    public function __construct(Clock $clock, ?DateInterval $leeway = null)
>>>>>>> 22c0e54 (table changes)
=======
    /** @var Clock */
    private $clock;

    /** @var DateInterval */
    private $leeway;

    public function __construct(Clock $clock, DateInterval $leeway = null)
>>>>>>> f330c64 (optimization in progress)
    {
        $this->clock  = $clock;
        $this->leeway = $this->guardLeeway($leeway);
    }

<<<<<<< HEAD
<<<<<<< HEAD
    /** @return DateInterval */
    private function guardLeeway(DateInterval $leeway = null)
=======
    private function guardLeeway(?DateInterval $leeway): DateInterval
>>>>>>> 22c0e54 (table changes)
=======
    /** @return DateInterval */
    private function guardLeeway(DateInterval $leeway = null)
>>>>>>> f330c64 (optimization in progress)
    {
        if ($leeway === null) {
            return new DateInterval('PT0S');
        }

        if ($leeway->invert === 1) {
            throw LeewayCannotBeNegative::create();
        }

        return $leeway;
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
        $now = $this->clock->now();

        $this->assertIssueTime($token, $now->add($this->leeway));
        $this->assertMinimumTime($token, $now->add($this->leeway));
        $this->assertExpiration($token, $now->sub($this->leeway));
    }

    /** @throws ConstraintViolation */
<<<<<<< HEAD
<<<<<<< HEAD
    private function assertExpiration(Token $token, DateTimeInterface $now)
=======
    private function assertExpiration(Token $token, DateTimeInterface $now): void
>>>>>>> 22c0e54 (table changes)
=======
    private function assertExpiration(Token $token, DateTimeInterface $now)
>>>>>>> f330c64 (optimization in progress)
    {
        if ($token->isExpired($now)) {
            throw new ConstraintViolation('The token is expired');
        }
    }

    /** @throws ConstraintViolation */
<<<<<<< HEAD
<<<<<<< HEAD
    private function assertMinimumTime(Token $token, DateTimeInterface $now)
=======
    private function assertMinimumTime(Token $token, DateTimeInterface $now): void
>>>>>>> 22c0e54 (table changes)
=======
    private function assertMinimumTime(Token $token, DateTimeInterface $now)
>>>>>>> f330c64 (optimization in progress)
    {
        if (! $token->isMinimumTimeBefore($now)) {
            throw new ConstraintViolation('The token cannot be used yet');
        }
    }

    /** @throws ConstraintViolation */
<<<<<<< HEAD
<<<<<<< HEAD
    private function assertIssueTime(Token $token, DateTimeInterface $now)
=======
    private function assertIssueTime(Token $token, DateTimeInterface $now): void
>>>>>>> 22c0e54 (table changes)
=======
    private function assertIssueTime(Token $token, DateTimeInterface $now)
>>>>>>> f330c64 (optimization in progress)
    {
        if (! $token->hasBeenIssuedBefore($now)) {
            throw new ConstraintViolation('The token was issued in the future');
        }
    }
}

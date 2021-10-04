<?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
declare(strict_types=1);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

namespace Lcobucci\JWT\Validation;

use Lcobucci\JWT\Exception;
use RuntimeException;

use function array_map;
use function implode;

final class RequiredConstraintsViolated extends RuntimeException implements Exception
{
    /** @var ConstraintViolation[] */
<<<<<<< HEAD
<<<<<<< HEAD
    private $violations = [];

    /**
     * @param ConstraintViolation ...$violations
     * @return self
     */
    public static function fromViolations(ConstraintViolation ...$violations)
=======
    private array $violations = [];

    public static function fromViolations(ConstraintViolation ...$violations): self
>>>>>>> 22c0e54 (table changes)
=======
    private $violations = [];

    /**
     * @param ConstraintViolation ...$violations
     * @return self
     */
    public static function fromViolations(ConstraintViolation ...$violations)
>>>>>>> f330c64 (optimization in progress)
    {
        $exception             = new self(self::buildMessage($violations));
        $exception->violations = $violations;

        return $exception;
    }

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
    /**
     * @param ConstraintViolation[] $violations
     *
     * @return string
     */
    private static function buildMessage(array $violations)
<<<<<<< HEAD
    {
        $violations = array_map(
            static function (ConstraintViolation $violation) {
=======
    /** @param ConstraintViolation[] $violations */
    private static function buildMessage(array $violations): string
    {
        $violations = array_map(
            static function (ConstraintViolation $violation): string {
>>>>>>> 22c0e54 (table changes)
=======
    {
        $violations = array_map(
            static function (ConstraintViolation $violation) {
>>>>>>> f330c64 (optimization in progress)
                return '- ' . $violation->getMessage();
            },
            $violations
        );

        $message  = "The token violates some mandatory constraints, details:\n";
        $message .= implode("\n", $violations);

        return $message;
    }

    /** @return ConstraintViolation[] */
<<<<<<< HEAD
<<<<<<< HEAD
    public function violations()
=======
    public function violations(): array
>>>>>>> 22c0e54 (table changes)
=======
    public function violations()
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->violations;
    }
}

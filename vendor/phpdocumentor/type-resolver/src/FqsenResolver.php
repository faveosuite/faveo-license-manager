<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Context;
<<<<<<< HEAD
<<<<<<< HEAD

=======
>>>>>>> 22c0e54 (table changes)
=======

>>>>>>> f330c64 (optimization in progress)
use function explode;
use function implode;
use function strpos;

/**
 * Resolver for Fqsen using Context information
 *
 * @psalm-immutable
 */
class FqsenResolver
{
    /** @var string Definition of the NAMESPACE operator in PHP */
    private const OPERATOR_NAMESPACE = '\\';

<<<<<<< HEAD
<<<<<<< HEAD
    public function resolve(string $fqsen, ?Context $context = null): Fqsen
=======
    public function resolve(string $fqsen, ?Context $context = null) : Fqsen
>>>>>>> 22c0e54 (table changes)
=======
    public function resolve(string $fqsen, ?Context $context = null): Fqsen
>>>>>>> f330c64 (optimization in progress)
    {
        if ($context === null) {
            $context = new Context('');
        }

        if ($this->isFqsen($fqsen)) {
            return new Fqsen($fqsen);
        }

        return $this->resolvePartialStructuralElementName($fqsen, $context);
    }

    /**
     * Tests whether the given type is a Fully Qualified Structural Element Name.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    private function isFqsen(string $type): bool
=======
    private function isFqsen(string $type) : bool
>>>>>>> 22c0e54 (table changes)
=======
    private function isFqsen(string $type): bool
>>>>>>> f330c64 (optimization in progress)
    {
        return strpos($type, self::OPERATOR_NAMESPACE) === 0;
    }

    /**
     * Resolves a partial Structural Element Name (i.e. `Reflection\DocBlock`) to its FQSEN representation
     * (i.e. `\phpDocumentor\Reflection\DocBlock`) based on the Namespace and aliases mentioned in the Context.
     *
     * @throws InvalidArgumentException When type is not a valid FQSEN.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    private function resolvePartialStructuralElementName(string $type, Context $context): Fqsen
=======
    private function resolvePartialStructuralElementName(string $type, Context $context) : Fqsen
>>>>>>> 22c0e54 (table changes)
=======
    private function resolvePartialStructuralElementName(string $type, Context $context): Fqsen
>>>>>>> f330c64 (optimization in progress)
    {
        $typeParts = explode(self::OPERATOR_NAMESPACE, $type, 2);

        $namespaceAliases = $context->getNamespaceAliases();

        // if the first segment is not an alias; prepend namespace name and return
        if (!isset($namespaceAliases[$typeParts[0]])) {
            $namespace = $context->getNamespace();
            if ($namespace !== '') {
                $namespace .= self::OPERATOR_NAMESPACE;
            }

            return new Fqsen(self::OPERATOR_NAMESPACE . $namespace . $type);
        }

        $typeParts[0] = $namespaceAliases[$typeParts[0]];

        return new Fqsen(self::OPERATOR_NAMESPACE . implode(self::OPERATOR_NAMESPACE, $typeParts));
    }
}

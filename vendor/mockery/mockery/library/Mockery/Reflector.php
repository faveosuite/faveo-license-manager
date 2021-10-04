<?php

/**
 * Mockery
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://github.com/padraic/mockery/blob/master/LICENSE
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to padraic@php.net so we can send you a copy immediately.
 *
 * @category   Mockery
 * @package    Mockery
 * @copyright  Copyright (c) 2017 Dave Marshall https://github.com/davedevelopment
 * @license    http://github.com/padraic/mockery/blob/master/LICENSE New BSD License
 */

namespace Mockery;

/**
 * @internal
 */
class Reflector
{
    /**
     * Determine if the parameter is typed as an array.
     *
     * @param \ReflectionParameter $param
     *
     * @return bool
     */
    public static function isArray(\ReflectionParameter $param)
    {
        $type = $param->getType();

        return $type instanceof \ReflectionNamedType && $type->getName();
    }

    /**
     * Compute the string representation for the paramater type.
     *
     * @param \ReflectionParameter $param
     * @param bool $withoutNullable
     *
     * @return string|null
     */
    public static function getTypeHint(\ReflectionParameter $param, $withoutNullable = false)
    {
        if (!$param->hasType()) {
            return null;
        }

        $type = $param->getType();
        $declaringClass = $param->getDeclaringClass();
        $typeHint = self::typeToString($type, $declaringClass);

        return (!$withoutNullable && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
    }

    /**
     * Compute the string representation for the return type.
     *
     * @param \ReflectionParameter $param
     * @param bool $withoutNullable
     *
     * @return string|null
     */
    public static function getReturnType(\ReflectionMethod $method, $withoutNullable = false)
    {
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        $type = $method->getReturnType();

        if (is_null($type) && method_exists($method, 'getTentativeReturnType')) {
            $type = $method->getTentativeReturnType();
        }

        if (is_null($type)) {
<<<<<<< HEAD
            return null;
        }

        $typeHint = self::typeToString($type, $method->getDeclaringClass());

        return (!$withoutNullable && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
    }

    /**
     * Compute the string representation for the simplest return type.
     *
     * @param \ReflectionParameter $param
     *
     * @return string|null
     */
    public static function getSimplestReturnType(\ReflectionMethod $method)
    {
        $type = $method->getReturnType();

        if (is_null($type) && method_exists($method, 'getTentativeReturnType')) {
            $type = $method->getTentativeReturnType();
        }

        if (is_null($type) || $type->allowsNull()) {
            return null;
        }

        $typeInformation = self::getTypeInformation($type, $method->getDeclaringClass());

        // return the first primitive type hint
        foreach ($typeInformation as $info) {
            if ($info['isPrimitive']) {
                return $info['typeHint'];
            }
        }

        // if no primitive type, return the first type
        foreach ($typeInformation as $info) {
            return $info['typeHint'];
        }

        return null;
=======
        if (!$method->hasReturnType()) {
=======
>>>>>>> f330c64 (optimization in progress)
            return null;
        }

        $typeHint = self::typeToString($type, $method->getDeclaringClass());

        return (!$withoutNullable && $type->allowsNull()) ? self::formatNullableType($typeHint) : $typeHint;
>>>>>>> 22c0e54 (table changes)
    }

    /**
     * Compute the string representation for the simplest return type.
     *
     * @param \ReflectionParameter $param
     *
     * @return string|null
     */
    public static function getSimplestReturnType(\ReflectionMethod $method)
    {
        $type = $method->getReturnType();

        if (is_null($type) && method_exists($method, 'getTentativeReturnType')) {
            $type = $method->getTentativeReturnType();
        }

        if (is_null($type) || $type->allowsNull()) {
            return null;
        }

        $typeInformation = self::getTypeInformation($type, $method->getDeclaringClass());

        // return the first primitive type hint
        foreach ($typeInformation as $info) {
            if ($info['isPrimitive']) {
                return $info['typeHint'];
            }
        }

        // if no primitive type, return the first type
        foreach ($typeInformation as $info) {
            return $info['typeHint'];
        }

        return null;
    }

    /**
     * Get the string representation of the given type.
     *
     * @param \ReflectionType $type
     * @param string $declaringClass
     *
     * @return string|null
     */
    private static function typeToString(\ReflectionType $type, \ReflectionClass $declaringClass)
    {
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        return \implode('|', \array_map(function (array $typeInformation) {
            return $typeInformation['typeHint'];
        }, self::getTypeInformation($type, $declaringClass)));
    }

    /**
     * Get the string representation of the given type.
     *
     * @param \ReflectionType  $type
     * @param \ReflectionClass $declaringClass
     *
     * @return list<array{typeHint: string, isPrimitive: bool}>
     */
    private static function getTypeInformation(\ReflectionType $type, \ReflectionClass $declaringClass)
    {
<<<<<<< HEAD
        // PHP 8 union types can be recursively processed
        if ($type instanceof \ReflectionUnionType) {
            $types = [];

            foreach ($type->getTypes() as $innterType) {
                foreach (self::getTypeInformation($innterType, $declaringClass) as $info) {
                    if ($info['typeHint'] === 'null' && $info['isPrimitive']) {
                        continue;
                    }

                    $types[] = $info;
                }
            }

            return $types;
=======
=======
>>>>>>> f330c64 (optimization in progress)
        // PHP 8 union types can be recursively processed
        if ($type instanceof \ReflectionUnionType) {
            $types = [];

<<<<<<< HEAD
                return $typeHint === 'null' ? null : $typeHint;
            }, $type->getTypes())));
>>>>>>> 22c0e54 (table changes)
=======
            foreach ($type->getTypes() as $innterType) {
                foreach (self::getTypeInformation($innterType, $declaringClass) as $info) {
                    if ($info['typeHint'] === 'null' && $info['isPrimitive']) {
                        continue;
                    }

                    $types[] = $info;
                }
            }

            return $types;
>>>>>>> f330c64 (optimization in progress)
        }

        // $type must be an instance of \ReflectionNamedType
        $typeHint = $type->getName();

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        // builtins can be returned as is
        if ($type->isBuiltin()) {
            return [
                [
                    'typeHint' => $typeHint,
                    'isPrimitive' => in_array($typeHint, ['array', 'bool', 'int', 'float', 'null', 'object', 'string']),
                ],
            ];
        }

        // 'static' can be returned as is
        if ($typeHint === 'static') {
            return [
                [
                    'typeHint' => $typeHint,
                    'isPrimitive' => false,
                ],
            ];
<<<<<<< HEAD
=======
        // builtins and 'static' can be returned as is
        if (($type->isBuiltin() || $typeHint === 'static')) {
            return $typeHint;
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
        }

        // 'self' needs to be resolved to the name of the declaring class
        if ($typeHint === 'self') {
            $typeHint = $declaringClass->getName();
        }

        // 'parent' needs to be resolved to the name of the parent class
        if ($typeHint === 'parent') {
            $typeHint = $declaringClass->getParentClass()->getName();
        }

        // class names need prefixing with a slash
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
        return [
            [
                'typeHint' => sprintf('\\%s', $typeHint),
                'isPrimitive' => false,
            ],
        ];
<<<<<<< HEAD
=======
        return sprintf('\\%s', $typeHint);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
    }

    /**
     * Format the given type as a nullable type.
     *
<<<<<<< HEAD
<<<<<<< HEAD
=======
     * This method MUST only be called on PHP 7.1+.
     *
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
     * @param string $typeHint
     *
     * @return string
     */
    private static function formatNullableType($typeHint)
    {
        if (\PHP_VERSION_ID < 80000) {
            return sprintf('?%s', $typeHint);
        }

        return $typeHint === 'mixed' ? 'mixed' : sprintf('%s|null', $typeHint);
    }
}

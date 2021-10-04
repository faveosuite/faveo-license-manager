<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\DocBlock\Tag;

// phpcs:ignore SlevomatCodingStandard.Classes.SuperfluousInterfaceNaming.SuperfluousSuffix
interface DocBlockFactoryInterface
{
    /**
     * Factory method for easy instantiation.
     *
     * @param array<string, class-string<Tag>> $additionalTags
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public static function createInstance(array $additionalTags = []): DocBlockFactory;
=======
    public static function createInstance(array $additionalTags = []) : DocBlockFactory;
>>>>>>> 22c0e54 (table changes)
=======
    public static function createInstance(array $additionalTags = []): DocBlockFactory;
>>>>>>> f330c64 (optimization in progress)

    /**
     * @param string|object $docblock
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function create($docblock, ?Types\Context $context = null, ?Location $location = null): DocBlock;
=======
    public function create($docblock, ?Types\Context $context = null, ?Location $location = null) : DocBlock;
>>>>>>> 22c0e54 (table changes)
=======
    public function create($docblock, ?Types\Context $context = null, ?Location $location = null): DocBlock;
>>>>>>> f330c64 (optimization in progress)
}

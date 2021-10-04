<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\ControllerMetadata;

/**
 * Builds method argument data.
 *
 * @author Iltar van der Berg <kjarli@gmail.com>
 */
interface ArgumentMetadataFactoryInterface
{
    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * @param string|object|array $controller The controller to resolve the arguments for
=======
     * @param mixed $controller The controller to resolve the arguments for
>>>>>>> 22c0e54 (table changes)
=======
     * @param string|object|array $controller The controller to resolve the arguments for
>>>>>>> f330c64 (optimization in progress)
     *
     * @return ArgumentMetadata[]
     */
    public function createArgumentMetadata($controller);
}

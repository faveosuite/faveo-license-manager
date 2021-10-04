<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Loader;

/**
 * IniFileLoader loads translations from an ini file.
 *
 * @author stealth35
 */
class IniFileLoader extends FileLoader
{
    /**
     * {@inheritdoc}
     */
<<<<<<< HEAD
<<<<<<< HEAD
    protected function loadResource(string $resource)
=======
    protected function loadResource($resource)
>>>>>>> 22c0e54 (table changes)
=======
    protected function loadResource(string $resource)
>>>>>>> f330c64 (optimization in progress)
    {
        return parse_ini_file($resource, true);
    }
}

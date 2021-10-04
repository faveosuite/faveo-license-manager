<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Descriptor;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Descriptor interface.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
interface DescriptorInterface
{
<<<<<<< HEAD
<<<<<<< HEAD
    public function describe(OutputInterface $output, object $object, array $options = []);
=======
    /**
     * Describes an object if supported.
     *
     * @param object $object
     */
    public function describe(OutputInterface $output, $object, array $options = []);
>>>>>>> 22c0e54 (table changes)
=======
    public function describe(OutputInterface $output, object $object, array $options = []);
>>>>>>> f330c64 (optimization in progress)
}

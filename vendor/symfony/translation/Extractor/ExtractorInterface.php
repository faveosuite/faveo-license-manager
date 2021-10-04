<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Translation\Extractor;

use Symfony\Component\Translation\MessageCatalogue;

/**
 * Extracts translation messages from a directory or files to the catalogue.
 * New found messages are injected to the catalogue using the prefix.
 *
 * @author Michel Salib <michelsalib@hotmail.com>
 */
interface ExtractorInterface
{
    /**
     * Extracts translation messages from files, a file or a directory to the catalogue.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param string|iterable<string> $resource Files, a file or a directory
=======
     * @param string|string[] $resource Files, a file or a directory
>>>>>>> 22c0e54 (table changes)
=======
     * @param string|iterable<string> $resource Files, a file or a directory
>>>>>>> f330c64 (optimization in progress)
     */
    public function extract($resource, MessageCatalogue $catalogue);

    /**
     * Sets the prefix that should be used for new found messages.
<<<<<<< HEAD
<<<<<<< HEAD
=======
     *
     * @param string $prefix The prefix
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
     */
    public function setPrefix(string $prefix);
}

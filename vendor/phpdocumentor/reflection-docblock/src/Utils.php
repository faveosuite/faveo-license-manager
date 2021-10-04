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

use phpDocumentor\Reflection\Exception\PcreException;
<<<<<<< HEAD
<<<<<<< HEAD
use Webmozart\Assert\Assert;

=======
>>>>>>> 22c0e54 (table changes)
=======
use Webmozart\Assert\Assert;

>>>>>>> f330c64 (optimization in progress)
use function preg_last_error;
use function preg_split as php_preg_split;

abstract class Utils
{
    /**
     * Wrapper function for phps preg_split
     *
     * This function is inspired by {@link https://github.com/thecodingmachine/safe/blob/master/generated/pcre.php}. But
     * since this library is all about performance we decided to strip everything we don't need. Reducing the amount
     * of files that have to be loaded, ect.
     *
     * @param string $pattern The pattern to search for, as a string.
     * @param string $subject The input string.
<<<<<<< HEAD
<<<<<<< HEAD
     * @param int $limit If specified, then only substrings up to limit are returned with the
=======
     * @param int|null $limit If specified, then only substrings up to limit are returned with the
>>>>>>> 22c0e54 (table changes)
=======
     * @param int $limit If specified, then only substrings up to limit are returned with the
>>>>>>> f330c64 (optimization in progress)
     *      rest of the string being placed in the last substring. A limit of -1 or 0 means "no limit".
     * @param int $flags flags can be any combination of the following flags (combined with the | bitwise operator):
     * *PREG_SPLIT_NO_EMPTY*
     *      If this flag is set, only non-empty pieces will be returned by preg_split().
     * *PREG_SPLIT_DELIM_CAPTURE*
     *      If this flag is set, parenthesized expression in the delimiter pattern will be captured
     *      and returned as well.
     * *PREG_SPLIT_OFFSET_CAPTURE*
     *      If this flag is set, for every occurring match the appendant string offset will also be returned.
     *      Note that this changes the return value in an array where every element is an array consisting of the
     *      matched string at offset 0 and its string offset into subject at offset 1.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @return string[] Returns an array containing substrings of subject
     *                                                      split along boundaries matched by pattern
     *
     * @throws PcreException
     */
    public static function pregSplit(string $pattern, string $subject, int $limit = -1, int $flags = 0): array
=======
     * @return string[] Returns an array containing substrings of subject split along boundaries matched by pattern
     *
     * @throws PcreException
     */
    public static function pregSplit(string $pattern, string $subject, ?int $limit = -1, int $flags = 0) : array
>>>>>>> 22c0e54 (table changes)
=======
     * @return string[] Returns an array containing substrings of subject
     *                                                      split along boundaries matched by pattern
     *
     * @throws PcreException
     */
    public static function pregSplit(string $pattern, string $subject, int $limit = -1, int $flags = 0): array
>>>>>>> f330c64 (optimization in progress)
    {
        $parts = php_preg_split($pattern, $subject, $limit, $flags);
        if ($parts === false) {
            throw PcreException::createFromPhpError(preg_last_error());
        }

<<<<<<< HEAD
<<<<<<< HEAD
        Assert::allString($parts);

=======
>>>>>>> 22c0e54 (table changes)
=======
        Assert::allString($parts);

>>>>>>> f330c64 (optimization in progress)
        return $parts;
    }
}

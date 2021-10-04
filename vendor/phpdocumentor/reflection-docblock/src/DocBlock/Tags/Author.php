<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\DocBlock\Tags;

use InvalidArgumentException;
<<<<<<< HEAD
<<<<<<< HEAD

use function filter_var;
use function preg_match;
use function trim;

=======
use function filter_var;
use function preg_match;
use function trim;
>>>>>>> 22c0e54 (table changes)
=======

use function filter_var;
use function preg_match;
use function trim;

>>>>>>> f330c64 (optimization in progress)
use const FILTER_VALIDATE_EMAIL;

/**
 * Reflection class for an {@}author tag in a Docblock.
 */
final class Author extends BaseTag implements Factory\StaticMethod
{
    /** @var string register that this is the author tag. */
    protected $name = 'author';

    /** @var string The name of the author */
    private $authorName;

    /** @var string The email of the author */
    private $authorEmail;

    /**
     * Initializes this tag with the author name and e-mail.
     */
    public function __construct(string $authorName, string $authorEmail)
    {
        if ($authorEmail && !filter_var($authorEmail, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('The author tag does not have a valid e-mail address');
        }

        $this->authorName  = $authorName;
        $this->authorEmail = $authorEmail;
    }

    /**
     * Gets the author's name.
     *
     * @return string The author's name.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getAuthorName(): string
=======
    public function getAuthorName() : string
>>>>>>> 22c0e54 (table changes)
=======
    public function getAuthorName(): string
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->authorName;
    }

    /**
     * Returns the author's email.
     *
     * @return string The author's email.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getEmail(): string
=======
    public function getEmail() : string
>>>>>>> 22c0e54 (table changes)
=======
    public function getEmail(): string
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->authorEmail;
    }

    /**
     * Returns this tag in string form.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function __toString(): string
=======
    public function __toString() : string
>>>>>>> 22c0e54 (table changes)
=======
    public function __toString(): string
>>>>>>> f330c64 (optimization in progress)
    {
        if ($this->authorEmail) {
            $authorEmail = '<' . $this->authorEmail . '>';
        } else {
            $authorEmail = '';
        }

<<<<<<< HEAD
<<<<<<< HEAD
        $authorName = $this->authorName;
=======
        $authorName = (string) $this->authorName;
>>>>>>> 22c0e54 (table changes)
=======
        $authorName = $this->authorName;
>>>>>>> f330c64 (optimization in progress)

        return $authorName . ($authorEmail !== '' ? ($authorName !== '' ? ' ' : '') . $authorEmail : '');
    }

    /**
<<<<<<< HEAD
<<<<<<< HEAD
     * Attempts to create a new Author object based on the tag body.
     */
    public static function create(string $body): ?self
=======
     * Attempts to create a new Author object based on †he tag body.
     */
    public static function create(string $body) : ?self
>>>>>>> 22c0e54 (table changes)
=======
     * Attempts to create a new Author object based on the tag body.
     */
    public static function create(string $body): ?self
>>>>>>> f330c64 (optimization in progress)
    {
        $splitTagContent = preg_match('/^([^\<]*)(?:\<([^\>]*)\>)?$/u', $body, $matches);
        if (!$splitTagContent) {
            return null;
        }

        $authorName = trim($matches[1]);
        $email      = isset($matches[2]) ? trim($matches[2]) : '';

        return new static($authorName, $email);
    }
}

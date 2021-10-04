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

namespace phpDocumentor\Reflection\DocBlock;

use phpDocumentor\Reflection\DocBlock\Tags\Example;
<<<<<<< HEAD
<<<<<<< HEAD

=======
>>>>>>> 22c0e54 (table changes)
=======

>>>>>>> f330c64 (optimization in progress)
use function array_slice;
use function file;
use function getcwd;
use function implode;
use function is_readable;
use function rtrim;
use function sprintf;
use function trim;
<<<<<<< HEAD
<<<<<<< HEAD

=======
>>>>>>> 22c0e54 (table changes)
=======

>>>>>>> f330c64 (optimization in progress)
use const DIRECTORY_SEPARATOR;

/**
 * Class used to find an example file's location based on a given ExampleDescriptor.
 */
class ExampleFinder
{
    /** @var string */
    private $sourceDirectory = '';

    /** @var string[] */
    private $exampleDirectories = [];

    /**
     * Attempts to find the example contents for the given descriptor.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function find(Example $example): string
=======
    public function find(Example $example) : string
>>>>>>> 22c0e54 (table changes)
=======
    public function find(Example $example): string
>>>>>>> f330c64 (optimization in progress)
    {
        $filename = $example->getFilePath();

        $file = $this->getExampleFileContents($filename);
        if (!$file) {
            return sprintf('** File not found : %s **', $filename);
        }

        return implode('', array_slice($file, $example->getStartingLine() - 1, $example->getLineCount()));
    }

    /**
     * Registers the project's root directory where an 'examples' folder can be expected.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function setSourceDirectory(string $directory = ''): void
=======
    public function setSourceDirectory(string $directory = '') : void
>>>>>>> 22c0e54 (table changes)
=======
    public function setSourceDirectory(string $directory = ''): void
>>>>>>> f330c64 (optimization in progress)
    {
        $this->sourceDirectory = $directory;
    }

    /**
     * Returns the project's root directory where an 'examples' folder can be expected.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getSourceDirectory(): string
=======
    public function getSourceDirectory() : string
>>>>>>> 22c0e54 (table changes)
=======
    public function getSourceDirectory(): string
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->sourceDirectory;
    }

    /**
     * Registers a series of directories that may contain examples.
     *
     * @param string[] $directories
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function setExampleDirectories(array $directories): void
=======
    public function setExampleDirectories(array $directories) : void
>>>>>>> 22c0e54 (table changes)
=======
    public function setExampleDirectories(array $directories): void
>>>>>>> f330c64 (optimization in progress)
    {
        $this->exampleDirectories = $directories;
    }

    /**
     * Returns a series of directories that may contain examples.
     *
     * @return string[]
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function getExampleDirectories(): array
=======
    public function getExampleDirectories() : array
>>>>>>> 22c0e54 (table changes)
=======
    public function getExampleDirectories(): array
>>>>>>> f330c64 (optimization in progress)
    {
        return $this->exampleDirectories;
    }

    /**
     * Attempts to find the requested example file and returns its contents or null if no file was found.
     *
     * This method will try several methods in search of the given example file, the first one it encounters is
     * returned:
     *
     * 1. Iterates through all examples folders for the given filename
     * 2. Checks the source folder for the given filename
     * 3. Checks the 'examples' folder in the current working directory for examples
     * 4. Checks the path relative to the current working directory for the given filename
     *
     * @return string[] all lines of the example file
     */
<<<<<<< HEAD
<<<<<<< HEAD
    private function getExampleFileContents(string $filename): ?array
=======
    private function getExampleFileContents(string $filename) : ?array
>>>>>>> 22c0e54 (table changes)
=======
    private function getExampleFileContents(string $filename): ?array
>>>>>>> f330c64 (optimization in progress)
    {
        $normalizedPath = null;

        foreach ($this->exampleDirectories as $directory) {
            $exampleFileFromConfig = $this->constructExamplePath($directory, $filename);
            if (is_readable($exampleFileFromConfig)) {
                $normalizedPath = $exampleFileFromConfig;
                break;
            }
        }

        if (!$normalizedPath) {
            if (is_readable($this->getExamplePathFromSource($filename))) {
                $normalizedPath = $this->getExamplePathFromSource($filename);
            } elseif (is_readable($this->getExamplePathFromExampleDirectory($filename))) {
                $normalizedPath = $this->getExamplePathFromExampleDirectory($filename);
            } elseif (is_readable($filename)) {
                $normalizedPath = $filename;
            }
        }

        $lines = $normalizedPath && is_readable($normalizedPath) ? file($normalizedPath) : false;

        return $lines !== false ? $lines : null;
    }

    /**
     * Get example filepath based on the example directory inside your project.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    private function getExamplePathFromExampleDirectory(string $file): string
=======
    private function getExamplePathFromExampleDirectory(string $file) : string
>>>>>>> 22c0e54 (table changes)
=======
    private function getExamplePathFromExampleDirectory(string $file): string
>>>>>>> f330c64 (optimization in progress)
    {
        return getcwd() . DIRECTORY_SEPARATOR . 'examples' . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Returns a path to the example file in the given directory..
     */
<<<<<<< HEAD
<<<<<<< HEAD
    private function constructExamplePath(string $directory, string $file): string
=======
    private function constructExamplePath(string $directory, string $file) : string
>>>>>>> 22c0e54 (table changes)
=======
    private function constructExamplePath(string $directory, string $file): string
>>>>>>> f330c64 (optimization in progress)
    {
        return rtrim($directory, '\\/') . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Get example filepath based on sourcecode.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    private function getExamplePathFromSource(string $file): string
=======
    private function getExamplePathFromSource(string $file) : string
>>>>>>> 22c0e54 (table changes)
=======
    private function getExamplePathFromSource(string $file): string
>>>>>>> f330c64 (optimization in progress)
    {
        return sprintf(
            '%s%s%s',
            trim($this->getSourceDirectory(), '\\/'),
            DIRECTORY_SEPARATOR,
            trim($file, '"')
        );
    }
}

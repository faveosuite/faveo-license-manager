<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Input;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;

/**
 * InputInterface is the interface implemented by all input classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface InputInterface
{
    /**
     * Returns the first argument from the raw parameters (not parsed).
     *
     * @return string|null The value of the first argument or null otherwise
     */
    public function getFirstArgument();

    /**
     * Returns true if the raw parameters (not parsed) contain a value.
     *
     * This method is to be used to introspect the input parameters
     * before they have been validated. It must be used carefully.
     * Does not necessarily return the correct result for short options
     * when multiple flags are combined in the same option.
     *
     * @param string|array $values     The values to look for in the raw parameters (can be an array)
     * @param bool         $onlyParams Only check real parameters, skip those following an end of options (--) signal
     *
     * @return bool true if the value is contained in the raw parameters
     */
    public function hasParameterOption($values, bool $onlyParams = false);

    /**
     * Returns the value of a raw option (not parsed).
     *
     * This method is to be used to introspect the input parameters
     * before they have been validated. It must be used carefully.
     * Does not necessarily return the correct result for short options
     * when multiple flags are combined in the same option.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param string|array                     $values     The value(s) to look for in the raw parameters (can be an array)
     * @param string|bool|int|float|array|null $default    The default value to return if no result is found
     * @param bool                             $onlyParams Only check real parameters, skip those following an end of options (--) signal
=======
     * @param string|array $values     The value(s) to look for in the raw parameters (can be an array)
     * @param mixed        $default    The default value to return if no result is found
     * @param bool         $onlyParams Only check real parameters, skip those following an end of options (--) signal
>>>>>>> 22c0e54 (table changes)
=======
     * @param string|array                     $values     The value(s) to look for in the raw parameters (can be an array)
     * @param string|bool|int|float|array|null $default    The default value to return if no result is found
     * @param bool                             $onlyParams Only check real parameters, skip those following an end of options (--) signal
>>>>>>> f330c64 (optimization in progress)
     *
     * @return mixed The option value
     */
    public function getParameterOption($values, $default = false, bool $onlyParams = false);

    /**
     * Binds the current Input instance with the given arguments and options.
     *
     * @throws RuntimeException
     */
    public function bind(InputDefinition $definition);

    /**
     * Validates the input.
     *
     * @throws RuntimeException When not enough arguments are given
     */
    public function validate();

    /**
     * Returns all the given arguments merged with the default values.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @return array<string|bool|int|float|array|null>
=======
     * @return array
>>>>>>> 22c0e54 (table changes)
=======
     * @return array<string|bool|int|float|array|null>
>>>>>>> f330c64 (optimization in progress)
     */
    public function getArguments();

    /**
     * Returns the argument value for a given argument name.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @return mixed
=======
     * @return string|string[]|null The argument value
>>>>>>> 22c0e54 (table changes)
=======
     * @return mixed
>>>>>>> f330c64 (optimization in progress)
     *
     * @throws InvalidArgumentException When argument given doesn't exist
     */
    public function getArgument(string $name);

    /**
     * Sets an argument value by name.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param mixed $value The argument value
=======
     * @param string|string[]|null $value The argument value
>>>>>>> 22c0e54 (table changes)
=======
     * @param mixed $value The argument value
>>>>>>> f330c64 (optimization in progress)
     *
     * @throws InvalidArgumentException When argument given doesn't exist
     */
    public function setArgument(string $name, $value);

    /**
     * Returns true if an InputArgument object exists by name or position.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @return bool true if the InputArgument object exists, false otherwise
     */
    public function hasArgument(string $name);
=======
     * @param string|int $name The InputArgument name or position
     *
     * @return bool true if the InputArgument object exists, false otherwise
     */
    public function hasArgument($name);
>>>>>>> 22c0e54 (table changes)
=======
     * @return bool true if the InputArgument object exists, false otherwise
     */
    public function hasArgument(string $name);
>>>>>>> f330c64 (optimization in progress)

    /**
     * Returns all the given options merged with the default values.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @return array<string|bool|int|float|array|null>
=======
     * @return array
>>>>>>> 22c0e54 (table changes)
=======
     * @return array<string|bool|int|float|array|null>
>>>>>>> f330c64 (optimization in progress)
     */
    public function getOptions();

    /**
     * Returns the option value for a given option name.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @return mixed
=======
     * @return string|string[]|bool|null The option value
>>>>>>> 22c0e54 (table changes)
=======
     * @return mixed
>>>>>>> f330c64 (optimization in progress)
     *
     * @throws InvalidArgumentException When option given doesn't exist
     */
    public function getOption(string $name);

    /**
     * Sets an option value by name.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param mixed $value The option value
=======
     * @param string|string[]|bool|null $value The option value
>>>>>>> 22c0e54 (table changes)
=======
     * @param mixed $value The option value
>>>>>>> f330c64 (optimization in progress)
     *
     * @throws InvalidArgumentException When option given doesn't exist
     */
    public function setOption(string $name, $value);

    /**
     * Returns true if an InputOption object exists by name.
     *
     * @return bool true if the InputOption object exists, false otherwise
     */
    public function hasOption(string $name);

    /**
     * Is this input means interactive?
     *
     * @return bool
     */
    public function isInteractive();

    /**
     * Sets the input interactivity.
     */
    public function setInteractive(bool $interactive);
}

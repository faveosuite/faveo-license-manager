<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Allows to inspect input and output of a command.
 *
 * @author Francesco Levorato <git@flevour.net>
 */
class ConsoleEvent extends Event
{
    protected $command;

    private $input;
    private $output;

<<<<<<< HEAD
<<<<<<< HEAD
    public function __construct(?Command $command, InputInterface $input, OutputInterface $output)
=======
    public function __construct(Command $command = null, InputInterface $input, OutputInterface $output)
>>>>>>> 22c0e54 (table changes)
=======
    public function __construct(?Command $command, InputInterface $input, OutputInterface $output)
>>>>>>> f330c64 (optimization in progress)
    {
        $this->command = $command;
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * Gets the command that is executed.
     *
     * @return Command|null A Command instance
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Gets the input instance.
     *
     * @return InputInterface An InputInterface instance
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Gets the output instance.
     *
     * @return OutputInterface An OutputInterface instance
     */
    public function getOutput()
    {
        return $this->output;
    }
}

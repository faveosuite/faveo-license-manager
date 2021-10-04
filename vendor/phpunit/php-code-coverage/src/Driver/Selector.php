<?php declare(strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\Driver;

use function phpversion;
use function version_compare;
use SebastianBergmann\CodeCoverage\Filter;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
use SebastianBergmann\Environment\Runtime;

final class Selector
{
    /**
     * @throws NoCodeCoverageDriverAvailableException
     * @throws PcovNotAvailableException
     * @throws PhpdbgNotAvailableException
<<<<<<< HEAD
<<<<<<< HEAD
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     * @throws XdebugNotAvailableException
=======
     * @throws XdebugNotAvailableException
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
>>>>>>> 22c0e54 (table changes)
=======
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     * @throws XdebugNotAvailableException
>>>>>>> f330c64 (optimization in progress)
     */
    public function forLineCoverage(Filter $filter): Driver
    {
        $runtime = new Runtime;

        if ($runtime->hasPHPDBGCodeCoverage()) {
            return new PhpdbgDriver;
        }

        if ($runtime->hasPCOV()) {
            return new PcovDriver($filter);
        }

        if ($runtime->hasXdebug()) {
            if (version_compare(phpversion('xdebug'), '3', '>=')) {
                $driver = new Xdebug3Driver($filter);
            } else {
                $driver = new Xdebug2Driver($filter);
            }

            $driver->enableDeadCodeDetection();

            return $driver;
        }

        throw new NoCodeCoverageDriverAvailableException;
    }

    /**
     * @throws NoCodeCoverageDriverWithPathCoverageSupportAvailableException
<<<<<<< HEAD
<<<<<<< HEAD
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     * @throws XdebugNotAvailableException
=======
     * @throws XdebugNotAvailableException
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
>>>>>>> 22c0e54 (table changes)
=======
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     * @throws XdebugNotAvailableException
>>>>>>> f330c64 (optimization in progress)
     */
    public function forLineAndPathCoverage(Filter $filter): Driver
    {
        if ((new Runtime)->hasXdebug()) {
            if (version_compare(phpversion('xdebug'), '3', '>=')) {
                $driver = new Xdebug3Driver($filter);
            } else {
                $driver = new Xdebug2Driver($filter);
            }

            $driver->enableDeadCodeDetection();
            $driver->enableBranchAndPathCoverage();

            return $driver;
        }

        throw new NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
    }
}

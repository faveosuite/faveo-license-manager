<?php

namespace Facade\Ignition\Facades;

<<<<<<< HEAD
<<<<<<< HEAD
use Facade\Ignition\Support\SentReports;
=======
>>>>>>> 22c0e54 (table changes)
=======
use Facade\Ignition\Support\SentReports;
>>>>>>> f330c64 (optimization in progress)
use Illuminate\Support\Facades\Facade;

/**
 * Class Flare.
 *
 * @method static void glow(string $name, string $messageLevel = \Facade\FlareClient\Enums\MessageLevels::INFO, array $metaData = [])
 * @method static void context($key, $value)
 * @method static void group(string $groupName, array $properties)
 *
 * @see \Facade\FlareClient\Flare
 */
class Flare extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Facade\FlareClient\Flare::class;
    }
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)

    public static function sentReports(): SentReports
    {
        return app(SentReports::class);
    }
<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
}

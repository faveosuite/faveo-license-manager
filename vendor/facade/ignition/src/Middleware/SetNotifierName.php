<?php

namespace Facade\Ignition\Middleware;

use Facade\FlareClient\Report;

class SetNotifierName
{
<<<<<<< HEAD
<<<<<<< HEAD
    public const NOTIFIER_NAME = 'Laravel Client';
=======
    const NOTIFIER_NAME = 'Laravel Client';
>>>>>>> 22c0e54 (table changes)
=======
    public const NOTIFIER_NAME = 'Laravel Client';
>>>>>>> f330c64 (optimization in progress)

    public function handle(Report $report, $next)
    {
        $report->notifierName(static::NOTIFIER_NAME);

        return $next($report);
    }
}

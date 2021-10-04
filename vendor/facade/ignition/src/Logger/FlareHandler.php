<?php

namespace Facade\Ignition\Logger;

use Facade\FlareClient\Flare;
use Facade\FlareClient\Report;
use Facade\Ignition\Ignition;
<<<<<<< HEAD
<<<<<<< HEAD
use Facade\Ignition\Support\SentReports;
=======
>>>>>>> 22c0e54 (table changes)
=======
use Facade\Ignition\Support\SentReports;
>>>>>>> f330c64 (optimization in progress)
use Facade\Ignition\Tabs\Tab;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Throwable;

class FlareHandler extends AbstractProcessingHandler
{
    /** @var \Facade\FlareClient\Flare */
    protected $flare;

<<<<<<< HEAD
<<<<<<< HEAD
    /** @var \Facade\Ignition\Support\SentReports */
    protected $sentReports;

    protected $minimumReportLogLevel = Logger::ERROR;

    public function __construct(Flare $flare, SentReports $sentReports, $level = Logger::DEBUG, $bubble = true)
    {
        $this->flare = $flare;

        $this->sentReports = $sentReports;

=======
=======
    /** @var \Facade\Ignition\Support\SentReports */
    protected $sentReports;

>>>>>>> f330c64 (optimization in progress)
    protected $minimumReportLogLevel = Logger::ERROR;

    public function __construct(Flare $flare, SentReports $sentReports, $level = Logger::DEBUG, $bubble = true)
    {
        $this->flare = $flare;

<<<<<<< HEAD
>>>>>>> 22c0e54 (table changes)
=======
        $this->sentReports = $sentReports;

>>>>>>> f330c64 (optimization in progress)
        parent::__construct($level, $bubble);
    }

    public function setMinimumReportLogLevel(int $level)
    {
        if (! in_array($level, Logger::getLevels())) {
            throw new \InvalidArgumentException('The given minimum log level is not supported.');
        }

        $this->minimumReportLogLevel = $level;
    }

<<<<<<< HEAD
<<<<<<< HEAD
    protected function write(array $record): void
    {
        if (! $this->shouldReport($record)) {
            return;
        }

        if ($this->hasException($record)) {
            /** @var Throwable $throwable */
            $throwable = $record['context']['exception'];
=======
    protected function write(array $report): void
=======
    protected function write(array $record): void
>>>>>>> f330c64 (optimization in progress)
    {
        if (! $this->shouldReport($record)) {
            return;
        }

        if ($this->hasException($record)) {
            /** @var Throwable $throwable */
<<<<<<< HEAD
            $throwable = $report['context']['exception'];
>>>>>>> 22c0e54 (table changes)
=======
            $throwable = $record['context']['exception'];
>>>>>>> f330c64 (optimization in progress)

            collect(Ignition::$tabs)
                ->each(function (Tab $tab) use ($throwable) {
                    $tab->beforeRenderingErrorPage($this->flare, $throwable);
                });

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
            $report = $this->flare->report($record['context']['exception']);

            if ($report) {
                $this->sentReports->add($report);
            }
<<<<<<< HEAD
=======
            $this->flare->report($report['context']['exception']);
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

            return;
        }

        if (config('flare.send_logs_as_events')) {
<<<<<<< HEAD
<<<<<<< HEAD
            if ($this->hasValidLogLevel($record)) {
                $this->flare->reportMessage(
                    $record['message'],
                    'Log ' . Logger::getLevelName($record['level']),
                    function (Report $flareReport) use ($record) {
                        foreach ($record['context'] as $key => $value) {
=======
            if ($this->hasValidLogLevel($report)) {
                $this->flare->reportMessage(
                    $report['message'],
                    'Log ' . Logger::getLevelName($report['level']),
                    function (Report $flareReport) use ($report) {
                        foreach ($report['context'] as $key => $value) {
>>>>>>> 22c0e54 (table changes)
=======
            if ($this->hasValidLogLevel($record)) {
                $this->flare->reportMessage(
                    $record['message'],
                    'Log ' . Logger::getLevelName($record['level']),
                    function (Report $flareReport) use ($record) {
                        foreach ($record['context'] as $key => $value) {
>>>>>>> f330c64 (optimization in progress)
                            $flareReport->context($key, $value);
                        }
                    }
                );
            }
        }
    }

    protected function shouldReport(array $report): bool
    {
        if (! config('flare.key')) {
            return false;
        }

        return $this->hasException($report) || $this->hasValidLogLevel($report);
    }

    protected function hasException(array $report): bool
    {
        $context = $report['context'];

        return isset($context['exception']) && $context['exception'] instanceof Throwable;
    }

    protected function hasValidLogLevel(array $report): bool
    {
        return $report['level'] >= $this->minimumReportLogLevel;
    }
}

<?php

namespace Facade\Ignition\SolutionProviders;

use Facade\Ignition\Solutions\RunMigrationsSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Illuminate\Database\QueryException;
use Throwable;

class MissingColumnSolutionProvider implements HasSolutionsForThrowable
{
    /**
     * See https://dev.mysql.com/doc/refman/8.0/en/server-error-reference.html#error_er_bad_field_error.
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public const MYSQL_BAD_FIELD_CODE = '42S22';
=======
    const MYSQL_BAD_FIELD_CODE = '42S22';
>>>>>>> 22c0e54 (table changes)
=======
    public const MYSQL_BAD_FIELD_CODE = '42S22';
>>>>>>> f330c64 (optimization in progress)

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof QueryException) {
            return false;
        }

        return  $this->isBadTableErrorCode($throwable->getCode());
    }

    protected function isBadTableErrorCode($code): bool
    {
        return $code === static::MYSQL_BAD_FIELD_CODE;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new RunMigrationsSolution('A column was not found')];
    }
}

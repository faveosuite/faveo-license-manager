<?php

namespace Illuminate\Validation\Rules;

use Illuminate\Support\Traits\Conditionable;

class Exists
{
    use Conditionable, DatabaseRule;

    /**
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> f330c64 (optimization in progress)
     * Ignore soft deleted models during the existence check.
     *
     * @param  string  $deletedAtColumn
     * @return $this
     */
    public function withoutTrashed($deletedAtColumn = 'deleted_at')
    {
        $this->whereNull($deletedAtColumn);

        return $this;
    }

    /**
<<<<<<< HEAD
=======
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString()
    {
        return rtrim(sprintf('exists:%s,%s,%s',
            $this->table,
            $this->column,
            $this->formatWheres()
        ), ',');
    }
}

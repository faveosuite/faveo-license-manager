<?php

namespace Illuminate\Database\Eloquent;

use RuntimeException;

class RelationNotFoundException extends RuntimeException
{
    /**
     * The name of the affected Eloquent model.
     *
     * @var string
     */
    public $model;

    /**
     * The name of the relation.
     *
     * @var string
     */
    public $relation;

    /**
     * Create a new exception instance.
     *
     * @param  object  $model
     * @param  string  $relation
<<<<<<< HEAD
<<<<<<< HEAD
     * @param  string|null  $type
     * @return static
     */
    public static function make($model, $relation, $type = null)
    {
        $class = get_class($model);

        $instance = new static(
            is_null($type)
                ? "Call to undefined relationship [{$relation}] on model [{$class}]."
                : "Call to undefined relationship [{$relation}] on model [{$class}] of type [{$type}].",
        );
=======
=======
     * @param  string|null  $type
>>>>>>> f330c64 (optimization in progress)
     * @return static
     */
    public static function make($model, $relation, $type = null)
    {
        $class = get_class($model);

<<<<<<< HEAD
        $instance = new static("Call to undefined relationship [{$relation}] on model [{$class}].");
>>>>>>> 22c0e54 (table changes)
=======
        $instance = new static(
            is_null($type)
                ? "Call to undefined relationship [{$relation}] on model [{$class}]."
                : "Call to undefined relationship [{$relation}] on model [{$class}] of type [{$type}].",
        );
>>>>>>> f330c64 (optimization in progress)

        $instance->model = $class;
        $instance->relation = $relation;

        return $instance;
    }
}

<?php

namespace Illuminate\Support\Traits;

trait Conditionable
{
    /**
     * Apply the callback if the given "value" is truthy.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @param  callable|null  $default
<<<<<<< HEAD
<<<<<<< HEAD
     * @return $this|mixed
=======
     *
     * @return mixed
>>>>>>> 22c0e54 (table changes)
=======
     * @return $this|mixed
>>>>>>> f330c64 (optimization in progress)
     */
    public function when($value, $callback, $default = null)
    {
        if ($value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }

    /**
     * Apply the callback if the given "value" is falsy.
     *
     * @param  mixed  $value
     * @param  callable  $callback
     * @param  callable|null  $default
<<<<<<< HEAD
<<<<<<< HEAD
     * @return $this|mixed
=======
     *
     * @return mixed
>>>>>>> 22c0e54 (table changes)
=======
     * @return $this|mixed
>>>>>>> f330c64 (optimization in progress)
     */
    public function unless($value, $callback, $default = null)
    {
        if (! $value) {
            return $callback($this, $value) ?: $this;
        } elseif ($default) {
            return $default($this, $value) ?: $this;
        }

        return $this;
    }
}

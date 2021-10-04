<?php

namespace Illuminate\Database\Eloquent\Casts;

use ArrayObject as BaseArrayObject;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
<<<<<<< HEAD
<<<<<<< HEAD
=======
use ReturnTypeWillChange;
>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)

class ArrayObject extends BaseArrayObject implements Arrayable, JsonSerializable
{
    /**
     * Get a collection containing the underlying array.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collect()
    {
        return collect($this->getArrayCopy());
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * Get the array that should be JSON serialized.
     *
     * @return array
     */
<<<<<<< HEAD
<<<<<<< HEAD
    #[\ReturnTypeWillChange]
=======
    #[ReturnTypeWillChange]
>>>>>>> 22c0e54 (table changes)
=======
    #[\ReturnTypeWillChange]
>>>>>>> f330c64 (optimization in progress)
    public function jsonSerialize()
    {
        return $this->getArrayCopy();
    }
}

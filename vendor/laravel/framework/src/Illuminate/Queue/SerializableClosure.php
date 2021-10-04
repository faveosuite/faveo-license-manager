<?php

namespace Illuminate\Queue;

use Opis\Closure\SerializableClosure as OpisSerializableClosure;

<<<<<<< HEAD
<<<<<<< HEAD
/**
 * @deprecated This class will be removed in Laravel 9.
 */
=======
>>>>>>> 22c0e54 (table changes)
=======
/**
 * @deprecated This class will be removed in Laravel 9.
 */
>>>>>>> f330c64 (optimization in progress)
class SerializableClosure extends OpisSerializableClosure
{
    use SerializesAndRestoresModelIdentifiers;

    /**
     * Transform the use variables before serialization.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param  array  $data
=======
     * @param  array  $data The Closure's use variables
>>>>>>> 22c0e54 (table changes)
=======
     * @param  array  $data
>>>>>>> f330c64 (optimization in progress)
     * @return array
     */
    protected function transformUseVariables($data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = $this->getSerializedPropertyValue($value);
        }

        return $data;
    }

    /**
     * Resolve the use variables after unserialization.
     *
<<<<<<< HEAD
<<<<<<< HEAD
     * @param  array  $data
=======
     * @param  array  $data The Closure's transformed use variables
>>>>>>> 22c0e54 (table changes)
=======
     * @param  array  $data
>>>>>>> f330c64 (optimization in progress)
     * @return array
     */
    protected function resolveUseVariables($data)
    {
        foreach ($data as $key => $value) {
            $data[$key] = $this->getRestoredPropertyValue($value);
        }

        return $data;
    }
}

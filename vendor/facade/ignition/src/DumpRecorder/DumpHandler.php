<?php

namespace Facade\Ignition\DumpRecorder;

use Symfony\Component\VarDumper\Cloner\VarCloner;

class DumpHandler
{
    /** @var \Facade\Ignition\DumpRecorder\DumpRecorder */
    protected $dumpRecorder;

    public function __construct(DumpRecorder $dumpRecorder)
    {
        $this->dumpRecorder = $dumpRecorder;
    }

    public function dump($value)
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $data = (new VarCloner())->cloneVar($value);
=======
        $data = (new VarCloner)->cloneVar($value);
>>>>>>> 22c0e54 (table changes)
=======
        $data = (new VarCloner())->cloneVar($value);
>>>>>>> f330c64 (optimization in progress)

        $this->dumpRecorder->record($data);
    }
}

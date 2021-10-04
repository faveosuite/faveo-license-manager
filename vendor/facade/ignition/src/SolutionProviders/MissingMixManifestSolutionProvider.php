<?php

<<<<<<< HEAD
<<<<<<< HEAD
=======

>>>>>>> 22c0e54 (table changes)
=======
>>>>>>> f330c64 (optimization in progress)
namespace Facade\Ignition\SolutionProviders;

use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\HasSolutionsForThrowable;
use Illuminate\Support\Str;
use Throwable;

class MissingMixManifestSolutionProvider implements HasSolutionsForThrowable
{
    public function canSolve(Throwable $throwable): bool
    {
        return Str::startsWith($throwable->getMessage(), 'The Mix manifest does not exist');
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [
            BaseSolution::create('Missing Mix Manifest File')
<<<<<<< HEAD
<<<<<<< HEAD
                ->setSolutionDescription('Did you forget to run `npm ci && npm run dev`?'),
=======
                ->setSolutionDescription('Did you forget to run `npm install && npm run dev`?'),
>>>>>>> 22c0e54 (table changes)
=======
                ->setSolutionDescription('Did you forget to run `npm ci && npm run dev`?'),
>>>>>>> f330c64 (optimization in progress)
        ];
    }
}

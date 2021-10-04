<?php

declare(strict_types=1);

namespace Faker\Extension;

use Faker\Generator;

/**
 * A helper trait to be used with GeneratorAwareExtension.
 */
trait GeneratorAwareExtensionTrait
{
    /**
     * @var Generator|null
     */
    private $generator;

    /**
     * @return static
     */
<<<<<<< HEAD
<<<<<<< HEAD
    public function withGenerator(Generator $generator): Extension
=======
    public function withGenerator(Generator $generator): self
>>>>>>> 22c0e54 (table changes)
=======
    public function withGenerator(Generator $generator): Extension
>>>>>>> f330c64 (optimization in progress)
    {
        $instance = clone $this;

        $instance->generator = $generator;

        return $instance;
    }
}

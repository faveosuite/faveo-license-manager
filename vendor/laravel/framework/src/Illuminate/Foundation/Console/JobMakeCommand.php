<?php

namespace Illuminate\Foundation\Console;

<<<<<<< HEAD
<<<<<<< HEAD
use Illuminate\Console\Concerns\CreatesMatchingTest;
=======
>>>>>>> 22c0e54 (table changes)
=======
use Illuminate\Console\Concerns\CreatesMatchingTest;
>>>>>>> f330c64 (optimization in progress)
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class JobMakeCommand extends GeneratorCommand
{
<<<<<<< HEAD
<<<<<<< HEAD
    use CreatesMatchingTest;

=======
>>>>>>> 22c0e54 (table changes)
=======
    use CreatesMatchingTest;

>>>>>>> f330c64 (optimization in progress)
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new job class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Job';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('sync')
                        ? $this->resolveStubPath('/stubs/job.stub')
                        : $this->resolveStubPath('/stubs/job.queued.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Jobs';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['sync', null, InputOption::VALUE_NONE, 'Indicates that job should be synchronous'],
        ];
    }
}

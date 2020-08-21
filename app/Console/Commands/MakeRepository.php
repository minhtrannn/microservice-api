<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeRepository extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return void
     */
    protected function replaceClass($stub, $name)
    {   
        $stub = parent::replaceClass($stub, $name);

        return $this->model()
                ? str_replace(['DummyRepository', 'DummyModel'], [$this->getNameInput(), $this->model()], $stub)
                : str_replace('DummyRepository', $this->getNameInput(), $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->model()
                ? app_path() . '/Console/Commands/Stubs/Repository/make-repository-model.stub'
                : app_path() . '/Console/Commands/Stubs/Repository/make-repository.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }

    /**
     * Determine if the command is generating a repository model.
     *
     * @return model
     */
    protected function model()
    {
        return $this->input->getOption('model') ?: '';
    }

    /**
     * Get the desired model name from the input.
     *
     * @return string
     */
    protected function getModelInput()
    {
        return trim($this->argument('model'));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The model of the repository.'],
        ];
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeRpcController extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:rpcController';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new rpc controller';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

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

        return str_replace('DummyController', $this->getNameInput(), $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resource()
                ? app_path() . '/Console/Commands/Stubs/RpcController/make-rpc-controller-resource.stub'
                : app_path() . '/Console/Commands/Stubs/RpcController/make-rpc-controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }

    /**
     * Determine if the command is generating a rpc controller resource.
     *
     * @return resource
     */
    protected function resource()
    {
        return $this->input->getOption('resource') ?: '';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['resource', 'r', InputOption::VALUE_OPTIONAL, 'The resource rpc controller.'],
        ];
    }
}

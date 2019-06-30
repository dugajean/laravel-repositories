<?php

namespace Dugajean\Repositories\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Dugajean\Repositories\Console\Commands\BaseCommand;

abstract class BaseCreator
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var BaseCommand
     */
    protected $command;

    /**
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Create the repository.
     *
     * @param string      $name
     * @param string      $model
     * @param BaseCommand $command
     *
     * @return int
     */
    public function create($name, $model, BaseCommand $command)
    {
        $this->command = $command;

        $this->setName($name);
        $this->setModel($model);
        $this->createDirectory();

        return $this->createClass();
    }

    /**
     * Get the repository directory.
     *
     * @return mixed
     */
    protected function getDirectory()
    {
        return Config::get("repositories.{$this->command->getCurrentEntity()}_path");
    }

    /**
     * Create the necessary directory.
     */
    protected function createDirectory()
    {
        $directory = $this->getDirectory();

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Get the path.
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getName() . '.php';
    }

    /**
     * Get the stub.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStub()
    {
        return $this->files->get($this->getStubPath() . $this->command->getCurrentEntity() . '.stub');
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStubPath()
    {
        return __DIR__ . '/../../../../resources/stubs/';
    }

    /**
     * Populate the stub.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function populateStub()
    {
        $populate_data = $this->getPopulateData();
        $stub = $this->getStub();

        foreach ($populate_data as $key => $value) {
            $stub = str_replace($key, $value, $stub);
        }

        return $stub;
    }

    /**
     * Generate the class file.
     *
     * @return bool
     */
    abstract protected function createClass();

    /**
     * Fetch the replacement data for the stub.
     *
     * @return array
     */
    abstract protected function getPopulateData();
}

<?php

namespace Dugajean\Repositories\Console\Commands\Creators;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Doctrine\Common\Inflector\Inflector;

class CriteriaCreator
{
    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $criteria;

    /**
     * @var string
     */
    protected $model;

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
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param string $criteria
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
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
     * Create the criteria.
     *
     * @param string $criteria
     * @param string $model
     *
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function create($criteria, $model)
    {
        $this->setCriteria($criteria);
        $this->setModel($model);
        $this->createDirectory();

        return $this->createClass();
    }


    /**
     * Create the criteria directory.
     */
    public function createDirectory()
    {
        $directory = $this->getDirectory();

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Get the criteria directory.
     *
     * @return string
     */
    public function getDirectory()
    {
        $model = $this->getModel();
        $directory = Config::get('repositories.criteria_path');

        if (isset($model) && !empty($model)) {
            $directory .= DIRECTORY_SEPARATOR . $this->pluralizeModel();
        }

        return $directory;
    }


    /**
     * Get the populate data.
     *
     * @return array
     */
    protected function getPopulateData()
    {
        $criteria = $this->getCriteria();
        $model = $this->pluralizeModel();
        $criteriaNamespace = Config::get('repositories.criteria_namespace');
        $criteriaClass = $criteria;

        if (isset($model) && !empty($model)) {
            $criteriaNamespace .= '\\' . $model;
        }

        $populateData = [
            'criteria_namespace' => $criteriaNamespace,
            'criteria_class' => $criteriaClass,
        ];

        return $populateData;
    }

    /**
     * Get the path.
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getCriteria() . '.php';
    }

    /**
     * Get the stub.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStub()
    {
        return $this->files->get($this->getStubPath() . "criteria.stub");
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
        $populateData = $this->getPopulateData();
        $stub = $this->getStub();

        foreach ($populateData as $search => $replace) {
            $stub = str_replace($search, $replace, $stub);
        }

        return $stub;
    }

    /**
     * Create the repository class.
     *
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createClass()
    {
        if ($this->files->exists($this->getPath())) {
            throw new \RuntimeException("The criteria with the name '$this->criteria' already exists.");
        }

        return $this->files->put($this->getPath(), $this->populateStub());
    }

    /**
     * Pluralize the model.
     *
     * @return string
     */
    protected function pluralizeModel()
    {
        if (null === $this->getModel()) {
            return '';
        }

        $pluralized = Inflector::pluralize($this->getModel());
        $modelName = ucfirst($pluralized);

        return $modelName;
    }
}

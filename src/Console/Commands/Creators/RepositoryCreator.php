<?php

namespace Dugajean\Repositories\Console\Commands\Creators;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Doctrine\Common\Inflector\Inflector;

class RepositoryCreator extends BaseCreator
{
    /**
     * Get the repository name.
     *
     * @return mixed|string
     */
    public function getName()
    {
        $repositoryName = parent::getName();

        if (strpos($repositoryName, 'Repository') === false) {
            $repositoryName .= 'Repository';
        }

        // Return repository name.
        return $repositoryName;
    }

    /**
     * Get the populate data.
     *
     * @return array
     */
    protected function getPopulateData()
    {
        $repositoryNamespace = Config::get('repositories.repository_namespace');
        $repositoryClass = $this->getRepositoryName();
        $modelPath = Config::get('repositories.model_path');
        $modelName = $this->getModelName();
        $modelNamespace = Config::get('repositories.model_namespace');

        $populateData = [
            'repository_namespace' => $repositoryNamespace,
            'repository_class' => $repositoryClass,
            'model_path' => $modelPath,
            'model_namespace' => $modelNamespace,
            'model_name' => $modelName,
        ];

        return $populateData;
    }

    /**
     * Creates the class.
     *
     * @return bool|int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createClass()
    {
        $model = explode('\\', Config::get('repositories.model_namespace'))[1] . '\\' . $this->name;

        if (!class_exists($model)) {
            Artisan::call('make:model', ['name' => $model]);
        }

        if ($this->files->exists($this->getPath())) {
            throw new \RuntimeException("The repository with the name '{$this->getName()}' already exists.");
        }

        return $this->files->put($this->getPath(), $this->populateStub());
    }

    /**
     * Get the model name.
     *
     * @return string
     */
    private function getModelName()
    {
        $model = $this->getModel();

        return isset($model) && !empty($model) ? $model : Inflector::singularize($this->stripRepositoryName());
    }

    /**
     * Get the stripped repository name.
     *
     * @return string
     */
    private function stripRepositoryName()
    {
        $stripped = str_ireplace('repository', '', $this->getName());
        $result = ucfirst($stripped);

        return $result;
    }
}

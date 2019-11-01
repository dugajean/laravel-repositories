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
        $repositoryClass = $this->getName();
        $modelPath = Config::get('repositories.model_namespace');
        $modelName = $this->getModelName();

        $populateData = [
            'repository_namespace' => $repositoryNamespace,
            'repository_class' => $repositoryClass,
            'model_path' => $modelPath,
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
        $model = Config::get('repositories.model_namespace') . '\\' . $this->name;

        if (!class_exists($model)) {
            if ($this->command->confirm("Do you want to create a {$model} model?")) {
                Artisan::call('make:model', ['name' => $this->name]);
            } else {
                throw new \RuntimeException("Could not create repository: Model {$model} does not exist.");
            }
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

        return ucfirst($stripped);
    }
}

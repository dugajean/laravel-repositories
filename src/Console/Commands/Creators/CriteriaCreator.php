<?php

namespace Dugajean\Repositories\Console\Commands\Creators;

use Illuminate\Support\Facades\Config;
use Doctrine\Common\Inflector\Inflector;

class CriteriaCreator extends BaseCreator
{
    /**
     * Get the populate data.
     *
     * @return array
     */
    protected function getPopulateData()
    {
        $criteria = $this->getName();
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
     * Create the repository class.
     *
     * @return int
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createClass()
    {
        if ($this->files->exists($this->getPath())) {
            throw new \RuntimeException("The criteria with the name '{$this->getName()}' already exists.");
        }

        return $this->files->put($this->getPath(), $this->populateStub());
    }

    /**
     * Pluralize the model.
     *
     * @return string
     */
    private function pluralizeModel()
    {
        if (null === $this->getModel()) {
            return '';
        }

        $pluralized = Inflector::pluralize($this->getModel());
        $modelName = ucfirst($pluralized);

        return $modelName;
    }
}

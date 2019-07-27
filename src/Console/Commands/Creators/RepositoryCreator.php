<?php

    namespace Dugajean\Repositories\Console\Commands\Creators;

    use Illuminate\Filesystem\Filesystem;
    use Illuminate\Support\Facades\Config;
    use Illuminate\Support\Facades\Artisan;
    use Illuminate\Database\Eloquent\Model;
    use Doctrine\Common\Inflector\Inflector;
    use Dugajean\Repositories\Eloquent\Repository;

    class RepositoryCreator
    {
        /**
         * @var Filesystem
         */
        protected $files;

        /**
         * @var Repository
         */
        protected $repository;

        /**
         * @var Model
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
        public function getRepository()
        {
            return $this->repository;
        }

        /**
         * @param mixed $repository
         */
        public function setRepository($repository)
        {
            $this->repository = $repository;
        }

        /**
         * @return Model
         */
        public function getModel()
        {
            return $this->model;
        }

        /**
         * @param Model $model
         */
        public function setModel($model)
        {
            $this->model = $model;
        }

        /**
         * Create the repository.
         *
         * @param Repository $repository
         * @param Model      $model
         *
         * @return int
         * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
         */
        public function create($repository, $model)
        {
            $this->setRepository($repository);
            $this->setModel($model);
            $this->createDirectory();

            return $this->createClass();
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
         * Get the repository directory.
         *
         * @return mixed
         */
        protected function getDirectory()
        {
            return Config::get('repositories.repository_path');
        }

        /**
         * Get the repository name.
         *
         * @return mixed|string
         */
        protected function getRepositoryName()
        {
            $repositoryName = $this->getRepository();

            if (!strpos($repositoryName, 'Repository') !== false) {
                $repositoryName .= 'Repository';
            }

            // Return repository name.
            return $repositoryName;
        }

        /**
         * Get the model name.
         *
         * @return string
         */
        protected function getModelName()
        {
            $model = $this->getModel();

            if (isset($model) && !empty($model)) {
                $modelName = $model;
            } else {
                $modelName = Inflector::singularize($this->stripRepositoryName());
            }

            return $modelName;
        }

        /**
         * Get the stripped repository name.
         *
         * @return string
         */
        protected function stripRepositoryName()
        {
            $repository = strtolower($this->getRepository());
            $stripped = str_replace("repository", "", $repository);
            $result = ucfirst($stripped);

            return $result;
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
         * Get the path.
         *
         * @return string
         */
        protected function getPath()
        {
            return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getRepositoryName() . '.php';
        }

        /**
         * Get the stub.
         *
         * @return string
         * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
         */
        protected function getStub()
        {
            return $this->files->get($this->getStubPath() . "repository.stub");
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
         * Creates the class.
         *
         * @return bool|int
         * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
         */
        protected function createClass()
        {
            $model = explode('\\', Config::get('repositories.model_namespace'))[1] . '\\' . $this->repository;

            if (!class_exists($model)) {
                Artisan::call('make:model', ['name' => $model]);
            }

            if ($this->files->exists($this->getPath())) {
                throw new \RuntimeException("The repository with the name '$this->repository' already exists.");
            }

            return $this->files->put($this->getPath(), $this->populateStub());
        }
    }

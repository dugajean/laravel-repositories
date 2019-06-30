<?php

namespace Dugajean\Repositories\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Dugajean\Repositories\Console\Commands\Creators\BaseCreator;
use Dugajean\Repositories\Console\Commands\Creators\RepositoryCreator;

abstract class BaseCommand extends Command
{
    /**
     * @var RepositoryCreator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @param BaseCreator $creator
     */
    public function __construct(BaseCreator $creator)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = app()['composer'];
    }

    /**
     * Execute the console command.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->write();
        $this->composer->dumpAutoloads();
    }

    /**
     * Generates the file.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function write()
    {
        try {
            $created = $this->creator->create($this->argument('name'), $this->option('model'), $this);
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
            return;
        }

        if ($created) {
            $this->info("Successfully created the {$this->getCurrentEntity()} class!");
        }
    }

    /**
     * Returns: repository or criteria
     *
     * @return string
     */
    public function getCurrentEntity()
    {
        return explode(':', $this->argument('command'))[1];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name for this entity.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_REQUIRED, 'The model name.', null],
        ];
    }
}

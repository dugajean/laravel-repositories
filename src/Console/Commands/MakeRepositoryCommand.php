<?php

namespace Dugajean\Repositories\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Dugajean\Repositories\Console\Commands\Creators\CreatorInterface;
use Dugajean\Repositories\Console\Commands\Creators\RepositoryCreator;

class MakeRepositoryCommand extends Command
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
    protected $description = 'Create a new repository class';

    /**
     * @var RepositoryCreator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @param RepositoryCreator $creator
     */
    public function __construct(RepositoryCreator $creator)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = app()['composer'];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $arguments = $this->argument();
        $options = $this->option();

        $this->writeRepository($arguments, $options);
        $this->composer->dumpAutoloads();
    }

    /**
     * @param array $arguments
     * @param array $options
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function writeRepository($arguments, $options)
    {
        try {
            if ($this->creator->create($arguments['repository'], $options['model'])) {
                $this->info('Successfully created the repository class');
            }
        } catch (\RuntimeException $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['repository', InputArgument::REQUIRED, 'The repository name.'],
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
            ['model', null, InputOption::VALUE_OPTIONAL, 'The model name.', null],
        ];
    }
}

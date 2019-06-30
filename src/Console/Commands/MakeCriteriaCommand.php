<?php

namespace Dugajean\Repositories\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Dugajean\Repositories\Console\Commands\Creators\CriteriaCreator;
use Dugajean\Repositories\Console\Commands\Creators\CreatorInterface;

class MakeCriteriaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:criteria';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new criteria class';

    /**
     * @var CreatorInterface
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @param CriteriaCreator $creator
     */
    public function __construct(CriteriaCreator $creator)
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
        $this->writeCriteria();
        $this->composer->dumpAutoloads();
    }

    /**
     * Write the criteria.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function writeCriteria()
    {
        try {
            if ($this->creator->create($this->argument('criteria'), $this->option('model'))) {
                $this->info('Successfully created the criteria class');
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
            ['criteria', InputArgument::REQUIRED, 'The criteria name.'],
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

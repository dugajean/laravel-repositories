<?php

namespace Dugajean\Repositories\Console\Commands;

use Dugajean\Repositories\Console\Commands\Creators\RepositoryCreator;

class MakeRepositoryCommand extends BaseCommand
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
     * @param RepositoryCreator $creator
     */
    public function __construct(RepositoryCreator $creator)
    {
        parent::__construct($creator);
    }
}

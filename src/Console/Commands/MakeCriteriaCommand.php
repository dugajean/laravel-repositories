<?php

namespace Dugajean\Repositories\Console\Commands;

use Dugajean\Repositories\Console\Commands\Creators\CriteriaCreator;

class MakeCriteriaCommand extends BaseCommand
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
     * @param CriteriaCreator $creator
     */
    public function __construct(CriteriaCreator $creator)
    {
        parent::__construct($creator);
    }
}

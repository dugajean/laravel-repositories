<?php

namespace Dugajean\Repositories\Criteria;

use Illuminate\Database\Eloquent\Model;
use Dugajean\Repositories\Contracts\RepositoryInterface;

abstract class Criteria
{
    /**
     * @param Model               $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public abstract function apply($model, RepositoryInterface $repository);
}

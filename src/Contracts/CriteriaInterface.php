<?php

namespace Dugajean\Repositories\Contracts;

use Dugajean\Repositories\Criteria\Criteria;

interface CriteriaInterface
{
    /**
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true);

    /**
     * @return Criteria
     */
    public function getCriteria();

    /**
     * @param Criteria $criteria
     *
     * @return $this
     */
    public function getByCriteria(Criteria $criteria);

    /**
     * @param Criteria $criteria
     *
     * @return $this
     */
    public function pushCriteria(Criteria $criteria);

    /**
     * @return $this
     */
    public function applyCriteria();
}

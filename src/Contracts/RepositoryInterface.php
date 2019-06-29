<?php

namespace Dugajean\Repositories\Contracts;

interface RepositoryInterface
{
    /**
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * @param int   $perPage
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($perPage = 1, $columns = ['*']);

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param array $data
     *
     * @return bool
     */
    public function saveModel(array $data);

    /**
     * @param array $data
     * @param int   $id
     *
     * @return mixed
     */
    public function update(array $data, $id);

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function delete($id);

    /**
     * @param int   $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * @param string $field
     * @param mixed  $value
     * @param array  $columns
     *
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*']);

    /**
     * @param string $field
     * @param mixed  $value
     * @param array  $columns
     *
     * @return mixed
     */
    public function findAllBy($field, $value, $columns = ['*']);

    /**
     * @param mixed $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere($where, $columns = ['*']);
}

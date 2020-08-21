<?php

namespace App\Repositories;

/**
 * Class BaseRepository.
 */
class BaseRepository
{
    /**
     * @return mixed
     */
    public function getAll()
    {
        return $this->query()->orderBy('id', 'DESC')->get();
    }

    /**
     * Get Paginated.
     *
     * @param $per_page
     * @param string $active
     * @param string $order_by
     * @param string $sort
     *
     * @return mixed
     */
    public function getPaginated($per_page, $active = '', $order_by = 'id', $sort = 'asc')
    {
        if ($active) {
            return $this->query()->where('status', $active)
                ->orderBy($order_by, $sort)
                ->paginate($per_page);
        } else {
            return $this->query()->orderBy($order_by, $sort)
                ->paginate($per_page);
        }
    }

    /**
     * @return mixed
     */
    public function getCount($soft_delete=true)
    {
        $query = $this->query();
        if ($soft_delete){
            $query->whereNull('deleted_at');
        }

        return $query->count();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id, $soft_delete=true)
    {
        if ($soft_delete){
            return $this->query()
                ->where('id', $id)
                ->whereNull('deleted_at')
                ->first();
        }

        return $this->query()->find($id);
    }

    /**
     * @param $data
     * 
     * @return mixed
     */
    public function create($data){
        return $this->query()->insert($data);
    }

    /**
     * @return mixed
     */
    public function query()
    {
        return call_user_func(static::MODEL.'::query');
    }
}

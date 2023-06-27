<?php

namespace App\Repositories;

use App\Contracts\BaseContract;
use App\Http\Responses\HttpResponse;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseRepository
 */
class BaseRepository implements BaseContract
{
    use HttpResponse;

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attributes): mixed
    {
        return $this->model->create($attributes);
    }

    public function update(array $attributes, string $id): bool
    {
        return $this->findOneOrFail($id)->update($attributes);
    }

    public function updateOrCreate(array $where, array $attributes)
    {
        return $this->model->updateOrCreate($where, $attributes);
    }

    public function all($columns = ['*'], string $orderBy = 'id', string $sortBy = 'asc'): mixed
    {
        return $this->model->orderBy($orderBy, $sortBy)->get($columns);
    }

    public function getBy(array $where, int $quantity = 3): mixed
    {
        return $this->model->where($where)->take($quantity)->get();
    }

    public function find(string $id): mixed
    {
        return $this->model->find($id);
    }

    public function findOneOrFail(string $id): mixed
    {
        return $this->model->findOrFail($id);
    }

    public function findBy(array $data): mixed
    {
        return $this->model->where($data)->get();
    }

    public function findOneBy(array $data): mixed
    {
        return $this->model->where($data)->first();
    }

    public function findOneByOrFail(array $data): mixed
    {
        return $this->model->where($data)->firstOrFail();
    }

    public function delete(string $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function forceDelete(): bool
    {
        return $this->model->forceDelete();
    }

    public function count(): int
    {
        $user = auth()->user();
        $account = $user->account;
        $activeOrganiser = $account->active_organiser;

        return $this->model->query()
            ->when(!$activeOrganiser, function ($query) use ($user) {
                $query->whereIn('organiser_id', $user->organiserIds());
            })
            ->when($activeOrganiser, function ($query) use ($activeOrganiser) {
                $query->where('organiser_id', $activeOrganiser);
            })
            ->count();
    }
}

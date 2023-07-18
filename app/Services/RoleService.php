<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\BaseRepository;

class RoleService extends BaseRepository
{
    public function __construct(Role $model)
    {
        parent::__construct($model);
    }
}

<?php

namespace App\Services;

use App\Models\AccountEventAccess;
use App\Repositories\BaseRepository;

class AccountEventAccessService extends BaseRepository
{
    public function __construct(AccountEventAccess $model)
    {
        parent::__construct($model);
    }
}

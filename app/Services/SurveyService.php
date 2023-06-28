<?php

namespace App\Services;

use App\Models\Survey;
use App\Repositories\BaseRepository;

class SurveyService extends BaseRepository
{
    public function __construct(Survey $model)
    {
        parent::__construct($model);
    }

}

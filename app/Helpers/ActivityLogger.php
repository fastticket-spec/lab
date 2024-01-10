<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

trait ActivityLogger
{
    public function logActivity(string $description, ?Model $performedOn = null, ?array $properties = null): void
    {
        $activity = activity();

        if ($performedOn) {
            $activity->performedOn($performedOn);
        }

        if ($properties) {
            $activity->withProperties($properties);
        }

        $causalName = auth()->user()->fullName;

        $activity->causedBy(auth()->user())->log("$causalName $description");
    }
}

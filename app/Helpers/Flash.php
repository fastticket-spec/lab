<?php

namespace App\Helpers;

class Flash
{
    public static function notify(string $message, string $type = 'success'): void
    {
        request()->session()->flash('alert', [
            'type' => $type,
            'message' => $message,
        ]);
    }
}

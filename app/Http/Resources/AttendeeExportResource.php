<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttendeeExportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'event_title' => $this->event->title,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'reference' => $this->ref,
//            'answers' => $this->answers,
            'downloads' => $this->downloads,
            'date_created' => $this->created_at->format('Y/M/d h:i A'),
            'printed' => $this->printed,
            'collected' => $this->collected
        ];
    }
}

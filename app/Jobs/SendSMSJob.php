<?php

namespace App\Jobs;

use App\Helpers\DreamsSmsHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $phone, public string $message)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DreamsSmsHelper::sendSMS($this->phone, $this->message);
    }
}

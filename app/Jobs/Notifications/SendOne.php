<?php

namespace App\Jobs\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Jobs\Notifications\AsMail;
use App\Jobs\Notifications\AsPing;
use App\Jobs\Notifications\AsSlack;
use App\Jobs\Notifications\AsSms;

use App\Models\TaskNotification;

class SendOne implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $notification;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(TaskNotification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->notification->type == 'mail')
        {
            dispatch(new AsMail($this->notification->toMail()));
        }
        else if ($this->notification->type == 'ping')
        {
            dispatch(new AsPing($this->notification->toPing()));
        }
        else if ($this->notification->type == 'slack')
        {
            dispatch(new AsSlack($this->notification->toSlack()));
        }
        else if ($this->notification->type == 'sms')
        {
            dispatch(new AsSms($this->notification->toSms()));
        }
    }
}

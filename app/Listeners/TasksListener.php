<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Jobs\Notifications\SendMany;

class TasksListener implements ShouldQueue
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Task\Running',
            'App\Listeners\TasksListener@onTaskRunning'
        );
    
        $events->listen(
            'App\Events\Task\Failed',
            'App\Listeners\TasksListener@onTaskFailed'
        );

        $events->listen(
            'App\Events\Task\Interrupted',
            'App\Listeners\TasksListener@onTaskInterrupted'
        );

        $events->listen(
            'App\Events\Task\Completed',
            'App\Listeners\TasksListener@onTaskCompleted'
        );
    }

    /**
     * Handle task running events.
     */ 
    public function onTaskRunning($event)
    {
        dump('onTaskRunning');

        $this->send($event->task->notifications()->running()->get());
    }

    /**
     * Handle task failed events.
     */ 
    public function onTaskFailed($event)
    {
        dump('onTaskFailed');

        $this->send($event->task->notifications()->failed()->get());
    }

    /**
     * Handle task interrupted events.
     */ 
    public function onTaskInterrupted($event)
    {
        dump('onTaskInterrupted');

        $this->send($event->task->notifications()->interrupted()->get());
    }

    /**
     * Handle task completed events.
     */ 
    public function onTaskCompleted($event)
    {
        dump('onTaskCompleted');

        $this->send($event->task->notifications()->completed()->get());
    }

    private function send($notifications)
    {
        if ($notifications->count())
        {
            dispatch(new SendMany($notifications));
        }
    }
}

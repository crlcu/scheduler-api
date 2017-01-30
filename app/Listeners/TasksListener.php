<?php

namespace App\Listeners;

use App\Events\TaskRunning;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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

        // $notifications = $event->task->notifications()->running()->get();

        // foreach ($notifications as $notification)
        // {
        //     $notification->send();
        // }
    }

    /**
     * Handle task failed events.
     */ 
    public function onTaskFailed($event)
    {
        dump('onTaskFailed');

        // $notifications = $event->task->notifications()->failed()->get();

        // foreach ($notifications as $notification)
        // {
        //     $notification->send();
        // }
    }

    /**
     * Handle task interrupted events.
     */ 
    public function onTaskInterrupted($event)
    {
        dump('onTaskInterrupted');

        // $notifications = $task->notifications()->interrupted()->get();

        // foreach ($notifications as $notification)
        // {
        //     $notification->send();
        // }
    }

    /**
     * Handle task completed events.
     */ 
    public function onTaskCompleted($event)
    {
        dump('onTaskCompleted');

        // $notifications = $event->task->notifications()->completed()->get();

        // foreach ($notifications as $notification)
        // {
        //     $notification->send();
        // }
    }
}

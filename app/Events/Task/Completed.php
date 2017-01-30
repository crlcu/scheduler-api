<?php

namespace App\Events\Task;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

use App\Models\Task;

class Completed
{
    use Dispatchable, SerializesModels;

    public $task;

    /**
     * Create a new event instance.
     *
     * @param  Task  $task
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
}

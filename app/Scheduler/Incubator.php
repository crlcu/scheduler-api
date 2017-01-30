<?php

namespace App\Scheduler;

use Carbon\Carbon;
use Cron\CronExpression;
use Symfony\Component\Process\Process;

use App\Events\Task\Running;
use App\Events\Task\Completed;
use App\Events\Task\Failed;

use App\Models\Task;
use App\Models\TaskExecution;

class Incubator
{
    private $task;
    private $execution;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Set the task
     *
     * @param   \App\Models\Task  $task
     * @return  \App\Scheduler\Incubator
     */
    public function set(Task $task)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Run task
     *
     * @return  bool
     */
    public function run()
    {
        // Check if task can run
        if (!$this->canRun())
        {
            return false;
        }

        // Mark as running
        $this->running();

        // Start task execution 
        $ok = $this->task->is_via_ssh ? $this->runViaSSH() : $this->runViaProcess();

        // Mark task as done
        if ($ok)
        {
            // Mark as completed
            $this->completed();
        }
        else
        {
            // Mark as failed
            $this->failed();
        }

        return $ok;
    }

    /**
     * Run task via process
     *
     * @return  bool
     */
    private function runViaProcess()
    {
        $process = new Process($this->task->command);
        $process->setTimeout(3600);

        // Start the process
        $process->start();

        // Store the pid
        $this->pid($process->getPid());

        // Wait for the process to be done
        $process->wait(function ($type, $buffer) {
            $this->output($buffer);
        });

        if (!$process->isSuccessful()) {
            // $this->execution->result .= $process->getErrorOutput();

            return false;
        }

        return true;
    }

    /**
     * Run task via ssh
     *
     * @return  bool
     */
    private function runViaSSH()
    {
        dump("runViaSSH");
    }

    /**
     * Check if task can run
     *
     * @return  bool
     */
    private function canRun()
    {
        $ok = true;

        if (!$this->task->is_concurrent && $this->task->last_run['is_running'])
        {
            $ok = false;
        }

        return $ok;
    }

    /**
     * Mark task as running
     *
     * @return  void
     */
    private function running()
    {
        if (!$this->task->is_one_time_only)
        {
            // Update next due date
            $cron = CronExpression::factory($this->task->cron_expression);
            $this->task->update(['next_due' => $cron->getNextRunDate()->format('Y-m-d H:i:s')]);
        } else if ($this->task->is_one_time_only && $this->task->next_due <= Carbon::now()) {
            // Mark task as disabled
            $this->task->update(['is_enabled' => 0]);
        }

        // Create a new task execution
        $this->execution = TaskExecution::create([
            'task_id'   => $this->task->id,
            'status'    => 'running',
        ]);

        // Emit task running event
        event(new Running($this->task));
    }

    /**
     * Mark task as completed
     *
     * @return  void
     */
    private function completed()
    {
        $this->execution->update(['status' => 'completed']);

        // Emit task completed event
        event(new Completed($this->task));
    }

    /**
     * Mark task as failed
     *
     * @return  void
     */
    private function failed()
    {
        $this->execution->update(['status' => 'failed']);

        // Emit task failed event
        event(new Failed($this->task));
    }

    /**
     * Store the pid of the process
     *
     * @param   int $pid
     * @return  void
     */
    private function pid($pid)
    {
        $this->execution->update(['pid' => $pid]);
    }

    /**
     * Output the content
     *
     * @param   string  $content
     * @return  void
     */
    private function output($content)
    {
        dump($content);

        $this->execution->update(['result' => $this->execution->result . $content]);
    }
}

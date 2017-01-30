<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Venturecraft\Revisionable\RevisionableTrait;

class TaskNotification extends Model
{
    use SoftDeletes, RevisionableTrait;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['task_id', 'type', 'status', 'subject', 'with_result', 'only_result', 'to', 'slack_config_json', 'accept_unsubscribe'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_via_slack', 'slack', 'unsubscribe_id'];


    /**
     * Accessors & Mutators
     */
    public function getSubjectAttribute($value)
    {
        if ($value)
        {
            return $value;
        }
        
        switch ($this->status)
        {
            case 'running':
                return sprintf('The task "%s" has started to run', $this->task->name);

                break;
            case 'failed':
                return sprintf('The execution of task "%s" has failed', $this->task->name);

                break;
            case 'interrupted':
                return sprintf('The execution of task "%s" was interrupted', $this->task->name);

                break;
            case 'completed':
                return sprintf('The execution of task "%s" is now completed', $this->task->name);

                break;
        }

        return "Notification";
    }

    public function getIsViaSlackAttribute($value)
    {
        return $this->type == 'slack';
    }

    public function getSlackAttribute($value)
    {
        return json_decode($this->slack_config_json ? : '[]', true);
    }

    public function getUnsubscribeIdAttribute($value)
    {
        return $this->accept_unsubscribe ? md5(sprintf('%s:%s:%s', $this->id, $this->type, $this->created_at)) : null;
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', '=', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', '=', 'failed');
    }

    public function scopeInterrupted($query)
    {
        return $query->where('status', '=', 'interrupted');
    }

    public function scopeRunning($query)
    {
        return $query->where('status', '=', 'running');
    }

    /**
     * Relations
     */
    public function task()
    {
        return $this->belongsTo('App\Models\Task');
    }


    /**
     * Methods
     */
    public function toMail()
    {
        return [
            'template'  => $this->status == 'running' ? 'emails.tasks.running' : 'emails.tasks.execution',
            'to'        => $this->to,
            'subject'   => $this->subject,
            'data'      => [
                // 'notification'  => $this,
                // 'task'          => $this->task,
            ],
        ];
    }

    public function toPing()
    {
        return [
            'url'       => $this->to,
            'headers' => [
                'User-Agent' => 'Tasks Scheduler/1.0',
            ],
            'form_params' => $this->task->last_run->toArray(),
        ];
    }

    public function toSlack()
    {
        return [
            'config'    => $this->slack,
            'to'        => $this->to,
            'message'   => $this->__message(),
        ];
    }

    public function toSms()
    {
        return [
            'from'  => config('nexmo.from'),
            'to'    => $this->to,
            'text'  => $this->__message(),
        ];
    }

    private function __message()
    {
        $message = '';
        $result = $this->with_result ? sprintf("\n```%s```", $this->task->last_run->result) : '';

        switch ($this->status)
        {
            case 'running':
                $message = sprintf('The task *%s* has started to run.%s', $this->task->name, $result);

                break;
            case 'failed':
                $message = sprintf("The execution of task *%s* has failed.%s", $this->task->name, $result);

                break;
            case 'interrupted':
                $message = sprintf("The execution of task *%s* was interrupted.%s", $this->task->name, $result);

                break;
            case 'completed':
                $message = sprintf("The execution of task *%s* is now completed.%s", $this->task->name, $result);

                break;
        }

        return $message;
    }
}

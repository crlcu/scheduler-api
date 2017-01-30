<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Task;

use Incubator;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $task = Task::find(1);
        $incubator = Incubator::set($task);

        dd($incubator->run());
        dd($task->run());
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Task;
use App\Models\TaskExecution;

class TaskExecutionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $task = Task::forCurrentUser()
            ->findOrFail($id);

        $query = $task->executions();

        if ($search = $request->input('search'))
        {
           // $query = $query->search($search, null, true, 1);
            $query = $query->where('created_at', 'like', '%' . $search . '%');
        }

        $paginator = $query->orderBy('created_at', 'desc')
            ->paginate(min($request->input('limit', 10), 1000));
        
        return response()
            ->json($paginator)
            ->withCallback($request->input('callback'));
    }
}

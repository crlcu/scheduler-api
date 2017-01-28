<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Validation\Rule;

use App\Models\Group;
use App\Models\Role;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Group::with('users');
        
        if ($search = $request->input('search'))
        {
           // $query = $query->search($search, null, true, 1);
            $query = $query->where('name', 'like', '%' . $search . '%');
        }

        $paginator = $query->orderBy('name')
            ->paginate(min($request->input('limit', 10), 1000));

        return response()
            ->json($paginator)
            ->withCallback($request->input('callback'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $roles = Role::all();

        return response()
            ->json([
                'roles' => $roles
            ])
            ->withCallback($request->input('callback'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation rules
        $this->validate($request, [
            'name'  => ['required', 'max:255', Rule::unique('roles')],
        ]);

        // Take only the name from request
        $fields = $request->only('name');

        // Create new group
        $group = new Group($fields);

        // Save the group to database
        $success = $group->save();

        return response()
            ->json([
                'success'   => $success,
                'data'      => $group,
            ])
            ->withCallback($request->input('callback'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        // Find the group in database
        $group = Group::findOrFail($id);

        return response()
            ->json($group)
            ->withCallback($request->input('callback'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Find the group in database
        $group = Group::findOrFail($id);

        // Validation rules
        $this->validate($request, [
            'name'  => ['required', 'max:255', Rule::unique('roles')->ignore($group->id)],
        ]);
        
        // Take only the name from request
        $fields = $request->only('name');

        // Fill the fields
        $group->fill($fields);

        // Update group
        $success = $group->save();

        return response()
            ->json([
                'success'   => $success,
                'data'      => $group,
            ])
            ->withCallback($request->input('callback'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        //
    }
}

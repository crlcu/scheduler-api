<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Validation\Rule;

use App\Models\Role;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Role::with('groups');
        
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
        //
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
            'name'          => ['required', 'max:255', Rule::unique('roles')],
            'description'   => 'required',
        ]);

        // Take only name and description from request
        $fields = $request->only('name', 'description');

        // Create new role
        $role = new Role($fields);

        // Save the role to database
        $success = $role->save();

        return response()
            ->json([
                'success'   => $success,
                'data'      => $role,
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
        // Find the role in database
        $role = Role::findOrFail($id);

        return response()
            ->json($role)
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
        // Find the role in database
        $role = Role::findOrFail($id);

        // Validation rules
        $this->validate($request, [
            'name'          => ['required', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description'   => 'required',
        ]);
        
        // Take only name and description from request
        $fields = $request->only('name', 'description');

        // Fill the fields
        $role->fill($fields);

        // Update role
        $success = $role->save();

        return response()
            ->json([
                'success'   => $success,
                'data'      => $role,
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
        // Find the role in database
        $role = Role::findOrFail($id);

        // Delete the role
        $success = $role->delete();

        return response()
            ->json(['success' => $success])
            ->withCallback($request->input('callback'));
    }
}

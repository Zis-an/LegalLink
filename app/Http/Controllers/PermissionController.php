<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the permissions.
     */
    public function index(): View
    {
        $permissions = Permission::orderByDesc('id')->get();
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission.
     */
    public function create(): View
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created permission in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|unique:permissions|min:3',
            'guard_name' => 'required|min:3',
        ]);

        Permission::create($request->only(['name', 'guard_name']));
        return redirect()->route('permissions.index');
    }

    /**
     * Show the form for editing the specified permission.
     */
    public function edit(int $id): View
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|min:3|unique:permissions,name,' . $id,
            'guard_name' => 'required|min:3',
        ]);

        $permission = Permission::findOrFail($id);
        $permission->update($request->only(['name', 'guard_name']));

        return redirect()->route('permissions.index');
    }

    /**
     * Remove the specified permission from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        Permission::findOrFail($id)->delete();
        return redirect()->route('permissions.index');
    }

    /**
     * Display the specified permission.
     */
    public function show(int $id): View
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.show', compact('permission'));
    }
}

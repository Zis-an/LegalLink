<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::orderByDesc('id')->get();
        return view('permissions.index', compact('permissions'));
    }

    public function create(): View
    {
        return view('permissions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'       => 'required|unique:permissions|min:3',
            'guard_name' => 'required|min:3',
        ]);

        Permission::create($request->only(['name', 'guard_name']));
        return redirect()->route('permissions.index');
    }

    public function edit(int $id): View
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', compact('permission'));
    }

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

    public function destroy(int $id): RedirectResponse
    {
        Permission::findOrFail($id)->delete();
        return redirect()->route('permissions.index');
    }

    public function show(int $id): View
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.show', compact('permission'));
    }
}

<?php

namespace App\Http\Controllers\PermissionGui;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $allowedSorts = ['name', 'permissions_count', 'users_count'];
        $sort = in_array($request->sort, $allowedSorts) ? $request->sort : 'name';
        $dir  = $request->dir === 'desc' ? 'desc' : 'asc';

        $roles = Role::withCount('permissions', 'users')
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString();

        return response()->json([
            'roles'   => $roles,
            'filters' => ['sort' => $sort, 'dir' => $dir],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);

        return response()->json(['message' => "Role \"{$data['name']}\" created.", 'role' => $role], 201);
    }

    public function show(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($p) {
            return str_contains($p->name, '.') ? explode('.', $p->name)[0] : 'general';
        });

        return response()->json([
            'role'               => $role->load('permissions'),
            'groupedPermissions' => $permissions,
            'rolePermissionIds'  => $role->permissions->pluck('id'),
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'          => "sometimes|required|string|max:255|unique:roles,name,{$role->id}",
            'permissions'   => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if (isset($data['name'])) {
            $role->update(['name' => $data['name']]);
        }

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return response()->json(['message' => "Role \"{$role->name}\" updated.", 'role' => $role->fresh('permissions')]);
    }

    public function destroy(Role $role)
    {
        $name = $role->name;
        $role->delete();

        return response()->json(['message' => "Role \"{$name}\" deleted."]);
    }
}

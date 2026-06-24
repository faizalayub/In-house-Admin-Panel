<?php

namespace App\Http\Controllers\PermissionGui;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $allowedSorts = ['name', 'email', 'created_at'];
        $sort   = in_array($request->sort, $allowedSorts) ? $request->sort : 'name';
        $dir    = $request->dir === 'desc' ? 'desc' : 'asc';
        $search = trim($request->search ?? '');

        $users = User::with('roles', 'permissions')
            ->when($search, fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            ))
            ->orderBy($sort, $dir)
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($user) => [
                'id'                       => $user->id,
                'name'                     => $user->name,
                'email'                    => $user->email,
                'roles'                    => $user->roles->pluck('name'),
                'direct_permissions_count' => $user->permissions->count(),
                'all_permissions_count'    => $user->getAllPermissions()->count(),
                'joined'                   => $user->created_at->diffForHumans(),
            ]);

        return response()->json([
            'users'   => $users,
            'filters' => ['sort' => $sort, 'dir' => $dir, 'search' => $search],
        ]);
    }

    public function show(User $user)
    {
        $permissions = Permission::all()->groupBy(function ($p) {
            return str_contains($p->name, '.') ? explode('.', $p->name)[0] : 'general';
        });

        return response()->json([
            'user'                    => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'allRoles'                => Role::all(),
            'groupedPermissions'      => $permissions,
            'userRoleIds'             => $user->roles->pluck('id'),
            'userDirectPermissionIds' => $user->permissions->pluck('id'),
            'effectivePermissions'    => $user->getAllPermissions()->pluck('name')->sort()->values(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'roles'         => 'sometimes|array',
            'roles.*'       => 'integer|exists:roles,id',
            'permissions'   => 'sometimes|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        if (array_key_exists('roles', $data)) {
            $user->syncRoles($data['roles']);
        }

        if (array_key_exists('permissions', $data)) {
            $user->syncPermissions($data['permissions']);
        }

        return response()->json(['message' => "User \"{$user->name}\" updated."]);
    }
}

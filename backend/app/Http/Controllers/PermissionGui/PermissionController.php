<?php

namespace App\Http\Controllers\PermissionGui;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $search         = trim($request->search ?? '');
        $onlyUnassigned = (bool) $request->unassigned;
        $perPage        = 15;
        $page           = max(1, (int) ($request->page ?? 1));

        $directIds = DB::table('model_has_permissions')
            ->where('model_type', User::class)
            ->pluck('permission_id')->unique();

        $roleIds = DB::table('role_has_permissions')
            ->join('model_has_roles', 'role_has_permissions.role_id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', User::class)
            ->pluck('role_has_permissions.permission_id')->unique();

        $assignedIds = $directIds->merge($roleIds)->unique()->values()->all();

        $permissions = Permission::orderBy('name')
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->get();

        $grouped = $permissions
            ->groupBy(fn ($p) => str_contains($p->name, '.') ? explode('.', $p->name)[0] : 'general')
            ->sortKeys();

        if ($onlyUnassigned) {
            $grouped = $grouped->filter(
                fn ($perms) => $perms->some(fn ($p) => ! in_array($p->id, $assignedIds))
            );
        }

        $featureKeys = $grouped->keys()->values();

        $paginator = new LengthAwarePaginator(
            $featureKeys->slice(($page - 1) * $perPage, $perPage)->values(),
            $featureKeys->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $paginatedGrouped = collect($paginator->items())->mapWithKeys(
            fn ($feature) => [$feature => $grouped[$feature]]
        );

        return response()->json([
            'groupedPermissions'      => $paginatedGrouped,
            'featurePaginator'        => $paginator->withQueryString(),
            'filteredPermissionCount' => $permissions->count(),
            'totalPermissions'        => Permission::count(),
            'assignedPermissionIds'   => $assignedIds,
            'totalUnassigned'         => Permission::whereNotIn('id', $assignedIds)->count(),
            'filters'                 => ['search' => $search, 'unassigned' => $onlyUnassigned],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'feature' => 'nullable|string|max:100|regex:/^[a-z0-9_-]+$/',
            'action'  => 'required|string|max:100|regex:/^[a-z0-9_.-]+$/',
        ]);

        $name = $data['feature']
            ? "{$data['feature']}.{$data['action']}"
            : $data['action'];

        if (Permission::where('name', $name)->exists()) {
            return response()->json(['errors' => ['action' => "Permission \"{$name}\" already exists."]], 422);
        }

        $permission = Permission::create(['name' => $name, 'guard_name' => 'web']);

        return response()->json(['message' => "Permission \"{$name}\" created.", 'permission' => $permission], 201);
    }

    public function destroy(Permission $permission)
    {
        $name = $permission->name;
        $permission->delete();

        return response()->json(['message' => "Permission \"{$name}\" deleted."]);
    }

    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'feature'   => 'required|string|max:100|regex:/^[a-z0-9_-]+$/',
            'actions'   => 'required|array|min:1',
            'actions.*' => 'string|in:view,create,edit,delete,export,import,approve,publish',
        ]);

        $created = [];
        foreach ($data['actions'] as $action) {
            $name = "{$data['feature']}.{$action}";
            if (! Permission::where('name', $name)->exists()) {
                Permission::create(['name' => $name, 'guard_name' => 'web']);
                $created[] = $name;
            }
        }

        return response()->json([
            'message' => count($created) . " permission(s) created for feature \"{$data['feature']}\".",
            'created' => $created,
        ], 201);
    }
}

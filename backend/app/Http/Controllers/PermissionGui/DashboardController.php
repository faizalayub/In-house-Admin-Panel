<?php

namespace App\Http\Controllers\PermissionGui;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'overview');

        $totalUsers       = User::count();
        $totalRoles       = Role::count();
        $totalPermissions = Permission::count();
        $unassignedUsers  = User::whereDoesntHave('roles')->whereDoesntHave('permissions')->count();
        $emptyRoles       = Role::doesntHave('permissions')->count();
        $orphanedPerms    = Permission::doesntHave('roles')->count();
        $coverage         = $totalUsers > 0
            ? round((($totalUsers - User::whereDoesntHave('roles')->count()) / $totalUsers) * 100)
            : 0;
        $featureCount = Permission::all()
            ->filter(fn ($p) => str_contains($p->name, '.'))
            ->map(fn ($p) => explode('.', $p->name)[0])
            ->unique()->count();

        $stats = [
            'total_users'          => $totalUsers,
            'total_roles'          => $totalRoles,
            'total_permissions'    => $totalPermissions,
            'unassigned_users'     => $unassignedUsers,
            'empty_roles'          => $emptyRoles,
            'orphaned_permissions' => $orphanedPerms,
            'coverage'             => $coverage,
            'feature_count'        => $featureCount,
        ];

        $overviewRoles = Role::withCount('permissions', 'users')
            ->orderByDesc('users_count')->limit(10)->get();

        $allowedRoleSorts = ['name', 'permissions_count', 'users_count'];
        $roleSort = in_array($request->role_sort, $allowedRoleSorts) ? $request->role_sort : 'name';
        $roleDir  = $request->role_dir === 'desc' ? 'desc' : 'asc';
        $roles    = Role::withCount('permissions', 'users')
            ->orderBy($roleSort, $roleDir)
            ->paginate(15, ['*'], 'role_page')
            ->withQueryString();

        $permSearch     = trim($request->perm_search ?? '');
        $onlyUnassigned = (bool) $request->perm_unassigned;
        $permPerPage    = 15;
        $permPage       = max(1, (int) ($request->perm_page ?? 1));

        $directIds   = DB::table('model_has_permissions')
            ->where('model_type', User::class)
            ->pluck('permission_id')->unique();
        $rolePermIds = DB::table('role_has_permissions')
            ->join('model_has_roles', 'role_has_permissions.role_id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', User::class)
            ->pluck('role_has_permissions.permission_id')->unique();
        $assignedIds = $directIds->merge($rolePermIds)->unique()->values()->all();

        $allPerms = Permission::orderBy('name')
            ->when($permSearch, fn ($q) => $q->where('name', 'like', "%{$permSearch}%"))
            ->get();

        $grouped = $allPerms
            ->groupBy(fn ($p) => str_contains($p->name, '.') ? explode('.', $p->name)[0] : 'general')
            ->sortKeys();

        if ($onlyUnassigned) {
            $grouped = $grouped->filter(
                fn ($perms) => $perms->some(fn ($p) => ! in_array($p->id, $assignedIds))
            );
        }

        $featureKeys   = $grouped->keys()->values();
        $permPaginator = new LengthAwarePaginator(
            $featureKeys->slice(($permPage - 1) * $permPerPage, $permPerPage)->values(),
            $featureKeys->count(),
            $permPerPage,
            $permPage,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'perm_page']
        );

        $paginatedGrouped = collect($permPaginator->items())->mapWithKeys(
            fn ($feature) => [$feature => $grouped[$feature]]
        );

        $allowedUserSorts = ['name', 'email', 'created_at'];
        $userSort   = in_array($request->user_sort, $allowedUserSorts) ? $request->user_sort : 'name';
        $userDir    = $request->user_dir === 'desc' ? 'desc' : 'asc';
        $userSearch = trim($request->user_search ?? '');

        $users = User::with('roles', 'permissions')
            ->when($userSearch, fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$userSearch}%")
                ->orWhere('email', 'like', "%{$userSearch}%")
            ))
            ->orderBy($userSort, $userDir)
            ->paginate(15, ['*'], 'user_page')
            ->withQueryString()
            ->through(fn ($u) => [
                'id'                       => $u->id,
                'name'                     => $u->name,
                'email'                    => $u->email,
                'roles'                    => $u->roles->pluck('name'),
                'direct_permissions_count' => $u->permissions->count(),
                'all_permissions_count'    => $u->getAllPermissions()->count(),
                'joined'                   => $u->created_at->diffForHumans(),
            ]);

        $roleDetail = null;
        if ($request->filled('role_id')) {
            $role = Role::find((int) $request->role_id);
            if ($role) {
                $rPerms = Permission::orderBy('name')->get()->groupBy(
                    fn ($p) => str_contains($p->name, '.') ? explode('.', $p->name)[0] : 'general'
                );
                $roleDetail = [
                    'role'               => $role->load('permissions'),
                    'groupedPermissions' => $rPerms,
                    'rolePermissionIds'  => $role->permissions->pluck('id')->values(),
                ];
            }
        }

        $userDetail = null;
        if ($request->filled('user_id')) {
            $u = User::find((int) $request->user_id);
            if ($u) {
                $uPerms = Permission::orderBy('name')->get()->groupBy(
                    fn ($p) => str_contains($p->name, '.') ? explode('.', $p->name)[0] : 'general'
                );
                $userDetail = [
                    'user'                    => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email],
                    'allRoles'                => Role::all(),
                    'groupedPermissions'      => $uPerms,
                    'userRoleIds'             => $u->roles->pluck('id')->values(),
                    'userDirectPermissionIds' => $u->permissions->pluck('id')->values(),
                    'effectivePermissions'    => $u->getAllPermissions()->pluck('name')->sort()->values(),
                ];
            }
        }

        return response()->json([
            'tab'                     => $tab,
            'stats'                   => $stats,
            'overviewRoles'           => $overviewRoles,
            'roles'                   => $roles,
            'roleFilters'             => ['sort' => $roleSort, 'dir' => $roleDir],
            'groupedPermissions'      => $paginatedGrouped,
            'featurePaginator'        => $permPaginator->withQueryString(),
            'filteredPermissionCount' => $allPerms->count(),
            'totalPermissions'        => $totalPermissions,
            'assignedPermissionIds'   => $assignedIds,
            'totalUnassigned'         => Permission::whereNotIn('id', $assignedIds)->count(),
            'permFilters'             => ['search' => $permSearch, 'unassigned' => $onlyUnassigned],
            'users'                   => $users,
            'userFilters'             => ['sort' => $userSort, 'dir' => $userDir, 'search' => $userSearch],
            'roleDetail'              => $roleDetail,
            'userDetail'              => $userDetail,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $features = [
            'users' => ['view', 'create', 'edit', 'delete'],
            'posts' => ['view', 'create', 'edit', 'delete', 'publish'],
            'reports' => ['view', 'export'],
            'settings' => ['view', 'edit'],
        ];

        foreach ($features as $feature => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$feature}.{$action}", 'guard_name' => 'web']);
            }
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);

        $admin->syncPermissions(Permission::all());
        $editor->syncPermissions(Permission::whereIn('name', [
            'posts.view', 'posts.create', 'posts.edit', 'users.view',
        ])->get());
        $viewer->syncPermissions(Permission::whereIn('name', [
            'posts.view', 'users.view', 'reports.view',
        ])->get());

        User::factory()->create(['name' => 'Alice Admin', 'email' => 'alice@example.com'])->assignRole('admin');
        User::factory()->create(['name' => 'Bob Editor', 'email' => 'bob@example.com'])->assignRole('editor');
        User::factory()->create(['name' => 'Carol Viewer', 'email' => 'carol@example.com'])->assignRole('viewer');
    }
}

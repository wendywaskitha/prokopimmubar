<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat role-role yang diperlukan jika belum ada
        $roles = [
            'super_admin',
            'editor',
            'penulis',
            'kontributor'
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Berikan semua permissions ke role super_admin
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $permissions = Permission::all();
            $superAdmin->syncPermissions($permissions);
        }

        // Berikan permissions untuk editor (hampir semua kecuali manajemen user)
        $editor = Role::where('name', 'editor')->first();
        if ($editor) {
            $editorPermissions = Permission::whereNotIn('name', [
                'view_user', 'view_any_user', 'create_user', 'update_user', 
                'delete_user', 'delete_any_user', 'force_delete_user', 
                'force_delete_any_user', 'restore_user', 'restore_any_user',
                'replicate_user', 'reorder_user',
                'view_role', 'view_any_role', 'create_role', 'update_role',
                'delete_role', 'delete_any_role'
            ])->get();
            $editor->syncPermissions($editorPermissions);
        }

        // Berikan permissions untuk penulis (hanya berita milik sendiri)
        $penulis = Role::where('name', 'penulis')->first();
        if ($penulis) {
            $penulisPermissions = Permission::whereIn('name', [
                'view_news', 'view_any_news', 'create_news', 'update_news',
                'view_category', 'view_any_category',
                'view_tag', 'view_any_tag',
                'view_gallery', 'view_any_gallery', 'create_gallery', 'update_gallery',
                'widget_StatsOverview', 'widget_NewsStatsWidget', 
                'widget_NewsStatusChart', 'widget_NewsByCategoryChart',
                'page_Profile'
            ])->get();
            $penulis->syncPermissions($penulisPermissions);
        }

        // Berikan permissions untuk kontributor (hanya submit berita draft)
        $kontributor = Role::where('name', 'kontributor')->first();
        if ($kontributor) {
            $kontributorPermissions = Permission::whereIn('name', [
                'create_news', 'view_news',
                'view_category', 'view_any_category',
                'view_tag', 'view_any_tag',
                'page_Profile'
            ])->get();
            $kontributor->syncPermissions($kontributorPermissions);
        }
    }
}

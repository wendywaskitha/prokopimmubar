<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure roles exist
        $roles = ['super_admin', 'editor', 'penulis', 'kontributor'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Membuat super admin
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'phone' => '081234567890',
            'position' => 'Administrator',
            'bio' => 'Administrator sistem Warta Daerah Kabupaten Muna Barat',
        ]);
        $superAdmin->assignRole('super_admin');

        // Membuat editor
        $editor = User::factory()->create([
            'name' => 'Editor Berita',
            'email' => 'editor@example.com',
            'phone' => '081234567891',
            'position' => 'Editor',
            'bio' => 'Editor berita Warta Daerah Kabupaten Muna Barat',
        ]);
        $editor->assignRole('editor');

        // Membuat penulis
        $penulis = User::factory()->create([
            'name' => 'Penulis Berita',
            'email' => 'penulis@example.com',
            'phone' => '081234567892',
            'position' => 'Penulis',
            'bio' => 'Penulis berita Warta Daerah Kabupaten Muna Barat',
        ]);
        $penulis->assignRole('penulis');

        // Membuat kontributor
        $kontributor = User::factory()->create([
            'name' => 'Kontributor Berita',
            'email' => 'kontributor@example.com',
            'phone' => '081234567893',
            'position' => 'Kontributor',
            'bio' => 'Kontributor berita Warta Daerah Kabupaten Muna Barat',
        ]);
        $kontributor->assignRole('kontributor');

        // Membuat 10 user acak dengan role kontributor
        $users = User::factory(10)->create();
        foreach ($users as $user) {
            $user->assignRole('kontributor');
        }
    }
}

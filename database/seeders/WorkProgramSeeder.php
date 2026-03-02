<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkProgram;
use App\Models\User;

class WorkProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Memastikan user sudah ada
        if (User::count() == 0) {
            $this->call(UserSeeder::class);
        }
        
        // Membuat 20 program kerja dengan berbagai status
        $workPrograms = [
            [
                'title' => 'Pembangunan Jalan Desa',
                'description' => 'Pembangunan dan perbaikan jalan desa di seluruh kabupaten',
                'category' => 'infrastruktur',
                'status' => 'ongoing',
                'priority' => 'high',
                'start_date' => now()->subMonths(6),
                'end_date' => now()->addMonths(6),
                'author_id' => User::first()->id,
            ],
            [
                'title' => 'Peningkatan Kualitas Pendidikan',
                'description' => 'Pembangunan fasilitas pendidikan dan peningkatan kualitas guru',
                'category' => 'pendidikan',
                'status' => 'ongoing',
                'priority' => 'high',
                'start_date' => now()->subMonths(3),
                'end_date' => now()->addMonths(9),
                'author_id' => User::first()->id,
            ],
            [
                'title' => 'Pembangunan Puskesmas',
                'description' => 'Pembangunan puskesmas baru untuk meningkatkan pelayanan kesehatan',
                'category' => 'kesehatan',
                'status' => 'ongoing',
                'priority' => 'medium',
                'start_date' => now()->subMonths(2),
                'end_date' => now()->addMonths(10),
                'author_id' => User::first()->id,
            ],
            [
                'title' => 'Pengadaan Air Bersih',
                'description' => 'Pengadaan air bersih untuk masyarakat di daerah terpencil',
                'category' => 'sosial',
                'status' => 'ongoing',
                'priority' => 'medium',
                'start_date' => now()->subMonths(1),
                'end_date' => now()->addMonths(11),
                'author_id' => User::first()->id,
            ],
            [
                'title' => 'Pembangunan Pasar Tradisional',
                'description' => 'Pembangunan pasar tradisional untuk meningkatkan ekonomi masyarakat',
                'category' => 'ekonomi',
                'status' => 'ongoing',
                'priority' => 'medium',
                'start_date' => now(),
                'end_date' => now()->addYear(),
                'author_id' => User::first()->id,
            ],
        ];
        
        foreach ($workPrograms as $program) {
            WorkProgram::create($program);
        }
        
        // Membuat 15 program kerja tambahan dengan factory
        WorkProgram::factory(15)->create();
    }
}

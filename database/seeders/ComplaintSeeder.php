<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Complaint;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 30 aduan masyarakat dengan berbagai status
        Complaint::factory()->baru()->count(10)->create();
        Complaint::factory()->diproses()->count(8)->create();
        Complaint::factory()->selesai()->count(7)->create();
        Complaint::factory()->ditolak()->count(5)->create();
    }
}

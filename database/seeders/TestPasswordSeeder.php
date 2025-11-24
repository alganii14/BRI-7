<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeder ini untuk testing fitur notifikasi password
     * 
     * @return void
     */
    public function run()
    {
        // Update existing users untuk testing
        // Set password_changed_at ke null untuk user dengan role manager dan rmft
        User::whereIn('role', ['manager', 'rmft'])
            ->update(['password_changed_at' => null]);
        
        echo "✓ Password status reset untuk testing notifikasi\n";
        echo "  Manager dan RMFT sekarang akan menerima notifikasi untuk mengubah password\n";
    }
}

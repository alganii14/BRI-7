<?php

/**
 * Script untuk testing fitur password visibility dan pembatasan akses
 * 
 * Cara menjalankan:
 * php test-password-feature.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Testing Fitur Password Visibility dan Pembatasan Akses ===\n\n";

// Test 1: Cek user manager dan rmft
echo "1. Mencari user dengan role manager dan rmft...\n";
$managers = User::where('role', 'manager')->get();
$rmfts = User::where('role', 'rmft')->get();

echo "   - Manager: " . $managers->count() . " user\n";
echo "   - RMFT: " . $rmfts->count() . " user\n\n";

// Test 2: Cek kolom password_changed_at
echo "2. Memeriksa kolom password_changed_at...\n";
$sampleUser = User::first();
if ($sampleUser) {
    $hasColumn = array_key_exists('password_changed_at', $sampleUser->getAttributes());
    echo "   - Kolom password_changed_at: " . ($hasColumn ? "✓ Ada" : "✗ Tidak ada") . "\n";
    
    if ($hasColumn) {
        $usersNeedChange = User::whereIn('role', ['manager', 'rmft'])
            ->whereNull('password_changed_at')
            ->get();
        echo "   - User yang perlu ganti password: " . $usersNeedChange->count() . " user\n";
        
        if ($usersNeedChange->count() > 0) {
            echo "\n   Daftar user yang perlu ganti password:\n";
            foreach ($usersNeedChange as $user) {
                echo "   - {$user->name} ({$user->email}) - Role: {$user->role}\n";
            }
        }
    }
} else {
    echo "   - Tidak ada user di database\n";
}

echo "\n3. Testing method needsPasswordChange()...\n";
if ($managers->count() > 0) {
    $testManager = $managers->first();
    echo "   - Testing dengan user: {$testManager->name}\n";
    echo "   - password_changed_at: " . ($testManager->password_changed_at ?? 'NULL') . "\n";
    echo "   - needsPasswordChange(): " . ($testManager->needsPasswordChange() ? 'true' : 'false') . "\n";
}

if ($rmfts->count() > 0) {
    $testRmft = $rmfts->first();
    echo "   - Testing dengan user: {$testRmft->name}\n";
    echo "   - password_changed_at: " . ($testRmft->password_changed_at ?? 'NULL') . "\n";
    echo "   - needsPasswordChange(): " . ($testRmft->needsPasswordChange() ? 'true' : 'false') . "\n";
}

// Test 4: Simulasi reset password_changed_at untuk testing
echo "\n4. Opsi untuk reset password_changed_at (untuk testing)...\n";
echo "   Untuk reset semua manager dan rmft, jalankan query SQL:\n";
echo "   UPDATE users SET password_changed_at = NULL WHERE role IN ('manager', 'rmft');\n";

echo "\n=== Testing Selesai ===\n";
echo "\nLangkah selanjutnya:\n";
echo "1. Jalankan migration: php artisan migrate\n";
echo "2. Test di browser:\n";
echo "   - Login page: Cek toggle password visibility\n";
echo "   - Profile page: Cek toggle password visibility di 3 field\n";
echo "   - Login sebagai manager/rmft yang password_changed_at = NULL\n";
echo "   - Coba akses dashboard (harus redirect ke profil)\n";
echo "   - Ganti password di profil\n";
echo "   - Coba akses dashboard lagi (harus berhasil)\n";

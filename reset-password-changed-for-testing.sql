-- Script SQL untuk reset password_changed_at untuk testing
-- Gunakan script ini untuk mensimulasikan user yang belum ganti password

-- 1. Reset semua manager dan rmft (untuk testing pembatasan akses)
UPDATE users 
SET password_changed_at = NULL 
WHERE role IN ('manager', 'rmft');

-- 2. Cek user yang perlu ganti password
SELECT 
    id,
    name,
    email,
    role,
    password_changed_at,
    CASE 
        WHEN password_changed_at IS NULL THEN 'Perlu Ganti Password'
        ELSE 'Sudah Ganti Password'
    END as status
FROM users 
WHERE role IN ('manager', 'rmft')
ORDER BY role, name;

-- 3. Reset hanya untuk user tertentu (ganti ID sesuai kebutuhan)
-- UPDATE users SET password_changed_at = NULL WHERE id = 1;

-- 4. Set password_changed_at untuk user tertentu (simulasi sudah ganti password)
-- UPDATE users SET password_changed_at = NOW() WHERE id = 1;

-- 5. Cek statistik
SELECT 
    role,
    COUNT(*) as total_users,
    SUM(CASE WHEN password_changed_at IS NULL THEN 1 ELSE 0 END) as belum_ganti,
    SUM(CASE WHEN password_changed_at IS NOT NULL THEN 1 ELSE 0 END) as sudah_ganti
FROM users 
WHERE role IN ('manager', 'rmft')
GROUP BY role;

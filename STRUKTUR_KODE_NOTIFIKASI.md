# Struktur Kode Fitur Notifikasi Password

## Overview
Fitur ini terdiri dari beberapa komponen yang bekerja sama untuk menampilkan notifikasi dan tracking status password.

## Komponen Utama

### 1. Database Layer

#### Migration
**File:** `database/migrations/2025_11_24_104201_add_password_changed_at_to_users_table.php`

Menambahkan kolom `password_changed_at` (timestamp, nullable) ke tabel `users`.

```php
Schema::table('users', function (Blueprint $table) {
    $table->timestamp('password_changed_at')->nullable()->after('password');
});
```

### 2. Model Layer

#### User Model
**File:** `app/Models/User.php`

**Perubahan:**
1. Menambahkan `password_changed_at` ke `$fillable`
2. Menambahkan cast untuk `password_changed_at` sebagai datetime
3. Menambahkan method `needsPasswordChange()`

**Method `needsPasswordChange()`:**
```php
public function needsPasswordChange()
{
    // Hanya untuk manager dan rmft
    if (!in_array($this->role, ['manager', 'rmft'])) {
        return false;
    }
    
    // Jika password_changed_at null, berarti belum pernah ganti password
    return is_null($this->password_changed_at);
}
```

**Logic:**
- Return `false` jika role bukan manager atau rmft
- Return `true` jika `password_changed_at` adalah null
- Return `false` jika `password_changed_at` sudah terisi

### 3. Controller Layer

#### NotificationController (Baru)
**File:** `app/Http/Controllers/NotificationController.php`

**Methods:**

1. **`getUnreadCount()`**
   - Endpoint: `GET /api/notifications/count`
   - Return: `{ "count": 0 atau 1 }`
   - Logic: Cek apakah user perlu ubah password

2. **`getNotifications()`**
   - Endpoint: `GET /api/notifications`
   - Return: Array notifikasi
   - Logic: Jika perlu ubah password, return notifikasi dengan detail

#### ProfileController (Update)
**File:** `app/Http/Controllers/ProfileController.php`

**Method `updatePassword()` - Perubahan:**
```php
$user->update([
    'password' => Hash::make($validated['new_password']),
    'password_changed_at' => now()  // ← Tambahan ini
]);
```

**Logic:** Set `password_changed_at` ke waktu sekarang saat user mengubah password.

#### AkunController (Update)
**File:** `app/Http/Controllers/AkunController.php`

**Method `store()` - Perubahan:**
```php
$validated['password'] = Hash::make($validated['password']);
$validated['password_changed_at'] = null;  // ← Tambahan ini
```

**Method `update()` - Perubahan:**
```php
if (!empty($validated['password'])) {
    $validated['password'] = Hash::make($validated['password']);
    $validated['password_changed_at'] = null;  // ← Tambahan ini
}
```

**Logic:** 
- Saat admin membuat akun baru, set `password_changed_at` = null
- Saat admin reset password, set `password_changed_at` = null

### 4. Route Layer

#### Web Routes
**File:** `routes/web.php`

**Tambahan Routes:**
```php
// Notification Routes - All authenticated users
Route::get('api/notifications/count', [NotificationController::class, 'getUnreadCount'])
    ->name('api.notifications.count');
Route::get('api/notifications', [NotificationController::class, 'getNotifications'])
    ->name('api.notifications');
```

**Middleware:** `auth` (hanya user yang sudah login)

### 5. View Layer

#### Layout App (Update)
**File:** `resources/views/layouts/app.blade.php`

**Komponen yang Ditambahkan:**

1. **HTML Structure:**
```html
<div class="notification-container">
    <button class="notification-bell" id="notificationBell">
        <svg>...</svg>
        <span class="notification-badge" id="notificationBadge">0</span>
    </button>
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h3>Notifikasi</h3>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="notification-empty">Tidak ada notifikasi</div>
        </div>
    </div>
</div>
```

2. **CSS Styles:**
- `.notification-container` - Container utama
- `.notification-bell` - Button bell icon
- `.notification-badge` - Badge merah dengan angka
- `.notification-dropdown` - Dropdown menu
- `.notification-item` - Item notifikasi individual
- Responsive styles untuk mobile

3. **JavaScript Functions:**

**`loadNotifications()`:**
```javascript
function loadNotifications() {
    // Load notification count
    fetch('/api/notifications/count')
        .then(response => response.json())
        .then(data => {
            // Update badge
        });

    // Load notifications
    fetch('/api/notifications')
        .then(response => response.json())
        .then(data => {
            // Render notifications
        });
}
```

**`toggleNotifications()`:**
```javascript
function toggleNotifications() {
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('show');
}
```

**Auto Refresh:**
```javascript
setInterval(loadNotifications, 300000); // 5 menit
```

**Event Listeners:**
- Click outside to close dropdown
- DOMContentLoaded to load initial notifications

#### Akun Index (Update)
**File:** `resources/views/akun/index.blade.php`

**Perubahan:**

1. **Tambah Kolom di Tabel Manager:**
```html
<th>STATUS PASSWORD</th>
```

```html
<td>
    @if($manager->password_changed_at)
        <span class="badge badge-success">✓ Sudah Diubah</span>
        <br><small>{{ $manager->password_changed_at->format('d/m/Y H:i') }}</small>
    @else
        <span class="badge badge-warning">⚠ Password Default</span>
    @endif
</td>
```

2. **Tambah Kolom di Tabel RMFT:**
```html
<th>STATUS PASSWORD</th>
```

```html
<td>
    @if($rmft->password_changed_at)
        <span class="badge badge-success">✓ Sudah Diubah</span>
        <br><small>{{ $rmft->password_changed_at->format('d/m/Y H:i') }}</small>
    @else
        <span class="badge badge-warning">⚠ Password Default</span>
    @endif
</td>
```

3. **Tambah CSS untuk Badge:**
```css
.badge-success {
    background-color: #4caf50;
    color: white;
}

.badge-warning {
    background-color: #ff9800;
    color: white;
}
```

## Flow Diagram

### Flow 1: User Login dengan Password Default
```
User Login (Manager/RMFT)
    ↓
DOMContentLoaded Event
    ↓
loadNotifications()
    ↓
API Call: GET /api/notifications/count
    ↓
NotificationController::getUnreadCount()
    ↓
Check: $user->needsPasswordChange()
    ↓
Return: { "count": 1 }
    ↓
Update Badge: Show "1"
    ↓
API Call: GET /api/notifications
    ↓
NotificationController::getNotifications()
    ↓
Return: Array dengan notifikasi
    ↓
Render Notification Item
```

### Flow 2: User Mengubah Password
```
User Click "Ubah Password"
    ↓
Redirect ke /profile
    ↓
User Isi Form Password
    ↓
Submit Form
    ↓
ProfileController::updatePassword()
    ↓
Update: password + password_changed_at = now()
    ↓
Redirect ke /profile dengan success message
    ↓
User Refresh atau Navigate
    ↓
loadNotifications()
    ↓
Check: $user->needsPasswordChange() → false
    ↓
Return: { "count": 0 }
    ↓
Hide Badge
    ↓
Show: "Tidak ada notifikasi"
```

### Flow 3: Admin Melihat Status Password
```
Admin Login
    ↓
Navigate ke /akun
    ↓
AkunController::index()
    ↓
Query: Get all managers and rmfts
    ↓
Return View dengan data
    ↓
Blade Template Loop
    ↓
For Each User:
    Check: $user->password_changed_at
    ↓
    If NULL:
        Show Badge Warning "Password Default"
    ↓
    If NOT NULL:
        Show Badge Success "Sudah Diubah"
        Show Timestamp
```

## API Response Format

### GET /api/notifications/count
```json
{
    "count": 1
}
```

### GET /api/notifications
```json
{
    "notifications": [
        {
            "id": "password-change",
            "type": "warning",
            "title": "Ubah Password Default",
            "message": "Untuk keamanan akun Anda, silakan ubah password default Anda.",
            "link": "http://localhost/profile",
            "link_text": "Ubah Password"
        }
    ]
}
```

## Security Considerations

1. **Password Hashing:** Semua password di-hash dengan bcrypt
2. **Middleware Auth:** API notifikasi hanya bisa diakses user yang login
3. **Role Check:** Notifikasi hanya muncul untuk role yang sesuai
4. **Timestamp Audit:** `password_changed_at` dicatat untuk audit trail
5. **CSRF Protection:** Form update password menggunakan CSRF token

## Performance Considerations

1. **Auto Refresh:** Interval 5 menit untuk menghindari terlalu banyak request
2. **Lightweight API:** Response JSON minimal
3. **Client-side Rendering:** Notifikasi di-render di client untuk mengurangi server load
4. **Database Index:** Kolom `role` dan `password_changed_at` bisa di-index jika perlu

## Extensibility

Fitur ini bisa diperluas untuk:
1. Multiple notification types
2. Notification history
3. Mark as read functionality
4. Push notifications
5. Email reminders
6. Custom notification preferences

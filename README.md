# Warehouse Inventory

Aplikasi web manajemen inventori gudang yang dibangun dengan **Laravel**, **Vue 3**, dan **Inertia.js**. Aplikasi ini membantu mencatat dan memantau data barang serta stok gudang dalam satu tempat.

## Teknologi yang Digunakan

| Bagian        | Teknologi                                                |
|---------------|----------------------------------------------------------|
| Backend       | PHP 8.3+, Laravel 13, Laravel Fortify, Laravel Sanctum   |
| Frontend      | Vue 3, TypeScript, Inertia.js 3, Tailwind CSS 4          |
| Build tool    | Vite (Vite+)                                             |
| Testing       | Pest                                                     |
| Kualitas kode | Laravel Pint, PHPStan (Larastan), vue-tsc                |

## Prasyarat

Pastikan perangkat lunak berikut sudah terpasang di komputer kamu:

- **PHP** versi 8.3 atau lebih baru
- **Composer** (https://getcomposer.org)
- **Node.js** dan **npm** (https://nodejs.org)
- **Git**
- **Database**: SQLite (default) atau MySQL/PostgreSQL

Cek versi dengan perintah berikut:

```bash
php -v
composer -V
node -v
npm -v
```

> Jika memakai SQLite, pastikan ekstensi PHP `pdo_sqlite` dan `sqlite3` aktif.

## Instalasi

### 1. Clone repository

```bash
git clone https://github.com/ffadlyyy/warehouse-inventory.git
cd warehouse-inventory
```

### 2. Instalasi cepat

Satu perintah ini akan menginstal dependensi PHP, membuat file `.env`, membuat app key, menjalankan migrasi database, menginstal dependensi npm, dan build aset frontend:

```bash
composer setup
```

Jika berhasil, lanjut ke bagian [Menjalankan Aplikasi](#menjalankan-aplikasi).

### 3. Instalasi manual (jika sebelumnya gagal)

```bash
# Instal dependensi PHP
composer install

# Salin file environment
cp .env.example .env          # Windows (CMD): copy .env.example .env

# Buat application key
php artisan key:generate

# Instal dependensi frontend
npm install
```

### 4. Konfigurasi database

Buka file `.env` dan sesuaikan pengaturan database.

**Opsi A: SQLite**

```env
DB_CONNECTION=sqlite
```

Buat file database kosong jika belum ada:

```bash
touch database/database.sqlite    # Windows (CMD): type nul > database\database.sqlite
```

**Opsi B: MySQL**

Buat database kosong terlebih dahulu (misalnya `warehouse_inventory`), lalu isi:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=warehouse_inventory
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan migrasi database

```bash
php artisan migrate
```

Jika proyek memiliki seeder, jalankan juga:

```bash
php artisan db:seed
```

## Menjalankan Aplikasi

Jalankan server development (backend dan frontend sekaligus):

```bash
composer dev
```

Buka alamat yang muncul di terminal (biasanya **http://localhost:8000**) melalui browser.

Jika `composer dev` bermasalah, jalankan dua terminal terpisah:

```bash
# Terminal 1: backend
php artisan serve

# Terminal 2: frontend
npm run dev
```

Untuk menghentikan server, tekan `Ctrl + C` di terminal.

## Akses Login

Ada dua cara untuk masuk ke aplikasi.

### Cara 1: Daftar akun baru

1. Buka halaman registrasi di **http://localhost:8000/register**
2. Isi nama, email, dan password
3. Setelah terdaftar, login di **http://localhost:8000/login**

### Cara 2: Buat akun lewat terminal

```bash
php artisan tinker
```

Lalu jalankan:

```php
\App\Models\User::factory()->create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => 'password',
]);
```

Ketik `exit` untuk keluar dari tinker, lalu login dengan akun berikut:

| Peran | Email               | Password   |
|-------|---------------------|------------|
| Admin | admin@example.com   | password   |

> **Penting:** akun di atas hanya untuk pengembangan lokal. Jangan gunakan password ini di lingkungan produksi.

## Perintah yang Sering Dipakai

| Perintah               | Fungsi                                              |
|------------------------|-----------------------------------------------------|
| `composer dev`         | Menjalankan server development                      |
| `npm run build`        | Build aset frontend untuk produksi                  |
| `composer test`        | Menjalankan lint, analisis statis, dan pengujian    |
| `composer lint`        | Merapikan kode PHP dengan Pint                      |
| `composer types:check` | Analisis statis dengan PHPStan                      |
| `npm run types:check`  | Pengecekan tipe Vue/TypeScript                      |

## Pemecahan Masalah

**Halaman tampil kosong atau error `Vite manifest not found`**
Aset frontend belum di-build. Jalankan `npm run dev` (saat development) atau `npm run build`.

**Error `No application encryption key has been specified`**
Jalankan `php artisan key:generate`.

**Error `could not find driver`**
Ekstensi PHP untuk database belum aktif. Aktifkan `pdo_sqlite` (SQLite) atau `pdo_mysql` (MySQL) di `php.ini`.

**Error koneksi database**
Periksa kembali nilai `DB_*` di file `.env`, pastikan server database berjalan, lalu jalankan `php artisan config:clear`.

**Perubahan `.env` tidak terbaca**
Jalankan `php artisan config:clear`, lalu restart server.

## Struktur Folder

```
app/          Kode aplikasi (model, controller, action)
bootstrap/    File bootstrap framework
config/       File konfigurasi
database/     Migrasi, factory, seeder
public/       Entry point dan aset publik
resources/    Halaman dan komponen Vue, CSS
routes/       Definisi rute
storage/      Log, cache, dan file unggahan
tests/        Pengujian (Pest)
```

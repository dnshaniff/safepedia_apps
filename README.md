# WAS Apps

Warehouse Approval System (WAS Apps) adalah aplikasi pengajuan pembangunan gudang distribusi dengan proses approval berjenjang.

Dibangun menggunakan:

- Laravel 12
- PostgreSQL
- Bootstrap 5
- jQuery
- DataTables
- Leaflet Maps
- ApexCharts
- Dropzone
- FormValidation

---

## Features

### Authentication

- Login system
- Role based access control
- Permission management

### Warehouse Construction

- Create warehouse construction request
- Select warehouse location using interactive map (Leaflet)
- Upload multiple supporting documents (minimum 3 files)
- Construction budget estimation
- Dynamic budget item repeater
- Automatic budget calculation

### Approval Workflow

Approval dilakukan sesuai urutan berikut:

1. Requestor
2. SPV Gudang
3. Kepala Gudang
4. Manager Operasional
5. Direktur Operasional
6. Direktur Keuangan

Fitur approval:

- Approve
- Reject / Return
- Approval history
- Approval notes
- Status tracking

### Dashboard

- Total Construction
- In Progress Construction
- Approved Construction
- Monthly Construction Statistics
- Latest Construction Monitoring

---

## Requirements

Pastikan environment memiliki:

- PHP 8.3+
- Composer
- PostgreSQL 14+
- NodeJS 20+
- NPM

---

## Installation

Clone repository:

```bash
git clone <repository-url>
```

Masuk ke folder project:

```bash
cd was-apps
```

Install dependency PHP:

```bash
composer install
```

Install dependency frontend:

```bash
npm install
```

---

## Environment Setup

Copy file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Sesuaikan konfigurasi database PostgreSQL:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=safepedia_apps
DB_USERNAME=your_postgres_username
DB_PASSWORD=your_postgres_password
```

> Pastikan `DB_USERNAME` dan `DB_PASSWORD` disesuaikan dengan PostgreSQL yang tersedia pada komputer Anda.

---

## Database Setup

Jalankan migrasi dan seeder:

```bash
php artisan migrate --seed
```

Seeder yang tersedia:

- PermissionSeeder
- EmployeeSeeder
- ApprovalSeeder

---

## Storage Link

Jalankan perintah berikut untuk mengakses file upload:

```bash
php artisan storage:link
```

---

## Frontend Build

Development:

```bash
npm run dev
```

---

## Running Application

Jalankan server Laravel:

```bash
php artisan serve
```

Aplikasi akan tersedia pada:

```text
http://127.0.0.1:8000
```

---

## Important Notes

Sebelum melakukan pengujian aplikasi:

1. Login menggunakan akun hasil seeder.
2. Klik avatar pengguna pada pojok kanan atas halaman.
3. Klik nama pengguna untuk membuka halaman Profile.
4. Aktifkan Two Factor Authentication (2FA).
5. Gunakan Google Authenticator atau aplikasi TOTP sejenis untuk menghasilkan kode OTP.
6. Setelah 2FA aktif, logout dan login kembali untuk menguji proses verifikasi OTP.

---

## Login Accounts

Password seluruh akun:

```text
P@ssw0rd123
```

| Username | Full Name | Position | Role |
|-----------|-----------|-----------|-----------|
| administrator | Administrator | System Administrator | Administrator |
| budi | Budi | Staff Gudang | User |
| maman | Maman | SPV Gudang | Supervisi |
| cecep | Cecep | Kepala Gudang | Supervisi |
| asep | Asep | Manager Operasional | Supervisi |
| yohan | Yohan | Direktur Operasional | Supervisi |
| yoseph | Direktur Keuangan | Direktur Keuangan | Supervisi |

Gunakan akun-akun di atas untuk menguji proses approval sesuai tahapan yang tersedia.

---

## Two Factor Authentication (2FA)

Aplikasi menggunakan TOTP (Time-based One Time Password) yang kompatibel dengan:

- Google Authenticator
- Microsoft Authenticator
- Authy
- Aplikasi TOTP lainnya

### Aktivasi 2FA

1. Login menggunakan salah satu akun yang tersedia.
2. Klik avatar pengguna pada pojok kanan atas.
3. Klik nama pengguna untuk membuka halaman Profile.
4. Pada halaman Profile, buka bagian **Two Factor Authentication**.
5. Scan QR Code menggunakan aplikasi authenticator.
6. Masukkan kode OTP yang dihasilkan aplikasi.
7. Simpan konfigurasi.

Setelah aktif, user akan diminta memasukkan kode OTP setiap kali login.

---

## Approval Status

| Status | Description |
|----------|-------------|
| Draft | Pengajuan baru dibuat |
| Pending | Menunggu approval berikutnya |
| Approved | Seluruh approval selesai |
| Returned | Dikembalikan untuk revisi |
| Canceled | Pengajuan dibatalkan |

> Untuk menguji alur approval secara penuh, gunakan akun sesuai urutan role yang telah disediakan pada bagian Login Accounts.

---

## Project Structure

```text
app/
├── Domains
│   ├── Approvals
│   ├── Employees
│   ├── Pages
│   ├── Roles
│   ├── Users
│   └── WarehouseConstructions
```

Setiap domain dipisahkan berdasarkan:

- Controllers
- Requests
- Services
- Models

Pendekatan ini digunakan untuk menjaga modularitas dan mempermudah maintenance.

## Notes

- File upload disimpan menggunakan Laravel Storage.
- Koordinat gudang menggunakan Leaflet dan OpenStreetMap.
- Budget dihitung otomatis berdasarkan item budget yang diinput.
- Approval history tersimpan untuk setiap tahapan approval.
- Dashboard menampilkan statistik dan monitoring pengajuan pembangunan gudang.

---

## Technical Test

Project ini dibuat sebagai submission untuk:

**Technical Test PHP Developer**  
PT Safepedia Global Teknologi

# Ruang Booking Final 2

Aplikasi **Ruang Booking Final 2** adalah sistem manajemen pemesanan ruangan yang dibuat menggunakan **Laravel 11+** dan **Tailwind CSS**. Aplikasi ini digunakan untuk mengelola peminjaman ruang, dengan fitur otentikasi, role user dan admin, serta sistem approval dan notifikasi.

---

## 🔧 Cara Instalasi

1. **Clone atau download repository**
   ```bash
   git clone https://github.com/namauser/ruang-booking-final-2.git
   cd ruang-booking-final-2
   ```

2. **Install dependency**
   ```bash
   composer install
   npm install
   npm run build
   ```

3. **Copy file `.env` dan konfigurasi**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Setup database**
   - Edit file `.env`:
     ```env
     DB_DATABASE=ruang_booking
     DB_USERNAME=root
     DB_PASSWORD=
     ```

   - Jalankan migrate dan seeder:
     ```bash
     php artisan migrate --seed
     ```

5. **Jalankan server**
   ```bash
   php artisan serve
   ```

   Akses aplikasi di: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## 👥 Role & Login

- **Admin**
  - Akses: `/admin/dashboard`
  - Role: `admin`
- **User**
  - Akses: `/dashboard`
  - Role: `user`

Login dapat dilakukan melalui halaman `/login`.

---

## Fitur Utama

- ✅ Autentikasi dan Registrasi** (Laravel Breeze)
- ✅ Role-based access** (`admin` & `user`)
- ✅ Dashboard admin dan user terpisah**
- ✅ Peminjaman Ruangan**
- ✅ Approval Booking oleh Admin**
- ✅ Riwayat & Status Peminjaman**
- ✅ Notifikasi flash / status**
- ✅ Tampilan modern menggunakan Tailwind CSS**
- ✅ Validasi Formulir**

---

## Struktur Folder Penting

- `app/Http/Controllers/Admin/` → Logic admin
- `resources/views/admin/` → Tampilan admin
- `routes/web.php` → Routing utama
- `app/Models/Booking.php` → Model peminjaman

> Dibuat dengan semangat tinggi walaupun sambil pusing oleh [Verina]
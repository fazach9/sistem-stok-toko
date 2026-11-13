# 📦 Website Inventaris Gudang & Manajemen Stok Toko

Aplikasi web berbasis **PHP** dan **MySQL** yang dilengkapi dengan antarmuka **Bootstrap** serta tabel interaktif **DataTables** untuk membantu pencatatan, pengelolaan transaksi, dan pemantauan stok barang secara efisien.

> *Proyek ini dikembangkan untuk mendigitalisasi pencatatan dan pemantauan ketersediaan stok barang pada operasional gudang maupun toko.*

## ✨ Fitur Utama
- **Autentikasi Admin (`index.php` & `logout.php`):** Sistem login dan logout untuk menjaga keamanan akses data inventaris.
- **Dashboard Ringkasan (`dashboard.php`):** Menampilkan ikhtisar informasi stok barang dan aktivitas inventaris secara ringkas.
- **Manajemen Data Barang (`barang.php`):** Fitur tambah, lihat, ubah, dan hapus data barang yang dilengkapi pencarian dan penyortiran cepat menggunakan **DataTables**.
- **Pencatatan Transaksi (`transaksi.php`):** Mencatat riwayat barang masuk dan keluar yang terintegrasi langsung dengan pembaruan jumlah stok.
- **Database Terpusat (`database.sql` & `config.php`):** Penyimpanan data terstruktur menggunakan **MySQL**.

## 🛠️ Teknologi yang Digunakan
- **Backend:** PHP Native
- **Database:** MySQL / MariaDB
- **Frontend & UI:** HTML5, CSS3, Bootstrap
- **Plugin Tabel:** DataTables (pencarian instan, paginasi, dan filter data)

## 🗂️ Struktur Folder
```text
stok-toko/
├── config.php       # Konfigurasi koneksi database MySQL
├── database.sql     # File skema dan data awal database
├── index.php        # Halaman login pengguna
├── dashboard.php    # Halaman utama dashboard admin
├── barang.php       # Halaman manajemen stok barang
├── transaksi.php    # Halaman pencatatan transaksi stok
├── logout.php       # Proses keluar sesi (logout)
└── README.md        # Dokumentasi proyek
```

## 🚀 Cara Instalasi & Menjalankan
1. **Unduh atau Clone Repository**
   Pindahkan folder proyek ke dalam direktori web server lokal Anda (contoh: `C:\xampp\htdocs\stok-toko` untuk pengguna XAMPP).
2. **Import Database**
   - Buka **phpMyAdmin** melalui browser (`http://localhost/phpmyadmin`).
   - Buat database baru (sesuaikan namanya dengan konfigurasi di file `config.php`).
   - Pilih tab **Import**, lalu pilih dan unggah file `database.sql`.
3. **Konfigurasi Koneksi**
   Buka file `config.php` dan sesuaikan pengaturan `host`, `username`, `password`, serta `nama database` dengan server lokal Anda.
4. **Akses Aplikasi**
   Jalankan Apache dan MySQL pada XAMPP, lalu buka browser dan akses `http://localhost/stok-toko`.

## 👨‍💻 Pengembang
**Faza Chansa Maulana, S.Kom**
- **Latar Belakang:** S1 Teknik Informatika – Universitas Muria Kudus
- **Email:** [fazachansa123@gmail.com](mailto:fazachansa123@gmail.com)
- **Lokasi:** Kudus, Jawa Tengah, Indonesia

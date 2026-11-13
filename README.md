# Sistem Pencatatan Stok Toko (PHP + MySQL + Bootstrap)

## Fitur
- Login admin (password di-hash, proteksi CSRF, query terparameter). Akun admin dibuat saat pertama kali dibuka.
- Dashboard: jumlah barang, total unit, nilai stok, stok menipis, penjualan hari ini, grafik masuk vs keluar 7 hari, daftar perlu restock.
- Data barang: tambah, edit, hapus, kategori, satuan, harga beli/jual, stok minimum, pencarian dan sorting (DataTables), ekspor CSV.
- Transaksi stok: barang masuk (restock) dan keluar (terjual), stok otomatis berubah, stok tidak bisa minus, riwayat dengan filter tanggal/jenis, ekspor CSV.

## Instalasi (XAMPP / Laragon)
1. Salin folder `stok-toko` ke `htdocs` (XAMPP) atau `www` (Laragon).
2. Nyalakan Apache dan MySQL, buka phpMyAdmin, lalu **Import** `database.sql`.
3. Jika perlu, sesuaikan user/password database di `config.php`.
4. Buka `http://localhost/stok-toko/` dan buat akun admin.

Catatan: Bootstrap, jQuery, DataTables, dan Chart.js dimuat dari CDN, jadi perlu koneksi internet. Aplikasi PHP ini tidak bisa dipasang di GitHub Pages (butuh hosting PHP). Jika di-online-kan, gunakan HTTPS dan password database yang kuat.

CREATE DATABASE IF NOT EXISTS stok_toko CHARACTER SET utf8mb4;
USE stok_toko;
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS barang (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(30) UNIQUE NOT NULL,
  nama VARCHAR(120) NOT NULL,
  kategori VARCHAR(60) DEFAULT 'Umum',
  satuan VARCHAR(20) DEFAULT 'pcs',
  harga_beli DECIMAL(12,0) DEFAULT 0,
  harga_jual DECIMAL(12,0) DEFAULT 0,
  stok INT DEFAULT 0,
  stok_min INT DEFAULT 5,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS transaksi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  barang_id INT NOT NULL,
  jenis ENUM('masuk','keluar') NOT NULL,
  qty INT NOT NULL,
  harga DECIMAL(12,0) DEFAULT 0,
  catatan VARCHAR(200),
  user_id INT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (barang_id) REFERENCES barang(id) ON DELETE CASCADE
);
INSERT IGNORE INTO barang (kode,nama,kategori,satuan,harga_beli,harga_jual,stok,stok_min) VALUES
('BRG001','Beras 5 kg','Sembako','karung',58000,63000,20,5),
('BRG002','Minyak Goreng 1 L','Sembako','botol',15000,17000,3,6),
('BRG003','Gula Pasir 1 kg','Sembako','pak',14000,16000,25,8),
('BRG004','Mie Instan','Makanan','bungkus',2800,3500,100,30),
('BRG005','Air Mineral 600 ml','Minuman','botol',2500,3500,4,12);

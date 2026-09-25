-- Database Inventaris dengan 3 Tabel + Foreign Keys & Seed Data

CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS supplier (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    kontak VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(150) NOT NULL,
    harga DECIMAL(12,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    kategori_id INT NOT NULL,
    supplier_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE,
    FOREIGN KEY (supplier_id) REFERENCES supplier(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed Data (Minimal 5 per tabel)
INSERT INTO kategori (nama_kategori) VALUES
('Elektronik'), ('Aksesoris Komputer'), ('Perangkat Jaringan'), ('Komponen PC'), ('Peralatan Kantor');

INSERT INTO supplier (nama_supplier, kontak) VALUES
('PT Tech Nusantara', '081234567890'),
('CV Komputer Jaya', '082198765432'),
('Distributor IndoTech', '085211223344'),
('Global Supply Corp', '081988776655'),
('Mega Hardware Store', '087766554433');

INSERT INTO produk (nama_produk, harga, stok, kategori_id, supplier_id) VALUES
('Laptop Gaming RTX 4060', 15500000.00, 10, 1, 1),
('Keyboard Mekanikal RGB', 750000.00, 25, 2, 2),
('Router WiFi 6 AX3000', 1200000.00, 15, 3, 3),
('RAM DDR5 32GB Kit', 2100000.00, 20, 4, 4),
('Monitor 27 Inch 165Hz', 3400000.00, 12, 1, 5);

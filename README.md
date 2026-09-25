# 📦 Sistem CRUD Inventaris Barang — Tugas Rutin 8

Aplikasi manajemen inventaris barang berbasis **PHP Native** dan **MySQL** yang dibangun menggunakan arsitektur **PDO Singleton Pattern**, **Prepared Statements**, serta proteksi keamanan dari serangan SQL Injection dan Cross-Site Scripting (XSS)[cite: 41, 46, 49].

Proyek ini dibuat untuk memenuhi kriteria pengumpulan **Tugas Rutin 8 (TR 8)** pada mata kuliah **Pemrograman Web**.

---

## 🚀 Fitur Utama & Pemenuhan Persyaratan (Requirements)

- **Database Relasional & Foreign Keys**: Menggunakan 3 tabel terintegrasi (`kategori`, `supplier`, dan `produk`) dengan batasan *Foreign Key* `ON DELETE CASCADE`.
- **Data Seed Awal**: Dilengkapi dengan minimal 5 data *dummy* pada masing-masing tabel[cite: 41, 47].
- **Koneksi PDO Singleton Pattern**: Mengoptimalkan pengelolaan koneksi database agar hanya membuat satu instance `PDO` secara efisien (`config/Database.php`).
- **Tampilan List Produk (JOIN 2 Tabel)**: Menampilkan nama kategori dan supplier secara dinamis menggabungkan data dari 2 tabel[cite: 41, 46].
- **Form Create & Update Relasional**: Pilihan kategori dan supplier ditampilkan menggunakan dropdown `<select>` dinamis, serta form edit dilengkapi dengan data terisi otomatis (*pre-filled*)[cite: 41, 44, 45].
- **Prepared Statements**: Seluruh kueri manipulasi dan pencarian data diikat menggunakan `PDO::prepare()` dan `bindValue()` untuk menjamin keamanan dari SQL Injection[cite: 41, 43, 44, 45, 46].
- **XSS Protection**: Seluruh teks yang dicitak ke antarmuka HTML dibungkus aman dengan fungsi `htmlspecialchars()`[cite: 41, 46].
- **Flash Message System**: Menampilkan notifikasi umpan balik sukses/gagal yang responsif setelah melakukan aksi data (*Redirect Pattern*)[cite: 41, 46].
- **UI Modern & Dark Mode**: Antarmuka bersih dan nyaman menggunakan Bootstrap 5 dan Bootstrap Icons[cite: 46, 48].

### 🌟 Fitur Tambahan (Bonus Points)
- **Database Transaction pada Delete**: Proses penghapusan data berada dalam lingkup `beginTransaction()` dan `commit()` / `rollBack()`[cite: 41, 43].
- **Fitur Pencarian (Search)**: Pencarian cepat berdasarkan nama produk, kategori, maupun nama supplier[cite: 41, 46].
- **Pagination**: Penomoran halaman otomatis untuk membatasi tampilan baris data agar rapi[cite: 41, 46].

---

## 📁 Struktur Folder Proyek

```text
TugasWeb-Pertemuan8-CRUDInventaris/
├── config/
│   └── Database.php      # Koneksi Database (PDO Singleton)
├── css/
│   └── style.css         # Styling kustom antarmuka
├── create.php            # Form Tambah Produk Baru
├── database.sql          # Skema Database & Seed Data
├── delete.php            # Proses Hapus Data (Transaction)
├── edit.php              # Form Edit Produk (Pre-filled)
├── index.php             # Halaman Utama List Produk + Search + Pagination
└── README.md             # Dokumentasi Proyek




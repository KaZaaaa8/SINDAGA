# SINDAGA - Sistem Informasi Data Warga

SINDAGA adalah sistem informasi manajemen data kependudukan berbasis web yang dirancang untuk mengelola data penduduk, kartu keluarga, dan wilayah administratif secara efisien.

---

## 📋 Fitur Utama

### 1. Manajemen Data Penduduk
- Input dan edit data penduduk.
- Pencarian dan filter data.
- Status kependudukan (Menetap/Pindah/Meninggal).
- Riwayat perubahan data.

### 2. Kartu Keluarga
- Pengelolaan data KK.
- Manajemen anggota keluarga.
- Status hubungan keluarga.
- Validasi data KK.

### 3. Manajemen Wilayah
- Struktur wilayah bertingkat.
- Pengelolaan RT/RW.
- Pemetaan penduduk per wilayah.
- Statistik wilayah.

### 4. Sistem Pelaporan
- Laporan data penduduk.
- Laporan kartu keluarga.
- Export data ke Excel.
- Filter laporan berdasarkan periode.

### 5. Manajemen Pengguna
- Multi-level user (Admin & Petugas).
- Manajemen akses.
- Profil pengguna.
- Log aktivitas.

### 6. Database Tools
- Backup database.
- Restore database.
- Monitoring sistem.

---

## 🛠 Teknologi

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: Tailwind CSS
- **Library**: PHPSpreadsheet

---

## ⚙️ Persyaratan Sistem

- **Web Server**: Apache/Nginx
- **PHP**: Versi 7.4 atau lebih tinggi
- **Database**: MySQL 5.7 atau lebih tinggi
- **Dependency Manager**: Composer

---

## 📂 Struktur Direktori

```
SINDAGA/
├── config/             # File konfigurasi
├── includes/           # Komponen shared
├── pages/              # Halaman aplikasi
│   ├── auth/           # Autentikasi
│   ├── penduduk/       # Manajemen penduduk
│   ├── kartu-keluarga/ # Manajemen KK
│   ├── wilayah/        # Manajemen wilayah
│   ├── laporan/        # Sistem laporan
│   ├── pengguna/       # Manajemen user
│   └── database/       # Tools database
└── vendor/             # Dependencies
```

---

## 🔒 Keamanan

- Password hashing menggunakan algoritma modern.
- Validasi input dan sanitasi data.
- Proteksi terhadap SQL Injection.
- Manajemen session yang aman.
- Role-based access control (RBAC).

---

## 🚀 Penggunaan

### 1. Login Sistem

### 2. Manajemen Data
#### Data Penduduk
- Input data lengkap penduduk.
- Upload dokumen pendukung.
- Update status kependudukan.

#### Kartu Keluarga
- Pembuatan KK baru.
- Penambahan anggota keluarga.
- Perubahan data KK.

#### Pelaporan
- Generate laporan periode tertentu.
- Export data ke Excel.
- Cetak laporan.

### 3. Backup & Restore
#### Backup Database
1. Akses menu **Database**.
2. Klik tombol **Backup**.
3. Simpan file SQL.

#### Restore Database
1. Pilih file backup SQL.
2. Klik tombol **Restore**.
3. Konfirmasi proses.

---

## 💻 Pengembangan

### Coding Standards
- PSR-4 autoloading.
- Clean code principles.
- Dokumentasi fungsi.
- Consistent naming convention.

### Version Control
- Git flow workflow.
- Semantic versioning.
- Meaningful commit messages.

---

## 📜 Lisensi

SINDAGA dilisensikan di bawah [MIT License](LICENSE).

---

## 👨‍💻 Pengembang

**Muhammad Faza Husnan**  
📧 Email: [fazahusnan06@gmail.com](mailto:fazahusnan06@gmail.com)

---

## 📞 Dukungan
Untuk bantuan dan dukungan, hubungi kami:
- **Email**: [fazahusnan06@gmail.com](mailto:fazahusnan06@gmail.com)

© 2024 SINDAGA. All rights reserved.

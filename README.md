# Kiosk-Web-PHP-Native

## Sistem Kiosk Pemesanan Makanan & Minuman

Sistem ini merupakan aplikasi kiosk pemesanan berbasis web yang dikembangkan menggunakan **PHP Native** dan **Database SQL**. Sistem dirancang untuk melayani proses pemesanan makanan dan minuman secara efisien melalui peran tiga jenis pengguna utama: **Pembeli**, **Karyawan**, dan **Admin**.

## 📦 Teknologi yang Digunakan

- **Bahasa Pemrograman:** PHP Native (tanpa framework)
- **Database:** SQL (MySQL / MariaDB)

## 🔐 Login Admin

Untuk kebutuhan pengujian atau penggunaan awal, sistem menyediakan akun admin default dengan kredensial berikut:

- **Username:** `admin`
- **Password:** `admin`

> Catatan: Username dan password ini disimpan secara hardcoded dalam sistem. Setelah deploy ke produksi, disarankan untuk mengubah metode autentikasi agar lebih aman.

Akun karyawan dapat dibuat melalui dashboard admin setelah login.

---

## 👥 Jenis Pengguna dan Fungsionalitas

### 1. Pembeli
Pengguna yang ingin melakukan pemesanan makanan dan minuman. Fitur yang tersedia untuk pembeli meliputi:

- Menampilkan daftar menu
- Menambahkan item ke keranjang
- Checkout pesanan


### 2. Karyawan
Bertugas menangani proses pemesanan yang masuk. Fitur utama untuk karyawan antara lain:

- Melihat daftar pesanan masuk
- Mengonfirmasi pembayaran dan invoice
- Memproses dan menyelesaikan pesanan
- Mengelola daftar menu (tambah/edit/hapus item)

### 3. Admin
Memiliki hak akses penuh terhadap sistem. Fitur yang dapat diakses oleh admin meliputi:

- Pengaturan harga dan daftar menu
- Pencatatan arus kas harian
- Pembuatan akun karyawan
- Melihat dan menyusun laporan penjualan

---
[manualbookcaffeKiosK.pdf](https://github.com/user-attachments/files/20795001/manualbookcaffeKiosK.pdf)

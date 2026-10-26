# Buku Induk Sekolah

Aplikasi administrasi sekolah berbasis CodeIgniter 4 untuk mengelola data siswa, guru, kelas, dan ringkasan laporan.

## Kebutuhan

- PHP 8.2 atau lebih baru dengan ekstensi `intl`, `mbstring`, `mysqli`, dan `pdo_mysql`
- Composer
- MySQL atau MariaDB

## Menjalankan secara lokal

1. Buat database dan user MySQL khusus aplikasi, misalnya `buku_induk_sekolah` dan `buku_app`, lalu berikan hak akses ke database tersebut.
2. Atur host, nama database, username, dan password di `.env`. Jangan commit atau bagikan `.env`.
3. Pasang dependency: `composer install`.
4. Buat tabel: `php spark migrate`.
5. Jalankan aplikasi: `php spark serve`.
6. Buka `http://localhost:8080`. Saat pertama kali, aplikasi meminta Anda membuat akun administrator.

Untuk pemakaian lokal, `php spark serve` menggunakan port 8080. Jika memasang Apache atau Nginx nanti, arahkan document root ke direktori `public`.

Password administrator minimal 12 karakter. Password disimpan menggunakan `password_hash`; halaman setup otomatis ditutup setelah akun pertama dibuat.

## Modul awal

- Login dan setup administrator pertama
- Pengelolaan akun administrator/operator oleh administrator
- Dashboard ringkas jumlah siswa, guru, dan kelas
- CRUD data siswa dengan profil lengkap (identitas, alamat, orang tua/wali, bantuan, sekolah, dan data fisik), guru, dan kelas
- Peringatan data utama siswa yang kosong pada daftar dan detail siswa
- Import siswa melalui CSV atau salin-tempel tabel dari Excel (maksimal 1.000 baris per proses)
- Import dan lihat nilai rapor 11 mata pelajaran per tahun ajaran dan semester; administrator dapat mengubah nama mapel
- Hapus seluruh data siswa (khusus administrator dengan konfirmasi ketik)
- Laporan jumlah siswa per kelas dan tahun ajaran

## Import siswa dari Excel/CSV

Buka menu **Data siswa → Backup data siswa** untuk mengunduh seluruh data siswa sebagai CSV, atau pilih **Import Excel/CSV** untuk menambah data. Pada halaman import, salin header template ke Excel, isi tabel, lalu tempel seluruh tabel termasuk header. Alternatifnya, unduh template CSV dan unggah kembali setelah diisi. Nama dan JK wajib diisi; NIPD boleh kosong bila belum tersedia. Tanggal menggunakan `YYYY-MM-DD` atau `DD/MM/YYYY`; rombel harus sudah tersedia di Data Kelas. Jika nama rombel dipakai untuk beberapa tahun ajaran, isi kolom Tahun Ajaran. Kolom opsional boleh kosong atau berisi tanda `-`; angka desimal dengan koma lokal dinormalisasi otomatis. Layak PIP menyimpan keterangan/kategori bantuan, bukan hanya Ya/Tidak. Template mencakup data identitas, alamat, orang tua/wali, bantuan, sekolah, serta data fisik. Import dibatalkan seluruhnya jika ada baris tidak valid atau NIPD duplikat.

## Nilai rapor

Buka menu **Nilai rapor**, unduh template CSV terbaru, lalu isi NIPD atau Nama Siswa, tahun ajaran (format `YYYY/YYYY`), semester Ganjil/Genap, dan nilai 0–100 untuk semua 11 mata pelajaran. Jika NIPD kosong atau tidak ditemukan, aplikasi mencari berdasarkan Nama Siswa; bila nama cocok dengan beberapa siswa, isi NIPD agar siswa tidak tertukar. Anda dapat mengunggah CSV UTF-8 atau menempelkan tabel dari Excel. Import menerima maksimal 1.000 siswa; jika ada kesalahan validasi, seluruh proses dibatalkan. Mengimpor siswa dan periode yang sudah tersimpan akan memperbarui nilainya. Administrator dapat mengganti nama mapel melalui **Atur nama mapel**; nilai yang tersimpan tetap mengikuti slot mapelnya. Jika tabel nilai rapor belum ada, buka menu tersebut dan administrator dapat menjalankan migration khusus melalui tombol yang tersedia; tombol tidak menjalankan migration aplikasi lainnya.

## Tes

Jalankan `composer test`.


jalankan web server 
cd /var/www/buku
php spark serve
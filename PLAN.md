# Rencana Implementasi Fitur Leaderboard

Berikut adalah langkah-langkah yang akan kita ambil untuk mengimplementasikan fitur leaderboard di sisi server.

- [ ] **1. Membuat Route:**
  - [ ] Buat endpoint `GET /contents/{content}/leaderboard` di file `routes/web.php` (atau `routes/student.php` jika ada).
  - [ ] Arahkan route tersebut ke metode `show` di `LeaderboardController`.

- [ ] **2. Membuat Controller:**
  - [ ] Buat file controller baru di `app/Http/Controllers/Student/LeaderboardController.php`.
  - [ ] Buat metode `show(Content $content)` di dalamnya.
  - [ ] Metode ini akan memanggil `LeaderboardService` dan mengembalikan view Inertia dengan data yang diterima.

- [ ] **3. Mengimplementasikan Logika di `LeaderboardService`:**
  - [ ] Buat metode publik `getLeaderboardForContent(Content $content)` di `app/Services/LeaderboardService.php`.
  - [ ] Implementasikan logika `if` untuk membedakan antara konten `Quiz` dan `Material`.
  - [ ] **Untuk Kuis:** Lakukan query `LEFT JOIN` dari siswa kelas ke `quiz_submissions` dan urutkan berdasarkan `id IS NULL`, `score DESC`, lalu `duration_seconds ASC`.
  - [ ] **Untuk Materi:** Lakukan query `LEFT JOIN` dari siswa kelas ke `content_student` dan urutkan berdasarkan `id IS NULL`, lalu `completed_at ASC`.
  - [ ] Setelah mendapatkan hasil query, lakukan iterasi untuk menambahkan atribut `rank` secara manual di PHP.

- [ ] **4. Membuat API Resource:**
  - [ ] Buat atau modifikasi `app/Http/Resources/LeaderboardResource.php`.
  - [ ] Format data output agar sesuai dengan kebutuhan frontend, termasuk `user`, `rank`, `score`, `time_spent`, dan `completed_at`.
  - [ ] Pastikan nilai-nilai seperti `rank` dan `score` menjadi `null` jika siswa belum mengerjakan konten.

# Rencana Implementasi Fitur Achievement & Activity Log

Rencana ini menguraikan langkah-langkah untuk mengimplementasikan sistem achievement yang modular dan sistem pencatatan aktivitas pengguna yang generik, berdasarkan diskusi dan penyempurnaan arsitektur.

## Fase 1: Fondasi Arsitektur (Log & Kontrak)

- [x] **Buat Migrasi `activity_log`**:
  - [x] Buat file migrasi baru `create_activity_log_table`.
  - [x] Definisikan skema: `id`, `user_id` (foreign key), `activity_type` (string, misal: 'user.login', 'content.completed'), `subject_id` & `subject_type` (polimorfik), `description` (text), `timestamps`.
- [x] **Buat Model `ActivityLog`**:
  - [x] Buat file model `app/Models/ActivityLog.php` untuk tabel baru.
- [x] **Buat `AchievementContract`**:
  - [x] Buat interface di `app/Contracts/AchievementContract.php`.
  - [x] Definisikan metode `slug(): string` dan `check(User $user, array $context = []): bool`.
- [x] **Buat Kelas Achievement Konkret**:
  - [x] Buat direktori `app/Achievements`.
  - [x] Buat kelas `QuizChampion.php`.
  - [x] Buat kelas `FastLearner.php`.
  - [x] Buat kelas `PerfectScore.php`.
  - [x] Buat kelas `StreakMaster.php`.
  - [x] Buat kelas `TopRank.php`.
  - [x] Pastikan setiap kelas mengimplementasikan `AchievementContract`.
- [x] **Buat `AchievementSeeder`**:
  - [x] Buat seeder baru `AchievementSeeder`.
  - [x] Isi seeder untuk mempopulasikan tabel `achievements` dengan data yang `slug`-nya cocok dengan kelas konkret.

## Fase 2: Orkestrasi & Pemicu Logika

- [x] **Buat `ActivityLogService`**:
  - [x] Buat kelas di `app/Services/ActivityLogService.php`.
  - [x] Implementasikan metode `log(...)` untuk sentralisasi pencatatan.
- [x] **Buat `AchievementService` (Orkestrator)**:
  - [x] Buat kelas di `app/Services/AchievementService.php`.
  - [x] Implementasikan registri untuk menampung semua kelas `AchievementContract`.
  - [x] Buat metode pemroses seperti `processQuizCompletion(...)` dan `processUserActivity(...)`.
- [x] **Integrasikan Pencatatan & Pemicu**:
  - [x] Buat listener untuk event Login dan panggil service yang relevan.
  - [x] Modifikasi listener `ProcessContentCompletion` untuk memanggil `ActivityLogService` dan `AchievementService`.
- [x] **Implementasikan Logika di Kelas Achievement**:
  - [x] Implementasikan metode `check()` di setiap kelas achievement (`QuizChampion`, `PerfectScore`, dll).
  - [x] Untuk `StreakMaster`, implementasikan kueri ke tabel `activity_log` untuk memeriksa tanggal berurutan.

## Fase 3: Penyediaan Data ke Halaman Inertia

- [x] **Buat `AchievementResource`**:
  - [x] Buat resource baru untuk memformat data achievement sesuai dengan struktur yang dibutuhkan frontend (`id`, `name`, `description`, `image`, `locked`, `achieved_at`).
- [x] **Modifikasi `Student\AchievementController`**:
  - [x] Edit metode `index()` di `app/Http/Controllers/Student/AchievementController.php`.
  - [x] Ambil semua data achievement dan status perolehan pengguna.
  - [x] Gunakan `AchievementResource` untuk memformat data.
  - [x] Kirim data sebagai prop ke view Inertia `student/achievement/index`.

## Fase 4: Verifikasi & Pengujian

- [ ] **Tulis Unit Tests**:
  - [ ] Buat unit test untuk setiap kelas achievement individual di `app/Achievements/`.
  - [ ] Buat unit test untuk `ActivityLogService`.
  - [ ] Buat unit test untuk `AchievementService`.
- [ ] **Tulis Feature Tests**:
  - [ ] Buat feature test untuk alur login dan verifikasi `activity_log`.
  - [ ] Buat feature test untuk alur penyelesaian kuis dan verifikasi `activity_log` serta perolehan achievement.
  - [ ] Buat feature test untuk halaman achievement (`Student\AchievementController@index`) dan validasi prop Inertia.

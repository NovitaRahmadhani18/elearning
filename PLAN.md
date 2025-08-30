
# Rencana Strategis Implementasi Sistem Notifikasi

Dokumen ini menguraikan rencana strategis untuk mengimplementasikan sistem notifikasi backend menggunakan fitur bawaan Laravel, yang terintegrasi dengan komponen frontend yang sudah ada dalam arsitektur Inertia.js.

---

### 1. **Memahami Tujuan**

Tujuan utamanya adalah membangun sistem notifikasi backend yang andal untuk memberi tahu pengguna tentang peristiwa penting di dalam aplikasi. Metode pengiriman data ke frontend harus melalui *shared props* Inertia, bukan API JSON, dan format data harus sesuai dengan tipe `TNotification` yang ada di frontend.

**Skenario Notifikasi yang Diperlukan:**

1.  **Kelas Baru untuk Guru:** Saat admin membuat kelas baru, guru yang ditugaskan menerima notifikasi.
2.  **Konten Baru untuk Siswa:** Saat guru membuat konten, semua siswa di kelas tersebut menerima notifikasi.
3.  **Penyelesaian Kuis oleh Siswa:** Saat siswa menyelesaikan kuis, guru menerima notifikasi.
4.  **Siswa Bergabung ke Kelas:** Saat siswa bergabung ke kelas, admin dan guru menerima notifikasi.
5.  **Pencapaian untuk Siswa:** Saat siswa mendapatkan *achievement*, siswa tersebut menerima notifikasi.

---

### 2. **Investigasi & Analisis**

-   **Komponen Frontend:** File `@resources/js/components/notification-popover.tsx` menjadi acuan utama, terutama tipe `TNotification`.
-   **Arsitektur Backend:** Aplikasi sudah menggunakan pola *event-driven*, yang akan kita manfaatkan untuk memicu notifikasi secara bersih.
-   **Alur Data:** Data notifikasi akan disediakan sebagai *shared props* melalui middleware `app/Http/Middleware/HandleInertiaRequests.php`.
-   **Perubahan Status:** Tindakan seperti "Mark as Read" akan ditangani melalui rute web standar dengan `return back()`, sesuai dengan pola Inertia.

---

### 3. **Pendekatan Strategis yang Diusulkan**

-   **Fase 1: Penyiapan Fondasi Notifikasi**
    1.  Buat migrasi tabel `notifications` melalui `php artisan notifications:table` dan jalankan `migrate`.
    2.  Tambahkan *trait* `Notifiable` ke model `User`.

-   **Fase 2: Implementasi Notifikasi di Backend**
    1.  Buat kelas Notifikasi untuk setiap skenario (`NewContentAvailable`, `QuizGraded`, dll.).
    2.  Konfigurasikan metode `toDatabase` di setiap kelas untuk menghasilkan array yang cocok dengan tipe `TNotification`.
    3.  Buat *Listeners* untuk setiap *Event* yang relevan untuk memicu pengiriman notifikasi.

-   **Fase 3: Berbagi Data Notifikasi dengan Frontend**
    1.  Modifikasi metode `share` di middleware `HandleInertiaRequests.php`.
    2.  Tambahkan logika untuk mengambil notifikasi pengguna, memetakannya ke format `TNotification`, dan membagikannya sebagai *prop* `notifications` dan `unreadNotificationsCount`.

-   **Fase 4: Implementasi "Mark as Read"**
    1.  Buat rute `POST /notifications/read` di `routes/web.php`.
    2.  Buat `NotificationController` dengan metode `markAsRead` untuk memproses permintaan dan mengarahkan kembali pengguna.

-   **Fase 5: Integrasi Frontend & Pembersihan**
    1.  Modifikasi komponen `notification-popover.tsx` untuk menggunakan data dari `usePage().props.notifications`.
    2.  Hapus data `mockNotifications`.
    3.  Implementasikan pemicu "Mark as Read" menggunakan komponen `<Link>` dari Inertia.

---

### 4. **Strategi Verifikasi**

-   **Pengujian Backend:** Gunakan `Notification::fake()` dalam *feature tests* untuk memverifikasi pengiriman notifikasi.
-   **Pengujian Fungsional:** Lakukan pengujian manual untuk setiap skenario untuk memastikan notifikasi muncul dengan benar di UI.

---

### ✅ Task Todo List

**Fase 1: Penyiapan Fondasi Notifikasi**
- [x] Jalankan `php artisan notifications:table` untuk membuat migrasi.
- [x] Jalankan `php artisan migrate` untuk membuat tabel `notifications`.
- [x] Tambahkan *trait* `Illuminate\Notifications\Notifiable` ke model `app/Models/User.php`.

**Fase 2: Implementasi Skenario Notifikasi (Backend - Panggilan Langsung)**
- [x] **Skenario 1: Kelas Baru untuk Guru**
    - [x] Buat kelas Notifikasi `NewClassroomAssignment`.
    - [x] Panggil notifikasi ini dari `app/Services/ClassroomService.php` dalam metode `createClassroom` untuk dikirim ke guru yang bersangkutan.
- [x] **Skenario 2: Konten Baru untuk Siswa**
    - [x] Buat kelas Notifikasi `NewContentAvailable`.
    - [x] Panggil notifikasi ini dari `app/Services/ContentService.php` dalam metode `createMaterial` dan `createQuiz`. Lakukan iterasi untuk mengirim ke semua siswa di kelas tersebut.
- [x] **Skenario 3: Siswa Menyelesaikan Konten**
    - [x] Buat kelas Notifikasi `ContentCompletedByStudent`.
    - [x] Panggil notifikasi ini dari `app/Http/Controllers/Student/ContentController.php` (di metode `show` untuk materi, dan `resultQuiz` untuk kuis) untuk dikirim ke guru pengampu.
- [x] **Skenario 4: Siswa Bergabung ke Kelas**
    - [x] Buat kelas Notifikasi `StudentJoinedClassroom`.
    - [x] Panggil notifikasi ini dari `app/Services/ClassroomService.php` dalam metode `joinClassroom` untuk dikirim ke guru dan semua admin.
- [x] **Skenario 5: Siswa Mendapatkan Pencapaian**
    - [x] Buat kelas Notifikasi `AchievementUnlockedNotification`.
    - [x] Panggil notifikasi ini dari `app/Services/AchievementService.php` dalam metode `award` untuk dikirim ke siswa yang bersangkutan.

**Fase 3: Berbagi Data Notifikasi dengan Frontend (Middleware)**
- [x] Modifikasi metode `share` di `app/Http/Middleware/HandleInertiaRequests.php`.
- [x] Tambahkan logika untuk mengambil notifikasi pengguna.
- [x] Petakan (map) hasil query notifikasi agar sesuai dengan format `TNotification` frontend.
- [x] Bagikan data notifikasi dan jumlah yang belum dibaca sebagai *prop* Inertia.

**Fase 4: Implementasi "Mark as Read"**
- [ ] Buat `NotificationController` dengan metode `markAsRead`.
- [ ] Tambahkan rute `POST /notifications/read` di `routes/web.php` yang menunjuk ke controller tersebut.

**Fase 5: Integrasi Frontend & Pembersihan**
- [ ] Modifikasi `notification-popover.tsx` untuk menggunakan *props* `notifications` dan `unreadNotificationsCount` dari `usePage()`.
- [ ] Hapus array `mockNotifications` dari `notification-popover.tsx`.
- [ ] Tambahkan komponen `<Link>` atau tombol yang memicu permintaan POST ke rute `notifications.read`.


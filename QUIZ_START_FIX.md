# Quiz Start Issue - Fixed

## Masalah yang Ditemukan:

1. **Logika inisialisasi salah**: `$this->submission->answers` bisa bernilai array kosong `[]` yang dianggap "truthy" dalam kondisi `if`, menyebabkan quiz dianggap sudah dimulai
2. **Duplikasi increment**: `correctAnswers` di-increment dua kali (di `saveAnswer()` dan `selectAnswer()`)
3. **Urutan inisialisasi salah**: `currentQuestion` diset sebelum `initializeSubmission()` dipanggil
4. **Database field default**: `answers` diset sebagai `[]` instead of `null` untuk quiz baru

## Perbaikan yang Dilakukan:

### 1. **StartQuiz.php**

- **Perbaikan kondisi inisialisasi**: Mengubah `if ($this->submission->answers)` menjadi `if ($this->submission->answers && count($this->submission->answers) > 0)`
- **Menghapus duplikasi increment**: Menghilangkan increment `correctAnswers` di `selectAnswer()`
- **Perbaikan urutan inisialisasi**: Memanggil `initializeSubmission()` sebelum mengakses `currentQuestion`
- **Database field default**: Mengubah default `answers` dari `[]` menjadi `null` untuk quiz baru

### 2. **Logika Completion Check**

- **Simplified check**: Hanya mengecek `is_completed` dan `completed_at` field, bukan `count(answers)`
- **Proper initialization**: Memastikan semua property diinisialisasi dengan benar

### 3. **Current Question Handling**

- **Fallback logic**: Menambahkan fallback untuk memastikan `currentQuestion` selalu ter-set
- **Proper index**: Memastikan `currentQuestionIndex` dimulai dari 0 untuk quiz baru

## Hasil:

- ✅ Quiz baru akan memulai dari pertanyaan pertama (index 0)
- ✅ Quiz yang sudah dimulai akan melanjutkan dari pertanyaan terakhir
- ✅ Tidak ada duplikasi increment pada `correctAnswers`
- ✅ Tidak ada langsung ke hasil untuk quiz baru

## Testing:

Silakan test dengan:

1. **Quiz baru**: Harus memulai dari pertanyaan pertama
2. **Quiz yang dilanjutkan**: Harus melanjutkan dari pertanyaan yang belum dijawab
3. **Quiz yang sudah selesai**: Harus menampilkan hasil

Quiz system sekarang sudah diperbaiki dan siap digunakan!

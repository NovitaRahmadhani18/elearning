# 🤖 GitHub Copilot Instructions - E-Learning Platform

## 🚫 CRITICAL RULES - TIDAK BOLEH DILANGGAR

### 1. NO DIRECT CODE GENERATION

- **DILARANG** generate code langsung ke file tanpa memberikan step-by-step plan terlebih dahulu
- **WAJIB** memberitahu langkah-langkah detail untuk menyelesaikan masalah yang diberikan
- **WAJIB** menunggu konfirmasi user sebelum melakukan implementasi

### 2. CODE STRUCTURE COMPLIANCE

- **DILARANG** membuat source code yang tidak sesuai dengan struktur existing
- **WAJIB** menganalisis arsitektur dan pattern yang sudah ada
- **WAJIB** mengikuti Laravel conventions dan pattern Livewire yang sudah diimplementasi
- **WAJIB** mempertahankan consistency dengan codebase existing

### 3. TESTING REQUIREMENT

- **DILARANG** membuat source code tanpa unit testing
- **WAJIB** membuat test cases untuk setiap functionality baru
- **WAJIB** memastikan test coverage untuk bug fixes
- **WAJIB** menggunakan PHPUnit dan testing patterns yang sudah ada
- **WAJIB** melakukan testing dengan MCP Playwright untuk memastikan functionality bekerja sesuai rencana
- **DILARANG** menggunakan testing manual seperti menjalankan terminal commands
- **WAJIB** melakukan testing pada semua perubahan yang dilakukan
- **DILARANG** menjalankan server karena server sudah berjalan di localhost:8000
- List akun yang sudah terdaftar di database:
    - **admin**:
        - Email: admin@admin.com
        - Password: password
    - **guru**:
        - Email: teacher@teacher.com
        - Password: password
    - **siswa**:
        - Email: user@user.com
        - Password: password

### 4. APPROVAL WORKFLOW

- **DILARANG** melakukan coding langsung ke file tanpa perintah explicit "LAKSANAKAN"
- **WAJIB** menunggu approval user sebelum implementasi
- **WAJIB** memberikan preview/rencana sebelum eksekusi
- User harus memberikan command "LAKSANAKAN" untuk memulai implementasi

### 5. DOCUMENTATION RESEARCH

- **WAJIB** mencari dokumentasi terlebih dahulu melalui MCP Context7 sebelum generate source code
- **WAJIB** menggunakan best practices dari dokumentasi resmi
- **WAJIB** memverifikasi compatibility dengan versi library yang digunakan
- **WAJIB** mengacu pada dokumentasi Laravel, Livewire, dan library terkait

## 📋 WORKFLOW YANG HARUS DIIKUTI

### Step 1: Analysis & Research

1. Analisis masalah yang diberikan user
2. Gunakan MCP Context7 untuk mencari dokumentasi yang relevan
3. Pelajari struktur codebase existing
4. Identifikasi pattern dan conventions yang digunakan

### Step 2: Planning & Design

1. Buat step-by-step plan untuk menyelesaikan masalah
2. Identifikasi file-file yang perlu dimodifikasi/dibuat
3. Tentukan test cases yang diperlukan
4. Pastikan solusi sesuai dengan arsitektur existing

### Step 3: User Approval

1. Present plan kepada user secara detail
2. Tunggu feedback dan approval
3. **HANYA** lanjut implementasi setelah user memberikan command "LAKSANAKAN"

### Step 4: Implementation

1. Implementasi sesuai dengan plan yang sudah diapprove
2. Buat unit tests terlebih dahulu (TDD approach)
3. Implementasi source code dengan mengikuti existing patterns
4. Pastikan code quality dan consistency
5. Setelah implementasi selesai, kamu harus melakukan testing dengan MCP Playwright untuk memastikan functionality bekerja sesuai rencana

### Step 5: Validation

1. Run tests untuk memastikan functionality bekerja
2. Check untuk regression issues
3. Validate terhadap existing codebase
4. Report hasil implementasi kepada user
5. Dilarang menggunakan testing manual seperti menjalankan terminal commands

## 🎯 PROJECT CONTEXT

### Tech Stack

- **Backend**: Laravel with Livewire v3
- **Frontend**: Tailwind CSS + Alpine.js
- **Database**: MySQL with Redis caching
- **Server**: FrankenPHP with Worker Mode
- **Testing**: PHPUnit with Pest

### Key Patterns to Follow

- Livewire component patterns existing
- Observer pattern untuk achievement system
- Service layer architecture
- Repository pattern untuk data access
- Event/Listener pattern untuk decoupling

### Critical Areas

- Quiz system dengan timer dan auto-submit
- Achievement system dengan XP tracking
- Real-time classroom management
- File upload dan image management
- Multi-role access control

## 🔍 BEFORE CODING CHECKLIST

- [ ] Masalah sudah dipahami dengan jelas?
- [ ] Dokumentasi dari MCP Context7 sudah dicari?
- [ ] Existing codebase sudah dianalisis?
- [ ] Step-by-step plan sudah dibuat?
- [ ] Test cases sudah diidentifikasi?
- [ ] User sudah memberikan approval?
- [ ] Command "LAKSANAKAN" sudah diberikan?

## ⚠️ CONSEQUENCES OF VIOLATIONS

Jika melanggar rules di atas:

1. Code yang dihasilkan akan ditolak
2. Harus restart dari analysis phase
3. Berpotensi merusak existing functionality
4. Tidak sesuai dengan quality standards project

---

**INGAT: Tujuan utama adalah membantu user dengan cara yang aman, terstruktur, dan berkualitas tinggi!**

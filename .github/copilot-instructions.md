# E-Learning Platform - AI Developer Guide

## RULES

Mulai sekarang saya akan menanyakanmu untuk melakukan generate code, JANGAN SAMPAI KAMU MENGEDIT CODE, sebelum kamu memberikan plan dan saya berikan perintah LAKSANAKAN,kamu tidak perlu menjelaskan dokumentasi terkait fitur yang sudah kamu buat di file readme, cukup jelaskan saja melalui chat secara singkat, dan pastikan kamu mengikuti struktur kode yang sudah ada. Kamu juga harus memastikan bahwa kode yang kamu hasilkan sesuai dengan standar pengembangan yang telah ditetapkan dalam proyek ini.

## IMPORTANT NOTE

Before you start working on any feature, please ensure you have the latest code from the repository and that you understand the existing architecture and patterns. This will help maintain consistency and avoid conflicts in the codebase. and use Context7 for latest documentation and updates. Use the latest documentation and updates available in Context7 to stay informed about any changes or new features. you doesn't need to generate documentation for codebase that you have generated. you must also ensure that you are familiar with the existing codebase and its structure to avoid unnecessary duplication or conflicts. and plase make code structure consistent with existing patterns. for styling frontend, you must be consistent with Tailwind CSS conventions and use the provided custom color palette and existing components. you also must using best practice for everything. and if you are not sure about something, please ask for clarification before proceeding. This will help ensure that your contributions align with the project's goals and standards.and make sure to follow the development patterns and conventions outlined in this document. This will help maintain consistency and quality across the codebase. don't generate long code, insetead make it short and simple, and if you need to generate long code, please break it down into smaller parts and explain each part clearly. This will make it easier for others to understand and review your code.

## Architecture Overview

This is a **Laravel-based e-learning platform** with role-based access control (Admin/Teacher/Student) built around **Livewire 3** for interactive components and **Tailwind CSS** for styling.

### Core Domain Models

- **Quiz System**: `Quiz` → `Question` → `QuestionOption` with image support
- **Classroom Management**: `Classroom` ↔ `User` (many-to-many via `ClassroomStudent`)
- **Content Delivery**: Polymorphic `Content` model linking quizzes/materials to classrooms
- **User Experience**: Level-up system with achievements, streaks, and experience points

### Key Dependencies

- **Livewire v3**: Real-time reactive components (`app/Livewire/`)
- **Spatie Laravel Permission**: Role-based access control (`RoleEnum.php`)
- **Level-Up**: Gamification system with XP and achievements
- **Trix Editor**: Rich text editing for content
- **Blade Icons**: Google Material Design icons (`gmdi-*`)

## Development Patterns

### 1. Livewire Component Architecture

Components use **database transactions** and **temporary file storage** patterns:

```php
// File uploads: temp/ → permanent storage on save
public function updatedQuestionImages($value, $key) {
    $path = $value->store('temp/questions', 'public');
    $this->questions[$key]['image_path'] = $path;
}

// Always wrap database operations in transactions
DB::transaction(function () {
    // Create quiz, questions, and options
    $this->moveImageToPermanentLocation($tempPath, 'questions');
});
```

### 2. Route Organization by Role

- `/admin/*` → Admin dashboard and classroom management
- `/teacher/*` → Quiz creation, material management
- `/user/*` → Student dashboard and quiz taking
- Livewire components handle create/edit forms: `CreateQuiz.php`, `EditQuiz.php`

### 3. Image Management Convention

```
storage/app/public/
├── quiz-images/
│   ├── questions/     # Question images
│   └── options/       # Option images
└── temp/
    ├── questions/     # Temporary during editing
    └── options/       # Cleaned up on save/cancel
```

## Development Workflow

### Setup & Testing

```bash
# Standard Laravel setup
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed && php artisan storage:link

# Development server
php artisan serve  # Backend
npm run dev       # Frontend (Vite)

# Testing with Pest
php artisan test
```

### Database Schema Patterns

- **Polymorphic Relations**: `Content` table uses `contentable_type/contentable_id`
- **Pivot Tables**: Custom pivot attributes (e.g., `progress` in `classroom_user`)
- **Image Storage**: Nullable `image_path` columns for file uploads
- **Soft Constraints**: Use `cascade` for critical relationships

### Frontend Standards

- **Tailwind CSS**: Custom color palette with `primary` (blue) and `secondary` (yellow)
- **Alpine.js**: For simple interactivity alongside Livewire
- **Blade Components**: Custom components in `app/View/Components/`
- **Icon System**: `<x-gmdi-*>` for Google Material Design icons

## Common Gotchas

1. **File Upload Cleanup**: Always clean temporary files in `cancel()` methods
2. **Role Middleware**: Routes use role-based middleware (currently commented out)
3. **Image Validation**: 2MB limit, handle both temp and permanent paths
4. **Livewire State**: Use `wire:model` for two-way binding, `wire:click` for actions
5. **Database Transactions**: Wrap multi-model operations for consistency

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

# E-Learning Platform

Laravel-based e-learning platform with role-based access control, quiz management, and image upload capabilities.

## Features

### Quiz Management with Image Support
- **Create/Edit Quizzes**: Full Livewire implementation for creating and editing quizzes
- **Dynamic Questions**: Add/remove questions with real-time validation
- **Dynamic Options**: Add/remove options per question (minimum 2 options)
- **Image Support**: Upload images for both questions and options
- **File Validation**: Images up to 2MB supported (jpg, png, gif, etc.)
- **Smart Image Management**: Temporary storage during editing, permanent storage on save
- **Image Cleanup**: Automatic deletion of unused images

### Classroom Management with Real-time Student Assignment
- **Real-time Student Management**: Livewire component for instant student assignment/removal
- **Auto-sync Checkboxes**: Students are added/removed immediately upon checkbox change
- **Search Functionality**: Real-time search through student names and emails
- **Visual Feedback**: Instant notifications for successful operations
- **Loading States**: Visual indicators during database operations
- **Error Handling**: Graceful error handling with user-friendly messages
- **Student Summary**: Live count and display of selected students

### Database Structure
- **Questions Table**: Stores question text and optional image path
- **Question Options Table**: Stores option text, correct answer flag, and optional image path
- **Classroom User Pivot**: Many-to-many relationship between classrooms and students
- **Image Storage**: Organized in `storage/app/public/quiz-images/` with subfolders for questions and options

### User Roles
- **Admin**: Full system access
- **Teacher**: Create/manage quizzes, materials, and view students
- **Student**: Access assigned courses and take quizzes

### Technical Implementation
- **Livewire v3**: Modern reactive components with real-time validation
- **File Uploads**: Using Livewire's `WithFileUploads` trait
- **Image Processing**: Automatic file handling with Laravel Storage
- **Database Transactions**: Ensure data consistency during quiz operations
- **Real-time Student Management**: Livewire component for instant classroom updates
- **Auto-sync Operations**: Database changes without page refresh
- **Responsive Design**: Mobile-friendly interface with Tailwind CSS

## Installation

1. Clone the repository
2. Install dependencies: `composer install && npm install`
3. Set up environment: `cp .env.example .env`
4. Generate key: `php artisan key:generate`
5. Run migrations: `php artisan migrate --seed`
6. Create storage link: `php artisan storage:link`
7. Start server: `php artisan serve`

## Usage

### Creating Quizzes with Images

1. Navigate to `/teacher/quizes/create`
2. Fill in quiz details (title, description, settings)
3. Add questions with optional images
4. Add options with optional images
5. Select correct answers using radio buttons
6. Save quiz - images are automatically processed and stored

### Managing Classroom Students (Real-time)

1. Navigate to `/admin/classroom/{id}/edit`
2. Use the "Manage Students" section on the right
3. Search for students using the search box
4. Check/uncheck students to instantly add/remove them
5. See real-time feedback with notifications
6. View selected students count and summary
7. No need to click "Update" - changes are saved immediately

### Image Management

- **Supported Formats**: JPG, PNG, GIF, WebP
- **File Size Limit**: 2MB per image
- **Storage Location**: `storage/app/public/quiz-images/`
- **Temporary Storage**: `storage/app/public/temp/` during editing
- **Automatic Cleanup**: Unused images are automatically deleted

### File Structure
```
storage/app/public/
├── quiz-images/
│   ├── questions/     # Question images
│   └── options/       # Option images
└── temp/
    ├── questions/     # Temporary question images
    └── options/       # Temporary option images
```

## Database Schema

### Questions Table
```sql
- id (primary key)
- question_text (text)
- image_path (string, nullable) # Path to question image
- quiz_id (foreign key)
- timestamps
```

### Question Options Table
```sql
- id (primary key)
- option_text (text)
- image_path (string, nullable) # Path to option image
- is_correct (boolean)
- question_id (foreign key)
- timestamps
```

## API Endpoints

### Quiz Management
- `GET /teacher/quizes` - List all quizzes
- `GET /teacher/quizes/create` - Create quiz form (Livewire)
- `GET /teacher/quizes/{quiz}/edit` - Edit quiz form (Livewire)
- `GET /teacher/quizes/{quiz}` - View quiz details
- `DELETE /teacher/quizes/{quiz}` - Delete quiz

### Classroom Management
- `GET /admin/classroom` - List all classrooms
- `GET /admin/classroom/create` - Create classroom form
- `GET /admin/classroom/{classroom}/edit` - Edit classroom form with Livewire student management
- `PUT /admin/classroom/{classroom}` - Update classroom details
- `PUT /admin/classrooms/{classroom}/students` - Sync students (legacy endpoint, now handled by Livewire)
- `DELETE /admin/classroom/{classroom}` - Delete classroom

### Livewire Components
- `CreateQuiz` - Full quiz creation with image upload
- `EditQuiz` - Full quiz editing with image upload  
- `ClassroomStudents` - Real-time student assignment management

### Image Handling
- Images are handled automatically through Livewire file uploads
- Temporary files are stored during editing
- Final images are moved to permanent location on save
- Unused images are cleaned up automatically

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests: `php artisan test`
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

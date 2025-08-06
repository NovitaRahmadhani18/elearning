# Achievement System Documentation

## Overview

The badge/achievement system allows users and students to earn badges by completing various challenges and milestones. It integrates with the existing level-up package for experience points and uses Laravel's observer pattern for automatic achievement checking.

## Available Achievements

### 1. Quiz Champion
- **Requirement**: Complete any quiz with score ≥ 85
- **Reward**: 50 XP
- **Icon**: Orange star with "85+" text

### 2. Fast Learner  
- **Requirement**: Complete any quiz in less than 10 minutes
- **Reward**: 30 XP
- **Icon**: Green star with checkmark

### 3. Perfect Score
- **Requirement**: Achieve 100% score on any quiz
- **Reward**: 100 XP
- **Icon**: Purple star with "100" text

### 4. Streak Master
- **Requirement**: Complete quizzes on 5 consecutive days
- **Reward**: 75 XP
- **Icon**: Orange star with "5D" text

### 5. Top Rank
- **Requirement**: Be in top 3 on the leaderboard (based on average quiz scores)
- **Reward**: 150 XP
- **Icon**: Blue star with "TOP" text

## System Architecture

### Core Components

1. **Achievement Classes** (`app/Achievements/`)
   - Each achievement extends `LevelUp\Experience\Contracts\Achievement`
   - Contains qualification logic and metadata

2. **AchievementService** (`app/Services/AchievementService.php`)
   - Centralized logic for checking and granting achievements
   - Handles experience point distribution
   - Manages achievement notifications

3. **QuizSubmissionObserver** (`app/Observers/QuizSubmissionObserver.php`)
   - Automatically triggers achievement checks when quizzes are completed
   - Registered in `AppServiceProvider`

4. **UserAchievements Livewire Component** (`app/Livewire/UserAchievements.php`)
   - Interactive achievement display page
   - Shows progress, statistics, and unlocked badges

### Database Structure

The system uses the existing level-up package tables:
- `achievements` - Achievement definitions
- `achievement_user` - User achievement progress and unlocks

## Usage

### Viewing Achievements
Users can view their achievements at `/user/lencana` which displays:
- Total achievements unlocked
- Experience points earned
- Completion percentage
- Individual achievement cards with status

### Automatic Achievement Checking
Achievements are automatically checked when:
- A quiz submission is created (completed)
- A quiz submission is updated to completed status

### Manual Achievement Checking
Use the console command for testing or retroactive awarding:

```bash
# Check achievements for specific user
php artisan achievements:check 1

# Check achievements for all users
php artisan achievements:check --all
```

### Achievement Notifications
When a user earns an achievement:
- A flash message is stored in the session
- A toast notification appears on the next page load
- The notification shows achievement name and XP earned

## Extending the System

### Adding New Achievements

1. **Create Achievement Class**
```php
<?php

namespace App\Achievements;

use LevelUp\Experience\Contracts\Achievement;

class NewAchievement extends Achievement
{
    public string $name = 'Achievement Name';
    public string $description = 'Achievement description';
    public string $image = '/images/achievements/new-achievement.svg';
    public bool $secret = false;
    public int $points = 25;

    public function qualifier(object $user): bool
    {
        // Add your qualification logic here
        return $user->someCondition();
    }
}
```

2. **Register in AchievementService**
Add the class to the `$achievementClasses` array in `AchievementService.php`.

3. **Add to Seeder**
Add the achievement data to `AchievementSeeder.php`.

4. **Create Icon**
Add an SVG icon to `public/images/achievements/`.

### Achievement Ideas for Future

- **Quiz Streak**: Complete 10 quizzes without failing
- **Early Bird**: Complete quiz within first hour of release
- **Night Owl**: Complete quiz after 10 PM
- **Perfectionist**: Get 100% on 5 different quizzes
- **Speed Runner**: Complete quiz in under 5 minutes
- **Classroom Hero**: Complete all content in a classroom
- **Social Learner**: Join 3 different classrooms

### Customizing Experience Points

Modify the `$points` property in achievement classes to adjust XP rewards.

### Adding Progress Tracking

For achievements that require progress tracking (like "Complete 10 quizzes"):

1. Update the achievement's `qualifier` method to calculate progress
2. Use the pivot table's `progress` field (0-100)
3. Update `AchievementService::grantAchievement()` to handle partial progress

## Testing

Run the achievement tests:
```bash
php artisan test tests/Feature/AchievementTest.php
```

The test suite covers:
- Achievement qualification logic
- Proper XP distribution
- Prevention of duplicate awards
- Edge cases and error handling

## Performance Considerations

- Achievement checking is triggered by quiz completion (low frequency)
- Database queries are optimized with proper indexing
- Leaderboard calculations are cached where possible
- Achievement checking is wrapped in try-catch for error resilience

## Troubleshooting

### Achievements Not Being Awarded
1. Check if the QuizSubmissionObserver is registered in AppServiceProvider
2. Verify the achievement qualification logic
3. Check logs for any errors during achievement checking
4. Ensure the achievement exists in the database (run the seeder)

### Missing Achievement Icons
1. Verify SVG files exist in `public/images/achievements/`
2. Check file permissions
3. Ensure proper path configuration in achievement classes

### Incorrect Experience Points
1. Verify the level-up package is configured correctly
2. Check if the User model uses the `GiveExperience` trait
3. Ensure the achievement's `$points` property is set correctly
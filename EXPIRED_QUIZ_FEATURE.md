# Auto-Complete Expired Quiz Feature

## Overview

This feature automatically completes expired quizzes to prevent content blocking when quiz deadlines have passed.

## Key Components

### 1. ExpiredQuizService (`app/Services/ExpiredQuizService.php`)

- **Purpose**: Centralized service to handle expired quiz auto-completion
- **Key Methods**:
    - `autoCompleteExpiredQuiz()`: Auto-completes a specific expired quiz for a user
    - `handleExpiredQuizContent()`: Handles expired quiz content and marks it as completed
    - `autoCompleteExpiredQuizzesInClassroom()`: Bulk auto-complete expired quizzes in a classroom

### 2. Enhanced CheckPrevContent Middleware (`app/Http/Middleware/CheckPrevContent.php`)

- **Purpose**: Prevents users from being blocked by expired quizzes
- **Enhancement**: Now checks for expired quizzes and auto-completes them when needed
- **Logic**: If previous content is not completed and it's an expired quiz, auto-complete it

### 3. Updated ClassroomController (`app/Http/Controllers/User/ClassroomController.php`)

- **Enhancement**: Auto-completes expired quizzes when user accesses classroom
- **User Feedback**: Shows notification when expired quizzes are auto-completed
- **Benefit**: Ensures smooth user experience without manual intervention

### 4. Enhanced StartQuiz Component (`app/Livewire/StartQuiz.php`)

- **Enhancement**: Auto-completes expired quiz when user tries to start it
- **User Feedback**: Redirects with informative message about auto-completion
- **Benefit**: Prevents users from attempting to start expired quizzes

### 5. Improved UI Display (`resources/views/pages/user/classroom/show.blade.php`)

- **Enhancement**: Visual indicators for expired quizzes
- **Status Badges**:
    - "Expired" for quizzes past deadline
    - "Missed" for expired quizzes without submission
- **Visual Cues**: Red text for expired due dates

## How It Works

1. **When User Accesses Classroom**:

    - System checks for expired quizzes in the classroom
    - Auto-completes them with 0 score if no submission exists
    - Shows notification to user about auto-completed quizzes

2. **When User Navigates Through Content**:

    - Middleware checks if previous content is completed
    - If previous content is expired quiz, auto-completes it
    - User can proceed to next content without being blocked

3. **When User Tries to Start Expired Quiz**:
    - StartQuiz component detects expired quiz
    - Auto-completes the quiz
    - Redirects user with informative message

## Auto-Completion Logic

### For Quizzes Without Submission:

```php
// Creates new submission with 0 score
$submission = [
    'user_id' => $userId,
    'started_at' => now(),
    'completed_at' => now(),
    'score' => 0,
    'total_questions' => $quiz->questions()->count(),
    'correct_answers' => 0,
    'time_spent' => 0,
    'is_completed' => true,
    'answers' => []
];
```

### For Quizzes With Incomplete Submission:

```php
// Completes existing submission with current progress
$existingSubmission->update([
    'completed_at' => now(),
    'is_completed' => true,
    'score' => $existingSubmission->score ?: 0
]);
```

## User Experience Benefits

1. **No Content Blocking**: Users can access content even if they missed quiz deadlines
2. **Clear Visual Feedback**: Expired quizzes are clearly marked with status badges
3. **Informative Notifications**: Users are informed when quizzes are auto-completed
4. **Seamless Navigation**: Content flow continues without manual intervention
5. **Progress Preservation**: Partial quiz attempts are preserved and completed

## Technical Features

- **Logging**: All auto-completion activities are logged for audit purposes
- **Error Handling**: Comprehensive error handling with fallback mechanisms
- **Performance**: Minimal performance impact with efficient database queries
- **Consistency**: Centralized logic ensures consistent behavior across components

## Configuration

No additional configuration required. The feature works automatically based on quiz `due_time` settings.

## Timer Bug Fix (Refresh Issue)

### Problem

The quiz timer was resetting or increasing when the page was refreshed, causing incorrect time calculations.

### Root Cause

1. **Client-server time desynchronization**: Timer was initialized from server but not properly synchronized on page refresh
2. **No validation**: Missing validation to prevent timer manipulation
3. **Client-side timer reset**: Alpine.js timer was reset without considering elapsed time

### Solution Implemented

1. **Enhanced Timer Validation**:

    - Added `validateTimerIntegrity()` method to check for timer manipulation
    - Server-side validation before processing any quiz actions
    - Logging of timer discrepancies for audit purposes

2. **Improved Client-Side Timer**:

    - Proper time synchronization on page load/refresh
    - Periodic server sync every 30 seconds
    - Page visibility change detection for timer sync
    - Prevention of local storage timer manipulation

3. **Robust Server-Side Logic**:
    - `refreshTimer()` method for on-demand time validation
    - Additional validation in `initializeSubmission()`
    - Timer expiration checks at multiple points

### Code Changes

```php
// New validation method
public function validateTimerIntegrity()
{
    if ($this->quiz->time_limit > 0 && $this->serverStartTime) {
        $actualElapsedSeconds = now()->diffInSeconds($this->serverStartTime);
        $actualTimeRemaining = max(0, (int)($this->quiz->time_limit - $actualElapsedSeconds));

        if (abs($this->timeRemaining - $actualTimeRemaining) > 5) {
            $this->timeRemaining = $actualTimeRemaining;
            // Log potential manipulation
        }
    }
    return true;
}

// Enhanced timer refresh
public function refreshTimer()
{
    $this->calculateTimeRemaining();
    return $this->timeRemaining;
}
```

### Security Enhancements

- Prevention of client-side timer manipulation
- Clearing of localStorage/sessionStorage timer data
- Server-side time validation on every critical action
- Audit logging of timer discrepancies

### User Experience

- Seamless timer operation across page refreshes
- Accurate time display regardless of network conditions
- No more timer "jumping" or incorrect time calculations

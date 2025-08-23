# Strategic Development Plan: E-Learning Platform Enhancements

This document outlines the strategic plans for implementing new features and improving the robustness of the e-learning platform, based on discussions and analyses.

## 1. Sequential Classroom Content

This section outlines the strategy for creating a student-facing classroom detail page that displays content in a sequential, unlockable order.

### 1.1. Objective

Create a classroom detail page where students see a full list of the classroom's content (e.g., materials, quizzes). The content must be displayed in a specific order, and each item must be marked with a status for the student:

*   **Completed:** The student has finished this content.
*   **Unlocked:** The content is available for the student to access.
*   **Locked:** The content cannot be accessed until the preceding content is completed.

### 1.2. Strategic Approach

The strategy is to centralize the complex status-calculation logic in a dedicated backend service. This service will determine the status of each content item for a given student. The result will be added to the API response, allowing the frontend to simply render the UI based on the provided status.

### 1.3. Implementation Plan

**Phase 1: Backend Logic Implementation**

1.  **Create `ContentStatusService`:**
    *   Generate a new service class at `app/Services/ContentStatusService.php`. This class will encapsulate all the logic for determining content statuses.
2.  **Implement Status Calculation Logic:**
    *   The service will be designed to be initialized with a student's progress for a given classroom.
    *   **In the Controller (e.g., `ClassroomController@show`):**
        1.  Fetch all `Content` for the classroom, ensuring they are ordered by the existing `order` column.
        2.  Fetch all of the student's completed content IDs for that classroom from the `content_student` pivot table.
        3.  Instantiate the `ContentStatusService` with the set of completed content IDs.
    *   **In the Service:** The service will provide a method to process the ordered list of content and determine the status of each item based on these rules:
        *   A content item's status is **`Completed`** if its ID is in the student's completed set.
        *   A content item's status is **`Unlocked`** if it is not `Completed` AND either (a) it is the first item in the sequence, or (b) the preceding item in the sequence is `Completed`.
        *   Otherwise, the status is **`Locked`**.

**Phase 2: API & Frontend Integration**

1.  **Enhance API Response:**
    *   Modify `app/Http/Resources/ContentResource.php` to include a `status` attribute.
    *   This attribute will be populated by calling the `ContentStatusService` for each content item as it's being serialized. The controller will pass the initialized service to the resource collection.
2.  **Implement Frontend UI:**
    *   The React component for the classroom detail page will receive the list of content objects, each with its `status`.
    *   The component will use this `status` to conditionally render the UI:
        *   Display a visual indicator (e.g., lock icon, checkmark icon).
        *   Apply CSS classes to change the appearance (e.g., grey-out locked items).
        *   Enable or disable the link to the content.

### 1.4. Verification Strategy

*   **Unit Test:** Test the `ContentStatusService` with mock data to verify the status logic under various conditions (e.g., no progress, partial progress, full completion).
*   **Feature Test:** Test the classroom detail API endpoint to ensure the JSON response contains the correct `status` field for each content item based on a simulated student's progress.
*   **Manual QA:** Log in as a student, complete a content item, and revisit the classroom page to confirm the next item becomes unlocked.

## 2. Database Design for New Features

This section details the proposed database schema for tracking student points, quiz submissions, and achievements.

### 2.1. Student Points System

*   **Objective:** Provide a flexible and auditable system for tracking points earned by students.
*   **Proposed Table:** `student_points`
    *   `id` (Primary Key)
    *   `user_id` (Foreign Key to `users.id`)
    *   `points_earned` (Integer)
    *   `sourceable_id` (Morphs ID, e.g., the ID of the quiz submission or achievement)
    *   `sourceable_type` (Morphs Type, e.g., `App\Models\QuizSubmission` or `App\Models\Achievement`)
    *   `description` (String, e.g., "Completed 'Algebra Basics' quiz")
    *   `created_at`, `updated_at` (Timestamps)
*   **Modification to `users` Table:** Add a `total_points` column for quick retrieval of a user's current point balance. This will be a denormalized value updated by an observer or service.
    *   `total_points` (Integer, default 0)

### 2.2. Quiz Submission Tracking

*   **Objective:** Capture detailed information about each student's quiz attempt, including answers and time taken.
*   **Proposed Table:** `quiz_submissions`
    *   `id` (Primary Key)
    *   `quiz_id` (Foreign Key to `quizzes.id`)
    *   `student_id` (Foreign Key to `users.id`)
    *   `score` (Integer, calculated upon submission)
    *   `started_at` (Timestamp)
    *   `completed_at` (Timestamp, nullable until finished)
    *   `duration_seconds` (Integer, calculated from timestamps)
    *   `created_at`, `updated_at` (Timestamps)
*   **Proposed Table:** `submission_answers`
    *   `id` (Primary Key)
    *   `quiz_submission_id` (Foreign Key to `quiz_submissions.id`)
    *   `question_id` (Foreign Key to `questions.id`)
    *   `answer_id` (Foreign Key to `answers.id` - the answer chosen by the student)
    *   `is_correct` (Boolean, stored at time of submission for historical accuracy)
    *   `created_at`, `updated_at` (Timestamps)

### 2.3. Achievement System

*   **Objective:** Track and reward students for unlocking specific achievements.
*   **Proposed Table:** `achievements`
    *   `id` (Primary Key)
    *   `slug` (String, unique, e.g., `perfect-score`)
    *   `name` (String, e.g., "Perfect Score")
    *   `description` (Text)
    *   `icon_path` (String, path to the image)
    *   `points_reward` (Integer, points awarded for unlocking)
    *   `created_at`, `updated_at` (Timestamps)
*   **Proposed Table:** `user_achievements` (Pivot Table)
    *   `id` (Primary Key)
    *   `user_id` (Foreign Key to `users.id`)
    *   `achievement_id` (Foreign Key to `achievements.id`)
    *   `unlocked_at` (Timestamp)
    *   `created_at`, `updated_at` (Timestamps)
    *   Unique constraint on `(user_id, achievement_id)`

## 3. Leaderboard Strategy

This section details the approach for creating leaderboards for different content types.

### 3.1. Quiz Leaderboard

*   **Objective:** Rank students based on their performance (score and time) on a specific quiz.
*   **Strategy:** No new table is required. The leaderboard will be **dynamically generated** from the `quiz_submissions` table.
*   **Ranking Logic:** For a given `quiz_id`, students will be ranked by `MAX(score)` (highest score) descending, then by `MIN(duration_seconds)` (fastest time) ascending as a tie-breaker.
*   **Conceptual Query:**
    ```sql
    SELECT
        qs.student_id,
        u.name,
        u.avatar,
        MAX(qs.score) AS best_score,
        MIN(qs.duration_seconds) AS best_time
    FROM
        quiz_submissions qs
    JOIN
        users u ON qs.student_id = u.id
    WHERE
        qs.quiz_id = [SPECIFIC_QUIZ_ID]
    GROUP BY
        qs.student_id, u.name, u.avatar
    ORDER BY
        best_score DESC, best_time ASC
    LIMIT 100;
    ```
*   **Performance:** Proper indexing on `quiz_submissions` (`quiz_id`, `score`, `duration_seconds`) is crucial. Caching mechanisms (e.g., Redis) can be implemented for high-traffic leaderboards.

### 3.2. Material Leaderboard (Revised)

*   **Objective:** Rank students based on who is the fastest to open a specific material after it becomes available.
*   **Strategy:** The leaderboard will be **dynamically generated** from the `content_student` table.
*   **Ranking Logic:** For a given `content_id` (of type material), students will be ranked by the `created_at` timestamp in the `content_student` table in ascending order.
*   **Conceptual Query:**
    ```sql
    SELECT
        cs.student_id,
        u.name,
        u.avatar,
        cs.created_at AS access_time
    FROM
        content_student cs
    JOIN
        users u ON cs.student_id = u.id
    WHERE
        cs.content_id = [SPECIFIC_MATERIAL_CONTENT_ID]
    ORDER BY
        access_time ASC
    LIMIT 100;
    ```
*   **Point Awarding:** When a `content_student` record is created, an event listener will award points to the student based on the `points` column of the associated `Content` model. These points contribute to the student's `total_points`.

## 4. Robust Quiz-Taking Mechanism

This section outlines the strategy for creating a resilient and secure quiz-taking experience, handling various interruptions and time limits.

### 4.1. Core Principles & State Management

*   **Server as Source of Truth:** The server is the ultimate authority for quiz state, time, and scoring. Client-side data is considered untrusted.
*   **Quiz Session State:** Each quiz attempt is a "session" represented by a `quiz_submission` record on the server.
*   **Client-Side Persistence:** `localStorage` will be used to persist the `submission_id` and current answers on the client for recovery from refreshes/crashes.

### 4.2. Handling Quiz Session Phases

*   **Starting the Quiz:**
    *   Client `POST` to `api/quizzes/{quiz}/submissions`.
    *   Server creates `quiz_submission` (with `started_at` timestamp), returns submission ID and questions.
    *   Client stores `submission_id` in `localStorage`, initiates fullscreen, and starts client-side timer.
*   **Answering Questions & Autosaving:**
    *   Client updates local state (answers) in `localStorage`.
    *   Client periodically (`PATCH` every 30-60s) sends current state to `api/quiz-submissions/{submission}` for server-side persistence (`submission_answers` table).
*   **Finishing the Quiz:**
    *   Triggered by client-side timer expiry or manual "Finish" button.
    *   Client `POST` to `api/quiz-submissions/{submission}/complete`.
    *   Server calculates final `score`, sets `completed_at`, `duration_seconds`, awards points/achievements.
    *   Server validates submission time against `started_at` and quiz `duration`.
    *   Client clears `localStorage` and redirects to results.

### 4.3. Interruption Handling

*   **Internet Loss:**
    *   Client listens for `navigator.onLine` events.
    *   If offline, display warning; quiz continues using local state.
    *   On reconnect, trigger immediate autosave.
*   **Page Refresh/Browser Crash:**
    *   Client checks `localStorage` for `submission_id` on load.
    *   If found, fetches latest state from server (`GET api/quiz-submissions/{submission}`) to resume.
*   **Page/Tab Inactivity:**
    *   Client listens for `document.visibilitychange`.
    *   Server-authoritative timer continues regardless of client activity.

### 4.4. Time Management (Server-Authoritative Timer)

*   **Principle:** Remaining time is **always recalculated**, never stored.
*   **Key Data:** `quiz.duration` (static) and `quiz_submission.started_at` (immutable, set by server).
*   **Calculation:**
    *   `deadline = started_at + duration`.
    *   Client-side timer continuously calculates `remaining_time = deadline - current_time`.
*   **Resilience:** When page refreshes, the client re-fetches the original `started_at` from the server and restarts the countdown from the correct remaining time.

### 4.5. Fullscreen Behavior

*   **Auto-Start:** On initial quiz load (triggered by user's "Start Quiz" click), the application will automatically request fullscreen.
*   **User Toggle:** A UI button will be provided to allow users to manually enter/exit fullscreen.
    *   Listens to `fullscreenchange` event to keep UI state synchronized.
    *   Calls `document.documentElement.requestFullscreen()` or `document.exitFullscreen()`.

## 5. API Routes & Authentication Strategy

This section clarifies the routing and authentication approach for the interactive quiz features.

### 5.1. Rationale for Hybrid Routing

*   **Inertia for Page Loads:** `routes/web.php` will be used for initial page loads and full page navigations (e.g., `GET /classrooms/{classroom}/quizzes/{quiz}` to load the quiz component shell).
*   **API for Dynamic Interactions:** `routes/api.php` will be used for small, frequent, background interactions within the already-loaded quiz component (e.g., starting a session, autosaving answers, completing the quiz). This provides:
    *   **Efficiency:** Lighter responses for background tasks.
    *   **Separation of Concerns:** Clear distinction between page serving and data operations.
    *   **SPA-like Behavior:** The quiz component behaves like a mini-SPA once loaded, naturally communicating with API endpoints.

### 5.2. Authentication for API Routes

*   **Solution:** **Laravel Sanctum** will be used for SPA authentication.
*   **Mechanism:**
    1.  **Sanctum Installation & Configuration:** Install Sanctum, publish assets, run migrations.
    2.  **Middleware:** Add `\Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class` to the `api` middleware group in `app/Http/Kernel.php`.
    3.  **CORS & Stateful Domains:** Configure `config/sanctum.php` to include the frontend domain in `stateful` domains.
    4.  **User Model Trait:** Add `HasApiTokens` trait to `App\Models\User`.
    5.  **Route Protection:** Protect quiz API routes with `auth:sanctum` middleware in `routes/api.php`.
    6.  **Frontend Setup:** Ensure `axios` is configured to send credentials (`withCredentials: true`) and that the initial CSRF cookie is set (e.g., via `resources/js/bootstrap.js` calling `/sanctum/csrf-cookie`).
*   **Outcome:** Sanctum will leverage the existing web session cookie to authenticate requests to `/api` routes, providing seamless and secure authentication without requiring separate API tokens.

## 6. Verification Strategies (General)

*   **Backend Feature Tests:** Comprehensive tests for all new API endpoints, business logic (scoring, point awarding, achievement checking), and server-side time validation.
*   **Frontend Component Tests:** Unit tests for React components' state management, timer logic, and UI interactions.
*   **End-to-End (E2E) Tests:** Automated tests simulating full user flows, including interruptions (e.g., page reload mid-quiz).
*   **Manual QA:** Critical for testing all edge cases and user experience aspects, especially for interruption handling and fullscreen behavior.

## 7. Risk Assessment & Considerations (General)

*   **Client-Side Manipulation:** Mitigated by server-authoritative logic for time, scoring, and state.
*   **Browser API Limitations:** Fullscreen API requires user interaction to initiate; `beforeunload` has limited customizability. These are UX enhancements, not security measures.
*   **Data Volume:** Proper indexing and caching strategies are essential for performance as `quiz_submissions`, `submission_answers`, and `student_points` tables grow.
*   **Complexity:** New service layers and event listeners will be introduced to manage the complex logic, ensuring maintainability.
*   **Incentive Structure (Material Leaderboard):** The "fastest to open" metric for materials explicitly rewards speed, which may not align with desired learning behaviors. This is a deliberate choice based on user requirements.


## 8. Implementation Tracking Checklist

### Foundational Setup
- [x] Implement Role-Based Access Control (RBAC) Middleware & Gates
- [ ] Install and configure Laravel Sanctum for API authentication
- [ ] Configure frontend HTTP client (`axios`) for Sanctum (CSRF, credentials)

### Sequential Classroom Content (Section 1)
- [x] Create `ContentStatusService` for status calculation
- [x] Implement logic in the service to determine Locked/Unlocked/Completed status
- [x] Integrate service into the `ClassroomController` and `ContentResource`
- [x] Implement frontend UI to display content status and control access

### Database & Models (Section 2)
- [x] **Student Points:** Create `student_points` migration and model
- [x] **User Points:** Add `total_points` to `users` table migration
- [x] **Quiz Submissions:** Create `quiz_submissions` migration and model
- [x] **Submission Answers:** Create `submission_answers` migration and model
- [x] **Achievements:** Create `achievements` migration and model
- [x] **User Achievements:** Create `user_achievements` migration and model

### Leaderboard Features (Section 3)
- [ ] Implement Quiz Leaderboard dynamic query logic
- [ ] Implement Material Leaderboard dynamic query logic
- [x] Create event listener to award points for material access

### Quiz-Taking Mechanism (Section 4 & 5)
- [x] **API:** Create route to **start** a quiz session (`POST /api/quizzes/{quiz}/submissions`)
- [x] **API:** Create route to **autosave** quiz progress (`PATCH /api/quiz-submissions/{submission}`)
- [x] **API:** Create route to **complete** a quiz session (`POST /api/quiz-submissions/{submission}/complete`)
- [x] **API:** Create route to **resume** a quiz session (`GET /api/quiz-submissions/{submission}`)
- [x] **Frontend:** Implement client-side state management with `localStorage`
- [x] **Frontend:** Implement interruption handling (refresh, offline)
- [x] **Frontend:** Implement server-authoritative timer logic
- [x] **Frontend:** Implement fullscreen toggle behavior

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AchievementService;
use App\Models\User;

class CheckAchievements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'achievements:check {user_id?} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and award achievements for a specific user or all users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $achievementService = app(AchievementService::class);

        if ($this->option('all')) {
            $this->info('Checking achievements for all users...');
            $achievementService->checkAllUsersAchievements();
            $this->info('Achievement check completed for all users.');
            return;
        }

        $userId = $this->argument('user_id');
        
        if (!$userId) {
            // If no user ID provided, ask for one
            $userId = $this->ask('Enter user ID to check achievements for:');
        }

        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return;
        }

        $this->info("Checking achievements for user: {$user->name} (ID: {$user->id})");
        
        $newAchievements = $achievementService->checkAchievementsForUser($user);
        
        if (empty($newAchievements)) {
            $this->info('No new achievements awarded.');
        } else {
            $this->info('New achievements awarded:');
            foreach ($newAchievements as $achievement) {
                $this->line("  - {$achievement}");
            }
        }

        // Show current achievement status
        $userAchievements = $achievementService->getUserAchievements($user);
        $unlockedCount = count(array_filter($userAchievements, fn($a) => $a['unlocked']));
        $totalCount = count($userAchievements);
        
        $this->info("Current status: {$unlockedCount}/{$totalCount} achievements unlocked");
    }
}
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\SendAbandonedCartEmails::class,
        Commands\SendBirthdayDiscounts::class,
        Commands\SendLowStockAlerts::class,
        Commands\GenerateSalesReports::class,
        Commands\CleanupExpiredCarts::class,
        Commands\ProcessReorderReminders::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Send abandoned cart emails every hour
        $schedule->command('emails:abandoned-cart')
            ->hourly()
            ->between('9:00', '21:00'); // Only during business hours

        // Send birthday discounts daily at 9 AM
        $schedule->command('emails:birthday-discounts')
            ->dailyAt('09:00');

        // Check for low stock and send alerts daily at 8 AM
        $schedule->command('inventory:low-stock-alerts')
            ->dailyAt('08:00');

        // Generate daily sales reports at midnight
        $schedule->command('reports:daily-sales')
            ->dailyAt('00:30');

        // Generate weekly sales reports on Mondays at 1 AM
        $schedule->command('reports:weekly-sales')
            ->weeklyOn(1, '01:00');

        // Generate monthly sales reports on the 1st of each month
        $schedule->command('reports:monthly-sales')
            ->monthlyOn(1, '02:00');

        // Clean up expired carts every 6 hours
        $schedule->command('cart:cleanup')
            ->everySixHours();

        // Send reorder reminders weekly on Wednesdays
        $schedule->command('emails:reorder-reminders')
            ->weeklyOn(3, '10:00');

        // Clean up old analytics data monthly
        $schedule->command('analytics:cleanup')
            ->monthlyOn(15, '03:00');

        // Backup database daily at 2 AM
        $schedule->command('backup:database')
            ->dailyAt('02:00')
            ->sendOutputTo(storage_path('logs/backup.log'));
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

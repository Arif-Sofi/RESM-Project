<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateEventStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status of past events to COMPLETED';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = \App\Models\Event::where('end_at', '<', now())
            ->where('status', '!=', 'COMPLETED')
            ->update(['status' => 'COMPLETED']);

        $this->info("Updated {$count} past events to COMPLETED.");
    }
}

<?php

namespace App\Console\Commands;

use App\Mail\EventReminderNotification;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEventReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-event-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails for upcoming events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $events = Event::whereNull('reminder_sent_at')
            ->whereBetween('start_at', [$now->copy()->addMinutes(59), $now->copy()->addMinutes(61)])
            ->get();

        foreach ($events as $event) {
            // イベントの作成者に送信
            if ($event->creator) {
                Mail::to($event->creator->email)->queue(new EventReminderNotification($event));
            }

            // イベントの参加者に送信
            foreach ($event->staff as $staffMember) {
                Mail::to($staffMember->email)->queue(new EventReminderNotification($event));
            }

            $event->reminder_sent_at = $now;
            $event->save();

            $this->info("Sent reminder for event: {$event->name}");
        }

        $this->info('Event reminders sent successfully.');
    }
}

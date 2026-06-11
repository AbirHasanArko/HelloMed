<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\NotificationLog;
use App\Support\NotificationService;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';

    protected $description = 'Send appointment reminder notifications and keep delivery logs';

    public function handle(): int
    {
        $sentCount = 0;

        $sentCount += $this->sendWindowReminders(24, 20);
        $sentCount += $this->sendWindowReminders(1, 20);

        $this->info("Appointment reminders processed. Sent: {$sentCount}");

        return self::SUCCESS;
    }

    private function sendWindowReminders(int $hoursAhead, int $windowMinutes): int
    {
        $start = now()->addHours($hoursAhead);
        $end = (clone $start)->addMinutes($windowMinutes);

        $appointments = Appointment::query()
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('scheduled_for', [$start, $end])
            ->get();

        $sent = 0;

        foreach ($appointments as $appointment) {
            $eventKey = 'appointment.reminder.'.$hoursAhead.'h.'.$appointment->id;
            $alreadySent = NotificationLog::query()
                ->where('event_key', $eventKey)
                ->where('status', 'sent')
                ->exists();

            if ($alreadySent) {
                continue;
            }

            NotificationService::sendEmail(
                $appointment->patient_email,
                'HelloMed Appointment Reminder',
                "Reminder: You have an appointment with {$appointment->doctor?->name} on {$appointment->scheduled_for?->format('M d, Y h:i A')}.",
                $eventKey,
                $appointment->user,
                $appointment,
                ['hours_ahead' => $hoursAhead]
            );

            $sent++;
        }

        return $sent;
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ScheduleActivityNotification extends Notification
{
    use Queueable;

    public $schedule;
    public $user;
    public $action; // 'ditambahkan' atau 'diperbarui'

    public function __construct($schedule, $user, $action)
    {
        $this->schedule = $schedule;
        $this->user = $user;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Jadwal Mengajar ' . ucfirst($this->action),
            'message' => $this->user->name . ' telah ' . $this->action . ' jadwal mengajar untuk ' . 
                         $this->schedule->instructor->name . 
                         ' pada hari ' . ucwords($this->schedule->day_of_week),
            'url' => route('admin.instructors.schedule', $this->schedule->instructor),
            'icon' => 'calendar',
        ];
    }
}
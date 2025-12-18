<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class InstructorActivityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $instructor;
    public $user;
    public $action; // 'ditambahkan' atau 'diperbarui'

    public function __construct($instructor, $user, $action)
    {
        $this->instructor = $instructor;
        $this->user = $user;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; // Simpan ke DB dan real-time (opsional)
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Data Instruktur ' . ucfirst($this->action),
            'message' => $this->user->name . ' telah ' . $this->action . ' data instruktur: ' . $this->instructor->name,
            'url' => route('admin.instructors.show', $this->instructor),
            'icon' => 'user-plus', // atau user-edit
        ];
    }
}
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralActivityNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $model;
    public $user;
    public $menu;
    public $action;

    public function __construct($model, $user, $menu, $action)
    {
        $this->model = $model;
        $this->user = $user;
        $this->menu = $menu;
        $this->action = $action;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast']; // Simpan ke database + real-time (opsional)
    }

    public function toArray($notifiable)
    {
        // Ambil nama data (name, code, atau fallback)
        $name = $this->model->name ?? $this->model->code ?? $this->model->title ?? 'Data';

        return [
            'title'   => "$this->menu " . ucfirst($this->action),
            'message' => $this->user->name . " telah {$this->action} {$this->menu}: {$name}",
            'url'     => $this->getUrl(),
            'icon'    => 'document-text',
        ];
    }

    private function getUrl()
    {
        // Sesuaikan route detail masing-masing model
        if ($this->model instanceof \App\Models\MasterProgram) {
            return route('admin.programs.master.show', $this->model);
        }

        if ($this->model instanceof \App\Models\Program) {
            // Kalau ada route show untuk Program, tambahkan di sini
            // return route('admin.programs.show', $this->model);
            return route('admin.programs.index');
        }

        if ($this->model instanceof \App\Models\CompetencyUnit) {
            return route('admin.programs.units.show', $this->model);
        }

        return route('admin.dashboard');
    }
}
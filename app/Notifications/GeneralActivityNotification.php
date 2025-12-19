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
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        $name = 'Data';

        if ($this->model instanceof \App\Models\User) {
            $name = $this->model->name;
        } elseif ($this->model instanceof \App\Models\Participant) {
            $name = $this->model->user?->name ?? 'Peserta';
        } elseif ($this->model instanceof \App\Models\Instructor) {
            $name = $this->model->user?->name ?? 'Instruktur';
        } elseif ($this->model instanceof \App\Models\MasterProgram) {
            $name = $this->model->name;
        } elseif ($this->model instanceof \App\Models\Program) {
            $name = $this->model->masterProgram?->name ?? 'Program';
        } elseif ($this->model instanceof \App\Models\CompetencyUnit) {
            $name = $this->model->name;
        }

        return [
            'title'   => ucfirst($this->action) . ' ' . $this->menu,
            'message' => $this->user->name . ' telah ' . $this->action . ' ' . $this->menu . ': ' . $name,
            'url'     => $this->getUrl(),
            'icon'    => 'activity',
            'time'    => now()->diffForHumans(),
        ];
    }

    private function getUrl()
    {
        if ($this->model instanceof \App\Models\User) {
            return route('admin.users.index');
        }

        if ($this->model instanceof \App\Models\Participant) {
            return route('admin.participants.show', $this->model);
        }

        if ($this->model instanceof \App\Models\Instructor) {
            return route('admin.instructors.show', $this->model);
        }

        if ($this->model instanceof \App\Models\MasterProgram) {
            return route('admin.programs.master.show', $this->model);
        }

        if ($this->model instanceof \App\Models\CompetencyUnit) {
            return route('admin.programs.units.show', $this->model);
        }

        if ($this->model instanceof \App\Models\Program) {
            return route('admin.programs.show', $this->model);
        }

        return route('admin.dashboard');
    }
}
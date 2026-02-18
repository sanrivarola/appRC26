<?php


namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
class HorarioNoCerrado extends Notification
{
    use Queueable;

    public function __construct()
    {
        // Si necesitas, podés pasar datos acá (ej: fecha).
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'mensaje' => 'Te falta cerrar tu horario de salida hoy.',
            'link' => route('horario.card'),
        ];
    }
}

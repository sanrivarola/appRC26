<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Horario;
use App\Models\User;

class AsignarUserIdAHorarios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'horarios:asignar-userid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna el user_id en horarios antiguos a partir del campo equipo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sin_user = 0;
        $con_user = 0;

        $horarios = Horario::whereNull('user_id')->get();

        if ($horarios->isEmpty()) {
            $this->info('No hay horarios sin user_id.');
            return 0;
        }

        foreach ($horarios as $horario) {
            $user = User::where('name', $horario->equipo)->first();
            if ($user) {
                $horario->user_id = $user->id;
                $horario->save();
                $con_user++;
            } else {
                $sin_user++;
                $this->warn("No se encontró usuario para equipo: {$horario->equipo} (id_horario: {$horario->id_horario})");
            }
        }

        $this->info("Proceso finalizado. Se actualizaron $con_user horarios.");
        if ($sin_user > 0) {
            $this->warn("$sin_user horarios no pudieron asociarse a ningún usuario.");
        }
        return 0;
    }
}

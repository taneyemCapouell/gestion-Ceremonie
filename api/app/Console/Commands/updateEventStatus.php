<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class updateEventStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'everyMinute:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will update event status  ';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Changer le statut de "En attente" à "En cours" lorsque la date planifiée est atteinte
        $eventsPending = Event::where('status', 'En attente')
            ->where('date_start', '<=', Carbon::now()) // Date planifiée est passée ou égale à maintenant
            ->get();

        foreach ($eventsPending as $event) {
            // Convertit la date et l'heure planifiées en objet Carbon pour une comparaison précise
            $scheduledDateTime = Carbon::parse($event->date_start . ' ' . $event->time);

            // Vérifie si la date et l'heure planifiées sont passées
            if ($scheduledDateTime->isPast()) {
                // Vérifie si le statut n'a pas déjà été changé
                if ($event->status !== 'En cours') {
                    $event->update(['status' => 'En cours']);
                    info('Statut de l\'événement ' . $event->name . ' changé en "En cours"');
                }
            }
        }

        // Changer le statut de "En cours" à "Terminé" le lendemain de la date planifiée
        $eventsInProgress = Event::where('status', 'En cours')
            ->where('date_start', '<', Carbon::now()->subDay()) // Lendemain de la date planifiée
            ->get();

        foreach ($eventsInProgress as $event) {
            // Vérifie si le statut n'a pas déjà été changé
            if ($event->status !== 'Terminer') {
                $event->update(['status' => 'Terminer']);
                info('Statut de l\'événement ' . $event->name . ' changé en "Terminer"');
            }
        }
    }
}

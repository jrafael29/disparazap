<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use DateTimeZone;


class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // verifica os fluxos a serem enviados
        // $schedule->command('run:get-ready-flows')->everyFiveSeconds();

        // verifica instancias a cada dois minutos
        // $schedule->command('run:check-instances')->everyTwoMinutes();

        // verifica os numeros que devam ser verificados a cada 5 segundos.
        $schedule->command('run:get-ready-phonenumbers-to-verify')->everyFiveSeconds();
    }


    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function scheduleTimezone(): DateTimeZone|string|null
    {
        return 'America/Sao_Paulo';
    }
}

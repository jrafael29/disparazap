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
        $schedule->command('run:get-ready-flows')
            ->withoutOverlapping()
            ->everyFiveSeconds();

        // verifica instancias a cada dois minutos
        $schedule->command('run:check-instances')
            ->withoutOverlapping()
            ->everyMinute();

        // inicia o processo de verificação de existencia dos telefones.
        $schedule->command('run:get-ready-phonenumbers-to-verify')
            // ->withoutOverlapping()
            ->everySecond();

        // verifica todas as checagem, e finaliza caso tenha todas verificações concluidas
        $schedule->command('run:check-if-done-phonenumbers-checks')
            ->withoutOverlapping()
            ->everyFiveSeconds();
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

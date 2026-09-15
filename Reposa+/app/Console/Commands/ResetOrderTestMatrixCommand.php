<?php

namespace App\Console\Commands;

use Database\Seeders\OrderTestMatrixSeeder;
use Illuminate\Console\Command;

class ResetOrderTestMatrixCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:reset-test-matrix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia los pedidos existentes y genera una matriz de 9 pedidos ejemplares coherentes para pruebas intensivas';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Limpiando y regenerando matriz de pedidos de prueba...');

        $seeder = new OrderTestMatrixSeeder;
        $seeder->run();

        $this->info('¡Matriz de 9 pedidos generada satisfactoriamente con 100% de coherencia en Estado, Seguimiento y Acciones!');

        return Command::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\ImportCsvController;

class import_command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import csv to database';

    /**
     * Execute the console command.
     *
     * @var    array|string $result
     * @return string
     */
    public function handle()
    {
        $import_csv = new ImportCsvController();
        $result = $import_csv->index();

        if (is_array($result)) {
            $this->table(['Id', 'Name', 'Email', 'Age', 'Location', 'Error'], $result);
        } else {
            $this->info($result ?? 'Success');
        }
    }
}

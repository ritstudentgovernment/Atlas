<?php

namespace App\Console\Commands;

use App\Spot;
use Illuminate\Console\Command;

class RemoveSpots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:spots {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all spots currently in the database.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->option('force')) {
            $delete = $this->choice('Are you sure you want to remove all spots in the database?', ['Yes', 'No'], 1);
            if ($delete == 'No') {
                return 0;
            }
        }
        Spot::all()->each(function (Spot $spot) {
            $spot->delete();
        });
        $this->info('Spots deleted successfully');

        return 0;
    }
}

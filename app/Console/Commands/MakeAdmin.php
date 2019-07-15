<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {email} {first?} {last?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote or create an admin account.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function makeAdmin(User $user)
    {
        $user->assignRole('admin');
        $this->info("Successfully made $user->first_name $user->last_name ($user->email) an admin!");
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->argument('email');
        if ($user = User::where('email', $email)->first()) {
            $this->makeAdmin($user);
        } else {
            $this->info('No user with that email was found.');
            $makeUser = $this->choice('Would you like to make an account with that email now?', ['Yes', 'No'], 0);
            if ($makeUser == 'Yes') {
                $first = $this->ask('Enter the desired first name: ');
                $last = $this->ask('Enter the desired last name: ');
                $now = Carbon::now('America/New_York')->toDateTimeString();
                $user = User::create([

                    'first_name' => $first,
                    'last_name'  => $last,
                    'email'      => $email,
                    'password'   => bcrypt(str_random(8)),
                    'created_at' => $now,
                    'updated_at' => $now,

                ]);
                $this->makeAdmin($user);
            } else {
                $this->line('Goodbye!');
            }
        }

        return 0;
    }
}

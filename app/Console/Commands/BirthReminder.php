<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\User;
use App\Jobs\BirthMailJob;

class BirthReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:birthreminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Respectively send birthday wishes to registered users on their birth date via email.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
            $users = User::whereMonth('birthdate', '=', date('m'))->whereDay('birthdate', '=', date('d'))->get();
            foreach($users as $users){
                BirthMailJob::dispatch($users);
            }
        
    }
}

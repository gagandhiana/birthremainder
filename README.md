# About birthremailder

In this project, we will send mails to the registerd users to wish them on thier birthday. We will use command, job and schedule to send mails in queue.

## Step 1 : Create Mail Class
~~~~
php artisan make:mail BirthMail
~~~~

## Step 2 : Create command
~~~~
php artisan make:command BirthReminder
~~~~

## Step 3 : set the signature of the command like - 
~~~~
protected $signature = 'users:birthreminder';
~~~~

## Step 4 : Run this command in our terminal 
~~~~
php artisan users:birthreminder
~~~~
- The $description variable is something which should take the precedence with your actual description. This description provides the information about the command.
~~~~
protected $description = 'Respectively send birthday wishes to registered users on their birth date via email.';
~~~~

## Step 5 : Inside the handle() method write all the logic, Open App\Console\Commands\BirthReminder 
~~~~
public function handle()
    {
            $users = User::whereMonth('birthdate', '=', date('m'))->whereDay('birthdate', '=', date('d'))->get();
            foreach($users as $users){
                BirthMailJob::dispatch($users);
            }
        
    }
~~~~

## Step 6 : Create job
~~~~
hp artisan make:job BirthMailJob
~~~~

## Step 7 : Inside the handle() method, Open App\Jobs\BirthMailJob.php
~~~~
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Mail\BirthMail;
use Mail;
use App\Models\User;

class BirthMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $users;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $users)
    {
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = array('id' => $this->users->id, 'name' => $this->users->name, 'email' => $this->users->email);
        Mail::to($data['email'])->send(new BirthMail($data));
    }
}
~~~~

## Step 8 : Register Task Scheduler Command, Open app/Console/Kernel.php 
~~~~
<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */

     protected $commands = [
        Commands\BirthReminder::class,
    ];


    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('users:birthreminder')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
~~~~
 -  we injected BirthReminder command in $commands variable, the schedule() function schedule the command to be invoked on a regular interval.

 ## Step 9 : You can run this command 
 ~~~~
  php artisan users:birthreminder
~~~~
 - On successful execution of command, user will get mail 

## Step 10 : Run Laravel Scheduler Locally
~~~~
php artisan schedule:work
~~~~

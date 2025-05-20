<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command refresh DB and set up the app';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting app refresh...');

        $this->info('Running migration...');
        $this->call('migrate:fresh', ['--seed' => true]);

        // $this->info('Creating passport client personal key...');
        // $this->call('passport:client', ['--personal' => true, '--name' => 'Personal Access Client', '--client' => 0]);

        $this->info('Generate passport key...');
        $this->call('passport:key', ['--force' => true]);

        $this->info('App refresh successfully.');

        return Command::SUCCESS;
    }
}

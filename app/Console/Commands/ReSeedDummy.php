<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReSeedDummy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:refreshdb';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh DB and Its Dummy Content';

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
        $this->info($this->description);
        $this->call("migrate:reset");
        $this->call("admin:install");
        $this->call("db:seed");
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Question;

class QAndAReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'removes all previous progresses';

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
     * @return int
     */
    public function handle()
    {
        Question::where('answered',1)->update([
            'answered' => 0
        ]);

        $this->info("all previous progresses has been removed");
    }
}

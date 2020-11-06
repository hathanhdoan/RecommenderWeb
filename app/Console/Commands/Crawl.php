<?php

namespace App\Console\Commands;

use App\Scraper\Foody;
use Illuminate\Console\Command;

class Crawl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $crawl = new Foody();
        $rs = $crawl->scrape();
        echo "<pre>";
         print_r($value = $rs);
        echo "</pre>";
        exit();
    }
}

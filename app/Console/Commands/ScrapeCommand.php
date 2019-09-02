<?php

namespace App\Console\Commands;

use App\Http\Controllers\ScrapeController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class ScrapeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:yelp {url} {offset?} {limit?} {iterate?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will trigger scrape for yelp.com';

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
    public function handle(ScrapeController $scrape)
    {
        $url     = $this->argument('url');
        $limit   = $this->argument('limit');
        $iterate = $this->argument('iterate');
        $offset  = $this->argument('offset');


        $this->info('SCRAPE THIS URL  ' . $url);

        $request = \Illuminate\Support\Facades\Request::merge([
            'url'     => $url,
            'offset'  => (isset($offset))  ?  $offset : 10,
            'limit'   => (isset($limit))   ?  $limit : 10, // do nothing for now
            'iterate' => (isset($iterate)) ?  $iterate : 30
        ]);

        $scrape->store($request);
    }
}

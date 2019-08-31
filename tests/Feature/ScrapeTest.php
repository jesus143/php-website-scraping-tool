<?php

namespace Tests\Feature;

use App\Record;
use App\Scrape;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScrapeTest extends TestCase
{
    use RefreshDatabase;

    public function test_scrape_yelp() {

        $starts = [
            0
//            , 10, 20, 30, 40, 50, 60, 70, 80
        ];

        foreach($starts as $start) {
            $this->post('scrape', [
                'url' => 'https://www.yelp.com/search?find_desc=Property%20Management&find_loc=Honolulu%2C%20HI&start=' . $start,
                'page' => $start
            ]);
        }

        $this->assertTrue(true);


//        dd(Scrape::all());
//        dd(Record::all());

//        $this->post('scrape', [
//           'url' => 'https://www.yelp.com/search?find_desc=Property%20Management&find_loc=Honolulu%2C%20HI&start=0'
//        ]);
    }
}

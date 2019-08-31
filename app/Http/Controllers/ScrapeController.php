<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScrapeIndexRequest;
use App\Http\Requests\ScrapeStoreRequest;
use App\Scrape;
use Illuminate\Http\Request;
use Weidner\Goutte\GoutteFacade;
use Goutte;
use Symfony\Component\DomCrawler\Crawler;
use App\Record;

/**
 * @todo This is has to be added to repository
 *
 * Class ScrapeController
 * @package App\Http\Controllers
 */
class ScrapeController extends Controller
{
    protected $record, $scrape, $parentUrl='https://www.yelp.com';

    public function index(ScrapeIndexRequest $request)
    {
        return "index for scraping!";
    }

    public function store(Request $request)
    {
        $input = $request->input();

        $url      = $input['url'];
        $offset   = $input['offset'];
        $limit    = $input['limit'];
        $iterate  = $input['iterate'];


        echo " offfset " . $offset;

        for ($i=0; $i<$iterate; $i++) {
            $offsetFinal = $i * $offset;// 0, 10, 20, .....

            // Init scrape db
            $this->scrape = Scrape::where('url', $url)->first();

            if (! $this->scrape) {
                $this->scrape = new Scrape();

                $this->scrape->offset = $offsetFinal;
            } else if (($this->scrape->offset != $offsetFinal) && ($this->scrape->offset < $offsetFinal)) {
                $this->scrape->offset = $offsetFinal;
                $this->scrape->count = 0;
            }

            $i = $this->scrape->offset / $limit;

            echo "\n Loop: $i Offset: " . $this->scrape->offset;


            $this->scrape->url = $url;
            $this->scrape->limit = $limit;

            $urlFinal = $url . '&start=' . $this->scrape->offset;

            echo "\n Url Final: " . $urlFinal;

            $crawler = Goutte::request('GET', $urlFinal);

            $this->getParentListing($crawler);
        }

        // Execute Crawler
        // $this->getListingDetail('http://127.0.0.1:8000/sample');
        // $this->getListingDetail('https://www.yelp.com//biz/royal-realty-honolulu?osq=Property+Management');
        // $this->getListingDetailAttribute( );
        // $this->sampleNode( );
    }

    protected function getParentListing($crawler) {
        $data = [];

        $crawler->filter('.alternate__373c0__1uacp .link-size--inherit__373c0__2JXk5')->each(function ($node, $i) use ($crawler, $data) {
            if($i > $this->scrape->count) {
                $limit = $this->scrape->limit;
                $offset = $this->scrape->offset;


                // Init record db
                $this->record = new Record();

                $this->scrape->count = $i;

                $title = $node->text('no name');

                $link = $this->parentUrl . $node->attr('href');

                dump("\n counter: $i limit:  $limit offset: $offset ");
                dump(" address " . $title);
                dump(" address " . $link);

                $this->record->name_of_business = $title;
                $this->record->yelp_listing_url = $link;

                $this->getListingDetail($link);
            }
        });
    }

    protected function getListingDetailAttribute($html=null) {
        $crawler = new Crawler($html);

        $streetAddress = $crawler->filterXPath('//strong[contains(@class, "street-address")]')->text('');
        $address = $crawler->filterXPath('//address')->text('');
        $neighborhood = $crawler->filterXPath('//span[contains(@class, "neighborhood-str-list")]')->text('');
        $phoneNumber = $crawler->filterXPath('//span[contains(@class, "biz-phone")]')->text('');
        $website = $crawler->filterXPath('//a[contains(@rel, "noopener")]')->text('');

        dump("DETAIL:");
        dump("Street Address: " . $streetAddress);
        dump("Address: " . $address);
        dump("Neighborhood: " . $neighborhood);

        dump($phoneNumber);
        dump($website);


        $this->record->city_name = $streetAddress . ',' . $address . ',' . $neighborhood ;
        $this->record->phone     = $phoneNumber;
        $this->record->website   = $website;
    }

    protected function getListingDetail($url) {
        $crawler = Goutte::request('GET', $url);

        $crawler->filterXPath('//div[contains(@class, "mapbox-text")]')->each(function ($node) use ($crawler) {
            $this->getListingDetailAttribute($node->html('no detail'));
        });

        $this->findKeywords($crawler);

        $this->record->save();
        $this->scrape->save();
    }

    protected function findKeywords($crawler) {
        $keywords = [
            'airbnb',
            'vacation',
            'short term'
        ];

        $keywordFound = '';

        foreach ($keywords as $keyword) {
            $keywordMatched = $crawler->filterXPath('//text()[contains(.,"' . $keyword . '")]')->text("dont exist");

            if ($keywordMatched != 'dont exist') {
                dump("keyword found " . $keyword);

                $keywordFound .= $keyword . ',';
            } else {
                $keywordMatched = $crawler->filterXPath('//text()[contains(.,"' . ucfirst($keyword) . '")]')->text("dont exist");

                if ($keywordMatched != 'dont exist') {
                    dump("keyword found " . ucfirst($keyword));

                    $keywordFound .= $keyword . ',';
                } else {
                    dump(" keyword not  found " . $keyword);
                }
            }
        }

        $this->record->keyword  = $keywordFound;
    }

    public function page() {
        return view('sample');
    }

    protected function sampleNode() {
        $html =  '  
        <div>
            <div class="container"> 
                <p class="message">Hello World!</p>
                <p>Hello Crawler!</p>
                <address>mimbalot buru-un iligan city</address>
            </div> 
        </div> 
 ';

        $crawler = new Crawler($html);


        $content = $crawler->filterXPath('//p[contains(@class, "message")]')->text();
        $address = $crawler->filterXPath('//address')->text();

        dump($content);
        dump($address);
    }

    protected function sampleHtml() {

        $html =  '
        <div class="mapbox-text">
                                    <ul>
                                        <li class="u-relative">
                <span aria-hidden="true" style="width: 18px; height: 18px;" class="icon icon--18-marker icon--size-18 u-absolute u-sticky-top">
    <svg role="img" class="icon_svg">
        <use xlink:href="#18x18_marker"></use>
    </svg>
</span>
                                            <a href="/biz_attribute?biz_id=4vHLIgI9P7-IFKtHZ5HIuw" class="link-more icon-wrapper mapbox-edit">
            <span aria-hidden="true" style="width: 14px; height: 14px;" class="icon icon--14-pencil icon--size-14 icon--linked u-space-r-half">
    <svg role="img" class="icon_svg">
        <use xlink:href="#14x14_pencil"></use>
    </svg>
</span><span>Edit</span>
                                            </a>
                                            <div class="map-box-address u-space-l4">
                                                <strong class="street-address">
                                                    Serving Honolulu Area

                                                </strong>

                                                <address>
                                                    10 Marin Ln<br>Honolulu, HI 96817
                                                </address>

                                                <span class="neighborhood-str-list">
            Downtown        </span>


                                            </div>

                                        </li>


                                        <li>
                    <span aria-hidden="true" style="width: 18px; height: 18px;" class="icon icon--18-phone icon--size-18">
    <svg role="img" class="icon_svg">
        <use xlink:href="#18x18_phone"></use>
    </svg>
</span>
                                            <span class="offscreen">Phone number</span>
                                            <span class="biz-phone">
            (808) 780-2975
        </span>

                                        </li>

                                        <li>
                    <span aria-hidden="true" style="width: 18px; height: 18px;" class="icon icon--18-external-link icon--size-18">
    <svg role="img" class="icon_svg">
        <use xlink:href="#18x18_external_link"></use>
    </svg>
</span>    <span class="biz-website js-biz-website js-add-url-tagging">
        <span class="offscreen">Business website</span>
        <a href="/biz_redir?url=http%3A%2F%2Fwww.royalrealtyllc.com&amp;website_link_type=website&amp;src_bizid=4vHLIgI9P7-IFKtHZ5HIuw&amp;cachebuster=1567233437&amp;s=835f8932428b1d13dc85376f1fb6b1eabb74e4ebeed860a7ae6d35f909a1c4ad&amp;campaign_id=Vl1LxPNAoLlEQp9tB3fRoA" target="_blank" rel="noopener">royalrealtyllc.com</a>
    </span>

                                        </li>

                                        <li class="u-relative">
                <span aria-hidden="true" style="width: 18px; height: 18px;" class="icon icon--18-speech icon--size-18">
    <svg role="img" class="icon_svg">
        <use xlink:href="#18x18_speech"></use>
    </svg>
</span>


                                            <a href="javascript:;" class="js-message-biz">
                                                Contact agent

                                                <div class="time-stamp mtb-response-time-not-fast-responder">
                                                    Replies in about <strong>3 hours</strong>
                                                    <span class="u-bullet-before">100% response rate</span>
                                                </div>
                                            </a>


                                        </li>



                                        <li class="clearfix">
                                            <div>
                    <span aria-hidden="true" style="width: 18px; height: 18px;" class="icon icon--18-mobile icon--size-18">
    <svg role="img" class="icon_svg">
        <use xlink:href="#18x18_mobile"></use>
    </svg>
</span>
                                                <a href="javascript:;" class="js-biz-to-phone">Send to your Phone</a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
 ';

        return $html;
    }




}

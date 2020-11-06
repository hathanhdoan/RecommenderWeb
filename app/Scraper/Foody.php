<?php
namespace App\Scraper;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;
Class Foody{
    public function scrape(){
        $url = "https://www.foody.vn/ho-chi-minh/banh-9-sach-banh-sau-rieng-tran-hung-dao";

        $client = new Client();
        $crawler = $client->request('GET',$url);
        $rs = [];


        $address_elements =  $crawler->filter('div.res-common-add')->children();
        $address_elements = $address_elements->nodes;

        $price_elements =  $crawler->filter('div.res-common-minmaxprice')->children();
        $price_elements = $price_elements->nodes;

        $category_elements =  $crawler->filter('div.category-items')->children();
        $category_elements = $category_elements->nodes;

        $cuisine_elements =  $crawler->filter('div.category-cuisines')->children();
        $cuisine_elements = $cuisine_elements->nodes;

        $branch =  $crawler->filter('h2.brands')->attr('href');
        $crawler->filter('div.category-cuisines')->each(function(Crawler $node){
            $branch = $node->attr('href');
        });
//        return $branch;
        $rs = [
            'street_address'=> trim($address_elements[1]->textContent),
            'district'=> $address_elements[3]->textContent,
            'city'=> $address_elements[4]->textContent,
            'price'=> trim($price_elements[1]->textContent),
            'category'=> trim($category_elements[0]->textContent),
            'cuisine'=> trim($cuisine_elements[1]->textContent),
            'target_customer'=> trim($cuisine_elements[2]->textContent),

        ];
        return $rs;
    }

    public function crawlBranch(){
        $url = "https://www.foody.vn/ho-chi-minh/banh-9-sach-banh-sau-rieng-tran-hung-dao";

        $client = new Client();
        $crawler = $client->request('GET',$url);
        $rs = [];
    }
}

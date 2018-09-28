<?php

namespace App\Service;

use App\Entity\Share;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ShareFinder
{
    private $clientHttp;
    private $domCrawler;

    public function __construct()
    {
        $this->clientHttp = new Client();
        $this->domCrawler = new Crawler();
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function find(string $company): Share
    {
        $html = $this->clientHttp
            ->request('GET', 'https://www.bankier.pl/inwestowanie/profile/quote.html?symbol=' . $company);

        $this->domCrawler->add($html->getBody()->getContents());

        $price = $this->domCrawler
            ->filter('#boxProfilHeader > .boxHeader > .textNowrap > .profilLast')
            ->getNode(0)
            ->nodeValue;
        $price = str_replace(',', '.', str_replace(' zł', '', $price));

        $share = new Share();
        $share->setCompany($company);
        $share->setPrice((float) $price);

        return $share;
    }
}
<?php

declare(strict_types=1);

namespace App\Service\StockExchange;

use App\Entity\Company;
use App\Entity\CompanyShare;
use App\Entity\CompanySource;
use App\Exception\StockExchange\AllSourceFailedException;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ShareFinder
{
    private $clientHttp;

    public function __construct()
    {
        $this->clientHttp = new Client();
    }

    /**
     * @throws AllSourceFailedException
     */
    public function find(Company $company): CompanyShare
    {
        /** @var CompanySource $source */
        foreach ($company->getSources() as $source) {
            try {
                $html = $this->clientHttp
                    ->request('GET', $source->getPath());

                $crawler = new Crawler();
                $crawler->add($html->getBody()->getContents());

                $price = $crawler
                    ->filter($source->getPriceSelector())
                    ->getNode(0)
                    ->nodeValue;

                $price = str_replace(',', '.', preg_replace("/[^0-9,.]/", "", $price));

                $share = new CompanyShare();
                $share->setCompany($company);
                $share->setPrice((float) $price);

                return $share;
            } catch (\GuzzleHttp\Exception\GuzzleException | \Exception $e) {
                /** todo log something */
                continue;
            }
        }

        throw new AllSourceFailedException($company);
    }
}
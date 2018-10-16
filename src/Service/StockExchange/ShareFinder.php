<?php

declare(strict_types=1);

namespace App\Service\StockExchange;

use App\Entity\Company;
use App\Entity\CompanyShare;
use App\Entity\CompanySource;
use App\Event\StockExchange\ShareFoundExceptionEvent;
use App\Exception\StockExchange\AllSourceFailedException;
use App\Exception\StockExchange\SourceFailedException;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ShareFinder
{
    private $clientHttp;

    private $dispatcher;

    public function __construct()
    {
        $this->clientHttp = new Client();
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * @throws AllSourceFailedException
     */
    public function find(Company $company): CompanyShare
    {

        /** @var CompanySource $source */
        foreach ($company->getSources() as $source) {
            try {
                $share = new CompanyShare();
                $share->setCompany($company);
                $share->setPrice($this->findPrice($source));

                return $share;
            } catch (\GuzzleHttp\Exception\GuzzleException | \Exception $e) {
                $this->dispatchFoundExceptionEvent($e);
            }
        }

        throw new AllSourceFailedException($company);
    }

    /**
     * @throws SourceFailedException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function findPrice(CompanySource $source): float
    {
        $html = $this->clientHttp
            ->request('GET', $source->getPath());

        $crawler = new Crawler();
        $crawler->add($html->getBody()->getContents());

        $price = $crawler
            ->filter($source->getPriceSelector())
            ->getNode(0)
            ->nodeValue;

        $price = str_replace(',', '.', preg_replace("/[^0-9,.]/", "", $price));

        if (is_numeric($price)) {
            return (float) $price;
        }

        throw new SourceFailedException($source);
    }

    private function dispatchFoundExceptionEvent(\Exception $e): void
    {
        $this->dispatcher->dispatch(
            ShareFoundExceptionEvent::NAME,
            new ShareFoundExceptionEvent($e)
        );
    }
}
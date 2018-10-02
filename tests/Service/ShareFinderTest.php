<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Company;
use App\Entity\CompanyShare;
use App\Entity\CompanySource;
use App\Exception\StockExchange\AllSourceFailedException;
use App\Service\StockExchange\ShareFinder;
use PHPUnit\Framework\TestCase;

class ShareFinderTest extends TestCase
{
    private $finder;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        $this->finder = new ShareFinder();

        parent::__construct($name, $data, $dataName);
    }

    /**
     * @dataProvider provideCompanies
     * @throws AllSourceFailedException
     */
    public function testCorrectFind(Company $company): void
    {
        $share = $this->finder->find($company);

        $this->assertInstanceOf(CompanyShare::class, $share);
    }

    /**
     * @throws AllSourceFailedException
     */
    public function testFindException(): void
    {
        $this->expectException(AllSourceFailedException::class);
        $this->finder->find($this->getFalseCompany());
    }

    public function provideCompanies(): array
    {
        $sources = [
            [
                'path' => 'https://www.bankier.pl/inwestowanie/profile/quote.html?symbol=PGE',
                'priceSelector' => '#boxProfilHeader > .boxHeader > .textNowrap > .profilLast',
            ],
            [
                'path' => 'https://www.bankier.pl/inwestowanie/profile/quote.html?symbol=CDPROJEKT',
                'priceSelector' => '#boxProfilHeader > .boxHeader > .textNowrap > .profilLast',
            ],
            [
                'path' => 'https://www.bankier.pl/inwestowanie/profile/quote.html?symbol=CIGAMES',
                'priceSelector' => '#boxProfilHeader > .boxHeader > .textNowrap > .profilLast',
            ],
        ];

        $companies = [];

        foreach ($sources as $source) {
            array_push($companies, [$this->getCompany($source['path'], $source['priceSelector'])]);
        }

        return $companies;
    }

    private function getCompany($path, $priceSelector): Company
    {
        $source = new CompanySource();
        $source->setPath($path);
        $source->setPriceSelector($priceSelector);

        $company = new Company();
        $company->setName('Test company');
        $company->addToSources($source);

        return $company;
    }

    private function getFalseCompany(): Company
    {
        $source = new CompanySource();
        $source->setPath('false_path');
        $source->setPriceSelector('false_selector');

        $company = new Company();
        $company->setName('Test company');
        $company->addToSources($source);

        return $company;
    }
}
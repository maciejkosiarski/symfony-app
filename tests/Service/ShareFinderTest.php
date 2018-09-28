<?php

namespace App\Tests\Service;

use App\Entity\Share;
use App\Service\ShareFinder;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

class ShareFinderTest extends TestCase
{
    private $finder;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->finder = new ShareFinder();
    }

    /**
     * @dataProvider provideCompanies
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
	public function testCorrectFind(string $company)
	{
        $share = $this->finder->find($company);

        $this->assertInstanceOf(Share::class, $share);
	}

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testFindException()
    {
        $this->expectException(ClientException::class);
        $this->finder->find('UNDEFINED');
	}

    public function provideCompanies(): array
    {
        return [
            ['PGE'],
            ['CDPROJEKT'],
            ['MBANK'],
        ];
    }
}
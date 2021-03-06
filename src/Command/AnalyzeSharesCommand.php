<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\StockExchange\ShareAnalyzer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeSharesCommand extends Command
{
    private $analyzer;

    public function __construct(?string $name = null, ShareAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('app:shares:analyze')
            ->setDescription('Analyze shares prices and notify by watchers')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->analyzer->analyze();
    }
}
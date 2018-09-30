<?php

namespace App\Command;

use App\Entity\CompanyShare;
use App\Event\StockExchange\ShareFoundEvent;
use App\Event\StockExchange\ShareFoundExceptionEvent;
use App\Exception\CommandAlreadyRunningException;
use App\Repository\CompanyRepository;
use App\Service\ShareFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FindShareCommand extends Command
{
    use LockableTrait;

    private $repository;

    private $dispatcher;

    public function __construct(?string $name = null, CompanyRepository $repository, EventDispatcherInterface $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('app:find:share')
            ->setDescription('Find share current prices')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try{
            if (!$this->lock()) {
                throw new CommandAlreadyRunningException('Find share');
            }

            $finder = new ShareFinder();

            foreach ($this->repository->findByActive(true) as $company) {
                try {
                    $this->dispatchFoundEvent($finder->find($company));
                } catch (\Exception $e) {
                    $this->dispatchFoundExceptionEvent($e, new ConsoleLogger($output));
                }
            }
        } catch (\Exception $e) {
            $this->dispatchFoundExceptionEvent($e, new ConsoleLogger($output));
        } finally {
            $this->release();
        }
    }

    private function dispatchFoundEvent(CompanyShare $share): void
    {
        $this->dispatcher->dispatch(
            ShareFoundEvent::NAME,
            new ShareFoundEvent($share)
        );
    }

    private function dispatchFoundExceptionEvent(\Exception $e,  ConsoleLogger $logger)
    {
        $this->dispatcher->dispatch(
            ShareFoundExceptionEvent::NAME,
            new ShareFoundExceptionEvent($e, $logger)
        );
    }
}
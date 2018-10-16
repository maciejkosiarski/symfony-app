<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\CompanyShare;
use App\Event\StockExchange\CompanySourcesFailedEvent;
use App\Event\StockExchange\ShareFoundEvent;
use App\Event\StockExchange\ShareFoundExceptionEvent;
use App\Exception\CommandAlreadyRunningException;
use App\Exception\StockExchange\AllSourceFailedException;
use App\Repository\CompanyRepository;
use App\Service\StockExchange\ShareFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateSharesCommand extends Command
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

    protected function configure(): void
    {
        $this->setName('app:shares:update')
            ->setDescription('Find current shares prices of active companies')
            ->setHelp('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try{
            if (!$this->lock()) {
                throw new CommandAlreadyRunningException('Find share');
            }

            $finder = new ShareFinder();

            foreach ($this->repository->findByActive(true) as $company) {
                try {
                    $this->dispatchFoundEvent($finder->find($company));
                } catch (AllSourceFailedException $e) {
                    $this->dispatchCompanySourcesFailedEvent($e);
                } catch (\Exception $e) {
                    $this->dispatchFoundExceptionEvent($e);
                }
            }
        } catch (\Exception $e) {
            $this->dispatchFoundExceptionEvent($e);
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

    private function dispatchFoundExceptionEvent(\Exception $e): void
    {
        $this->dispatcher->dispatch(
            ShareFoundExceptionEvent::NAME,
            new ShareFoundExceptionEvent($e)
        );
    }

    private function dispatchCompanySourcesFailedEvent(AllSourceFailedException $e)
    {
        $this->dispatcher->dispatch(
            CompanySourcesFailedEvent::NAME,
            new CompanySourcesFailedEvent($e)
        );
    }
}
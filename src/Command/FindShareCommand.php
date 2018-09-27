<?php

namespace App\Command;

use App\Entity\Share;
use App\Event\Share\ShareFoundEvent;
use App\Service\ShareFinder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FindShareCommand extends Command
{
    private $dispatcher;

    public function __construct(?string $name = null, EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;

        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('app:find:share')
            ->setDescription('Find share current prices')
            ->setHelp('')
            ->addArgument('company', InputArgument::REQUIRED,'Which company share price we want to find.');;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $finder = new ShareFinder();

        $this->dispatchFoundEvent($finder->find($input->getArgument('company')));
    }

    private function dispatchFoundEvent(Share $share): void
    {
        $this->dispatcher->dispatch(
            ShareFoundEvent::NAME,
            new ShareFoundEvent($share)
        );
    }
}
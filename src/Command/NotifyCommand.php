<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\NotificationQueuePosition;
use App\Event\NotificationSentEvent;
use App\Event\NotificationSentExceptionEvent;
use App\Repository\NotificationQueuePositionRepository;
use App\Service\Notifier\Factory\NotifierFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class NotifyCommand extends Command
{
	private $factory;

	private $repository;

	private $dispatcher;

	public function __construct(
		NotificationQueuePositionRepository $repository,
		NotifierFactory $factory,
		EventDispatcherInterface $dispatcher
	) {
		$this->repository = $repository;
		$this->factory    = $factory;
		$this->dispatcher = $dispatcher;

		parent::__construct();
	}

	protected function configure(): void
	{
		$this->setName('app:notify')
			->setDescription('Send notifications.')
			->setHelp('This command send register and available notification to users.')
			->addArgument('notifier', InputArgument::REQUIRED,'Which notifier want to use.');

	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		try{
			$notifier = $this->factory->getNotifierByName($input->getArgument('notifier'));
			/** @var NotificationQueuePosition $queuePosition */
			foreach ($this->repository->getQueueToSendByNotifier($notifier) as $queuePosition) {
				$notifier->notify($queuePosition->getNotification());

				$this->dispatchSentEvent($queuePosition);
			}
		} catch (\Exception $e) {
			$this->dispatchSentExceptionEvent($e, new ConsoleLogger($output));
		}
	}

	private function dispatchSentEvent(NotificationQueuePosition $queuePosition): void
	{
		$this->dispatcher->dispatch(
			NotificationSentEvent::NAME,
			new NotificationSentEvent($queuePosition)
		);
	}

	private function dispatchSentExceptionEvent(\Exception $e, ConsoleLogger $logger): void
	{
		$this->dispatcher->dispatch(
			NotificationSentExceptionEvent::NAME,
			new NotificationSentExceptionEvent($e, $logger)
		);
	}
}
<?php

namespace App\Command;

use App\Entity\NotificationQueuePosition;
use App\Event\NotificationSendedEvent;
use App\Event\NotificationSendedExceptionEvent;
use App\Exception\CommandAlreadyRunningException;
use App\Repository\NotificationQueuePositionRepository;
use App\Service\Factory\NotifierFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class NotifyCommand
 * @package App\Command
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotifyCommand extends Command
{
	use LockableTrait;

	/**
	 * @var NotifierFactory
	 */
	private $factory;

	/**
	 * @var NotificationQueuePositionRepository
	 */
	private $repository;

	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher;

	public function __construct(
		?string $name = null,
		NotificationQueuePositionRepository $repository,
		NotifierFactory $factory,
		EventDispatcherInterface $dispatcher
	) {
		$this->repository = $repository;
		$this->factory    = $factory;
		$this->dispatcher = $dispatcher;

		parent::__construct($name);
	}

	protected function configure(): void
	{
		$this->setName('app:notify')
			->setDescription('Send notifications.')
			->setHelp('This command send register and available notification to users.')
			->addArgument('notifier', InputArgument::REQUIRED,'Which notifier want to use.');

	}

	/**
	 * @param InputInterface  $input
	 * @param OutputInterface $output
	 */
	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		try{
			if (!$this->lock()) {
				throw new CommandAlreadyRunningException('Notify');
			}

			$notifier = $this->factory->getNotifierByName($input->getArgument('notifier'));
			/** @var NotificationQueuePosition $queuePosition */
			foreach ($this->repository->getActiveByNotifier($notifier) as $queuePosition) {
				$notifier->notify($queuePosition->getNotification());

				$this->dispatchSendedEvent($queuePosition);
			}
		} catch (\Exception $e) {
			$this->dispatchSendedExceptionEvent($e, new ConsoleLogger($output));
		} finally {
			$this->release();
		}
	}

	/**
	 * @param NotificationQueuePosition $queuePosition
	 */
	private function dispatchSendedEvent(NotificationQueuePosition $queuePosition): void
	{
		$this->dispatcher->dispatch(
			NotificationSendedEvent::NAME,
			new NotificationSendedEvent($queuePosition)
		);
	}

	/**
	 * @param \Exception    $e
	 * @param ConsoleLogger $logger
	 */
	private function dispatchSendedExceptionEvent(\Exception $e, ConsoleLogger $logger): void
	{
		$this->dispatcher->dispatch(
			NotificationSendedExceptionEvent::NAME,
			new NotificationSendedExceptionEvent($e, $logger)
		);
	}
}
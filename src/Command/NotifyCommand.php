<?php

namespace App\Command;

use App\Entity\Notification;
use App\Event\NotificationSendedEvent;
use App\Event\NotificationSendedExceptionEvent;
use App\Repository\NotificationRepository;
use App\Service\Factory\NotifierFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class NotifyCommand
 * @package App\Command
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotifyCommand extends Command
{
	/**
	 * @var NotifierFactory
	 */
	private $factory;

	/**
	 * @var NotificationRepository
	 */
	private $repository;

	/**
	 * @var EventDispatcher
	 */
	private $dispatcher;

	public function __construct(
		?string $name = null,
		NotificationRepository $repository,
		NotifierFactory $factory,
		EventDispatcher $dispatcher
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
			$notifier = $this->factory->getNotifierByName($input->getArgument('notifier'));
			/** @var Notification $notification */
			foreach ($this->repository->getActiveByNotifier($notifier) as $notification) {
				$notifier->notify($notification);

				$this->dispatchSendedEvent($notification);
			}
		} catch (\Exception $e) {
			$this->dispatchSendedExceptionEvent($e, new ConsoleLogger($output));
		}
	}

	private function dispatchSendedEvent(Notification $notification): void
	{
		$this->dispatcher->dispatch(
			NotificationSendedEvent::NAME,
			new NotificationSendedEvent($notification)
		);
	}

	private function dispatchSendedExceptionEvent(\Exception $e, ConsoleLogger $logger): void
	{
		$this->dispatcher->dispatch(
			NotificationSendedExceptionEvent::NAME,
			new NotificationSendedExceptionEvent($e, $logger)
		);
	}
}
<?php

namespace App\Command;

use App\Exception\CreateNotifierException;
use App\Repository\NotificationRepository;
use App\Service\Factory\NotifierFactory;
use App\Service\Notifier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

	public function __construct(?string $name = null, NotificationRepository $repository, NotifierFactory $factory)
	{
		$this->repository = $repository;
		$this->factory    = $factory;
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
	 * @throws CreateNotifierException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$notifier = $this->getNotifier($input->getArgument('notifier'));

		foreach ($this->repository->getActiveByNotifier($notifier) as $notification) {
			$notifier->notify($notification);
		}
	}

	/**
	 * @param string $notifierType
	 * @return Notifier
	 * @throws CreateNotifierException
	 */
	private function getNotifier(string $notifierType): Notifier
	{
		$createMethod = 'create' . ucfirst($notifierType). 'Notifier';

		if (method_exists($this->factory, $createMethod)) {
			return $this->factory->{$createMethod}();
		};

		throw new CreateNotifierException($createMethod);
	}
}
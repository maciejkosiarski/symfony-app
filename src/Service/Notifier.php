<?php


namespace App\Service;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Notifier
 * @package App\Service
 * @author  Maciej Kosiarski <mks@moleo.pl>
 */
class Notifier
{
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * @var \Swift_Mailer
	 */
	private $mailer;

	/**
	 * NotificationsPicker constructor.
	 * @param EntityManagerInterface $em
	 * @param \Swift_Mailer $mailer
	 */
	public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer)
	{
		$this->em = $em;
		$this->mailer = $mailer;
	}

	/**
	 * @param int $type
	 * @return array|Notification[]
	 */
	public function find(int $type): array
	{
		/** @var NotificationRepository $repository */
		$repository = $this->em->getRepository(Notification::class);

		return $repository->getActiveByType($type);
	}

	/**
	 * @param int $type
	 */
	public function notify(int $type): void
	{
		foreach ($this->find($type) as $notification) {
			$message = (new \Swift_Message('Notify'))
				->setFrom('notify@example.com')
				->setTo($notification->getUser()->getEmail())
				->setBody($notification->getMessage());

			$this->mailer->send($message);

			if (!$notification->isLoop()) {
				$notification->activeToggle();

				$this->em->flush();
			}
		}
	}
}
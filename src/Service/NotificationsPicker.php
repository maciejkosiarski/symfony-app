<?php


namespace App\Service;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class NotificationsPicker
 * @package App\Service
 * @author  Maciej Kosiarski <mks@moleo.pl>
 */
class NotificationsPicker
{
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	/**
	 * NotificationsPicker constructor.
	 * @param EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
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
	 * @param int           $type
	 * @param \Swift_Mailer $mailer
	 */
	public function notify(int $type, \Swift_Mailer $mailer): void
	{
		foreach ($this->find($type) as $notification) {
			$message = (new \Swift_Message('Notify'))
				->setFrom('notify@example.com')
				->setTo($notification->getUser()->getEmail())
				->setBody($notification->getMessage());

			$mailer->send($message);

			if (!$notification->isLoop()) {
				$notification->activeToggle();

				$this->em->flush();
			}
		}
	}
}
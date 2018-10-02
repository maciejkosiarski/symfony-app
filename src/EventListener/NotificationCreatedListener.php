<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Notification;
use App\Entity\NotificationQueuePosition;
use Cron\CronExpression;
use Doctrine\ORM\Event\LifecycleEventArgs;

class NotificationCreatedListener
{
	/**
	 * @param LifecycleEventArgs $args
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function postPersist(LifecycleEventArgs $args): void
	{
		$entity = $args->getEntity();

		if (!$entity instanceof Notification) {
			return;
		}

		$em = $args->getEntityManager();

		$cronExpression = CronExpression::factory($entity->getIntervalExpression());

		$dudeDate = new \DateTime('@' . $cronExpression->getNextRunDate()->getTimestamp());
		$dudeDate ->setTimezone($cronExpression->getNextRunDate()->getTimezone());

		$queuePosition = new NotificationQueuePosition();
		$queuePosition->setNotification($entity);
		$queuePosition->setDueDate($dudeDate);

		$em->persist($queuePosition);

		$em->flush();
	}
}
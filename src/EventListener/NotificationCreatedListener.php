<?php

namespace App\EventListener;

use App\Entity\Notification;
use App\Entity\NotificationQueuePosition;
use Cron\CronExpression;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Class NotificationCreatedListener
 * @package App\EventListener
 * @author  Maciej Kosiarski <maciek.kosiarski@gmail.com>
 */
class NotificationCreatedListener
{
	/**
	 * @param LifecycleEventArgs $args
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function postPersist(LifecycleEventArgs $args)
	{
		$entity = $args->getEntity();

		if (!$entity instanceof Notification) {
			return;
		}

		$em = $args->getEntityManager();

		$cronExpression = CronExpression::factory($entity->getIntervalExpression());

		$dudeDate = new \DateTime('@' . $cronExpression->getNextRunDate()->getTimestamp());
		$dudeDate ->setTimezone($cronExpression->getNextRunDate()->getTimezone());

		$em->persist(new NotificationQueuePosition($entity, $dudeDate));

		$em->flush();
	}
}
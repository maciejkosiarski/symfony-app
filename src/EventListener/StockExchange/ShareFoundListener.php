<?php

declare(strict_types=1);

namespace App\EventListener\StockExchange;

use App\Entity\CompanyWatcher;
use App\Entity\Notification;
use App\Event\StockExchange\ShareFoundEvent;
use App\Event\StockExchange\ShareFoundExceptionEvent;
use App\Service\StockExchange\ShareAnalyzer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ShareFoundListener
{
    private $em;
    private $analyzer;
    private $dispatcher;

    public function __construct(EntityManagerInterface $em, ShareAnalyzer $sa, EventDispatcherInterface $edi)
    {
        $this->em = $em;
        $this->analyzer = $sa;
        $this->dispatcher = $edi;
    }

    public function onShareFound(ShareFoundEvent $event): void
    {
        $share = $event->getShare();

        try {
            $messages = [];
            $messages[] = $this->analyzer->checkExtremes($share);
            $messages[] = $this->analyzer->checkDifference($share);

            $repository = $this->em->getRepository(CompanyWatcher::class);

            $watchers = $repository->findByCompany($share->getCompany());

            $this->em->persist($share);
            $this->em->flush();

            foreach ($messages as $message) {
                if ($message) {
                    /** @var CompanyWatcher $watcher */
                    foreach ($watchers as $watcher) {
                        $notification = new Notification();
                        $notification->setUser($watcher->getUser());
                        $notification->setMessage($message);
                        $notification->setIntervalExpression('* * * * *');
                        $notification->setRecurrent(false);
                        $notification->setType(Notification::TYPE_EMAIL);

                        $this->em->persist($notification);
                        $this->em->flush();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->dispatchShareFoundExceptionEvent($e);
        }
    }

    private function dispatchShareFoundExceptionEvent(\Exception $e): void
    {
        $this->dispatcher->dispatch(
            ShareFoundExceptionEvent::NAME,
            new ShareFoundExceptionEvent($e)
        );
    }
}
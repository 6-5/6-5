<?php

namespace AppBundle\EventListener;

use AppBundle\Event\ReportEvent;
use AppBundle\Mailer\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MailerListener implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ReportEvent::ADDRESSED => 'onReportAddressed',
        ];
    }

    public function onReportAddressed(ReportEvent $event)
    {
        $this->mailer->notifyReportAddressed($event->getReport());
    }
}

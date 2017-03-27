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
            ReportEvent::READED => 'onReportReaded',
            ReportEvent::ACCEPTED => 'onReportAccepted',
            ReportEvent::REFUSED => 'onReportRefused',
            ReportEvent::TRANSFERRED => 'onReportTransferred',
        ];
    }

    public function onReportAddressed(ReportEvent $event)
    {
        $this->mailer->notifyReportAddressed($event->getReport());
    }

    public function onReportReaded(ReportEvent $event)
    {
        $this->mailer->notifyReportReaded($event->getReport());
    }

    public function onReportAccepted(ReportEvent $event)
    {
        $this->mailer->notifyReportAccepted($event->getReport());
    }

    public function onReportRefused(ReportEvent $event)
    {
        $this->mailer->notifyReportRefused($event->getReport());
    }

    public function onReportTransferred(ReportEvent $event)
    {
        $this->mailer->notifyReportTransferred($event->getReport());
    }
}

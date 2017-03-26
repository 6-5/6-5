<?php

namespace AppBundle\Mailer;

use AppBundle\Entity\Report;
use AppBundle\Entity\User;
use Symfony\Component\Templating\EngineInterface;

class Mailer
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function notifyReportAddressed(Report $report)
    {
        $this->notifyReport($report->getLastDecision()->getUser(), 'report/addressed', $report);
    }

    public function notifyReportReaded(Report $report)
    {
        $this->notifyReport($report->getCreatedBy(), 'report/readed', $report);
    }

    public function notifyReportAccepted(Report $report)
    {
        $this->notifyReport($report->getCreatedBy(), 'report/accepted', $report);
    }

    public function notifyReportRefused(Report $report)
    {
        $this->notifyReport($report->getCreatedBy(), 'report/refused', $report);
    }

    public function notifyReportTransferred(Report $report)
    {
        $this->notifyReport($report->getCreatedBy(), 'report/transferred', $report);
    }

    private function notifyReport(User $to, $template, Report $report)
    {
        $this->mailer->send(
            $this->createMessage($to, $template, ['report' => $report])
        );
    }

    private function createMessage(User $to, $template, $params)
    {
        return \Swift_Message::newInstance()
            ->setFrom('send@example.com')
            ->setTo($to->getEmailCanonical())
            ->setSubject($this->templating->render("emails/$template.subject.txt.twig", $params))
            ->setBody($this->templating->render("emails/$template.body.txt.twig", $params), 'text/plain')
        ;
    }
}

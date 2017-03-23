<?php

namespace AppBundle\Mailer;

use AppBundle\Entity\Report;
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
        $this->mailer->send(
            $this->createMessage('report/addressed', ['report' => $report])
        );
    }

    private function createMessage($name, $params)
    {
        return \Swift_Message::newInstance()
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setSubject($this->templating->render("emails/$name.subject.txt.twig", $params))
            ->setBody($this->templating->render("emails/$name.html.twig", $params), 'text/html')
            ->addPart($this->templating->render("emails/$name.txt.twig", $params), 'text/plain')
        ;
    }
}

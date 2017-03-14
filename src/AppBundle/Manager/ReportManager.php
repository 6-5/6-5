<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Decision;
use AppBundle\Entity\Report;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Workflow\Workflow;

class ReportManager
{
    private $em;
    private $workflow;

    public function __construct(EntityManager $em, Workflow $workflow)
    {
        $this->em = $em;
        $this->workflow = $workflow;
    }

    public function createReport(User $createdBy = null)
    {
        $repo = $this->em->getRepository('AppBundle:Report');
        while ($repo->findOneByReference($reference = strtoupper(bin2hex(random_bytes(3))))) { };

        return (new Report())
            ->setCreatedBy($createdBy)
            ->setReference($reference)
        ;
    }

    public function saveAsDraft(Report $report)
    {
        $this->workflow->apply($report, 'draft');

        $report->setIsDraft(true);

        return $report;
    }

    public function addressTo(Report $report, User $user)
    {
        $this->workflow->apply($report, 'address');

        $decision = (new Decision())
            ->setUser($user)
            ->setStatus(Report::STATUS_ADDRESSED)
        ;

        $report
            ->setIsDraft(false)
            ->setAddressedTo($user)
            ->addDecision($decision)
        ;

        return $report;
    }

    public function read(Report $report, $readedAt = null)
    {
        $this->workflow->apply($report, 'read');

        /** @var Decision $decision */
        $decision = $report->getDecisions()->last();
        $decision
            ->setReadedAt($readedAt ?: new \DateTime())
            ->setStatus(Report::STATUS_READED)
        ;

        return $report;
    }

    public function decideToAccept(Report $report, $comment, $decidedAt = null)
    {
        $this->workflow->apply($report, 'accept');

        return $this->decideTo($report, Report::STATUS_ACCEPTED, $comment, $decidedAt);
    }

    public function decideToRefuse(Report $report, $comment, $decidedAt = null)
    {
        $this->workflow->apply($report, 'refuse');

        return $this->decideTo($report, Report::STATUS_REFUSED, $comment, $decidedAt);
    }

    public function decideToTransfer(Report $report, $newUser, $comment, $decidedAt = null)
    {
        $this->workflow->apply($report, 'transfer');

        $report = $this->decideTo($report, Report::STATUS_TRANSFERRED, $comment, $decidedAt);

        $decision = (new Decision())
            ->setUser($newUser)
            ->setStatus(Report::STATUS_ADDRESSED)
        ;

        $report->addDecision($decision);

        return $report;
    }

    private function decideTo(Report $report, $status, $comment, $decidedAt = null)
    {
        /** @var Decision $decision */
        $decision = $report->getDecisions()->last();
        $decision
            ->setDecidedAt($decidedAt ?: new \DateTime())
            ->setStatus($status)
            ->setComment($comment)
        ;

        return $report;
    }
}
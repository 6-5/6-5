<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Decision;
use AppBundle\Entity\Report;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class ReportManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createReport()
    {
        $repo = $this->em->getRepository('AppBundle:Report');
        while ($repo->findOneByReference($reference = strtoupper(bin2hex(random_bytes(3))))) { };

        return (new Report())
            ->setReference($reference)
        ;
    }

    public function addressTo(Report $report, User $user)
    {
        $decision = (new Decision())
            ->setUser($user)
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
        /** @var Decision $decision */
        $decision = $report->getDecisions()->last();
        $decision->setReadedAt($readedAt ?: new \DateTime());

        return $report;
    }

    public function decideToAccept(Report $report, $comment, $decidedAt = null)
    {
        return $this->decideTo($report, Decision::STATE_ACCEPTED, $comment, $decidedAt);
    }

    public function decideToRefuse(Report $report, $comment, $decidedAt = null)
    {
        return $this->decideTo($report, Decision::STATE_REFUSED, $comment, $decidedAt);
    }

    public function decideToTransfer(Report $report, $newUser, $comment, $decidedAt = null)
    {
        $report = $this->decideTo($report, Decision::STATE_TRANSFERRED, $comment, $decidedAt);

        $decision = (new Decision())
            ->setUser($newUser)
        ;

        $report->addDecision($decision);

        return $report;
    }

    private function decideTo(Report $report, $state, $comment, $decidedAt = null)
    {
        /** @var Decision $decision */
        $decision = $report->getDecisions()->last();
        $decision
            ->setDecidedAt($decidedAt ?: new \DateTime())
            ->setState($state)
            ->setComment($comment)
        ;

        return $report;
    }
}
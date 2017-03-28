<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Decision;
use AppBundle\Entity\Report;
use AppBundle\Entity\User;
use AppBundle\Event\ReportEvent;
use AppBundle\Utils\Utils;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Workflow\Workflow;

class ReportManager
{
    private $em;
    private $translator;
    private $requestStack;
    private $workflow;
    private $tokenStorage;
    private $dispatcher;

    public function __construct(
        EntityManager $em,
        TranslatorInterface $translator,
        RequestStack $requestStack,
        Workflow $workflow,
        TokenStorage $tokenStorage,
        EventDispatcherInterface $dispatcher
    ) {
        $this->em = $em;
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->workflow = $workflow;
        $this->tokenStorage = $tokenStorage;
        $this->dispatcher = $dispatcher;
    }

    public function createReport(User $createdBy = null)
    {
        $repo = $this->em->getRepository('AppBundle:Report');
        while ($repo->findOneByReference($reference = strtoupper(bin2hex(random_bytes(3))))) {
        };

        return (new Report())
            ->setCreatedBy($createdBy)
            ->setReference($reference);
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
            ->setStatus(Report::STATUS_ADDRESSED);

        $report
            ->setIsDraft(false)
            ->setAddressedTo($user)
            ->addDecision($decision);

        $event = new ReportEvent($report);
        $this->dispatcher->dispatch(ReportEvent::ADDRESSED, $event);

        return $report;
    }

    public function read(Report $report, $readedAt = null)
    {
        // mark as readed only the first time
        if ($report->getLastDecision()->getReadedAt()) {
            return $report;
        }

        $this->workflow->apply($report, 'read');

        /** @var Decision $decision */
        $decision = $report->getDecisions()->last();
        $decision
            ->setReadedAt($readedAt ?: new \DateTime())
            ->setStatus(Report::STATUS_READED);

        $event = new ReportEvent($report);
        $this->dispatcher->dispatch(ReportEvent::READED, $event);

        return $report;
    }

    public function decideToAccept(Report $report, $comment, $decidedAt = null)
    {
        $this->workflow->apply($report, 'accept');

        $report = $this->decideTo($report, Report::STATUS_ACCEPTED, $comment, $decidedAt);

        $event = new ReportEvent($report);
        $this->dispatcher->dispatch(ReportEvent::ACCEPTED, $event);

        return $report;
    }

    public function decideToRefuse(Report $report, $comment, $decidedAt = null)
    {
        $this->workflow->apply($report, 'refuse');

        $report = $this->decideTo($report, Report::STATUS_REFUSED, $comment, $decidedAt);

        $event = new ReportEvent($report);
        $this->dispatcher->dispatch(ReportEvent::ACCEPTED, $event);

        return $report;
    }

    public function decideToTransfer(Report $report, $newUser, $comment, $decidedAt = null)
    {
        $this->workflow->apply($report, 'transfer');

        $report = $this->decideTo($report, Report::STATUS_TRANSFERRED, $comment, $decidedAt);

        $decision = (new Decision())
            ->setUser($newUser)
            ->setStatus(Report::STATUS_ADDRESSED);

        $report->addDecision($decision);

        $event = new ReportEvent($report);
        $this->dispatcher->dispatch(ReportEvent::TRANSFERRED, $event);
        $this->dispatcher->dispatch(ReportEvent::ADDRESSED, $event);

        return $report;
    }

    private function decideTo(Report $report, $status, $comment, $decidedAt = null)
    {
        /** @var Decision $decision */
        $decision = $report->getDecisions()->last();
        $decision
            ->setDecidedAt($decidedAt ?: new \DateTime())
            ->setStatus($status)
            ->setComment($comment);

        return $report;
    }

    /**
     * Gets all translated classifications of a report.
     *
     * Example in FR:
     * <code>
     * [
     *     'unclassified' => 'non classifié',
     *     'intern' => 'intern',
     *     ...
     * ]
     * </code>
     *
     * @param null $locale Locale used to translate (default: locale in current request)
     *
     * @return array
     */
    public function getClassifications($locale = null)
    {
        $locale = $locale ?: ($request = $this->requestStack->getCurrentRequest()) ? $request->getLocale() : 'en';

        $classifications = [];
        foreach (Utils::getConstants(Report::class, 'CLASSIFICATION_') as $id) {
            $classifications[$id] = $this->translator->trans($id, [], 'classification', $locale);
        }

        return $classifications;
    }

    /**
     * Gets all translated urgencies of a report.
     *
     * Example in FR:
     * <code>
     * [
     *     'low' => 'faible',
     *     'normal' => 'normale',
     *     ...
     * ]
     * </code>
     *
     * @param null $locale Locale used to translate (default: locale in current request)
     *
     * @return array
     */
    public function getUrgencies($locale = null)
    {
        $locale = $locale ?: ($request = $this->requestStack->getCurrentRequest()) ? $request->getLocale() : 'en';

        $urgencies = [];
        foreach (Utils::getConstants(Report::class, 'URGENCY_') as $id) {
            $urgencies[$id] = $this->translator->trans($id, [], 'urgency', $locale);
        }

        return $urgencies;
    }


    /**
     * Checks if given user is current decider of report.
     *
     * @param Report $report
     * @param User|null $user Default to logged user
     *
     * @return bool
     */
    public function isCurrentDecider(Report $report, User $user = null)
    {
        $user = $user ?: $this->tokenStorage->getToken()->getUser();

        /** @var Decision $decision */
        $decision = $report->getDecisions()->last();

        return $decision->getUser() === $user;
    }

    /**
     * Checks if given user can decide to accept/refuse/transfer report.
     *
     * @param Report $report Default to logged user
     * @param User|null $user
     *
     * @return bool
     */
    public function canDecide(Report $report, User $user = null)
    {
        return $this->isCurrentDecider($report, $user)
            && ($this->workflow->can($report, 'accept')
                || $this->workflow->can($report, 'refuse')
                || $this->workflow->can($report, 'transfer')
            );
    }
}

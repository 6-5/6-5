<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Report;
use AppBundle\Manager\ReportManager;
use AppBundle\Entity\User;
use AppBundle\Manager\UserManager;

class AppExtension extends \Twig_Extension
{
    private $rm;
    private $um;

    public function __construct(ReportManager $rm, UserManager $um)
    {
        $this->rm = $rm;
        $this->um = $um;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('status_to_icon', array($this, 'statusToIcon')),
            new \Twig_SimpleFilter('status_to_bg', array($this, 'statusToBg')),
            new \Twig_SimpleFilter('user_fullname', array($this, 'displayFullName')),
        );
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_current_decider', array($this, 'isCurrentDecider')),
        );
    }

    public function statusToIcon($status)
    {
        return [
            Report::STATUS_DRAFT => 'gesture',
            Report::STATUS_ADDRESSED => 'hourglass_empty',
            Report::STATUS_READED => 'hourglass_full',
            Report::STATUS_TRANSFERRED => 'subdirectory_arrow_right',
            Report::STATUS_ACCEPTED => 'check',
            Report::STATUS_REFUSED => 'close',
        ][$status];
    }

    public function statusToBg($status)
    {
        return [
            Report::STATUS_DRAFT => '',
            Report::STATUS_ADDRESSED => 'bg-blue-grey',
            Report::STATUS_READED => 'bg-blue-grey',
            Report::STATUS_TRANSFERRED => 'bg-orange',
            Report::STATUS_ACCEPTED => 'bg-green',
            Report::STATUS_REFUSED => 'bg-red',
        ][$status];
    }

    /**
     * @see ReportManager::isCurrentDecider()
     */
    public function isCurrentDecider(Report $report)
    {
        return $this->rm->isCurrentDecider($report);
    }

    public function displayFullName(User $user, $abbreviated = false, $locale = null)
    {
        return $this->um->getFullName($user, $abbreviated, $locale);
    }
}

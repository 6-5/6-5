<?php

namespace AppBundle\Event;

use AppBundle\Entity\Report;
use Symfony\Component\EventDispatcher\Event;

class ReportEvent extends Event
{
    const ADDRESSED = 'report.addressed';
    const READED = 'report.readed';
    const TRANSFERRED = 'report.transferred';
    const ACCEPTED = 'report.accepted';
    const REFUSED = 'report.refused';

    private $report;

    public function __construct(Report $report)
    {
        $this->report = $report;
    }

    /**
     * @return Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param Report $report
     */
    public function setReport($report)
    {
        $this->report = $report;
    }
}

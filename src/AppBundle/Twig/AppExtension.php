<?php

namespace AppBundle\Twig;

use AppBundle\Entity\Report;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('status_to_icon', array($this, 'statusToIcon')),
            new \Twig_SimpleFilter('status_to_bg', array($this, 'statusToBg')),
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
}
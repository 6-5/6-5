<?php

namespace AppBundle\Manager;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\MessageSelector;
use AppBundle\Entity\User;

class UserManager
{
    private $translator;
    private $requestStack;

    public function __construct(Translator $translator, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
    }

    public function getRanks($abbreviated = false, $locale = null)
    {
        return;
    }

    public function getFullName(User $user, $abbreviate = false, $locale = null)
    {
        return;
    }
}

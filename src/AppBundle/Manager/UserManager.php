<?php

namespace AppBundle\Manager;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use AppBundle\Entity\User;

class UserManager
{
    private $translator;
    private $requestStack;

    public function __construct(TranslatorInterface $translator, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
    }

    public function getRanks($abbreviated = false, $locale = null)
    {
        //$userReflection = new \ReflectionClass(User::class);
        //$constants = $userReflection->getConstants();
        //return $constants;

        return;
    }

    public function getFullName(User $user, $abbreviate = false, $locale = null)
    {
        return;
    }
}

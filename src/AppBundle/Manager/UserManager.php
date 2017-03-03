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
        $locale = $locale ?: $this->requestStack->getCurrentRequest()->getLocale();
        $r = new \ReflectionClass(User::class);
        $filter = function ($constant) { return false !== strpos($constant, 'RANK_'); };
        $constants = array_filter($r->getConstants(), $filter, ARRAY_FILTER_USE_KEY);

        $ranks = [];
        foreach ($constants as $id) {
            $ranks[$id] = $this->translator->trans($abbreviated ? $id.'.abbr' : $id, [], 'ranks', $locale);
        }

        return $ranks;
    }

    public function getFullName(User $user, $abbreviated = false, $locale = null)
    {

        if ($abbreviated) {
            return $this->translator->trans(
                $user->getRank() . '.abbr',
                [],
                'ranks',
                $locale) . ' ' . strtoupper($user->getLastname()) . ' ' . ucfirst($user->getFirstname());
        } else {
            return ucfirst($this->translator->trans(
                $user->getRank(),
                [],
                'ranks',
                $locale)) . ' ' . strtoupper($user->getLastname()) . ' ' . ucfirst($user->getFirstname());;
        }
    }
}

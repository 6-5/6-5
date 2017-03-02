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
        $ranks = [];
        $userReflection = new \ReflectionClass(User::class);
        $constants = $userReflection->getConstants();

        if ($abbreviated) {
            foreach ($constants as $key => $value) {
                if (preg_match('[^RANK_.*$]', $key)) {
                    $ranks[$key] = $this->translator->trans(
                        $constants[$key] . '.abbr',
                        [],
                        'ranks',
                        $locale ? $locale : $this->requestStack->getCurrentRequest()->getLocale()
                    );
                }
            }
        } else {
            foreach ($constants as $key => $value) {
                if (preg_match('[^RANK_.*$]', $key)) {
                    $ranks[$key] = $this->translator->trans(
                        $constants[$key],
                        [],
                        'ranks',
                        $locale ? $locale : $this->requestStack->getCurrentRequest()->getLocale());
                }
            }
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

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
                    $ranks[$key] = $this->translator->trans($constants[$key] . '.abbr', [], 'ranks', $locale);
                }
            }
        } else {
            foreach ($constants as $key => $value) {
                if (preg_match('[^RANK_.*$]', $key)) {
                    $ranks[$key] = $this->translator->trans($constants[$key], [], 'ranks', $locale);
                }
            }
        }

        return $ranks;
    }

    public function getFullName(User $user, $abbreviate = false, $locale = null)
    {
        return;
    }
}

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


    /**
     * Get a list of the military ranks in the specified locale
     *
     * @param bool $abbreviated Tells whether the ranks should abbreviated or not
     * @param null $locale Specifies the locale in which to get the ranks
     * @return array An associative array with the english rank as key and specified locale rank as value
     */
    public function getRanks($abbreviated = false, $locale = null)
    {
        $locale = $locale ?: $this->requestStack->getCurrentRequest()->getLocale();
        $r = new \ReflectionClass(User::class);
        $filter = function ($constant) { return false !== strpos($constant, 'RANK_'); };
        $constants = array_filter($r->getConstants(), $filter, ARRAY_FILTER_USE_KEY);

        $ranks = [];
        foreach ($constants as $id) {
            $ranks[$id] = $this->translator->trans($abbreviated ? $id . '.abbr' : $id, [], 'ranks', $locale);
        }

        return $ranks;
    }

    /**
     * Displays the military address of a user
     *
     * The military address can take multiple forms.
     * The most common and most legit being : <rank> <LASTNAME> <Firstname>
     *
     * @param User $user A User entitiy
     * @param bool $abbreviated Tells whether the rank should be abbreviated or not
     * @param null $locale Specifies the locale in which to get the rank
     * @return string A string containing the military address of the given user in the given locale.
     */
    public function getFullName(User $user, $abbreviated = false, $locale = null)
    {
        $locale = $locale ?: $this->requestStack->getCurrentRequest()->getLocale();
        $rank = $abbreviated ? $user->getRank() . '.abbr' : $user->getRank();

        return $this->translator->trans($rank, [], 'ranks', $locale) .
            ' ' . strtoupper($user->getLastname()) . ' ' . ucfirst($user->getFirstname());
    }
}

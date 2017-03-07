<?php

namespace Tests\AppBundle\Manager;

use AppBundle\Entity\User;
use AppBundle\Manager\UserManager;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\XliffFileLoader;

class UserManagerTest extends \PHPUnit\Framework\TestCase
{
    /** @var  UserManager */
    private $userManager;
    /** @var Symfony\Component\Translation\Translator; */
    private $translator;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $requestStack;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $request;

    protected function SetUp()
    {
        $this->requestStack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->translator = new Translator(null);
        $this->translator->setFallbackLocales(array('en'));
        $this->translator->addLoader('xlf', new XliffFileLoader());
        $this->translator->addResource('xlf', "./app/Resources/translations/ranks.en.xlf", 'en', 'ranks');  // fallback resource
    }

    /**
     * @dataProvider getFullNameProvider
     */
    public function testGetFullName($rank, $firstname, $lastname, $abbreviated, $locale, $expected)
    {
        $this->translator->setLocale($locale);
        $this->translator->addResource('xlf', "./app/Resources/translations/ranks.$locale.xlf", $locale, 'ranks');
        $this->userManager = new UserManager($this->translator, $this->requestStack);

        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->any())
            ->method('getLocale')
            ->willReturn('en');

        $user = (new User())
            ->setRank($rank)
            ->setFirstname($firstname)
            ->setLastname($lastname);

        $fullname = $this->userManager->getFullName($user, $abbreviated, $locale);
        $this->assertEquals($expected, $fullname);
    }

    public function getFullNameProvider()
    {
        return [
            [User::RANK_PRIVATE, 'Dany', 'Maillard', true, 'fr', 'sdt MAILLARD Dany'],
            [User::RANK_PRIVATE, 'Dany', 'Maillard', false, 'fr', 'soldat MAILLARD Dany'],
            [User::RANK_QUARTERMASTER_SERGEANT, 'Gabriel', 'Freitas', true, 'de', 'four FREITAS Gabriel'],
            [User::RANK_QUARTERMASTER_SERGEANT, 'Gabriel', 'Freitas', false, 'de', 'fourier FREITAS Gabriel'],
            [User::RANK_CAPTAIN, 'Sébastien', 'Pahud', true, 'it', 'cap PAHUD Sébastien'],
            [User::RANK_CAPTAIN, 'Sébastien', 'Pahud', false, 'it', 'capitano PAHUD Sébastien'],
            [User::RANK_WARRANT_OFFICER_CLASS_2, 'John', 'Doe', true, 'br', 'wo2 DOE John'],
            [User::RANK_WARRANT_OFFICER_CLASS_2, 'John', 'Doe', false, null, 'warrant officer class 2 DOE John'],
        ];
    }

    /**
     * @dataProvider getRanksProvider
     */
    public function testGetRanks($locale, $abbreviated, $subset)
    {
        $this->translator->setLocale($locale);
        $this->translator->addResource('xlf', "./app/Resources/translations/ranks.$locale.xlf", $locale, 'ranks');
        $this->userManager = new UserManager($this->translator, $this->requestStack);

        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->any())
            ->method('getLocale')
            ->willReturn('en');

        $ranks = $this->userManager->getRanks($abbreviated, $locale);
        $this->assertArraySubset($subset, $ranks);
    }

    public function getRanksProvider()
    {
        return [
            ['en', true, [
                User::RANK_RECRUIT => 'recr',
                User::RANK_SERGEANT => 'sgt',
                User::RANK_FIRST_SERGEANT => 'sfc',
                User::RANK_MAJOR => 'maj',
                User::RANK_GENERAL => 'gen',
            ],
            ],
            ['fr', true, [
                User::RANK_RECRUIT => 'recr',
                User::RANK_SERGEANT => 'sgt',
                User::RANK_FIRST_SERGEANT => 'sgtm chef',
                User::RANK_MAJOR => 'maj',
                User::RANK_GENERAL => 'gen',
            ],
            ],
            ['de', false, [
                User::RANK_RECRUIT => 'rekrut',
                User::RANK_SERGEANT => 'wachtmeister',
                User::RANK_FIRST_SERGEANT => 'hauptfeldweibel',
                User::RANK_MAJOR => 'major',
                User::RANK_GENERAL => 'general',
            ],
            ],
            ['it', false, [
                User::RANK_RECRUIT => 'recluta',
                User::RANK_SERGEANT => 'sergente',
                User::RANK_FIRST_SERGEANT => 'sergente maggiore capo',
                User::RANK_MAJOR => 'maggiore',
                User::RANK_GENERAL => 'generale',
            ],
            ],
        ];
    }
}

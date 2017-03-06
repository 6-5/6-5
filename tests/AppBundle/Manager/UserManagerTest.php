<?php

namespace Tests\AppBundle\Manager;

use AppBundle\Entity\User;
use AppBundle\Manager\UserManager;

class UserManagerTest extends \PHPUnit\Framework\TestCase
{
    /** @var  UserManager */
    private $userManager;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $translator;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $requestStack;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $request;

    protected function SetUp()
    {
        $this->translator = $this->getMockBuilder('Symfony\Component\Translation\Translator')
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestStack = $this->getMockBuilder('Symfony\Component\HttpFoundation\RequestStack')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock();


        $this->userManager = new UserManager($this->translator, $this->requestStack);
    }

    /**
     * @dataProvider getFullNameProvider
     */
    public function testGetFullName($rank, $firstname, $lastname, $abbreviated, $locale, $translated, $expected)
    {
        $this->translator
            ->expects($this->any())
            ->method('trans')
            ->willReturn($translated);

        $this->requestStack
            ->expects($this->any())
            ->method('getCurrentRequest')
            ->willReturn($this->request);

        $this->request
            ->expects($this->any())
            ->method('getLocale')
            ->willReturn('en');

        // Create some users
        $user = new User();
        $user->setRank($rank);
        $user->setFirstname($firstname);
        $user->setLastname($lastname);

        $fullname = $this->userManager->getFullName($user, $abbreviated, $locale);
        $this->assertEquals($expected, $fullname);
    }

    public function getFullNameProvider()
    {
        return [
            [User::RANK_PRIVATE, 'Dany', 'Maillard', true, 'fr', 'sdt', 'sdt MAILLARD Dany'],
            [User::RANK_PRIVATE, 'Dany', 'Maillard', false, 'fr', 'soldat', 'soldat MAILLARD Dany'],
            [User::RANK_QUARTERMASTER_SERGEANT, 'Gabriel', 'Freitas', true, 'de', 'four', 'four FREITAS Gabriel'],
            [User::RANK_QUARTERMASTER_SERGEANT, 'Gabriel', 'Freitas', false, 'de', 'fourier', 'fourier FREITAS Gabriel'],
            [User::RANK_CAPTAIN, 'Sébastien', 'Pahud', true, 'it', 'cap', 'cap PAHUD Sébastien'],
            [User::RANK_CAPTAIN, 'Sébastien', 'Pahud', false, 'it', 'capitano', 'capitano PAHUD Sébastien'],
            [User::RANK_WARRANT_OFFICER_CLASS_2, 'John', 'Doe', false, 'br', 'wo2', 'wo2 DOE John'],
            [User::RANK_WARRANT_OFFICER_CLASS_2, 'John', 'Doe', false, null, 'warrant officer class 2', 'warrant officer class 2 DOE John'],
        ];
    }
}

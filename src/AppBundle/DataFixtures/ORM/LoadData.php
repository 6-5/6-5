<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $userManager = $this->container->get('fos_user.user_manager');
        $grades = [User::GRADE_COLONEL, User::GRADE_CAPTAIN, User::GRADE_LIEUTENANT, User::GRADE_SOLDIER] ;
        $users = [User::GRADE_COLONEL => [], User::GRADE_CAPTAIN => [], User::GRADE_LIEUTENANT => [],  User::GRADE_SOLDIER => []];

        foreach ($grades as $k => $grade) {
            for ($i = 0; $i < pow(3, $k); $i++) {
                /** @var User $user */
                $users[$grade][] = $user = $userManager->createUser();
                $user->setFirstname($faker->firstName);
                $user->setLastname($faker->lastName);
                $user->setGrade($grade);

                $user->setUsername($grade.$i);
                $user->setPlainPassword($grade.$i);
                $user->setEmail($grade.$i.'@6-5.ch');
                $user->addRole('ROLE_USER');
                $user->setEnabled(true);

                if ($k > 0) {
                    $user->setSupervisedBy($faker->randomElement($users[$grades[$k - 1]]));
                }

                $userManager->updateUser($user, true);
            }
        }

        $manager->flush();
    }
}

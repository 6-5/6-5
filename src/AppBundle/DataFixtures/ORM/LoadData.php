<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Decision;
use AppBundle\Entity\Report;
use AppBundle\Entity\User;
use AppBundle\Manager\ReportManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadData implements FixtureInterface, ContainerAwareInterface
{
    /** @var Generator */
    protected $faker;
    /** @var  ReportManager */
    protected $reportManager;

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
        $this->faker = \Faker\Factory::create();
        $userManager = $this->container->get('fos_user.user_manager');
        $this->reportManager = $this->container->get('app.report_manager');
        $grades = [User::GRADE_COLONEL, User::GRADE_CAPTAIN, User::GRADE_LIEUTENANT, User::GRADE_SOLDIER] ;
        $users = [User::GRADE_COLONEL => [], User::GRADE_CAPTAIN => [], User::GRADE_LIEUTENANT => [],  User::GRADE_SOLDIER => []];

        // User
        foreach ($grades as $k => $grade) {
            for ($i = 0; $i < pow(3, $k); $i++) {
                $users[$grade][] = $user = $userManager->createUser();
                $user->setFirstname($this->faker->firstName);
                $user->setLastname($this->faker->lastName);
                $user->setGrade($grade);

                $user->setUsername($grade.$i);
                $user->setPlainPassword($grade.$i);
                $user->setEmail($grade.$i.'@6-5.ch');
                $user->addRole('ROLE_USER');
                $user->setEnabled(true);

                if ($k > 0) {
                    $user->setSupervisedBy($this->faker->randomElement($users[$grades[$k - 1]]));
                }

                $userManager->updateUser($user, true);
            }
        }

        // Report
        /** @var User $soldier */
        foreach ($users[User::GRADE_SOLDIER] as $soldier) {
            // draft
            $report = $this->createReport($soldier);
            $manager->persist($report);

            // report without decision but 50% readed
            $addressTo = $this->faker->randomElement($this->faker->randomElement($users));
            $report = $this->createReport($soldier, $addressTo);
            $report = $this->decide($report, $this->faker->boolean());
            $manager->persist($report);

            // report with accepted/refused decision
            $addressTo = $this->faker->randomElement($this->faker->randomElement($users));
            $state = $this->faker->boolean() ? Decision::STATE_ACCEPTED : Decision::STATE_REFUSED;
            $report = $this->createReport($soldier, $addressTo);
            $report = $this->decide($report, true, $state);
            $manager->persist($report);

            // report with transferred decision
            /** @var User $addressTo */
            $addressTo = $this->faker->randomElement($this->faker->randomElement($users));
            $transferTo = $addressTo->getSupervisedBy();
            if ($transferTo) {
                $report = $this->createReport($soldier, $addressTo);
                $report = $this->decide($report, true, Decision::STATE_TRANSFERRED, $transferTo);
                $manager->persist($report);
            }

            // report with hierarchical transferred decision
            /** @var User $addressTo */
            $report = $this->createReport($soldier, $soldier->getSupervisedBy());
            $report->setIsHierarchical(true);
            $report = $this->decide($report, true, Decision::STATE_TRANSFERRED, $soldier->getSupervisedBy()->getSupervisedBy());
            $manager->persist($report);
        }

        $manager->flush();
    }

    public function createReport(User $createdBy, User $addressTo = null)
    {
        $report = ($this->reportManager->createReport())
            ->setCreatedBy($createdBy)
            ->setCreatedAt($createdAt = $this->faker->dateTimeBetween('-14 days', '-7 days'))
            ->setObject(ucfirst($this->faker->words(3, true)))
        ;

        if ($addressTo) {
            $report = $this->reportManager->addressTo($report, $addressTo);
        }

        return $report;
    }

    private function decide(Report $report, $readed = false, $state = null, User $newUserIfTransferred = null)
    {
        $createdAt = $report->getLastDecision()->getCreatedAt();
        $readedAt = null;
        if ($readed) {
            $this->reportManager->read($report, $readedAt = $this->faker->dateTimeBetween($createdAt, $createdAt->modify('+ 1 days')));
        }

        if ($state) {
            $decidedAt = $this->faker->dateTimeBetween($readedAt, $readedAt->modify('+ 1 days'));
            switch ($state) {
                case Decision::STATE_ACCEPTED:
                    $report = $this->reportManager->decideToAccept($report, $this->faker->realText(), $decidedAt);
                    break;
                case Decision::STATE_REFUSED:
                    $report = $this->reportManager->decideToRefuse($report, $this->faker->realText(), $decidedAt);
                    break;
                case Decision::STATE_TRANSFERRED:
                    $report = $this->reportManager->decideToTransfer($report, $newUserIfTransferred, $this->faker->realText(), $decidedAt);
                    $report = $this->decide($report, $readed = $this->faker->boolean());
                    if ($readed && $this->faker->boolean()) {
                        $state = $this->faker->boolean() ? Decision::STATE_ACCEPTED : Decision::STATE_REFUSED;
                        $report = $this->decide($report, true, $state);
                    }
                    break;
            }
        }

        return $report;
    }
}

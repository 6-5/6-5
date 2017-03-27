<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Decision;
use AppBundle\Entity\File;
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
        $ranks = [User::RANK_COLONEL, User::RANK_CAPTAIN, User::RANK_SECOND_LIEUTENANT, User::RANK_PRIVATE] ;
        $users = [User::RANK_COLONEL => [], User::RANK_CAPTAIN => [], User::RANK_SECOND_LIEUTENANT => [],  User::RANK_PRIVATE => []];

        // User
        foreach ($ranks as $k => $rank) {
            for ($i = 0; $i < pow(3, $k); $i++) {
                $users[$rank][] = $user = $userManager->createUser();
                $user->setFirstname($this->faker->firstName);
                $user->setLastname($this->faker->lastName);
                $user->setRank($rank);

                $user->setUsername($rank.$i);
                $user->setPlainPassword($rank.$i);
                $user->setEmail($rank.$i.'@6-5.ch');
                $user->addRole('ROLE_USER');
                $user->setEnabled(true);

                if ($k > 0) {
                    $user->setSupervisedBy($this->faker->randomElement($users[$ranks[$k - 1]]));
                }

                $userManager->updateUser($user, true);
            }
        }

        // Report
        /** @var User $private */
        foreach ($users[User::RANK_PRIVATE] as $private) {
            for ($i = 0; $i < $this->faker->numberBetween(2, 5); $i++) {
                // draft
                $report = $this->createReport($private);
                $manager->persist($report);

                    // report without decision but 50% readed

                    $addressTo = $this->faker->randomElement($this->faker->randomElement($users));
                    $report = $this->createReport($private, $addressTo);
                    $report = $this->decide($report, $this->faker->boolean());
                    $manager->persist($report);

                // report with transferred decision
                /** @var User $addressTo */
                $addressTo = $this->faker->randomElement($this->faker->randomElement($users));
                $transferTo = $addressTo->getSupervisedBy();
                if ($transferTo) {
                    $report = $this->createReport($private, $addressTo);
                    $report = $this->decide($report, true, Report::STATUS_TRANSFERRED, $transferTo);
                    $manager->persist($report);
                }

                // report with accepted/refused decision
                $addressTo = $this->faker->randomElement($this->faker->randomElement($users));
                $status = $this->faker->boolean() ? Report::STATUS_ACCEPTED : Report::STATUS_REFUSED;
                $report = $this->createReport($private, $addressTo);
                $report = $this->decide($report, true, $status);
                $manager->persist($report);

                // report with hierarchical transferred decision
                /** @var User $addressTo */
                $report = $this->createReport($private, $private->getSupervisedBy());
                $report->setIsHierarchical(true);
                $report = $this->decide($report, true, Report::STATUS_TRANSFERRED, $private->getSupervisedBy()->getSupervisedBy());
                $manager->persist($report);
            }
        }

        $manager->flush();
    }

    public function createReport(User $createdBy, User $addressTo = null)
    {
        $report = ($this->reportManager->createReport($createdBy))
            ->setCreatedAt($createdAt = $this->faker->dateTimeBetween('-14 days', '-7 days'))
            ->setObject(ucfirst($this->faker->words($this->faker->numberBetween(5, 10), true)))
            ->setMessage($this->faker->paragraph())
            ->setStartedAt($this->faker->dateTimeBetween('+ 1 day', '+ 14 days'))
            ->setPlace($this->faker->city)
        ;

        $file = new File();
        $report->addFile($file);

        if ($addressTo) {
            $report = $this->reportManager->addressTo($report, $addressTo);
        }

        return $report;
    }

    private function decide(Report $report, $readed = false, $status = null, User $newUserIfTransferred = null)
    {
        $createdAt = $report->getLastDecision()->getCreatedAt();
        $readedAt = null;
        if ($readed) {
            $report = $this->reportManager->read($report, $readedAt = $this->faker->dateTimeBetween($createdAt, $createdAt->modify('+ 1 days')));
        }

        if ($status) {
            $decidedAt = $this->faker->dateTimeBetween($readedAt, $readedAt->modify('+ 1 days'));
            switch ($status) {
                case Report::STATUS_ACCEPTED:
                    $report = $this->reportManager->decideToAccept($report, $this->faker->realText(), $decidedAt);
                    break;
                case Report::STATUS_REFUSED:
                    $report = $this->reportManager->decideToRefuse($report, $this->faker->realText(), $decidedAt);
                    break;
                case Report::STATUS_TRANSFERRED:
                    $report = $this->reportManager->decideToTransfer($report, $newUserIfTransferred, $this->faker->realText(), $decidedAt);
                    $report = $this->decide($report, $readed = $this->faker->boolean());
                    if ($readed && $this->faker->boolean()) {
                        $status = $this->faker->boolean() ? Report::STATUS_ACCEPTED : Report::STATUS_REFUSED;
                        $report = $this->decide($report, true, $status);
                    }
                    break;
            }
        }

        return $report;
    }
}

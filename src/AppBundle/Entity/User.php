<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    const GRADE_RECRUIT = 'recruit';
    const GRADE_SOLDIER = 'soldier';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text", length=64)
     */
    protected $grade;

    /**
     * One Category has Many Categories.
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="supervisedBy")
     */
    protected $subordinates;

    /**
     * Many Categories have One Category.
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="subordinates")
     */
    protected $supervisedBy;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Report", mappedBy="createdBy")
     */
    protected $reports;

    /**
     * Set grade
     *
     * @param string $grade
     *
     * @return User
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return string
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Add subordinate
     *
     * @param \AppBundle\Entity\User $subordinate
     *
     * @return User
     */
    public function addSubordinate(\AppBundle\Entity\User $subordinate)
    {
        $this->subordinates[] = $subordinate;
        $subordinate->setSupervisedBy($this);

        return $this;
    }

    /**
     * Remove subordinate
     *
     * @param \AppBundle\Entity\User $subordinate
     */
    public function removeSubordinate(\AppBundle\Entity\User $subordinate)
    {
        $this->subordinates->removeElement($subordinate);
    }

    /**
     * Get subordinates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubordinates()
    {
        return $this->subordinates;
    }

    /**
     * Set supervisedBy
     *
     * @param \AppBundle\Entity\User $supervisedBy
     *
     * @return User
     */
    public function setSupervisedBy(\AppBundle\Entity\User $supervisedBy = null)
    {
        $this->supervisedBy = $supervisedBy;

        return $this;
    }

    /**
     * Get supervisedBy
     *
     * @return \AppBundle\Entity\User
     */
    public function getSupervisedBy()
    {
        return $this->supervisedBy;
    }

    /**
     * Add report
     *
     * @param \AppBundle\Entity\Report $report
     *
     * @return User
     */
    public function addReport(\AppBundle\Entity\Report $report)
    {
        $this->reports[] = $report;
        $report->setCreatedBy($report);

        return $this;
    }

    /**
     * Remove report
     *
     * @param \AppBundle\Entity\Report $report
     */
    public function removeReport(\AppBundle\Entity\Report $report)
    {
        $this->reports->removeElement($report);
    }

    /**
     * Get reports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReports()
    {
        return $this->reports;
    }
}

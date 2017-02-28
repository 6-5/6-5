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
    protected $createdReports;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Report", mappedBy="addressedTo")
     */
    protected $addressedReports;

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
     * Add createdReport
     *
     * @param \AppBundle\Entity\Report $createdReport
     *
     * @return User
     */
    public function addCreatedReport(\AppBundle\Entity\Report $createdReport)
    {
        $this->createdReports[] = $createdReport;
        $createdReport->setCreatedBy($this);

        return $this;
    }

    /**
     * Remove createdReport
     *
     * @param \AppBundle\Entity\Report $createdReport
     */
    public function removeCreatedReport(\AppBundle\Entity\Report $createdReport)
    {
        $this->createdReports->removeElement($createdReport);
    }

    /**
     * Get createdReports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCreatedReports()
    {
        return $this->createdReports;
    }

    /**
     * Add addressedReport
     *
     * @param \AppBundle\Entity\Report $addressedReport
     *
     * @return User
     */
    public function addAddressedReport(\AppBundle\Entity\Report $addressedReport)
    {
        $this->addressedReports[] = $addressedReport;
        $addressedReport->setAddressedTo($this);

        return $this;
    }

    /**
     * Remove addressedReport
     *
     * @param \AppBundle\Entity\Report $addressedReport
     */
    public function removeAddressedReport(\AppBundle\Entity\Report $addressedReport)
    {
        $this->addressedReports->removeElement($addressedReport);
    }

    /**
     * Get addressedReports
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddressedReports()
    {
        return $this->addressedReports;
    }
}

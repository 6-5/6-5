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
    const GRADE_LIEUTENANT = 'lieutenant';
    const GRADE_CAPTAIN = 'captain';
    const GRADE_COLONEL = 'colonel';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=31)
     */
    protected $grade;

    /**
     * @ORM\Column(type="string", length=31)
     */
    protected $firstname;

    /**
     * @ORM\Column(type="string", length=31)
     */
    protected $lastname;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="supervisedBy", )
     */
    protected $subordinates;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="subordinates", cascade={"remove"})
     * @ORM\JoinColumn(onDelete="SET NULL")
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Desicion", mappedBy="user")
     */
    private $decisions;

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

    /**
     * Add decision
     *
     * @param \AppBundle\Entity\Desicion $decision
     *
     * @return User
     */
    public function addDecision(\AppBundle\Entity\Desicion $decision)
    {
        $this->decisions[] = $decision;
        $decision->setUser($this);

        return $this;
    }

    /**
     * Remove decision
     *
     * @param \AppBundle\Entity\Desicion $decision
     */
    public function removeDecision(\AppBundle\Entity\Desicion $decision)
    {
        $this->decisions->removeElement($decision);
    }

    /**
     * Get decisions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDecisions()
    {
        return $this->decisions;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }
}

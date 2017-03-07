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
    /**
     * English names based upon (consulted on 2017-03-01):
     * https://en.wikipedia.org/wiki/Military_ranks_of_the_Swiss_Armed_Forces
     *
     * Nonexistant equivalent or multiple equivalents have been adapted to avoid collisions
     */

    const RANK_RECRUIT = 'recruit';
    const RANK_PRIVATE = 'private';
    const RANK_APPOINTEE = 'appointee';
    const RANK_PRIVATE_FIRST_CLASS = 'private_first_class';
    const RANK_CORPORAL = 'corporal';
    const RANK_SERGEANT = 'sergeant';
    const RANK_CHIEF_SERGEANT = 'chief_sergeant';
    const RANK_MASTER_SERGEANT = 'master_sergeant';
    const RANK_QUARTERMASTER_SERGEANT = 'quartermaster_sergeant';
    const RANK_FIRST_SERGEANT = 'first_sergeant';
    const RANK_WARRANT_OFFICER_CLASS_4 = 'warrant_officer_class_4';
    const RANK_WARRANT_OFFICER_CLASS_3 = 'warrant_officer_class_3';
    const RANK_WARRANT_OFFICER_CLASS_2 = 'warrant_officer_class_2';
    const RANK_WARRANT_OFFICER_CLASS_1 = 'warrant_officer_class_1';
    const RANK_SECOND_LIEUTENANT = 'second_lieutenant';
    const RANK_FIRST_LIEUTENANT = 'first_lieutenant';
    const RANK_CAPTAIN = 'captain';
    const RANK_MAJOR = 'major';
    const RANK_LIEUTENANT_COLONEL = 'lieutenant_colonel';
    const RANK_COLONEL = 'colonel';
    const RANK_SPECIALIST_OFFICER = 'specialist_officer';
    const RANK_BRIGADIER_GENERAL = 'brigadier_general';
    const RANK_MAJOR_GENERAL = 'major_general';
    const RANK_LIEUTENANT_GENERAL = 'lieutenant_general';
    const RANK_GENERAL = 'general';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=31)
     */
    protected $rank;

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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Decision", mappedBy="user")
     */
    private $decisions;

    /**
     * Set rank
     *
     * @param string $rank
     *
     * @return User
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return string
     */
    public function getRank()
    {
        return $this->rank;
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
     * @param \AppBundle\Entity\Decision $decision
     *
     * @return User
     */
    public function addDecision(\AppBundle\Entity\Decision $decision)
    {
        $this->decisions[] = $decision;
        $decision->setUser($this);

        return $this;
    }

    /**
     * Remove decision
     *
     * @param \AppBundle\Entity\Decision $decision
     */
    public function removeDecision(\AppBundle\Entity\Decision $decision)
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

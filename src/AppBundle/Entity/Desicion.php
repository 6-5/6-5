<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Desicion
 *
 * @ORM\Table(name="desicion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DesicionRepository")
 */
class Desicion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="readedAt", type="datetime")
     */
    private $readedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="decidedAt", type="datetime")
     */
    private $decidedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=15)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Report", inversedBy="decisions")
     */
    private $report;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="decisions")
     */
    private $user;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Desicion
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set readedAt
     *
     * @param \DateTime $readedAt
     *
     * @return Desicion
     */
    public function setReadedAt($readedAt)
    {
        $this->readedAt = $readedAt;

        return $this;
    }

    /**
     * Get readedAt
     *
     * @return \DateTime
     */
    public function getReadedAt()
    {
        return $this->readedAt;
    }

    /**
     * Set decidedAt
     *
     * @param \DateTime $decidedAt
     *
     * @return Desicion
     */
    public function setDecidedAt($decidedAt)
    {
        $this->decidedAt = $decidedAt;

        return $this;
    }

    /**
     * Get decidedAt
     *
     * @return \DateTime
     */
    public function getDecidedAt()
    {
        return $this->decidedAt;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return Desicion
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set comment
     *
     * @param string $comment
     *
     * @return Desicion
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set report
     *
     * @param \AppBundle\Entity\Report $report
     *
     * @return Desicion
     */
    public function setReport(\AppBundle\Entity\Report $report = null)
    {
        $this->report = $report;

        return $this;
    }

    /**
     * Get report
     *
     * @return \AppBundle\Entity\Report
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Desicion
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}

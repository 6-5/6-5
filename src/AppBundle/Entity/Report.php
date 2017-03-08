<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Workflow\Marking;

/**
 * Report
 *
 * @ORM\Table(name="report")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ReportRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Report
{
    const URGENCY_LOW = 'low';
    const URGENCY_NORMAL = 'normal';
    const URGENCY_HIGH = 'high';
    const URGENCY_CRITICAL = 'critical';

    const CLASSIFICATION_UNCLASSIFIED = 'unclassified';
    const CLASSIFICATION_INTERN = 'intern';
    const CLASSIFICATION_CONFIDENTIAL = 'confidential';
    const CLASSIFICATION_SECRET = 'secret';

    const STATUS_DRAFT = 'draft';
    const STATUS_ADDRESSED = 'addressed';
    const STATUS_READED = 'readed';
    const STATUS_TRANSFERRED = 'transferred';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REFUSED = 'refused';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="createdReports")
     * @Assert\NotBlank(groups={"Default", "Draft"})
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="addressedReports")
     * @Assert\NotBlank()
     */
    private $addressedTo;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=6, unique=true)
     * @Assert\NotBlank(groups={"Default", "Draft"})
     */
    private $reference;

    /**
     * @var bool
     *
     * @ORM\Column(name="isHierarchical", type="boolean")
     */
    private $isHierarchical;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=255)
     * @Assert\NotBlank(groups={"Default", "Draft"})
     * @Assert\Length(max="255", groups={"Default", "Draft"})
     */
    private $object;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startedAt", type="datetime", nullable=true)
     * @Assert\NotBlank()
     */
    private $startedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="place", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $place;

    /**
     * @var string
     *
     * @ORM\Column(name="urgency", type="string", length=15)
     * @Assert\NotBlank(groups={"Default", "Draft"})
     */
    private $urgency;

    /**
     * @var string
     *
     * @ORM\Column(name="classification", type="string", length=15)
     * @Assert\NotBlank(groups={"Default", "Draft"})
     */
    private $classification;

    /**
     * @var bool
     *
     * @ORM\Column(name="isDraft", type="boolean")
     */
    private $isDraft;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\NotBlank(groups={"Default", "Draft"})
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\File", mappedBy="report", cascade={"all"})
     */
    private $files;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Decision", mappedBy="report", cascade={"all"})
     */
    private $decisions;

    public $marking;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();
        $this->decisions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->urgency = self::URGENCY_NORMAL;
        $this->classification = self::CLASSIFICATION_INTERN;
        $this->createdAt = new \DateTime();
        $this->isDraft = true;
        $this->isHierarchical = false;
        $this->startedAt = new \DateTime();
    }

    /**
     * @ORM\PostLoad()
     */
    public function getMarking()
    {
        return $this->marking = (new Marking([$this->getStatus() => 1]))->getPlaces();
    }

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
     * Set reference
     *
     * @param string $reference
     *
     * @return Report
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set isHierarchical
     *
     * @param boolean $isHierarchical
     *
     * @return Report
     */
    public function setIsHierarchical($isHierarchical)
    {
        $this->isHierarchical = $isHierarchical;

        return $this;
    }

    /**
     * Get isHierarchical
     *
     * @return bool
     */
    public function getIsHierarchical()
    {
        return $this->isHierarchical;
    }

    /**
     * Set object
     *
     * @param string $object
     *
     * @return Report
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Report
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set startedAt
     *
     * @param \DateTime $startedAt
     *
     * @return Report
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return \DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set place
     *
     * @param string $place
     *
     * @return Report
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set urgency
     *
     * @param string $urgency
     *
     * @return Report
     */
    public function setUrgency($urgency)
    {
        $this->urgency = $urgency;

        return $this;
    }

    /**
     * Get urgency
     *
     * @return string
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * Set classification
     *
     * @param string $classification
     *
     * @return Report
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;

        return $this;
    }

    /**
     * Get classification
     *
     * @return string
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return Report
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get isDraft
     *
     * @return bool
     */
    public function isDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Report
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
     * Set createdBy
     *
     * @param \AppBundle\Entity\User $createdBy
     *
     * @return Report
     */
    public function setCreatedBy(\AppBundle\Entity\User $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \AppBundle\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set addressedTo
     *
     * @param \AppBundle\Entity\User $addressedTo
     *
     * @return Report
     */
    public function setAddressedTo(\AppBundle\Entity\User $addressedTo = null)
    {
        $this->addressedTo = $addressedTo;

        return $this;
    }

    /**
     * Get addressedTo
     *
     * @return \AppBundle\Entity\User
     */
    public function getAddressedTo()
    {
        return $this->addressedTo;
    }

    /**
     * Add file
     *
     * @param \AppBundle\Entity\File $file
     *
     * @return Report
     */
    public function addFile(\AppBundle\Entity\File $file)
    {
        $this->files[] = $file;
        $file->setReport($this);

        return $this;
    }

    /**
     * Remove file
     *
     * @param \AppBundle\Entity\File $file
     */
    public function removeFile(\AppBundle\Entity\File $file)
    {
        $this->files->removeElement($file);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Add decision
     *
     * @param \AppBundle\Entity\Decision $decision
     *
     * @return Report
     */
    public function addDecision(\AppBundle\Entity\Decision $decision)
    {
        $this->decisions[] = $decision;
        $decision->setReport($this);

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
     * @return Decision
     */
    public function getLastDecision()
    {
        return $this->decisions->last();
    }

    public function getStatus()
    {
        if ($this->isDraft()) {
            return Report::STATUS_DRAFT;
        }

        return $this->getLastDecision()->getStatus();
    }
}

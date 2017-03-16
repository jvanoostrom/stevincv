<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * @ORM\Entity
 * @ORM\Table(name="education")
 */
class Education
{
    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Vul de naam van de opleiding in.")
     *
     */
    protected $educationName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     */
    protected $educationSpecialisation = null;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Vul het opleidingsinstituut in.")
     *
     */
    protected $educationInstitute;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\DateTime()
     * @Assert\NotBlank(message="Vul de startdatum in.")
     */
    protected $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     *
     * @Assert\DateTime()
     */
    protected $endDate = null;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set educationName
     *
     * @param string $educationName
     *
     * @return Education
     */
    public function setEducationName($educationName)
    {
        $this->educationName = $educationName;

        return $this;
    }

    /**
     * Get educationName
     *
     * @return string
     */
    public function getEducationName()
    {
        return $this->educationName;
    }

    /**
     * Set educationSpecialisation
     *
     * @param string $educationSpecialisation
     *
     * @return Education
     */
    public function setEducationSpecialisation($educationSpecialisation)
    {
        $this->educationSpecialisation = $educationSpecialisation;

        return $this;
    }

    /**
     * Get educationSpecialisation
     *
     * @return string
     */
    public function getEducationSpecialisation()
    {
        return $this->educationSpecialisation;
    }

    /**
     * Set educationInstitute
     *
     * @param string $educationInstitute
     *
     * @return Education
     */
    public function setEducationInstitute($educationInstitute)
    {
        $this->educationInstitute = $educationInstitute;

        return $this;
    }

    /**
     * Get educationInstitute
     *
     * @return string
     */
    public function getEducationInstitute()
    {
        return $this->educationInstitute;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Education
     */
    public function setStartDate(\DateTime $startDate = null)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return Education
     */
    public function setEndDate(\DateTime $endDate = null)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Education
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Education
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

    /**
     * @Assert\Callback
     */
    public function validateEndDate(ExecutionContextInterface $context, $payload)
    {
        if ($this->getEndDate() != null)
        {
            if($this->getEndDate() < $this->getStartDate())
            {
                $context->buildViolation('De einddatum moet na de startdatum liggen.')
                    ->atPath('endDate')
                    ->addViolation();
            }
        }
    }

}

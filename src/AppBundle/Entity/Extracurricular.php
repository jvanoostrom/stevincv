<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="extracurricular")
 */
class Extracurricular
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
     * @Assert\NotBlank(message="Vul de titel van de nevenactiviteit in.")
     *
     */
    protected $extracurricularName;

    /**
     * @ORM\Column(type="date")
     * @Assert\DateTime()
     * @Assert\NotBlank(message="Vul de startdatum in.")
     */
    protected $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
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
     * Set extracurricularName
     *
     * @param string $extracurricularName
     *
     * @return Extracurricular
     */
    public function setExtracurricularName($extracurricularName)
    {
        $this->extracurricularName = $extracurricularName;

        return $this;
    }

    /**
     * Get extracurricularName
     *
     * @return string
     */
    public function getExtracurricularName()
    {
        return $this->extracurricularName;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Extracurricular
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
     * @return Extracurricular
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
     * @return Extracurricular
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
     * @return Extracurricular
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

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="certificate")
 */
class Certificate
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
     * @Assert\NotBlank(message="Vul de naam van het certificaat in.")
     *
     */
    protected $certificateName;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Vul het opleidingsinstituut in.")
     *
     */
    protected $certificateInstitute;

    /**
     * @ORM\Column(type="date")
     * @Assert\DateTime()
     * @Assert\NotBlank(message="Vul de behaalde datum in.")
     */
    protected $obtainedDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
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
     * Set certificateName
     *
     * @param string $certificateName
     *
     * @return Certificate
     */
    public function setCertificateName($certificateName)
    {
        $this->certificateName = $certificateName;

        return $this;
    }

    /**
     * Get certificateName
     *
     * @return string
     */
    public function getCertificateName()
    {
        return $this->certificateName;
    }

    /**
     * Set certificateInstitute
     *
     * @param string $certificateInstitute
     *
     * @return Certificate
     */
    public function setCertificateInstitute($certificateInstitute)
    {
        $this->certificateInstitute = $certificateInstitute;

        return $this;
    }

    /**
     * Get certificateInstitute
     *
     * @return string
     */
    public function getCertificateInstitute()
    {
        return $this->certificateInstitute;
    }

    /**
     * Set obtainedDate
     *
     * @param \DateTime $obtainedDate
     *
     * @return Certificate
     */
    public function setObtainedDate(\DateTime $obtainedDate = null)
    {
        $this->obtainedDate = $obtainedDate;

        return $this;
    }

    /**
     * Get obtainedDate
     *
     * @return \DateTime
     */
    public function getObtainedDate()
    {
        return $this->obtainedDate;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Certificate
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
     * @return Certificate
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

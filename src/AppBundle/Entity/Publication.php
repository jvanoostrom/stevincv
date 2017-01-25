<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="publication")
 */
class Publication
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
     * @Assert\NotBlank(message="Vul de titel van de publicatie in.", groups={"Publication"})
     *
     */
    protected $publicationTitle;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Vul het publicatiemedium in.", groups={"Publication"})
     *
     */
    protected $publicationJournal;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank(message="Vul de datum van de publicatie in.", groups={"Publication"})
     */
    protected $publishedDate;

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
     * Set publicationTitle
     *
     * @param string $publicationTitle
     *
     * @return Publication
     */
    public function setPublicationTitle($publicationTitle)
    {
        $this->publicationTitle = $publicationTitle;

        return $this;
    }

    /**
     * Get publicationTitle
     *
     * @return string
     */
    public function getPublicationTitle()
    {
        return $this->publicationTitle;
    }

    /**
     * Set publicationJournal
     *
     * @param string $publicationJournal
     *
     * @return Publication
     */
    public function setPublicationJournal($publicationJournal)
    {
        $this->publicationJournal = $publicationJournal;

        return $this;
    }

    /**
     * Get publicationJournal
     *
     * @return string
     */
    public function getPublicationJournal()
    {
        return $this->publicationJournal;
    }

    /**
     * Set publishedDate
     *
     * @param \DateTime $publishedDate
     *
     * @return Publication
     */
    public function setPublishedDate(\DateTime $publishedDate)
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    /**
     * Get publishedDate
     *
     * @return \DateTime
     */
    public function getPublishedDate()
    {
        return $this->publishedDate;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Publication
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
     * @return Publication
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

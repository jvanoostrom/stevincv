<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile")
 */
class Profile
{
    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="profile_tag",
     *      joinColumns={@ORM\JoinColumn(name="profile_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    private $tags;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Vul de korte beschrijving in.", groups={"Profile"})
     *
     */
    protected $shortDescription;

    /**
     * @ORM\Column(type="string",)
     *
     * @Assert\NotBlank(message="Vul de quote in.", groups={"Profile"})
     *
     */
    protected $quoteLine;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="Vul de profieltekst in.", groups={"Profile"})
     *
     */
    protected $profileText;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->tags = new ArrayCollection();
    }

    /**
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set quoteLine
     *
     * @param string $quoteLine
     *
     * @return Profile
     */
    public function setQuoteLine($quoteLine)
    {
        $this->quoteLine = $quoteLine;

        return $this;
    }

    /**
     * Get quoteLine
     *
     * @return string
     */
    public function getQuoteLine()
    {
        return $this->quoteLine;
    }

    /**
     * Set profileText
     *
     * @param string $profileText
     *
     * @return Profile
     */
    public function setProfileText($profileText)
    {
        $this->profileText = $profileText;

        return $this;
    }

    /**
     * Get profileText
     *
     * @return string
     */
    public function getProfileText()
    {
        return $this->profileText;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Profile
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
     * @return Profile
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
     * Set shortDescription
     *
     * @param string $shortDescription
     *
     * @return Profile
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Remove tag
     *
     * @param \AppBundle\Entity\Tag $tag
     */
    public function removeTag(\AppBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add tag
     *
     * @param \AppBundle\Entity\Tag $tag
     *
     * @return Profile
     */
    public function addTag(\AppBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }
}

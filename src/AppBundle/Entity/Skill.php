<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="skill")
 */
class Skill
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
     * @ORM\Column(type="string", length=30)
     *
     * @Assert\NotBlank(message="Vul een competentie in.")
     *
     */
    protected $skillText;

    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\NotBlank(message="Vul een gewicht in.")
     *
     */
    protected $skillWeight;

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
     * Set skillText
     *
     * @param string $skillText
     *
     * @return Skill
     */
    public function setSkillText($skillText)
    {
        $this->skillText = $skillText;

        return $this;
    }

    /**
     * Get skillText
     *
     * @return string
     */
    public function getSkillText()
    {
        return $this->skillText;
    }

    /**
     * Set skillWeight
     *
     * @param integer $skillWeight
     *
     * @return Skill
     */
    public function setSkillWeight($skillWeight)
    {
        $this->skillWeight = $skillWeight;

        return $this;
    }

    /**
     * Get skillWeight
     *
     * @return integer
     */
    public function getSkillWeight()
    {
        return $this->skillWeight;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Skill
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

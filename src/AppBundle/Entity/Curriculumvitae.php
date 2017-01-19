<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="curriculumvitae")
 */
class Curriculumvitae
{
    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Profiel")
     */
    private $profiel;

    /**
     * @ORM\ManyToMany(targetEntity="Project")
     * @ORM\JoinTable(name="curriculumvitae_project",
     *      joinColumns={@ORM\JoinColumn(name="cv_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")}
     *      )
     */
    private $projects;


    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Vul de naam van het cv in.", groups={"Curriculumvitae"})
     *
     */
    protected $curriculumvitaeName;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->projects = new ArrayCollection();
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Curriculumvitae
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
     * @return Curriculumvitae
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
     * Set profiel
     *
     * @param \AppBundle\Entity\Profiel $profiel
     *
     * @return Curriculumvitae
     */
    public function setProfiel(\AppBundle\Entity\Profiel $profiel = null)
    {
        $this->profiel = $profiel;

        return $this;
    }

    /**
     * Get profiel
     *
     * @return \AppBundle\Entity\Profiel
     */
    public function getProfiel()
    {
        return $this->profiel;
    }

    /**
     * Set curriculumvitaeName
     *
     * @param string $curriculumvitaeName
     *
     * @return Curriculumvitae
     */
    public function setCurriculumvitaeName($curriculumvitaeName)
    {
        $this->curriculumvitaeName = $curriculumvitaeName;

        return $this;
    }

    /**
     * Get curriculumvitaeName
     *
     * @return string
     */
    public function getCurriculumvitaeName()
    {
        return $this->curriculumvitaeName;
    }

    /**
     * Add project
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return Curriculumvitae
     */
    public function addProject(\AppBundle\Entity\Project $project)
    {
        $this->projects[] = $project;

        return $this;
    }

    /**
     * Remove project
     *
     * @param \AppBundle\Entity\Project $project
     */
    public function removeProject(\AppBundle\Entity\Project $project)
    {
        $this->projects->removeElement($project);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjects()
    {
        return $this->projects;
    }
}

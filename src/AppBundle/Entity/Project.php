<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="project")
 */
class Project
{
    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Curriculumvitae_Project", mappedBy="projects")
     */
    private $curriculumvitaeProjects;

    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="project_tag",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
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
     * @Assert\NotBlank(message="Vul de klantnaam in.", groups={"Project"})
     *
     */
    protected $customerName;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Vul de functietitel in.", groups={"Project"})
     *
     */
    protected $functionTitle;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="Vul de tekst voor de situatie in.", groups={"Project"})
     *
     */
    protected $situationText;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="Vul de tekst voor de werkzaamheden in.", groups={"Project"})
     *
     */
    protected $taskText;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\NotBlank(message="Vul de tekst voor het resultaat in.", groups={"Project"})
     *
     */
    protected $resultText;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank(message="Vul de startdatum in.", groups={"Project"})
     */
    protected $startDate;

    /**
     * @ORM\Column(type="date")
     *
     * @Assert\NotBlank(message="Vul de einddatum in.", groups={"Project"})
     */
    protected $endDate;

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
     * Get Id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    

    /**
     * Set customerName
     *
     * @param string $customerName
     *
     * @return Project
     */
    public function setCustomerName($customerName)
    {
        $this->customerName = $customerName;

        return $this;
    }

    /**
     * Get customerName
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->customerName;
    }

    /**
     * Set functionTitle
     *
     * @param string $functionTitle
     *
     * @return Project
     */
    public function setFunctionTitle($functionTitle)
    {
        $this->functionTitle = $functionTitle;

        return $this;
    }

    /**
     * Get functionTitle
     *
     * @return string
     */
    public function getFunctionTitle()
    {
        return $this->functionTitle;
    }

    /**
     * Set situationText
     *
     * @param string $situationText
     *
     * @return Project
     */
    public function setSituationText($situationText)
    {
        $this->situationText = $situationText;

        return $this;
    }

    /**
     * Get situationText
     *
     * @return string
     */
    public function getSituationText()
    {
        return $this->situationText;
    }

    /**
     * Set taskText
     *
     * @param string $taskText
     *
     * @return Project
     */
    public function setTaskText($taskText)
    {
        $this->taskText = $taskText;

        return $this;
    }

    /**
     * Get taskText
     *
     * @return string
     */
    public function getTaskText()
    {
        return $this->taskText;
    }

    /**
     * Set resultText
     *
     * @param string $resultText
     *
     * @return Project
     */
    public function setResultText($resultText)
    {
        $this->resultText = $resultText;

        return $this;
    }

    /**
     * Get resultText
     *
     * @return string
     */
    public function getResultText()
    {
        return $this->resultText;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return Project
     */
    public function setStartDate(\DateTime $startDate)
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
     * @return Project
     */
    public function setEndDate(\DateTime $endDate)
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
     * @return Project
     */
    public function setUpdatedAt(\DateTime $updatedAt)
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
     * @return Project
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
     * @return Project
     */
    public function addTag(\AppBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Add curriculumvitaeProject
     *
     * @param \AppBundle\Entity\Curriculumvitae_Project $curriculumvitaeProject
     *
     * @return Project
     */
    public function addCurriculumvitaeProject(\AppBundle\Entity\Curriculumvitae_Project $curriculumvitaeProject)
    {
        $this->curriculumvitaeProjects[] = $curriculumvitaeProject;

        return $this;
    }

    /**
     * Remove curriculumvitaeProject
     *
     * @param \AppBundle\Entity\Curriculumvitae_Project $curriculumvitaeProject
     */
    public function removeCurriculumvitaeProject(\AppBundle\Entity\Curriculumvitae_Project $curriculumvitaeProject)
    {
        $this->curriculumvitaeProjects->removeElement($curriculumvitaeProject);
    }

    /**
     * Get curriculumvitaeProjects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCurriculumvitaeProjects()
    {
        return $this->curriculumvitaeProjects;
    }
}

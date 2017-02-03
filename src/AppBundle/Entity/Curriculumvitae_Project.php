<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="curriculumvitae_project")
 */
class Curriculumvitae_Project
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Curriculumvitae", inversedBy="curriculumvitaeProjects")
     * @ORM\JoinColumn(name="cv_id", referencedColumnName="id", nullable=false)
     */
    protected $curriculumvitae;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="curriculumvitaeProjects")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    protected $projects;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $isImportantProject;


    /**
     * Set isImportantProject
     *
     * @param boolean $isImportantProject
     *
     * @return Curriculumvitae_Project
     */
    public function setIsImportantProject($isImportantProject)
    {
        $this->isImportantProject = $isImportantProject;

        return $this;
    }

    /**
     * Get isImportantProject
     *
     * @return boolean
     */
    public function getIsImportantProject()
    {
        return $this->isImportantProject;
    }

    /**
     * Set curriculumvitae
     *
     * @param \AppBundle\Entity\Curriculumvitae $curriculumvitae
     *
     * @return Curriculumvitae_Project
     */
    public function setCurriculumvitae(\AppBundle\Entity\Curriculumvitae $curriculumvitae)
    {
        $this->curriculumvitae = $curriculumvitae;

        return $this;
    }

    /**
     * Get curriculumvitae
     *
     * @return \AppBundle\Entity\Curriculumvitae
     */
    public function getCurriculumvitae()
    {
        return $this->curriculumvitae;
    }

    /**
     * Set projects
     *
     * @param \AppBundle\Entity\Project $projects
     *
     * @return Curriculumvitae_Project
     */
    public function setProjects(\AppBundle\Entity\Project $projects)
    {
        $this->projects = $projects;

        return $this;
    }

    /**
     * Get projects
     *
     * @return \AppBundle\Entity\Project
     */
    public function getProjects()
    {
        return $this->projects;
    }
}

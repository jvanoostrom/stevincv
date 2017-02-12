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
     * @ORM\ManyToOne(targetEntity="Curriculumvitae", inversedBy="curriculumvitaeProjects", cascade={"persist"})
     * @ORM\JoinColumn(name="cv_id", referencedColumnName="id", nullable=false)
     */
    protected $curriculumvitae;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=false)
     */
    protected $project;

    /**
     * @ORM\Column(type="boolean")
     *
     */
    protected $important;


    /**
     * Set important
     *
     * @param boolean $important
     *
     * @return Curriculumvitae_Project
     */
    public function setImportant($important)
    {
        $this->important = $important;

        return $this;
    }

    /**
     * Get important
     *
     * @return boolean
     */
    public function isImportant()
    {
        return $this->important;
    }

    /**
     * Set curriculumvitae
     *
     * @param \AppBundle\Entity\Curriculumvitae $curriculumvitae
     *
     * @return Curriculumvitae_Project
     */
    public function setCurriculumvitae(\AppBundle\Entity\Curriculumvitae $curriculumvitae = null)
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
     * Set project
     *
     * @param \AppBundle\Entity\Project $project
     *
     * @return Curriculumvitae_Project
     */
    public function setProject(\AppBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \AppBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }
}

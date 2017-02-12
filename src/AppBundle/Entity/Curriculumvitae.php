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
     * @ORM\ManyToOne(targetEntity="Profile")
     */
    private $profile;

    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinTable(name="curriculumvitae_tag",
     *      joinColumns={@ORM\JoinColumn(name="cv_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     *      )
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="Curriculumvitae_Project", mappedBy="curriculumvitae", cascade={"persist"})
     */
    private $curriculumvitaeProjects;

    /**
     * @ORM\ManyToMany(targetEntity="Education")
     * @ORM\JoinTable(name="curriculumvitae_education",
     *      joinColumns={@ORM\JoinColumn(name="cv_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="education_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"endDate" = "desc", "startDate" = "desc"})
     */
    private $education;

    /**
     * @ORM\ManyToMany(targetEntity="Certificate")
     * @ORM\JoinTable(name="curriculumvitae_certificate",
     *      joinColumns={@ORM\JoinColumn(name="cv_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="certificate_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"obtainedDate" = "desc"})
     */
    private $certificates;

    /**
     * @ORM\ManyToMany(targetEntity="Extracurricular")
     * @ORM\JoinTable(name="curriculumvitae_extracurricular",
     *      joinColumns={@ORM\JoinColumn(name="cv_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="extracurricular_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"endDate" = "desc", "startDate" = "desc"})
     */
    private $extracurricular;

    /**
     * @ORM\ManyToMany(targetEntity="Publication")
     * @ORM\JoinTable(name="curriculumvitae_publication",
     *      joinColumns={@ORM\JoinColumn(name="cv_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="publication_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"publishedDate" = "desc"})
     */
    private $publications;

    /**
     * @ORM\ManyToMany(targetEntity="Skill")
     * @ORM\JoinTable(name="curriculumvitae_skill",
     *      joinColumns={@ORM\JoinColumn(name="cv_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="skill_id", referencedColumnName="id")}
     *      )
     * @ORM\OrderBy({"skillText" = "asc"})
     */
    private $skills;

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

    protected $projects;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->education = new ArrayCollection();
        $this->certificates = new ArrayCollection();
        $this->publications = new ArrayCollection();
        $this->extracurricular = new ArrayCollection();
        $this->skills = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->curriculumvitaeProjects = new ArrayCollection();
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
     * Set profile
     *
     * @param \AppBundle\Entity\Profile $profile
     *
     * @return Curriculumvitae
     */
    public function setProfile(\AppBundle\Entity\Profile $profile = null)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return \AppBundle\Entity\Profile
     */
    public function getProfile()
    {
        return $this->profile;
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
     * @return Curriculumvitae
     */
    public function addTag(\AppBundle\Entity\Tag $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Add education
     *
     * @param \AppBundle\Entity\Education $education
     *
     * @return Curriculumvitae
     */
    public function addEducation(\AppBundle\Entity\Education $education)
    {
        $this->education[] = $education;

        return $this;
    }

    /**
     * Remove education
     *
     * @param \AppBundle\Entity\Education $education
     */
    public function removeEducation(\AppBundle\Entity\Education $education)
    {
        $this->education->removeElement($education);
    }

    /**
     * Get education
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEducation()
    {
        return $this->education;
    }

    /**
     * Add certificate
     *
     * @param \AppBundle\Entity\Certificate $certificate
     *
     * @return Curriculumvitae
     */
    public function addCertificate(\AppBundle\Entity\Certificate $certificate)
    {
        $this->certificates[] = $certificate;

        return $this;
    }

    /**
     * Remove certificate
     *
     * @param \AppBundle\Entity\Certificate $certificate
     */
    public function removeCertificate(\AppBundle\Entity\Certificate $certificate)
    {
        $this->certificates->removeElement($certificate);
    }

    /**
     * Get certificates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCertificates()
    {
        return $this->certificates;
    }

    /**
     * Add extracurricular
     *
     * @param \AppBundle\Entity\Extracurricular $extracurricular
     *
     * @return Curriculumvitae
     */
    public function addExtracurricular(\AppBundle\Entity\Extracurricular $extracurricular)
    {
        $this->extracurricular[] = $extracurricular;

        return $this;
    }

    /**
     * Remove extracurricular
     *
     * @param \AppBundle\Entity\Extracurricular $extracurricular
     */
    public function removeExtracurricular(\AppBundle\Entity\Extracurricular $extracurricular)
    {
        $this->extracurricular->removeElement($extracurricular);
    }

    /**
     * Get extracurricular
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExtracurricular()
    {
        return $this->extracurricular;
    }

    /**
     * Add publication
     *
     * @param \AppBundle\Entity\Publication $publication
     *
     * @return Curriculumvitae
     */
    public function addPublication(\AppBundle\Entity\Publication $publication)
    {
        $this->publications[] = $publication;

        return $this;
    }

    /**
     * Remove publication
     *
     * @param \AppBundle\Entity\Publication $publication
     */
    public function removePublication(\AppBundle\Entity\Publication $publication)
    {
        $this->publications->removeElement($publication);
    }

    /**
     * Get publications
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPublications()
    {
        return $this->publications;
    }

    /**
     * Add skill
     *
     * @param \AppBundle\Entity\Skill $skill
     *
     * @return Curriculumvitae
     */
    public function addSkill(\AppBundle\Entity\Skill $skill)
    {
        $this->skills[] = $skill;

        return $this;
    }

    /**
     * Remove skill
     *
     * @param \AppBundle\Entity\Skill $skill
     */
    public function removeSkill(\AppBundle\Entity\Skill $skill)
    {
        $this->skills->removeElement($skill);
    }

    /**
     * Get skills
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSkills()
    {
        return $this->skills;
    }

    /**
     * Add curriculumvitaeProject
     *
     * @param Curriculumvitae_Project $curriculumvitaeProject
     *
     * @return Curriculumvitae
     */
    public function addCurriculumvitaeProject(Curriculumvitae_Project $curriculumvitaeProject)
    {
        $this->curriculumvitaeProjects->add($curriculumvitaeProject);
        $curriculumvitaeProject->setCurriculumvitae($this);

        return $this;
    }

    /**
     * Remove curriculumvitaeProject
     *
     * @param Curriculumvitae_Project $curriculumvitaeProject
     * @return $this
     */
    public function removeCurriculumvitaeProject(Curriculumvitae_Project $curriculumvitaeProject)
    {
        $this->curriculumvitaeProjects->removeElement($curriculumvitaeProject);

        return $this;
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

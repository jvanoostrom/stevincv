<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;


/**
 * @ORM\Entity
 * @ORM\Table(name="personalia")
 * @Vich\Uploadable
 */
class Personalia
{
    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="personalia", cascade={"persist"})
     */
    private $user;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, options={"default":"Bas"})
     *
     * @Assert\NotBlank(message="Vul je voornaam in.", groups={"Registration", "Personalia"})
     * @Assert\Length(
     *     max=255,
     *     maxMessage="De voornaam is te lang.",
     *     groups={"Registration", "Personalia"}
     * )
     */
    protected $firstName = 'Bas';

    /**
     * @ORM\Column(type="string", length=255, options={"default":"van Toor"})
     *
     * @Assert\NotBlank(message="Vul je achternaam in.", groups={"Registration", "Personalia"})
     * @Assert\Length(
     *     max=255,
     *     maxMessage="De achternaam is te lang.",
     *     groups={"Registration", "Personalia"}
     * )
     */
    protected $lastName = 'van Toorn';

    /**
     * @ORM\Column(type="date", options={"default":"1935-09-17"})
     *
     * @Assert\NotBlank(message="Vul je geboortedatum in.", groups={"Registration", "Personalia"})
     */
    protected $dateOfBirth = '1935-09-17';

    /**
     * @ORM\Column(type="string", length=255, options={"default":"Vlaardingen"})
     *
     * @Assert\NotBlank(message="Vul je woonplaats in.", groups={"Registration", "Personalia"})
     * @Assert\Length(
     *     max=255,
     *     maxMessage="De naam van je woonplaats is te lang.",
     *     groups={"Registration", "Personalia"}
     * )
     */
    protected $placeOfResidence = 'Vlaardingen';

    /**
     * @ORM\Column(type="string", length=255, options={"default":"bassie.jpg"})
     *
     * @Assert\NotBlank(message="Voeg een profielfoto toe.", groups={"Registration", "Personalia"})
     * @Assert\File(mimeTypes={ "image/png", "image/jpeg" })
     */
    protected $profileImageName = 'bassie.jpg';

    /**
     * @ORM\Column(type="string", length=255, options={"default":"bassie_circle.jpg"})
     *
     * @Assert\NotBlank(message="Voeg een profielfoto toe.", groups={"Registration", "Personalia"})
     * @Assert\File(mimeTypes={ "image/png", "image/jpeg" })
     */
    protected $profileAvatarName = 'bassie_circle.jpg';

    /**
     *
     * @Vich\UploadableField(mapping="profile_image", fileNameProperty="profileImageName")
     *
     * @var File
     */
    private $profileImageFile = null;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var \DateTime
     */
    private $updatedAt;

    public function __construct()
    {
        $this->firstName = "Bas";
        $this->lastName = "van Toor";
        $this->dateOfBirth = new \DateTime("1935-09-17");
        $this->placeOfResidence = "Vlaardingen";
        $this->profileImageName = "bassie.jpg";
        $this->profileAvatarName = "bassie_circ.jpg";
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Personalia
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Personalia
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set dateOfBirth
     *
     * @param \DateTime $dateOfBirth
     *
     * @return Personalia
     */
    public function setDateOfBirth(\DateTime $dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get dateOfBirth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set placeOfResidence
     *
     * @param string $placeOfResidence
     *
     * @return Personalia
     */
    public function setPlaceOfResidence($placeOfResidence)
    {
        $this->placeOfResidence = $placeOfResidence;

        return $this;
    }

    /**
     * Get placeOfResidence
     *
     * @return string
     */
    public function getPlaceOfResidence()
    {
        return $this->placeOfResidence;
    }

    /**
     * Set profileImageName
     *
     * @param string $profileImageName
     *
     * @return Personalia
     */
    public function setProfileImageName($profileImageName)
    {
        $this->profileImageName = $profileImageName;

        return $this;
    }

    /**
     * Get profileImageName
     *
     * @return string
     */
    public function getProfileAvatarName()
    {
        return $this->profileAvatarName;
    }

    /**
     * Set profileImageName
     *
     * @param string $profileImageName
     *
     * @return Personalia
     */
    public function setProfileAvatarName($profileAvatarName)
    {
        $this->profileAvatarName = $profileAvatarName;

        return $this;
    }

    /**
     * Get profileImageName
     *
     * @return string
     */
    public function getProfileImageName()
    {
        return $this->profileImageName;
    }

    /**
     *
     * Set profileImageFile
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     *
     * @return Personalia
     */
    public function setProfileImageFile(File $image = null)
    {
        $this->profileImageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     *
     * Get profileImageFile
     *
     * @return File|null
     */
    public function getProfileImageFile()
    {
        return $this->profileImageFile;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Personalia
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
     * @return Personalia
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

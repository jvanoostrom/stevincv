<?php


namespace AppBundle\Entity;


use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Personalia", inversedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $personalia;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get ID
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set personalia
     *
     * @param \AppBundle\Entity\Personalia $personalia
     *
     * @return User
     */
    public function setPersonalia(\AppBundle\Entity\Personalia $personalia = null)
    {
        $this->personalia = $personalia;

        return $this;
    }

    /**
     * Get personalia
     *
     * @return \AppBundle\Entity\Personalia
     */
    public function getPersonalia()
    {
        return $this->personalia;
    }
}

<?php


namespace AppBundle\Entity;


use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


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
     *
     * @Assert\Valid()
     */
    private $personalia;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $getThreeMonthsEmail;

    /**
     * @ORM\Column(type="integer")
     */
    protected $countThreeMonthsEmail;

    public function __construct()
    {
        parent::__construct();
        $this->setGetThreeMonthsEmail(true);
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

    /**
     * Set getThreeMonthsEmail
     *
     * @param boolean $getThreeMonthsEmail
     *
     * @return User
     */
    public function setGetThreeMonthsEmail($getThreeMonthsEmail)
    {
        $this->getThreeMonthsEmail = $getThreeMonthsEmail;

        return $this;
    }

    /**
     * Get getThreeMonthsEmail
     *
     * @return boolean
     */
    public function getGetThreeMonthsEmail()
    {
        return $this->getThreeMonthsEmail;
    }

    /**
     * Set countThreeMonthsEmail
     *
     * @param integer $countThreeMonthsEmail
     *
     * @return User
     */
    public function setCountThreeMonthsEmail($countThreeMonthsEmail)
    {
        $this->countThreeMonthsEmail = $countThreeMonthsEmail;

        return $this;
    }

    /**
     * Get countThreeMonthsEmail
     *
     * @return integer
     */
    public function getCountThreeMonthsEmail()
    {
        return $this->countThreeMonthsEmail;
    }
}

<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="tag")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     *
     * @Assert\NotBlank(message="Vul een tag in.", groups={"Tag"})
     *
     */
    protected $tagText;

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
     * Set tagText
     *
     * @param string $tagText
     *
     * @return Tag
     */
    public function setTagText($tagText)
    {
        $this->tagText = $tagText;

        return $this;
    }

    /**
     * Get tagText
     *
     * @return string
     */
    public function getTagText()
    {
        return $this->tagText;
    }

}

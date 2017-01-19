<?php

namespace AppBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Tag;

/**
 * Tags DataTransformer.
 */
class TagsDataTransformer implements DataTransformerInterface
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * Convert string of tags to array.
     *
     * @param string $string
     *
     * @return array
     */
    private function stringToArray($string)
    {
        $tags = explode(',', $string);

        // strip whitespaces from beginning and end of a tag text
        foreach ($tags as &$text) {
            $text = trim($text);
        }

        // removes duplicates
        return array_unique($tags);
    }

    /**
     * Transforms tags entities into string (separated by comma).
     *
     * @param Collection | null $tagCollection A collection of entities or NULL
     *
     * @return string | null An string of tags or NULL
     * @throws UnexpectedTypeException
     */
    public function transform($tagCollection)
    {
        if (null === $tagCollection) {
            return null;
        }

        if (!($tagCollection instanceof Collection)) {
            throw new UnexpectedTypeException($tagCollection, 'Doctrine\Common\Collections\Collection');
        }

        $tags = array();

        /**
         * @var \AppBundle\Entity\Tag $tag
         */
        foreach ($tagCollection as $tag) {
            array_push($tags, $tag->getTagText());
        }

        return implode(', ', $tags);
    }

    /**
     * Transforms string into tags entities.
     *
     * @param string | null $data Input string data
     *
     * @return Collection | null
     * @throws UnexpectedTypeException
     * @throws AccessDeniedException
     */
    public function reverseTransform($data)
    {
        $tagCollection = new ArrayCollection();

        if ('' === $data || null === $data) {
            return $tagCollection;
        }

        if (!is_string($data)) {
            throw new UnexpectedTypeException($data, 'string');
        }

        foreach ($this->stringToArray($data) as $name) {

            $tag = $this->em->getRepository('AppBundle:Tag')
                ->findOneBy(array('tagText' => $name));

            if (null === $tag) {
                $tag = new Tag();
                $tag->setTagText($name);

                $this->em->persist($tag);
            }

            $tagCollection->add($tag);

        }

        return $tagCollection;
    }
}